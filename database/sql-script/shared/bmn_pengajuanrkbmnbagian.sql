-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 18, 2025 at 03:52 AM
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
  `id_bagian_pengusul` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_biro_pengusul` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_bagian_pelaksana` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_biro_pelaksana` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
('21', 'R3-001', '669', '664', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '4010201003', 'Diajukan Ke Unit Pelaksana', 2027, '2025-05-07', NULL, NULL, NULL, 'Perluasan', 'Non ATR', 'Pembelian', 3000000000, 6000000000, 'Uraian rumah negara', 'Keterangan rumah negara', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 2, 'public/bmn_rkbmn_tor_esign/tor_38_signed.pdf', '2025-04-11 22:51:59', NULL, NULL, '2025-03-18 03:06:39', '2025-05-07 02:33:44'),
('22', 'R4-001', '669', '664', '749', '688', 'WA | Program Dukungan Manajemen', '5801', 'AAA', '3020101001', 'Diajukan Ke Unit Pelaksana', 2027, '2025-05-20', NULL, NULL, NULL, 'Khusus Lainnya', 'Non ATR', 'Pembelian', 800000000, 1600000000, 'Uraian kendaraan jabatan', 'keterangan kendaraan jabatan', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_39_signed.pdf', '2025-04-11 22:15:58', NULL, NULL, '2025-03-18 04:15:17', '2025-05-20 08:52:37'),
('23', 'R5-001', '669', '664', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Pembelian', 600000000, 1200000000, 'Uraian Kendaraan Operasional', 'Keterangan Kendaraan Operasional', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_40_signed.pdf', '2025-04-10 21:12:46', NULL, NULL, '2025-03-18 04:38:28', '2025-04-10 07:12:46'),
('24', 'R1-002', '669', '664', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104004', 'Draft', 2027, NULL, NULL, NULL, NULL, 'Perluasan', NULL, 'Pembelian', 1000000000, 4000000000, 'Uraian Spesifikasi Kantor 2', 'Keterangan Kantor 2', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 4, 'public/bmn_rkbmn_tor_esign/tor_41_signed.pdf', '2025-04-10 21:55:54', NULL, NULL, '2025-03-19 07:41:39', '2025-04-10 07:55:54'),
('25', 'R1-003', '0', '732', '678', '677', '|', NULL, NULL, NULL, 'Draft', 2027, NULL, NULL, NULL, NULL, 'Perluasan', NULL, 'Pembelian', 123123, 1477476, '123', '123', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 12, NULL, NULL, NULL, NULL, '2025-04-08 07:19:06', '2025-04-08 07:19:06'),
('26', 'R1-003', '669', '664', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104019', 'Draft', 2027, NULL, NULL, NULL, NULL, 'Tambah Unit', NULL, 'Pembelian', 150000000, 1800000000, 'ayam goreng', 'ayam goreng', NULL, NULL, NULL, NULL, '531111 - Belanja Modal Tanah', '131111 - Tanah', 12, NULL, NULL, NULL, NULL, '2025-05-07 02:32:58', '2025-05-07 02:32:58'),
('27', 'R5-002', '669', '664', '749', '688', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020102003', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Sewa', 350000000, 700000000, 'Toyota Kijang 2025', 'Kendaraan Operasioneal untuk Bagian Administrasi BMN', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, NULL, NULL, NULL, NULL, '2025-05-21 02:05:02', '2025-05-21 02:05:02'),
('28', 'R4-002', '669', '664', '749', '688', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020101001', 'Draft', 2027, NULL, NULL, NULL, NULL, NULL, NULL, 'Pembelian', 500000000, 1000000000, 'Toyota Camry', 'Kendaraan Jabatan untuk Eselon I', NULL, NULL, NULL, NULL, '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, NULL, NULL, NULL, NULL, '2025-05-21 07:13:23', '2025-05-21 07:13:23'),
('29', 'R1-004', '0', '677', NULL, NULL, NULL, NULL, NULL, '2010104020', 'Draft', 2027, '2025-10-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5000000000, 'Pengadaan Gedung Kantor Biro Pengelolaan Bangunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-29 11:07:05', '2025-10-29 11:07:05'),
('30', 'R5-003', '0', '732', NULL, NULL, NULL, NULL, NULL, '3020102004', 'Draft', 2027, '2025-10-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 800000000, 'Kendaraan Operasional Biro Perencanaan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-29 11:07:05', '2025-10-29 11:07:05'),
('31', 'R1-005', '669', '664', '678', '677', 'Program Peningkatan Sarana dan Prasarana', 'Pengadaan Fasilitas Kantor', 'Kantor', '2010104001', 'approved', 2027, '2025-06-15', '2025-07-01', '2025-07-10', '2025-07-15', 'Peningkatan Fasilitas', 'Non ATR', 'Pembelian', 2500000000, 5000000000, 'Pengadaan gedung kantor baru untuk bagian administrasi', 'Fasilitas kantor yang representatif', 'dokumen/gedung_kantor.pdf', 'Diperlukan untuk efisiensi kerja', 'Disetujui sebagai prioritas utama', 'Sudah sesuai dengan rencana kerja', '531114 - Belanja Modal Peralatan dan Mesin', '131114 - Peralatan dan Mesin', 1, 'public/bmn_rkbmn_tor_esign/tor_42_signed.pdf', '2025-06-20 03:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_42_signed.pdf', '2025-06-25 03:00:00', '2025-06-10 01:00:00', '2025-07-15 08:00:00'),
('32', 'R4-003', '669', '664', '693', '688', 'Program Dukungan Operasional', 'Pengadaan Kendaraan Jabatan', 'Kendaraan', '3020101002', 'approved', 2027, '2025-06-20', '2025-07-05', '2025-07-12', '2025-07-18', 'Pengadaan Kendaraan', 'Non ATR', 'Pembelian', 1200000000, 2400000000, 'Pengadaan kendaraan jabatan untuk kepala bagian', 'Kendaraan untuk kegiatan dinas', 'dokumen/kendaraan_jabatan.pdf', 'Diperlukan untuk mobilitas kerja', 'Disetujui sesuai aturan', 'Sudah tercantum dalam RKAKL', '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_43_signed.pdf', '2025-06-25 02:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_43_signed.pdf', '2025-07-02 02:00:00', '2025-06-15 02:00:00', '2025-07-18 09:00:00'),
('33', 'R3-002', '0', '677', '678', '677', 'Program Pengembangan Infrastruktur', 'Pemeliharaan Bangunan', 'Bangunan', '4010202001', 'approved', 2027, '2025-06-25', '2025-07-08', '2025-07-15', '2025-07-20', 'Perbaikan Struktural', 'Non ATR', 'Pemeliharaan', 1800000000, 3600000000, 'Pemeliharaan gedung utama biro pengelolaan bangunan', 'Perbaikan kerusakan struktural', 'dokumen/pemeliharaan_bangunan.pdf', 'Diperlukan untuk keselamatan kerja', 'Disetujui sebagai kebutuhan mendesak', 'Telah diverifikasi oleh tim teknis', '531111 - Belanja Modal Tanah', '131111 - Tanah', 1, 'public/bmn_rkbmn_tor_esign/tor_44_signed.pdf', '2025-07-01 04:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_44_signed.pdf', '2025-07-05 04:00:00', '2025-06-20 03:00:00', '2025-07-20 10:00:00'),
('34', 'R5-004', '0', '688', '749', '688', 'Program Dukungan Fasilitas', 'Pengadaan Peralatan', 'Peralatan', '3020102005', 'approved', 2027, '2025-07-01', '2025-07-12', '2025-07-18', '2025-07-25', 'Pengadaan Fasilitas', 'Non ATR', 'Pembelian', 900000000, 1800000000, 'Pengadaan peralatan kantor untuk biro umum', 'Peralatan pendukung operasional', 'dokumen/peralatan_kantor.pdf', 'Diperlukan untuk efisiensi kerja', 'Disetujui sesuai kebutuhan', 'Sudah masuk dalam DIPA', '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 5, 'public/bmn_rkbmn_tor_esign/tor_45_signed.pdf', '2025-07-05 05:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_45_signed.pdf', '2025-07-10 05:00:00', '2025-06-25 04:00:00', '2025-07-25 11:00:00'),
('35', 'R6-001', '749', '688', '693', '688', 'Program Pengadaan Fungsional', 'Pengadaan Kendaraan', 'Kendaraan', '3020103001', 'rejected', 2027, '2025-07-05', '2025-07-15', '2025-07-20', NULL, 'Pengadaan Kendaraan Fungsional', 'Non ATR', 'Pembelian', 2000000000, 4000000000, 'Pengadaan kendaraan fungsional untuk kegiatan lapangan', 'Kendaraan operasional khusus', 'dokumen/kendaraan_fungsional.pdf', 'Diperlukan untuk kegiatan fungsional', 'Ditolak karena spesifikasi tidak sesuai', 'Perlu revisi spesifikasi teknis', '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 2, 'public/bmn_rkbmn_tor_esign/tor_46_signed.pdf', '2025-07-10 06:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_46_signed.pdf', '2025-07-12 06:00:00', '2025-07-01 05:00:00', '2025-07-22 12:00:00'),
('36', 'R1-006', '640', '639', '678', '677', 'Program SDM', 'Pengembangan Karier ASN', 'Pelatihan', '2010201001', 'rejected', 2027, '2025-07-10', '2025-07-18', '2025-07-25', NULL, 'Pelatihan dan Pengembangan', 'Non ATR', 'Belanja Barang/Jasa', 500000000, 1000000000, 'Pengadaan fasilitas pelatihan dan pengembangan karier ASN', 'Fasilitas pelatihan internal', 'dokumen/fasilitas_pelatihan.pdf', 'Diperlukan untuk peningkatan kapasitas SDM', 'Ditolak karena anggaran melebihi pagu', 'Perlu penyesuaian anggaran', '533111 - Belanja Barang dan Jasa', '132111 - Peralatan dan Mesin', 1, 'public/bmn_rkbmn_tor_esign/tor_47_signed.pdf', '2025-07-12 07:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_47_signed.pdf', '2025-07-15 07:00:00', '2025-07-05 06:00:00', '2025-07-26 13:00:00'),
('37', 'R2-001', '0', '732', '678', '677', 'Program Perencanaan', 'Evaluasi Kinerja Organisasi', 'Laporan', '1010101001', 'rejected', 2027, '2025-07-15', '2025-07-22', '2025-07-28', NULL, 'Evaluasi dan Pelaporan', 'ATR', 'Belanja Barang/Jasa', 300000000, 600000000, 'Pengadaan sistem evaluasi kinerja organisasi', 'Sistem manajemen kinerja', 'dokumen/sistem_kinerja.pdf', 'Diperlukan untuk pengelolaan kinerja', 'Ditolak karena sudah ada di sistem lain', 'Perlu koordinasi dengan unit terkait', '533111 - Belanja Barang dan Jasa', '133111 - Akumulasi Penyusutan Aktiva Tetap', 1, 'public/bmn_rkbmn_tor_esign/tor_48_signed.pdf', '2025-07-18 08:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_48_signed.pdf', '2025-07-20 08:00:00', '2025-07-10 07:00:00', '2025-07-29 14:00:00'),
('38', 'R4-004', '0', '664', '693', '688', 'Program Keuangan', 'Pengadaan Sistem Akuntansi', 'Software', '3020201001', 'rejected', 2027, '2025-07-20', '2025-07-25', '2025-08-01', NULL, 'Pengadaan Software', 'Non ATR', 'Pengadaan Langsung', 1500000000, 3000000000, 'Pengadaan software akuntansi dan keuangan terintegrasi', 'Sistem akuntansi terpusat', 'dokumen/software_akuntansi.pdf', 'Diperlukan untuk efisiensi pengelolaan keuangan', 'Ditolak karena belum ada HPS', 'Perlu penyusunan HPS terlebih dahulu', '532111 - Belanja Modal Peralatan dan Mesin', '132111 - Peralatan dan Mesin', 1, 'public/bmn_rkbmn_tor_esign/tor_49_signed.pdf', '2025-07-22 09:00:00', 'public/bmn_rkbmn_lampiran_esign/lampiran_49_signed.pdf', '2025-07-25 09:00:00', '2025-07-15 08:00:00', '2025-08-02 15:00:00'),
('DUMMY01', 'R1-001', '503', '502', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104004', 'approved', 2022, '2022-01-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2500000000, 'Gedung Kantor Baru', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY02', 'R4-001', '506', '502', '749', '688', 'WA | Program Dukungan Manajemen', '5801', 'AAA', '3020101001', 'rejected', 2022, '2022-02-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 700000000, 'Kendaraan Jabatan Eselon II', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY03', 'R5-001', '509', '502', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'completed', 2022, '2022-03-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500000000, 'Bus Operasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY04', 'R3-001', '512', '502', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '4010201003', 'pending', 2023, '2023-01-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4000000000, 'Rumah Dinas Tipe A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY05', 'R1-002', '515', '502', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104019', 'approved', 2023, '2023-02-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3200000000, 'Renovasi Gedung Arsip', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY06', 'R5-002', '518', '502', '749', '688', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020102003', 'in_progress', 2023, '2023-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 800000000, 'Mobil Operasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY07', 'R4-002', '521', '502', '749', '688', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '3020101001', 'draft', 2024, '2024-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 900000000, 'Kendaraan Jabatan Menteri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY08', 'R1-003', '524', '502', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104020', 'approved', 2024, '2024-03-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5000000000, 'Pembangunan Gedung Parkir', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY09', 'R3-002', '527', '502', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '4010201003', 'completed', 2024, '2024-05-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2800000000, 'Rumah Dinas Tipe B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY10', 'R5-003', '530', '502', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'rejected', 2024, '2024-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 450000000, 'Motor Operasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY11', 'R1-004', '669', '664', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104004', 'pending', 2025, '2025-02-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7500000000, 'Gedung Pusat Data', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY12', 'R4-003', '669', '664', '749', '688', 'WA | Program Dukungan Manajemen', '5801', 'AAA', '3020101001', 'in_progress', 2025, '2025-03-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1200000000, 'Kendaraan Jabatan Eselon I', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY13', 'R5-004', '693', '688', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'draft', 2025, '2025-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 600000000, 'Minibus Operasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY14', 'R1-005', '713', '702', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104019', 'approved', 2025, '2025-05-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500000000, 'Renovasi Ruang Rapat', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY15', 'R3-003', '721', '716', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '4010201003', 'completed', 2022, '2022-07-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1800000000, 'Rumah Dinas Tipe C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY16', 'R5-005', '725', '500', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020102003', 'pending', 2023, '2023-08-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 950000000, 'Mobil Listrik Operasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY17', 'R1-006', '729', '500', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104020', 'in_progress', 2024, '2024-09-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4200000000, 'Pembangunan Gedung Server', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY18', 'R4-004', '730', '500', '749', '688', 'WA | Program Dukungan Manajemen', '5801', 'AAA', '3020101001', 'draft', 2025, '2025-10-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1100000000, 'Kendaraan Jabatan Direktur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY19', 'R5-006', '738', '501', '749', '688', 'WA | Program Dukungan Manajemen', '5784', 'EBA', '3020199999', 'approved', 2022, '2022-11-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 300000000, 'Motor Patroli', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50'),
('DUMMY20', 'R1-007', '741', '501', '678', '677', 'WA | Program Dukungan Manajemen', '6575', 'EBA', '2010104004', 'completed', 2023, '2023-12-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6000000000, 'Gedung Arsip Nasional', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-04 03:14:50', '2025-11-04 03:14:50');

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
