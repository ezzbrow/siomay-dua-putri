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
    private function guardCart(int $pembeliId)
    {
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
    // PLACEHOLDER untuk step 5 (metode) & 6 (pembayaran) — diimplementasi di Tahap 2b & 2c
    // ====================================================================
    public function metode()           { return $this->stepNotImplementedYet('metode'); }
    public function saveMetode()      { return $this->stepNotImplementedYet('metode'); }
    public function jemput()           { return $this->stepNotImplementedYet('jemput'); }
    public function saveJemput()      { return $this->stepNotImplementedYet('jemput'); }
    public function antar()            { return $this->stepNotImplementedYet('antar'); }
    public function saveAntar()       { return $this->stepNotImplementedYet('antar'); }
    public function pembayaran()       { return $this->stepNotImplementedYet('pembayaran'); }
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
}
