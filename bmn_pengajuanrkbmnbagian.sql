-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 28, 2025 at 04:52 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bmn_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `bmn_pengajuanrkbmnbagian`
--

CREATE TABLE `bmn_pengajuanrkbmnbagian` (
  `id` int NOT NULL,
  `kode_jenis_pengajuan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_bagian_pengusul` int DEFAULT NULL,
  `id_biro_pengusul` int DEFAULT NULL,
  `id_bagian_pelaksana` int DEFAULT NULL,
  `id_biro_pelaksana` int DEFAULT NULL,
  `program` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `output` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_anggaran` int DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_kebmn` date DEFAULT NULL,
  `tanggal_keperencanaan` date DEFAULT NULL,
  `tanggal_final` date DEFAULT NULL,
  `tujuan_rencana` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `atr_nonatr` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `skema` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `harga_barang` double DEFAULT NULL,
  `total_anggaran` double DEFAULT NULL,
  `uraian_barang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dokumen_pendukung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `alasan_pengusul_bmn` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `alasan_koordinator_bmn` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `alasan_perencanaan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `akun_belanja` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_neraca` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kuantitas` int DEFAULT NULL,
  `tor_signed_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_verifikasi_tor` timestamp NULL DEFAULT NULL,
  `lampiran_signed_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_verifikasi_lampiran` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data untuk tabel `bmn_pengajuanrkbmnbagian` sekarang di-seed melalui Laravel Seeder.
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bmn_pengajuanrkbmnbagian`
--
ALTER TABLE `bmn_pengajuanrkbmnbagian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pengajuan_bagian_pengusul` (`id_bagian_pengusul`),
  ADD KEY `fk_pengajuan_bagian_pelaksana` (`id_bagian_pelaksana`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bmn_pengajuanrkbmnbagian`
--
ALTER TABLE `bmn_pengajuanrkbmnbagian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bmn_pengajuanrkbmnbagian`
--
ALTER TABLE `bmn_pengajuanrkbmnbagian`
  ADD CONSTRAINT `fk_pengajuan_bagian_pelaksana` FOREIGN KEY (`id_bagian_pelaksana`) REFERENCES `bagian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengajuan_bagian_pengusul` FOREIGN KEY (`id_bagian_pengusul`) REFERENCES `bagian` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
