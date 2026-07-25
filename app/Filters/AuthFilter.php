<?php

namespace App\Filters;

use App\Models\AdminModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if ($session->get('admin_id')) {
            return;
        }

        $cookieName = 'siomay_remember';
        $token = $_COOKIE[$cookieName] ?? null;
        if ($token !== null && $token !== '') {
            $row = (new AdminModel())->findByRememberToken($token);
            if ($row) {
                $session->set([
                    'admin_id'   => (int) $row['id'],
                    'admin_user' => $row['username'],
                    'admin_nama' => $row['nama_toko'],
                ]);
                return;
            }
        }

        return redirect()->to('/admin/login')
            ->with('error', 'Silakan login untuk mengakses area admin.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
