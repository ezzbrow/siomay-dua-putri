<h2>Laporan Laba-Rugi</h2>
<form method="get">
    Dari: <input type="date" name="dari" value="<?= esc($dari) ?>">
    Sampai: <input type="date" name="sampai" value="<?= esc($sampai) ?>">
    <button type="submit">Filter</button>
</form>

<p>Pemasukan: Rp <?= number_format($pemasukan, 0, ',', '.') ?></p>
<p>Pengeluaran: Rp <?= number_format($pengeluaran, 0, ',', '.') ?></p>
<p><strong>Laba/Rugi: Rp <?= number_format($laba_rugi, 0, ',', '.') ?></strong></p>