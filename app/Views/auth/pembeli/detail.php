<?php // TODO: didesain oleh frontend, JANGAN diedit oleh Antigravity ?>
<h1>Detail Pesanan</h1>

<p>No. Pesanan: <?= esc($pesanan['kode_pesanan'] ?? '') ?></p>
<p>Tanggal Dibutuhkan: <?= esc($pesanan['tanggal_dibutuhkan'] ?? '') ?></p>
<p>Metode: <?= esc($pesanan['metode'] ?? '') ?></p>
<?php if (!empty($pesanan['alamat'])): ?>
  <p>Alamat Pengiriman: <?= esc($pesanan['alamat']) ?></p>
<?php endif; ?>
<p>Status: <?= esc($pesanan['status'] ?? '') ?></p>

<h3>Menu Pesanan</h3>
<ul>
<?php if (isset($items) && is_array($items)): ?>
  <?php foreach ($items as $item): ?>
    <li>
      <?= esc($item['produk_nama']) ?> 
      <?= !empty($item['nama_varian']) ? '(' . esc($item['nama_varian']) . ')' : '' ?> 
      x <?= esc($item['jumlah']) ?> = Rp<?= esc(number_format((float)$item['subtotal_item'], 0, ',', '.')) ?>
    </li>
  <?php endforeach; ?>
<?php endif; ?>
</ul>

<p>Subtotal: Rp<?= esc(number_format((float)($pesanan['subtotal'] ?? 0), 0, ',', '.')) ?></p>
<p>Pajak: Rp<?= esc(number_format((float)($pesanan['pajak'] ?? 0), 0, ',', '.')) ?></p>
<p>Total: Rp<?= esc(number_format((float)($pesanan['total'] ?? 0), 0, ',', '.')) ?></p>

<a href="<?= base_url('akun/riwayat') ?>">Kembali ke Riwayat Pesanan</a>
