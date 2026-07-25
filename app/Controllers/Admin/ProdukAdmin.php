<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;

class ProdukAdmin extends BaseController
{
    protected ProdukModel $produk;
    protected VarianProdukModel $varian;

    public function __construct()
    {
        $this->produk = new ProdukModel();
        $this->varian = new VarianProdukModel();
    }

    public function index()
    {
        $data['produk'] = $this->produk->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC')
            ->findAll();

        return view('admin/produk/index', $data);
    }

    public function create()
    {
        return view('admin/produk/form', [
            'mode'   => 'create',
            'produk' => [
                'id'          => '',
                'nama'        => '',
                'kategori'    => 'Somay Sapi',
                'harga'       => '',
                'status_aktif'=> 1,
            ],
        ]);
    }

    public function store()
    {
        if (! $this->produk->save($this->collectProdukInput())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->produk->errors());
        }
        return redirect()->to('/admin/produk')->with('message', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $produk = $this->produk->find($id);
        if (! $produk) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }
        $produk['varian'] = $this->varian->where('produk_id', $id)->findAll();
        return view('admin/produk/form', [
            'mode'   => 'edit',
            'produk' => $produk,
        ]);
    }

    public function update(int $id)
    {
        if (! $this->produk->find($id)) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }
        if (! $this->produk->update($id, $this->collectProdukInput($id))) {
            return redirect()->back()->withInput()
                ->with('errors', $this->produk->errors());
        }
        return redirect()->to('/admin/produk')->with('message', 'Produk berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($this->produk->find($id)) {
            $this->produk->delete($id);
            return redirect()->to('/admin/produk')->with('message', 'Produk berhasil dihapus.');
        }
        return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
    }

    public function storeVarian(int $produkId)
    {
        $produk = $this->produk->find($produkId);
        if (! $produk) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }
        $nama = trim((string) $this->request->getPost('nama_varian'));
        if ($nama === '') {
            return redirect()->back()->with('error', 'Nama varian wajib diisi.');
        }
        $this->varian->insert(['produk_id' => $produkId, 'nama_varian' => $nama]);
        return redirect()->to('/admin/produk/edit/' . $produkId)
            ->with('message', 'Varian berhasil ditambahkan.');
    }

    public function deleteVarian(int $produkId, int $varianId)
    {
        $varian = $this->varian->find($varianId);
        if ($varian && (int) $varian['produk_id'] === $produkId) {
            $this->varian->delete($varianId);
        }
        return redirect()->to('/admin/produk/edit/' . $produkId)
            ->with('message', 'Varian berhasil dihapus.');
    }

    private function collectProdukInput(?int $id = null): array
    {
        $harga = (string) $this->request->getPost('harga');
        $harga = (float) str_replace([',', ' '], ['.', ''], $harga);

        return [
            'nama'        => trim((string) $this->request->getPost('nama')),
            'kategori'    => (string) $this->request->getPost('kategori'),
            'harga'       => $harga,
            'status_aktif'=> (int) ($this->request->getPost('status_aktif') ? 1 : 0),
        ];
    }
}
