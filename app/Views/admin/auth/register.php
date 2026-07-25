<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Admin (Setup Awal) — Siomay Dua Putri</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #C9A8E0; color: #111; min-height: 100vh; display: flex; flex-direction: column; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; }
        header .duaputri { color: #DC2626; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; width: 420px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        h1 { margin-top: 0; color: #1D4ED8; font-size: 1.4em; }
        label { display: block; font-weight: 600; margin: 12px 0 4px; }
        input { width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; box-sizing: border-box; }
        .btn { display: block; width: 100%; margin-top: 16px; padding: 10px; background: #DC2626; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; font-size: 0.9em; }
        .msg.err { background: #FEE2E2; color: #991B1B; }
        .err { color: #B91C1C; font-size: 0.8em; }
        .note { font-size: 0.85em; color: #6B7280; margin-top: 8px; }
    </style>
</head>
<body>
<header><strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Setup Admin</header>
<main>
    <div class="card">
        <h1>Registrasi Admin (Setup Awal)</h1>
        <p class="note">Halaman ini hanya bisa diakses sekali. Setelah registrasi, silakan login.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="msg err"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form method="post" action="<?= base_url('admin/register') ?>">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" value="<?= esc(old('nama_toko')) ?>" required>
            <?php if (! empty($errors['nama_toko'])): ?><div class="err"><?= esc($errors['nama_toko']) ?></div><?php endif; ?>

            <label>Username</label>
            <input type="text" name="username" value="<?= esc(old('username')) ?>" required>
            <?php if (! empty($errors['username'])): ?><div class="err"><?= esc($errors['username']) ?></div><?php endif; ?>

            <label>Email</label>
            <input type="email" name="email" value="<?= esc(old('email')) ?>" required>
            <?php if (! empty($errors['email'])): ?><div class="err"><?= esc($errors['email']) ?></div><?php endif; ?>

            <label>Nomor HP</label>
            <input type="text" name="nomor_hp" value="<?= esc(old('nomor_hp')) ?>" required>
            <?php if (! empty($errors['nomor_hp'])): ?><div class="err"><?= esc($errors['nomor_hp']) ?></div><?php endif; ?>

            <label>Password</label>
            <input type="password" name="password" required>
            <?php if (! empty($errors['password'])): ?><div class="err"><?= esc($errors['password']) ?></div><?php endif; ?>

            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirm" required>
            <?php if (! empty($errors['password_confirm'])): ?><div class="err"><?= esc($errors['password_confirm']) ?></div><?php endif; ?>

            <button class="btn" type="submit">Daftar &amp; Simpan</button>
        </form>
    </div>
</main>
</body>
</html>
