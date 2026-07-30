<form method="get">
    Dari: <input type="date" name="dari" value="<?= esc($dari ?? '') ?>">
    Sampai: <input type="date" name="sampai" value="<?= esc($sampai ?? '') ?>">
    <button type="submit">Filter</button>
</form>

<table border="1">
<tr><th>Kode</th><th>Tanggal</th><th>Metode</th><th>Total</th></tr>
<?php foreach ($riwayat as $r): ?>
<tr>
    <td><?= esc($r['kode_pesanan']) ?></td>
    <td><?= esc($r['tanggal_dibutuhkan']) ?></td>
    <td><?= esc($r['metode']) ?></td>
    <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
</tr>
<?php endforeach; ?>
</table>

