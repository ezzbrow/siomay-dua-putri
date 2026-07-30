<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = Database::connect();
        $menungguKonfirmasi = $db->table('pesanan')
            ->where('status', 'menunggu_konfirmasi')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $data = [
            'nama_toko'           => session()->get('admin_nama'),
            'username'            => session()->get('admin_user'),
            'menungguKonfirmasi'  => $menungguKonfirmasi,
        ];
        return view('admin/dashboard', $data);
    }

    /**
     * Tombol "Konfirmasi Lunas" — admin sudah cek manual (mutasi/notifikasi
     * QRIS) di luar sistem, lalu tandai pesanan sebagai lunas di sini.
     * Hanya boleh dari status 'menunggu_konfirmasi' (bukan dari status lain)
     * supaya tidak ada state loncat sembarangan.
     */
    public function konfirmasiLunas(int $id)
    {
        $db = Database::connect();
        $pesanan = $db->table('pesanan')->where('id', $id)->get()->getRowArray();
        if (! $pesanan) {
            return redirect()->to('/admin/dashboard')->with('error', 'Pesanan tidak ditemukan.');
        }
        if ($pesanan['status'] !== 'menunggu_konfirmasi') {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Pesanan ini bukan status menunggu konfirmasi (status saat ini: ' . $pesanan['status'] . ').');
        }

        $db->table('pesanan')->where('id', $id)->update(['status' => 'lunas']);

        $trx = $db->table('transaksi')->where('pesanan_id', $id)->get()->getRowArray();
        if ($trx) {
            $db->table('transaksi')->where('id', (int) $trx['id'])->update([
                'status_pembayaran' => 'lunas',
                'nominal_diterima'  => (float) $pesanan['total'],
            ]);
        }

        return redirect()->to('/admin/dashboard')
            ->with('message', 'Pesanan ' . $pesanan['kode_pesanan'] . ' dikonfirmasi lunas.');
    }
}
