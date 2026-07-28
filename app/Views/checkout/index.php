<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — Siomay Dua Putri</title>
    <style>
        :root {
            --bg-page: #FFF7ED;
            --bg-card: #FFFFFF;
            --bg-soft: #FEF3E8;
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
            flex-wrap: wrap;
        }
        header .brand {
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: 1.1rem;
            text-decoration: none; color: inherit;
        }
        header .duaputri { color: var(--brand-merah); }
        header .brand-mark {
            width: 32px; height: 32px;
            background: var(--brand-kuning);
            border-radius: var(--radius-md);
            display: inline-flex; align-items: center; justify-content: center;
            color: #1F1611; font-weight: 800;
        }
        header .header-right { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        header a.cart-link,
        header a.back-link {
            color: var(--brand-kuning);
            text-decoration: none;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.10);
            font-size: 0.9rem;
        }
        header a.cart-link:hover, header a.back-link:hover { background: rgba(255, 255, 255, 0.18); }

        main {
            max-width: 720px;
            margin: 24px auto;
            padding: 0 16px 64px;
        }
        h1 {
            color: var(--brand);
            margin: 0 0 4px;
            font-size: 1.5rem;
        }
        p.lead { color: var(--ink-soft); margin: 0 0 20px; }

        .flash {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            font-weight: 500;
        }
        .flash-ok { background: var(--ok-bg); color: var(--ok-fg); }
        .flash-err { background: var(--err-bg); color: var(--err-fg); }
        .field-errors {
            background: var(--err-bg);
            color: var(--err-fg);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .field-errors ul { margin: 6px 0 0; padding-left: 20px; }

        section.card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-md);
            padding: 20px 24px;
            margin-bottom: 20px;
        }
        section.card h2 {
            color: var(--brand);
            margin: 0 0 12px;
            font-size: 1.15rem;
        }
        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-variant-numeric: tabular-nums;
        }
        .summary-line.muted { color: var(--ink-soft); }
        .summary-line.total {
            font-weight: 700;
            font-size: 1.15rem;
            border-top: 2px dashed var(--brand);
            padding-top: 10px;
            margin-top: 8px;
            color: var(--brand-merah);
        }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--ink);
        }
        .form-group label .required { color: var(--brand-merah); }
        .form-group .hint {
            color: var(--ink-soft);
            font-size: 0.85rem;
            margin-top: 4px;
        }
        .form-group input[type="date"],
        .form-group input[type="text"],
        .form-group input[type="tel"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
            font-family: inherit;
            min-height: 44px;
            font-size: 1rem;
            transition: border-color var(--t-fast), box-shadow var(--t-fast);
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }
        .form-group textarea { min-height: 80px; resize: vertical; }

        .metode-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 480px) { .metode-group { grid-template-columns: 1fr; } }
        .metode-option {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px 14px;
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            cursor: pointer;
            background: #fff;
            transition: border-color var(--t-fast), background var(--t-fast);
        }
        .metode-option:hover { border-color: var(--brand); background: var(--bg-soft); }
        .metode-option input { margin-top: 4px; }
        .metode-option strong { display: block; color: var(--ink); }
        .metode-option span { color: var(--ink-soft); font-size: 0.85rem; }

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

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            background: #fff;
            color: var(--ink);
            border: 1.5px solid var(--line);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            min-height: 44px;
        }
        .btn-secondary:hover { background: var(--bg-soft); border-color: var(--muted, #A89889); }

        .items-list { margin: 0; padding: 0; list-style: none; }
        .items-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--line);
            gap: 12px;
        }
        .items-list li:last-child { border-bottom: none; }
        .items-list .nm { font-weight: 600; }
        .items-list .v { color: var(--ink-soft); font-size: 0.9rem; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= base_url('etalase') ?>">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <div class="header-right">
        <a class="back-link" href="<?= base_url('keranjang') ?>">← Keranjang</a>
    </div>
</header>
<main>
    <h1>Checkout</h1>
    <p class="lead">Lengkapi data pesanan Anda. Pembayaran QRIS menyusul (Langkah 5).</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $fieldErrors = session()->getFlashdata('errors'); ?>
    <?php if (! empty($fieldErrors) && is_array($fieldErrors)): ?>
        <div class="field-errors">
            <strong>Perbaiki isian berikut:</strong>
            <ul>
                <?php foreach ($fieldErrors as $msg): ?>
                    <li><?= esc($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Ringkasan Pesanan</h2>
        <ul class="items-list">
            <?php foreach ($cart['rows'] as $row): ?>
                <li>
                    <div>
                        <div class="nm">
                            <?= esc($row['produk']['nama']) ?>
                            <?php if (! empty($row['varian'])): ?>
                                <span class="v">(varian: <?= esc($row['varian']['nama_varian']) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="v"><?= (int) $row['jumlah'] ?> × Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                    </div>
                    <div class="v">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="summary-line muted">
            <span>Subtotal</span>
            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
        </div>
        <?php if ($pajakAktif): ?>
            <div class="summary-line muted">
                <span>Pajak (<?= rtrim(rtrim(number_format($pajakPersen, 2), '0'), '.') ?>%)</span>
                <span>Rp <?= number_format($pajak, 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
        <div class="summary-line total">
            <span>Total</span>
            <span>Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
        </div>
    </section>

    <form method="post" action="<?= base_url('checkout') ?>" novalidate>
        <?= csrf_field() ?>

        <section class="card">
            <h2>Tanggal Pesanan Dibutuhkan</h2>
            <div class="form-group">
                <label for="tanggal_dibutuhkan">Tanggal dibutuhkan <span class="required">*</span></label>
                <input type="date" id="tanggal_dibutuhkan" name="tanggal_dibutuhkan"
                       min="<?= esc($besok) ?>"
                       value="<?= esc(old('tanggal_dibutuhkan', $besok)) ?>" required>
                <p class="hint">Pre-order minimal H+1. Sistem tidak menerima pesanan untuk hari yang sama.</p>
            </div>
        </section>

        <section class="card">
            <h2>Data Pemesan</h2>
            <div class="form-group">
                <label for="nama_pembeli">Nama <span class="required">*</span></label>
                <input type="text" id="nama_pembeli" name="nama_pembeli"
                       value="<?= esc(old('nama_pembeli', $pembeliNama)) ?>" required maxlength="255" autocomplete="name">
            </div>
            <div class="form-group">
                <label for="nomor_hp">Nomor HP <span class="required">*</span></label>
                <input type="tel" id="nomor_hp" name="nomor_hp"
                       value="<?= esc(old('nomor_hp')) ?>" required maxlength="30" autocomplete="tel" placeholder="cth: 081234567890">
                <p class="hint">Untuk konfirmasi pesanan via WhatsApp.</p>
            </div>
        </section>

        <section class="card">
            <h2>Metode Penerimaan</h2>
            <div class="metode-group">
                <label class="metode-option">
                    <input type="radio" name="metode" value="ambil_sendiri" required
                           <?= old('metode', 'ambil_sendiri') === 'ambil_sendiri' ? 'checked' : '' ?>>
                    <span>
                        <strong>Ambil Sendiri</strong>
                        <span>Ambil pesanan di lokasi UMKM<?= $alamatUmkm ? ' (' . esc($alamatUmkm) . ')' : '' ?></span>
                    </span>
                </label>
                <label class="metode-option">
                    <input type="radio" name="metode" value="diantar"
                           <?= old('metode') === 'diantar' ? 'checked' : '' ?>>
                    <span>
                        <strong>Diantar</strong>
                        <span>Admin antar ke alamat Anda (akan diongkosi via Maxim)</span>
                    </span>
                </label>
            </div>
        </section>

        <section class="card" id="alamat-section" style="display:none;">
            <h2>Alamat Pengiriman <span class="required" style="color:var(--brand-merah);">*</span></h2>
            <div class="form-group">
                <label for="alamat">Alamat lengkap <span class="required">*</span></label>
                <textarea id="alamat" name="alamat" maxlength="500" placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota"><?= esc(old('alamat')) ?></textarea>
            </div>
        </section>

        <section class="card">
            <h2>Catatan (opsional)</h2>
            <div class="form-group">
                <label for="catatan">Catatan pesanan</label>
                <textarea id="catatan" name="catatan" maxlength="500" placeholder="cth: extra saus, bumbunya dipisah"><?= esc(old('catatan')) ?></textarea>
                <p class="hint">Hanya untuk permintaan rasa, BUKAN alamat.</p>
            </div>
        </section>

        <div class="actions">
            <a class="btn-secondary" href="<?= base_url('keranjang') ?>">← Kembali</a>
            <button type="submit" class="btn" style="flex:1;">Simpan Pesanan</button>
        </div>
    </form>
</main>
<script>
    (function () {
        var radios = document.querySelectorAll('input[name="metode"]');
        var alamatSection = document.getElementById('alamat-section');
        var alamatInput = document.getElementById('alamat');
        function sync() {
            var v = document.querySelector('input[name="metode"]:checked');
            if (v && v.value === 'diantar') {
                alamatSection.style.display = '';
                alamatInput.required = true;
            } else {
                alamatSection.style.display = 'none';
                alamatInput.required = false;
            }
        }
        radios.forEach(function (r) { r.addEventListener('change', sync); });
        sync();
    })();
</script>
</body>
</html>
