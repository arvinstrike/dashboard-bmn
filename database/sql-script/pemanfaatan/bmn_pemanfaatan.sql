-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 25, 2025 at 07:57 AM
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
  `pic_penyewa` varchar(255) DEFAULT NULL,
  `nomor_hp_pic_penyewa` varchar(255) DEFAULT NULL,
  `pic_administrasi_bmn` varchar(255) DEFAULT NULL,
  `nomor_pic_administrasi_bmn` varchar(255) DEFAULT NULL,
  `nama_mitra_penyewa` varchar(255) DEFAULT NULL,
  `jenis_mitra` enum('Perusahaan','Yayasan','Koperasi','Perseorangan') DEFAULT NULL,
  `jenis_usulan` enum('Perpanjangan','Usulan Baru') DEFAULT NULL,
  `peruntukan_sewa` text,
  `keterangan_uraian` text,
  `nodin_konfirmasi_nomor` varchar(255) DEFAULT NULL,
  `nodin_konfirmasi_tanggal` date DEFAULT NULL,
  `nodin_konfirmasi_mitra_peruntukan` varchar(255) DEFAULT NULL,
  `nodin_konfirmasi_tanggal_berakhir_sewa` date DEFAULT NULL,
  `surat_konfirmasi_nomor` varchar(255) DEFAULT NULL,
  `surat_konfirmasi_tanggal` date DEFAULT NULL,
  `surat_konfirmasi_tujuan` text,
  `surat_konfirmasi_tujuan_surat` text,
  `surat_konfirmasi_peruntukan` text,
  `surat_konfirmasi_peruntukan_surat` text,
  `surat_konfirmasi_nomor_perjanjian_lama` text,
  `surat_konfirmasi_nomor_perjanjian_lama_dpr` text,
  `surat_konfirmasi_nomor_perjanjian_lama_mitra` text,
  `surat_konfirmasi_tanggal_berakhir` date DEFAULT NULL,
  `surat_konfirmasi_tanggal_konfirmasi_terakhir` date DEFAULT NULL,
  `surat_konfirmasi_kasub_nama_nomor` text,
  `surat_konfirmasi_kasub_nama` text,
  `surat_konfirmasi_kasub_nomor` text,
  `surat_konfirmasi_lampiran` text,
  `dokumen_surat_usulan_sewa` text,
  `dokumen_npwp` text,
  `dokumen_ktp_penandatangan` text,
  `dokumen_nib` text,
  `nodin_berjenjang_mitra` varchar(255) DEFAULT NULL,
  `nodin_berjenjang_peruntukan` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_nomor` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_tanggal` date DEFAULT NULL,
  `surat_usulan_kpknl_hal` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_tujuan` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_isi` text,
  `surat_usulan_kpknl_peruntukan` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_tanggal_berakhir` date DEFAULT NULL,
  `surat_usulan_kpknl_nama_kasubag` varchar(255) DEFAULT NULL,
  `surat_usulan_kpknl_nomor_kasubag` varchar(255) DEFAULT NULL,
  `sptjm_nomor` varchar(255) DEFAULT NULL,
  `sptjm_tanggal` date DEFAULT NULL,
  `sptjm_kode_barang` varchar(255) DEFAULT NULL,
  `sptjm_nup` varchar(255) DEFAULT NULL,
  `sptjm_luasan_sewa` varchar(255) DEFAULT NULL,
  `sptjm_lokasi_sewa` varchar(255) DEFAULT NULL,
  `surat_pernyataan_nomor` varchar(255) DEFAULT NULL,
  `surat_pernyataan_tanggal` date DEFAULT NULL,
  `surat_pernyataan_kode_barang` varchar(255) DEFAULT NULL,
  `surat_pernyataan_nup` varchar(255) DEFAULT NULL,
  `surat_pernyataan_luasan_sewa` varchar(255) DEFAULT NULL,
  `surat_pernyataan_lokasi_sewa` varchar(255) DEFAULT NULL,
  `daftar_bmn` json DEFAULT NULL,
  `dokumen_psp` text,
  `dokumen_kib` text,
  `dokumen_usulan_ttd` text,
  `dokumen_jadwal_penilaian` text,
  `dokumen_basl` text,
  `dokumen_persetujuan_kpknl` text,
  `nodin_persetujuan_kpknl_nomor` varchar(255) DEFAULT NULL,
  `nodin_persetujuan_kpknl_tanggal` date DEFAULT NULL,
  `nodin_persetujuan_kpknl_tujuan` varchar(255) DEFAULT NULL,
  `nodin_persetujuan_kpknl_nomor_persetujuan` varchar(255) DEFAULT NULL,
  `nodin_persetujuan_kpknl_tanggal_persetujuan` date DEFAULT NULL,
  `nodin_persetujuan_kpknl_periode_sewa` varchar(255) DEFAULT NULL,
  `nodin_persetujuan_kpknl_nominal` decimal(18,2) DEFAULT NULL,
  `nodin_persetujuan_kpknl_mitra` varchar(255) DEFAULT NULL,
  `nodin_persetujuan_kpknl_kasub` varchar(255) DEFAULT NULL,
  `surat_invoice_nomor` varchar(255) DEFAULT NULL,
  `surat_invoice_tanggal` date DEFAULT NULL,
  `surat_invoice_tujuan` varchar(255) DEFAULT NULL,
  `surat_invoice_nomor_persetujuan` varchar(255) DEFAULT NULL,
  `surat_invoice_tanggal_persetujuan` date DEFAULT NULL,
  `surat_invoice_periode_sewa` varchar(255) DEFAULT NULL,
  `surat_invoice_nominal` decimal(18,2) DEFAULT NULL,
  `surat_invoice_mitra` varchar(255) DEFAULT NULL,
  `surat_invoice_kasub` varchar(255) DEFAULT NULL,
  `dokumen_kode_billing` text,
  `dokumen_bukti_bayar` text,
  `perjanjian_logo_penyewa` text,
  `perjanjian_mitra` varchar(255) DEFAULT NULL,
  `perjanjian_peruntukan` varchar(255) DEFAULT NULL,
  `perjanjian_gedung` varchar(255) DEFAULT NULL,
  `perjanjian_hari_tanggal` varchar(255) DEFAULT NULL,
  `perjanjian_detail_pihak_kedua` text,
  `nodin_ttd_nomor` varchar(255) DEFAULT NULL,
  `nodin_ttd_tanggal` date DEFAULT NULL,
  `nodin_ttd_tujuan` varchar(255) DEFAULT NULL,
  `nodin_ttd_mitra` varchar(255) DEFAULT NULL,
  `nodin_ttd_judul_perjanjian` varchar(255) DEFAULT NULL,
  `nodin_internal_nomor` varchar(255) DEFAULT NULL,
  `nodin_internal_tanggal` date DEFAULT NULL,
  `nodin_internal_mitra` varchar(255) DEFAULT NULL,
  `nodin_internal_judul_perjanjian` varchar(255) DEFAULT NULL,
  `nodin_internal_nomor_perjanjian` varchar(255) DEFAULT NULL,
  `nodin_internal_detail_persetujuan` text,
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
  `keterangan` text,
  `is_complete` tinyint(1) NOT NULL DEFAULT '0',
  `status_sewa` enum('draft','review','approved','active','completed','cancelled','expired') NOT NULL DEFAULT 'draft',
  `total_pendapatan_terealisasi` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT 'Total pendapatan yang sudah dibayar',
  `total_pendapatan_outstanding` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT 'Total pendapatan yang belum dibayar (invoice terbit)',
  `periode_pembayaran_ke` int NOT NULL DEFAULT '0' COMMENT 'Periode pembayaran ke berapa saat ini',
  `total_periode_pembayaran` int DEFAULT NULL COMMENT 'Total periode pembayaran yang harus dilakukan',
  `tanggal_aktivasi` date DEFAULT NULL COMMENT 'Tanggal sewa mulai aktif/berlangsung',
  `tanggal_penyelesaian` date DEFAULT NULL COMMENT 'Tanggal sewa selesai (actual)',
  `dapat_diperpanjang` tinyint(1) NOT NULL DEFAULT '1',
  `batas_perpanjangan` date DEFAULT NULL COMMENT 'Batas waktu untuk mengajukan perpanjangan',
  `kali_perpanjangan` int NOT NULL DEFAULT '0' COMMENT 'Sudah diperpanjang berapa kali',
  `catatan_pembayaran` text,
  `catatan_status` text,
  `approved_at` timestamp NULL DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` varchar(255) DEFAULT NULL,
  `cancelled_reason` text,
  `nodin_berjenjang_nomor` varchar(100) DEFAULT NULL,
  `nodin_berjenjang_tanggal` date DEFAULT NULL,
  `nodin_berjenjang_tanggal_mulai` date DEFAULT NULL,
  `nodin_berjenjang_tanggal_selesai` date DEFAULT NULL,
  `nodin_berjenjang_nominal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bmn_pemanfaatan`
