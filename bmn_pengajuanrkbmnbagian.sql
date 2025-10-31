-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 31, 2025 at 07:22 AM
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
  `id` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_jenis_pengajuan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_bagian_pengusul` int DEFAULT NULL,
  `id_biro_pengusul` int DEFAULT NULL,
  `id_bagian_pelaksana` int DEFAULT NULL,
  `id_biro_pelaksana` int DEFAULT NULL,
  `program` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `output` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_anggaran` int DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `tanggal_kebmn` date DEFAULT NULL,
  `tanggal_keperencanaan` date DEFAULT NULL,
  `tanggal_final` date DEFAULT NULL,
  `tujuan_rencana` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atr_nonatr` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skema` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_barang` double DEFAULT NULL,
  `total_anggaran` double DEFAULT NULL,
  `uraian_barang` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `dokumen_pendukung` text COLLATE utf8mb4_unicode_ci,
  `alasan_pengusul_bmn` text COLLATE utf8mb4_unicode_ci,
  `alasan_koordinator_bmn` text COLLATE utf8mb4_unicode_ci,
  `alasan_perencanaan` text COLLATE utf8mb4_unicode_ci,
  `akun_belanja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akun_neraca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kuantitas` int DEFAULT NULL,
  `tor_signed_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_verifikasi_tor` timestamp NULL DEFAULT NULL,
  `lampiran_signed_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_verifikasi_lampiran` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bmn_pengajuanrkbmnbagian`
--

INSERT INTO `bmn_pengajuanrkbmnbagian` (`id`, `kode_jenis_pengajuan`, `id_bagian_pengusul`, `id_biro_pengusul`, `id_bagian_pelaksana`, `id_biro_pelaksana`, `program`, `kegiatan`, `output`, `kode_barang`, `status`, `tahun_anggaran`, `tanggal_pengajuan`, `tanggal_kebmn`, `tanggal_keperencanaan`, `tanggal_final`, `tujuan_rencana`, `atr_nonatr`, `skema`, `harga_barang`, `total_anggaran`, `uraian_barang`, `keterangan`, `dokumen_pendukung`, `alasan_pengusul_bmn`, `alasan_koordinator_bmn`, `alasan_perencanaan`, `akun_belanja`, `akun_neraca`, `kuantitas`, `tor_signed_path`, `tanggal_verifikasi_tor`, `lampiran_signed_path`, `tanggal_verifikasi_lampiran`, `created_at`, `updated_at`) VALUES
('21', 'R3-001', 669, 664, 678, 677, 'WA | Program Dukungan Manajemen', '6575', 'EBA', '4010201003', 'Diajukan Ke Unit Pelaksana', 2027, '2025-05-07', NULL, NULL, NULL, 'Perluasan', 'Non ATR', 'Pembelian', 3000000000, 6000000000, 'Uraian rumah negara', 'Keterangan rumah negara', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 2, 'public/bmn_rkbmn_tor_esign/tor_38_signed.pdf', '2025-04-11 22:51:59', NULL, NULL, '2025-03-18 03:06:39', '2025-05-07 02:33:44'),
('22', 'R4-001', 669, 664, 749, 688, 'WA | Program Dukungan Manajemen', '5801', 'AAA', '3020101001', 'Diajukan Ke Unit Pelaksana', 2027, '2025-05-20', NULL, NULL, NULL, 'Khusus Lainnya', 'Non ATR', 'Pembelian', 800000000, 1600000000, 'Uraian kendaraan jabatan', 'keterangan kendaraan jabatan', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_39_signed.pdf', '2025-04-11 22:15:58', NULL, NULL, '2025-03-18 04:15:17', '2025-05-20 08:52:37'),
('23', 'R5-001', 669, 664, 749, 688, 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Pembelian', 600000000, 1200000000, 'Uraian Kendaraan Operasional', 'Keterangan Kendaraan Operasional', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_40_signed.pdf', '2025-04-10 21:12:46', NULL, NULL, '2025-03-18 04:38:28', '2025-04-10 07:12:46'),
('24', 'R1-002', 669, 664, 678, 677, 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104004', 'Draft', 2027, NULL, NULL, NULL, NULL, 'Perluasan', NULL, 'Pembelian', 1000000000, 4000000000, 'Uraian Spesifikasi Kantor 2', 'Keterangan Kantor 2', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 4, 'public/bmn_rkbmn_tor_esign/tor_41_signed.pdf', '2025-04-10 21:55:54', NULL, NULL, '2025-03-19 07:41:39', '2025-04-10 07:55:54'),
('25', 'R1-003', 0, 732, 678, 677, '|', NULL, NULL, NULL, 'Draft', 2027, NULL, NULL, NULL, NULL, 'Perluasan', NULL, 'Pembelian', 123123, 1477476, '123', '123', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 12, NULL, NULL, NULL, NULL, '2025-04-08 07:19:06', '2025-04-08 07:19:06'),
('26', 'R1-003', 669, 664, 678, 677, 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104019', 'Draft', 2027, NULL, NULL, NULL, NULL, 'Tambah Unit', NULL, 'Pembelian', 150000000, 1800000000, 'ayam goreng', 'ayam goreng', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 12, NULL, NULL, NULL, NULL, '2025-05-07 02:32:58', '2025-05-07 02:32:58'),
('27', 'R5-002', 669, 664, 749, 688, 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020102003', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Sewa', 350000000, 700000000, 'Toyota Kijang 2025', 'Kendaraan Operasioneal untuk Bagian Administrasi BMN', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, NULL, NULL, NULL, NULL, '2025-05-21 02:05:02', '2025-05-21 02:05:02'),
('28', 'R4-002', 669, 664, 749, 688, 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020101001', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Pembelian', 500000000, 1000000000, 'Toyota Camry', 'Kendaraan Jabatan untuk Eselon I', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, NULL, NULL, NULL, NULL, '2025-05-21 07:13:23', '2025-05-21 07:13:23'),
('29', 'R1-004', 0, 677, NULL, NULL, NULL, NULL, NULL, '2010104020', 'Draft', 2027, '2025-10-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5000000000, 'Pengadaan Gedung Kantor Biro Pengelolaan Bangunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-29 11:07:05', '2025-10-29 11:07:05'),
('30', 'R5-003', 0, 732, NULL, NULL, NULL, NULL, NULL, '3020102004', 'Draft', 2027, '2025-10-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 800000000, 'Kendaraan Operasional Biro Perencanaan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-29 11:07:05', '2025-10-29 11:07:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bmn_pengajuanrkbmnbagian`
--
ALTER TABLE `bmn_pengajuanrkbmnbagian`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
