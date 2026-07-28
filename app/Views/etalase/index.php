<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etalase — Siomay Dua Putri</title>
    <style>
        /* === Design tokens (Warm Artisan, brand Siomay) === */
        :root {
            --bg-page: #FFF7ED;        /* warm cream */
            --bg-card: #FFFFFF;
            --bg-soft: #FEF3E8;
            --brand: #1D4ED8;          /* wordmark Siomay */
            --brand-merah: #DC2626;    /* wordmark Dua Putri + aksen */
            --brand-kuning: #FACC15;   /* aksen highlight */
            --terracotta: #C2410C;     /* tombol primary CTA */
            --sand: #F5E6D3;
            --olive: #6B7B3C;
            --ink: #1F1611;            /* body text */
            --ink-soft: #6B5B4A;
            --muted: #A89889;
            --line: #EAE0D2;
            --ok-bg: #DCFCE7;
            --ok-fg: #166534;
            --warn-bg: #FEF3C7;
            --warn-fg: #92400E;
            --err-bg: #FEE2E2;
            --err-fg: #991B1B;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-pill: 999px;
            --shadow-sm: 0 1px 2px rgba(31, 22, 17, 0.06);
            --shadow-md: 0 4px 16px rgba(31, 22, 17, 0.08);
            --shadow-lg: 0 8px 32px rgba(31, 22, 17, 0.10);
            --t-fast: 150ms ease-out;
            --t-base: 220ms ease-out;
        }

        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
            margin: 0;
            background: var(--bg-page);
            color: var(--ink);
            line-height: 1.5;
            min-height: 100dvh;
        }
        a { color: var(--brand); }
        button, input, select, textarea { font: inherit; color: inherit; }

        /* === Header === */
        header {
            background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        header .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        header .duaputri { color: var(--brand-merah); }
        header .brand-mark {
            width: 32px; height: 32px;
            background: var(--brand-kuning);
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1F1611;
            font-weight: 800;
        }
        header a.cart-link {
            color: var(--brand-kuning);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.10);
            transition: background var(--t-fast), transform var(--t-fast);
        }
        header a.cart-link:hover { background: rgba(255, 255, 255, 0.18); }
        header a.cart-link:active { transform: scale(0.97); }
        header a.cart-link svg { width: 18px; height: 18px; }

        /* === Layout === */
        main {
            max-width: 960px;
            margin: 24px auto;
            padding: 0 16px 64px;
        }

        /* === Banners / status === */
        .status-banner {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .status-open { background: var(--ok-bg); color: var(--ok-fg); }
        .status-closed { background: var(--err-bg); color: var(--err-fg); }
        .status-banner svg { width: 18px; height: 18px; flex-shrink: 0; }

        .flash {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .flash-ok { background: var(--ok-bg); color: var(--ok-fg); }
        .flash-err { background: var(--err-bg); color: var(--err-fg); }
        .flash svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* === Cards === */
        section.kategori,
        .cart-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--line);
        }
        h2 {
            margin: 0 0 12px;
            color: var(--brand);
            font-size: 1.35rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        h2 svg { width: 22px; height: 22px; }

        /* === Produk row === */
        .produk {
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: #FFFCF7;
            transition: box-shadow var(--t-fast), transform var(--t-fast);
        }
        .produk:hover { box-shadow: var(--shadow-sm); }
        .produk.tidak-tersedia {
            background: #F3F4F6;
            color: #9CA3AF;
            opacity: 0.75;
        }
        .produk.tidak-tersedia .nama,
        .produk.tidak-tersedia .harga {
            text-decoration: line-through;
            text-decoration-color: #9CA3AF;
        }
        .nama {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--ink);
        }
        .harga {
            color: var(--brand-merah);
            font-weight: 700;
            font-size: 1rem;
            font-variant-numeric: tabular-nums;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            background: var(--warn-bg);
            color: var(--warn-fg);
            margin-left: 8px;
            font-weight: 600;
        }
        .badge.disabled { background: #E5E7EB; color: #6B7280; }

        /* === Forms (qty-form, catatan) === */
        .qty-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .qty-form select,
        .qty-form input[type="number"] {
            padding: 8px 12px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
            min-height: 40px;
        }
        .qty-form input[type="number"] { width: 72px; font-variant-numeric: tabular-nums; }
        .qty-form select:focus,
        .qty-form input[type="number"]:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            font-family: inherit;
            background: #fff;
            resize: vertical;
            min-height: 60px;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
        }
        textarea:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }
        .helper { font-size: 0.85rem; color: var(--ink-soft); margin-top: 6px; }

        /* === Buttons === */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 16px;
            background: var(--terracotta);
            color: #fff;
            text-decoration: none;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            min-height: 40px;
            transition: background var(--t-fast), transform var(--t-fast), box-shadow var(--t-fast);
            box-shadow: 0 2px 0 rgba(194, 65, 12, 0.25);
        }
        .btn:hover { background: #9A340B; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); box-shadow: 0 1px 0 rgba(194, 65, 12, 0.25); }
        .btn:focus-visible { outline: 3px solid var(--brand-kuning); outline-offset: 2px; }
        .btn.secondary {
            background: #fff;
            color: var(--ink);
            border: 1.5px solid var(--line);
            box-shadow: none;
        }
        .btn.secondary:hover { background: var(--bg-soft); border-color: var(--muted); }
        .btn.icon-only { padding: 8px 10px; min-width: 40px; }
        .btn:disabled {
            background: #D1D5DB;
            color: #6B7280;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
        .btn svg { width: 16px; height: 16px; }

        /* === Cart === */
        .cart-empty {
            color: var(--ink-soft);
            text-align: center;
            padding: 16px 8px;
            margin: 0;
        }
        .cart-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
            gap: 12px;
            flex-wrap: wrap;
        }
        .cart-line:last-child { border-bottom: none; }
        .cart-line .info { flex: 1; min-width: 200px; }
        .cart-line .info .nm {
            font-weight: 600;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cart-line .info .v {
            font-size: 0.9rem;
            color: var(--ink-soft);
            margin-top: 2px;
            font-variant-numeric: tabular-nums;
        }
        .cart-line .actions { display: flex; gap: 6px; align-items: center; }
        .cart-line form { display: inline; margin: 0; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px dashed var(--brand);
            color: var(--ink);
        }
        .total-row .amount {
            color: var(--brand-merah);
            font-variant-numeric: tabular-nums;
            font-size: 1.35rem;
        }

        .min-ok, .min-warning {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .min-ok { background: var(--ok-bg); color: var(--ok-fg); }
        .min-warning { background: var(--warn-bg); color: var(--warn-fg); }
        .min-ok svg, .min-warning svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* === View transitions & motion-safe === */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* === Mobile === */
        @media (max-width: 600px) {
            header { padding: 12px 16px; }
            header .brand { font-size: 1rem; }
            main { margin: 16px auto; padding: 0 12px 96px; }
            section.kategori, .cart-card { padding: 16px; border-radius: var(--radius-md); }
            h2 { font-size: 1.2rem; }
            .produk { padding: 12px; gap: 10px; }
            .qty-form { width: 100%; }
            .qty-form input[type="number"] { width: 64px; }
            .total-row { font-size: 1.1rem; }
            .total-row .amount { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<header>
    <span class="brand">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </span>
    <a class="cart-link" href="<?= base_url('keranjang') ?>" aria-label="Lihat keranjang">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
        Keranjang
    </a>
</header>
<main>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="flash flash-ok" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= esc(session()->getFlashdata('message')) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($tokoBuka): ?>
        <div class="status-banner status-open">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Toko sedang buka (<?= esc(substr($nowServerTime, 0, 5)) ?>).</span>
        </div>
    <?php else: ?>
        <div class="status-banner status-closed">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Toko sedang tutup. <?= esc($alasanTutup) ?></span>
        </div>
    <?php endif; ?>

    <div class="cart-card">
        <h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
            Keranjang
        </h2>
        <?php if (empty($cart['rows'])): ?>
            <p class="cart-empty">Belum ada item. Tambahkan dari etalase di bawah.</p>
        <?php else: ?>
            <?php foreach ($cart['rows'] as $row): ?>
                <div class="cart-line">
                    <div class="info">
                        <div class="nm">
                            <?= esc($row['produk']['nama']) ?>
                            <?php if (! empty($row['varian'])): ?>
                                <span class="v">(varian: <?= esc($row['varian']['nama_varian']) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="v">
                            <?= (int) $row['jumlah'] ?> × Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            = Rp <?= number_format($row['subtotal'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="actions">
                        <form method="post" action="<?= base_url('keranjang/kurang') ?>" style="margin:0;">
                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                            <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                            <input type="hidden" name="jumlah" value="1">
                            <button class="btn secondary icon-only" type="submit" aria-label="Kurangi jumlah">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                        </form>
                        <form method="post" action="<?= base_url('keranjang/hapus') ?>" style="margin:0;">
                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                            <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                            <button class="btn secondary icon-only" type="submit" onclick="return confirm('Hapus item ini?')" aria-label="Hapus item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-row">
                <span>Total</span>
                <span class="amount">Rp <?= number_format($cart['total'], 0, ',', '.') ?></span>
            </div>

            <?php if ($cart['canCheckout']): ?>
                <div class="min-ok">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Minimum order terpenuhi. Silakan lanjut.</span>
                </div>
                <p style="margin-top:16px;">
                    <a class="btn" href="#" onclick="alert('Lanjut ke Langkah 4 (checkout) — belum dibangun.'); return false;">
                        Lanjut ke Checkout
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </p>
            <?php else: ?>
                <div class="min-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>Minimum order Rp <?= number_format($cart['minOrder'], 0, ',', '.') ?>. Kurang Rp <?= number_format($cart['kekurangan'], 0, ',', '.') ?> lagi.</span>
                </div>
                <p style="margin-top:16px;">
                    <button class="btn" disabled>Lanjut ke Checkout</button>
                </p>
            <?php endif; ?>

            <form method="post" action="<?= base_url('keranjang/catatan') ?>" style="margin-top:20px;">
                <label for="catatan" style="font-weight:600; display:block; margin-bottom:6px;">Catatan (opsional, untuk permintaan rasa)</label>
                <textarea id="catatan" name="catatan" rows="2" maxlength="500" placeholder="Contoh: extra saus, tidak pedas, bumbunya dipisah"><?= esc($catatan ?? '') ?></textarea>
                <p class="helper">
                    <strong>Penting:</strong> kolom ini hanya untuk permintaan rasa, <strong>BUKAN</strong> untuk alamat.
                    Alamat pengiriman diisi di langkah berikutnya.
                </p>
                <button class="btn secondary" type="submit" style="margin-top:8px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Catatan
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php foreach (['Somay Sapi', 'Lumpia', 'Pentol Goreng'] as $kategori): ?>
        <?php $items = $grouped[$kategori] ?? []; if (empty($items)) continue; ?>
        <section class="kategori">
            <h2>
                <?php if ($kategori === 'Somay Sapi'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2l1.5 4M18 2l-1.5 4M5 6h14a2 2 0 0 1 2 2v3a8 8 0 0 1-16 0V8a2 2 0 0 1 2-2z"/></svg>
                <?php elseif ($kategori === 'Lumpia'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/></svg>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                <?php endif; ?>
                <?= esc($kategori) ?>
            </h2>
            <?php foreach ($items as $p):
                $tersedia = \App\Helpers\ProductAvailability::isProductTersedia($p, $tokoBuka);
                $kelas = $tersedia ? '' : 'tidak-tersedia';
                $isLumpia = ($p['kategori'] ?? '') === 'Lumpia';
            ?>
                <div class="produk <?= $kelas ?>">
                    <div>
                        <div class="nama">
                            <?= esc($p['nama']) ?>
                            <?php if (! $tersedia): ?>
                                <span class="badge disabled">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Tidak tersedia
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="harga">Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?></div>
                    </div>
                    <?php if ($tersedia): ?>
                        <form class="qty-form" method="post" action="<?= base_url('keranjang/tambah') ?>">
                            <input type="hidden" name="produk_id" value="<?= (int) $p['id'] ?>">
                            <?php if ($isLumpia): ?>
                                <select name="varian_id" required aria-label="Pilih varian">
                                    <option value="">— pilih varian —</option>
                                    <?php foreach ($p['varian'] as $v): ?>
                                        <option value="<?= (int) $v['id'] ?>"><?= esc($v['nama_varian']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="varian_id" value="0">
                            <?php endif; ?>
                            <input type="number" name="jumlah" value="1" min="1" max="999" required aria-label="Jumlah">
                            <button class="btn" type="submit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
