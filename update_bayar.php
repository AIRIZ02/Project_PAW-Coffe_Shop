<?php
require_once "config.php";

$id = $_GET['id'];

// update status pesanan
$pdo->prepare("UPDATE pesanan SET status = 1 WHERE id_pesanan = ?")
    ->execute([$id]);

// simpan log cetak struk
$pdo->prepare("INSERT INTO struk (id_pesanan, waktu_cetak) VALUES (?, NOW())")
    ->execute([$id]);

header("Location: pesanan.php?msg=updated");
exit;
?>
