<?php
$flashMsg = session()->getFlashdata('message');
$flashErr = session()->getFlashdata('error');
$errors   = session()->getFlashdata('errors') ?? [];
$old = function ($k, $d = '') use ($p) { return old($k, $p[$k] ?? $d); };
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan — Admin Siomay Dua Putri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        :root { --primary:#4C1D95; --primary-hover:#6D28D9; --secondary-fixed:#ebddff; --on-surface:#1d1a22; --on-surface-variant:#4a4452; --outline:#e7e0eb; --surface:#fff; --ok:#166534; --err:#991B1B; }
        * { box-sizing:border-box; }
        body { font-family:"Be Vietnam Pro",system-ui,sans-serif; background:#F8F6FF; color:var(--on-surface); margin:0; line-height:1.55; }
        .wrap { max-width:720px; margin:0 auto; padding:32px 16px 80px; }
        h1 { font-size:1.5rem; font-weight:800; color:var(--primary); margin:0 0 8px; }
        .lead { color:var(--on-surface-variant); margin:0 0 24px; }
        .card { background:var(--surface); border:1px solid var(--outline); border-radius:20px; padding:24px; box-shadow:0 10px 30px rgba(76,29,149,0.08); }
        .field { margin-bottom:16px; }
        .field label { display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem; }
        .field input[type="text"], .field input[type="number"], .field input[type="time"], .field textarea { width:100%; box-sizing:border-box; padding:10px 12px; border:1.5px solid var(--outline); border-radius:12px; font-family:inherit; min-height:44px; }
        .field textarea { resize:vertical; }
        .field-row { display:flex; gap:12px; }
        .field-row > * { flex:1; }
        .checkbox { display:flex; align-items:center; gap:8px; }
        .checkbox input { width:18px; height:18px; }
        .flash { padding:12px 16px; border-radius:12px; margin-bottom:16px; font-weight:500; }
        .flash-ok { background:#E6F6EC; color:var(--ok); }
        .flash-err { background:#FCE6E6; color:var(--err); }
        .err-item { font-size:0.85rem; color:var(--err); margin-top:2px; }
        .field-hint { font-size:0.85rem; color:var(--on-surface-variant); margin:6px 0 0; }
        .qris-preview { margin-bottom:10px; }
        .qris-preview img { max-width:180px; border:1.5px solid var(--outline); border-radius:12px; display:block; }
        .btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:11px 20px; background:var(--primary); color:#fff; border:none; border-radius:12px; font-weight:600; cursor:pointer; font-size:0.95rem; min-height:44px; }
        .btn-primary:hover { background:var(--primary-hover); color:#fff; }
        .back { margin-top:20px; }
        .back a { color:var(--primary); text-decoration:none; font-weight:600; }
        .material-symbols-outlined { vertical-align:middle; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Pengaturan</h1>
    <p class="lead">Konfigurasi minimum order, pajak, jam operasional, dan biaya stand untuk Pesan Stand (F21).</p>

    <?php if ($flashMsg): ?><div class="flash flash-ok"><?= esc($flashMsg) ?></div><?php endif; ?>
    <?php if ($flashErr): ?><div class="flash flash-err"><?= esc($flashErr) ?></div><?php endif; ?>

    <form method="post" action="<?= base_url('admin/pengaturan/save') ?>" enctype="multipart/form-data" class="card">
        <?= csrf_field() ?>

        <div class="field-row">
            <div class="field">
                <label for="minimum_order">Minimum Order (Rp)</label>
                <input type="number" id="minimum_order" name="minimum_order" min="0" step="1000" required
                       value="<?= esc((string) $old('minimum_order', 100000)) ?>">
            </div>
            <div class="field">
                <label for="biaya_stand">Biaya Stand (Rp) — untuk Pesan Stand</label>
                <input type="number" id="biaya_stand" name="biaya_stand" min="0" step="1000" required
                       value="<?= esc((string) $old('biaya_stand', 0)) ?>">
            </div>
        </div>

        <div class="field-row">
            <div class="field">
                <label>Pajak (PPN)</label>
                <div class="checkbox">
                    <input type="checkbox" id="pajak_aktif" name="pajak_aktif" value="1" <?= ((int) $old('pajak_aktif', 0)) === 1 ? 'checked' : '' ?>>
                    <label for="pajak_aktif" style="margin:0;">Aktifkan pajak</label>
                </div>
            </div>
            <div class="field">
                <label for="pajak_persen">Persentase Pajak (%)</label>
                <input type="number" id="pajak_persen" name="pajak_persen" min="0" max="100" step="0.01" required
                       value="<?= esc((string) $old('pajak_persen', 10)) ?>">
            </div>
        </div>

        <div class="field">
            <label for="alamat_umkm">Alamat UMKM</label>
            <textarea id="alamat_umkm" name="alamat_umkm" rows="2" maxlength="500"><?= esc($old('alamat_umkm')) ?></textarea>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="jam_buka">Jam Buka</label>
                <input type="time" id="jam_buka" name="jam_buka"
                       value="<?= esc($old('jam_buka')) ?>">
            </div>
            <div class="field">
                <label for="jam_tutup">Jam Tutup</label>
                <input type="time" id="jam_tutup" name="jam_tutup"
                       value="<?= esc($old('jam_tutup')) ?>">
            </div>
        </div>

        <div class="field">
            <label for="qris_image">Gambar QRIS Statis</label>
            <?php if (! empty($p['qris_image'])): ?>
                <div class="qris-preview">
                    <img src="<?= base_url('uploads/qris/' . $p['qris_image']) ?>" alt="QRIS saat ini">
                </div>
            <?php endif; ?>
            <input type="file" id="qris_image" name="qris_image" accept="image/png,image/jpeg">
            <p class="field-hint">Format JPG/PNG, maksimal 2MB. Kosongkan kalau tidak ingin mengganti gambar yang sudah ada.</p>
            <?php if (! empty($errors['qris_image'])): ?>
                <p class="err-item"><?= esc($errors['qris_image']) ?></p>
            <?php endif; ?>
        </div>

        <button class="btn-primary" type="submit">
            <span class="material-symbols-outlined">save</span>
            Simpan Pengaturan
        </button>
    </form>

    <p class="back"><a href="<?= base_url('admin/dashboard') ?>">← Kembali ke Dashboard</a></p>
</div>
</body>
</html>
