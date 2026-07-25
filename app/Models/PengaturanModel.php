<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table         = 'pengaturan';
    protected $primaryKey    = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'minimum_order',
        'pajak_persen',
        'pajak_aktif',
        'alamat_umkm',
        'jam_buka',
        'jam_tutup',
    ];

    public function getSingleton(): array
    {
        $row = $this->orderBy('id', 'ASC')->first();
        if ($row === null) {
            $row = [
                'id'             => 0,
                'minimum_order'  => 100000.00,
                'pajak_persen'   => 10.00,
                'pajak_aktif'    => 0,
                'alamat_umkm'    => '',
                'jam_buka'       => null,
                'jam_tutup'      => null,
            ];
            $this->insert($row);
            $row = $this->orderBy('id', 'ASC')->first();
        }
        return $row;
    }
}
