<?php
require_once 'config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<h1>Pembayaran Order #<?= $order['id_pesanan'] ?></h1>

<p>Total Harga: Rp <?= number_format($order['total_harga']) ?></p>

<form method="POST" action="payment_process.php">
    <input type="hidden" name="id_pesanan" value="<?= $id ?>">

    <label>Uang Diterima:</label>
    <input type="number" name="dibayar" required>

    <button type="submit">Bayar</button>
</form>
