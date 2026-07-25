<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table         = 'admin';
    protected $primaryKey    = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'nama_toko',
        'username',
        'password_hash',
        'remember_token',
        'nomor_hp',
        'email',
    ];

    protected $validationRules = [
        'nama_toko'     => 'required|max_length[255]',
        'username'      => 'required|max_length[100]|alpha_numeric|is_unique[admin.username,id,{id}]',
        'email'         => 'required|valid_email|max_length[255]|is_unique[admin.email,id,{id}]',
        'password_hash' => 'required',
        'nomor_hp'      => 'required|max_length[30]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique'  => 'Username sudah dipakai.',
            'alpha_numeric' => 'Username hanya boleh huruf dan angka.',
        ],
        'email' => [
            'is_unique' => 'Email sudah dipakai.',
        ],
    ];

    public function findByUsername(string $username): ?array
    {
        $row = $this->where('username', $username)->first();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return $this->countAllResults();
    }

    public function findByRememberToken(string $token): ?array
    {
        $row = $this->where('remember_token', $token)->first();
        return $row ?: null;
    }
}
