<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PengeluaranModel;

class Pengeluaran extends BaseController
{
    public function index()
    {
        $model = new PengeluaranModel();
        $data['pengeluaran'] = $model->orderBy('tanggal', 'DESC')->findAll();
        return view('admin/pengeluaran/index', $data);
    }

    public function tambah()
    {
        $model = new PengeluaranModel();
        $model->save([
            'tanggal' => $this->request->getPost('tanggal'),
            'kategori' => $this->request->getPost('kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'jumlah' => $this->request->getPost('jumlah'),
        ]);
        return redirect()->to('/admin/pengeluaran');
    }

    public function hapus($id)
    {
        $model = new PengeluaranModel();
        $model->delete($id);
        return redirect()->to('/admin/pengeluaran');
    }
}