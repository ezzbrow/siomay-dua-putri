<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Services\StandCartService;

/**
 * PesanStand — controller untuk alur Booking Stand ke Acara (F21).
 *
 * 6 step:
 *   1. tentang    GET  /pesan-stand           — halaman promosi (bebas akses)
 *   2. form       GET  /pesan-stand/form      — form data acara (wajib login)
 *      saveForm   POST /pesan-stand/form
 *   3. menu       GET  /pesan-stand/menu      — pilih menu 14 produk stand
 *      tambahMenu POST /pesan-stand/menu/tambah
 *      kurangMenu POST /pesan-stand/menu/kurang
 *   4. ringkasan  GET  /pesan-stand/ringkasan — rekap + kalkulasi total
 *      lanjut     POST /pesan-stand/ringkasan  — trigger finalisasiBooking
 *   5. pembayaran GET  /pesan-stand/pembayaran?kode=XXX
 *      konfirmasi POST /pesan-stand/konfirmasi-bayar/{kode}
 *   6. sukses     GET  /pesan-stand/sukses/{kode}
 *
 * Session keys (terpisah dari checkout_* Pesan Antar):
 *   stand_nama, stand_wa, stand_jenis_acara, stand_nama_acara,
 *   stand_tanggal_acara, stand_lokasi_acara, stand_estimasi_tamu, stand_catatan
 *   stand_cart (dikelola StandCartService)
 *
 * JANGAN modifikasi Checkout.php — file ini terpisah total.
 */
class PesanStand extends BaseController
{
    // ====================================================================
    // STEP 1: Tentang Stand Acara (bebas akses)
    // ====================================================================

    public function tentang()
    {
        return view('pesan-stand/tentang', [
            'title' => 'Pesan Stand Acara — Siomay Dua Putri',
        ]);
    }

    // ====================================================================
    // STEP 2: Form Data Acara (wajib login via filter customerAuth)
    // ====================================================================

    public function form()
    {
        $pembeli = $this->getLoggedInPembeli();

        // Pre-fill dari session jika sudah pernah isi
        $data = [
            'title'               => 'Data Acara — Pesan Stand Siomay Dua Putri',
            'currentStep'         => 2,
            'stand_nama'          => session()->get('stand_nama') ?? ($pembeli['nama'] ?? ''),
            'stand_wa'            => session()->get('stand_wa')   ?? ($pembeli['nomor_hp'] ?? ''),
            'stand_jenis_acara'   => session()->get('stand_jenis_acara') ?? '',
            'stand_nama_acara'    => session()->get('stand_nama_acara') ?? '',
            'stand_tanggal_acara' => session()->get('stand_tanggal_acara') ?? '',
            'stand_lokasi_acara'  => session()->get('stand_lokasi_acara') ?? '',
            'stand_estimasi_tamu' => session()->get('stand_estimasi_tamu') ?? '',
            'stand_catatan'       => session()->get('stand_catatan') ?? '',
        ];

        return view('pesan-stand/form', $data);
    }

    public function saveForm()
    {
        $rules = [
            'nama'          => 'required|max_length[255]',
            'nomor_wa'      => 'required|max_length[30]',
            'jenis_acara'   => 'required|in_list[pernikahan,ulang_tahun,arisan,pengajian,seminar,grand_opening,lainnya]',
            'nama_acara'    => 'required|max_length[255]',
            'tanggal_acara' => 'required',
            'lokasi_acara'  => 'required|max_length[500]',
            'estimasi_tamu' => 'permit_empty|integer|greater_than[0]',
            'catatan'       => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tanggal = (string) $this->request->getPost('tanggal_acara');
        $today   = (new \DateTime('today'))->format('Y-m-d');
        if ($tanggal <= $today) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal acara harus setelah hari ini (minimal besok).');
        }

        // Simpan ke session dengan key stand_*
        session()->set([
            'stand_nama'          => trim((string) $this->request->getPost('nama')),
            'stand_wa'            => trim((string) $this->request->getPost('nomor_wa')),
            'stand_jenis_acara'   => (string) $this->request->getPost('jenis_acara'),
            'stand_nama_acara'    => trim((string) $this->request->getPost('nama_acara')),
            'stand_tanggal_acara' => $tanggal,
            'stand_lokasi_acara'  => trim((string) $this->request->getPost('lokasi_acara')),
            'stand_estimasi_tamu' => (string) $this->request->getPost('estimasi_tamu'),
            'stand_catatan'       => trim((string) ($this->request->getPost('catatan') ?? '')),
        ]);

        return redirect()->to('/pesan-stand/menu');
    }

