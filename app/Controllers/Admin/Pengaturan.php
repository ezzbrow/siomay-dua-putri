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
        $jamBuka      = (string) $this->request->getPost('jam_buka');
        $jamTutup     = (string) $this->request->getPost('jam_tutup');

        if ($pajakPersen < 0 || $pajakPersen > 100) {
            return redirect()->back()->withInput()->with('error', 'Persentase pajak harus 0-100.');
        }
        if ($minimumOrder < 0) {
            return redirect()->back()->withInput()->with('error', 'Minimum order tidak boleh negatif.');
        }
        if ($biayaStand < 0) {
            return redirect()->back()->withInput()->with('error', 'Biaya stand tidak boleh negatif.');
        }

        $data = [
            'pajak_aktif'    => $pajakAktif,
            'pajak_persen'   => $pajakPersen,
            'minimum_order'  => $minimumOrder,
            'biaya_stand'    => $biayaStand,
            'alamat_umkm'    => $alamatUmkm,
            'jam_buka'       => $jamBuka !== '' ? $jamBuka : null,
            'jam_tutup'      => $jamTutup !== '' ? $jamTutup : null,
        ];

        $row = $this->pengaturan->first();
        if ($row) {
            $this->pengaturan->update($row['id'], $data);
        } else {
            $this->pengaturan->insert($data);
        }
        return redirect()->to('/admin/pengaturan')->with('message', 'Pengaturan disimpan.');
    }
}
