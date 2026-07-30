<?= $this->include('partials/header') ?>

<main class="wizard-main" style="max-width:720px;">
    <?= $this->include('partials/stand-progress', ['current' => 3]) ?>

    <div class="wizard-heading">
        <h1 class="wizard-title">
            <span class="material-symbols-outlined" aria-hidden="true">restaurant_menu</span>
            Pilih Menu Stand
        </h1>
        <p class="wizard-tagline">Pilih menu yang ingin disajikan di acara Anda. Tidak ada minimum order.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="menu-layout">
        <!-- Daftar Produk -->
        <div class="menu-produk-list">
            <?php foreach ($grouped as $kategori => $produkList): ?>
                <div class="menu-kategori-group">
                    <div class="menu-kategori-label">
                        <span class="material-symbols-outlined" aria-hidden="true">label</span>
                        <?= esc($kategori) ?>
                    </div>
                    <?php foreach ($produkList as $p): ?>
                        <?php $qty = (int) ($cart[$p['id']] ?? 0); ?>
                        <div class="menu-item-card" id="card-produk-<?= (int) $p['id'] ?>">
                            <div class="menu-item-info">
                                <div class="menu-item-nama"><?= esc($p['nama']) ?></div>
                                <div class="menu-item-harga">Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?> / pcs</div>
                            </div>
                            <div class="menu-item-qty">
                                <?php if ($qty > 0): ?>
                                    <form method="post" action="<?= base_url('pesan-stand/menu/kurang') ?>" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="produk_id" value="<?= (int) $p['id'] ?>">
                                        <button type="submit" class="qty-btn qty-minus" aria-label="Kurangi <?= esc($p['nama']) ?>">
                                            <span class="material-symbols-outlined" aria-hidden="true">remove</span>
                                        </button>
                                    </form>
                                    <span class="qty-value"><?= $qty ?></span>
                                <?php else: ?>
                                    <span class="qty-placeholder"></span>
                                    <span class="qty-value qty-zero">0</span>
                                <?php endif; ?>
                                <form method="post" action="<?= base_url('pesan-stand/menu/tambah') ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= (int) $p['id'] ?>">
                                    <button type="submit" class="qty-btn qty-plus" aria-label="Tambah <?= esc($p['nama']) ?>">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Sticky Subtotal + Navigasi -->
        <div class="menu-sticky-bar">
            <div class="sticky-summary-card">
                <div class="sticky-summary-title">
                    <span class="material-symbols-outlined" aria-hidden="true">shopping_bag</span>
                    Ringkasan Pilihan
                </div>

                <?php if (! empty($cartView['rows'])): ?>
                    <div class="sticky-items">
                        <?php foreach ($cartView['rows'] as $row): ?>
                            <div class="sticky-item-row">
                                <span class="sticky-item-nama"><?= esc($row['produk']['nama']) ?></span>
                                <span class="sticky-item-qty-price"><?= $row['jumlah'] ?> × Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="sticky-subtotal">
                        <span>Subtotal Menu</span>
                        <span class="sticky-subtotal-amount">Rp <?= number_format($cartView['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <p class="sticky-hint">Biaya stand akan ditampilkan di ringkasan berikutnya.</p>
                <?php else: ?>
                    <div class="sticky-empty">
                        <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                        <p>Belum ada menu dipilih.<br>Pilih minimal 1 item.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="menu-nav-actions">
                <a href="<?= base_url('pesan-stand/form') ?>" class="wizard-btn wizard-btn-outline" id="btn-kembali-form">
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                    Kembali
                </a>
                <?php if (! empty($cartView['rows'])): ?>
                    <a href="<?= base_url('pesan-stand/ringkasan') ?>" class="wizard-btn wizard-btn-primary" id="btn-lanjut-ringkasan">
                        Lihat Ringkasan
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                <?php else: ?>
                    <button type="button" class="wizard-btn wizard-btn-primary" disabled style="opacity:0.5;cursor:not-allowed;flex:1;">
                        Pilih menu dulu
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<style>
    .wizard-main { margin: 0 auto; padding: 32px 20px 48px; }
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
    .wizard-flash .material-symbols-outlined { font-size: 20px; flex-shrink: 0; }

    /* Layout grid */
    .menu-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 680px) {
        .menu-layout { grid-template-columns: 1fr; }
    }

    /* Kategori label */
    .menu-kategori-label {
        display: flex; align-items: center; gap: 6px;
        font-weight: 700; font-size: 0.85rem; color: var(--primary);
        text-transform: uppercase; letter-spacing: 0.05em;
        padding: 12px 0 8px;
        border-bottom: 1px solid var(--outline-variant);
        margin-bottom: 4px;
    }
    .menu-kategori-label .material-symbols-outlined { font-size: 18px; }
    .menu-kategori-group { margin-bottom: 8px; }

    /* Item card */
    .menu-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        margin-bottom: 8px;
        transition: box-shadow var(--t-fast), border-color var(--t-fast);
    }
    .menu-item-card:has(.qty-value:not(.qty-zero)) {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(76,29,149,0.10);
    }
    .menu-item-info { flex: 1; min-width: 0; }
    .menu-item-nama { font-weight: 600; font-size: 0.95rem; color: var(--on-surface); }
    .menu-item-harga { font-size: 0.82rem; color: var(--on-surface-variant); margin-top: 2px; }

    /* QTY controls */
    .menu-item-qty {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .qty-btn {
        width: 32px; height: 32px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background var(--t-fast), transform var(--t-fast);
    }
    .qty-btn .material-symbols-outlined { font-size: 18px; }
    .qty-minus {
        background: var(--surface-variant);
        color: var(--on-surface-variant);
    }
    .qty-minus:hover { background: #e5e0ea; transform: scale(1.05); }
    .qty-plus {
        background: var(--primary);
        color: #fff;
    }
    .qty-plus:hover { background: var(--primary-hover); transform: scale(1.05); }
    .qty-value {
        min-width: 28px;
        text-align: center;
        font-weight: 700;
        font-size: 1rem;
        color: var(--on-surface);
    }
    .qty-zero { color: var(--outline-variant); }
    .qty-placeholder { width: 32px; display: inline-block; }

    /* Sticky panel */
    .menu-sticky-bar { position: sticky; top: 20px; }
    .sticky-summary-card {
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        padding: 20px;
        margin-bottom: 12px;
    }
    .sticky-summary-title {
        display: flex; align-items: center; gap: 6px;
        font-weight: 700; font-size: 0.95rem; color: var(--on-surface);
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--outline-variant);
    }
    .sticky-summary-title .material-symbols-outlined { font-size: 20px; color: var(--primary); }
    .sticky-items { margin-bottom: 12px; }
    .sticky-item-row {
        display: flex; justify-content: space-between; align-items: baseline;
        padding: 4px 0;
        font-size: 0.82rem;
        border-bottom: 1px dashed var(--outline-variant);
    }
    .sticky-item-row:last-child { border-bottom: none; }
    .sticky-item-nama { color: var(--on-surface); font-weight: 500; flex: 1; margin-right: 8px; }
    .sticky-item-qty-price { color: var(--on-surface-variant); white-space: nowrap; }
    .sticky-subtotal {
        display: flex; justify-content: space-between; align-items: center;
        font-weight: 700; font-size: 1rem; color: var(--on-surface);
        padding-top: 10px;
        border-top: 1.5px solid var(--outline-variant);
        margin-top: 8px;
    }
    .sticky-subtotal-amount { color: var(--primary); font-size: 1.1rem; }
    .sticky-hint {
        font-size: 0.75rem; color: var(--on-surface-variant);
        margin: 8px 0 0; text-align: center;
    }
    .sticky-empty {
        text-align: center;
        color: var(--on-surface-variant);
        padding: 16px 0;
    }
    .sticky-empty .material-symbols-outlined { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.5; }
    .sticky-empty p { margin: 0; font-size: 0.85rem; line-height: 1.5; }

    .menu-nav-actions {
        display: flex; gap: 10px;
    }
    .wizard-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 12px 16px; border-radius: var(--radius-md);
        font-size: 0.9rem; font-weight: 700; text-decoration: none;
        min-height: 44px; cursor: pointer; border: none;
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
