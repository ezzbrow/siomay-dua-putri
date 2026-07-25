<?php

namespace App\Models;

use CodeIgniter\Model;

class VarianProdukModel extends Model
{
    protected $table         = 'varian_produk';
    protected $primaryKey    = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['produk_id', 'nama_varian'];

    protected $validationRules = [
        'produk_id'  => 'required|is_natural_no_zero',
        'nama_varian'=> 'required|max_length[100]',
    ];
}
