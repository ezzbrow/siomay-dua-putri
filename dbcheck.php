<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=siomay_dua_putri', 'root', '');
echo "pesanan_acara:\n";
$stmt = $pdo->query('DESCRIBE pesanan_acara');
foreach($stmt as $r) echo '  ' . $r['Field'] . ' | ' . $r['Type'] . ' | ' . $r['Null'] . ' | ' . $r['Default'] . "\n";

echo "\nitem_pesanan_acara:\n";
$stmt = $pdo->query('DESCRIBE item_pesanan_acara');
foreach($stmt as $r) echo '  ' . $r['Field'] . ' | ' . $r['Type'] . ' | ' . $r['Null'] . "\n";

echo "\npengaturan columns:\n";
$stmt = $pdo->query('DESCRIBE pengaturan');
foreach($stmt as $r) echo '  ' . $r['Field'] . ' | ' . $r['Type'] . ' | ' . $r['Default'] . "\n";

echo "\npengaturan values:\n";
$stmt = $pdo->query('SELECT * FROM pengaturan LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row) foreach($row as $k => $v) echo "  $k: $v\n";

echo "\nproduk tampil_di_pesan_stand=1:\n";
$stmt = $pdo->query('SELECT id, nama, kategori, harga FROM produk WHERE tampil_di_pesan_stand=1 ORDER BY kategori, id');
foreach($stmt as $r) echo '  id=' . $r['id'] . ' | ' . $r['nama'] . ' | ' . $r['kategori'] . ' | ' . $r['harga'] . "\n";

echo "\nTotal produk stand: " . $pdo->query('SELECT COUNT(*) FROM produk WHERE tampil_di_pesan_stand=1')->fetchColumn() . "\n";
