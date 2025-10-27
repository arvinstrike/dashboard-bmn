-- digitall.bmn_pengajuan_bangunan_perkantoran definition

CREATE TABLE bmn_pengajuan_bangunan_perkantoran (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  klasifikasi_bangunan varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  klasifikasi_pejabat varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  luas_ruang_kerja double DEFAULT NULL,
  luas_ruang_tamu double DEFAULT NULL,
  luas_ruang_rapat double DEFAULT NULL,
  luas_ruang_tunggu double DEFAULT NULL,
  luas_ruang_istirahat double DEFAULT NULL,
  luas_ruang_sekretaris double DEFAULT NULL,
  luas_ruang_simpan double DEFAULT NULL,
  luas_ruang_toilet double DEFAULT NULL,
  luas_ruang_rapat_utama double DEFAULT NULL,
  lokasi text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuan_kendaraan_fungsional definition

CREATE TABLE bmn_pengajuan_kendaraan_fungsional (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  jenis_kendaraan varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuan_kendaraan_jabatan definition

CREATE TABLE bmn_pengajuan_kendaraan_jabatan (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  pejabat_pemakai varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  spesifikasi varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuan_kendaraan_operasional definition

CREATE TABLE bmn_pengajuan_kendaraan_operasional (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  jenis_satker varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  jenis_kendaraan varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuan_pemeliharaan definition

CREATE TABLE bmn_pengajuan_pemeliharaan (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  kode_pengajuan varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kode unik pengajuan (auto generated)',
  kode_barang_nup varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kode barang + NUP dari SIMAN',
  kode_barang varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kode barang dari SIMAN',
  nup varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'NUP dari SIMAN',
  nama_barang text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama barang dari SIMAN',
  kondisi varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kondisi barang dari SIMAN',
  kuantitas decimal(10,2) DEFAULT '1.00' COMMENT 'Kuantitas barang',
  kategori varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kategori aset (bangunan, kendaraan, dll)',
  table_source varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tabel sumber dari SIMAN',
  luas_bangunan decimal(15,2) DEFAULT NULL COMMENT 'Luas bangunan (m2)',
  luas_dasar_bangunan decimal(15,2) DEFAULT NULL COMMENT 'Luas dasar bangunan (m2)',
  jumlah_lantai int DEFAULT NULL COMMENT 'Jumlah lantai bangunan',
  nilai_perolehan decimal(20,2) NOT NULL COMMENT 'Nilai/biaya pemeliharaan yang diajukan',
  no_psp varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor PSP dari SIMAN',
  gambar varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama file gambar barang',
  catatan_pengaju text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari pengaju',
  catatan_pelaksana text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari pelaksana/verifikator',
  id_bagian_pengusul int NOT NULL COMMENT 'ID bagian yang mengusulkan',
  id_bagian_pelaksana int NOT NULL COMMENT 'ID bagian pelaksana pengadaan',
  status_barang enum('draft','diajukan','approved','rejected','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Status pengajuan',
  tanggal_pengajuan datetime DEFAULT NULL COMMENT 'Tanggal submit pengajuan',
  tanggal_approved datetime DEFAULT NULL COMMENT 'Tanggal di-approve',
  tanggal_rejected datetime DEFAULT NULL COMMENT 'Tanggal di-reject',
  tanggal_completed datetime DEFAULT NULL COMMENT 'Tanggal selesai',
  created_by int DEFAULT NULL COMMENT 'User yang membuat',
  updated_by int DEFAULT NULL COMMENT 'User yang update terakhir',
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_kode_barang_nup (kode_barang_nup),
  KEY idx_kode_pengajuan (kode_pengajuan),
  KEY idx_status_barang (status_barang),
  KEY idx_bagian_pengusul (id_bagian_pengusul),
  KEY idx_bagian_pelaksana (id_bagian_pelaksana),
  KEY idx_created_at (created_at),
  KEY idx_kode_barang (kode_barang),
  KEY idx_nup (nup),
  KEY idx_tanggal_pengajuan (tanggal_pengajuan),
  KEY idx_kategori (kategori)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel pengajuan pemeliharaan BMN';


-- digitall.bmn_pengajuan_pengelolaan definition

CREATE TABLE bmn_pengajuan_pengelolaan (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  kode_barang varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  nup int NOT NULL,
  kategori_barang varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  nama_barang varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  kondisi_awal varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  nilai_perolehan decimal(15,2) DEFAULT NULL,
  no_psp varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  tahun_perolehan varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  ruangan_id int DEFAULT NULL,
  bagian_id varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  biro_id int DEFAULT NULL,
  deputi_id int DEFAULT NULL,
  jenis_pengelolaan enum('Pemeliharaan','Pemindahtanganan','Penghapusan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pemeliharaan',
  deskripsi_usulan text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  estimasi_biaya decimal(15,2) DEFAULT NULL,
  prioritas enum('Rendah','Sedang','Tinggi','Darurat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Sedang',
  tanggal_pengajuan date NOT NULL,
  status_pengajuan enum('Draft','Pending','Disetujui','Ditolak','Dalam Proses','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  catatan text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  diajukan_oleh int NOT NULL,
  disetujui_oleh int DEFAULT NULL,
  tanggal_persetujuan date DEFAULT NULL,
  dokumen_pendukung varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  alasan_pemindahtanganan text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  unit_tujuan varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  penanggung_jawab_baru varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  alasan_penghapusan text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  metode_penghapusan enum('Dijual','Dihibahkan','Dimusnahkan','Ditukar') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  nilai_sisa decimal(15,2) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_kode_barang_nup (kode_barang,nup),
  KEY idx_status_pengajuan (status_pengajuan),
  KEY idx_jenis_pengelolaan (jenis_pengelolaan),
  KEY idx_prioritas (prioritas),
  KEY idx_tanggal_pengajuan (tanggal_pengajuan),
  KEY idx_diajukan_oleh (diajukan_oleh)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuan_rumah_negara definition

CREATE TABLE bmn_pengajuan_rumah_negara (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  peruntukan_pejabat varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  klasifikasi_pejabat varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  lokasi varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  tujuan_rumah varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  jenis_rumah varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  jenis_pengadaan_rumah varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  luas_tanah int DEFAULT NULL,
  jumlah_ruang_kerja int DEFAULT NULL,
  jumlah_ruang_duduk int DEFAULT NULL,
  jumlah_ruang_fungsional int DEFAULT NULL,
  jumlah_ruang_makan int DEFAULT NULL,
  jumlah_ruang_tidur int DEFAULT NULL,
  jumlah_ruang_wc int DEFAULT NULL,
  jumlah_dapur int DEFAULT NULL,
  jumlah_gudang int DEFAULT NULL,
  jumlah_garasi int DEFAULT NULL,
  jumlah_ruang_tidur_pramuwisma int DEFAULT NULL,
  jumlah_ruang_cuci int DEFAULT NULL,
  jumlah_kamar_mandi_pramuwisma int DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- digitall.bmn_pengajuanrkbmnbagian definition

CREATE TABLE bmn_pengajuanrkbmnbagian (
  id int NOT NULL AUTO_INCREMENT,
  kode_jenis_pengajuan varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  id_bagian_pengusul int DEFAULT NULL,
  id_biro_pengusul int DEFAULT NULL,
  id_bagian_pelaksana int DEFAULT NULL,
  id_biro_pelaksana int DEFAULT NULL,
  program varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  kegiatan varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  output varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  kode_barang varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  status varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  tahun_anggaran int DEFAULT NULL,
  tanggal_pengajuan date DEFAULT NULL,
  tanggal_kebmn date DEFAULT NULL,
  tanggal_keperencanaan date DEFAULT NULL,
  tanggal_final date DEFAULT NULL,
  tujuan_rencana varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  atr_nonatr varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  skema varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  harga_barang double DEFAULT NULL,
  total_anggaran double DEFAULT NULL,
  uraian_barang text COLLATE utf8mb4_general_ci,
  keterangan text COLLATE utf8mb4_general_ci,
  dokumen_pendukung text COLLATE utf8mb4_general_ci,
  alasan_pengusul_bmn text COLLATE utf8mb4_general_ci,
  alasan_koordinator_bmn text COLLATE utf8mb4_general_ci,
  alasan_perencanaan text COLLATE utf8mb4_general_ci,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  akun_belanja varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  akun_neraca varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  kuantitas int DEFAULT NULL,
  tor_signed_path varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  tanggal_verifikasi_tor timestamp NULL DEFAULT NULL,
  lampiran_signed_path varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  tanggal_verifikasi_lampiran timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;