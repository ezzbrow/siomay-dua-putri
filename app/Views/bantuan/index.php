<?php // TODO: didesain oleh frontend, JANGAN diedit oleh Antigravity ?>
<h1>Butuh Bantuan?</h1>

<p>Hubungi kami jika ada kendala dengan pesanan Anda.</p>

<?php if (!empty($waUrl)): ?>
  <a href="<?= esc($waUrl) ?>" target="_blank">Chat WhatsApp Penjual</a>
<?php endif; ?>

<a href="<?= base_url('/') ?>">Kembali ke Beranda</a>
