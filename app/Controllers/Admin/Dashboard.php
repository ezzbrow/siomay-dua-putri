<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'nama_toko' => session()->get('admin_nama'),
            'username'  => session()->get('admin_user'),
        ];
        return view('admin/dashboard', $data);
    }
}
