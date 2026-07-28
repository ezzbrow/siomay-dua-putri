<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etalase — Siomay Dua Putri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <style>
        /* === Design tokens (Purple theme, CLAUDE.md §12) === */
        :root {
            --primary: #4C1D95;
            --primary-hover: #6D28D9;
            --secondary: #712edd;
            --secondary-light: #8B5CF6;
            --secondary-fixed: #ebddff;
            --background: #fef7ff;
            --surface: #ffffff;
            --surface-variant: #f3ebf6;
            --on-surface: #1d1a22;
            --on-surface-variant: #4a4452;
            --outline-variant: #e7e0eb;
            --accent: #e19760;

            /* semantic helpers (di-derive dari token di atas) */
            --page-bg-gradient: linear-gradient(135deg, #F8F6FF 0%, #F2ECFF 100%);
            --card-shadow: 0 10px 30px rgba(76, 29, 149, 0.08);
            --card-shadow-hover: 0 20px 40px rgba(76, 29, 149, 0.12);

            --ok-bg: #E6F6EC;
            --ok-fg: #166534;
            --warn-bg: #FFF1D6;
            --warn-fg: #92400E;
            --err-bg: #FCE6E6;
            --err-fg: #991B1B;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-pill: 999px;

            --t-fast: 180ms ease-out;
            --t-base: 220ms ease-out;
        }

        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: "Be Vietnam Pro", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background: var(--page-bg-gradient);
            color: var(--on-surface);
            line-height: 1.55;
            min-height: 100dvh;
            font-feature-settings: "cv11", "ss01";
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-hover); }
        button, input, select, textarea { font: inherit; color: inherit; }
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
            vertical-align: middle;
            user-select: none;
        }

        /* === Header === */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, #3B157A 100%);
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 10;
            flex-wrap: wrap;
        }
        header .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
        }
        header .brand:hover { color: #fff; }
        header .duaputri { color: var(--secondary-light); }
        header .brand-mark {
            width: 36px; height: 36px;
            background: var(--secondary-fixed);
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 800;
        }
        header a.cart-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.12);
            transition: background var(--t-fast), transform var(--t-fast);
        }
        header a.cart-link:hover { background: rgba(255, 255, 255, 0.22); color: #fff; }
        header a.cart-link:active { transform: scale(0.97); }
        header a.cart-link .material-symbols-outlined { font-size: 20px; }

        /* === Header auth area === */
        header .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        header .auth-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 7px 14px;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.20);
            transition: background var(--t-fast);
            font-size: 0.9rem;
        }
        header .auth-link:hover { background: rgba(255, 255, 255, 0.20); color: #fff; }
        header .auth-link.secondary {
            background: var(--secondary-light);
            color: var(--primary);
            border-color: transparent;
        }
        header .auth-link.secondary:hover { background: #A78BFA; color: var(--primary); }
        header .account-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 4px 4px 12px;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.12);
            font-size: 0.9rem;
        }
        header .account-chip .avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--secondary-fixed);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
        }
        header .account-chip .name {
            color: #fff;
            font-weight: 600;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        header .account-chip .riwayat-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.10);
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            transition: background var(--t-fast);
        }
        header .account-chip .riwayat-link:hover { background: rgba(255, 255, 255, 0.20); color: #fff; }
        header .account-chip .logout-form { display: inline; margin: 0; }
        header .account-chip button.logout {
            color: #fff;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.20);
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
            font-family: inherit;
            transition: background var(--t-fast);
        }
        header .account-chip button.logout:hover { background: rgba(255, 255, 255, 0.20); }

        /* === Layout === */
        main {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 20px 80px;
        }

        /* === Banners / status === */
        .status-banner,
        .flash {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            border: 1px solid transparent;
        }
        .status-open,
        .flash-ok { background: var(--ok-bg); color: var(--ok-fg); border-color: rgba(22, 101, 52, 0.18); }
        .status-closed,
        .flash-err { background: var(--err-bg); color: var(--err-fg); border-color: rgba(153, 27, 27, 0.18); }
        .status-banner .material-symbols-outlined,
        .flash .material-symbols-outlined { font-size: 22px; flex-shrink: 0; }

        /* === Cards === */
        section.kategori,
        .cart-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--outline-variant);
            transition: box-shadow var(--t-base);
        }
        section.kategori:hover,
        .cart-card:hover { box-shadow: var(--card-shadow-hover); }
        h2 {
            margin: 0 0 16px;
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        h2 .material-symbols-outlined { font-size: 26px; color: var(--secondary); }

        /* === Produk row === */
        .produk {
            border: 1px solid var(--outline-variant);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            margin: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            background: var(--surface);
            transition: box-shadow var(--t-fast), transform var(--t-fast), border-color var(--t-fast);
        }
        .produk:hover {
            box-shadow: 0 6px 18px rgba(76, 29, 149, 0.10);
            border-color: var(--secondary-fixed);
        }
        .produk.tidak-tersedia {
            background: var(--surface-variant);
            color: #8A8194;
            opacity: 0.7;
        }
        .produk.tidak-tersedia .nama,
        .produk.tidak-tersedia .harga {
            text-decoration: line-through;
            text-decoration-color: #8A8194;
        }
        .nama {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--on-surface);
        }
        .harga {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.05rem;
            font-variant-numeric: tabular-nums;
            margin-top: 2px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            background: var(--warn-bg);
            color: var(--warn-fg);
            margin-left: 8px;
            font-weight: 600;
        }
        .badge.disabled { background: var(--surface-variant); color: var(--on-surface-variant); }
        .badge .material-symbols-outlined { font-size: 14px; }

        /* === Forms === */
        .qty-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .qty-form select,
        .qty-form input[type="number"] {
            padding: 10px 14px;
            border: 1.5px solid var(--outline-variant);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--on-surface);
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
            min-height: 44px;
        }
        .qty-form input[type="number"] { width: 80px; font-variant-numeric: tabular-nums; }
        .qty-form select:focus,
        .qty-form input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(76, 29, 149, 0.18);
        }
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1.5px solid var(--outline-variant);
            border-radius: var(--radius-md);
            font-family: inherit;
            background: var(--surface);
            color: var(--on-surface);
            resize: vertical;
            min-height: 80px;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
        }
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(76, 29, 149, 0.18);
        }
        .helper { font-size: 0.85rem; color: var(--on-surface-variant); margin-top: 8px; }

        /* === Buttons === */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            min-height: 44px;
            transition: background var(--t-fast), transform var(--t-fast), box-shadow var(--t-fast);
            box-shadow: 0 4px 12px rgba(76, 29, 149, 0.20);
        }
        .btn:hover { background: var(--primary-hover); color: #fff; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(76, 29, 149, 0.28); }
        .btn:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(76, 29, 149, 0.18); }
        .btn:focus-visible { outline: 3px solid var(--secondary-light); outline-offset: 2px; }
        .btn.secondary {
            background: var(--surface);
            color: var(--primary);
            border: 1.5px solid var(--outline-variant);
            box-shadow: none;
        }
        .btn.secondary:hover { background: var(--surface-variant); color: var(--primary-hover); border-color: var(--secondary-light); }
        .btn.icon-only { padding: 10px; min-width: 44px; }
        .btn:disabled {
            background: var(--surface-variant);
            color: var(--on-surface-variant);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
        .btn .material-symbols-outlined { font-size: 18px; }

        /* === Cart === */
        .cart-empty {
            color: var(--on-surface-variant);
            text-align: center;
            padding: 24px 8px;
            margin: 0;
            font-style: italic;
        }
        .cart-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid var(--outline-variant);
            gap: 12px;
            flex-wrap: wrap;
        }
        .cart-line:last-child { border-bottom: none; }
        .cart-line .info { flex: 1; min-width: 200px; }
        .cart-line .info .nm {
            font-weight: 600;
            color: var(--on-surface);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cart-line .info .v {
            font-size: 0.9rem;
            color: var(--on-surface-variant);
            margin-top: 4px;
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
            margin-top: 18px;
            padding-top: 16px;
            border-top: 2px dashed var(--primary);
            color: var(--on-surface);
        }
        .total-row .amount {
            color: var(--primary);
            font-variant-numeric: tabular-nums;
            font-size: 1.4rem;
        }

        .min-ok, .min-warning {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        .min-ok { background: var(--ok-bg); color: var(--ok-fg); border: 1px solid rgba(22, 101, 52, 0.18); }
        .min-warning { background: var(--warn-bg); color: var(--warn-fg); border: 1px solid rgba(146, 64, 14, 0.18); }
        .min-ok .material-symbols-outlined,
        .min-warning .material-symbols-outlined { font-size: 22px; flex-shrink: 0; }

        /* === Motion-safe === */
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
            main { margin: 20px auto; padding: 0 14px 96px; }
            section.kategori, .cart-card { padding: 18px; border-radius: var(--radius-md); }
            h2 { font-size: 1.25rem; }
            .produk { padding: 14px; gap: 12px; }
            .qty-form { width: 100%; }
            .qty-form input[type="number"] { width: 70px; }
            .total-row { font-size: 1.1rem; }
            .total-row .amount { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= base_url('etalase') ?>">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <a class="cart-link" href="<?= base_url('keranjang') ?>" aria-label="Lihat keranjang">
        <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
        Keranjang
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
                <a class="riwayat-link" href="<?= base_url('akun/riwayat') ?>">Riwayat</a>
                <form method="post" action="<?= base_url('logout') ?>" class="logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="logout">Logout</button>
                </form>
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

    <?php if ($tokoBuka): ?>
        <div class="status-banner status-open">
            <span class="material-symbols-outlined" aria-hidden="true">storefront</span>
            <span>Toko sedang buka (<?= esc(substr($nowServerTime, 0, 5)) ?>).</span>
        </div>
    <?php else: ?>
        <div class="status-banner status-closed">
            <span class="material-symbols-outlined" aria-hidden="true">do_not_disturb_on</span>
            <span>Toko sedang tutup. <?= esc($alasanTutup) ?></span>
        </div>
    <?php endif; ?>

    <div class="cart-card">
        <h2>
            <span class="material-symbols-outlined" aria-hidden="true">shopping_bag</span>
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
                                <span class="material-symbols-outlined" aria-hidden="true">remove</span>
                            </button>
                        </form>
                        <form method="post" action="<?= base_url('keranjang/hapus') ?>" style="margin:0;">
                            <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                            <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                            <button class="btn secondary icon-only" type="submit" onclick="return confirm('Hapus item ini?')" aria-label="Hapus item">
                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
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
                    <span class="material-symbols-outlined" aria-hidden="true">verified</span>
                    <span>Minimum order terpenuhi. Silakan lanjut.</span>
                </div>
                <p style="margin-top:16px;">
                    <a class="btn" href="#" onclick="alert('Lanjut ke Langkah 4 (checkout) — belum dibangun.'); return false;">
                        Lanjut ke Checkout
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                </p>
            <?php else: ?>
                <div class="min-warning">
                    <span class="material-symbols-outlined" aria-hidden="true">warning</span>
                    <span>Minimum order Rp <?= number_format($cart['minOrder'], 0, ',', '.') ?>. Kurang Rp <?= number_format($cart['kekurangan'], 0, ',', '.') ?> lagi.</span>
                </div>
                <p style="margin-top:16px;">
                    <button class="btn" disabled>Lanjut ke Checkout</button>
                </p>
            <?php endif; ?>

            <form method="post" action="<?= base_url('keranjang/catatan') ?>" style="margin-top:24px;">
                <label for="catatan" style="font-weight:600; display:block; margin-bottom:8px;">Catatan (opsional, untuk permintaan rasa)</label>
                <textarea id="catatan" name="catatan" rows="2" maxlength="500" placeholder="Contoh: extra saus, tidak pedas, bumbunya dipisah"><?= esc($catatan ?? '') ?></textarea>
                <p class="helper">
                    <strong>Penting:</strong> kolom ini hanya untuk permintaan rasa, <strong>BUKAN</strong> untuk alamat.
                    Alamat pengiriman diisi di langkah berikutnya.
                </p>
                <button class="btn secondary" type="submit" style="margin-top:10px;">
                    <span class="material-symbols-outlined" aria-hidden="true">save</span>
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
                    <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                <?php elseif ($kategori === 'Lumpia'): ?>
                    <span class="material-symbols-outlined" aria-hidden="true">nutrition</span>
                <?php else: ?>
                    <span class="material-symbols-outlined" aria-hidden="true">cookie</span>
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
                                    <span class="material-symbols-outlined" aria-hidden="true">block</span>
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
                                <span class="material-symbols-outlined" aria-hidden="true">add</span>
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
