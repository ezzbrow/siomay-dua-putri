<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin · Daftar Produk</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #F3F4F6; color: #111; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; }
        header .duaputri { color: #DC2626; }
        main { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 8px 14px; background: #DC2626; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 0.95em; }
        .btn.secondary { background: #6B7280; }
        .btn.danger { background: #B91C1C; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #E5E7EB; }
        th { background: #F9FAFB; font-size: 0.85em; text-transform: uppercase; color: #6B7280; }
        .badge { display: inline-block; font-size: 0.75em; padding: 2px 8px; border-radius: 999px; background: #DCFCE7; color: #166534; }
        .badge.off { background: #FEE2E2; color: #991B1B; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .msg.ok { background: #DCFCE7; color: #166534; }
        .msg.err { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
<header><strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Admin</header>
<main>
    <div class="topbar">
        <h1>Daftar Produk</h1>
        <a class="btn" href="<?= base_url('admin/produk/create') ?>">+ Tambah Produk</a>
    </div>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="msg ok"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="msg err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produk as $p): ?>
            <tr>
                <td><?= esc($p['nama']) ?></td>
                <td><?= esc($p['kategori']) ?></td>
                <td>Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?></td>
                <td>
                    <?php if ((int) $p['status_aktif'] === 1): ?>
                        <span class="badge">Aktif</span>
                    <?php else: ?>
                        <span class="badge off">Nonaktif</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn secondary" href="<?= base_url('admin/produk/edit/' . $p['id']) ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($produk)): ?>
            <tr><td colspan="5" style="text-align:center;color:#6B7280;padding:24px;">Belum ada produk.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
