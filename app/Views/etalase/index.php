<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Etalase — Siomay Dua Putri</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #C9A8E0; color: #111; }
        header { background: #1D4ED8; color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        header .duaputri { color: #DC2626; }
        header a.cart-link { color: #FACC15; text-decoration: none; font-weight: 600; }
        main { max-width: 960px; margin: 24px auto; padding: 0 16px; }
        .status-banner { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .status-open { background: #DCFCE7; color: #166534; }
        .status-closed { background: #FEE2E2; color: #991B1B; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-ok { background: #DCFCE7; color: #166534; }
        .flash-err { background: #FEE2E2; color: #991B1B; }
        section.kategori { background: #fff; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        h2 { margin-top: 0; color: #1D4ED8; }
        .produk { border: 1px solid #eee; border-radius: 8px; padding: 12px; margin: 8px 0; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .produk.tidak-tersedia { background: #E5E7EB; color: #6B7280; opacity: 0.7; }
        .produk.tidak-tersedia .nama, .produk.tidak-tersedia .harga { text-decoration: line-through; }
        .nama { font-weight: 600; font-size: 1.05em; }
        .harga { color: #DC2626; font-weight: 600; }
        .varian-info { font-size: 0.85em; color: #6B7280; }
        .badge { display: inline-block; font-size: 0.75em; padding: 2px 8px; border-radius: 999px; background: #FACC15; color: #111; margin-left: 6px; }
        .badge.disabled { background: #9CA3AF; color: #fff; }
        .qty-form { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .qty-form select, .qty-form input[type="number"] { padding: 6px 8px; border: 1px solid #D1D5DB; border-radius: 6px; }
        .qty-form input[type="number"] { width: 64px; }
        .btn { display: inline-block; padding: 6px 12px; background: #DC2626; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 0.9em; }
        .btn.secondary { background: #6B7280; }
        .btn:disabled { background: #9CA3AF; cursor: not-allowed; }
        .cart-card { background: #fff; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .cart-card h2 { margin-top: 0; }
        .cart-empty { color: #6B7280; }
        .cart-line { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #F3F4F6; gap: 12px; flex-wrap: wrap; }
        .cart-line:last-child { border-bottom: none; }
        .cart-line .info { flex: 1; }
        .cart-line .info .nm { font-weight: 600; }
        .cart-line .info .v { font-size: 0.85em; color: #6B7280; }
        .cart-line form { display: inline; margin: 0; }
        .total-row { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1em; margin-top: 12px; padding-top: 12px; border-top: 2px solid #1D4ED8; }
        .min-warning { background: #FEF3C7; color: #92400E; padding: 10px 14px; border-radius: 6px; margin-top: 12px; }
        .min-ok { background: #DCFCE7; color: #166534; padding: 10px 14px; border-radius: 6px; margin-top: 12px; }
        textarea { width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-family: inherit; }
        .helper { font-size: 0.8em; color: #6B7280; }
    </style>
</head>
<body>
<header>
    <strong>Siomay <span class="duaputri">Dua Putri</span></strong> · Etalase
    <a class="cart-link" href="<?= base_url('keranjang') ?>">🛒 Keranjang</a>
</header>
<main>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="flash flash-ok"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>

    <?php if ($tokoBuka): ?>
        <div class="status-banner status-open">Toko sedang buka (<?= esc(substr($nowServerTime, 0, 5)) ?>).</div>
    <?php else: ?>
        <div class="status-banner status-closed">Toko sedang tutup. <?= esc($alasanTutup) ?></div>
    <?php endif; ?>

    <div class="cart-card">
        <h2>Keranjang</h2>
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
                    <form method="post" action="<?= base_url('keranjang/kurang') ?>" style="margin:0;">
                        <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                        <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                        <input type="hidden" name="jumlah" value="1">
                        <button class="btn secondary" type="submit">−</button>
                    </form>
                    <form method="post" action="<?= base_url('keranjang/hapus') ?>" style="margin:0;">
                        <input type="hidden" name="produk_id" value="<?= (int) $row['produk']['id'] ?>">
                        <input type="hidden" name="varian_id" value="<?= (int) ($row['varian']['id'] ?? 0) ?>">
                        <button class="btn secondary" type="submit" onclick="return confirm('Hapus item ini?')">Hapus</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="total-row">
                <span>Total</span>
                <span>Rp <?= number_format($cart['total'], 0, ',', '.') ?></span>
            </div>

            <?php if ($cart['canCheckout']): ?>
                <div class="min-ok">Minimum order terpenuhi. Silakan lanjut.</div>
                <p style="margin-top:12px;">
                    <a class="btn" href="#" onclick="alert('Lanjut ke Langkah 4 (checkout) — belum dibangun.'); return false;">Lanjut</a>
                </p>
            <?php else: ?>
                <div class="min-warning">
                    Minimum order Rp <?= number_format($cart['minOrder'], 0, ',', '.') ?>.
                    Kurang Rp <?= number_format($cart['kekurangan'], 0, ',', '.') ?> lagi.
                </div>
                <p style="margin-top:12px;">
                    <button class="btn" disabled>Lanjut</button>
                </p>
            <?php endif; ?>

            <form method="post" action="<?= base_url('keranjang/catatan') ?>" style="margin-top:16px;">
                <label for="catatan" style="font-weight:600;">Catatan (opsional, untuk permintaan rasa)</label>
                <textarea id="catatan" name="catatan" rows="2" maxlength="500" placeholder="Contoh: extra saus, tidak pedas, bumbunya dipisah"><?= esc($catatan ?? '') ?></textarea>
                <p class="helper">
                    <strong>Penting:</strong> kolom ini hanya untuk permintaan rasa, <strong>BUKAN</strong> untuk alamat.
                    Alamat pengiriman diisi di langkah berikutnya.
                </p>
                <button class="btn secondary" type="submit" style="margin-top:6px;">Simpan Catatan</button>
            </form>
        <?php endif; ?>
    </div>

    <?php foreach (['Somay Sapi', 'Lumpia', 'Pentol Goreng'] as $kategori): ?>
        <?php $items = $grouped[$kategori] ?? []; if (empty($items)) continue; ?>
        <section class="kategori">
            <h2><?= esc($kategori) ?></h2>
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
                                <span class="badge disabled">Tidak tersedia</span>
                            <?php endif; ?>
                        </div>
                        <div class="harga">Rp <?= number_format((float) $p['harga'], 0, ',', '.') ?></div>
                    </div>
                    <?php if ($tersedia): ?>
                        <form class="qty-form" method="post" action="<?= base_url('keranjang/tambah') ?>">
                            <input type="hidden" name="produk_id" value="<?= (int) $p['id'] ?>">
                            <?php if ($isLumpia): ?>
                                <select name="varian_id" required>
                                    <option value="">— pilih varian —</option>
                                    <?php foreach ($p['varian'] as $v): ?>
                                        <option value="<?= (int) $v['id'] ?>"><?= esc($v['nama_varian']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="varian_id" value="0">
                            <?php endif; ?>
                            <input type="number" name="jumlah" value="1" min="1" max="999" required>
                            <button class="btn" type="submit">+ Tambah</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