--

INSERT INTO `bmn_pemanfaatan` (`id`, `pic`, `pic_penyewa`, `nomor_hp_pic_penyewa`, `pic_administrasi_bmn`, `nomor_pic_administrasi_bmn`, `nama_mitra_penyewa`, `jenis_mitra`, `jenis_usulan`, `peruntukan_sewa`, `keterangan_uraian`, `nodin_konfirmasi_nomor`, `nodin_konfirmasi_tanggal`, `nodin_konfirmasi_mitra_peruntukan`, `nodin_konfirmasi_tanggal_berakhir_sewa`, `surat_konfirmasi_nomor`, `surat_konfirmasi_tanggal`, `surat_konfirmasi_tujuan`, `surat_konfirmasi_tujuan_surat`, `surat_konfirmasi_peruntukan`, `surat_konfirmasi_peruntukan_surat`, `surat_konfirmasi_nomor_perjanjian_lama`, `surat_konfirmasi_nomor_perjanjian_lama_dpr`, `surat_konfirmasi_nomor_perjanjian_lama_mitra`, `surat_konfirmasi_tanggal_berakhir`, `surat_konfirmasi_tanggal_konfirmasi_terakhir`, `surat_konfirmasi_kasub_nama_nomor`, `surat_konfirmasi_kasub_nama`, `surat_konfirmasi_kasub_nomor`, `surat_konfirmasi_lampiran`, `dokumen_surat_usulan_sewa`, `dokumen_npwp`, `dokumen_ktp_penandatangan`, `dokumen_nib`, `nodin_berjenjang_mitra`, `nodin_berjenjang_peruntukan`, `surat_usulan_kpknl_nomor`, `surat_usulan_kpknl_tanggal`, `surat_usulan_kpknl_hal`, `surat_usulan_kpknl_tujuan`, `surat_usulan_kpknl_isi`, `surat_usulan_kpknl_peruntukan`, `surat_usulan_kpknl_tanggal_berakhir`, `surat_usulan_kpknl_nama_kasubag`, `surat_usulan_kpknl_nomor_kasubag`, `sptjm_nomor`, `sptjm_tanggal`, `sptjm_kode_barang`, `sptjm_nup`, `sptjm_luasan_sewa`, `sptjm_lokasi_sewa`, `surat_pernyataan_nomor`, `surat_pernyataan_tanggal`, `surat_pernyataan_kode_barang`, `surat_pernyataan_nup`, `surat_pernyataan_luasan_sewa`, `surat_pernyataan_lokasi_sewa`, `daftar_bmn`, `dokumen_psp`, `dokumen_kib`, `dokumen_usulan_ttd`, `dokumen_jadwal_penilaian`, `dokumen_basl`, `dokumen_persetujuan_kpknl`, `nodin_persetujuan_kpknl_nomor`, `nodin_persetujuan_kpknl_tanggal`, `nodin_persetujuan_kpknl_tujuan`, `nodin_persetujuan_kpknl_nomor_persetujuan`, `nodin_persetujuan_kpknl_tanggal_persetujuan`, `nodin_persetujuan_kpknl_periode_sewa`, `nodin_persetujuan_kpknl_nominal`, `nodin_persetujuan_kpknl_mitra`, `nodin_persetujuan_kpknl_kasub`, `surat_invoice_nomor`, `surat_invoice_tanggal`, `surat_invoice_tujuan`, `surat_invoice_nomor_persetujuan`, `surat_invoice_tanggal_persetujuan`, `surat_invoice_periode_sewa`, `surat_invoice_nominal`, `surat_invoice_mitra`, `surat_invoice_kasub`, `dokumen_kode_billing`, `dokumen_bukti_bayar`, `perjanjian_logo_penyewa`, `perjanjian_mitra`, `perjanjian_peruntukan`, `perjanjian_gedung`, `perjanjian_hari_tanggal`, `perjanjian_detail_pihak_kedua`, `nodin_ttd_nomor`, `nodin_ttd_tanggal`, `nodin_ttd_tujuan`, `nodin_ttd_mitra`, `nodin_ttd_judul_perjanjian`, `nodin_internal_nomor`, `nodin_internal_tanggal`, `nodin_internal_mitra`, `nodin_internal_judul_perjanjian`, `nodin_internal_nomor_perjanjian`, `nodin_internal_detail_persetujuan`, `mitra`, `jenis_usaha`, `konfirmasi_permintaan_data_penyewa_nomor`, `konfirmasi_permintaan_data_penyewa_tanggal`, `konfirmasi_permintaan_data_penyewa_dokumen`, `konfirmasi_penyewa_nomor`, `konfirmasi_penyewa_tanggal`, `konfirmasi_penyewa_dokumen`, `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_nomor`, `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal`, `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_dokumen`, `penilaian`, `berita_acara_survei_lapangan`, `persetujuan_pemanfaatan_sewa_kpk_nomor`, `persetujuan_pemanfaatan_sewa_kpk_tanggal`, `persetujuan_pemanfaatan_sewa_kpk_dokumen`, `lokasi`, `uraian`, `pembayaran_ntpn`, `pembayaran_tanggal`, `pembayaran_dokumen`, `perjanjian_nomor`, `perjanjian_tanggal_penandatanganan`, `jangka_waktu_nilai`, `jangka_waktu_satuan`, `biaya_sewa`, `tanggal_mulai`, `tanggal_berakhir`, `total_biaya_sewa`, `dokumen_perjanjian`, `keterangan`, `is_complete`, `status_sewa`, `total_pendapatan_terealisasi`, `total_pendapatan_outstanding`, `periode_pembayaran_ke`, `total_periode_pembayaran`, `tanggal_aktivasi`, `tanggal_penyelesaian`, `dapat_diperpanjang`, `batas_perpanjangan`, `kali_perpanjangan`, `catatan_pembayaran`, `catatan_status`, `approved_at`, `activated_at`, `completed_at`, `cancelled_at`, `cancelled_by`, `cancelled_reason`, `nodin_berjenjang_nomor`, `nodin_berjenjang_tanggal`, `nodin_berjenjang_tanggal_mulai`, `nodin_berjenjang_tanggal_selesai`, `nodin_berjenjang_nominal`) VALUES
(40, NULL, 'Mirza', '08123456789', 'Mirza', '0812345678', 'Nana 2', 'Perusahaan', 'Perpanjangan', 'Untuk bangun gedung', 'Mandiri TBK', '1000', '2023-02-10', 'Mandiri', '2020-02-19', 'Bla bla', NULL, 'untuk demokrasi', NULL, 'bmn', NULL, 'bmn', NULL, NULL, '1202-10-20', NULL, NULL, NULL, NULL, NULL, 'uploads/pemanfaatan/1763051443_dokumen_surat_usulan_sewa_2164-Article Text-7333-1-10-20200630.pdf', 'uploads/pemanfaatan/1763051443_dokumen_npwp_2164-Article Text-7333-1-10-20200630.pdf', NULL, NULL, 'BCA', 'BMN', 'ba', NULL, 'PT', NULL, 'Mandiri 20 Maret 2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MJP200', '200', 'Tangerang', 'bla bla', '2020-02-20', 'spageti', '2020', '100', 'Bekasi', '\"[{\\\"kode_barang\\\":\\\"1000\\\",\\\"nup\\\":\\\"1000\\\",\\\"jenis_bmn\\\":\\\"Keuangan\\\",\\\"luas\\\":\\\"200\\\",\\\"nilai\\\":\\\"10000\\\",\\\"lokasi\\\":\\\"Mojokerto\\\",\\\"peruntukan\\\":\\\"2000\\\"}]\"', 'uploads/pemanfaatan/1763051443_dokumen_psp_391de16e-4b05-403b-9b48-279b733d3d62.jpg', 'uploads/pemanfaatan/1763051443_dokumen_kib_49-55-jurnal-tedi-purwanto.pdf', 'uploads/pemanfaatan/1763057224_dokumen_usulan_ttd_7d5d4a7d-0158-4ab9-a1c7-86466818b0b8.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/pemanfaatan/1763051443_dokumen_bukti_bayar_1713775834348wcnd69tz.png', NULL, NULL, 'Sewa', 'Parlemen', 'Senin 15 Januari 2025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Mojokerto\"]', NULL, NULL, NULL, NULL, '1001010', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/pemanfaatan/1763051443_dokumen_perjanjian_319709.319714.pdf', NULL, 0, 'draft', 0.00, 0.00, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, NULL, 'a', 'a', 'a', 'aa', 'ab', 'Perusahaan', 'Perpanjangan', 'a', 'a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'draft', 0.00, 0.00, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, NULL, 'ana', '0812345678', 'a', '0812345678', 'BCA', 'Perusahaan', 'Perpanjangan', 'a', 'a', NULL, NULL, NULL, '2025-11-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'BCA', 'Sewa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"[]\"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'draft', 0.00, 0.00, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1001/AA23/B/2025', '2025-11-20', NULL, NULL, 250000.00),
(45, NULL, 'Nurdin', '0812345678', 'Sergio', '0812345678', 'Bank Mandiri', 'Perusahaan', 'Perpanjangan', 'Kantor dan ATM', 'Gedung Nusantara II', NULL, NULL, NULL, NULL, 'AAA/222/2002', '2025-11-23', 'PT BANK MANDIRI', NULL, 'SEWA', NULL, 'AAA/2020/2015', 'AAA/19/2/2025', NULL, '2025-12-05', '2025-11-30', NULL, 'Maulana', '08139201224', NULL, NULL, NULL, NULL, NULL, 'BCA', 'Sewa', 'AAA/BB/CC', '2025-11-18', NULL, 'PT BANK MANDIRI TBK', NULL, 'untuk sewa menyewa', '2025-11-25', 'Wisnu', '081928102992', 'AAA/BB/CC/DD', '2025-11-25', 'aa', 'MJP200', '200', 'Tangerang', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'draft', 0.00, 0.00, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1001/AA23/B/2025', '2025-11-19', '2025-11-26', '2025-12-03', 100000.00);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
