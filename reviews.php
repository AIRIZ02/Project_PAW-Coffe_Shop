<?php
require_once 'config.php';

// insert review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        INSERT INTO reviews (nama_pelanggan, rating, komentar, tanggal_review)
        VALUES (?, ?, ?, NOW())
    ");

    $stmt->execute([
        $_POST['nama_pelanggan'],
        $_POST['rating'],
        $_POST['komentar']
    ]);

    header("Location: reviews.php?success=1");
    exit;
}

$reviews = $pdo->query("SELECT * FROM reviews ORDER BY tanggal_review DESC")
               ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Review Pelanggan - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-yellow-100">

    <!-- HEADER -->
    <header class="bg-gradient-to-r from-amber-800 to-amber-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold">⭐ Review Pelanggan</h1>
        </div>
    </header>

    <!-- NAV -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex gap-4">
                <a href="index.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Menu</a>
                <a href="cart.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Keranjang</a>
                <a href="orders.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Pesanan</a>
                <a href="reviews.php" class="px-6 py-4 font-semibold text-amber-700 border-b-2 border-amber-700">Review</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-10 max-w-3xl">

        <!-- ALERT -->
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-200 text-green-800 border border-green-400 rounded">
                Review berhasil dikirim!
            </div>
        <?php endif; ?>

        <!-- FORM REVIEW -->
        <div class="bg-white p-6 shadow-md rounded-lg mb-10">
            <h2 class="text-2xl font-bold mb-4 text-amber-700">Tinggalkan Review</h2>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Nama:</label>
                    <input type="text" name="nama_pelanggan" required class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Rating (1 - 5):</label>
                    <input type="number" name="rating" min="1" max="5" required class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Komentar:</label>
                    <textarea name="komentar" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <button type="submit" 
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                    Kirim Review
                </button>
            </form>
        </div>

        <!-- LIST REVIEW -->
        <h2 class="text-2xl font-bold mb-4 text-amber-700">Review Pelanggan</h2>

        <?php foreach ($reviews as $r): ?>
            <div class="bg-white border-l-4 border-amber-600 p-5 shadow mb-4 rounded">
                <div class="flex justify-between">
                    <strong class="text-lg text-amber-700"><?= htmlspecialchars($r['nama_pelanggan']) ?></strong>
                    <span class="text-yellow-600 font-bold">⭐ <?= $r['rating'] ?></span>
                </div>
                <p class="mt-2 text-gray-700"><?= nl2br(htmlspecialchars($r['komentar'])) ?></p>
                <small class="text-gray-500 block mt-2">
                    <?= date("d M Y H:i", strtotime($r['tanggal_review'])) ?>
                </small>
            </div>
        <?php endforeach; ?>
        <a class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition" href="index.php">Kembali ke menu</a>
    </main>
</body>
</html>
