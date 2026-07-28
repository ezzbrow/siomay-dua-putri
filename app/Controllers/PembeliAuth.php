<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembeliModel;

class PembeliAuth extends BaseController
{
    public function register()
    {
        if (session()->get('pembeli_id')) {
            return redirect()->to('/akun/riwayat');
        }

        return view('auth/pembeli/register');
    }

    public function storeRegister()
    {
        $rules = [
            'nama'      => 'required|max_length[255]',
            'email'     => 'required|valid_email|max_length[255]|is_unique[pembeli.email]',
            'password'  => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'nomor_hp'  => 'permit_empty|max_length[30]',
        ];
        $messages = [
            'email' => [
                'is_unique'   => 'Email sudah terdaftar. Silakan login atau pakai email lain.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama    = trim((string) $this->request->getPost('nama'));
        $email   = trim((string) $this->request->getPost('email'));
        $plain   = (string) $this->request->getPost('password');
        $nomorHp = trim((string) $this->request->getPost('nomor_hp'));

        $pembeli = new PembeliModel();
        $id = $pembeli->insert([
            'nama'          => $nama,
            'email'         => $email,
            'password_hash' => PembeliModel::hashPassword($plain),
            'nomor_hp'      => $nomorHp !== '' ? $nomorHp : null,
        ], true);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal. Coba lagi.');
        }

        session()->set([
            'pembeli_id'   => (int) $id,
            'pembeli_nama' => $nama,
            'pembeli_email'=> $email,
        ]);

        return redirect()->to('/akun/riwayat')->with('message', 'Selamat datang, ' . $nama . '! Akun Anda berhasil dibuat.');
    }

    public function login()
    {
        if (session()->get('pembeli_id')) {
            return redirect()->to('/akun/riwayat');
        }

        return view('auth/pembeli/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[255]',
            'password' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email'));
        $plain = (string) $this->request->getPost('password');

        $pembeli = (new PembeliModel())->findByEmail($email);
        if (! $pembeli || ! PembeliModel::verifyPassword($plain, $pembeli['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        session()->set([
            'pembeli_id'    => (int) $pembeli['id'],
            'pembeli_nama'  => $pembeli['nama'],
            'pembeli_email' => $pembeli['email'],
        ]);

        return redirect()->to('/akun/riwayat')->with('message', 'Berhasil login. Selamat datang, ' . $pembeli['nama'] . '!');
    }

    public function logout()
    {
        session()->remove(['pembeli_id', 'pembeli_nama', 'pembeli_email']);
        return redirect()->to('/etalase')->with('message', 'Anda telah logout.');
    }
}
