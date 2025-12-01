<?php
require_once "config.php";

// QUERY PESANAN + TOTAL HARGA
$query = $pdo->query("SELECT 
        p.id_pesanan,
        p.nama_pelanggan,
        p.meja,
        p.jenis_order,
        p.tanggal_pesanan,
        p.status,
        total_harga
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON dp.id_pesanan = p.id_pesanan
    LEFT JOIN menu m ON m.id_menu = dp.id_menu
    GROUP BY 
        p.id_pesanan,
        p.nama_pelanggan,
        p.meja,
        p.jenis_order,
        p.tanggal_pesanan,
        p.status
    ORDER BY p.id_pesanan DESC
");

$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Pesanan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        thead {
            background: #4a4a88;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: #f1f1f1;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: white;
        }

        .pending { background: #ffa726; }
        .diproses { background: #29b6f6; }
        .selesai { background: #66bb6a; }
        .dibatalkan { background: #ef5350; }

        .btn {
            padding: 6px 12px;
            background: #4a4a88;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn:hover {
            background: #3b3b72;
        }
    </style>

</head>

<body>

<h2>📋 Data Pesanan</h2>

<table>
<thead>
<tr>
  <th>No</th>
  <th>Nama Pelanggan</th>
  <th>Meja</th>
  <th>Jenis Order</th>
  <th>Tanggal</th>
  <th>Total</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php foreach ($orders as $o): ?>
<tr>
    <td><?= $o['id_pesanan'] ?></td>
    <td><?= $o['nama_pelanggan'] ?></td>
    <td><?= $o['meja'] ?></td>
    <td><?= ucfirst($o['jenis_order']) ?></td>
    <td><?= $o['tanggal_pesanan'] ?></td>

    <!-- TOTAL HARGA OTOMATIS DARI DETAIL PESANAN -->
    <td><b style="color:#4a4a88;">Rp <?= number_format($o['total_harga']) ?></b></td>

    <td>
        <?php
            $statusClass = [
                'pending' => 'pending',
                'diproses' => 'diproses',
                'selesai' => 'selesai',
                'dibatalkan' => 'dibatalkan'
            ];
        ?>
        <span class="badge <?= $statusClass[$o['status']] ?>">
            <?= ucfirst($o['status']) ?>
        </span>
    </td>

    <td>
        <a class="btn" href="struk.php?id=<?= $o['id_pesanan'] ?>">Struk</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

</table>

</body>
</html>
