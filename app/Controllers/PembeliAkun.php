<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PembeliAkun extends BaseController
{
    public function riwayat()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login untuk melihat riwayat pesanan.');
        }

        $db = \Config\Database::connect();
        $rows = $db->table('pesanan')
            ->select('id, kode_pesanan, metode, subtotal, pajak, total, status')
            ->where('pembeli_id', $pembeliId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return view('auth/pembeli/riwayat', [
            'pembeliNama'  => session()->get('pembeli_nama'),
            'pembeliEmail' => session()->get('pembeli_email'),
            'pesanan'      => $rows,
        ]);
    }
}
