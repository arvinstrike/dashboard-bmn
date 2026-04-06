# Dokumentasi Schema Database Pemanfaatan BMN

## 📋 Daftar Isi

1. [Ringkasan](#ringkasan)
2. [Arsitektur Database](#arsitektur-database)
3. [Tabel Parent: bmn_pemanfaatan](#tabel-parent-bmn_pemanfaatan)
4. [Tabel Child: surat_konfirmasi_perpanjangan_sewa](#tabel-child-surat_konfirmasi_perpanjangan_sewa)
5. [Tabel Child: nodin_berjenjang](#tabel-child-nodin_berjenjang)
6. [Tabel Child: surat_usulan_kpknl_sptjm](#tabel-child-surat_usulan_kpknl_sptjm)
7. [Relationships & Foreign Keys](#relationships--foreign-keys)
8. [Workflow & Auto-Populate Kasub](#workflow--auto-populate-kasub)
9. [Dual-Write Pattern](#dual-write-pattern)
10. [Entity Relationship Diagram](#entity-relationship-diagram)

---

## 🎯 Ringkasan

Sistem Pemanfaatan BMN telah direfactor dari **monolithic table** (1 tabel besar dengan 187+ kolom) menjadi **parent-child architecture** dengan tabel-tabel terpisah untuk setiap jenis dokumen.

### Perubahan Utama

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| **Struktur** | 1 tabel besar (`bmn_pemanfaatan`) | 1 parent + 3 child tables (dan akan bertambah) |
| **Kolom** | 187+ kolom dalam 1 tabel | Dipisah sesuai jenis dokumen |
| **Data Integrity** | Tidak ada relasi eksplisit | Foreign keys dengan CASCADE DELETE |
| **Scalability** | Sulit menambah dokumen baru | Mudah menambah tabel baru per dokumen |
| **Kasub Reference** | Manual copy-paste | Auto-populate dari dokumen sebelumnya |

### Tabel yang Sudah Direfactor (Fase 1)

1. ✅ **Surat Konfirmasi Perpanjangan Sewa** → `surat_konfirmasi_perpanjangan_sewa`
2. ✅ **Nodin Berjenjang** → `nodin_berjenjang`
3. ✅ **Surat Usulan KPKNL & SPTJM** → `surat_usulan_kpknl_sptjm`

### Tabel yang Masih di Parent (Akan Direfactor Kemudian)

- Surat Konfirmasi (legacy)
- Nodin Konfirmasi
- Surat Pernyataan
- Daftar BMN
- Nodin Persetujuan KPKNL
- Surat Invoice
- Perjanjian Sewa
- Nodin TTD
- Nodin Internal

---

## 🏗️ Arsitektur Database

### Konsep Parent-Child Relationship

```
┌─────────────────────────────────────┐
│     bmn_pemanfaatan (PARENT)        │
│  ┌───────────────────────────────┐  │
│  │ id (INT PRIMARY KEY)          │  │
│  │ kode_lokasi                   │  │
│  │ jenis_pemanfaatan             │  │
│  │ mitra                         │  │
│  │ ...                           │  │
│  │ (masih ada 187+ kolom untuk   │  │
│  │  backward compatibility)      │  │
│  └───────────────────────────────┘  │
└─────────────────────────────────────┘
            │
            │ 1:1 relationships
            ├────────────────────────────────────────┐
            │                                        │
            ▼                                        ▼
┌───────────────────────────┐         ┌─────────────────────────────┐
│ surat_konfirmasi_         │         │  nodin_berjenjang           │
│ perpanjangan_sewa (CHILD) │         │  (CHILD)                    │
│                           │         │                             │
│ • pemanfaatan_id (FK)     │───────▶ │ • pemanfaatan_id (FK)       │
│ • nomor                   │  kasub  │ • nomor                     │
│ • tanggal                 │  flow   │ • tanggal_mulai/selesai     │
│ • kasub_nama              │         │ • kasub_nama (auto-filled)  │
│ • kasub_nomor             │         │ • kasub_nomor (auto-filled) │
│ • ...                     │         │ • ...                       │
└───────────────────────────┘         └─────────────────────────────┘
                                                    │
                                                    │ kasub flow
                                                    ▼
                                      ┌─────────────────────────────┐
                                      │ surat_usulan_kpknl_sptjm    │
                                      │ (CHILD)                     │
                                      │                             │
                                      │ • pemanfaatan_id (FK)       │
                                      │ • surat_usulan_nomor        │
                                      │ • sptjm_nomor               │
                                      │ • kasubag_nama (auto)       │
                                      │ • kasubag_nomor (auto)      │
                                      │ • ...                       │
                                      └─────────────────────────────┘
```

---

## 📊 Tabel Parent: bmn_pemanfaatan

### Deskripsi
Tabel utama yang menyimpan informasi dasar pemanfaatan BMN. Tabel ini **TIDAK DIHAPUS** untuk menjaga backward compatibility.

### Primary Key
```sql
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY
```
⚠️ **Penting**: Menggunakan `INT` (signed integer), bukan `BIGINT` atau `UNSIGNED`.

### Kolom Utama (Simplified)

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| `id` | INT | NO | Primary key |
| `kode_lokasi` | VARCHAR(255) | YES | Kode lokasi BMN |
| `jenis_pemanfaatan` | VARCHAR(255) | YES | Jenis pemanfaatan (Sewa, KSP, dll) |
| `mitra` | VARCHAR(255) | YES | Nama mitra pemanfaatan |
| `alamat_objek` | TEXT | YES | Alamat objek yang dimanfaatkan |
| `nilai_pemanfaatan` | DECIMAL(15,2) | YES | Nilai ekonomis pemanfaatan |
| `tanggal_mulai` | DATE | YES | Tanggal mulai pemanfaatan |
| `tanggal_berakhir` | DATE | YES | Tanggal berakhir pemanfaatan |
| `is_complete` | BOOLEAN | YES | Status kelengkapan data |
| `created_at` | TIMESTAMP | YES | Waktu pembuatan record |
| `updated_at` | TIMESTAMP | YES | Waktu update terakhir |

### Kolom Legacy (Masih Ada untuk Backward Compatibility)

Tabel parent masih memiliki kolom-kolom seperti:
- `surat_konfirmasi_nomor`, `surat_konfirmasi_tanggal`, ... (untuk Surat Konfirmasi Perpanjangan Sewa)
- `nodin_berjenjang_nomor`, `nodin_berjenjang_tanggal`, ... (untuk Nodin Berjenjang)
- `surat_usulan_kpknl_nomor`, `surat_usulan_kpknl_tanggal`, ... (untuk Surat Usulan KPKNL)
- Dan kolom lainnya untuk dokumen yang belum direfactor

**Strategi**: Dual-write (data disimpan ke child table DAN parent table)

### Eloquent Model

```php
// app/Models/BmnPemanfaatan.php

class BmnPemanfaatan extends Model
{
    protected $table = 'bmn_pemanfaatan';

    // Relationships
    public function suratKonfirmasi()
    {
        return $this->hasOne(SuratKonfirmasiPerpanjanganSewa::class, 'pemanfaatan_id');
    }

    public function nodinBerjenjang()
    {
        return $this->hasOne(NodinBerjenjang::class, 'pemanfaatan_id');
    }

    public function suratUsulanKpknl()
    {
        return $this->hasOne(SuratUsulanKpknlSptjm::class, 'pemanfaatan_id');
    }

    // Scope untuk eager loading
    public function scopeWithDocuments($query)
    {
        return $query->with(['suratKonfirmasi', 'nodinBerjenjang', 'suratUsulanKpknl']);
    }
}
```

---

## 📄 Tabel Child: surat_konfirmasi_perpanjangan_sewa

### Deskripsi
Menyimpan data **Surat Konfirmasi Perpanjangan Sewa** yang dikirim ke mitra untuk mengonfirmasi perpanjangan kontrak sewa.

### Schema

```sql
CREATE TABLE `surat_konfirmasi_perpanjangan_sewa` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pemanfaatan_id` INT NOT NULL,
    `nomor` VARCHAR(255) NOT NULL,
    `tanggal` DATE NOT NULL,
    `tujuan_surat` TEXT NULL,
    `peruntukan_surat` TEXT NULL,
    `nomor_perjanjian_lama_dpr` TEXT NULL,
    `nomor_perjanjian_lama_mitra` TEXT NULL,
    `tanggal_berakhir` DATE NULL,
    `tanggal_konfirmasi_terakhir` DATE NULL,
    `kasub_nama` TEXT NULL,
    `kasub_nomor` TEXT NULL,
    `lampiran` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,

    FOREIGN KEY (`pemanfaatan_id`)
        REFERENCES `bmn_pemanfaatan`(`id`)
        ON DELETE CASCADE,

    INDEX `idx_pemanfaatan_id` (`pemanfaatan_id`),
    INDEX `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Kolom Detail

| Kolom | Tipe | Wajib? | Deskripsi |
|-------|------|--------|-----------|
| `id` | BIGINT UNSIGNED | ✅ | Primary key (auto increment) |
| `pemanfaatan_id` | INT | ✅ | Foreign key ke `bmn_pemanfaatan.id` |
| `nomor` | VARCHAR(255) | ✅ | Nomor surat (contoh: "B-123/DPR RI/XII/2024") |
| `tanggal` | DATE | ✅ | Tanggal surat dibuat |
| `tujuan_surat` | TEXT | ⚪ | Tujuan surat (nama mitra/penerima) |
| `peruntukan_surat` | TEXT | ⚪ | Peruntukan/keperluan sewa |
| `nomor_perjanjian_lama_dpr` | TEXT | ⚪ | Nomor perjanjian lama dari sisi DPR |
| `nomor_perjanjian_lama_mitra` | TEXT | ⚪ | Nomor perjanjian lama dari sisi Mitra |
| `tanggal_berakhir` | DATE | ⚪ | Tanggal berakhir kontrak lama |
| `tanggal_konfirmasi_terakhir` | DATE | ⚪ | Tanggal konfirmasi terakhir dikirim |
| `kasub_nama` | TEXT | ⚪ | Nama Kepala Sub Bagian yang menandatangani |
| `kasub_nomor` | TEXT | ⚪ | Nomor SK/NIP Kasub |
| `lampiran` | TEXT | ⚪ | Path file lampiran (jika ada) |

### Eloquent Model

```php
// app/Models/SuratKonfirmasiPerpanjanganSewa.php

class SuratKonfirmasiPerpanjanganSewa extends Model
{
    protected $table = 'surat_konfirmasi_perpanjangan_sewa';

    protected $fillable = [
        'pemanfaatan_id',
        'nomor',
        'tanggal',
        'tujuan_surat',
        'peruntukan_surat',
        'nomor_perjanjian_lama_dpr',
        'nomor_perjanjian_lama_mitra',
        'tanggal_berakhir',
        'tanggal_konfirmasi_terakhir',
        'kasub_nama',
        'kasub_nomor',
        'lampiran',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_berakhir' => 'date',
        'tanggal_konfirmasi_terakhir' => 'date',
    ];

    // Relationship
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
```

### Contoh Data

```php
[
    'pemanfaatan_id' => 46,
    'nomor' => 'B-1234/DPR RI/XI/2024',
    'tanggal' => '2024-11-15',
    'tujuan_surat' => 'Yth. PT Mitra Sejahtera',
    'peruntukan_surat' => 'Kantor Perwakilan DPR',
    'nomor_perjanjian_lama_dpr' => 'PKS-001/DPR/2023',
    'nomor_perjanjian_lama_mitra' => 'MOU-456/PMS/2023',
    'tanggal_berakhir' => '2024-12-31',
    'kasub_nama' => 'Dr. Ahmad Budiman, S.H., M.H.',
    'kasub_nomor' => '196501011990031001',
]
```

---

## 📝 Tabel Child: nodin_berjenjang

### Deskripsi
Menyimpan data **Nota Dinas Berjenjang** yang merupakan dokumen internal untuk persetujuan hierarkis perpanjangan/pemanfaatan BMN.

### Schema

```sql
CREATE TABLE `nodin_berjenjang` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pemanfaatan_id` INT NOT NULL,
    `nomor` VARCHAR(100) NOT NULL,
    `tanggal` DATE NULL COMMENT 'Legacy field untuk compatibility',
    `tanggal_mulai` DATE NOT NULL,
    `tanggal_selesai` DATE NOT NULL,
    `mitra` VARCHAR(255) NULL,
    `peruntukan` VARCHAR(255) NULL,
    `nominal` DECIMAL(15,2) NULL,
    `kasub_nama` VARCHAR(255) NULL COMMENT 'Auto-filled dari surat_konfirmasi',
    `kasub_nomor` VARCHAR(255) NULL COMMENT 'Auto-filled dari surat_konfirmasi',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,

    FOREIGN KEY (`pemanfaatan_id`)
        REFERENCES `bmn_pemanfaatan`(`id`)
        ON DELETE CASCADE,

    INDEX `idx_pemanfaatan_id` (`pemanfaatan_id`),
    INDEX `idx_tanggal_mulai` (`tanggal_mulai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Kolom Detail

| Kolom | Tipe | Wajib? | Auto-Populate? | Deskripsi |
|-------|------|--------|----------------|-----------|
| `id` | BIGINT UNSIGNED | ✅ | - | Primary key |
| `pemanfaatan_id` | INT | ✅ | - | Foreign key ke parent |
| `nomor` | VARCHAR(100) | ✅ | - | Nomor nota dinas |
| `tanggal` | DATE | ⚪ | - | Legacy field (untuk data lama) |
| `tanggal_mulai` | DATE | ✅ | - | Tanggal mulai periode pemanfaatan |
| `tanggal_selesai` | DATE | ✅ | - | Tanggal selesai periode pemanfaatan |
| `mitra` | VARCHAR(255) | ⚪ | - | Nama mitra |
| `peruntukan` | VARCHAR(255) | ⚪ | - | Peruntukan/keperluan |
| `nominal` | DECIMAL(15,2) | ⚪ | - | Nilai nominal sewa (Rupiah) |
| `kasub_nama` | VARCHAR(255) | ⚪ | ✅ | **AUTO dari Surat Konfirmasi** |
| `kasub_nomor` | VARCHAR(255) | ⚪ | ✅ | **AUTO dari Surat Konfirmasi** |

### Eloquent Model dengan Accessor

```php
// app/Models/NodinBerjenjang.php

use Carbon\Carbon;

class NodinBerjenjang extends Model
{
    protected $table = 'nodin_berjenjang';

    protected $fillable = [
        'pemanfaatan_id',
        'nomor',
        'tanggal',
        'tanggal_mulai',
        'tanggal_selesai',
        'mitra',
        'peruntukan',
        'nominal',
        'kasub_nama',
        'kasub_nomor',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nominal' => 'decimal:2',
    ];

    // Accessor: Hitung jangka waktu otomatis
    public function getJangkaWaktuAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return null;
        }

        $start = Carbon::parse($this->tanggal_mulai);
        $end = Carbon::parse($this->tanggal_selesai);
        $months = $start->diffInMonths($end);
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) {
            return "{$years} tahun {$remainingMonths} bulan";
        } elseif ($years > 0) {
            return "{$years} tahun";
        } else {
            return "{$months} bulan";
        }
    }

    // Relationship
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
```

### Contoh Data

```php
[
    'pemanfaatan_id' => 46,
    'nomor' => 'ND-789/SET.DPR-RI/XI/2024',
    'tanggal_mulai' => '2025-01-01',
    'tanggal_selesai' => '2026-12-31',
    'mitra' => 'PT Mitra Sejahtera',
    'peruntukan' => 'Kantor Perwakilan DPR',
    'nominal' => 500000000.00,
    'kasub_nama' => 'Dr. Ahmad Budiman, S.H., M.H.', // Auto dari Surat Konfirmasi
    'kasub_nomor' => '196501011990031001',          // Auto dari Surat Konfirmasi
]

// Accessor jangka_waktu akan return: "2 tahun"
```

---

## 🏛️ Tabel Child: surat_usulan_kpknl_sptjm

### Deskripsi
Menyimpan data gabungan **Surat Usulan ke KPKNL** dan **SPTJM (Surat Pernyataan Tanggung Jawab Mutlak)**. Kedua dokumen ini digabung karena selalu berpasangan.

### Schema

```sql
CREATE TABLE `surat_usulan_kpknl_sptjm` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pemanfaatan_id` INT NOT NULL,

    -- Surat Usulan KPKNL fields
    `surat_usulan_nomor` VARCHAR(255) NOT NULL,
    `surat_usulan_tanggal` DATE NOT NULL,
    `surat_usulan_hal` VARCHAR(255) NULL,
    `surat_usulan_tujuan` VARCHAR(255) NULL,
    `surat_usulan_isi` TEXT NULL,
    `surat_usulan_peruntukan` VARCHAR(255) NULL,
    `surat_usulan_tanggal_berakhir` DATE NULL,
    `kasubag_nama` VARCHAR(255) NULL COMMENT 'Auto-filled dari nodin_berjenjang atau surat_konfirmasi',
    `kasubag_nomor` VARCHAR(255) NULL COMMENT 'Auto-filled dari nodin_berjenjang atau surat_konfirmasi',

    -- SPTJM fields
    `sptjm_nomor` VARCHAR(255) NOT NULL,
    `sptjm_tanggal` DATE NULL,
    `sptjm_kode_barang` VARCHAR(255) NOT NULL,
    `sptjm_nup` VARCHAR(255) NULL,
    `sptjm_luasan_sewa` VARCHAR(255) NULL,
    `sptjm_lokasi_sewa` VARCHAR(255) NULL,

    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,

    FOREIGN KEY (`pemanfaatan_id`)
        REFERENCES `bmn_pemanfaatan`(`id`)
        ON DELETE CASCADE,

    INDEX `idx_pemanfaatan_id` (`pemanfaatan_id`),
    INDEX `idx_surat_usulan_tanggal` (`surat_usulan_tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Kolom Detail

#### A. Surat Usulan KPKNL

| Kolom | Tipe | Wajib? | Auto? | Deskripsi |
|-------|------|--------|-------|-----------|
| `surat_usulan_nomor` | VARCHAR(255) | ✅ | - | Nomor surat usulan |
| `surat_usulan_tanggal` | DATE | ✅ | - | Tanggal surat usulan |
| `surat_usulan_hal` | VARCHAR(255) | ⚪ | - | Hal/perihal surat |
| `surat_usulan_tujuan` | VARCHAR(255) | ⚪ | - | Tujuan surat (KPKNL mana) |
| `surat_usulan_isi` | TEXT | ⚪ | - | Isi/konten surat usulan |
| `surat_usulan_peruntukan` | VARCHAR(255) | ⚪ | - | Peruntukan sewa |
| `surat_usulan_tanggal_berakhir` | DATE | ⚪ | - | Tanggal berakhir yang diusulkan |
| `kasubag_nama` | VARCHAR(255) | ⚪ | ✅ | **AUTO dari Nodin Berjenjang atau Surat Konfirmasi** |
| `kasubag_nomor` | VARCHAR(255) | ⚪ | ✅ | **AUTO dari Nodin Berjenjang atau Surat Konfirmasi** |

#### B. SPTJM (Surat Pernyataan Tanggung Jawab Mutlak)

| Kolom | Tipe | Wajib? | Deskripsi |
|-------|------|--------|-----------|
| `sptjm_nomor` | VARCHAR(255) | ✅ | Nomor SPTJM |
| `sptjm_tanggal` | DATE | ⚪ | Tanggal SPTJM dibuat |
| `sptjm_kode_barang` | VARCHAR(255) | ✅ | Kode barang BMN |
| `sptjm_nup` | VARCHAR(255) | ⚪ | Nomor Urut Pendaftaran (NUP) |
| `sptjm_luasan_sewa` | VARCHAR(255) | ⚪ | Luas area yang disewa (m²) |
| `sptjm_lokasi_sewa` | VARCHAR(255) | ⚪ | Lokasi area yang disewa |

### Eloquent Model

```php
// app/Models/SuratUsulanKpknlSptjm.php

class SuratUsulanKpknlSptjm extends Model
{
    protected $table = 'surat_usulan_kpknl_sptjm';

    protected $fillable = [
        'pemanfaatan_id',
        // Surat Usulan KPKNL
        'surat_usulan_nomor',
        'surat_usulan_tanggal',
        'surat_usulan_hal',
        'surat_usulan_tujuan',
        'surat_usulan_isi',
        'surat_usulan_peruntukan',
        'surat_usulan_tanggal_berakhir',
        'kasubag_nama',
        'kasubag_nomor',
        // SPTJM
        'sptjm_nomor',
        'sptjm_tanggal',
        'sptjm_kode_barang',
        'sptjm_nup',
        'sptjm_luasan_sewa',
        'sptjm_lokasi_sewa',
    ];

    protected $casts = [
        'surat_usulan_tanggal' => 'date',
        'surat_usulan_tanggal_berakhir' => 'date',
        'sptjm_tanggal' => 'date',
    ];

    // Helper: Cek apakah SPTJM sudah diisi
    public function hasSptjmData()
    {
        return !empty($this->sptjm_nomor) && !empty($this->sptjm_kode_barang);
    }

    // Relationship
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
```

### Contoh Data

```php
[
    'pemanfaatan_id' => 46,

    // Surat Usulan KPKNL
    'surat_usulan_nomor' => 'SU-456/SET.DPR-RI/XI/2024',
    'surat_usulan_tanggal' => '2024-11-20',
    'surat_usulan_hal' => 'Usulan Perpanjangan Sewa Gedung',
    'surat_usulan_tujuan' => 'KPKNL Jakarta III',
    'surat_usulan_peruntukan' => 'Kantor Perwakilan DPR',
    'surat_usulan_tanggal_berakhir' => '2026-12-31',
    'kasubag_nama' => 'Dr. Ahmad Budiman, S.H., M.H.', // Auto dari Nodin atau Surat Konfirmasi
    'kasubag_nomor' => '196501011990031001',

    // SPTJM
    'sptjm_nomor' => 'SPTJM-789/SET.DPR-RI/XI/2024',
    'sptjm_tanggal' => '2024-11-20',
    'sptjm_kode_barang' => '01.01.05.02.0001',
    'sptjm_nup' => '001234',
    'sptjm_luasan_sewa' => '500 m²',
    'sptjm_lokasi_sewa' => 'Lantai 3-5, Gedung Perwakilan',
]
```

---

## 🔗 Relationships & Foreign Keys

### Foreign Key Constraints

Semua tabel child memiliki foreign key ke tabel parent dengan constraint:

```sql
FOREIGN KEY (pemanfaatan_id)
    REFERENCES bmn_pemanfaatan(id)
    ON DELETE CASCADE
```

#### ⚠️ Penting: Tipe Data Foreign Key

```sql
-- PARENT TABLE
bmn_pemanfaatan.id → INT (signed, 32-bit)

-- CHILD TABLES (harus match!)
pemanfaatan_id → INT (signed, 32-bit)
```

**TIDAK BOLEH menggunakan:**
- ❌ `BIGINT` (64-bit)
- ❌ `UNSIGNED INT` (tanpa negatif)
- ❌ `UNSIGNED BIGINT`

### Cascade Delete Behavior

Jika record di `bmn_pemanfaatan` dihapus, semua child records terkait akan **otomatis terhapus**:

```php
// Contoh: Hapus pemanfaatan ID 46
$pemanfaatan = BmnPemanfaatan::find(46);
$pemanfaatan->delete();

// Akan otomatis menghapus:
// - surat_konfirmasi_perpanjangan_sewa dengan pemanfaatan_id = 46
// - nodin_berjenjang dengan pemanfaatan_id = 46
// - surat_usulan_kpknl_sptjm dengan pemanfaatan_id = 46
```

### Relasi Eloquent

```php
// 1. Parent → Child (hasOne)
$pemanfaatan = BmnPemanfaatan::find(46);
$suratKonfirmasi = $pemanfaatan->suratKonfirmasi;
$nodinBerjenjang = $pemanfaatan->nodinBerjenjang;
$suratUsulan = $pemanfaatan->suratUsulanKpknl;

// 2. Child → Parent (belongsTo)
$suratKonfirmasi = SuratKonfirmasiPerpanjanganSewa::find(1);
$pemanfaatan = $suratKonfirmasi->pemanfaatan;

// 3. Eager Loading
$pemanfaatan = BmnPemanfaatan::with([
    'suratKonfirmasi',
    'nodinBerjenjang',
    'suratUsulanKpknl'
])->find(46);

// 4. Menggunakan scope
$pemanfaatan = BmnPemanfaatan::withDocuments()->find(46);
```

---

## 🔄 Workflow & Auto-Populate Kasub

### Alur Kerja Dokumen

```
STEP 1: Surat Konfirmasi Perpanjangan Sewa
├─ User mengisi: nomor, tanggal, tujuan, peruntukan, dll
├─ User mengisi: kasub_nama, kasub_nomor (MANUAL)
└─ Data disimpan ke: surat_konfirmasi_perpanjangan_sewa
            │
            │ kasub data mengalir ↓
            ▼
STEP 2: Nodin Berjenjang
├─ User mengisi: nomor, tanggal_mulai, tanggal_selesai, nominal, dll
├─ AUTO-FILL: kasub_nama ← dari Surat Konfirmasi
├─ AUTO-FILL: kasub_nomor ← dari Surat Konfirmasi
└─ User bisa override jika perlu
            │
            │ kasub data mengalir ↓
            ▼
STEP 3: Surat Usulan KPKNL & SPTJM
├─ User mengisi: nomor surat, nomor SPTJM, dll
├─ AUTO-FILL: kasubag_nama ← dari Nodin Berjenjang (prioritas 1)
├─ AUTO-FILL: kasubag_nomor ← dari Nodin Berjenjang (prioritas 1)
├─ FALLBACK: jika Nodin kosong → ambil dari Surat Konfirmasi
└─ User bisa override jika perlu
```

### Implementasi Auto-Populate

#### Backend: Controller Method

```php
// app/Http/Controllers/BmnUtilizationController.php

public function getAutoPopulateKasub(Request $request, $id)
{
    $utilization = BmnPemanfaatan::with([
        'suratKonfirmasi',
        'nodinBerjenjang'
    ])->findOrFail($id);

    $documentType = $request->query('document_type');
    $kasub = null;

    switch ($documentType) {
        case 'nodin_berjenjang':
            // Ambil dari Surat Konfirmasi
            if ($utilization->suratKonfirmasi) {
                $kasub = [
                    'nama' => $utilization->suratKonfirmasi->kasub_nama,
                    'nomor' => $utilization->suratKonfirmasi->kasub_nomor,
                ];
            }
            break;

        case 'surat_usulan_kpknl':
            // Prioritas 1: Ambil dari Nodin Berjenjang
            if ($utilization->nodinBerjenjang &&
                $utilization->nodinBerjenjang->kasub_nama) {
                $kasub = [
                    'nama' => $utilization->nodinBerjenjang->kasub_nama,
                    'nomor' => $utilization->nodinBerjenjang->kasub_nomor,
                ];
            }
            // Fallback: Ambil dari Surat Konfirmasi
            elseif ($utilization->suratKonfirmasi) {
                $kasub = [
                    'nama' => $utilization->suratKonfirmasi->kasub_nama,
                    'nomor' => $utilization->suratKonfirmasi->kasub_nomor,
                ];
            }
            break;
    }

    return response()->json([
        'success' => $kasub !== null,
        'kasub' => $kasub,
        'message' => $kasub
            ? 'Kasub data found'
            : 'No kasub data available from previous documents'
    ]);
}
```

#### Frontend: JavaScript AJAX

```javascript
// resources/views/utilization/documents.blade.php

function autoPopulateKasub(documentType, utilizationId, targetFieldNama, targetFieldNomor) {
    fetch(`/utilization-dashboard/${utilizationId}/auto-populate-kasub?document_type=${documentType}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.kasub) {
            // Populate fields
            document.querySelector(targetFieldNama).value = data.kasub.nama || '';
            document.querySelector(targetFieldNomor).value = data.kasub.nomor || '';

            // Show notification
            alert('Data Kasub telah diisi otomatis. Anda dapat mengubahnya jika diperlukan.');
        }
    })
    .catch(error => console.error('Failed to auto-populate:', error));
}

// Trigger saat modal dibuka
document.getElementById('modal-nodin-berjenjang')
    .addEventListener('show.bs.modal', function() {
        const namaField = document.getElementById('nodin_berjenjang_kasub_nama');
        const nomorField = document.getElementById('nodin_berjenjang_kasub_nomor');

        // Hanya auto-fill jika kosong
        if (!namaField.value && !nomorField.value) {
            autoPopulateKasub(
                'nodin_berjenjang',
                utilizationId,
                '#nodin_berjenjang_kasub_nama',
                '#nodin_berjenjang_kasub_nomor'
            );
        }
    });
```

### Route

```php
// routes/web.php

Route::get(
    '/utilization-dashboard/{id}/auto-populate-kasub',
    [BmnUtilizationController::class, 'getAutoPopulateKasub']
)->name('bmn.utilization.auto_populate_kasub');
```

---

## ♻️ Dual-Write Pattern

### Konsep

Data disimpan di **2 tempat**:
1. ✅ **Child table** (tabel baru yang terstruktur)
2. ✅ **Parent table** (kolom legacy untuk backward compatibility)

### Tujuan

- ✅ Kode lama yang masih mengakses parent table tetap berfungsi
- ✅ Kode baru menggunakan child table dengan relasi yang lebih baik
- ✅ Migrasi bertahap tanpa breaking changes

### Implementasi di Controller

```php
// app/Http/Controllers/BmnUtilizationController.php

case 'surat-konfirmasi-perpanjangan-sewa':
    // 1. Validasi dengan field names dari form
    $validated = $request->validate([
        'surat_konfirmasi_nomor' => 'required|string|max:255',
        'surat_konfirmasi_tanggal' => 'required|date',
        'surat_konfirmasi_tujuan_surat' => 'nullable|string',
        // ... kolom lainnya
    ]);

    // 2. Map ke field names child table (tanpa prefix)
    $childData = [
        'nomor' => $validated['surat_konfirmasi_nomor'],
        'tanggal' => $validated['surat_konfirmasi_tanggal'],
        'tujuan_surat' => $validated['surat_konfirmasi_tujuan_surat'] ?? null,
        // ... mapping lainnya
    ];

    // 3. WRITE #1: Simpan ke CHILD TABLE (tabel baru)
    $utilization->suratKonfirmasi()->updateOrCreate(
        ['pemanfaatan_id' => $utilization->id],
        $childData
    );

    // 4. WRITE #2: Simpan ke PARENT TABLE (backward compatibility)
    $utilization->update($validated);

    break;
```

### Diagram Flow

```
┌─────────────────┐
│  User Submit    │
│  Form Data      │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────┐
│  Controller validates data      │
│  (field names dengan prefix)    │
└────────┬────────────────────────┘
         │
         ├──────────────────┬──────────────────┐
         ▼                  ▼                  ▼
┌──────────────────┐  ┌──────────────┐  ┌──────────────┐
│  Map to child    │  │              │  │              │
│  field names     │  │              │  │              │
│  (no prefix)     │  │              │  │              │
└────────┬─────────┘  │              │  │              │
         │            │              │  │              │
         ▼            ▼              ▼  ▼              ▼
┌─────────────────┐ ┌──────────────────────────────────┐
│  CHILD TABLE    │ │  PARENT TABLE                    │
│  (new)          │ │  (legacy columns)                │
│                 │ │                                  │
│  surat_         │ │  surat_konfirmasi_nomor          │
│  konfirmasi_    │ │  surat_konfirmasi_tanggal        │
│  perpanjangan_  │ │  surat_konfirmasi_tujuan_surat   │
│  sewa           │ │  ...                             │
│                 │ │  (masih ada untuk compatibility) │
│  ├─ nomor       │ │                                  │
│  ├─ tanggal     │ │                                  │
│  ├─ tujuan_surat│ │                                  │
│  └─ ...         │ │                                  │
└─────────────────┘ └──────────────────────────────────┘
         │                        │
         └────────┬───────────────┘
                  ▼
         ┌────────────────┐
         │  Both synced!  │
         └────────────────┘
```

### Rencana Fase Out

Di masa depan (setelah semua kode direfactor):
1. ⚠️ Stop writing ke parent table columns
2. ⚠️ Mark legacy columns as deprecated
3. ⚠️ Drop legacy columns dari parent table

**Namun untuk saat ini, dual-write HARUS tetap dijalankan!**

---

## 📐 Entity Relationship Diagram

### ERD Lengkap (Text-Based)

```
┌───────────────────────────────────────────────────────────────────┐
│                      bmn_pemanfaatan (PARENT)                     │
├───────────────────────────────────────────────────────────────────┤
│ PK │ id (INT)                                                     │
│    │ kode_lokasi (VARCHAR)                                        │
│    │ jenis_pemanfaatan (VARCHAR)                                  │
│    │ mitra (VARCHAR)                                              │
│    │ alamat_objek (TEXT)                                          │
│    │ nilai_pemanfaatan (DECIMAL)                                  │
│    │ tanggal_mulai (DATE)                                         │
│    │ tanggal_berakhir (DATE)                                      │
│    │ is_complete (BOOLEAN)                                        │
│    │ created_at, updated_at (TIMESTAMP)                           │
│    │                                                              │
│    │ --- LEGACY COLUMNS (untuk backward compatibility) ---       │
│    │ surat_konfirmasi_nomor, surat_konfirmasi_tanggal, ...       │
│    │ nodin_berjenjang_nomor, nodin_berjenjang_tanggal, ...       │
│    │ surat_usulan_kpknl_nomor, ...                               │
│    │ (187+ kolom total)                                          │
└─────┬─────────────────────────────────────────────────────────────┘
      │
      │ 1:1 hasOne relationships
      │
      ├─────────────────────────────────┬─────────────────────────────┐
      │                                 │                             │
      ▼                                 ▼                             ▼
┌─────────────────────────┐  ┌─────────────────────────┐  ┌─────────────────────────┐
│ surat_konfirmasi_       │  │ nodin_berjenjang        │  │ surat_usulan_kpknl_     │
│ perpanjangan_sewa       │  │                         │  │ sptjm                   │
├─────────────────────────┤  ├─────────────────────────┤  ├─────────────────────────┤
│ PK │ id (BIGINT)        │  │ PK │ id (BIGINT)        │  │ PK │ id (BIGINT)        │
│ FK │ pemanfaatan_id     │  │ FK │ pemanfaatan_id     │  │ FK │ pemanfaatan_id     │
│    │   (INT)            │  │    │   (INT)            │  │    │   (INT)            │
│    │                    │  │    │                    │  │    │                    │
│    │ nomor*             │  │    │ nomor*             │  │    │ surat_usulan_nomor*│
│    │ tanggal*           │  │    │ tanggal            │  │    │ surat_usulan_      │
│    │ tujuan_surat       │  │    │ tanggal_mulai*     │  │    │   tanggal*         │
│    │ peruntukan_surat   │  │    │ tanggal_selesai*   │  │    │ surat_usulan_hal   │
│    │ nomor_perjanjian_  │  │    │ mitra              │  │    │ surat_usulan_tujuan│
│    │   lama_dpr         │  │    │ peruntukan         │  │    │ surat_usulan_isi   │
│    │ nomor_perjanjian_  │  │    │ nominal            │  │    │ surat_usulan_      │
│    │   lama_mitra       │  │    │                    │  │    │   peruntukan       │
│    │ tanggal_berakhir   │  │    │ 🔄 kasub_nama      │  │    │ surat_usulan_      │
│    │ tanggal_konfirmasi_│  │    │    (auto-filled)   │  │    │   tanggal_berakhir │
│    │   terakhir         │  │    │ 🔄 kasub_nomor     │  │    │                    │
│    │                    │  │    │    (auto-filled)   │  │    │ 🔄 kasubag_nama    │
│    │ 📝 kasub_nama      │─────▶│                    │  │    │    (auto-filled)   │
│    │ 📝 kasub_nomor     │  │    │ created_at         │  │    │ 🔄 kasubag_nomor   │
│    │                    │  │    │ updated_at         │  │    │    (auto-filled)   │
│    │ lampiran           │  │    │                    │  │    │                    │
│    │ created_at         │  │    │ ✨ Accessor:       │  │    │ sptjm_nomor*       │
│    │ updated_at         │  │    │   jangka_waktu     │  │    │ sptjm_tanggal      │
│    │                    │  │    │   (computed field) │  │    │ sptjm_kode_barang* │
└─────────────────────────┘  └──────────┬──────────────┘  │    │ sptjm_nup          │
                                        │                 │    │ sptjm_luasan_sewa  │
                                        │ kasub flow      │    │ sptjm_lokasi_sewa  │
                                        └─────────────────┼───▶│                    │
                                                          │    │ created_at         │
                                                          │    │ updated_at         │
                                                          │    │                    │
                                                          └────┤ ✨ Method:         │
                                                               │   hasSptjmData()   │
                                                               └─────────────────────┘

LEGEND:
  PK  = Primary Key
  FK  = Foreign Key
  *   = Required field (NOT NULL)
  📝  = User input (manual)
  🔄  = Auto-populated from previous document
  ✨  = Computed/helper method
  →   = Data flow direction (kasub chain)
```

### Cascade Delete Flow

```
DELETE bmn_pemanfaatan WHERE id = 46
    │
    ├─ CASCADE DELETE → surat_konfirmasi_perpanjangan_sewa
    │                   WHERE pemanfaatan_id = 46
    │
    ├─ CASCADE DELETE → nodin_berjenjang
    │                   WHERE pemanfaatan_id = 46
    │
    └─ CASCADE DELETE → surat_usulan_kpknl_sptjm
                        WHERE pemanfaatan_id = 46
```

---

## 📊 Rangkuman

### Keuntungan Refactoring

| Aspek | Sebelum | Sesudah | Benefit |
|-------|---------|---------|---------|
| **Struktur** | Monolithic (187+ cols) | Modular (tabel terpisah) | ✅ Lebih maintainable |
| **Data Integrity** | Tidak ada FK | Foreign keys CASCADE | ✅ Konsistensi data terjaga |
| **Kasub Reference** | Manual copy-paste | Auto-populate AJAX | ✅ Mengurangi human error |
| **Query Performance** | SELECT banyak kolom NULL | SELECT hanya kolom relevan | ✅ Lebih efisien |
| **Scalability** | Sulit tambah dokumen | Tinggal buat tabel baru | ✅ Mudah extend |
| **Code Clarity** | Mixed concerns | Separation of concerns | ✅ Lebih clean |

### Migration Status

```
✅ DONE (Fase 1 - 3 dokumen):
├─ surat_konfirmasi_perpanjangan_sewa
├─ nodin_berjenjang
└─ surat_usulan_kpknl_sptjm

⏳ TODO (Fase 2 - 9 dokumen):
├─ nodin_konfirmasi
├─ surat_pernyataan
├─ daftar_bmn
├─ nodin_persetujuan_kpknl
├─ surat_invoice
├─ perjanjian_sewa
├─ nodin_ttd
├─ nodin_internal
└─ (dokumen lainnya)
```

### Key Takeaways

1. ✅ **Parent table (`bmn_pemanfaatan`) tetap ada** - JANGAN DIHAPUS!
2. ✅ **Dual-write pattern** - Data ditulis ke child DAN parent table
3. ✅ **Foreign key menggunakan INT signed** - Harus match dengan parent
4. ✅ **Cascade delete** - Hapus parent = hapus semua children
5. ✅ **Auto-populate kasub** - Data mengalir: Surat Konfirmasi → Nodin → Surat Usulan
6. ✅ **User bisa override** - Auto-populate hanya default, user bebas ubah
7. ✅ **Backward compatibility** - Kode lama tetap berfungsi

---

## 📝 Changelog

| Tanggal | Perubahan |
|---------|-----------|
| 2024-11-26 | Initial refactoring - 3 tabel child dibuat |
| 2024-11-26 | Migration data lama (2 records per tabel) |
| 2024-11-26 | Implementasi auto-populate kasub |
| 2024-11-26 | Dokumentasi schema lengkap |

---

**Dokumen ini dibuat pada**: 26 November 2024
**Versi**: 1.0
**Author**: Development Team - Dashboard BMN DPR RI
