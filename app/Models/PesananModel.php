<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pembeli_id', 'kode_pesanan', 'nama_pembeli', 'nomor_hp',
        'metode', 'alamat', 'catatan', 'tanggal_dibutuhkan',
        'subtotal', 'pajak', 'total', 'status',
    ];
}