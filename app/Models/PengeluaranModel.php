<?php
namespace App\Models;
use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table = 'pengeluaran';
    protected $allowedFields = ['tanggal', 'kategori', 'deskripsi', 'jumlah'];
    protected $useTimestamps = false; // pakai created_at manual atau set true kalau mau otomatis
}