-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 17, 2025 at 05:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffeeshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 1, 18000),
(2, 1, 4, 1, 15000),
(3, 1, 5, 1, 20000),
(4, 2, 2, 1, 22000),
(5, 2, 4, 1, 15000),
(6, 3, 1, 2, 36000),
(7, 3, 3, 1, 25000),
(8, 3, 4, 1, 15000),
(9, 4, 1, 1, 18000);

--
-- Triggers `detail_pesanan`
--
DELIMITER $$
CREATE TRIGGER `trg_after_delete_detail` AFTER DELETE ON `detail_pesanan` FOR EACH ROW BEGIN
    DECLARE tgl DATE;
    DECLARE harga_menu INT;
    DECLARE nama_menu VARCHAR(100);

    SET tgl = CURDATE();

    SELECT harga, nama_menu INTO harga_menu, nama_menu
    FROM menu WHERE id_menu = OLD.id_menu;

    UPDATE laporan
    SET 
        total_transaksi = total_transaksi - 1,
        total_pendapatan = total_pendapatan - OLD.subtotal,
        jumlah_menu_terjual = jumlah_menu_terjual - OLD.jumlah
    WHERE tanggal = tgl;

    -- Hapus hanya logikanya
    -- JSON remove tidak bisa spesifik object,
    -- biasanya diperbarui ulang via script harian.
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_insert_detail` AFTER INSERT ON `detail_pesanan` FOR EACH ROW BEGIN
    DECLARE tgl DATE;
    DECLARE menu_nama VARCHAR(100);
    DECLARE menu_harga INT;

    SET tgl = CURDATE();

    -- Ambil data menu
    SELECT nama_menu, harga INTO menu_nama, menu_harga
    FROM menu WHERE id_menu = NEW.id_menu;

    -- Jika laporan hari ini belum ada → buat baru
    INSERT INTO laporan (tanggal, total_transaksi, total_pendapatan, jumlah_menu_terjual, menu_terjual)
    SELECT tgl, 0, 0, 0, '[]'
    WHERE NOT EXISTS (SELECT 1 FROM laporan WHERE tanggal = tgl);

    -- Update angka transaksi & pendapatan
    UPDATE laporan
    SET total_transaksi = total_transaksi + 1,
        total_pendapatan = total_pendapatan + NEW.subtotal,
        jumlah_menu_terjual = jumlah_menu_terjual + NEW.jumlah
    WHERE tanggal = tgl;

    -- Tambahkan item JSON
    UPDATE laporan
    SET menu_terjual = JSON_ARRAY_APPEND(
        menu_terjual, '$',
        JSON_OBJECT(
            'id_menu', NEW.id_menu,
            'nama_menu', menu_nama,
            'jumlah', NEW.jumlah,
            'subtotal', NEW.subtotal
        )
    )
    WHERE tanggal = tgl;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_transaksi` int(11) DEFAULT 0,
  `total_pendapatan` int(11) DEFAULT 0,
  `jumlah_menu_terjual` int(11) DEFAULT 0,
  `menu_terjual` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tanggal`, `total_transaksi`, `total_pendapatan`, `jumlah_menu_terjual`, `menu_terjual`) VALUES
(1, '2025-11-17', 9, 184000, 10, '[{\"id_menu\": 1, \"nama_menu\": \"Americano\", \"jumlah\": 1, \"subtotal\": 18000}, {\"id_menu\": 4, \"nama_menu\": \"French Fries\", \"jumlah\": 1, \"subtotal\": 15000}, {\"id_menu\": 5, \"nama_menu\": \"Cheese Cake\", \"jumlah\": 1, \"subtotal\": 20000}, {\"id_menu\": 2, \"nama_menu\": \"Cappuccino\", \"jumlah\": 1, \"subtotal\": 22000}, {\"id_menu\": 4, \"nama_menu\": \"French Fries\", \"jumlah\": 1, \"subtotal\": 15000}, {\"id_menu\": 1, \"nama_menu\": \"Americano\", \"jumlah\": 2, \"subtotal\": 36000}, {\"id_menu\": 3, \"nama_menu\": \"Matcha Latte\", \"jumlah\": 1, \"subtotal\": 25000}, {\"id_menu\": 4, \"nama_menu\": \"French Fries\", \"jumlah\": 1, \"subtotal\": 15000}, {\"id_menu\": 1, \"nama_menu\": \"Americano\", \"jumlah\": 1, \"subtotal\": 18000}]');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kategori` enum('Coffee','Non Coffee','Food','Snack') NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `status` enum('Tersedia','Habis') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `kategori`, `harga`, `deskripsi`, `status`) VALUES
(1, 'Americano', 'Coffee', 18000, 'Kopi espresso + air panas', 'Tersedia'),
(2, 'Cappuccino', 'Coffee', 22000, 'Espresso + susu foam', 'Tersedia'),
(3, 'Matcha Latte', 'Non Coffee', 25000, 'Matcha premium + susu', 'Tersedia'),
(4, 'French Fries', 'Food', 15000, 'Kentang goreng crispy', 'Tersedia'),
(5, 'Cheese Cake', 'Snack', 20000, 'Kue keju lembut', 'Habis');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode` enum('Cash','QRIS','Debit') NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `uang_diterima` int(11) DEFAULT NULL,
  `kembalian` int(11) DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `metode`, `total_bayar`, `uang_diterima`, `kembalian`, `tanggal_bayar`) VALUES
(1, 1, 'Cash', 40000, 50000, 10000, '2025-11-17 09:30:00'),
(2, 2, 'QRIS', 37000, 37000, 0, '2025-11-17 10:10:00'),
(3, 3, 'Debit', 62000, 62000, 0, '2025-11-17 11:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `tanggal_pesanan` datetime DEFAULT current_timestamp(),
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `meja` varchar(10) DEFAULT NULL,
  `total_harga` int(11) DEFAULT 0,
  `status` enum('Pending','Diproses','Selesai','Dibatalkan') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `tanggal_pesanan`, `nama_pelanggan`, `meja`, `total_harga`, `status`) VALUES
(1, '2025-11-17 09:15:00', 'Rani', 'A1', 40000, 'Selesai'),
(2, '2025-11-17 10:05:00', 'Bagas', 'A2', 37000, 'Selesai'),
(3, '2025-11-17 11:25:00', 'Dewi', '', 62000, 'Selesai'),
(4, '2025-11-17 12:10:00', 'Yudi', 'B3', 18000, 'Dibatalkan');

-- --------------------------------------------------------

--
-- Table structure for table `stok`
--

CREATE TABLE `stok` (
  `id_stok` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(30) DEFAULT NULL,
  `update_terakhir` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok`
--

INSERT INTO `stok` (`id_stok`, `id_menu`, `jumlah`, `satuan`, `update_terakhir`) VALUES
(1, 1, 40, 'Cup', '2025-11-17 08:00:00'),
(2, 2, 35, 'Cup', '2025-11-17 08:00:00'),
(3, 3, 20, 'Cup', '2025-11-17 08:00:00'),
(4, 4, 50, 'Porsi', '2025-11-17 08:00:00'),
(5, 5, 0, 'Slice', '2025-11-17 08:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_pesanan` (`id_pesanan`),
  ADD KEY `fk_detail_menu` (`id_menu`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pesanan_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `fk_stok_menu` (`id_menu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stok`
--
ALTER TABLE `stok`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pesanan_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `fk_stok_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
