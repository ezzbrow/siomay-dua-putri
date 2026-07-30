<?= $this->include('partials/header') ?>

<main class="wizard-main">
    <?= $this->include('partials/progress', ['current' => 6]) ?>

    <div class="wizard-heading">
        <h1 class="wizard-title">
            <span class="material-symbols-outlined" aria-hidden="true">qr_code_2</span>
            Pembayaran QRIS
        </h1>
        <p class="wizard-tagline">
            Scan QRIS di bawah ini menggunakan aplikasi pembayaran favoritmu.
        </p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="payment-card">
        <div class="payment-accent" aria-hidden="true"></div>

        <!-- QRIS Box -->
        <div class="qris-box">
            <span class="qris-badge">QRIS</span>
            <?php if ($qrisImageUrl): ?>
                <div class="qris-img-wrap">
                    <img src="<?= esc($qrisImageUrl) ?>" alt="QRIS Siomay Dua Putri" class="qris-img">
                </div>
                <p class="qris-note">
                    <span class="material-symbols-outlined" aria-hidden="true">info</span>
                    QRIS ini berlaku untuk semua bank &amp; dompet digital
                </p>
            <?php else: ?>
                <div class="qris-img-wrap qris-fallback">
                    <span class="material-symbols-outlined" aria-hidden="true">qr_code_2</span>
                    <p>QRIS belum tersedia. Hubungi admin untuk informasi pembayaran.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="payment-summary">
            <div class="summary-line">
                <span>Nomor Pesanan</span>
                <span class="mono"><?= esc($pesanan['kode_pesanan']) ?></span>
            </div>
            <div class="summary-line">
                <span>Atas Nama</span>
                <span><?= esc($pesanan['nama_pembeli']) ?></span>
            </div>
            <div class="summary-line">
                <span>Metode</span>
                <span><?= $pesanan['metode'] === 'ambil_sendiri' ? 'Ambil Sendiri' : 'Diantar' ?></span>
            </div>
            <div class="summary-line">
                <span>Tanggal Dibutuhkan</span>
                <span><?= date('d M Y', strtotime($pesanan['tanggal_dibutuhkan'])) ?></span>
            </div>
            <?php if (! empty($items)): ?>
                <div class="summary-items">
                    <?php foreach ($items as $item): ?>
                        <div class="summary-item-row">
                            <span><?= esc($item['produk_nama']) ?><?= $item['nama_varian'] ? ' (' . esc($item['nama_varian']) . ')' : '' ?></span>
                            <span><?= number_format((float) $item['jumlah'], 2, ',', '.') ?> × Rp <?= number_format((float) $item['harga_satuan'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ((float) $pesanan['pajak'] > 0): ?>
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>Rp <?= number_format((float) $pesanan['subtotal'], 0, ',', '.') ?></span>
                </div>
                <div class="summary-line">
                    <span>Pajak</span>
                    <span>Rp <?= number_format((float) $pesanan['pajak'], 0, ',', '.') ?></span>
                </div>
            <?php endif; ?>
            <div class="summary-line total">
                <span>Total Bayar</span>
                <span class="amount">Rp <?= number_format($grossAmount, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Instruksi -->
        <div class="payment-info">
            <span class="material-symbols-outlined" aria-hidden="true">info</span>
            <div>
                <strong>Cara bayar:</strong> Scan QRIS → masukkan nominal <strong>Rp <?= number_format($grossAmount, 0, ',', '.') ?></strong> → bayar → klik tombol <em>"Saya Sudah Bayar"</em> di bawah. Admin akan konfirmasi setelah cek mutasi.
            </div>
        </div>

        <div class="payment-actions">
            <a class="wizard-btn wizard-btn-primary" id="btn-sudah-bayar"
               href="<?= base_url('checkout/konfirmasi-bayar/' . $pesanan['kode_pesanan']) ?>">
                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                Saya Sudah Bayar
            </a>
        </div>
    </div>
</main>

<style>
    .wizard-main { max-width: 640px; margin: 0 auto; padding: 32px 20px 0; }
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
    .wizard-flash-err { background: var(--err-bg); color: var(--err-fg); border: 1px solid rgba(153, 27, 27, 0.18); }
    .wizard-flash .material-symbols-outlined { font-size: 20px; }

    .payment-card {
        position: relative;
        background: var(--surface); border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg); box-shadow: var(--card-shadow);
        padding: 0; overflow: hidden;
    }
    .payment-accent {
        position: absolute; top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, var(--secondary), var(--primary));
    }

    .qris-box {
        background: var(--secondary-fixed);
        padding: 28px 20px 20px; text-align: center;
    }
    .qris-badge {
        display: inline-block;
        background: var(--secondary); color: #fff;
        font-size: 0.7rem; font-weight: 800;
        padding: 3px 12px; border-radius: 999px;
        letter-spacing: 0.05em; margin-bottom: 12px;
    }
    .qris-img-wrap {
        background: #fff;
        border: 1.5px solid var(--outline-variant);
        border-radius: var(--radius-md);
        padding: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }
    .qris-img {
        max-width: 240px; width: 100%; height: auto; display: block;
        image-rendering: pixelated;
    }
    .qris-fallback {
        width: 220px; padding: 32px 16px;
        color: var(--on-surface-variant);
    }
    .qris-fallback .material-symbols-outlined { font-size: 48px; color: var(--muted, #A89889); display: block; margin-bottom: 8px; }
    .qris-fallback p { margin: 0; font-size: 0.85rem; }
    .qris-note {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 0.8rem; color: var(--on-surface-variant);
        margin: 0;
    }
    .qris-note .material-symbols-outlined { font-size: 15px; }

    .payment-summary {
        padding: 16px 24px;
        border-top: 1px dashed var(--outline-variant);
        border-bottom: 1px dashed var(--outline-variant);
    }
    .summary-line {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 4px 0; font-size: 0.9rem;
    }
    .summary-line span:first-child { color: var(--on-surface-variant); }
    .summary-line .mono {
        font-family: ui-monospace, "SF Mono", Consolas, monospace;
        font-size: 0.85rem;
    }
    .summary-items {
        border-top: 1px dashed var(--outline-variant);
        margin: 6px 0;
        padding-top: 4px;
    }
    .summary-item-row {
        display: flex; justify-content: space-between;
        font-size: 0.82rem; color: var(--on-surface-variant);
        padding: 2px 0;
    }
    .summary-line.total {
        font-weight: 700; font-size: 1.1rem;
        margin-top: 6px; padding-top: 10px;
        border-top: 1px solid var(--outline-variant);
    }
    .summary-line.total .amount {
        color: var(--primary);
        font-variant-numeric: tabular-nums;
        font-size: 1.3rem;
    }

    .payment-info {
        display: flex; gap: 10px; align-items: flex-start;
        padding: 14px 24px;
        background: var(--secondary-fixed);
        color: var(--on-surface);
        font-size: 0.85rem;
    }
    .payment-info .material-symbols-outlined { font-size: 20px; color: var(--secondary); flex-shrink: 0; }
    .payment-info strong { display: inline; }

    .payment-actions {
        padding: 20px 24px;
    }
    .wizard-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        width: 100%; padding: 13px 20px; border-radius: var(--radius-md);
        font-size: 1rem; font-weight: 700; text-decoration: none;
        min-height: 48px; cursor: pointer; border: none;
        transition: background var(--t-fast), transform var(--t-fast);
        box-sizing: border-box;
    }
    .wizard-btn-primary { background: var(--primary); color: #fff; }
    .wizard-btn-primary:hover { background: var(--primary-hover); color: #fff; transform: translateY(-1px); }
    .wizard-btn .material-symbols-outlined { font-size: 20px; }

    #btn-sudah-bayar:active { transform: scale(0.98); }
</style>

<?= $this->include('partials/footer') ?>
