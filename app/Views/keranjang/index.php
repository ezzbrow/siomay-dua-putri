<?= $this->include('partials/header') ?>

<header>
    <a class="brand" href="<?= base_url('etalase') ?>">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <a class="cart-link" href="<?= base_url('etalase') ?>" aria-label="Lanjut belanja">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
        Belanja
    </a>
    <div class="header-right">
        <?php if (session()->get('pembeli_id')): ?>
            <?php
                $pembeliNama  = (string) session()->get('pembeli_nama');
                $pembeliInisial = strtoupper(mb_substr($pembeliNama, 0, 1, 'UTF-8'));
            ?>
            <span class="account-chip">
                <span class="avatar" aria-hidden="true"><?= esc($pembeliInisial) ?></span>
                <span class="name"><?= esc($pembeliNama) ?></span>
            </span>
        <?php else: ?>
            <a class="auth-link" href="<?= base_url('login') ?>">Login</a>
            <a class="auth-link secondary" href="<?= base_url('daftar') ?>">Daftar</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="flash flash-ok" role="status">
            <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
            <span><?= esc(session()->getFlashdata('message')) ?></span>
        </div>
    <?php endif; ?>

    <h1 class="cart-title">Keranjang</h1>

    <?php if (empty($cart['rows'])): ?>
        <div class="cart-empty-state">
            <p>Keranjang kosong.</p>
            <a class="btn-primary" href="<?= base_url('etalase') ?>">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <ul class="cart-list">
            <?php foreach ($cart['rows'] as $row): ?>
                <li class="cart-item">
                    <div class="cart-item-info">
                        <div class="nm"><?= esc($row['produk']['nama']) ?>
                            <?php if (! empty($row['varian'])): ?>
                                <span class="v">(<?= esc($row['varian']['nama_varian']) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="v"><?= esc(rtrim(rtrim(number_format((float) $row['jumlah'], 2), '0'), '.')) ?> × Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            = <strong>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></strong></div>
                    </div>
                    <div class="cart-item-actions">
                        <form method="post" action="<?= base_url('keranjang/kurang') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                            <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                            <input type="hidden" name="jumlah" value="<?= esc(rtrim(rtrim(number_format((float) $row['jumlah'], 2), '0'), '.')) ?>">
                            <button class="btn-icon" type="submit" aria-label="Kurangi">
                                <span class="material-symbols-outlined">remove</span>
                            </button>
                        </form>
                        <form method="post" action="<?= base_url('keranjang/hapus') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                            <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                            <button class="btn-icon" type="submit" aria-label="Hapus" onclick="return confirm('Hapus item ini?')">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="cart-summary">
            <div class="cart-min">
                <?php if ($cart['canCheckout']): ?>
                    <div class="min-ok">Minimum order terpenuhi.</div>
                <?php else: ?>
                    <div class="min-warn">Kurang Rp <?= number_format($cart['kekurangan'], 0, ',', '.') ?> lagi</div>
                <?php endif; ?>
                <progress class="cart-progress" value="<?= min(100, ($cart['total'] / max($cart['minOrder'], 1)) * 100) ?>" max="100"></progress>
                <div class="cart-min-text">Rp <?= number_format($cart['total'], 0, ',', '.') ?> / Rp <?= number_format($cart['minOrder'], 0, ',', '.') ?></div>
            </div>

            <div class="cart-total-row">
                <span>Total</span>
                <span class="amount">Rp <?= number_format($cart['total'], 0, ',', '.') ?></span>
            </div>

            <form method="post" action="<?= base_url('keranjang/catatan') ?>" class="cart-catatan">
                <label for="catatan">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="2" maxlength="500" placeholder="cth: extra saus, bumbunya dipisah"><?= esc($catatan) ?></textarea>
                <p class="helper">Hanya untuk permintaan rasa, BUKAN alamat.</p>
                <button class="btn-secondary" type="submit">Simpan Catatan</button>
            </form>

            <a class="btn-primary cart-lanjut" href="<?= base_url('checkout/catatan') ?>"
               <?= $cart['canCheckout'] ? '' : 'aria-disabled="true" style="pointer-events:none;opacity:0.5;"' ?>>
                Lanjut ke Checkout
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>

<style>
    body { font-family: "Be Vietnam Pro", system-ui, sans-serif; background: var(--page-bg-gradient, #F8F6FF); color: var(--on-surface, #1d1a22); margin: 0; line-height: 1.55; }
    .brand { display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit; font-weight:700; }
    .duaputri { color: #8B5CF6; }
    .brand-mark { width:32px; height:32px; background:#ebddff; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; color:#4C1D95; font-weight:800; }
    main { max-width:760px; margin:0 auto; padding:24px 16px 80px; }
    .cart-title { font-size:1.4rem; font-weight:800; color:#4C1D95; margin:0 0 16px; }
    .flash { padding:12px 16px; border-radius:12px; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:500; }
    .flash-err { background:#FCE6E6; color:#991B1B; }
    .flash-ok { background:#E6F6EC; color:#166534; }
    .cart-empty-state { text-align:center; padding:60px 0; color:#6B5B4A; }
    .cart-empty-state p { margin:0 0 16px; }
    .cart-list { list-style:none; padding:0; margin:0 0 20px; }
    .cart-item { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 0; border-bottom:1px solid #e7e0eb; flex-wrap:wrap; }
    .cart-item-info { flex:1; min-width:200px; }
    .cart-item-info .nm { font-weight:600; }
    .cart-item-info .v { font-size:0.9rem; color:#4a4452; margin-top:4px; font-variant-numeric: tabular-nums; }
    .cart-item-actions { display:flex; gap:6px; }
    .btn-icon { background:#fff; border:1.5px solid #e7e0eb; border-radius:8px; width:36px; height:36px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
    .btn-icon:hover { background:#f3ebf6; }
    .cart-summary { background:#fff; border:1px solid #e7e0eb; border-radius:20px; padding:20px; box-shadow:0 10px 30px rgba(76,29,149,0.08); }
    .cart-min { margin-bottom:16px; }
    .min-ok { color:#166534; font-weight:600; margin-bottom:8px; }
    .min-warn { color:#92400E; font-weight:600; margin-bottom:8px; }
    .cart-progress { width:100%; height:8px; border-radius:4px; overflow:hidden; appearance:none; }
    .cart-progress::-webkit-progress-bar { background:#e7e0eb; }
    .cart-progress::-webkit-progress-value { background:#4C1D95; }
    .cart-progress::-moz-progress-bar { background:#4C1D95; }
    .cart-min-text { font-size:0.85rem; color:#4a4452; margin-top:6px; text-align:right; font-variant-numeric: tabular-nums; }
    .cart-total-row { display:flex; justify-content:space-between; font-weight:700; font-size:1.1rem; margin:16px 0; padding-top:16px; border-top:1px dashed #e7e0eb; }
    .cart-total-row .amount { color:#4C1D95; font-variant-numeric: tabular-nums; }
    .cart-catatan { margin:16px 0; }
    .cart-catatan label { display:block; font-weight:600; font-size:0.9rem; margin-bottom:6px; }
    .cart-catatan textarea { width:100%; box-sizing:border-box; padding:10px 12px; border:1.5px solid #e7e0eb; border-radius:12px; font-family:inherit; }
    .cart-catatan .helper { font-size:0.85rem; color:#4a4452; margin:6px 0; }
    .btn-primary, .btn-secondary { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 20px; border-radius:12px; font-weight:600; text-decoration:none; border:none; cursor:pointer; font-size:0.95rem; min-height:44px; transition: background 180ms; }
    .btn-primary { background:#4C1D95; color:#fff; width:100%; margin-top:12px; }
    .btn-primary:hover { background:#6D28D9; color:#fff; }
    .btn-secondary { background:#fff; color:#4C1D95; border:1.5px solid #e7e0eb; padding:8px 16px; font-size:0.85rem; min-height:36px; }
    .btn-secondary:hover { background:#f3ebf6; }
</style>
