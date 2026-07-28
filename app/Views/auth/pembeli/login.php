<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Siomay Dua Putri</title>
    <style>
        :root {
            --bg-page: #FFF7ED;
            --bg-card: #FFFFFF;
            --brand: #1D4ED8;
            --brand-merah: #DC2626;
            --brand-kuning: #FACC15;
            --terracotta: #C2410C;
            --ink: #1F1611;
            --ink-soft: #6B5B4A;
            --line: #EAE0D2;
            --err-bg: #FEE2E2;
            --err-fg: #991B1B;
            --ok-bg: #DCFCE7;
            --ok-fg: #166534;
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
            display: flex;
            flex-direction: column;
        }
        header {
            background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
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
        header a.cart-link {
            color: var(--brand-kuning);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.10);
        }
        header a.cart-link:hover { background: rgba(255, 255, 255, 0.18); }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 32px 16px 64px;
        }
        .auth-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 28px 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--line);
            max-width: 440px;
            width: 100%;
        }
        .auth-card h1 {
            color: var(--brand);
            margin: 0 0 4px;
            font-size: 1.5rem;
        }
        .auth-card p.lead {
            color: var(--ink-soft);
            margin: 0 0 20px;
            font-size: 0.95rem;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--ink);
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
            font-family: inherit;
            min-height: 44px;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 12px 16px;
            background: var(--terracotta);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            min-height: 44px;
            transition: background var(--t-fast), transform var(--t-fast);
            box-shadow: 0 2px 0 rgba(194, 65, 12, 0.25);
        }
        .btn:hover { background: #9A340B; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn:focus-visible { outline: 3px solid var(--brand-kuning); outline-offset: 2px; }
        .auth-foot {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            text-align: center;
            color: var(--ink-soft);
            font-size: 0.9rem;
        }
        .auth-foot a {
            color: var(--brand);
            font-weight: 600;
            text-decoration: none;
        }
        .auth-foot a:hover { text-decoration: underline; }
        .flash {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .flash-ok { background: var(--ok-bg); color: var(--ok-fg); }
        .flash-err { background: var(--err-bg); color: var(--err-fg); }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= base_url('etalase') ?>" style="text-decoration:none;color:inherit;">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <a class="cart-link" href="<?= base_url('etalase') ?>">Etalase</a>
</header>
<main>
    <div class="auth-card">
        <h1>Login Pembeli</h1>
        <p class="lead">Masuk untuk melanjutkan pesanan.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash flash-err" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('message')): ?>
            <div class="flash flash-ok" role="status"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('login') ?>" novalidate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required maxlength="255" autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="auth-foot">
            Belum punya akun? <a href="<?= base_url('daftar') ?>">Daftar di sini</a>
        </div>
    </div>
</main>
</body>
</html>