    // ====================================================================
    // STEP 3: Pilih Menu Stand
    // ====================================================================

    public function menu()
    {
        $this->guardFormSession();

        $produkModel = new ProdukModel();
        $semua       = $produkModel->where('tampil_di_pesan_stand', 1)
            ->orderBy('kategori', 'ASC')
            ->orderBy('nama', 'ASC')
            ->findAll();

        // Group by kategori
        $grouped = [];
        foreach ($semua as $p) {
            $grouped[$p['kategori']][] = $p;
        }

        $cartView = StandCartService::hydrate($produkModel);

        return view('pesan-stand/menu', [
            'title'       => 'Pilih Menu Stand — Siomay Dua Putri',
            'currentStep' => 3,
            'grouped'     => $grouped,
            'cart'        => StandCartService::get(),
            'cartView'    => $cartView,
        ]);
    }

    /**
     * POST /pesan-stand/menu/tambah
     * Body: produk_id (int)
     */
    public function tambahMenu()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        if ($produkId <= 0) {
            return redirect()->to('/pesan-stand/menu')->with('error', 'Produk tidak valid.');
        }

        // Validasi: produk harus tampil_di_pesan_stand=1
        $produk = (new ProdukModel())->where('id', $produkId)
            ->where('tampil_di_pesan_stand', 1)
            ->first();
        if (! $produk) {
            return redirect()->to('/pesan-stand/menu')->with('error', 'Produk tidak tersedia.');
        }

