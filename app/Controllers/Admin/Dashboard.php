<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\PengeluaranModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $pesananModel = new PesananModel();

        $data = [
            'nama_toko'     => session()->get('admin_nama'),
            'username'      => session()->get('admin_user'),
            'pesanan_lunas' => $pesananModel->where('status', 'lunas')
                                ->orderBy('tanggal_dibutuhkan', 'ASC')
                                ->findAll(),
        ];

        return view('admin/dashboard', $data);
    }

    public function riwayat()
    {
        $model = new PesananModel();
        $query = $model->where('status', 'lunas');

        $dari   = $this->request->getGet('dari');
        $sampai = $this->request->getGet('sampai');
        if ($dari && $sampai) {
            $query->where('tanggal_dibutuhkan >=', $dari)
                  ->where('tanggal_dibutuhkan <=', $sampai);
        }

        $data = [
            'riwayat' => $query->orderBy('tanggal_dibutuhkan', 'DESC')->findAll(),
            'dari'    => $dari,
            'sampai'  => $sampai,
        ];

        return view('admin/riwayat', $data);
    }

    public function laporan()
    {
        $model  = new PesananModel();
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $totalOmzet = $model->where('status', 'lunas')
            ->where('tanggal_dibutuhkan >=', $dari)
            ->where('tanggal_dibutuhkan <=', $sampai)
            ->selectSum('total')->get()->getRow();

        $data = [
            'total_omzet'      => $totalOmzet->total ?? 0,
            'jumlah_transaksi' => $model->where('status', 'lunas')
                                    ->where('tanggal_dibutuhkan >=', $dari)
                                    ->where('tanggal_dibutuhkan <=', $sampai)
                                    ->countAllResults(),
            'dari'   => $dari,
            'sampai' => $sampai,
        ];

        return view('admin/laporan', $data);
    }

    public function exportCsv()
    {
        $model  = new PesananModel();
        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $data = $model->where('status', 'lunas')
            ->where('tanggal_dibutuhkan >=', $dari)
            ->where('tanggal_dibutuhkan <=', $sampai)
            ->findAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Kode Pesanan', 'Tanggal', 'Metode', 'Total']);
        foreach ($data as $row) {
            fputcsv($output, [$row['kode_pesanan'], $row['tanggal_dibutuhkan'], $row['metode'], $row['total']]);
        }
        fclose($output);
        exit;
    }

    public function labaRugi()
    {
        $pesananModel    = new PesananModel();
        $pengeluaranModel = new PengeluaranModel();

        $dari   = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $pemasukanRow = $pesananModel->where('status', 'lunas')
            ->where('tanggal_dibutuhkan >=', $dari)
            ->where('tanggal_dibutuhkan <=', $sampai)
            ->selectSum('total')->get()->getRow();

        $pengeluaranRow = $pengeluaranModel->where('tanggal >=', $dari)
            ->where('tanggal <=', $sampai)
            ->selectSum('jumlah')->get()->getRow();

        $pemasukan   = $pemasukanRow->total ?? 0;
        $pengeluaran = $pengeluaranRow->jumlah ?? 0;

        $data = [
            'pemasukan'   => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'laba_rugi'   => $pemasukan - $pengeluaran,
            'dari'        => $dari,
            'sampai'      => $sampai,
        ];

        return view('admin/laba_rugi', $data);
    }
}