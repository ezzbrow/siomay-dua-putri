<?= $this->include('partials/header') ?>

<main>
    <?= $this->include('partials/progress', ['current' => 5]) ?>

    <h1 class="title">Ambil Sendiri</h1>
    <p class="lead">Isi data diri, nanti kamu ambil pesanan langsung di toko.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="info-toko">
            <h3>Lokasi Toko</h3>
            <p class="addr"><?= esc($pengaturan['alamat_umkm'] ?? 'Alamat UMKM belum diatur') ?></p>
            <?php if (! empty($pengaturan['alamat_umkm'])): ?>
                <iframe class="map"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=<?= urlencode($pengaturan['alamat_umkm']) ?>&output=embed"></iframe>
            <?php endif; ?>
        </div>

        <form method="post" action="<?= base_url('checkout/jemput') ?>" class="form">
            <?= csrf_field() ?>

            <div class="field">
                <label for="nama">Nama <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" required maxlength="255"
                       value="<?= esc(old('nama', (string) session()->get('pembeli_nama'))) ?>" autocomplete="name">
            </div>

            <div class="field">
                <label for="nomor_hp">Nomor WhatsApp <span class="req">*</span></label>
                <input type="tel" id="nomor_hp" name="nomor_hp" required maxlength="30"
                       value="<?= esc(old('nomor_hp')) ?>" placeholder="cth: 081234567890" autocomplete="tel">
            </div>

            <div class="actions">
                <a class="btn-secondary" href="<?= base_url('checkout/metode') ?>">← Kembali</a>
                <button class="btn-primary" type="submit">Lanjut →</button>
            </div>
        </form>
    </div>
</main>

<?= $this->include('partials/footer') ?>

<style>
    body { font-family: "Be Vietnam Pro", system-ui, sans-serif; background: #F8F6FF; color: #1d1a22; margin: 0; }
    main { max-width: 640px; margin: 0 auto; padding: 32px 16px 80px; }
    .title { font-size: 1.5rem; font-weight: 800; color: #4C1D95; margin: 0 0 8px; text-align: center; }
    .lead { color: #4a4452; text-align: center; margin: 0 0 24px; }
    .card { background: #fff; border: 1px solid #e7e0eb; border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(76,29,149,0.08); }
    .info-toko h3 { margin: 0 0 8px; color: #4C1D95; }
    .info-toko .addr { color: #1d1a22; margin: 0 0 12px; }
    .info-toko .map { width: 100%; height: 220px; border: 0; border-radius: 12px; margin-bottom: 20px; }
    .form .field { margin-bottom: 14px; }
    .form label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; }
    .form .req { color: #e19760; }
    .form input { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1.5px solid #e7e0eb; border-radius: 12px; font-family: inherit; min-height: 44px; }
    .flash { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; }
    .flash-err { background: #FCE6E6; color: #991B1B; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn-primary, .btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 11px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 0.95rem; min-height: 44px; flex: 1; }
    .btn-primary { background: #4C1D95; color: #fff; }
    .btn-primary:hover { background: #6D28D9; }
    .btn-secondary { background: #fff; color: #4C1D95; border: 1.5px solid #e7e0eb; }
    .btn-secondary:hover { background: #f3ebf6; }
</style>
