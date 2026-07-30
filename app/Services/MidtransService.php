<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Panjang maksimum kolom `transaksi.midtrans_order_id` di DB.
     * Schema: varchar(100) UNIQUE NOT NULL (lihat migration 2026-07-24-000001).
     * generateOrderId() truncate ke panjang ini agar INSERT tidak error.
     */
    public const MAX_ORDER_ID_LENGTH = 100;

    private static bool $configured = false;

    private static function configure(): void
    {
        if (self::$configured) {
            return;
        }
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
        self::$configured     = true;
    }

    /**
     * Generate midtrans_order_id dari kode_pesanan lokal.
     *
     * Strategi: $kodePesanan . '-' . timestamp (suffix untuk uniqueness attempt
     * supaya retry tidak konflik dengan order_id yang sudah ada). Hasil truncate
     * ke MAX_ORDER_ID_LENGTH (100 char) sesuai kolom DB.
     *
     * Format kode_pesanan lokal: "ORD-YYYYMMDD-XXXXXX" (20 char).
     * + "-" + 10 digit timestamp = 31 char. Jauh di bawah limit 100.
     */
    public static function generateOrderId(string $kodePesanan): string
    {
        $orderId = $kodePesanan . '-' . time();
        if (strlen($orderId) > self::MAX_ORDER_ID_LENGTH) {
            $orderId = substr($orderId, 0, self::MAX_ORDER_ID_LENGTH);
        }
        return $orderId;
    }

    /**
     * Generate QRIS charge untuk satu transaksi.
     *
     * Validasi nominal WAJIB dilakukan sebelum panggil method ini: $grossAmount
     * harus hasil re-hitung dari item_pesanan × harga di DB (bukan dari session/
     * POST). Lihat Checkout::pembayaran() untuk logic validasi.
     *
     * @param string $orderId          ID unik (pakai generateOrderId())
     * @param int    $grossAmount      Total tagihan, integer Rupiah tanpa desimal
     * @param array  $itemDetails      Format Midtrans: [['id'=>..,'price'=>..,'quantity'=>..,'name'=>..], ...]
     *                                Opsional, default []
     * @param array  $customerDetails  Format Midtrans: ['first_name'=>..,'phone'=>..]
     *                                Opsional, default []
     *
     * @return array ['ok'=>bool, 'data'=>array|null, 'error'=>string|null]
     */
    public static function generateQris(
        string $orderId,
        int $grossAmount,
        array $itemDetails = [],
        array $customerDetails = []
    ): array {
        self::configure();

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        if (! empty($itemDetails)) {
            $params['item_details'] = $itemDetails;
        }
        if (! empty($customerDetails)) {
            $params['customer_details'] = $customerDetails;
        }

        try {
            $response = CoreApi::charge($params);
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => 'Gagal generate QRIS: ' . $e->getMessage()];
        }

        $data = json_decode(json_encode($response), true);

        return ['ok' => true, 'data' => $data];
    }

    /**
     * Cek status transaksi ke Midtrans.
     *
     * Dipakai untuk 2 alur:
     * 1. Polling manual saat user klik "Saya Sudah Bayar" di halaman pembayaran —
     *    ini SELF-REPORT dari user, BUKAN bukti pembayaran beneran. Method ini
     *    cek real-time ke Midtrans, tapi tetap di-trigger oleh user (bukan
     *    webhook push). Bisa dipake untuk UX feedback cepan, tapi status
     *    LUNAS yang authoritative tetap dari webhook (lihat MidtransWebhook).
     * 2. Admin dashboard / future audit — cek status order_id kapan saja.
     *
     * @return array ['ok'=>bool, 'data'=>array|null, 'error'=>string|null]
     */
    public static function getStatus(string $orderId): array
    {
        self::configure();

        try {
            $response = Transaction::status($orderId);
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => 'Gagal cek status: ' . $e->getMessage()];
        }

        $data = json_decode(json_encode($response), true);

        return ['ok' => true, 'data' => $data];
    }

    /**
     * Verifikasi signature_key dari webhook Midtrans.
     *
     * Algoritma resmi (lihat dokumentasi Midtrans):
     *   sha512(order_id + status_code + gross_amount + ServerKey)
     *
     * Dipakai oleh MidtransWebhook controller — tolak request kalau signature
     * tidak cocok (bisa di-spoof).
     *
     * @param string $orderId      order_id dari notifikasi
     * @param string $statusCode   status_code dari notifikasi (mis. "200", "201")
     * @param string $grossAmount  gross_amount dari notifikasi
     * @param string $signatureKey  signature_key dari header/body notifikasi
     *
     * @return bool true kalau signature cocok
     */
    public static function verifySignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        self::configure();
        $serverKey = (string) Config::$serverKey;
        $payload = $orderId . $statusCode . $grossAmount . $serverKey;
        $expected = hash('sha512', $payload);
        return hash_equals($expected, $signatureKey);
    }
}
