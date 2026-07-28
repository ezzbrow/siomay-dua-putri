<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;
use App\Services\CartService;

class Keranjang extends BaseController
{
    public function index()
    {
        $produkModel     = new ProdukModel();
        $varianModel     = new VarianProdukModel();
        $pengaturanModel = new PengaturanModel();

        $grouped = $produkModel->groupedByCategory();
        $pengaturan = $pengaturanModel->getSingleton();
        $now        = date('H:i:s');
        $avail      = \App\Helpers\ProductAvailability::resolve(
            $pengaturan['jam_buka'] ?? null,
            $pengaturan['jam_tutup'] ?? null,
            $now
        );

        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);

        $data = [
            'grouped'      => $grouped,
            'pengaturan'   => $pengaturan,
            'nowServerTime'=> $now,
            'tokoBuka'     => $avail['tokoBuka'],
            'alasanTutup'  => $avail['alasan'],
            'cart'         => $cartView,
            'catatan'      => CartService::getCatatan(),
        ];

        return view('etalase/index', $data);
    }

    public function tambah()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $varianRaw = $this->request->getPost('varian_id');
        $varianId  = ($varianRaw === null || $varianRaw === '' || $varianRaw === '0') ? null : (int) $varianRaw;
        $jumlah    = (int) $this->request->getPost('jumlah') ?: 1;

        $result = CartService::add(
            $produkId,
            $varianId,
            $jumlah,
            new ProdukModel(),
            new VarianProdukModel()
        );

        if (! $result['ok']) {
            return redirect()->to('/keranjang')->with('error', $result['error'])->withInput();
        }
        return redirect()->to('/keranjang')->with('message', 'Item ditambahkan ke keranjang.');
    }

    public function kurang()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $varianRaw = $this->request->getPost('varian_id');
        $varianId  = ($varianRaw === null || $varianRaw === '' || $varianRaw === '0') ? null : (int) $varianRaw;
        $jumlah    = (int) $this->request->getPost('jumlah') ?: 1;

        CartService::decrement($produkId, $varianId, $jumlah);
        return redirect()->to('/keranjang')->with('message', 'Jumlah item dikurangi.');
    }

    public function hapus()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $varianRaw = $this->request->getPost('varian_id');
        $varianId  = ($varianRaw === null || $varianRaw === '' || $varianRaw === '0') ? null : (int) $varianRaw;

        CartService::remove($produkId, $varianId);
        return redirect()->to('/keranjang')->with('message', 'Item dihapus dari keranjang.');
    }

    public function simpanCatatan()
    {
        $catatan = trim((string) $this->request->getPost('catatan'));
        if (strlen($catatan) > 500) {
            $catatan = substr($catatan, 0, 500);
        }
        CartService::setCatatan($catatan);
        return redirect()->to('/keranjang')->with('message', 'Catatan disimpan.');
    }
}
