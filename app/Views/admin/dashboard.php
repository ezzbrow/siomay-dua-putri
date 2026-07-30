<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin — Siomay Dua Putri</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #F3F4F6; color: #111; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        header .duaputri { color: #DC2626; }
        main { max-width: 960px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 16px; }
        .btn { display: inline-block; padding: 8px 14px; background: #DC2626; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 0.95em; }
        .btn.secondary { background: #6B7280; }
        .ringkasan { display: flex; gap: 24px; flex-wrap: wrap; }
        .ringkasan .angka { font-size: 1.8em; font-weight: bold; color: #1D4ED8; }
        .ringkasan .label { color: #6B7280; font-size: 0.9em; }
    </style>
</head>
<body>
<header>
    <strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Dashboard
    <form method="post" action="<?= base_url('admin/logout') ?>" style="margin:0;">
        <button class="btn secondary" type="submit">Logout</button>
    </form>
</header>
<main>
    <h1>Selamat datang, <?= esc($nama_toko ?? 'Admin') ?></h1>

    <div class="card">
        <h2>Ringkasan Hari Ini</h2>
        <div class="ringkasan">
            <div>
                <div class="angka">Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></div>
                <div class="label">Omzet Hari Ini</div>
            </div>
            <div>
                <div class="angka"><?= $jumlah_transaksi_hari_ini ?></div>
                <div class="label">Jumlah Transaksi</div>
            </div>
        </div>
    </div>

    <div class="card">
        <p>Login sebagai: <strong><?= esc($username ?? '-') ?></strong></p>
        <p>
            <a class="btn" href="<?= base_url('admin/produk') ?>">Kelola Produk</a>
            <a class="btn secondary" href="<?= base_url('admin/riwayat') ?>">Riwayat Transaksi</a>
            <a class="btn secondary" href="<?= base_url('admin/laporan') ?>">Laporan</a>
            <a class="btn secondary" href="<?= base_url('admin/laba-rugi') ?>">Laba-Rugi</a>
        </p>
    </div>
    
    <div class="card">
        <h2>Pesanan Lunas — Perlu Diproses</h2>
        <?php if (empty($pesanan_lunas)): ?>
            <p>Belum ada pesanan lunas.</p>
        <?php else: ?>
        <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <tr><th>Kode</th><th>Tanggal Dibutuhkan</th><th>Metode</th><th>Total</th></tr>
        <?php foreach ($pesanan_lunas as $p): ?>
        <tr>
            <td><?= esc($p['kode_pesanan']) ?></td>
            <td><?= esc($p['tanggal_dibutuhkan']) ?></td>
            <td><?= esc($p['metode']) ?></td>
            <td>Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

<a class="btn secondary" href="<?= base_url('admin/konfirmasi-pembayaran') ?>">Konfirmasi Pembayaran</a>
    
</main>
</body>
</html>
