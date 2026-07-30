<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\MidtransService;

/**
 * Webhook handler untuk notifikasi pembayaran dari Midtrans.
 *
 * CATATAN PENTING untuk deploy:
 * - Webhook TIDAK BISA dites end-to-end tanpa URL publik yang bisa di-ping
 *   oleh Midtrans. Untuk dev lokal, pakai tunnel (ngrok/Cloudflare) atau
 *   deploy ke hosting publik. Lihat §11.12 CLAUDE.md.
 * - Signature WAJIB diverifikasi — jangan percaya payload yang tidak signed.
 * - Idempotensi: kalau notifikasi yang sama diterima >1x, jangan update status
 *   berkali-kali (cek status saat ini sebelum update).
 * - Validasi gross_amount: cocokkan dengan total tersimpan di DB. Kalau beda,
 *   log sebagai anomali tapi JANGAN update ke lunas (return 200 OK saja).
 *
 * Response SELALU 200 OK untuk notifikasi yang signature valid (meskipun
 * anomali), agar Midtrans tidak retry terus. Return 403 hanya untuk
 * signature verification failure.
 */
class MidtransWebhook extends BaseController
{
    /**
     * Route: POST /webhook/midtrans
     * EXCLUDE dari CSRF filter (sudah dihandle di routes — bisa pakai
     * 'csrf' => 'exclude' option jika filter csrf di-enable di production).
     */
    public function index()
    {
        $rawBody = $this->request->getBody();
        $payload = json_decode($rawBody, true);
        if (! is_array($payload)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Invalid JSON body']);
        }

        $orderId      = (string) ($payload['order_id'] ?? '');
        $statusCode   = (string) ($payload['status_code'] ?? '');
        $grossAmount  = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $trxStatus    = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus  = (string) ($payload['fraud_status'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Missing required fields']);
        }

        // 1. Verifikasi signature
        if (! MidtransService::verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            log_message('warning', "MidtransWebhook: signature invalid for order_id={$orderId}");
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Invalid signature']);
        }

        // 2. Cari transaksi lokal berdasarkan midtrans_order_id
        $db = \Config\Database::connect();
        $trx = $db->table('transaksi')
            ->where('midtrans_order_id', $orderId)
            ->get()->getRowArray();
        if (! $trx) {
            // order_id tidak dikenal — bisa jadi duplikat callback dengan order berbeda
            log_message('warning', "MidtransWebhook: unknown order_id={$orderId}");
            return $this->response->setStatusCode(200)
                ->setJSON(['status' => 'ok', 'message' => 'Unknown order_id, ignored']);
        }

        // 3. Idempotensi: kalau sudah lunas, return 200 OK tanpa proses ulang
        if ($trx['status_pembayaran'] === 'lunas' && in_array($trxStatus, ['settlement', 'capture'], true)) {
            return $this->response->setStatusCode(200)
                ->setJSON(['status' => 'ok', 'message' => 'Already settled, idempotent']);
        }

        // 4. Validasi gross_amount vs DB
        $pesanan = null;
        if (! empty($trx['pesanan_id'])) {
            $pesanan = $db->table('pesanan')
                ->where('id', (int) $trx['pesanan_id'])
                ->get()->getRowArray();
        } elseif (! empty($trx['pesanan_acara_id'])) {
            $pesanan = $db->table('pesanan_acara')
                ->where('id', (int) $trx['pesanan_acara_id'])
                ->get()->getRowArray();
        }

        if (! $pesanan) {
            log_message('warning', "MidtransWebhook: order_id={$orderId} has no related pesanan");
            return $this->response->setStatusCode(200)
                ->setJSON(['status' => 'ok', 'message' => 'No related pesanan, ignored']);
        }

        $expectedGross = (int) round((float) $pesanan['total']);
        $actualGross   = (int) round((float) $grossAmount);
        if ($expectedGross !== $actualGross) {
            log_message('error', "MidtransWebhook: GROSS AMOUNT MISMATCH for order_id={$orderId}: expected={$expectedGross}, actual={$actualGross}. NOT updating to lunas.");
            return $this->response->setStatusCode(200)
                ->setJSON(['status' => 'ok', 'message' => 'Anomaly: gross amount mismatch, ignored']);
        }

        // 5. Update status kalau settlement/capture + fraud != deny
        $isLunas = in_array($trxStatus, ['settlement', 'capture'], true) && $fraudStatus !== 'deny';

        if ($isLunas) {
            $trxUpdate = [
                'status_pembayaran' => 'lunas',
                'nominal_diterima'   => $actualGross,
            ];
            // MDR UMI: 0% untuk ≤500rb, 0.3% untuk >500rb
            $trxUpdate['mdr_persen'] = $actualGross > 500000 ? 0.30 : 0.00;

            $db->table('transaksi')->where('id', (int) $trx['id'])->update($trxUpdate);

            $pesananUpdate = ['status' => 'lunas'];
            if (! empty($trx['pesanan_id'])) {
                $db->table('pesanan')->where('id', (int) $trx['pesanan_id'])->update($pesananUpdate);
            } elseif (! empty($trx['pesanan_acara_id'])) {
                $db->table('pesanan_acara')
                    ->where('id', (int) $trx['pesanan_acara_id'])
                    ->update(['status_pembayaran' => 'lunas']);
            }
        } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire'], true)) {
            // Gagal/kedaluwarsa
            $newTrxStatus = $trxStatus === 'expire' ? 'kedaluwarsa' : 'gagal';
            $db->table('transaksi')->where('id', (int) $trx['id'])
                ->update(['status_pembayaran' => $newTrxStatus]);
            $newPesananStatus = $newTrxStatus;
            if (! empty($trx['pesanan_id'])) {
                $db->table('pesanan')->where('id', (int) $trx['pesanan_id'])
                    ->update(['status' => $newPesananStatus]);
            } elseif (! empty($trx['pesanan_acara_id'])) {
                $db->table('pesanan_acara')
                    ->where('id', (int) $trx['pesanan_acara_id'])
                    ->update(['status_pembayaran' => $newPesananStatus]);
            }
        } else {
            // pending atau status lain
            $db->table('transaksi')->where('id', (int) $trx['id'])
                ->update(['status_pembayaran' => $trxStatus ?: 'pending']);
        }

        return $this->response->setStatusCode(200)
            ->setJSON(['status' => 'ok', 'message' => 'Processed order_id=' . $orderId]);
    }
}
