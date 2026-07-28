<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\PengaturanModel;

class Landing extends BaseController
{
    public function index(): string
    {
        $pengaturan = (new PengaturanModel())->getSingleton();

        // Footer kontak: ambil dari pengaturan kalau ada, fallback ke admin (nomor HP).
        $kontakAlamat = trim((string) ($pengaturan['alamat_umkm'] ?? ''));
        if ($kontakAlamat === '') {
            $kontakAlamat = '';
        }

        $kontakHp = '';
        if (! empty($pengaturan['admin_hp'] ?? null)) {
            $kontakHp = (string) $pengaturan['admin_hp'];
        } else {
            // Fallback: ambil dari admin yang baru saja login/terdaftar.
            $admin = (new AdminModel())->first();
            if ($admin && ! empty($admin['nomor_hp'])) {
                $kontakHp = (string) $admin['nomor_hp'];
            }
        }

        // Pesanan Acara (F21) belum dibangun, jadi link sengaja kosong.
        // View akan render kartu sebagai "segera hadir" jika variabel ini null.
        $pesananAcaraUrl = null;

        $data = [
            'title'            => 'Siomay Dua Putri — Siomay Segar Setiap Hari',
            'footerDeskripsi'  => 'Siomay & bakso ikan segar, dibuat setiap hari dengan bahan berkualitas.',
            'kontakAlamat'     => $kontakAlamat,
            'kontakHp'         => $kontakHp,
            'pesananAcaraUrl'  => $pesananAcaraUrl,
        ];

        return view('landing/index', $data);
    }
}
