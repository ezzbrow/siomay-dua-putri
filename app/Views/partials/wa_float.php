<?php
// Ambil langsung dari DB agar robust terhadap setData scope issue
$waNumber = null;
$db = \Config\Database::connect();
$admin = $db->table('admin')->select('nomor_hp')->orderBy('id', 'ASC')->get()->getRowArray();
if ($admin && ! empty($admin['nomor_hp'])) {
    $digits = preg_replace('/[^0-9]/', '', (string) $admin['nomor_hp']);
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    } elseif (str_starts_with($digits, '8')) {
        $digits = '62' . $digits;
    }
    if (strlen($digits) >= 9) {
        $waNumber = $digits;
    }
}
$waMessage = $waMessage ?? 'Halo, saya mau tanya-tanya tentang Siomay Dua Putri.';
?>
<?php if (! empty($waNumber)): ?>
<a class="wa-float"
   href="https://wa.me/<?= esc($waNumber, 'url') ?>?text=<?= urlencode($waMessage) ?>"
   target="_blank" rel="noopener"
   aria-label="Chat WhatsApp">
    <span class="material-symbols-outlined" aria-hidden="true">chat</span>
</a>
<style>
    .wa-float {
        position: fixed; right: 20px; bottom: 20px; z-index: 99;
        width: 56px; height: 56px; border-radius: 50%;
        background: #25D366; color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        box-shadow: 0 6px 20px rgba(37,211,102,0.5);
        text-decoration: none; transition: transform 180ms;
    }
    .wa-float:hover { transform: scale(1.08); color: #fff; }
    .wa-float .material-symbols-outlined { font-size: 30px; }
</style>
<?php endif; ?>
