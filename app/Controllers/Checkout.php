<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;
use App\Services\CartService;

class Checkout extends BaseController
{
    /**
     * Guard bersama untuk semua step checkout wizard (catatan/tanggal/metode/jemput/antar/pembayaran):
     * - wajib login (filter customerAuth sudah handle)
     * - cart tidak boleh kosong
     * - cart harus memenuhi minimum order
     * - produk di cart harus masih tersedia (status_aktif)
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
        // Re-validasi ketersediaan (status_aktif) — TODO fix yang sudah
        // diterapkan di store() lama, dipindah ke sini agar konsisten untuk semua step.
        $unavailable = [];
        foreach ($cartView['rows'] as $row) {
            if ((int) ($row['produk']['status_aktif'] ?? 0) !== 1) {
                $unavailable[] = $row['produk']['nama']
                    . (! empty($row['varian']) ? ' (varian: ' . $row['varian']['nama_varian'] . ')' : '');
            }
        }
        if (! empty($unavailable)) {
            return redirect()->to('/keranjang')
                ->with('error', 'Produk berikut sudah tidak aktif dan tidak bisa dipesan: ' . implode(', ', $unavailable));
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
    // STEP 6: PEMBAYARAN (QRIS statis milik admin — Midtrans dihapus 30 Juli 2026)
    // ====================================================================

    /**
     * Tampilkan QRIS statis (pengaturan.qris_image, diupload admin) untuk
     * dibayar manual oleh pembeli. Validasi nominal tetap dihitung ulang dari
     * item_pesanan × harga di DB (bukan trust input klien) supaya total yang
     * ditampilkan tidak bisa dimanipulasi, meski tidak ada lagi API pihak
     * ketiga yang memverifikasi nominalnya secara otomatis.
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

        // Validasi ulang: pesanan.status harus 'pending' (belum dibayar)
        if ($pesanan['status'] !== 'pending') {
            return redirect()->to('/checkout/sukses/' . $kodePesanan)
                ->with('error', 'Pesanan ini sudah diproses (status: ' . $pesanan['status'] . ').');
        }

        // Hitung ulang subtotal dari item_pesanan × harga di DB (bukan trust total di row pesanan)
        $items = $db->table('item_pesanan ip')
            ->select('ip.subtotal_item')
            ->where('ip.pesanan_id', (int) $pesanan['id'])
            ->get()->getResultArray();

        $recalcSubtotal = 0.0;
        foreach ($items as $it) {
            $recalcSubtotal += (float) $it['subtotal_item'];
        }

        // Validasi: subtotal re-hitung harus sama dengan pesanan.subtotal
        // (kalau beda, ada inkonsistensi data — tolak untuk safety)
        if (abs($recalcSubtotal - (float) $pesanan['subtotal']) > 0.01) {
            return redirect()->to('/keranjang')
                ->with('error', 'Subtotal pesanan tidak konsisten dengan item. Hubungi admin.');
        }

        $grossAmount = (int) round((float) $pesanan['total']);

        $pengaturan = (new PengaturanModel())->getSingleton();

        // Idempotensi: kalau transaksi sudah ada untuk pesanan ini, pakai kode transaksi existing
        $existingTrx = $db->table('transaksi')
            ->where('pesanan_id', (int) $pesanan['id'])
            ->get()->getRowArray();

        // Kode transaksi lokal (dulu diisi midtrans_order_id dari API Midtrans).
        // Kolom tetap dipakai sebagai identifier internal, tidak lagi berasal
        // dari pihak ketiga.
        $kodeTransaksi = $existingTrx['midtrans_order_id'] ?? ('QRIS-' . $kodePesanan);

        $trxData = [
            'status_pembayaran' => 'menunggu_pembayaran',
        ];
        if ($existingTrx) {
            $db->table('transaksi')->where('id', (int) $existingTrx['id'])->update($trxData);
        } else {
            $trxData['midtrans_order_id'] = $kodeTransaksi;
            $trxData['pesanan_id']        = (int) $pesanan['id'];
            $trxData['mdr_persen']        = 0.00;
            $trxData['nominal_diterima']  = 0.00;
            $db->table('transaksi')->insert($trxData);
        }

        return view('checkout/pembayaran', [
            'pesanan'       => $pesanan,
            'qrisImage'     => $pengaturan['qris_image'] ?? null,
            'kodeTransaksi' => $kodeTransaksi,
            'grossAmount'   => $grossAmount,
        ]);
    }

    /**
     * Tombol "Saya Sudah Bayar" di halaman pembayaran. Karena QRIS sekarang
     * statis (bukan lagi API Midtrans yang bisa dicek statusnya), sistem
     * TIDAK BISA memverifikasi pembayaran otomatis — tombol ini hanya
     * menandai pesanan sebagai 'menunggu_konfirmasi', lalu admin yang
     * mengonfirmasi manual setelah cek mutasi/notifikasi QRIS di sisi
     * penjual (lihat dashboard admin — tombol "Konfirmasi Lunas").
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

        if ($pesanan['status'] !== 'pending') {
            return redirect()->to('/checkout/sukses/' . $kode)
                ->with('message', 'Pesanan ini sudah diproses sebelumnya (status: ' . $pesanan['status'] . ').');
        }

        $db->table('pesanan')->where('id', (int) $pesanan['id'])
            ->update(['status' => 'menunggu_konfirmasi']);
        $db->table('transaksi')->where('id', (int) $trx['id'])
            ->update(['status_pembayaran' => 'menunggu_konfirmasi']);

        return redirect()->to('/checkout/sukses/' . $kode)
            ->with('message', 'Konfirmasi diterima. Admin akan memverifikasi pembayaran Anda secara manual.');
    }
}
