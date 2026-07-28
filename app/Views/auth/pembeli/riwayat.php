<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pesanan — Siomay Dua Putri</title>
    <style>
        :root {
            --bg-page: #FFF7ED;
            --bg-card: #FFFFFF;
            --brand: #1D4ED8;
            --brand-merah: #DC2626;
            --brand-kuning: #FACC15;
            --ink: #1F1611;
            --ink-soft: #6B5B4A;
            --line: #EAE0D2;
            --ok-bg: #DCFCE7;
            --ok-fg: #166534;
            --warn-bg: #FEF3C7;
            --warn-fg: #92400E;
            --err-bg: #FEE2E2;
            --err-fg: #991B1B;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-md: 0 4px 16px rgba(31, 22, 17, 0.08);
            --t-fast: 150ms ease-out;
        }
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background: var(--bg-page);
            color: var(--ink);
            min-height: 100dvh;
            line-height: 1.5;
        }
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
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: 1.1rem;
        }
        header .duaputri { color: var(--brand-merah); }
        header .brand-mark {
            width: 32px; height: 32px;
            background: var(--brand-kuning);
            border-radius: var(--radius-md);
            display: inline-flex; align-items: center; justify-content: center;
            color: #1F1611; font-weight: 800;
        }
        header .account {
            display: flex; align-items: center; gap: 12px;
            font-size: 0.95rem;
        }
        header .account .name { font-weight: 600; }
        header .account .email {
            color: rgba(255,255,255,0.75);
            font-size: 0.85rem;
        }
        header .account .links { display: flex; gap: 6px; }
        header .account .account-link {
            color: var(--brand-kuning);
            text-decoration: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.10);
            transition: background var(--t-fast);
        }
        header .account .account-link:hover { background: rgba(255, 255, 255, 0.18); }
        header .account .logout-form { display: inline; margin: 0; }
        header .account button.logout {
            color: #fff;
            background: rgba(220, 38, 38, 0.85);
            border: none;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: inherit;
        }
        header .account button.logout:hover { background: #B91C1C; }

        main {
            max-width: 960px;
            margin: 24px auto;
            padding: 0 16px 64px;
        }
        .flash {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            font-weight: 500;
        }
        .flash-ok { background: var(--ok-bg); color: var(--ok-fg); }
        .flash-err { background: var(--err-bg); color: var(--err-fg); }

        h1 {
            color: var(--brand);
            margin: 0 0 4px;
            font-size: 1.5rem;
        }
        p.lead { color: var(--ink-soft); margin: 0 0 20px; }

        .pesanan-list {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        .empty {
            padding: 32px 24px;
            text-align: center;
            color: var(--ink-soft);
        }
        .empty p { margin: 0 0 12px; }
        .empty a.btn-link {
            display: inline-block;
            padding: 10px 20px;
            background: var(--brand);
            color: #fff;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
        }
        .empty a.btn-link:hover { background: #1E40AF; }

        .pesanan-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pesanan-item:last-child { border-bottom: none; }
        .pesanan-item .info { flex: 1; min-width: 200px; }
        .pesanan-item .kode {
            font-weight: 700;
            color: var(--ink);
            font-family: ui-monospace, "SF Mono", Consolas, monospace;
        }
        .pesanan-item .meta {
            font-size: 0.85rem;
            color: var(--ink-soft);
            margin-top: 2px;
        }
        .pesanan-item .right {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }
        .pesanan-item .total {
            font-weight: 700;
            color: var(--brand-merah);
            font-variant-numeric: tabular-nums;
        }
        .pesanan-item .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pending { background: var(--warn-bg); color: var(--warn-fg); }
        .status-lunas { background: var(--ok-bg); color: var(--ok-fg); }
        .status-gagal, .status-kedaluwarsa { background: var(--err-bg); color: var(--err-fg); }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= base_url('etalase') ?>" style="text-decoration:none;color:inherit;">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <div class="account">
        <div>
            <div class="name"><?= esc($pembeliNama) ?></div>
            <div class="email"><?= esc($pembeliEmail) ?></div>
        </div>
        <div class="links">
            <a class="account-link" href="<?= base_url('etalase') ?>">Etalase</a>
            <form method="post" action="<?= base_url('logout') ?>" class="logout-form">
                <?= csrf_field() ?>
                <button type="submit" class="logout">Logout</button>
            </form>
        </div>
    </div>
</header>
<main>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="flash flash-ok"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <h1>Riwayat Pesanan</h1>
    <p class="lead">Daftar pesanan yang Anda buat dengan akun ini.</p>

    <div class="pesanan-list">
        <?php if (empty($pesanan)): ?>
            <div class="empty">
                <p>Belum ada pesanan. Yuk, pesan siomay favoritmu!</p>
                <a class="btn-link" href="<?= base_url('etalase') ?>">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($pesanan as $p): ?>
                <div class="pesanan-item">
                    <div class="info">
                        <div class="kode"><?= esc($p['kode_pesanan']) ?></div>
                        <div class="meta">
                            <?= esc(ucfirst(str_replace('_', ' ', $p['metode']))) ?>
                            · #<?= (int) $p['id'] ?>
                        </div>
                    </div>
                    <div class="right">
                        <div class="total">Rp <?= number_format((float) $p['total'], 0, ',', '.') ?></div>
                        <span class="status status-<?= esc($p['status']) ?>"><?= esc(ucfirst($p['status'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
