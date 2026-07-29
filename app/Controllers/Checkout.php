<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\ProductAvailability;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;
use App\Services\CartService;
use App\Services\MidtransService;

class Checkout extends BaseController
{
    /**
     * Guard bersama untuk semua step checkout wizard (catatan/tanggal/metode/jemput/antar/pembayaran):
     * - wajib login (filter customerAuth sudah handle)
     * - cart tidak boleh kosong
     * - cart harus memenuhi minimum order
     * - produk di cart harus masih tersedia (status_aktif + jam buka)
     *
     * Return null jika OK, atau Response redirect (jika ada masalah) — supaya caller return.
     */
    private function guardCart(int $pembeliId = 0)
    {
        if (! $pembeliId) {
            $pembeliId = (int) session()->get('pembeli_id');
        }
        if (! $pembeliId) {
            return redirect()->to('/login')
                ->with('error', 'Sesi login berakhir. Silakan login lagi.');
        }
        $produkModel     = new ProdukModel();
        $varianModel     = new VarianProdukModel();
        $pengaturanModel = new PengaturanModel();
        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);
        if (empty($cartView['rows']) || ! $cartView['canCheckout']) {
            return redirect()->to('/keranjang')
                ->with('error', 'Keranjang kosong atau minimum order tidak terpenuhi.');
        }
        // Re-validasi ketersediaan (status_aktif + jam buka) — TODO fix yang sudah
        // diterapkan di store() lama, dipindah ke sini agar konsisten untuk semua step.
        $pengaturan = $pengaturanModel->getSingleton();
        $now = date('H:i:s');
        $avail = ProductAvailability::resolve(
            $pengaturan['jam_buka'] ?? null,
            $pengaturan['jam_tutup'] ?? null,
            $now
        );
        $tokoBuka = $avail['tokoBuka'];
        $unavailable = [];
        foreach ($cartView['rows'] as $row) {
            if (! ProductAvailability::isProductTersedia($row['produk'], $tokoBuka)) {
                $unavailable[] = $row['produk']['nama']
                    . (! empty($row['varian']) ? ' (varian: ' . $row['varian']['nama_varian'] . ')' : '');
            }
        }
        if (! empty($unavailable)) {
            $reason = $tokoBuka
                ? 'Produk berikut sudah tidak aktif dan tidak bisa dipesan: '
                : 'Toko sedang tutup (' . ($avail['alasan'] ?? 'di luar jam operasional') . '). Produk: ';
            return redirect()->to('/keranjang')
                ->with('error', $reason . implode(', ', $unavailable));
        }
        return $cartView;
    }

    /**
     * Hitung total (subtotal + pajak) dari cart view. Dipakai semua step.
     * @return array{subtotal:float, pajak:float, total:float, pajakAktif:bool, pajakPersen:float}
     */
    private function computeTotals(array $cartView, array $pengaturan): array
    {
        $pajakAktif = (bool) ($pengaturan['pajak_aktif'] ?? false);
        $pajakPersen = (float) ($pengaturan['pajak_persen'] ?? 0);
        $subtotal = (float) $cartView['total'];
        $pajak = $pajakAktif ? round($subtotal * $pajakPersen / 100, 2) : 0.0;
        return [
            'subtotal'    => $subtotal,
            'pajak'       => $pajak,
            'total'       => $subtotal + $pajak,
            'pajakAktif'  => $pajakAktif,
            'pajakPersen' => $pajakPersen,
        ];
    }

    // ====================================================================
    // STEP 3: Catatan
    // ====================================================================

    public function catatan()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
        $data = [
            'title'         => 'Catatan Pesanan — Siomay Dua Putri',
            'currentStep'   => 3,
            'catatanValue'  => (string) (session('checkout_catatan') ?? ''),
            'cart'          => $cartView,
        ];
        return view('checkout/catatan', $data);
    }

    public function saveCatatan()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
        $catatan = trim((string) $this->request->getPost('catatan'));
        if (strlen($catatan) > 250) {
            return redirect()->back()->withInput()
                ->with('error', 'Catatan maksimal 250 karakter.');
        }
        session()->set('checkout_catatan', $catatan);
        return redirect()->to('/checkout/tanggal');
    }

    // ====================================================================
    // STEP 4: Tanggal
    // ====================================================================

    public function tanggal()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
        $besok = (new \DateTime('tomorrow'))->format('Y-m-d');
        $data = [
            'title'        => 'Pilih Tanggal Pesanan — Siomay Dua Putri',
            'currentStep'  => 4,
            'besok'        => $besok,
            'tanggalValue' => (string) (session('checkout_tanggal') ?? $besok),
            'cart'         => $cartView,
        ];
        return view('checkout/tanggal', $data);
    }

    public function saveTanggal()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
        $tanggal = (string) $this->request->getPost('tanggal_dibutuhkan');
        $today = (new \DateTime('today'))->format('Y-m-d');
        if ($tanggal === '' || $tanggal <= $today) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal pesanan harus setelah hari ini (minimal H+1).');
        }
        session()->set('checkout_tanggal', $tanggal);
        return redirect()->to('/checkout/metode');
    }

    // ====================================================================
    // PLACEHOLDER untuk step 5 (metode) & 5a/5b (jemput/antar) — diimplementasi di Tahap 2b
    // ====================================================================
    // ====================================================================
    // STEP 5: Metode (Ambil Sendiri / Diantar)
    // ====================================================================
    public function metode()
    {
        $this->guardCart();
        return view('checkout/metode');
    }

    public function saveMetode()
    {
        $this->guardCart();
        $metode = (string) $this->request->getPost('metode');
        if (! in_array($metode, ['ambil_sendiri', 'diantar'], true)) {
            return redirect()->back()->with('error', 'Metode tidak valid.');
        }
        session()->set('checkout_metode', $metode);
        // Reset data step 5a/5b kalau user ganti metode
        session()->remove(['checkout_nama', 'checkout_nomor_hp', 'checkout_alamat', 'checkout_alamat_lat', 'checkout_alamat_lng']);
        return redirect()->to('/checkout/' . ($metode === 'ambil_sendiri' ? 'jemput' : 'antar'));
    }

    // ====================================================================
    // STEP 5a: Ambil Sendiri
    // ====================================================================
    public function jemput()
    {
        $this->guardCart();
        $metode = (string) session()->get('checkout_metode');
        if ($metode !== 'ambil_sendiri') {
            return redirect()->to('/checkout/metode');
        }
        $pengaturan = (new \App\Models\PengaturanModel())->getSingleton();
        return view('checkout/jemput', ['pengaturan' => $pengaturan]);
    }

    public function saveJemput()
    {
        $this->guardCart();
        $metode = (string) session()->get('checkout_metode');
        if ($metode !== 'ambil_sendiri') {
            return redirect()->to('/checkout/metode');
        }
        $rules = [
            'nama'     => 'required|max_length[255]',
            'nomor_hp' => 'required|max_length[30]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        session()->set('checkout_nama',     trim((string) $this->request->getPost('nama')));
        session()->set('checkout_nomor_hp', trim((string) $this->request->getPost('nomor_hp')));
        session()->remove(['checkout_alamat', 'checkout_alamat_lat', 'checkout_alamat_lng']);
        return redirect()->to('/checkout/sukses/TEST-PENDING');
    }

    // ====================================================================
    // STEP 5b: Diantar
    // ====================================================================
    public function antar()
    {
        $this->guardCart();
        $metode = (string) session()->get('checkout_metode');
        if ($metode !== 'diantar') {
            return redirect()->to('/checkout/metode');
        }
        return view('checkout/antar');
    }

    public function saveAntar()
    {
        $this->guardCart();
        $metode = (string) session()->get('checkout_metode');
        if ($metode !== 'diantar') {
            return redirect()->to('/checkout/metode');
        }
        $rules = [
            'nama'        => 'required|max_length[255]',
            'nomor_hp'    => 'required|max_length[30]',
            'alamat'      => 'required|max_length[500]',
            'alamat_lat'  => 'required|decimal',
            'alamat_lng'  => 'required|decimal',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        session()->set('checkout_nama',         trim((string) $this->request->getPost('nama')));
        session()->set('checkout_nomor_hp',     trim((string) $this->request->getPost('nomor_hp')));
        session()->set('checkout_alamat',       trim((string) $this->request->getPost('alamat')));
        session()->set('checkout_alamat_lat',   (string) $this->request->getPost('alamat_lat'));
        session()->set('checkout_alamat_lng',   (string) $this->request->getPost('alamat_lng'));
        return redirect()->to('/checkout/sukses/TEST-PENDING');
    }

    private function stepNotImplementedYet(string $name)
    {
        return view('checkout/soon', ['stepName' => $name]);
    }

    // ====================================================================
    // Halaman sukses (sudah ada, tidak berubah)
    // ====================================================================
    public function sukses(string $kode)
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login untuk melihat pesanan Anda.');
        }
        $db = \Config\Database::connect();
        $pesanan = $db->table('pesanan')
            ->where('kode_pesanan', $kode)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();
        if (! $pesanan) {
            return redirect()->to('/keranjang')->with('error', 'Pesanan tidak ditemukan.');
        }
        $items = $db->table('item_pesanan ip')
            ->select('ip.jumlah, ip.harga_satuan, ip.subtotal_item, p.nama AS produk_nama, vp.nama_varian')
            ->join('produk p', 'p.id = ip.produk_id', 'left')
            ->join('varian_produk vp', 'vp.id = ip.varian_id', 'left')
            ->where('ip.pesanan_id', (int) $pesanan['id'])
            ->get()->getResultArray();
        return view('checkout/sukses', ['pesanan' => $pesanan, 'items' => $items]);
    }

    // ====================================================================
    // STEP 6: PEMBAYARAN (generate QRIS via Midtrans + simpan midtrans_order_id)
    // ====================================================================

    /**
     * Generate QRIS via MidtransService, simpan midtrans_order_id ke tabel
     * transaksi. Validasi nominal PASTI dari DB (item_pesanan × harga di DB),
     * BUKAN dari input klien — untuk cegah manipulasi nominal.
     */
    public function pembayaran()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Sesi login berakhir.');
        }

        $kodePesanan = (string) $this->request->getGet('kode');
        if ($kodePesanan === '') {
            return redirect()->to('/keranjang')->with('error', 'Kode pesanan tidak valid.');
        }

        $db = \Config\Database::connect();
        $pesanan = $db->table('pesanan')
            ->where('kode_pesanan', $kodePesanan)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();
        if (! $pesanan) {
            return redirect()->to('/keranjang')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Validasi ulang: pesanan.status harus 'pending'
        if ($pesanan['status'] !== 'pending') {
            return redirect()->to('/checkout/sukses/' . $kodePesanan)
                ->with('error', 'Pesanan ini sudah diproses (status: ' . $pesanan['status'] . ').');
        }

        // Hitung ulang subtotal dari item_pesanan × harga di DB (bukan trust total di row pesanan)
        $items = $db->table('item_pesanan ip')
            ->select('ip.jumlah, ip.harga_satuan, ip.subtotal_item, p.nama AS produk_nama, vp.nama_varian')
            ->join('produk p', 'p.id = ip.produk_id', 'left')
            ->join('varian_produk vp', 'vp.id = ip.varian_id', 'left')
            ->where('ip.pesanan_id', (int) $pesanan['id'])
            ->get()->getResultArray();

        $recalcSubtotal = 0.0;
        $midtransItems  = [];
        foreach ($items as $it) {
            $recalcSubtotal += (float) $it['subtotal_item'];
            $midtransItems[] = [
                'id'       => (string) $it['produk_nama'],
                'price'    => (int) round((float) $it['harga_satuan']),
                'quantity' => (int) round((float) $it['jumlah']),
                'name'     => $it['produk_nama'] . ($it['nama_varian'] ? ' (' . $it['nama_varian'] . ')' : ''),
            ];
        }

        // Validasi: subtotal re-hitung harus sama dengan pesanan.subtotal
        // (kalau beda, ada inkonsistensi data — tolak untuk safety)
        if (abs($recalcSubtotal - (float) $pesanan['subtotal']) > 0.01) {
            return redirect()->to('/keranjang')
                ->with('error', 'Subtotal pesanan tidak konsisten dengan item. Hubungi admin.');
        }

        $grossAmount = (int) round((float) $pesanan['total']);

        // Idempotensi: kalau transaksi sudah ada untuk pesanan ini, pakai midtrans_order_id existing
        $existingTrx = $db->table('transaksi')
            ->where('pesanan_id', (int) $pesanan['id'])
            ->get()->getRowArray();

        $midtransOrderId = $existingTrx['midtrans_order_id'] ?? MidtransService::generateOrderId($kodePesanan);

        // Panggil Midtrans
        $customerDetails = [
            'first_name' => (string) $pesanan['nama_pembeli'],
            'phone'      => (string) $pesanan['nomor_hp'],
        ];
        $response = MidtransService::generateQris($midtransOrderId, $grossAmount, $midtransItems, $customerDetails);

        if (! ($response['ok'] ?? false)) {
            return redirect()->to('/checkout/sukses/' . $kodePesanan)
                ->with('error', 'Gagal generate QRIS: ' . ($response['error'] ?? 'unknown error'));
        }

        $midtransData = $response['data'] ?? [];

        // Simpan/update transaksi (idempoten — kalau existing, update)
        $trxData = [
            'midtrans_order_id' => $midtransOrderId,
            'status_pembayaran'  => $midtransData['transaction_status'] ?? 'pending',
            'mdr_persen'         => 0.00, // kategori UMI, default 0%
            'nominal_diterima'   => 0.00, // di-update oleh webhook saat settlement
        ];
        if ($existingTrx) {
            $db->table('transaksi')->where('id', (int) $existingTrx['id'])->update($trxData);
        } else {
            $trxData['pesanan_id'] = (int) $pesanan['id'];
            $db->table('transaksi')->insert($trxData);
        }

        // Tampilkan halaman pembayaran dengan data QRIS
        return view('checkout/pembayaran', [
            'pesanan'        => $pesanan,
            'midtransData'   => $midtransData,
            'midtransOrderId'=> $midtransOrderId,
            'grossAmount'    => $grossAmount,
            'expiryMinutes'  => 15,
        ]);
    }

    /**
     * Tombol "Saya Sudah Bayar" di halaman pembayaran — polling manual ke
     * Midtrans. BUKAN bukti pembayaran authoritative (itu webhook), tapi
     * feedback cepan untuk user.
     *
     * Jika settlement/capture → update pesanan.status='lunas' + transaksi.
     * Jika masih pending → set 'menunggu_konfirmasi' (user sudah klik tapi
     *   Midtrans belum konfirmasi — bisa jadi delay settlement).
     * Lainnya → set 'menunggu_konfirmasi' (default aman).
     */
    public function konfirmasiBayar(string $kode)
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Sesi login berakhir.');
        }

        $db = \Config\Database::connect();
        $pesanan = $db->table('pesanan')
            ->where('kode_pesanan', $kode)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();
        if (! $pesanan) {
            return redirect()->to('/keranjang')->with('error', 'Pesanan tidak ditemukan.');
        }

        $trx = $db->table('transaksi')
            ->where('pesanan_id', (int) $pesanan['id'])
            ->get()->getRowArray();
        if (! $trx) {
            return redirect()->to('/checkout/sukses/' . $kode)
                ->with('error', 'Transaksi belum di-generate. Silakan buka halaman pembayaran dulu.');
        }

        $response = MidtransService::getStatus($trx['midtrans_order_id']);
        if (! ($response['ok'] ?? false)) {
            return redirect()->to('/checkout/sukses/' . $kode)
                ->with('error', 'Gagal cek status: ' . ($response['error'] ?? 'unknown'));
        }

        $data = $response['data'] ?? [];
        $trxStatus = (string) ($data['transaction_status'] ?? '');
        $fraud     = (string) ($data['fraud_status'] ?? '');

        // Mapping Midtrans status → status lokal
        $isLunas = in_array($trxStatus, ['settlement', 'capture'], true)
            && $fraud !== 'deny';

        $newPesananStatus = $isLunas ? 'lunas' : 'menunggu_konfirmasi';
        $newTrxStatus     = $isLunas ? 'lunas' : ($trxStatus ?: 'pending');

        $db->table('pesanan')->where('id', (int) $pesanan['id'])
            ->update(['status' => $newPesananStatus]);

        $trxUpdate = [
            'status_pembayaran' => $newTrxStatus,
        ];
        if ($isLunas) {
            // gross_amount dari Midtrans saat settlement (integer rupiah)
            $gross = isset($data['gross_amount']) ? (int) $data['gross_amount'] : (int) $pesanan['total'];
            $trxUpdate['nominal_diterima'] = $gross;
            // MDR UMI: 0% untuk ≤500rb, 0.3% untuk >500rb (per PBI No. 23/6/PBI/2021 Pasal 52)
            $trxUpdate['mdr_persen'] = $gross > 500000 ? 0.30 : 0.00;
        }
        $db->table('transaksi')->where('id', (int) $trx['id'])->update($trxUpdate);

        $message = $isLunas
            ? 'Pembayaran terverifikasi! Pesanan Anda sudah lunas.'
            : 'Status Midtrans: ' . ($trxStatus ?: 'tidak diketahui') . '. Pembayaran akan dikonfirmasi setelah Midtrans mengirim notifikasi. Mohon tunggu.';
        return redirect()->to('/checkout/sukses/' . $kode)->with('message', $message);
    }
}
