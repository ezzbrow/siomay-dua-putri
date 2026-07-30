<?= $this->include('partials/header') ?>

<main class="wizard-main">
    <?= $this->include('partials/stand-progress', ['current' => 4]) ?>

    <div class="wizard-heading">
        <h1 class="wizard-title">
            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
            Ringkasan Booking
        </h1>
        <p class="wizard-tagline">Pastikan semua informasi benar sebelum melanjutkan ke pembayaran.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Rekap Data Acara -->
    <div class="ringkasan-card">
        <div class="ringkasan-card-header">
            <span class="material-symbols-outlined" aria-hidden="true">event</span>
            Data Acara
            <a href="<?= base_url('pesan-stand/form') ?>" class="ringkasan-edit-link" id="link-edit-form">
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                Ubah
            </a>
        </div>
        <div class="ringkasan-info-grid">
            <div class="ringkasan-info-row">
                <span class="info-label">Nama Pemesan</span>
                <span class="info-value"><?= esc($stand_nama) ?></span>
            </div>
            <div class="ringkasan-info-row">
                <span class="info-label">Nomor WhatsApp</span>
                <span class="info-value"><?= esc($stand_wa) ?></span>
            </div>
            <div class="ringkasan-info-row">
                <span class="info-label">Jenis Acara</span>
                <span class="info-value">
                    <?php
                    $labelAcara = [
                        'pernikahan'    => 'Pernikahan',
                        'ulang_tahun'   => 'Ulang Tahun',
                        'arisan'        => 'Arisan',
                        'pengajian'     => 'Pengajian',
                        'seminar'       => 'Seminar',
                        'grand_opening' => 'Grand Opening',
                        'lainnya'       => 'Lainnya',
                    ];
                    echo esc($labelAcara[$stand_jenis_acara] ?? $stand_jenis_acara);
                    ?>
                </span>
            </div>
            <div class="ringkasan-info-row">
                <span class="info-label">Nama Acara</span>
                <span class="info-value"><?= esc($stand_nama_acara) ?></span>
            </div>
            <div class="ringkasan-info-row">
                <span class="info-label">Tanggal Acara</span>
                <span class="info-value"><?= esc(date('d F Y', strtotime($stand_tanggal_acara))) ?></span>
            </div>
            <div class="ringkasan-info-row">
                <span class="info-label">Lokasi Acara</span>
                <span class="info-value"><?= esc($stand_lokasi_acara) ?></span>
            </div>
            <?php if ($stand_estimasi_tamu): ?>
            <div class="ringkasan-info-row">
                <span class="info-label">Estimasi Tamu</span>
                <span class="info-value"><?= esc($stand_estimasi_tamu) ?> orang</span>
            </div>
            <?php endif; ?>
            <?php if ($stand_catatan): ?>
            <div class="ringkasan-info-row">
                <span class="info-label">Catatan</span>
                <span class="info-value"><?= esc($stand_catatan) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Daftar Menu -->
    <div class="ringkasan-card">
        <div class="ringkasan-card-header">
            <span class="material-symbols-outlined" aria-hidden="true">restaurant_menu</span>
            Menu yang Dipilih
            <a href="<?= base_url('pesan-stand/menu') ?>" class="ringkasan-edit-link" id="link-edit-menu">
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                Ubah
            </a>
        </div>
        <?php if (! empty($cartView['rows'])): ?>
            <div class="ringkasan-items">
                <?php foreach ($cartView['rows'] as $row): ?>
                    <div class="ringkasan-item-row">
                        <span class="ringkasan-item-nama"><?= esc($row['produk']['nama']) ?></span>
                        <span class="ringkasan-item-detail">
                            <?= $row['jumlah'] ?> pcs × Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </span>
                        <span class="ringkasan-item-subtotal">Rp <?= number_format($row['subtotal_item'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ringkasan-empty">
                <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                Belum ada menu dipilih.
                <a href="<?= base_url('pesan-stand/menu') ?>">Pilih menu</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kalkulasi Total -->
    <div class="ringkasan-card ringkasan-total-card">
        <div class="ringkasan-card-header">
            <span class="material-symbols-outlined" aria-hidden="true">payments</span>
            Kalkulasi Pembayaran
        </div>
        <div class="total-lines">
            <div class="total-line">
                <span>Subtotal Menu</span>
                <span>Rp <?= number_format($cartView['subtotal'], 0, ',', '.') ?></span>
            </div>
            <div class="total-line">
                <span>Biaya Stand</span>
                <span>Rp <?= number_format($biayaStand, 0, ',', '.') ?></span>
            </div>
            <div class="total-line total-grand">
                <span>Total Pembayaran</span>
                <span class="total-grand-amount">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>
        <div class="total-info">
            <span class="material-symbols-outlined" aria-hidden="true">info</span>
            <div>
                Pembayaran dilakukan via <strong>QRIS statis</strong> di langkah berikutnya.
                Admin akan mengonfirmasi booking setelah pembayaran terverifikasi.
            </div>
        </div>
    </div>

    <!-- Navigasi -->
    <?php if (! empty($cartView['rows'])): ?>
        <form action="<?= base_url('pesan-stand/ringkasan') ?>" method="post" class="ringkasan-actions">
            <?= csrf_field() ?>
            <a href="<?= base_url('pesan-stand/menu') ?>" class="wizard-btn wizard-btn-outline" id="btn-kembali-menu">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Kembali
            </a>
            <button type="submit" class="wizard-btn wizard-btn-primary" id="btn-lanjut-pembayaran">
                <span class="material-symbols-outlined" aria-hidden="true">qr_code_2</span>
                Lanjut ke Pembayaran
            </button>
        </form>
    <?php else: ?>
        <div class="ringkasan-actions">
            <a href="<?= base_url('pesan-stand/menu') ?>" class="wizard-btn wizard-btn-primary" style="width:100%;justify-content:center;">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Pilih Menu Dulu
            </a>
        </div>
    <?php endif; ?>
</main>

<style>
    .wizard-main { max-width: 640px; margin: 0 auto; padding: 32px 20px 48px; }
    .wizard-heading { text-align: center; margin-bottom: 24px; }
    .wizard-title {
        font-size: 1.5rem; font-weight: 800; color: var(--on-surface);
        margin: 0 0 8px; display: inline-flex; align-items: center; gap: 10px;
    }
    .wizard-title .material-symbols-outlined { font-size: 28px; color: var(--primary); }
    .wizard-tagline { color: var(--on-surface-variant); margin: 0; font-size: 0.95rem; }
    .wizard-flash {
        padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem;
    }
    .wizard-flash-err { background: var(--err-bg); color: var(--err-fg); border: 1px solid rgba(153,27,27,0.18); }
    .wizard-flash .material-symbols-outlined { font-size: 20px; }

    .ringkasan-card {
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .ringkasan-card-header {
        display: flex; align-items: center; gap: 8px;
        font-weight: 700; font-size: 0.95rem; color: var(--on-surface);
        padding: 14px 20px;
        background: var(--secondary-fixed);
        border-bottom: 1px solid var(--outline-variant);
    }
    .ringkasan-card-header .material-symbols-outlined { font-size: 20px; color: var(--primary); }
    .ringkasan-edit-link {
        margin-left: auto;
        display: flex; align-items: center; gap: 4px;
        font-size: 0.82rem; color: var(--primary); text-decoration: none; font-weight: 600;
    }
    .ringkasan-edit-link:hover { text-decoration: underline; }
    .ringkasan-edit-link .material-symbols-outlined { font-size: 16px; }

    .ringkasan-info-grid { padding: 16px 20px; }
    .ringkasan-info-row {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 6px 0; border-bottom: 1px dashed var(--outline-variant); font-size: 0.9rem;
    }
    .ringkasan-info-row:last-child { border-bottom: none; }
    .info-label { color: var(--on-surface-variant); flex-shrink: 0; margin-right: 12px; }
    .info-value { font-weight: 600; text-align: right; color: var(--on-surface); }

    .ringkasan-items { padding: 12px 20px; }
    .ringkasan-item-row {
        display: grid; grid-template-columns: 1fr auto auto; gap: 8px;
        align-items: center; padding: 8px 0;
        border-bottom: 1px dashed var(--outline-variant); font-size: 0.88rem;
    }
    .ringkasan-item-row:last-child { border-bottom: none; }
    .ringkasan-item-nama { font-weight: 600; color: var(--on-surface); }
    .ringkasan-item-detail { color: var(--on-surface-variant); white-space: nowrap; }
    .ringkasan-item-subtotal { font-weight: 700; color: var(--primary); text-align: right; }
    .ringkasan-empty {
        display: flex; align-items: center; gap: 8px;
        padding: 16px 20px; color: var(--on-surface-variant); font-size: 0.9rem;
    }
    .ringkasan-empty .material-symbols-outlined { font-size: 20px; }

    .total-lines { padding: 16px 20px 8px; }
    .total-line {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; font-size: 0.92rem; color: var(--on-surface);
    }
    .total-line span:first-child { color: var(--on-surface-variant); }
    .total-grand {
        font-weight: 800; font-size: 1.1rem; margin-top: 8px;
        padding-top: 12px; border-top: 2px solid var(--outline-variant);
    }
    .total-grand span:first-child { color: var(--on-surface); }
    .total-grand-amount { color: var(--primary); font-size: 1.3rem; }

    .total-info {
        display: flex; gap: 8px; align-items: flex-start;
        padding: 12px 20px;
        background: var(--secondary-fixed);
        border-top: 1px solid var(--outline-variant);
        font-size: 0.82rem; color: var(--on-surface-variant);
    }
    .total-info .material-symbols-outlined { font-size: 18px; color: var(--secondary); flex-shrink: 0; margin-top: 1px; }

    .ringkasan-actions {
        display: flex; gap: 12px; margin-top: 8px;
    }
    .wizard-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 13px 20px; border-radius: var(--radius-md);
        font-size: 1rem; font-weight: 700; text-decoration: none;
        min-height: 48px; cursor: pointer; border: none;
        transition: background var(--t-fast), transform var(--t-fast);
        box-sizing: border-box; font-family: inherit;
    }
    .wizard-btn-primary { flex: 1; background: var(--primary); color: #fff; }
    .wizard-btn-primary:hover { background: var(--primary-hover); color: #fff; transform: translateY(-1px); }
    .wizard-btn-outline {
        background: transparent;
        color: var(--on-surface-variant);
        border: 1.5px solid var(--outline-variant);
        flex-shrink: 0;
    }
    .wizard-btn-outline:hover { background: var(--surface-variant); color: var(--on-surface); }
    .wizard-btn .material-symbols-outlined { font-size: 20px; }
</style>

<?= $this->include('partials/footer') ?>
