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
        <p>Login sebagai: <strong><?= esc($username ?? '-') ?></strong></p>
        <p>
            <a class="btn" href="<?= base_url('admin/produk') ?>">Kelola Produk</a>
        </p>
    </div>
</main>
</body>
</html>
