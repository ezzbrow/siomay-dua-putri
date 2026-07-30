<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Kata Sandi — Siomay Dua Putri</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #FBF9FF; padding: 40px 20px; display: flex; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 32px; max-width: 420px; width: 100%; border: 1px solid #E5E7EB; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h1 { font-size: 1.4rem; color: #5731B6; margin-top: 0; }
        p { color: #6B7280; font-size: 0.85rem; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; }
        input[type="password"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #E5E7EB; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 12px; background: #5731B6; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .alert { background: #FEE2E2; color: #991B1B; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; }
        a { color: #5731B6; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Buat Kata Sandi Baru</h1>
        <p>Silakan buat kata sandi baru untuk akun Anda.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('reset-password/proses') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

            <div class="form-group">
                <label for="password">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label for="password_confirm">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6" placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" class="btn-submit">Simpan Kata Sandi Baru</button>
        </form>
    </div>
</body>
</html>
