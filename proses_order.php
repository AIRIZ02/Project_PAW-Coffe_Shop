<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO pesanan (nama_pelanggan, meja, total_harga) VALUES (?, ?, ?)");
        $stmt->execute([
          
            $_POST['nama_pelanggan'],
            $_POST['meja'],
            $_POST['total_harga']
        ]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order details
        $cart = json_decode($_POST['cart'], true);
        $stmt = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        
        foreach ($cart as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $subtotal]);
        }
        
        // Insert payment
        $kembalian = 0;
        if ($_POST['metode'] === 'cash' && !empty($_POST['uang_diterima'])) {
            $kembalian = $_POST['uang_diterima'] - $_POST['total_harga'];
        }
        
        $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, metode, total_bayar, uang_diterima, kembalian) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $orderId,
            $_POST['metode'],
            $_POST['total_harga'],
            $_POST['uang_diterima'] ?? null,
            $kembalian
        ]);
        
        $pdo->commit();
        
        echo "<script>
            localStorage.removeItem('cart');
            alert('Pesanan berhasil! Nomor pesanan: $orderId');
            window.location.href = 'cart.php';
        </script>";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<?php
require_once 'config.php';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $stmt = $pdo->prepare("INSERT INTO reviews (nama_pelanggan, rating, komentar) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nama_pelanggan'], $_POST['rating'], $_POST['komentar']]);
    header('Location: reviews.php?success=1');
    exit;
}

// Get all reviews
$stmt = $pdo->query("SELECT * FROM reviews ORDER BY tanggal_review DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-gradient-to-r from-amber-800 to-amber-600 text-blue shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold">⭐ Review Pelanggan</h1>
        </div>
    </header>

    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex gap-4">
                <a href="index.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Menu</a>
                <a href="cart.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Keranjang</a>
                <a href="orders.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Pesanan</a>
                <a href="reviews.php" class="px-6 py-4 font-semibold text-amber-700 border-b-2 border-amber-700">Review</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            Terima kasih atas review Anda!
        </div>
        <?php endif; ?>

        <!-- Review Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold mb-4">Tinggalkan Review</h3>
            <form method="POST">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Nama Anda *</label>
                    <input type="text" name="nama_pelanggan" required class="w-full border rounded px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Rating *</label>
                    <div class="flex gap-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="<?php echo $i, $i === 5 ?> <?php'checked' : ' '; ?>" class="hidden peer">
                            <span class="text-3xl peer-checked:text-blue-400 text-gray-300">⭐</span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Komentar *</label>
                    <textarea name="komentar" required rows="4" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                
                <button type="submit" name="submit_review" class="bg-green-600 text-Blue px-6 py-2 rounded hover:bg-amber-700">
                    Kirim Review
                </button>
            </form>
        </div>

        <!-- Reviews List -->
        <h3 class="text-xl font-bold mb-4">Review Pelanggan</h3>
        <div class="space-y-4">
            <?php foreach ($reviews as $review): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-lg"><?php echo htmlspecialchars($review['nama_pelanggan']); ?></h4>
                        <p class="text-sm text-gray-500"><?php echo date('d M Y H:i', strtotime($review['tanggal_review'])); ?></p>
                    </div>
                    <div class="flex">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">⭐</span>
                        <?php endfor; ?>
                    </div>
                </div>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($review['komentar'])); ?></p>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($reviews)): ?>
            <div class="text-center py-12 bg-white rounded-lg">
                <p class="text-gray-500">Belum ada review</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>