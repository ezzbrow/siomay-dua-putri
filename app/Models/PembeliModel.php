<?php

namespace App\Models;

use CodeIgniter\Model;

class PembeliModel extends Model
{
    protected $table         = 'pembeli';
    protected $primaryKey    = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'nama',
        'email',
        'password_hash',
        'nomor_hp',
    ];

    protected $validationRules = [
        'nama'          => 'required|max_length[255]',
        'email'         => 'required|valid_email|max_length[255]|is_unique[pembeli.email,id,{id}]',
        'password_hash' => 'required',
        'nomor_hp'      => 'permit_empty|max_length[30]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique'  => 'Email sudah terdaftar. Silakan login atau pakai email lain.',
            'valid_email' => 'Format email tidak valid.',
        ],
        'nama' => [
            'required' => 'Nama wajib diisi.',
        ],
    ];

    public function findByEmail(string $email): ?array
    {
        $row = $this->where('email', $email)->first();
        return $row ?: null;
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
