<?php

namespace App\Controllers;

use App\Models\PengaturanModel;
use App\Models\ProdukModel;

class Etalase extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();
        $pengaturan  = (new PengaturanModel())->getSingleton();
        $grouped     = $produkModel->groupedByCategory();

        $data = [
            'grouped'    => $grouped,
            'pengaturan' => $pengaturan,
        ];

        return view('etalase/index', $data);
    }
}
