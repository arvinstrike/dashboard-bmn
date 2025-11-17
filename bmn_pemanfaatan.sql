-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2025 at 03:46 AM
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
-- Table structure for table `bmn_pemanfaatan`
--

CREATE TABLE `bmn_pemanfaatan` (
  `id` int NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `mitra` varchar(255) DEFAULT NULL,
  `jenis_usaha` varchar(255) DEFAULT NULL,
  `konfirmasi_permintaan_data_penyewa_nomor` varchar(255) DEFAULT NULL,
  `konfirmasi_permintaan_data_penyewa_tanggal` date DEFAULT NULL,
  `konfirmasi_permintaan_data_penyewa_dokumen` text,
  `konfirmasi_penyewa_nomor` varchar(255) DEFAULT NULL,
  `konfirmasi_penyewa_tanggal` date DEFAULT NULL,
  `konfirmasi_penyewa_dokumen` text,
  `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_nomor` varchar(255) DEFAULT NULL,
  `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal` date DEFAULT NULL,
  `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_dokumen` text,
  `penilaian` text,
  `berita_acara_survei_lapangan` text,
  `persetujuan_pemanfaatan_sewa_kpk_nomor` varchar(255) DEFAULT NULL,
  `persetujuan_pemanfaatan_sewa_kpk_tanggal` date DEFAULT NULL,
  `persetujuan_pemanfaatan_sewa_kpk_dokumen` text,
  `lokasi` varchar(255) DEFAULT NULL,
  `uraian` text,
  `pembayaran_ntpn` varchar(255) DEFAULT NULL,
  `pembayaran_tanggal` date DEFAULT NULL,
  `pembayaran_dokumen` text,
  `perjanjian_nomor` varchar(255) DEFAULT NULL,
  `perjanjian_tanggal_penandatanganan` date DEFAULT NULL,
  `jangka_waktu_nilai` int DEFAULT NULL,
  `jangka_waktu_satuan` varchar(50) DEFAULT NULL,
  `biaya_sewa` decimal(18,2) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `total_biaya_sewa` decimal(18,2) DEFAULT NULL,
  `dokumen_perjanjian` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bmn_pemanfaatan`
--
ALTER TABLE `bmn_pemanfaatan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bmn_pemanfaatan`
--
ALTER TABLE `bmn_pemanfaatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
