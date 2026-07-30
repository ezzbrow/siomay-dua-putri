<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected PengaturanModel $pengaturan;

    public function __construct()
    {
        $this->pengaturan = new PengaturanModel();
    }

    public function index()
    {
        $data = $this->pengaturan->getSingleton();
        return view('admin/pengaturan/index', ['p' => $data]);
    }

    public function save()
    {
        $pajakAktif  = $this->request->getPost('pajak_aktif') === '1' ? 1 : 0;
        $pajakPersen = (float) $this->request->getPost('pajak_persen');
        $minimumOrder = (float) $this->request->getPost('minimum_order');
        $biayaStand   = (float) $this->request->getPost('biaya_stand');
        $alamatUmkm   = trim((string) $this->request->getPost('alamat_umkm'));

        if ($pajakPersen < 0 || $pajakPersen > 100) {
            return redirect()->back()->withInput()->with('error', 'Persentase pajak harus 0-100.');
        }
        if ($minimumOrder < 0) {
            return redirect()->back()->withInput()->with('error', 'Minimum order tidak boleh negatif.');
        }
        if ($biayaStand < 0) {
            return redirect()->back()->withInput()->with('error', 'Biaya stand tidak boleh negatif.');
        }

        // PENTING: JANGAN pakai "if_exist" di sini. Rule if_exist mengecek
        // array_key_exists() di data GET/POST hasil getVar() - yang TIDAK
        // pernah memuat data file upload ($_FILES terpisah dari $_POST).
        // Field file jadi selalu "dianggap tidak ada" dan seluruh validasi
        // di bawahnya (is_image, mime_in, ext_in) DISKIP TANPA DIJALANKAN -
        // artinya file APAPUN (termasuk .php yang disamarkan jadi .png)
        // lolos begitu saja ke folder public/uploads/qris/ yang bisa
        // diakses langsung lewat URL. Rule file bawaan CI4 (max_size,
        // is_image, dst) sudah otomatis return true kalau memang tidak ada
        // file yang diupload (UPLOAD_ERR_NO_FILE) - jadi field ini tetap
        // optional TANPA perlu if_exist.
        $rules = [
            'qris_image' => [
                'label' => 'Gambar QRIS',
                'rules' => 'max_size[qris_image,2048]|is_image[qris_image]|mime_in[qris_image,image/jpg,image/jpeg,image/png]|ext_in[qris_image,jpg,jpeg,png]',
            ],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $row = $this->pengaturan->first();
        $qrisFilename = $row['qris_image'] ?? null;

        $qrisFile = $this->request->getFile('qris_image');
        if ($qrisFile && $qrisFile->isValid() && ! $qrisFile->hasMoved()) {
            $newName = $qrisFile->getRandomName();
            $qrisFile->move(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'qris', $newName);

            // Hapus file lama supaya folder upload tidak menumpuk file yatim
            if ($qrisFilename && is_file(FCPATH . 'uploads/qris/' . $qrisFilename)) {
                @unlink(FCPATH . 'uploads/qris/' . $qrisFilename);
            }
            $qrisFilename = $newName;
        }

        $data = [
            'pajak_aktif'    => $pajakAktif,
            'pajak_persen'   => $pajakPersen,
            'minimum_order'  => $minimumOrder,
            'biaya_stand'    => $biayaStand,
            'alamat_umkm'    => $alamatUmkm,
            'qris_image'     => $qrisFilename,
        ];

        if ($row) {
            $this->pengaturan->update($row['id'], $data);
        } else {
            $this->pengaturan->insert($data);
        }
        return redirect()->to('/admin/pengaturan')->with('message', 'Pengaturan disimpan.');
    }
}