        StandCartService::increment($produkId);
        return redirect()->to('/pesan-stand/menu');
    }

    /**
     * POST /pesan-stand/menu/kurang
     * Body: produk_id (int)
     */
    public function kurangMenu()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        if ($produkId > 0) {
            StandCartService::decrement($produkId);
        }
        return redirect()->to('/pesan-stand/menu');
    }

    // ====================================================================
    // STEP 4: Ringkasan Booking
    // ====================================================================

    public function ringkasan()
    {
        $this->guardFormSession();

        $produkModel = new ProdukModel();
        $cartView    = StandCartService::hydrate($produkModel);
        $pengaturan  = (new PengaturanModel())->getSingleton();
        $biayaStand  = (float) ($pengaturan['biaya_stand'] ?? 0);
        $total       = $cartView['subtotal'] + $biayaStand;

        return view('pesan-stand/ringkasan', [
            'title'      => 'Ringkasan Booking — Siomay Dua Putri',
            'currentStep' => 4,
            'cartView'   => $cartView,
            'biayaStand' => $biayaStand,
            'total'      => $total,
            // Data acara dari session
            'stand_nama'          => session()->get('stand_nama'),
            'stand_wa'            => session()->get('stand_wa'),
            'stand_jenis_acara'   => session()->get('stand_jenis_acara'),
            'stand_nama_acara'    => session()->get('stand_nama_acara'),
            'stand_tanggal_acara' => session()->get('stand_tanggal_acara'),
            'stand_lokasi_acara'  => session()->get('stand_lokasi_acara'),
            'stand_estimasi_tamu' => session()->get('stand_estimasi_tamu'),
            'stand_catatan'       => session()->get('stand_catatan'),
        ]);
    }

    /**
     * POST /pesan-stand/ringkasan — trigger finalisasiBooking
     */
    public function lanjutPembayaran()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Sesi login berakhir.');
        }

        $kodeBooking = $this->finalisasiBooking($pembeliId);
        if (! $kodeBooking) {
            return redirect()->to('/pesan-stand/ringkasan')
                ->with('error', 'Gagal membuat booking. Pastikan menu sudah dipilih dan coba lagi.');
        }

        return redirect()->to('/pesan-stand/pembayaran?kode=' . urlencode($kodeBooking));
    }

    // ====================================================================
    // STEP 5: Pembayaran QRIS
    // ====================================================================

    public function pembayaran()
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Sesi login berakhir.');
        }

        $kodeBooking = (string) $this->request->getGet('kode');
        if ($kodeBooking === '') {
            return redirect()->to('/pesan-stand')->with('error', 'Kode booking tidak valid.');
        }

        $db      = \Config\Database::connect();
        $booking = $db->table('pesanan_acara')
            ->where('kode_booking', $kodeBooking)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();

        if (! $booking) {
            return redirect()->to('/pesan-stand')->with('error', 'Booking tidak ditemukan.');
        }

        // Kalau sudah lewat pending/menunggu_konfirmasi → redirect ke sukses
        if (! in_array($booking['status_pembayaran'], ['pending', 'menunggu_konfirmasi'], true)) {
            return redirect()->to('/pesan-stand/sukses/' . $kodeBooking);
        }

        // Ambil items
        $items = $db->table('item_pesanan_acara ipa')
            ->select('ipa.jumlah, ipa.harga_satuan_snapshot, ipa.subtotal_item, p.nama AS produk_nama')
            ->join('produk p', 'p.id = ipa.produk_id', 'left')
            ->where('ipa.pesanan_acara_id', (int) $booking['id'])
            ->get()->getResultArray();

        $pengaturan  = (new PengaturanModel())->getSingleton();
        $qrisImage   = $pengaturan['qris_image'] ?? null;
        $qrisImageUrl = $qrisImage ? base_url($qrisImage) : null;

        return view('pesan-stand/pembayaran', [
            'title'        => 'Pembayaran Booking — Siomay Dua Putri',
            'currentStep'  => 5,
            'booking'      => $booking,
            'items'        => $items,
            'qrisImageUrl' => $qrisImageUrl,
            'grossAmount'  => (int) round((float) $booking['total']),
        ]);
    }

    /**
     * POST /pesan-stand/konfirmasi-bayar/{kode}
     * Tombol "Saya Sudah Bayar" — set status ke 'menunggu_konfirmasi'.
     */
    public function konfirmasiBayar(string $kode)
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Sesi login berakhir.');
        }

        $db      = \Config\Database::connect();
        $booking = $db->table('pesanan_acara')
            ->where('kode_booking', $kode)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();

        if (! $booking) {
            return redirect()->to('/pesan-stand')->with('error', 'Booking tidak ditemukan.');
        }

        if ($booking['status_pembayaran'] !== 'pending') {
            $msg = $booking['status_pembayaran'] === 'menunggu_konfirmasi'
                ? 'Pembayaran sudah dilaporkan. Menunggu konfirmasi admin.'
                : 'Status booking: ' . $booking['status_pembayaran'] . '.';
            return redirect()->to('/pesan-stand/sukses/' . $kode)->with('message', $msg);
        }

        $db->table('pesanan_acara')
            ->where('id', (int) $booking['id'])
            ->update(['status_pembayaran' => 'menunggu_konfirmasi']);

        return redirect()->to('/pesan-stand/sukses/' . $kode)
            ->with('message', 'Terima kasih! Pembayaran Anda sedang diverifikasi oleh admin. Kami akan segera konfirmasi.');
    }

    // ====================================================================
    // STEP 6: Sukses
    // ====================================================================

    public function sukses(string $kode)
    {
        $pembeliId = (int) session()->get('pembeli_id');
        if (! $pembeliId) {
            return redirect()->to('/login')->with('error', 'Silakan login untuk melihat booking Anda.');
        }

        $db      = \Config\Database::connect();
        $booking = $db->table('pesanan_acara')
            ->where('kode_booking', $kode)
            ->where('pembeli_id', $pembeliId)
            ->get()->getRowArray();

        if (! $booking) {
            return redirect()->to('/pesan-stand')->with('error', 'Booking tidak ditemukan.');
        }

        $items = $db->table('item_pesanan_acara ipa')
            ->select('ipa.jumlah, ipa.harga_satuan_snapshot, ipa.subtotal_item, p.nama AS produk_nama')
            ->join('produk p', 'p.id = ipa.produk_id', 'left')
            ->where('ipa.pesanan_acara_id', (int) $booking['id'])
            ->get()->getResultArray();

        // Ambil nomor HP admin untuk tombol WA
        $adminHp = '';
        $pengaturan = (new PengaturanModel())->getSingleton();
        if (! empty($pengaturan['admin_hp'] ?? null)) {
            $adminHp = (string) $pengaturan['admin_hp'];
        } else {
            $admin = (new \App\Models\AdminModel())->first();
            if ($admin && ! empty($admin['nomor_hp'])) {
                $adminHp = (string) $admin['nomor_hp'];
            }
        }

        return view('pesan-stand/sukses', [
            'title'   => 'Booking Berhasil — Siomay Dua Putri',
            'booking' => $booking,
            'items'   => $items,
            'adminHp' => $adminHp,
        ]);
    }

    // ====================================================================
    // Private helpers
    // ====================================================================

    /**
     * Ambil data pembeli yang sedang login dari session.
     */
    private function getLoggedInPembeli(): array
    {
        $pembeli = [];
        $pembeliId = (int) session()->get('pembeli_id');
        if ($pembeliId) {
            $row = (new \App\Models\PembeliModel())->find($pembeliId);
            if ($row) {
                $pembeli = (array) $row;
            }
        }
        return $pembeli;
    }

    /**
     * Guard: pastikan session form data acara (step 2) sudah terisi.
     * Jika belum, redirect ke form.
     */
    private function guardFormSession(): void
    {
        if (! session()->get('stand_nama') || ! session()->get('stand_tanggal_acara')) {
            redirect()->to('/pesan-stand/form')
                ->with('error', 'Lengkapi data acara terlebih dahulu.')
                ->send();
            exit;
        }
    }

    /**
     * Finalisasi booking:
     * - Generate kode_booking unik
     * - INSERT pesanan_acara
     * - INSERT item_pesanan_acara (harga dari DB, bukan session)
     * - Clear stand_cart dan session form acara
     *
     * Return kode_booking jika berhasil, null jika gagal.
     */
    private function finalisasiBooking(int $pembeliId): ?string
    {
        $produkModel = new ProdukModel();
        $pengaturan  = (new PengaturanModel())->getSingleton();
        $biayaStand  = (float) ($pengaturan['biaya_stand'] ?? 0);

        // Hydrate cart (harga dari DB)
        $cartView = StandCartService::hydrate($produkModel);
        if (empty($cartView['rows'])) {
            return null;
        }

        $subtotal = $cartView['subtotal'];
        $total    = $subtotal + $biayaStand;

        // Ambil data form dari session
        $namaPemesan    = (string) session()->get('stand_nama');
        $nomorHp        = (string) session()->get('stand_wa');
        $jenisAcara     = (string) session()->get('stand_jenis_acara');
        $namaAcara      = (string) session()->get('stand_nama_acara');
        $tanggalAcara   = (string) session()->get('stand_tanggal_acara');
        $lokasiAcara    = (string) session()->get('stand_lokasi_acara');
        $estimasiTamu   = session()->get('stand_estimasi_tamu');
        $catatan        = (string) (session()->get('stand_catatan') ?? '');

        if (! $namaPemesan || ! $tanggalAcara) {
            return null;
        }

        $db = \Config\Database::connect();

        // Generate kode_booking unik: STD-YYYYMMDD-XXXXXX
        do {
            $suffix      = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $kodeBooking = 'STD-' . date('Ymd') . '-' . $suffix;
            $exists      = $db->table('pesanan_acara')
                ->where('kode_booking', $kodeBooking)
                ->countAllResults();
        } while ($exists > 0);

        // INSERT pesanan_acara
        $insertData = [
            'pembeli_id'       => $pembeliId,
            'kode_booking'     => $kodeBooking,
            'nama_pemesan'     => $namaPemesan,
            'nomor_hp'         => $nomorHp,
            'jenis_acara'      => $jenisAcara,
            'nama_acara'       => $namaAcara,
            'tanggal_acara'    => $tanggalAcara,
            'lokasi_acara'     => $lokasiAcara,
            'estimasi_porsi'   => ($estimasiTamu !== null && $estimasiTamu !== '') ? (int) $estimasiTamu : null,
            'catatan'          => $catatan !== '' ? $catatan : null,
            'subtotal'         => $subtotal,
            'biaya_stand'      => $biayaStand,
            'total'            => $total,
            'status_pembayaran' => 'pending',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $db->table('pesanan_acara')->insert($insertData);
        $pesananAcaraId = $db->insertID();

        if (! $pesananAcaraId) {
            return null;
        }

        // INSERT item_pesanan_acara
        foreach ($cartView['rows'] as $row) {
            $db->table('item_pesanan_acara')->insert([
                'pesanan_acara_id'       => $pesananAcaraId,
                'produk_id'              => (int) $row['produk']['id'],
                'jumlah'                 => (int) $row['jumlah'],
                'harga_satuan_snapshot'  => (float) $row['harga'],
                'subtotal_item'          => (float) $row['subtotal_item'],
            ]);
        }

        // Clear cart dan session form
        StandCartService::clear();
        StandCartService::clearFormSession();

        return $kodeBooking;
    }
}
