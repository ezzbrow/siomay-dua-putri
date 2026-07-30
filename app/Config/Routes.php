<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Landing::index');

$routes->get('etalase', 'Etalase::index');

$routes->get('keranjang',              'Keranjang::index');
$routes->post('keranjang/tambah',      'Keranjang::tambah');
$routes->post('keranjang/kurang',      'Keranjang::kurang');
$routes->post('keranjang/hapus',       'Keranjang::hapus');
$routes->post('keranjang/catatan',     'Keranjang::simpanCatatan');

$routes->get('daftar',                 'PembeliAuth::register');
$routes->post('daftar',                'PembeliAuth::storeRegister');
$routes->get('login',                  'PembeliAuth::login');
$routes->post('login',                 'PembeliAuth::attemptLogin');
$routes->post('logout',                'PembeliAuth::logout');

$routes->get('akun/riwayat',           'PembeliAkun::riwayat', ['filter' => 'customerAuth']);

$routes->group('checkout', ['filter' => 'customerAuth'], static function ($routes): void {
    // Wizard 6 step (lihat CLAUDE.md §3.2 + §11.4). Step 1 (Etalase) di luar
    // group ini; step 2 (Keranjang) pakai route /keranjang existing. Step 3+
    // masing-masing halaman terpisah. cart tetap di session.
    $routes->get('catatan',           'Checkout::catatan');
    $routes->post('catatan',          'Checkout::saveCatatan');
    $routes->get('tanggal',           'Checkout::tanggal');
    $routes->post('tanggal',          'Checkout::saveTanggal');
    $routes->get('metode',            'Checkout::metode');
    $routes->post('metode',           'Checkout::saveMetode');
    $routes->get('jemput',            'Checkout::jemput');
    $routes->post('jemput',           'Checkout::saveJemput');
    $routes->get('antar',             'Checkout::antar');
    $routes->post('antar',            'Checkout::saveAntar');
    $routes->get('pembayaran',        'Checkout::pembayaran');
    // Endpoint polling manual user "Saya Sudah Bayar"
    $routes->get('konfirmasi-bayar/(:segment)', 'Checkout::konfirmasiBayar/$1');
    // Sukses (halaman terakhir) tetap pakai pola existing
    $routes->get('sukses/(:segment)', 'Checkout::sukses/$1');
});

// Webhook Midtrans — di luar group customerAuth (Midtrans tidak punya akun pembeli).
// Akan exclude dari CSRF via 'csrf' => 'exclude' option jika diperlukan.
$routes->post('webhook/midtrans', 'MidtransWebhook::index');

$routes->group('admin', static function ($routes): void {
    $routes->get('/', static fn () => redirect()->to('/admin/dashboard'));

    $routes->get('register',     'Admin\\Auth::register');
    $routes->post('register',    'Admin\\Auth::storeRegister');
    $routes->get('login',        'Admin\\Auth::login');
    $routes->post('login',       'Admin\\Auth::attemptLogin');
    $routes->post('logout',      'Admin\\Auth::logout');

    $routes->get('dashboard', 'Admin\\Dashboard::index', ['filter' => 'auth']);
    $routes->get('riwayat', 'Admin\Dashboard::riwayat', ['filter' => 'auth']);
    $routes->get('laporan', 'Admin\Dashboard::laporan', ['filter' => 'auth']);
    $routes->get('laporan/export', 'Admin\Dashboard::exportCsv', ['filter' => 'auth']);
    $routes->get('laba-rugi', 'Admin\Dashboard::labaRugi', ['filter' => 'auth']);

    $routes->get('pengeluaran', 'Admin\Pengeluaran::index', ['filter' => 'auth']);
    $routes->post('pengeluaran/tambah', 'Admin\Pengeluaran::tambah', ['filter' => 'auth']);
    $routes->get('pengeluaran/hapus/(:num)', 'Admin\Pengeluaran::hapus/$1', ['filter' => 'auth']);

    $routes->group('pengaturan', ['filter' => 'auth'], static function ($routes): void {
        $routes->get('/',  'Admin\\Pengaturan::index');
        $routes->post('save', 'Admin\\Pengaturan::save');
    });

    $routes->group('produk', ['filter' => 'auth'], static function ($routes): void {
        $routes->get('/',                 'Admin\\ProdukAdmin::index');
        $routes->get('create',            'Admin\\ProdukAdmin::create');
        $routes->post('store',            'Admin\\ProdukAdmin::store');
        $routes->get('edit/(:num)',       'Admin\\ProdukAdmin::edit/$1');
        $routes->post('update/(:num)',    'Admin\\ProdukAdmin::update/$1');
        $routes->get('delete/(:num)',     'Admin\\ProdukAdmin::delete/$1');
        $routes->post('(:num)/varian',         'Admin\\ProdukAdmin::storeVarian/$1');
        $routes->post('(:num)/varian/(:num)/delete', 'Admin\\ProdukAdmin::deleteVarian/$1/$2');
    });
});

// =====================================================================
// Pesan Stand / Booking Acara (F21)
// =====================================================================
$routes->group('pesan-stand', static function ($routes): void {
    // Step 1: Tentang (bebas akses, tanpa filter)
    $routes->get('/', 'PesanStand::tentang');

    // Step 2: Form Data Acara (wajib login)
    $routes->get('form', 'PesanStand::form', ['filter' => 'customerAuth']);
    $routes->post('form', 'PesanStand::saveForm', ['filter' => 'customerAuth']);

    // Step 3: Pilih Menu Stand
    $routes->get('menu', 'PesanStand::menu', ['filter' => 'customerAuth']);
    $routes->post('menu/tambah', 'PesanStand::tambahMenu', ['filter' => 'customerAuth']);
    $routes->post('menu/kurang', 'PesanStand::kurangMenu', ['filter' => 'customerAuth']);

    // Step 4: Ringkasan + trigger finalisasi
    $routes->get('ringkasan', 'PesanStand::ringkasan', ['filter' => 'customerAuth']);
    $routes->post('ringkasan', 'PesanStand::lanjutPembayaran', ['filter' => 'customerAuth']);

    // Step 5: Pembayaran QRIS + konfirmasi "Saya Sudah Bayar"
    $routes->get('pembayaran', 'PesanStand::pembayaran', ['filter' => 'customerAuth']);
    $routes->post('konfirmasi-bayar/(:segment)', 'PesanStand::konfirmasiBayar/$1', ['filter' => 'customerAuth']);

    // Step 6: Sukses
    $routes->get('sukses/(:segment)', 'PesanStand::sukses/$1', ['filter' => 'customerAuth']);
});
