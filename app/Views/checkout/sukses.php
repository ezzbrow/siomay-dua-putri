<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Tersimpan — Siomay Dua Putri</title>
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
            --ok-bg: #DCFCE7;
            --ok-fg: #166534;
            --warn-bg: #FEF3C7;
            --warn-fg: #92400E;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-md: 0 4px 16px rgba(31, 22, 17, 0.08);
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
            box-shadow: var(--shadow-md);
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
        header a.nav {
            color: var(--brand-kuning);
            text-decoration: none;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.10);
            font-size: 0.9rem;
        }
        header a.nav:hover { background: rgba(255, 255, 255, 0.18); }

        main {
            max-width: 720px;
            margin: 24px auto;
            padding: 0 16px 64px;
        }
        .flash {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            background: var(--ok-bg);
            color: var(--ok-fg);
            font-weight: 500;
        }
        section.card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-md);
            padding: 24px;
            margin-bottom: 20px;
        }
        .check-circle {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: var(--ok-bg);
            color: var(--ok-fg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
        }
        h1 { color: var(--brand); margin: 0 0 4px; font-size: 1.4rem; }
        p.lead { color: var(--ink-soft); margin: 0 0 20px; }

        .kode-box {
            background: var(--bg-soft);
            border: 1.5px dashed var(--brand);
            border-radius: var(--radius-md);
            padding: 16px;
            text-align: center;
            margin-bottom: 16px;
        }
        .kode-box .label { font-size: 0.85rem; color: var(--ink-soft); }
        .kode-box .kode {
            font-family: ui-monospace, "SF Mono", Consolas, monospace;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brand);
            margin-top: 4px;
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
            font-size: 1.1rem;
            border-top: 2px dashed var(--brand);
            padding-top: 10px;
            margin-top: 8px;
            color: var(--brand-merah);
        }

        .items-list { margin: 0; padding: 0; list-style: none; }
        .items-list li {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid var(--line);
        }
        .items-list li:last-child { border-bottom: none; }

        .status-pending {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            background: var(--warn-bg);
            color: var(--warn-fg);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 20px;
            background: var(--terracotta);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            min-height: 44px;
            transition: background 150ms ease-out;
        }
        .btn:hover { background: #9A340B; }
        .btn-secondary {
            background: #fff;
            color: var(--ink);
            border: 1.5px solid var(--line);
        }
        .btn-secondary:hover { background: var(--bg-soft); }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px; }

        .info-box {
            background: var(--warn-bg);
            color: var(--warn-fg);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= base_url('etalase') ?>">
        <span class="brand-mark" aria-hidden="true">S</span>
        Siomay <span class="duaputri">Dua Putri</span>
    </a>
    <a class="nav" href="<?= base_url('akun/riwayat') ?>">Riwayat Pesanan</a>
</header>
<main>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="flash"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>

    <section class="card">
        <span class="check-circle" aria-hidden="true">✓</span>
        <h1>Pesanan Tersimpan</h1>
        <p class="lead">Pesanan Anda sudah kami catat. Pembayaran QRIS menyusul (fitur di Langkah 5).</p>

        <div class="kode-box">
            <div class="label">Nomor Pesanan</div>
            <div class="kode"><?= esc($pesanan['kode_pesanan']) ?></div>
        </div>

        <p>Status: <span class="status-pending"><?= esc(ucfirst($pesanan['status'])) ?></span></p>
        <p>Metode: <strong><?= esc(ucfirst(str_replace('_', ' ', $pesanan['metode']))) ?></strong></p>
        <p>Tanggal dibutuhkan: <strong><?= esc(date('d M Y', strtotime($pesanan['tanggal_dibutuhkan']))) ?></strong></p>
        <?php if (! empty($pesanan['alamat'])): ?>
            <p>Alamat: <?= esc($pesanan['alamat']) ?></p>
        <?php endif; ?>
        <?php if (! empty($pesanan['catatan'])): ?>
            <p>Catatan: <?= esc($pesanan['catatan']) ?></p>
        <?php endif; ?>

        <div class="info-box">
            <strong>Catatan:</strong> Pesanan Anda sudah tersimpan di database dengan status <em>pending</em>. Untuk saat ini, halaman bukti transaksi/struk dan tombol WA otomatis akan dibangun di langkah berikutnya. Anda bisa menunggu admin menghubungi via WhatsApp untuk konfirmasi dan pembayaran.
        </div>

        <div class="actions">
            <a class="btn" href="<?= base_url('akun/riwayat') ?>">Lihat Riwayat Pesanan</a>
            <a class="btn btn-secondary" href="<?= base_url('etalase') ?>">Kembali ke Etalase</a>
        </div>
    </section>

    <section class="card">
        <h2 style="color:var(--brand);margin:0 0 12px;font-size:1.1rem;">Detail Pesanan</h2>
        <ul class="items-list">
            <?php foreach ($items as $it): ?>
                <li>
                    <div>
                        <?= esc($it['produk_nama']) ?>
                        <?php if (! empty($it['nama_varian'])): ?>
                            <span style="color:var(--ink-soft);"> (varian: <?= esc($it['nama_varian']) ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div><?= rtrim(rtrim(number_format((float) $it['jumlah'], 2), '0'), '.') ?> × Rp <?= number_format($it['harga_satuan'], 0, ',', '.') ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="summary-line muted">
            <span>Subtotal</span>
            <span>Rp <?= number_format((float) $pesanan['subtotal'], 0, ',', '.') ?></span>
        </div>
        <?php if ((float) $pesanan['pajak'] > 0): ?>
            <div class="summary-line muted">
                <span>Pajak</span>
                <span>Rp <?= number_format((float) $pesanan['pajak'], 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
        <div class="summary-line total">
            <span>Total</span>
            <span>Rp <?= number_format((float) $pesanan['total'], 0, ',', '.') ?></span>
        </div>
    </section>
</main>
</body>
</html>
