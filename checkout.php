<?php
require_once 'config.php';

$nama = $_POST['nama'];
$meja = $_POST['no_meja'];
$jenis = strtolower($_POST['jenis_order']);
$cart = json_decode($_POST['cart_data'], true);

$nomor_order = "ORD" . time();
$tanggal = date('Y-m-d H:i:s');

$total = 0;
foreach ($cart as $c) {
    $total += $c['quantity'] * $c['price'];
}

// simpan orders
$stmt = $pdo->prepare("INSERT INTO orders (nomor_order, nama_pelanggan, no_meja, jenis_order, tanggal, total_harga)
VALUES (?,?,?,?,?,?)");
$stmt->execute([$nomor_order, $nama, $meja, $jenis, $tanggal, $total]);
$id_order = $pdo->lastInsertId();

// simpan items
foreach ($cart as $c) {
    $pdo->prepare("INSERT INTO order_items (id_order, id_menu, jumlah, harga)
    VALUES (?,?,?,?)")->execute([$id_order, $c['id'], $c['quantity'], $c['price']]);
}

// kosongkan cart
echo "<script>localStorage.removeItem('cart');</script>";

echo "<h2>Pesanan Berhasil!</h2>";
echo "<p>No Order: <b>$nomor_order</b></p>";
echo "<a href='orders.php'>Lihat Pesanan</a>";
