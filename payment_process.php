<?php
require_once 'config.php';

$id = $_POST['id_pesanan'];
$dibayar = $_POST['dibayar'];

// Ambil total harga
$stmt = $pdo->prepare("SELECT total_harga FROM pesanan WHERE id_pesanan = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Pesanan tidak ditemukan!");
}

$kembalian = $dibayar - $order['total_harga'];

// Insert pembayaran
$stmt = $pdo->prepare("INSERT INTO pembayaran 
(id_pesanan, metode, total_bayar, uang_diterima, kembalian, tanggal_bayar)
VALUES (?, 'cash', ?, ?, ?, NOW())");

$stmt->execute([$id, $order['total_harga'], $dibayar, $kembalian]);

// Update status
$pdo->prepare("UPDATE pesanan SET status = 1 WHERE id_pesanan=?")->execute([$id]);

header("Location: struk.php?id=$id");
exit;
?>
