<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin — Siomay Dua Putri</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #C9A8E0; color: #111; min-height: 100vh; display: flex; flex-direction: column; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; }
        header .duaputri { color: #DC2626; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; width: 360px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        h1 { margin-top: 0; color: #1D4ED8; font-size: 1.4em; }
        label { display: block; font-weight: 600; margin: 12px 0 4px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; box-sizing: border-box; }
        .btn { display: block; width: 100%; margin-top: 16px; padding: 10px; background: #DC2626; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; font-size: 0.9em; }
        .msg.err { background: #FEE2E2; color: #991B1B; }
        .msg.ok { background: #DCFCE7; color: #166534; }
        .row { display: flex; align-items: center; gap: 6px; margin-top: 10px; }
    </style>
</head>
<body>
<header><strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Login Admin</header>
<main>
    <div class="card">
        <h1>Login</h1>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="msg err"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('message')): ?>
            <div class="msg ok"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif; ?>

        <?php if (! empty($lockSeconds) && $lockSeconds > 0): ?>
            <div class="msg err">Akun terkunci sementara. Coba lagi dalam <?= ceil($lockSeconds / 60) ?> menit.</div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('admin/login') ?>">
            <label>Username</label>
            <input type="text" name="username" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <div class="row">
                <input type="checkbox" name="remember" value="1" id="remember">
                <label for="remember" style="margin:0;font-weight:400;">Tetap masuk</label>
            </div>

            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</main>
</body>
</html>
