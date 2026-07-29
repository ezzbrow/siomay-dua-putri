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

        $pengaturan = $pengaturanModel->getSingleton();
        $now        = date('H:i:s');
        $avail      = \App\Helpers\ProductAvailability::resolve(
            $pengaturan['jam_buka'] ?? null,
            $pengaturan['jam_tutup'] ?? null,
            $now
        );

        $cartView = CartService::hydrate($produkModel, $varianModel, $pengaturanModel);

        $data = [
            'pengaturan'   => $pengaturan,
            'nowServerTime'=> $now,
            'tokoBuka'     => $avail['tokoBuka'],
            'alasanTutup'  => $avail['alasan'],
            'cart'         => $cartView,
            'catatan'      => CartService::getCatatan(),
        ];

        return view('keranjang/index', $data);
    }

    public function tambah()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $varianRaw = $this->request->getPost('varian_id');
        $varianId  = ($varianRaw === null || $varianRaw === '' || $varianRaw === '0') ? null : (int) $varianRaw;
        // qty bisa desimal (kg) atau integer (pcs) — pakai float, validasi per satuan
        $jumlah = (float) $this->request->getPost('jumlah');
        if ($jumlah <= 0) {
            return redirect()->to('/etalase')->with('error', 'Jumlah harus lebih dari 0.')->withInput();
        }

        // Validasi per satuan: kg butuh step_qty & min_qty, pcs integer >= 1
        $produkModel = new ProdukModel();
        $produk = $produkModel->find($produkId);
        if ($produk) {
            $satuan   = (string) ($produk['satuan'] ?? 'pcs');
            $stepQty  = (float) ($produk['step_qty'] ?? 1);
            $minQty   = (float) ($produk['min_qty'] ?? 1);
            if ($satuan === 'kg') {
                if ($jumlah < $minQty) {
                    return redirect()->to('/etalase')
                        ->with('error', 'Jumlah minimum untuk ' . $produk['nama'] . ' adalah ' . rtrim(rtrim(number_format($minQty, 2), '0'), '.') . ' kg.')
                        ->withInput();
                }
                // Kelipatan step_qty: toleransi float 0.0001
                $sisa = fmod($jumlah, $stepQty);
                if ($sisa > 0.0001 && abs($sisa - $stepQty) > 0.0001) {
                    return redirect()->to('/etalase')
                        ->with('error', 'Jumlah untuk ' . $produk['nama'] . ' harus kelipatan ' . rtrim(rtrim(number_format($stepQty, 2), '0'), '.') . ' kg.')
                        ->withInput();
                }
            } else {
                // pcs: integer >= 1
                if ((int) $jumlah !== (int) round($jumlah) || (int) $jumlah < 1) {
                    return redirect()->to('/etalase')
                        ->with('error', 'Jumlah untuk ' . $produk['nama'] . ' harus bilangan bulat minimal 1.')
                        ->withInput();
                }
                $jumlah = (int) $jumlah;
            }
        }

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
        $jumlah    = (float) $this->request->getPost('jumlah');
        if ($jumlah <= 0) {
            $jumlah = 1.0;
        }

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
