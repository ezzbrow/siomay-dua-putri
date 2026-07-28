<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;
use App\Services\CartService;

class Checkout extends BaseController
{
    public function index()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login atau daftar terlebih dahulu untuk checkout.');
        }

        $produkModel     = new ProdukModel();
        $varianModel     = new VarianProdukModel();
        $pengaturanModel = new PengaturanModel();

        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);
        if (empty($cartView['rows']) || ! $cartView['canCheckout']) {
            return redirect()->to('/keranjang')
                ->with('error', 'Keranjang kosong atau belum memenuhi minimum order. Minimal order Rp ' . number_format($cartView['minOrder'], 0, ',', '.') . '.');
        }

        $pengaturan = $pengaturanModel->getSingleton();
        $pajakAktif = (bool) ($pengaturan['pajak_aktif'] ?? false);
        $pajakPersen = (float) ($pengaturan['pajak_persen'] ?? 0);
        $subtotal = (float) $cartView['total'];
        $pajak = $pajakAktif ? round($subtotal * $pajakPersen / 100, 2) : 0.0;
        $grandTotal = $subtotal + $pajak;

        $besok = (new \DateTime('tomorrow'))->format('Y-m-d');

        $data = [
            'cart'           => $cartView,
            'subtotal'       => $subtotal,
            'pajakAktif'     => $pajakAktif,
            'pajakPersen'    => $pajakPersen,
            'pajak'          => $pajak,
            'grandTotal'     => $grandTotal,
            'pembeliNama'    => (string) session()->get('pembeli_nama'),
            'pembeliEmail'   => (string) session()->get('pembeli_email'),
            'besok'          => $besok,
            'alamatUmkm'     => (string) ($pengaturan['alamat_umkm'] ?? ''),
        ];

        return view('checkout/index', $data);
    }

    public function store()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')
                ->with('error', 'Sesi login berakhir. Silakan login lagi.');
        }

        $rules = [
            'tanggal_dibutuhkan' => 'required|valid_date[Y-m-d]',
            'nama_pembeli'       => 'required|max_length[255]',
            'nomor_hp'           => 'required|max_length[30]',
            'metode'             => 'required|in_list[ambil_sendiri,diantar]',
            'alamat'             => 'permit_empty|max_length[500]',
            'catatan'            => 'permit_empty|max_length[500]',
        ];
        $messages = [
            'tanggal_dibutuhkan' => [
                'required'   => 'Tanggal pesanan dibutuhkan wajib diisi.',
                'valid_date' => 'Format tanggal tidak valid.',
            ],
            'metode' => [
                'in_list' => 'Metode harus Ambil Sendiri atau Diantar.',
            ],
        ];
        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalDb = (string) $this->request->getPost('tanggal_dibutuhkan');
        $today = (new \DateTime('today'))->format('Y-m-d');
        if ($tanggalDb <= $today) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal pesanan harus setelah hari ini (minimal H+1).');
        }

        $metode    = (string) $this->request->getPost('metode');
        $alamatRaw = trim((string) $this->request->getPost('alamat'));
        $alamat    = $alamatRaw !== '' ? $alamatRaw : null;
        if ($metode === 'diantar' && ($alamat === null || $alamat === '')) {
            return redirect()->back()->withInput()
                ->with('error', 'Alamat pengiriman wajib diisi untuk metode Diantar.');
        }

        $produkModel     = new ProdukModel();
        $varianModel     = new VarianProdukModel();
        $pengaturanModel = new PengaturanModel();

        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);
        if (empty($cartView['rows']) || ! $cartView['canCheckout']) {
            return redirect()->to('/keranjang')
                ->with('error', 'Keranjang kosong atau minimum order tidak terpenuhi.');
        }

        $pengaturan = $pengaturanModel->getSingleton();
        $pajakAktif = (bool) ($pengaturan['pajak_aktif'] ?? false);
        $pajakPersen = (float) ($pengaturan['pajak_persen'] ?? 0);
        $subtotal = (float) $cartView['total'];
        $pajak = $pajakAktif ? round($subtotal * $pajakPersen / 100, 2) : 0.0;
        $grandTotal = $subtotal + $pajak;

        $namaPembeli = trim((string) $this->request->getPost('nama_pembeli'));
        $nomorHp     = trim((string) $this->request->getPost('nomor_hp'));
        $catatan     = trim((string) $this->request->getPost('catatan'));
        $catatan     = $catatan !== '' ? $catatan : null;

        $kodePesanan = $this->generateKodePesanan();

        $db = \Config\Database::connect();
        $db->transStart();

        $pesananId = $db->table('pesanan')->insert([
            'pembeli_id'         => $pembeliId,
            'kode_pesanan'       => $kodePesanan,
            'nama_pembeli'       => $namaPembeli,
            'nomor_hp'           => $nomorHp,
            'metode'             => $metode,
            'alamat'             => $alamat,
            'catatan'            => $catatan,
            'tanggal_dibutuhkan' => $tanggalDb,
            'subtotal'           => $subtotal,
            'pajak'              => $pajak,
            'total'              => $grandTotal,
            'status'             => 'pending',
        ], true);

        foreach ($cartView['rows'] as $row) {
            $db->table('item_pesanan')->insert([
                'pesanan_id'     => (int) $pesananId,
                'produk_id'      => (int) $row['produk']['id'],
                'varian_id'      => ! empty($row['varian']) ? (int) $row['varian']['id'] : null,
                'jumlah'         => (int) $row['jumlah'],
                'harga_satuan'   => (float) $row['harga'],
                'subtotal_item'  => (float) $row['subtotal'],
            ]);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan pesanan. Coba lagi.');
        }

        CartService::clear();

        return redirect()->to('/checkout/sukses/' . $kodePesanan)
            ->with('message', 'Pesanan berhasil disimpan. Silakan lanjut ke pembayaran.');
    }

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
            ->get()
            ->getRowArray();

        if (! $pesanan) {
            return redirect()->to('/keranjang')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = $db->table('item_pesanan ip')
            ->select('ip.jumlah, ip.harga_satuan, ip.subtotal_item, p.nama AS produk_nama, vp.nama_varian')
            ->join('produk p', 'p.id = ip.produk_id', 'left')
            ->join('varian_produk vp', 'vp.id = ip.varian_id', 'left')
            ->where('ip.pesanan_id', (int) $pesanan['id'])
            ->get()
            ->getResultArray();

        $data = [
            'pesanan' => $pesanan,
            'items'   => $items,
        ];

        return view('checkout/sukses', $data);
    }

    private function generateKodePesanan(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $db = \Config\Database::connect();
        for ($i = 0; $i < 5; $i++) {
            $suffix = strtoupper(bin2hex(random_bytes(3)));
            $kode = $prefix . $suffix;
            $exists = $db->table('pesanan')->where('kode_pesanan', $kode)->countAllResults();
            if ($exists === 0) {
                return $kode;
            }
        }
        return $prefix . strtoupper(bin2hex(random_bytes(6)));
    }
}
