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
        .btn.confirm { background: #16A34A; }
        .flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; }
        .flash-ok { background: #DCFCE7; color: #166534; }
        .flash-err { background: #FEE2E2; color: #991B1B; }
        table.pesanan-list { width: 100%; border-collapse: collapse; }
        table.pesanan-list th, table.pesanan-list td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #E5E7EB; font-size: 0.9em; }
        table.pesanan-list th { color: #6B7280; font-weight: 600; }
        .empty-hint { color: #6B7280; font-size: 0.9em; }
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

    <?php if ($msg = session()->getFlashdata('message')): ?>
        <div class="flash flash-ok"><?= esc($msg) ?></div>
    <?php endif; ?>
    <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc($err) ?></div>
    <?php endif; ?>

    <div class="card">
        <p>Login sebagai: <strong><?= esc($username ?? '-') ?></strong></p>
        <p>
            <a class="btn" href="<?= base_url('admin/produk') ?>">Kelola Produk</a>
            <a class="btn secondary" href="<?= base_url('admin/pengaturan') ?>">Pengaturan</a>
        </p>
    </div>

    <div class="card">
        <h2>Menunggu Konfirmasi Pembayaran</h2>
        <?php if (empty($menungguKonfirmasi)): ?>
            <p class="empty-hint">Tidak ada pesanan yang menunggu konfirmasi saat ini.</p>
        <?php else: ?>
            <table class="pesanan-list">
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Tanggal Dibutuhkan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menungguKonfirmasi as $p): ?>
                        <tr>
                            <td><?= esc($p['kode_pesanan']) ?></td>
                            <td><?= esc($p['nama_pembeli']) ?></td>
                            <td><?= esc($p['nomor_hp']) ?></td>
                            <td><?= esc($p['metode']) ?></td>
                            <td>Rp<?= number_format((float) $p['total'], 0, ',', '.') ?></td>
                            <td><?= esc($p['tanggal_dibutuhkan']) ?></td>
                            <td>
                                <form method="post" action="<?= base_url('admin/dashboard/konfirmasi-lunas/' . $p['id']) ?>" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <button class="btn confirm" type="submit">Konfirmasi Lunas</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
