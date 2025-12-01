<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-red-100">
    <header class="bg-gradient-to-r from-amber-500 to-amber-600 text-darkpurple shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold">🛒 Keranjang Belanja</h1>
        </div>
    </header>

    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex gap-4">
                <a href="index.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Menu</a>
                <a href="cart.php" class="px-6 py-4 font-semibold text-amber-700 border-b-2 border-amber-700">Keranjang</a>
                <a href="pesanan.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Pesanan</a>
                <a href="reviews.php" class="px-6 py-4 font-semibold text-gray-600 hover:text-amber-700">Review</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 max-w-2xl">
        <div id="cart-items" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Cart items will be loaded by JavaScript -->
        </div>

        <div id="checkout-form" class="bg-white rounded-lg shadow-md p-6" style="display: none;">
            <h3 class="text-xl font-bold mb-4">Informasi Pelanggan</h3>
            <form action="proses_order.php" method="POST">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Nama Pelanggan *</label>
                    <input type="text" name="nama_pelanggan" required class="w-full border rounded px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Nomor Meja *</label>
                    <input type="text" name="meja" required class="w-full border rounded px-3 py-2" placeholder="Contoh: A1">
                </div>
                
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Metode Pembayaran *</label>
                    <select name="metode" required class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Debit Card</option>
                    </select>
                </div>

                <div class="mb-4" id="cash-input" style="display: none;">
                    <label class="block font-semibold mb-2">Uang Diterima</label>
                    <input type="number" name="uang_diterima" class="w-full border rounded px-3 py-2">
                </div>
                
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="total_harga" id="total_harga">
                
                <div class="flex gap-4">
                    <button type="button" onclick="document.getElementById('checkout-form').style.display='none'" 
                            class="flex-1 bg-gray-300 py-2 rounded hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-red-800 text-white py-2 rounded hover:bg-green-700">
                        Konfirmasi Pesanan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function getCart() {
            const cart = localStorage.getItem('cart');
            return cart ? JSON.parse(cart) : [];
        }

        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function updateQuantity(id, delta) {
            let cart = getCart();
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
            }
            saveCart(cart);
            displayCart();
        }

        function displayCart() {
            const cart = getCart();
            const container = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">Keranjang masih kosong</p>';
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                html += `
                    <div class="flex justify-between items-center py-4 border-b">
                        <div class="flex-1">
                            <h4 class="font-semibold">${item.name}</h4>
                            <p class="text-sm text-gray-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="updateQuantity(${item.id}, -1)" 
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300">-</button>
                            <span class="w-8 text-center font-semibold">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1)" 
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300">+</button>
                            <span class="w-32 text-right font-bold">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                `;
            });

            html += `
                <div class="pt-4">
                    <div class="flex justify-between items-center text-xl font-bold mb-4">
                        <span>Total:</span>
                        <span class="text-amber-700">Rp ${total.toLocaleString('id-ID')}</span>
                    </div>
                    <button onclick="showCheckout()" 
                            class="w-full bg-red-800 text-white py-3 rounded hover:bg-green-700 text-lg font-semibold">
                        Proses Checkout
                    </button>
                </div>
            `;

            container.innerHTML = html;
        }

        function showCheckout() {
            const cart = getCart();
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('cart_data').value = JSON.stringify(cart);
            document.getElementById('total_harga').value = total;
            document.getElementById('checkout-form').style.display = 'block';
            window.scrollTo(0, document.body.scrollHeight);
        }

        // Show/hide cash input
        document.querySelector('select[name="metode"]')?.addEventListener('change', function() {
            document.getElementById('cash-input').style.display = 
                this.value === 'cash' ? 'block' : 'none';
        });

        displayCart();
    </script>
</body>
</html>
