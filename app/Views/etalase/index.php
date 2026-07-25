<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Etalase — Siomay Dua Putri</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #C9A8E0; color: #111; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; }
        header .duaputri { color: #DC2626; }
        main { max-width: 960px; margin: 24px auto; padding: 0 16px; }
        .status-banner { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .status-open { background: #DCFCE7; color: #166534; }
        .status-closed { background: #FEE2E2; color: #991B1B; }
        section.kategori { background: #fff; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        h2 { margin-top: 0; color: #1D4ED8; }
        .produk { border: 1px solid #eee; border-radius: 8px; padding: 12px; margin: 8px 0; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .produk.tidak-tersedia { background: #E5E7EB; color: #6B7280; opacity: 0.7; }
        .produk.tidak-tersedia .nama, .produk.tidak-tersedia .harga { text-decoration: line-through; }
        .nama { font-weight: 600; font-size: 1.05em; }
        .harga { color: #DC2626; font-weight: 600; }
        .varian { font-size: 0.85em; color: #6B7280; }
        .badge { display: inline-block; font-size: 0.75em; padding: 2px 8px; border-radius: 999px; background: #FACC15; color: #111; margin-left: 6px; }
        .badge.disabled { background: #9CA3AF; color: #fff; }
    </style>
</head>
<body>
<header>
    <strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Etalase
</header>
<main>
    <?php if ($tokoBuka): ?>
        <div class="status-banner status-open">Toko sedang buka (<?= esc(substr($nowServerTime, 0, 5)) ?>).</div>
    <?php else: ?>
        <div class="status-banner status-closed">Toko sedang tutup. <?= esc($alasanTutup) ?></div>
    <?php endif; ?>

    <?php foreach (['Somay Sapi', 'Lumpia', 'Pentol Goreng'] as $kategori): ?>
        <?php $items = $grouped[$kategori] ?? []; if (empty($items)) continue; ?>
        <section class="kategori">
            <h2><?= esc($kategori) ?></h2>
            <?php foreach ($items as $p):
                $tersedia = \App\Helpers\ProductAvailability::isProductTersedia($p, $tokoBuka);
                $kelas = $tersedia ? '' : 'tidak-tersedia';
                $disabledAttr = $tersedia ? '' : 'aria-disabled="true"';
            ?>
                <div class="produk <?= $kelas ?>" <?= $disabledAttr ?>>
                    <div>
                        <div class="nama">
                            <?= esc($p['nama']) ?>
                            <?php if (! $tersedia): ?>
                                <span class="badge disabled">Tidak tersedia</span>
                            <?php endif; ?>
                        </div>
                        <?php if (! empty($p['varian'])): ?>
                            <div class="varian">Varian:
                                <?= esc(implode(', ', array_column($p['varian'], 'nama_varian'))) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="harga">Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
