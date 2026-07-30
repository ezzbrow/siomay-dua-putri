<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi — Siomay Dua Putri</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #FBF9FF; padding: 40px 20px; display: flex; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 32px; max-width: 420px; width: 100%; border: 1px solid #E5E7EB; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h1 { font-size: 1.4rem; color: #5731B6; margin-top: 0; }
        p { color: #6B7280; font-size: 0.85rem; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; }
        input[type="email"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #E5E7EB; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 12px; background: #5731B6; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .alert { background: #FEE2E2; color: #991B1B; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; }
        .info { background: #E0F2FE; color: #0369A1; padding: 12px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; word-break: break-all; }
        a { color: #5731B6; text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Lupa Kata Sandi</h1>
        <p>Masukkan email Anda untuk menerima tautan reset password.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            <div class="info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('lupa-password') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Masukkan email terdaftar">
            </div>
            <button type="submit" class="btn-submit">Kirim Link Reset</button>
        </form>
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?= base_url('login') ?>">Kembali ke Halaman Masuk</a>
        </div>
    </div>
</body>
</html>
