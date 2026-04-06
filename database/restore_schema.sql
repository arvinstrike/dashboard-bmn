SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Table `bmn_dashboard`.`bagian`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bagian` (
  `id` VARCHAR(11) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  `iddeputi` INT NOT NULL,
  `idbiro` INT NOT NULL,
  `uraianbagian` VARCHAR(200) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  `status` SET('on', 'off') NOT NULL DEFAULT 'on',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`bmn_pemanfaatan`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bmn_pemanfaatan`;
CREATE TABLE IF NOT EXISTS `bmn_pemanfaatan` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pic_penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_hp_pic_penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `pic_administrasi_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_pic_administrasi_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `nama_mitra_penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `jenis_mitra` ENUM('Perusahaan', 'Yayasan', 'Koperasi', 'Perseorangan') NULL DEFAULT NULL,
  `jenis_usulan` ENUM('Perpanjangan', 'Usulan Baru') NULL DEFAULT NULL,
  `peruntukan_sewa` TEXT NULL DEFAULT NULL,
  `keterangan_uraian` TEXT NULL DEFAULT NULL,
  `nodin_konfirmasi_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `nodin_konfirmasi_tanggal` DATE NULL DEFAULT NULL,
  `nodin_konfirmasi_mitra_peruntukan` VARCHAR(255) NULL DEFAULT NULL,
  `nodin_konfirmasi_tanggal_berakhir_sewa` DATE NULL DEFAULT NULL,
  `surat_konfirmasi_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `surat_konfirmasi_tanggal` DATE NULL DEFAULT NULL,
  `surat_konfirmasi_tujuan` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_tujuan_surat` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_peruntukan` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_peruntukan_surat` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_nomor_perjanjian_lama` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_nomor_perjanjian_lama_dpr` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_nomor_perjanjian_lama_mitra` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_tanggal_berakhir` DATE NULL DEFAULT NULL,
  `surat_konfirmasi_tanggal_konfirmasi_terakhir` DATE NULL DEFAULT NULL,
  `surat_konfirmasi_kasub_nama_nomor` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_kasub_nama` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_kasub_nomor` TEXT NULL DEFAULT NULL,
  `surat_konfirmasi_lampiran` TEXT NULL DEFAULT NULL,
  `dokumen_surat_usulan_sewa` TEXT NULL DEFAULT NULL,
  `dokumen_npwp` TEXT NULL DEFAULT NULL,
  `dokumen_ktp_penandatangan` TEXT NULL DEFAULT NULL,
  `dokumen_nib` TEXT NULL DEFAULT NULL,
  `surat_pernyataan_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `surat_pernyataan_tanggal` DATE NULL DEFAULT NULL,
  `surat_pernyataan_kode_barang` VARCHAR(255) NULL DEFAULT NULL,
  `surat_pernyataan_nup` VARCHAR(255) NULL DEFAULT NULL,
  `surat_pernyataan_luasan_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `surat_pernyataan_lokasi_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `dokumen_psp` TEXT NULL DEFAULT NULL,
  `dokumen_kib` TEXT NULL DEFAULT NULL,
  `dokumen_usulan_ttd` TEXT NULL DEFAULT NULL,
  `dokumen_jadwal_penilaian` TEXT NULL DEFAULT NULL,
  `dokumen_basl` TEXT NULL DEFAULT NULL,
  `dokumen_persetujuan_kpknl` TEXT NULL DEFAULT NULL,
  `surat_invoice_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_nomor_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_tanggal` DATE NULL DEFAULT NULL,
  `surat_invoice_tanggal_faktur` DATE NULL DEFAULT NULL,
  `surat_invoice_tujuan` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_nomor_persetujuan` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_tanggal_persetujuan` DATE NULL DEFAULT NULL,
  `surat_invoice_periode_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_periode_mulai` DATE NULL DEFAULT NULL,
  `surat_invoice_periode_akhir` DATE NULL DEFAULT NULL,
  `surat_invoice_lama_periode` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_nominal` DECIMAL(18,2) NULL DEFAULT NULL,
  `surat_invoice_mitra` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_kasub` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_nama_kasubag_gelar` VARCHAR(255) NULL DEFAULT NULL,
  `surat_invoice_kasub_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `dokumen_kode_billing` TEXT NULL DEFAULT NULL,
  `daftar_bmn_nomor_surat` VARCHAR(255) NULL DEFAULT NULL,
  `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal` DATE NULL DEFAULT NULL,
  `usulan_pemanfaatan_sewa_permohonan_tarif_sewa_dokumen` TEXT NULL DEFAULT NULL,
  `is_complete` TINYINT(1) NOT NULL DEFAULT '0',
  `status_sewa` ENUM('draft', 'review', 'approved', 'active', 'completed', 'cancelled', 'expired') NOT NULL DEFAULT 'draft',
  `total_pendapatan_terealisasi` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT 'Total pendapatan yang sudah dibayar',
  `total_pendapatan_outstanding` DECIMAL(18,2) NOT NULL DEFAULT '0.00' COMMENT 'Total pendapatan yang belum dibayar (invoice terbit)',
  `periode_pembayaran_ke` INT NOT NULL DEFAULT '0' COMMENT 'Periode pembayaran ke berapa saat ini',
  `total_periode_pembayaran` INT NULL DEFAULT NULL COMMENT 'Total periode pembayaran yang harus dilakukan',
  `tanggal_aktivasi` DATE NULL DEFAULT NULL COMMENT 'Tanggal sewa mulai aktif/berlangsung',
  `tanggal_penyelesaian` DATE NULL DEFAULT NULL COMMENT 'Tanggal sewa selesai (actual)',
  `dapat_diperpanjang` TINYINT(1) NOT NULL DEFAULT '1',
  `batas_perpanjangan` DATE NULL DEFAULT NULL COMMENT 'Batas waktu untuk mengajukan perpanjangan',
  `kali_perpanjangan` INT NOT NULL DEFAULT '0' COMMENT 'Sudah diperpanjang berapa kali',
  `catatan_pembayaran` TEXT NULL DEFAULT NULL,
  `catatan_status` TEXT NULL DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `activated_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
  `cancelled_by` VARCHAR(255) NULL DEFAULT NULL,
  `cancelled_reason` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 50
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`bmn_pengajuanrkbmnbagian`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bmn_pengajuanrkbmnbagian` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis_pengajuan` VARCHAR(255) NULL DEFAULT NULL,
  `id_bagian_pengusul` VARCHAR(255) NULL DEFAULT NULL,
  `id_biro_pengusul` VARCHAR(255) NULL DEFAULT NULL,
  `id_bagian_pelaksana` VARCHAR(255) NULL DEFAULT NULL,
  `id_biro_pelaksana` VARCHAR(255) NULL DEFAULT NULL,
  `program` VARCHAR(255) NULL DEFAULT NULL,
  `kegiatan` VARCHAR(255) NULL DEFAULT NULL,
  `output` VARCHAR(255) NULL DEFAULT NULL,
  `kode_barang` VARCHAR(255) NULL DEFAULT NULL,
  `status` VARCHAR(255) NULL DEFAULT NULL,
  `tahun_anggaran` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_pengajuan` DATE NULL DEFAULT NULL,
  `tanggal_kebmn` DATE NULL DEFAULT NULL,
  `tanggal_keperencanaan` DATE NULL DEFAULT NULL,
  `tanggal_final` DATE NULL DEFAULT NULL,
  `tujuan_rencana` TEXT NULL DEFAULT NULL,
  `atr_nonatr` VARCHAR(255) NULL DEFAULT NULL,
  `skema` VARCHAR(255) NULL DEFAULT NULL,
  `harga_barang` DECIMAL(20,2) NULL DEFAULT NULL,
  `total_anggaran` DECIMAL(20,2) NULL DEFAULT NULL,
  `uraian_barang` TEXT NULL DEFAULT NULL,
  `keterangan` TEXT NULL DEFAULT NULL,
  `dokumen_pendukung` VARCHAR(255) NULL DEFAULT NULL,
  `alasan_pengusul_bmn` TEXT NULL DEFAULT NULL,
  `alasan_koordinator_bmn` TEXT NULL DEFAULT NULL,
  `alasan_perencanaan` TEXT NULL DEFAULT NULL,
  `akun_belanja` VARCHAR(255) NULL DEFAULT NULL,
  `akun_neraca` VARCHAR(255) NULL DEFAULT NULL,
  `kuantitas` INT NULL DEFAULT NULL,
  `tor_signed_path` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_verifikasi_tor` DATE NULL DEFAULT NULL,
  `lampiran_signed_path` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_verifikasi_lampiran` DATE NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`cache`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`cache_locks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`daftar_bmn`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `daftar_bmn` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` INT NOT NULL,
  `kode_barang` VARCHAR(100) NULL DEFAULT NULL,
  `nup` VARCHAR(50) NULL DEFAULT NULL,
  `jenis_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `luas_keseluruhan` DECIMAL(15,2) NULL DEFAULT NULL,
  `nilai_perolehan` DECIMAL(15,2) NULL DEFAULT NULL,
  `dicatat_di_simak` VARCHAR(50) NULL DEFAULT NULL,
  `objek_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `lokasi` VARCHAR(255) NULL DEFAULT NULL,
  `penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `peruntukan` VARCHAR(255) NULL DEFAULT NULL,
  `usulan_luas_sewa` DECIMAL(15,2) NULL DEFAULT NULL,
  `usulan_jangka_waktu` VARCHAR(100) NULL DEFAULT NULL,
  `usulan_periodesitas` VARCHAR(100) NULL DEFAULT NULL,
  `usulan_besaran_sewa` DECIMAL(15,2) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `daftar_bmn_pemanfaatan_id_index` (`pemanfaatan_id` ASC) VISIBLE,
  CONSTRAINT `daftar_bmn_pemanfaatan_id_foreign`
    FOREIGN KEY (`pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`failed_jobs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `failed_jobs_uuid_unique` (`uuid` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`job_batches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL DEFAULT NULL,
  `cancelled_at` INT NULL DEFAULT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`jobs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 33
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`nodin_berjenjang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nodin_berjenjang` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` INT NOT NULL,
  `nomor` VARCHAR(100) NOT NULL,
  `tanggal` DATE NULL DEFAULT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `mitra` VARCHAR(255) NULL DEFAULT NULL,
  `peruntukan` VARCHAR(255) NULL DEFAULT NULL,
  `nominal` DECIMAL(15,2) NULL DEFAULT NULL,
  `kasub_nama` VARCHAR(255) NULL DEFAULT NULL,
  `kasub_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `nodin_berjenjang_pemanfaatan_id_index` (`pemanfaatan_id` ASC) VISIBLE,
  INDEX `nodin_berjenjang_tanggal_mulai_index` (`tanggal_mulai` ASC) VISIBLE,
  CONSTRAINT `nodin_berjenjang_pemanfaatan_id_foreign`
    FOREIGN KEY (`pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`nodin_internals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nodin_internals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bmn_pemanfaatan_id` INT NOT NULL,
  `nomor_berjenjang_1` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_berjenjang_2` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_berjenjang_3` VARCHAR(255) NULL DEFAULT NULL,
  `perihal` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_surat` DATE NULL DEFAULT NULL,
  `nama_mitra` VARCHAR(255) NULL DEFAULT NULL,
  `objek_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_perjanjian_induk` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_persetujuan_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_persetujuan_sewa` DATE NULL DEFAULT NULL,
  `detail_persetujuan` TEXT NULL DEFAULT NULL,
  `judul_perjanjian` VARCHAR(255) NULL DEFAULT NULL,
  `nomor_perjanjian` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `nodin_internals_bmn_pemanfaatan_id_foreign` (`bmn_pemanfaatan_id` ASC) VISIBLE,
  CONSTRAINT `nodin_internals_bmn_pemanfaatan_id_foreign`
    FOREIGN KEY (`bmn_pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`nodin_persetujuan_kpknl`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nodin_persetujuan_kpknl` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` BIGINT UNSIGNED NOT NULL,
  `nomor_nodin` VARCHAR(255) NOT NULL,
  `tanggal_nodin` DATE NOT NULL,
  `perihal_nodin` VARCHAR(255) NULL DEFAULT NULL,
  `jangka_waktu` INT NULL DEFAULT NULL,
  `periode_sewa_mulai` DATE NULL DEFAULT NULL,
  `periode_sewa_selesai` DATE NULL DEFAULT NULL,
  `tujuan` VARCHAR(255) NULL DEFAULT NULL,
  `nominal` DECIMAL(15,2) NULL DEFAULT NULL,
  `mitra` VARCHAR(255) NULL DEFAULT NULL,
  `kasub` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`password_reset_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`perjanjian_sewa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `perjanjian_sewa` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` INT NOT NULL,
  `logo_penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_pertama_nama` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_pertama_kedudukan` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_pertama_keputusan_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_pertama_keputusan_tahun` VARCHAR(255) NULL DEFAULT NULL,
  `mitra_penyewa` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_kedua_nama` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_kedua_kedudukan` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_kedua_dasar_hukum` TEXT NULL DEFAULT NULL,
  `pihak_kedua_keputusan_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_kedua_keputusan_tanggal` DATE NULL DEFAULT NULL,
  `pihak_kedua_atas_nama` VARCHAR(255) NULL DEFAULT NULL,
  `pihak_kedua_alamat` TEXT NULL DEFAULT NULL,
  `pihak_kedua_kegiatan_usaha` VARCHAR(255) NULL DEFAULT NULL,
  `objek_luas` DOUBLE NULL DEFAULT NULL,
  `objek_satuan_luas` VARCHAR(255) NULL DEFAULT 'm2',
  `objek_letak` TEXT NULL DEFAULT NULL,
  `objek_gedung` VARCHAR(255) NULL DEFAULT NULL,
  `peruntukan` VARCHAR(255) NULL DEFAULT NULL,
  `nilai_sewa` DECIMAL(15,2) NULL DEFAULT NULL,
  `durasi_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `periode_mulai` DATE NULL DEFAULT NULL,
  `periode_selesai` DATE NULL DEFAULT NULL,
  `nomor_surat` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_surat` DATE NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `dokumen_perjanjian` VARCHAR(255) NULL DEFAULT NULL,
  `dokumen_bukti_bayar` VARCHAR(255) NULL DEFAULT NULL,
  `dokumen_bukti_tindak_lanjut_siman` VARCHAR(255) NULL DEFAULT NULL,
  `nilai_pendapatan_bukti_bayar` DECIMAL(15,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `pemanfaatan_id` (`pemanfaatan_id` ASC) VISIBLE,
  CONSTRAINT `perjanjian_sewa_ibfk_1`
    FOREIGN KEY (`pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id` ASC) VISIBLE,
  INDEX `sessions_last_activity_index` (`last_activity` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`surat_konfirmasi_perpanjangan_sewa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `surat_konfirmasi_perpanjangan_sewa` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` INT NOT NULL,
  `nomor` VARCHAR(255) NOT NULL,
  `tanggal` DATE NOT NULL,
  `tujuan_surat` TEXT NULL DEFAULT NULL,
  `peruntukan_surat` TEXT NULL DEFAULT NULL,
  `nomor_perjanjian_lama_dpr` TEXT NULL DEFAULT NULL,
  `nomor_perjanjian_lama_mitra` TEXT NULL DEFAULT NULL,
  `tanggal_berakhir` DATE NULL DEFAULT NULL,
  `tanggal_konfirmasi_terakhir` DATE NULL DEFAULT NULL,
  `kasub_nama` TEXT NULL DEFAULT NULL,
  `kasub_nomor` TEXT NULL DEFAULT NULL,
  `lampiran` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`surat_penyampaian_perjanjians`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `surat_penyampaian_perjanjians` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bmn_pemanfaatan_id` INT NOT NULL,
  `nomor_surat` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_surat` DATE NULL DEFAULT NULL,
  `nama_mitra` VARCHAR(255) NULL DEFAULT NULL,
  `alamat_mitra` VARCHAR(255) NULL DEFAULT NULL,
  `kota_mitra` VARCHAR(255) NULL DEFAULT NULL,
  `nama_usaha` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `surat_penyampaian_perjanjians_bmn_pemanfaatan_id_foreign` (`bmn_pemanfaatan_id` ASC) VISIBLE,
  CONSTRAINT `surat_penyampaian_perjanjians_bmn_pemanfaatan_id_foreign`
    FOREIGN KEY (`bmn_pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`surat_permohonan_ttds`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `surat_permohonan_ttds` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bmn_pemanfaatan_id` INT NOT NULL,
  `nomor_surat` VARCHAR(255) NULL DEFAULT NULL,
  `perihal` VARCHAR(255) NULL DEFAULT NULL,
  `tanggal_surat` DATE NULL DEFAULT NULL,
  `tujuan_surat` VARCHAR(255) NULL DEFAULT NULL,
  `tujuan_surat_bertempat` VARCHAR(255) NULL DEFAULT NULL,
  `nama_fasilitas_bmn` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `surat_permohonan_ttds_bmn_pemanfaatan_id_foreign` (`bmn_pemanfaatan_id` ASC) VISIBLE,
  CONSTRAINT `surat_permohonan_ttds_bmn_pemanfaatan_id_foreign`
    FOREIGN KEY (`bmn_pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`surat_usulan_kpknl_sptjm`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `surat_usulan_kpknl_sptjm` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pemanfaatan_id` INT NOT NULL,
  `surat_usulan_nomor` VARCHAR(255) NOT NULL,
  `surat_usulan_tanggal` DATE NOT NULL,
  `surat_usulan_hal` VARCHAR(255) NULL DEFAULT NULL,
  `surat_usulan_tujuan` VARCHAR(255) NULL DEFAULT NULL,
  `surat_usulan_isi` TEXT NULL DEFAULT NULL,
  `surat_usulan_peruntukan` VARCHAR(255) NULL DEFAULT NULL,
  `surat_usulan_tanggal_berakhir` DATE NULL DEFAULT NULL,
  `kasubag_nama` VARCHAR(255) NULL DEFAULT NULL,
  `kasubag_nomor` VARCHAR(255) NULL DEFAULT NULL,
  `sptjm_nomor` VARCHAR(255) NOT NULL,
  `sptjm_tanggal` DATE NULL DEFAULT NULL,
  `sptjm_kode_barang` VARCHAR(255) NOT NULL,
  `sptjm_nup` VARCHAR(255) NULL DEFAULT NULL,
  `sptjm_luasan_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `sptjm_lokasi_sewa` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `surat_usulan_kpknl_sptjm_pemanfaatan_id_index` (`pemanfaatan_id` ASC) VISIBLE,
  INDEX `surat_usulan_kpknl_sptjm_surat_usulan_tanggal_index` (`surat_usulan_tanggal` ASC) VISIBLE,
  CONSTRAINT `surat_usulan_kpknl_sptjm_pemanfaatan_id_foreign`
    FOREIGN KEY (`pemanfaatan_id`)
    REFERENCES `bmn_pemanfaatan` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `bmn_dashboard`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `users_email_unique` (`email` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
