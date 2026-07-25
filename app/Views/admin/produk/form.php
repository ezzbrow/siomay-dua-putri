<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $mode === 'create' ? 'Tambah' : 'Edit' ?> Produk</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #F3F4F6; color: #111; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; }
        header .duaputri { color: #DC2626; }
        main { max-width: 720px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 16px; }
        label { display:block; font-weight: 600; margin: 12px 0 4px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 8px 14px; background: #DC2626; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 0.95em; }
        .btn.secondary { background: #6B7280; }
        .err { color: #B91C1C; font-size: 0.85em; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; background: #FEE2E2; color: #991B1B; }
        .varian-list { list-style: none; padding: 0; margin: 0; }
        .varian-list li { display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border: 1px solid #E5E7EB; border-radius: 6px; margin-bottom: 6px; }
        .row { display: flex; gap: 8px; align-items: center; }
    </style>
</head>
<body>
<header><strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Admin</header>
<main>
    <h1><?= $mode === 'create' ? 'Tambah' : 'Edit' ?> Produk</h1>
    <a class="btn secondary" href="<?= base_url('admin/produk') ?>">&larr; Kembali</a>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="msg"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <div class="card">
        <form method="post" action="<?= $mode === 'create' ? base_url('admin/produk/store') : base_url('admin/produk/update/' . $produk['id']) ?>">
            <label>Nama</label>
            <input type="text" name="nama" value="<?= esc(old('nama', $produk['nama'] ?? '')) ?>" required>
            <?php if (! empty($errors['nama'])): ?><div class="err"><?= esc($errors['nama']) ?></div><?php endif; ?>

            <label>Kategori</label>
            <select name="kategori" required>
                <?php foreach (['Somay Sapi', 'Lumpia', 'Pentol Goreng'] as $k): ?>
                    <option value="<?= $k ?>" <?= old('kategori', $produk['kategori'] ?? '') === $k ? 'selected' : '' ?>><?= $k ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (! empty($errors['kategori'])): ?><div class="err"><?= esc($errors['kategori']) ?></div><?php endif; ?>

            <label>Harga (Rp)</label>
            <input type="number" name="harga" min="0" step="1" value="<?= esc(old('harga', $produk['harga'] ?? '')) ?>" required>
            <?php if (! empty($errors['harga'])): ?><div class="err"><?= esc($errors['harga']) ?></div><?php endif; ?>

            <label>
                <input type="checkbox" name="status_aktif" value="1" <?= old('status_aktif', $produk['status_aktif'] ?? 1) ? 'checked' : '' ?>>
                Aktif (tampil di etalase)
            </label>

            <p style="margin-top:16px;">
                <button class="btn" type="submit">Simpan</button>
            </p>
        </form>
    </div>

    <?php $isLumpia = ($produk['kategori'] ?? '') === 'Lumpia'; ?>
    <?php if ($mode === 'edit' && $isLumpia): ?>
        <div class="card" id="varian-block">
            <h2 style="margin-top:0;">Varian (khusus Lumpia)</h2>
            <?php if (! empty($produk['varian'])): ?>
                <ul class="varian-list">
                    <?php foreach ($produk['varian'] as $v): ?>
                        <li>
                            <span><?= esc($v['nama_varian']) ?></span>
                            <form method="post" action="<?= base_url('admin/produk/' . $produk['id'] . '/varian/' . $v['id'] . '/delete') ?>" style="margin:0;">
                                <button class="btn secondary" type="submit" onclick="return confirm('Hapus varian ini?')">Hapus</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Belum ada varian.</p>
            <?php endif; ?>

            <form method="post" action="<?= base_url('admin/produk/' . $produk['id'] . '/varian') ?>" class="row" style="margin-top:8px;">
                <input type="text" name="nama_varian" placeholder="Nama varian (mis. Frozen / Digoreng)" required>
                <button class="btn" type="submit">Tambah</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
    (function () {
        var select = document.querySelector('select[name="kategori"]');
        var block  = document.getElementById('varian-block');
        if (!select) return;
        function sync() {
            if (block) {
                block.style.display = (select.value === 'Lumpia') ? '' : 'none';
            }
        }
        select.addEventListener('change', sync);
        if (block) { sync(); }
    })();
    </script>
</main>
</body>
</html>
