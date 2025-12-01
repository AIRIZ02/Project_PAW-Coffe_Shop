<?php
require_once 'config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan=?");
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT dp.*, m.nama_menu, m.harga
    FROM detail_pesanan dp
    JOIN menu m ON dp.id_menu = m.id_menu
    WHERE dp.id_pesanan=?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE id_pesanan=?");
$stmt->execute([$id]);
$payment = $stmt->fetch();

// Hitung total dari detail pesanan
$grandTotal = 0;
foreach ($items as $i) {
    $grandTotal += $i['jumlah'] * $i['harga'];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Struk Pesanan</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f3f4f6;
        padding: 20px;
    }
    .container {
        width: 450px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        background: #4f46e5;
        color: white;
        padding: 12px;
        border-radius: 8px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th {
        background: #4f46e5;
        color: white;
        padding: 8px;
    }
    td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }
    tr:nth-child(even) {
        background: #f9fafb;
    }
    .total-box {
        margin-top: 15px;
        padding: 15px;
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 8px;
    }
    .btn-print, .btn-back {
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 15px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-print {
        background: #10b981;
        color: white;
    }
    .btn-back {
        background: #ef4444;
        color: white;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Struk Pesanan #<?= $pesanan['id_pesanan'] ?></h2>

    <p><strong>Nama:</strong> <?= $pesanan['nama_pelanggan'] ?></p>
    <p><strong>Meja:</strong> <?= $pesanan['meja'] ?></p>

    <table>
        <tr>
            <th>Menu</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($items as $i): ?>
        <tr>
            <td><?= $i['nama_menu'] ?></td>
            <td><?= $i['jumlah'] ?></td>
            <td>Rp <?= number_format($i['harga']) ?></td>
            <td>Rp <?= number_format($i['jumlah'] * $i['harga']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="total-box">
        <h3>Total: <strong>Rp <?= number_format($grandTotal) ?></strong></h3>

        <?php if ($payment): ?>
            <p><strong>Uang diterima:</strong> Rp <?= number_format($payment['uang_diterima']) ?></p>
            <p><strong>Kembalian:</strong> Rp <?= number_format($payment['kembalian']) ?></p>
        <?php endif; ?>
    </div>

    <button class="btn-print" onclick="window.print()">Print</button>
    <a class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition" href="reviews.php">Kembali & Review</a>
</div>

</body>
</html>
