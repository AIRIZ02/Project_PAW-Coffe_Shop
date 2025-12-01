<?php 
session_start();
require_once 'config.php';

// Get all menu items
$stmt = $pdo->query("SELECT * FROM menu WHERE status = 'available' ORDER BY kategori, nama_menu");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* Badge warna kategori */
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .coffee { background: #FEF3C7; color: #92400E; }
        .non-coffee { background: #DBEAFE; color: #1E3A8A; }
        .food { background: #FEE2E2; color: #991B1B; }
        .snack { background: #DCFCE7; color: #065F46; }
    </style>
</head>

<body class="bg-yellow-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-amber-800 to-amber-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold">☕ Coffee Shop</h1>

                <a href="cart.php" class="relative">
                    <span class="text-2xl">🛒</span>
                    <span id="cart-count"
                          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                          0
                    </span>
                </a>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex gap-4">
                <a href="index.php" class="px-6 py-4 font-semibold text-amber-700 border-b-2 border-amber-700">Menu</a>
                <a href="cart.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Keranjang</a>
                <a href="orders.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Pesanan</a>
                <a href="reviews.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Review</a>
            </div>
        </div>
    </nav>

    <!-- Menu Content -->
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6">Daftar Menu</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($menuItems as $item): ?>

            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">

                <!-- Icon -->
                <div class="text-5xl text-center mb-4">
                    <?php 
                    $icons = [
                        'Coffee' => '☕',
                        'Non Coffee' => '🍵',
                        'Food' => '🍰',
                        'Snack' => '🥐'
                    ];

                    echo $icons[$item['kategori']] ?? '🍽️';
                    ?>
                </div>

                <!-- Nama Menu -->
                <h3 class="font-bold text-xl mb-2">
                    <?= htmlspecialchars($item['nama_menu']); ?>
                </h3>

                <!-- Deskripsi -->
                <p class="text-gray-600 mb-3">
                    <?= htmlspecialchars($item['deskripsi']); ?>
                </p>

                <!-- Kategori & Harga -->
                <div class="flex justify-between items-center mb-2">
                    <span class="category-badge <?= strtolower(str_replace(' ', '-', $item['kategori'])); ?>">
                        <?= htmlspecialchars($item['kategori']); ?>
                    </span>

                    <span class="text-xl font-bold text-amber-700">
                        Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                    </span>
                </div>

                <!-- Stok -->
                <div class="mb-4">
                    <?php if ($item['stok'] > 10): ?>
                        <span class="text-green-600 font-semibold">Stok: <?= $item['stok']; ?></span>
                    <?php elseif ($item['stok'] > 0): ?>
                        <span class="text-yellow-600 font-semibold">Stok: <?= $item['stok']; ?> (Menipis)</span>
                    <?php else: ?>
                        <span class="text-red-600 font-semibold">Stok Habis</span>
                    <?php endif; ?>
                </div>

                <!-- Tombol Cart -->
                <button 
                    onclick="addToCart(<?= $item['id_menu']; ?>, '<?= htmlspecialchars($item['nama_menu']); ?>', <?= $item['harga']; ?>)"
                    class="w-full px-6 py-2 rounded transition 
                        <?= $item['stok'] == 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 text-white'; ?>"
                    <?= $item['stok'] == 0 ? 'disabled' : ''; ?>
                >
                    <?= $item['stok'] == 0 ? 'Stok Habis' : 'Tambah ke Keranjang'; ?>
                </button>

            </div>

            <?php endforeach; ?>
        </div>
    </main>


    <script>
        function getCart() {
            const cart = localStorage.getItem('cart');
            return cart ? JSON.parse(cart) : [];
        }

        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function addToCart(id, name, price) {
            let cart = getCart();
            const item = cart.find(x => x.id === id);

            if (item) {
                item.quantity++;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }

            saveCart(cart);
            alert('Item berhasil ditambahkan ke keranjang!');
        }

        function updateCartCount() {
            const cart = getCart();
            const count = cart.reduce((t, i) => t + i.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        }

        updateCartCount();
    </script>

</body>
</html>
