<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\ProductAvailability;
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
        // Re-validasi ketersediaan (status_aktif + jam buka) — konsisten untuk semua step.
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

    /**
     * Finalisasi pesanan: INSERT ke tabel pesanan + item_pesanan dari data session + cart.
     * Kosongkan cart setelah INSERT.
     *
     * Dipanggil di akhir saveJemput() dan saveAntar() setelah semua data session terkumpul.
     * Return kode_pesanan unik yang baru dibuat, atau null jika gagal.
     *
     * PENTING: nominal dihitung ulang dari harga di DB (bukan dari session/POST) —
     * mencegah manipulasi nominal dari sisi klien.
     */
    private function finalisasiPesanan(int $pembeliId): ?string
    {
        $produkModel     = new ProdukModel();
        $varianModel     = new VarianProdukModel();
        $pengaturanModel = new PengaturanModel();
        $pengaturan      = $pengaturanModel->getSingleton();

        // Hydrate cart (harga dari DB)
        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);
        if (empty($cartView['rows'])) {
            return null;
        }

        // Hitung totals dari DB
        $totals  = $this->computeTotals($cartView, $pengaturan);

        // Ambil data checkout dari session
        $metode    = (string) session()->get('checkout_metode');
        $catatan   = (string) (session()->get('checkout_catatan') ?? '');
        $tanggal   = (string) session()->get('checkout_tanggal');
        $nama      = (string) session()->get('checkout_nama');
        $nomorHp   = (string) session()->get('checkout_nomor_hp');
        $alamat    = (string) (session()->get('checkout_alamat') ?? '');
        // lat/lng disimpan tapi saat ini belum dipakai di tabel pesanan —
        // kolom sudah ada (alamat_lat, alamat_lng) dari migration 000005
        $alamatLat = (string) (session()->get('checkout_alamat_lat') ?? '');
        $alamatLng = (string) (session()->get('checkout_alamat_lng') ?? '');

        // Generate kode_pesanan unik: ORD-YYYYMMDD-XXXXXX (6 char alfanumerik acak)
        $db = \Config\Database::connect();
        do {
            $suffix     = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $kodePesanan = 'ORD-' . date('Ymd') . '-' . $suffix;
            $exists = $db->table('pesanan')->where('kode_pesanan', $kodePesanan)->countAllResults();
        } while ($exists > 0);

        // INSERT pesanan
        $pesananData = [
            'pembeli_id'        => $pembeliId,
            'kode_pesanan'      => $kodePesanan,
            'nama_pembeli'      => $nama,
            'nomor_hp'          => $nomorHp,
            'metode'            => $metode,
            'alamat'            => $metode === 'diantar' ? $alamat : null,
            'alamat_lat'        => ($metode === 'diantar' && $alamatLat !== '') ? (float) $alamatLat : null,
            'alamat_lng'        => ($metode === 'diantar' && $alamatLng !== '') ? (float) $alamatLng : null,
            'catatan'           => $catatan !== '' ? $catatan : null,
            'tanggal_dibutuhkan'=> $tanggal,
            'subtotal'          => $totals['subtotal'],
            'pajak'             => $totals['pajak'],
            'total'             => $totals['total'],
            'status'            => 'pending',
        ];
        $db->table('pesanan')->insert($pesananData);
        $pesananId = $db->insertID();

        if (! $pesananId) {
            return null;
        }

        // INSERT item_pesanan
        foreach ($cartView['rows'] as $row) {
            $db->table('item_pesanan')->insert([
                'pesanan_id'    => $pesananId,
                'produk_id'     => (int) $row['produk']['id'],
                'varian_id'     => $row['varian'] ? (int) $row['varian']['id'] : null,
                'jumlah'        => (float) $row['jumlah'],
                'harga_satuan'  => (float) $row['harga'],
                'subtotal_item' => (float) $row['subtotal'],
            ]);
        }

        // Kosongkan cart dan data checkout session
        CartService::clear();
        session()->remove([
            'checkout_catatan',
            'checkout_tanggal',
            'checkout_metode',
            'checkout_nama',
            'checkout_nomor_hp',
            'checkout_alamat',
            'checkout_alamat_lat',
            'checkout_alamat_lng',
        ]);

        return $kodePesanan;
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
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView  = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
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

        // Finalisasi: INSERT pesanan + item_pesanan, kosongkan cart
        $kodePesanan = $this->finalisasiPesanan($pembeliId);
        if (! $kodePesanan) {
            return redirect()->to('/keranjang')
                ->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }

        return redirect()->to('/checkout/pembayaran?kode=' . urlencode($kodePesanan));
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
        $pembeliId = (int) session()->get('pembeli_id');
        $cartView  = $this->guardCart($pembeliId);
        if ($cartView instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $cartView;
        }
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

        // Finalisasi: INSERT pesanan + item_pesanan, kosongkan cart
        $kodePesanan = $this->finalisasiPesanan($pembeliId);
        if (! $kodePesanan) {
            return redirect()->to('/keranjang')
                ->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }

        return redirect()->to('/checkout/pembayaran?kode=' . urlencode($kodePesanan));
    }

    // ====================================================================
    // STEP 6: PEMBAYARAN — tampilkan QRIS statis dari pengaturan
    // ====================================================================

    /**
     * Tampilkan halaman pembayaran dengan gambar QRIS statis yang di-upload admin.
     * Tidak ada koneksi ke Midtrans / payment gateway eksternal.
     *
     * Validasi:
     * - Pesanan harus ada dan milik pembeli yang login
     * - Status pesanan harus 'pending' (belum dibayar)
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

        // Kalau sudah dibayar, langsung ke halaman sukses
        if (! in_array($pesanan['status'], ['pending', 'menunggu_konfirmasi'], true)) {
            return redirect()->to('/checkout/sukses/' . $kodePesanan);
        }

        // Ambil item pesanan untuk ringkasan
        $items = $db->table('item_pesanan ip')
            ->select('ip.jumlah, ip.harga_satuan, ip.subtotal_item, p.nama AS produk_nama, vp.nama_varian')
            ->join('produk p', 'p.id = ip.produk_id', 'left')
            ->join('varian_produk vp', 'vp.id = ip.varian_id', 'left')
            ->where('ip.pesanan_id', (int) $pesanan['id'])
            ->get()->getResultArray();

        // Ambil QRIS statis dari pengaturan
        $pengaturan  = (new PengaturanModel())->getSingleton();
        $qrisImage   = $pengaturan['qris_image'] ?? null;
        $qrisImageUrl = $qrisImage ? base_url($qrisImage) : null;

        return view('checkout/pembayaran', [
            'pesanan'       => $pesanan,
            'items'         => $items,
            'qrisImageUrl'  => $qrisImageUrl,
            'grossAmount'   => (int) round((float) $pesanan['total']),
        ]);
    }

    /**
     * Tombol "Saya Sudah Bayar" — update status pesanan ke 'menunggu_konfirmasi'.
     * Admin akan konfirmasi manual via dashboard setelah cek mutasi rekening/QRIS.
     *
     * Tidak ada koneksi ke Midtrans. Status lunas HANYA bisa di-set oleh admin.
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

        // Hanya bisa klik kalau status masih 'pending'
        if ($pesanan['status'] !== 'pending') {
            $msg = $pesanan['status'] === 'menunggu_konfirmasi'
                ? 'Pembayaran sudah dilaporkan. Menunggu konfirmasi admin.'
                : 'Status pesanan: ' . $pesanan['status'] . '.';
            return redirect()->to('/checkout/sukses/' . $kode)->with('message', $msg);
        }

        // Update status ke menunggu_konfirmasi
        $db->table('pesanan')
            ->where('id', (int) $pesanan['id'])
            ->update(['status' => 'menunggu_konfirmasi']);

        return redirect()->to('/checkout/sukses/' . $kode)
            ->with('message', 'Terima kasih! Pembayaran Anda sedang diverifikasi oleh admin. Kami akan segera konfirmasi.');
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
}
