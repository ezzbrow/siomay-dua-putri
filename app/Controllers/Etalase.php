<?php

namespace App\Controllers;

use App\Helpers\ProductAvailability;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;

class Etalase extends BaseController
{
    public function index()
    {
        $produkModel   = new ProdukModel();
        $pengaturan    = (new PengaturanModel())->getSingleton();
        $grouped       = $produkModel->groupedByCategory();
        $nowServerTime = date('H:i:s');

        $availability = ProductAvailability::resolve(
            $pengaturan['jam_buka'] ?? null,
            $pengaturan['jam_tutup'] ?? null,
            $nowServerTime
        );

        $data = [
            'grouped'        => $grouped,
            'pengaturan'     => $pengaturan,
            'nowServerTime'  => $nowServerTime,
            'tokoBuka'       => $availability['tokoBuka'],
            'alasanTutup'    => $availability['alasan'],
        ];

        return view('etalase/index', $data);
    }
}
