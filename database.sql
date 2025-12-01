DROP DATABASE IF EXISTS coffeeshop;
CREATE DATABASE coffeeshop;
USE coffeeshop;

-- ==========================
-- TABEL MENU
-- ==========================
CREATE TABLE menu (
    id_menu INT PRIMARY KEY AUTO_INCREMENT,
    nama_menu VARCHAR(100) NOT NULL,
    kategori ENUM('Coffee', 'Non Coffee', 'Food', 'Snack') NOT NULL,
    stok INT(11) DEFAULT 0,
    harga DECIMAL(10,2) NOT NULL,
    deskripsi TEXT,
    status ENUM('available', 'unavailable') DEFAULT 'available'
);

-- ==========================
-- TABEL PESANAN
-- ==========================
CREATE TABLE pesanan (
    id_pesanan INT PRIMARY KEY AUTO_INCREMENT,
    tanggal_pesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pelanggan VARCHAR(100) NOT NULL,
    meja VARCHAR(10) NOT NULL,
    jenis_order ENUM('dine in', 'take away') DEFAULT 'dine in',
    total_harga DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending'
);

-- ==========================
-- TABEL DETAIL PESANAN
-- ==========================
CREATE TABLE detail_pesanan (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT NOT NULL,
    id_menu INT NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu)
);

-- ==========================
-- TABEL PEMBAYARAN
-- ==========================
CREATE TABLE pembayaran (
    id_pembayaran INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT NOT NULL,
    metode ENUM('cash', 'qris', 'debit') NOT NULL,
    total_bayar DECIMAL(10,2) NOT NULL,
    uang_diterima DECIMAL(10,2),
    kembalian DECIMAL(10,2),
    tanggal_bayar DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE
);

-- ==========================
-- TABEL STOK
-- ==========================
CREATE TABLE stok (
    id_stok INT PRIMARY KEY AUTO_INCREMENT,
    id_menu INT NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(50),
    update_terakhir DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_menu) REFERENCES menu(id_menu)
);

-- ==========================
-- TABEL LAPORAN
-- ==========================
CREATE TABLE laporan (
    id_laporan INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    total_transaksi INT DEFAULT 0,
    total_pendapatan DECIMAL(10,2) DEFAULT 0,
    menu_terlaris TEXT
);

-- ==========================
-- TABEL REVIEW
-- ==========================
CREATE TABLE reviews (
    id_review INT PRIMARY KEY AUTO_INCREMENT,
    nama_pelanggan VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    komentar TEXT NOT NULL,
    tanggal_review DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- TABEL STRUK (DIPERBAIKI)
-- ==========================
CREATE TABLE struk (
    id_struk INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    dibayar DECIMAL(10,2) NOT NULL,
    kembali DECIMAL(10,2) NOT NULL,
    waktu_cetak DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE
);

-- ==========================
-- INSERT DATA MENU
-- ==========================
INSERT INTO menu (nama_menu, kategori, stok, harga, deskripsi) VALUES
('Espresso', 'Coffee', 6, 25000, 'Strong Italian coffee'),
('Cappuccino', 'Coffee', 10, 35000, 'Coffee with steamed milk'),
('Latte', 'Coffee', 8, 38000, 'Smooth coffee with milk'),
('Green Tea', 'Non Coffee', 7, 20000, 'Fresh green tea'),
('Chocolate Cake', 'Food', 9, 45000, 'Rich chocolate cake'),
('Croissant', 'Snack', 11, 28000, 'Buttery croissant');
