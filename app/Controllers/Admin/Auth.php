<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Auth extends BaseController
{
    private const MAX_ATTEMPTS    = 5;
    private const WINDOW_SECONDS  = 600;
    private const LOCK_SECONDS    = 600;
    private const REMEMBER_COOKIE = 'siomay_remember';
    private const REMEMBER_DAYS   = 30;

    protected AdminModel $admin;

    public function __construct()
    {
        $this->admin = new AdminModel();
    }

    public function login()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }
        return view('admin/auth/login', ['lockSeconds' => $this->remainingLockSeconds()]);
    }

    public function attemptLogin()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }

        if ($this->remainingLockSeconds() > 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan gagal. Coba lagi nanti.');
        }

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        $row = $this->admin->findByUsername($username);
        if (! $row || ! password_verify($password, $row['password_hash'])) {
            $this->registerFailedAttempt();
            return redirect()->back()->withInput()
                ->with('error', 'Username atau password salah.');
        }

        $this->clearFailedAttempts();
        session()->regenerate();
        session()->set([
            'admin_id'    => (int) $row['id'],
            'admin_user'  => $row['username'],
            'admin_nama'  => $row['nama_toko'],
        ]);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->admin->update((int) $row['id'], ['remember_token' => $token]);
            setcookie(self::REMEMBER_COOKIE, $token, [
                'expires'  => time() + self::REMEMBER_DAYS * 86400,
                'path'     => '/',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        return redirect()->to('/admin');
    }

    public function logout()
    {
        if (session()->get('admin_id')) {
            $this->admin->update((int) session()->get('admin_id'), ['remember_token' => null]);
        }
        session()->remove(['admin_id', 'admin_user', 'admin_nama']);
        session()->destroy();
        if (isset($_COOKIE[self::REMEMBER_COOKIE])) {
            setcookie(self::REMEMBER_COOKIE, '', time() - 3600, '/');
            unset($_COOKIE[self::REMEMBER_COOKIE]);
        }
        return redirect()->to('/admin/login')->with('message', 'Anda sudah logout.');
    }

    public function register()
    {
        if ($this->admin->countAll() > 0) {
            return $this->alreadyRegisteredResponse();
        }
        return view('admin/auth/register');
    }

    public function storeRegister()
    {
        if ($this->admin->countAll() > 0) {
            return $this->alreadyRegisteredResponse();
        }

        $rules = [
            'nama_toko'     => 'required|max_length[255]',
            'username'      => 'required|max_length[100]|alpha_numeric|is_unique[admin.username]',
            'email'         => 'required|valid_email|max_length[255]|is_unique[admin.email]',
            'nomor_hp'      => 'required|max_length[30]',
            'password'      => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->admin->insert([
            'nama_toko'     => trim((string) $this->request->getPost('nama_toko')),
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'nomor_hp'      => trim((string) $this->request->getPost('nomor_hp')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/admin/login')
            ->with('message', 'Registrasi berhasil. Silakan login.');
    }

    private function registerFailedAttempt(): void
    {
        $session  = session();
        $attempts = (int) ($session->get('login_attempts') ?? 0) + 1;
        $firstAt  = $session->get('login_first_at') ?? time();
        if ($attempts === 1) {
            $firstAt = time();
        }
        $session->set('login_attempts', $attempts);
        $session->set('login_first_at', $firstAt);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $session->set('login_locked_until', time() + self::LOCK_SECONDS);
        }
    }

    private function clearFailedAttempts(): void
    {
        session()->remove(['login_attempts', 'login_first_at', 'login_locked_until']);
    }

    private function remainingLockSeconds(): int
    {
        $lockedUntil = session()->get('login_locked_until');
        if (! $lockedUntil) {
            return 0;
        }
        $remaining = (int) $lockedUntil - time();
        if ($remaining <= 0) {
            $this->clearFailedAttempts();
            return 0;
        }
        return $remaining;
    }

    private function alreadyRegisteredResponse()
    {
        return redirect()->to('/admin/login')
            ->with('error', 'Registrasi sudah ditutup. Admin sudah terdaftar, silakan login.');
    }
}
