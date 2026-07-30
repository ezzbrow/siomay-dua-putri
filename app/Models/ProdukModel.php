<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table         = 'produk';
    protected $primaryKey    = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['nama', 'kategori', 'harga', 'status_aktif'];

    protected $validationRules = [
        'nama'        => 'required|max_length[255]',
        'kategori'    => 'required|in_list[Siomay,Tahu,Pentol Goreng,Lumpia,Snack,Minuman,Lainnya]',
        'harga'       => 'required|decimal|greater_than_equal_to[0]',
        'status_aktif'=> 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'kategori' => [
            'in_list' => 'Kategori harus salah satu dari: Siomay, Tahu, Pentol Goreng, Lumpia, Snack, Minuman, Lainnya.',
        ],
    ];

    public function withVariants(): array
    {
        $varianModel = new VarianProdukModel();

        $produk = $this->where('tampil_di_pesan_antar', 1)
            ->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC')
            ->findAll();

        foreach ($produk as &$p) {
            $p['varian'] = $varianModel->where('produk_id', $p['id'])
                ->orderBy('nama_varian', 'ASC')
                ->findAll();
        }

        return $produk;
    }

    public function groupedByCategory(): array
    {
        $rows = $this->withVariants();

        $grouped = [
            'Siomay'       => [],
            'Tahu'         => [],
            'Pentol Goreng' => [],
            'Lumpia'       => [],
            'Snack'        => [],
            'Minuman'      => [],
            'Lainnya'      => [],
        ];
        foreach ($rows as $r) {
            $key = $r['kategori'];
            if (! isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $r;
        }

        return $grouped;
    }
}
