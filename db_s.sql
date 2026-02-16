-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 08:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_s`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `tipe` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `nama_barang`, `merk`, `tipe`, `jumlah`) VALUES
(1, 2, 'Laptop', 'Asus', 'Vivobook 14', 0),
(2, 2, 'Laptop', 'ROG', 'Gaming', 0),
(3, 2, 'Handphone', 'Samsung', 'Galaxy S Series', 0),
(4, 4, 'Makanan', '-', '-', 0),
(5, 2, 'Laptop', 'Asus', 'Galaxy S Series', 11),
(6, 2, 'Laptop', 'Asus', 'Galaxy S Series', 1);

-- --------------------------------------------------------

--
-- Table structure for table `barangg`
--

CREATE TABLE `barangg` (
  `no_agenda` varchar(255) NOT NULL,
  `no_spb` varchar(255) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `dari` varchar(255) NOT NULL,
  `tujuan` varchar(255) NOT NULL,
  `keterangan` text NOT NULL,
  `partial` varchar(255) NOT NULL,
  `informasi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategoribarang`
--

CREATE TABLE `kategoribarang` (
  `id_kategori` int(11) NOT NULL,
  `namakategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoribarang`
--

INSERT INTO `kategoribarang` (`id_kategori`, `namakategori`) VALUES
(2, 'Elektronik'),
(4, 'konsumsi '),
(5, 'industri');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `id_permit` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `jenis` varchar(250) DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `id_permit`, `id_user`, `jenis`, `waktu`) VALUES
(7, 6, 1, 'IN', '2026-01-10 05:56:50'),
(8, 6, 1, 'OUT', '2026-01-12 05:09:50'),
(9, 7, 1, 'IN', '2026-01-12 07:34:05'),
(10, 7, 1, 'OUT', '2026-01-13 02:53:26'),
(11, 9, 1, 'IN', '2026-01-14 03:26:50'),
(12, 10, 1, 'IN', '2026-01-14 05:23:18'),
(13, 10, 1, 'OUT', '2026-01-14 05:24:02'),
(14, 9, 2, 'OUT', '2026-01-15 03:18:31'),
(15, 6, 1, 'IN', '2026-01-15 04:30:56'),
(16, 6, 1, 'OUT', '2026-01-15 04:34:00'),
(17, 10, 1, 'IN', '2026-01-15 04:35:00'),
(18, 10, 1, 'MASUK', '2026-01-15 04:45:23'),
(19, 8, 1, 'IN', '2026-01-15 04:55:12'),
(20, 8, 1, 'MASUK', '2026-01-15 04:57:23'),
(21, 8, 1, 'KELUAR', '2026-01-15 04:57:28'),
(22, 10, 1, 'KELUAR', '2026-01-15 05:05:49'),
(23, 6, 1, 'IN', '2026-01-15 05:38:02'),
(24, 6, 1, 'MASUK', '2026-01-15 05:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `permits`
--

CREATE TABLE `permits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keperluan` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','in','out') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permits`
--

INSERT INTO `permits` (`id`, `user_id`, `tanggal`, `keperluan`, `status`, `created_at`) VALUES
(6, 2, '2026-01-10', 'Izin Masuk Handphone', 'approved', '2026-01-10 04:20:00'),
(8, 2, '2026-01-13', 'Izin Masuk Laptop', 'rejected', '2026-01-13 02:54:37'),
(10, 2, '2026-01-14', 'Izin Masuk Handphone', 'out', '2026-01-14 05:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `permit_items`
--

CREATE TABLE `permit_items` (
  `id` int(11) NOT NULL,
  `id_permit` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nomor_seri` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permit_items`
--

INSERT INTO `permit_items` (`id`, `id_permit`, `id_barang`, `nomor_seri`, `jumlah`) VALUES
(6, 6, 3, 'SMS293011', 1),
(8, 8, 2, 'RPOL213273322', 1),
(10, 10, 3, 'SMS293011', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_petugas` varchar(250) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_petugas`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Jhon doe 1', 'Admin', '$2y$12$g3vkUczn3otjqYx3LZgAse1CDdpByhCB/3JtKPpLiwANv5z7RUliy', 'admin', '2026-01-06 03:00:15'),
(2, 'Jhon doe 2', 'Petugas', '$2y$12$Ad5Oqb/pfDlCbN3bovacC.1x8JTEEQpDdMjBoAXUgr4RJ/agBQAtu', 'petugas', '2026-01-06 03:00:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permits`
--
ALTER TABLE `permits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permit_items`
--
ALTER TABLE `permit_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_permit` (`id_permit`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategoribarang`
--
ALTER TABLE `kategoribarang`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permits`
--
ALTER TABLE `permits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permit_items`
--
ALTER TABLE `permit_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `permits`
--
ALTER TABLE `permits`
  ADD CONSTRAINT `permits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permit_items`
--
ALTER TABLE `permit_items`
  ADD CONSTRAINT `permit_items_ibfk_1` FOREIGN KEY (`id_permit`) REFERENCES `permits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permit_items_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
