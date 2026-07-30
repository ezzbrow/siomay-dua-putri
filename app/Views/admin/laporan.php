<?= $this->include('partials/admin_header') ?>
<h2>Laporan Penjualan</h2>
<form method="get">
    Dari: <input type="date" name="dari" value="<?= esc($dari) ?>">
    Sampai: <input type="date" name="sampai" value="<?= esc($sampai) ?>">
    <button type="submit">Filter</button>
</form>

<p>Total Omzet: Rp <?= number_format($total_omzet, 0, ',', '.') ?></p>
<p>Jumlah Transaksi: <?= $jumlah_transaksi ?></p>

<a href="<?= base_url('admin/laporan/export?dari=' . $dari . '&sampai=' . $sampai) ?>">Export CSV</a>