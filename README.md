# 🏛️ Dashboard BMN (Barang Milik Negara)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql)](https://www.mysql.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.0-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-7.0-646CFF?style=flat&logo=vite)](https://vitejs.dev)

> **Sistem Dashboard Multi-Modul untuk Manajemen Pengajuan RKBMN dan Monitoring Pemanfaatan Barang Milik Negara**

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Modul-Modul](#-modul-modul)
  - [1. Dashboard Pengajuan RKBMN](#1-dashboard-pengajuan-rkbmn)
  - [2. Dashboard Statistik](#2-dashboard-statistik)
  - [3. Monitoring Pemanfaatan](#3-monitoring-pemanfaatan)
- [Struktur Database](#-struktur-database)
- [Teknologi](#-teknologi)
- [Instalasi & Setup](#-instalasi--setup)
- [Konfigurasi](#-konfigurasi)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Struktur Folder](#-struktur-folder)
- [API Endpoints](#-api-endpoints)
- [Troubleshooting](#-troubleshooting)
- [FAQ](#-faq)
- [License](#-license)

---

## 🎯 Tentang Proyek

**Dashboard BMN** adalah sistem informasi berbasis web yang dibangun menggunakan Laravel untuk mengelola **Barang Milik Negara (BMN)**. Sistem ini terdiri dari 3 modul utama yang saling terintegrasi dalam **satu database yang sama** (`bmn_dashboard`) untuk memudahkan proses perencanaan, monitoring, dan pelaporan aset pemerintah.

**Catatan Penting:** Ketiga modul menggunakan **database yang sama**, hanya **tabel yang digunakan** berbeda untuk setiap modul.

### Tujuan Sistem

- ✅ **Digitalisasi** proses pengajuan RKBMN (Rencana Kebutuhan Barang Milik Negara)
- ✅ **Monitoring** status pengajuan secara real-time
- ✅ **Visualisasi** data statistik untuk pengambilan keputusan
- ✅ **Tracking** pemanfaatan aset (sewa/pinjam pakai)
- ✅ **Manajemen dokumen** terpusat dan terorganisir

### 3 Modul Utama

| Modul | Route | Tabel yang Digunakan | Fungsi Utama |
|-------|-------|----------------------|--------------|
| **Dashboard Pengajuan RKBMN** | `/` | `bagian` + `bmn_pengajuanrkbmnbagian` | Menampilkan, memfilter, dan memonitor pengajuan RKBMN dari berbagai bagian |
| **Dashboard Statistik** | `/statistical-dashboard` | `bagian` + `bmn_pengajuanrkbmnbagian` | Menampilkan visualisasi statistik pengajuan RKBMN dalam bentuk chart dan grafik |
| **Monitoring Pemanfaatan** | `/utilization-dashboard` | `bmn_pemanfaatan` | Mengelola dan memonitor pemanfaatan BMN (sewa, pinjam pakai) |

> 💡 **Ringkasan Database:**
> - **1 Database:** `bmn_dashboard` (untuk semua modul)
> - **3 Tabel:** `bagian`, `bmn_pengajuanrkbmnbagian`, `bmn_pemanfaatan`
> - **Modul 1 & 2** share tabel yang sama (tabel `bagian` + `bmn_pengajuanrkbmnbagian`)
> - **Modul 3** menggunakan tabel terpisah (`bmn_pemanfaatan`)

---

## 🏗️ Arsitektur Sistem

### Diagram Arsitektur 3 Modul

```
┌─────────────────────────────────────────────────────────────────────┐
│                      DASHBOARD BMN SYSTEM                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │   MODUL 1        │  │   MODUL 2        │  │   MODUL 3        │  │
│  │                  │  │                  │  │                  │  │
│  │   Dashboard      │  │   Dashboard      │  │   Monitoring     │  │
│  │   Pengajuan      │  │   Statistik      │  │   Pemanfaatan    │  │
│  │   RKBMN          │  │   RKBMN          │  │   BMN            │  │
│  │                  │  │                  │  │                  │  │
│  │   Route: /       │  │ /statistical-    │  │ /utilization-    │  │
│  │                  │  │   dashboard      │  │   dashboard      │  │
│  │                  │  │                  │  │                  │  │
│  └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘  │
│           │                     │                     │             │
│           │                     │                     │             │
│           └──────────┬──────────┘                     │             │
│                      │                                │             │
│                      └────────────┬───────────────────┘             │
│                                   │                                 │
│                                   ▼                                 │
│              ┌────────────────────────────────────────┐             │
│              │    DATABASE: bmn_dashboard             │             │
│              │    (SATU DATABASE UNTUK SEMUA MODUL)   │             │
│              ├────────────────────────────────────────┤             │
│              │                                        │             │
│              │  📊 TABEL SHARED (Modul 1 & 2):       │             │
│              │     - bagian                          │             │
│              │     - bmn_pengajuanrkbmnbagian        │             │
│              │                                        │             │
│              │  📊 TABEL MODUL 3:                    │             │
│              │     - bmn_pemanfaatan (170+ kolom)    │             │
│              │                                        │             │
│              │  📂 SQL Files Location:                │             │
│              │     database/sql-script/shared/       │             │
│              │     database/sql-script/pemanfaatan/  │             │
│              └────────────────────────────────────────┘             │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Hubungan Modul & Database

**PENTING:** Semua modul menggunakan **DATABASE YANG SAMA** yaitu `bmn_dashboard`. Yang berbeda adalah **TABEL yang digunakan**:

- **Modul 1 & 2** (Dashboard RKBMN + Statistik) menggunakan **tabel yang sama** (SHARED):
  - Tabel `bagian` → Master data bagian/departemen
  - Tabel `bmn_pengajuanrkbmnbagian` → Data pengajuan RKBMN

- **Modul 3** (Monitoring Pemanfaatan) menggunakan **tabel terpisah**:
  - Tabel `bmn_pemanfaatan` → Data pemanfaatan/sewa BMN

**Catatan:** Folder `shared/` dan `pemanfaatan/` di `database/sql-script/` hanya untuk **organisasi file SQL**, bukan database terpisah.

---

## 📦 Modul-Modul

### 1. Dashboard Pengajuan RKBMN

**Route:** `/`
**Controller:** `BmnDashboardController`
**View:** `resources/views/bmn/dashboard.blade.php`

#### 📝 Deskripsi

Dashboard utama untuk menampilkan dan memfilter pengajuan **RKBMN (Rencana Kebutuhan Barang Milik Negara)** dari berbagai bagian/departemen. Dashboard ini menyediakan overview lengkap tentang status pengajuan, budget, dan approval.

#### ✨ Fitur Utama

1. **Statistik Cards**
   - 📊 Total Pengajuan
   - ⏳ Pending (Menunggu Approval)
   - ✅ Approved (Disetujui)
   - ❌ Rejected (Ditolak)
   - 💰 Total Anggaran Disetujui (Rupiah)

2. **Filter Multi-Dimensi**
   - Filter by **Jenis Pengajuan** (R1, R3, R4, R5, R6)
   - Filter by **Bagian/Departemen**
   - Filter by **Status** (Draft, Diajukan, Approved, Rejected, dll)
   - Filter by **Tahun Anggaran**
   - Filter by **Range Anggaran** (Min-Max)

3. **Tabel Data**
   - Pagination
   - Sortable columns
   - Responsive table
   - Export functionality (via Statistical Dashboard)

#### 🔄 Workflow

```
User → Pilih Filter → Apply Filter → Dashboard Update → Lihat Data Terfilter
                                            ↓
                                     Export ke Statistik
```

#### 📊 Jenis Pengajuan RKBMN

| Kode | Jenis Pengajuan |
|------|-----------------|
| **R1** | Tanah dan/atau Bangunan Perkantoran |
| **R3** | Tanah dan/atau Gedung Rumah Negara |
| **R4** | Kendaraan Jabatan |
| **R5** | Kendaraan Operasional |
| **R6** | Kendaraan Fungsional |

---

### 2. Dashboard Statistik

**Route:** `/statistical-dashboard`
**Controller:** `BmnStatisticalDashboardController`
**View:** `resources/views/bmn/statistical_dashboard.blade.php`

#### 📝 Deskripsi

Dashboard visualisasi data yang menampilkan **statistik dan analitik** pengajuan RKBMN dalam bentuk grafik, chart, dan diagram. Mendukung export data ke Excel dan PDF.

#### ✨ Fitur Utama

1. **Visual Analytics**
   - 📊 **Bar Chart** - Distribusi status pengajuan
   - 🥧 **Pie Chart** - Breakdown by departemen
   - 📈 **Line Chart** - Trend tahunan
   - 💹 **Budget Chart** - Alokasi anggaran per departemen

2. **Multi-Dimensional Statistics**
   - Statistik Status by Tahun
   - Statistik Departemen by Tahun
   - Alokasi Budget by Departemen & Tahun
   - Perbandingan ATR vs Non-ATR
   - Analisis Skema Pengadaan

3. **Export Functionality**
   - 📥 **Export to Excel** (.xlsx)
   - 📄 **Export to PDF**
   - Maintains applied filters in export
   - Custom formatting for reports

4. **Advanced Filtering**
   - Sama dengan Dashboard Pengajuan
   - Plus: Filter by ATR/Non-ATR
   - Plus: Filter by Skema Pengadaan

#### 🔄 Workflow

```
User → Pilih Filter → View Statistics → Analyze Data → Export Report
                           ↓
                   Interactive Charts
                   (Hover, Click, Zoom)
```

#### 📊 Contoh Visualisasi

- **Status Distribution**: Menampilkan berapa pengajuan yang Draft, Pending, Approved, Rejected
- **Department Breakdown**: Top 10 departemen dengan pengajuan terbanyak
- **Budget Allocation**: Total anggaran per jenis pengajuan
- **Yearly Trends**: Pertumbuhan pengajuan year-over-year

---

### 3. Monitoring Pemanfaatan

**Route:** `/utilization-dashboard`
**Controller:** `BmnUtilizationController`
**View:** `resources/views/bmn/utilization_dashboard.blade.php`

#### 📝 Deskripsi

Modul terpisah untuk **mengelola dan memonitor pemanfaatan BMN** (sewa, pinjam pakai). Sistem ini mengelola proses lengkap dari pendaftaran penyewa hingga penyelesaian kontrak, termasuk tracking dokumen dan pembayaran.

#### ✨ Fitur Utama

1. **CRUD Operations**
   - ➕ Create: Buat pengajuan pemanfaatan baru
   - 👁️ Read: Lihat detail pemanfaatan
   - ✏️ Update: Edit data pemanfaatan
   - 🗑️ Delete: Hapus data
   - ✅ Toggle Complete: Tandai lengkap/tidak lengkap

2. **5-Stage Workflow Process**

   **Tab 1: Informasi Penyewa** (Tahap 1)
   - Data PIC Penyewa
   - Data PIC Administrasi BMN
   - Nama Mitra Penyewa
   - Jenis Mitra (Perusahaan/Yayasan/Koperasi/Perseorangan)
   - Jenis Usulan (Perpanjangan/Usulan Baru)

   **Tab 2: Konfirmasi** (Tahap 2)
   - Nodin Konfirmasi (4 fields)
   - Surat Konfirmasi (7 fields)
   - Upload Dokumen Pendukung:
     - Surat Usulan Sewa
     - NPWP
     - KTP Penandatangan
     - NIB

   **Tab 3: Usulan Pemanfaatan** (Tahap 3)
   - Nodin Berjenjang
   - Surat Usulan KPKNL (5 fields)
   - SPTJM (6 fields)
   - Surat Pernyataan (6 fields)
   - Daftar BMN (JSON format)
   - Upload: PSP, KIB, Dokumen TTD

   **Tab 4: Penilaian KPKNL** (Tahap 4)
   - Upload: Jadwal Penilaian, BASL, Persetujuan KPKNL
   - Nodin Persetujuan KPKNL (9 fields)
   - Surat Invoice (9 fields)
   - Upload: Kode Billing

   **Tab 5: Perjanjian** (Tahap 5)
   - Detail Perjanjian (8 fields)
   - Nodin TTD (5 fields)
   - Nodin Internal (6 fields)
   - Upload: Bukti Bayar, Dokumen Perjanjian
   - Logo Penyewa

3. **Document Management System**
   - Upload hingga **15 jenis dokumen**
   - Format support: PDF, DOC, DOCX, JPG, PNG
   - Max file size: 2MB per file
   - Organized storage: `storage/app/public/uploads/pemanfaatan/`

4. **Status Tracking**

```
┌─────────┐    ┌────────┐    ┌──────────┐    ┌────────┐    ┌───────────┐
│  DRAFT  │───▶│ REVIEW │───▶│ APPROVED │───▶│ ACTIVE │───▶│ COMPLETED │
└─────────┘    └────────┘    └──────────┘    └────────┘    └───────────┘
                                    │             │
                                    │             │
                                    ▼             ▼
                            ┌───────────────────────────┐
                            │  CANCELLED / EXPIRED      │
                            └───────────────────────────┘
```

**Status Definitions:**
- **DRAFT**: Data baru, belum lengkap
- **REVIEW**: Diajukan untuk review
- **APPROVED**: Disetujui oleh KPKNL
- **ACTIVE**: Sewa sedang berjalan
- **COMPLETED**: Sewa selesai
- **CANCELLED**: Dibatalkan
- **EXPIRED**: Masa sewa habis

5. **Financial Tracking**
   - 💰 Biaya Sewa per Periode
   - 📊 Total Biaya Sewa
   - ✅ Total Pendapatan Terealisasi
   - ⏳ Total Pendapatan Outstanding
   - 🔢 Periode Pembayaran

6. **Automatic Completeness Check**
   - System otomatis check kelengkapan data
   - Highlight required fields
   - Toggle manual jika perlu

#### 🔄 Workflow Lengkap

```
1. Admin Create Entry (Status: DRAFT)
          ↓
2. Fill Tab 1: Info Penyewa
          ↓
3. Fill Tab 2: Upload Dokumen Konfirmasi
          ↓
4. Fill Tab 3: Upload Usulan ke KPKNL (Status → REVIEW)
          ↓
5. Fill Tab 4: KPKNL Assessment (Status → APPROVED)
          ↓
6. Fill Tab 5: Finalize Perjanjian
          ↓
7. First Payment Received (Status → ACTIVE)
          ↓
8. Track Payment & Revenue
          ↓
9. End of Contract (Status → COMPLETED)
```

#### 📊 Additional Pages

- **Review Page** (`/utilization-dashboard/review`) - Interface untuk review
- **Confirmation Page** (`/utilization-dashboard/confirmation`) - Workflow konfirmasi
- **Proposals Page** (`/utilization-dashboard/proposals`) - Listing semua usulan

---

## 🗄️ Struktur Database

### Database Name: `bmn_dashboard`

**PENTING:** Sistem menggunakan **SATU DATABASE** yang sama (`bmn_dashboard`) untuk semua modul. Yang berbeda adalah **TABEL yang digunakan** oleh masing-masing modul.

### 📂 1. Tabel Shared (Modul 1 & 2)

**Digunakan oleh:** Dashboard Pengajuan RKBMN & Dashboard Statistik

**Location SQL:** `database/sql-script/shared/`

#### Tabel: `bmn_pengajuanrkbmnbagian`

**Deskripsi:** Menyimpan data pengajuan RKBMN dari berbagai bagian/departemen

**Jumlah Kolom:** 66 kolom

**Key Columns:**

| Kolom | Tipe Data | Deskripsi |
|-------|-----------|-----------|
| `id` | VARCHAR(11) | Primary Key - ID unik pengajuan |
| `kode_jenis_pengajuan` | VARCHAR(10) | R1/R3/R4/R5/R6 |
| `tahun_anggaran` | VARCHAR(4) | Tahun anggaran (2024, 2025, dll) |
| `id_bagian_pengusul` | VARCHAR(11) | FK ke tabel bagian |
| `id_biro_pengusul` | VARCHAR(11) | FK ke tabel bagian |
| `id_bagian_pelaksana` | VARCHAR(11) | FK ke tabel bagian |
| `id_biro_pelaksana` | VARCHAR(11) | FK ke tabel bagian |
| `status` | VARCHAR(50) | Draft/Diajukan/Approved/Rejected/dll |
| `program` | VARCHAR(255) | Nama program |
| `kegiatan` | VARCHAR(255) | Kode kegiatan |
| `output` | VARCHAR(255) | Kode output |
| `akun_belanja` | VARCHAR(50) | Akun belanja |
| `kode_barang` | VARCHAR(50) | Kode barang BMN |
| `uraian_barang` | TEXT | Deskripsi barang |
| `kuantitas` | INT | Jumlah unit |
| `harga_barang` | DECIMAL(18,2) | Harga per unit |
| `total_anggaran` | DECIMAL(18,2) | Total anggaran (kuantitas × harga) |
| `atr_nonatr` | VARCHAR(50) | Klasifikasi ATR/Non-ATR |
| `skema` | VARCHAR(50) | Skema pengadaan |
| `tanggal_pengajuan` | DATE | Tanggal pengajuan |
| `tanggal_kebmn` | DATE | Tanggal ke BMN |
| `tanggal_keperencanaan` | DATE | Tanggal ke Perencanaan |
| `tanggal_final` | DATE | Tanggal finalisasi |
| `dokumen_pendukung` | TEXT | Path dokumen pendukung |
| `tor_signed_path` | TEXT | Path ToR yang sudah ditandatangani |
| `lampiran_signed_path` | TEXT | Path lampiran yang ditandatangani |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY (`id`)

**Status Values:**
- Draft
- Diajukan Ke Unit Pelaksana
- approved
- rejected
- pending
- completed
- in_progress

**SQL File:** `database/sql-script/shared/bmn_pengajuanrkbmnbagian.sql`

---

#### Tabel: `bagian`

**Deskripsi:** Master data bagian/departemen/biro

**Jumlah Kolom:** 7 kolom

| Kolom | Tipe Data | Deskripsi |
|-------|-----------|-----------|
| `id` | VARCHAR(11) | Primary Key - ID bagian |
| `iddeputi` | VARCHAR(11) | ID deputi |
| `idbiro` | VARCHAR(11) | ID biro |
| `uraianbagian` | VARCHAR(255) | Nama bagian/departemen |
| `status` | SET('on','off') | Status aktif/nonaktif |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `updated_at` | TIMESTAMP | Waktu update |

**Indexes:**
- PRIMARY KEY (`id`)

**Total Data:** 100+ departemen/bagian/komisi

**Contoh Data:**
- Komisi I
- Komisi II
- Biro Umum
- Biro Keuangan
- Bagian Persidangan
- dll.

**SQL File:** `database/sql-script/shared/bagian.sql`

---

### 📂 2. Tabel Pemanfaatan (Modul 3)

**Digunakan oleh:** Monitoring Pemanfaatan

**Location SQL:** `database/sql-script/pemanfaatan/`

#### Tabel: `bmn_pemanfaatan`

**Deskripsi:** Menyimpan data pemanfaatan BMN (sewa/pinjam pakai)

**Jumlah Kolom:** 170+ kolom (tabel sangat besar!)

**Struktur Kolom Berdasarkan Grouping:**

##### 🔹 Group 1: Informasi Penyewa (10 kolom)
- `pic_penyewa`, `nomor_hp_pic_penyewa`
- `pic_administrasi_bmn`, `nomor_pic_administrasi_bmn`
- `nama_mitra_penyewa`
- `jenis_mitra` (Perusahaan/Yayasan/Koperasi/Perseorangan)
- `jenis_usulan` (Perpanjangan/Usulan Baru)
- `peruntukan_sewa`, `keterangan_uraian`
- `is_complete` (Flag kelengkapan data)

##### 🔹 Group 2-15: Dokumen & Detail Proses
Terdapat 80+ kolom untuk berbagai dokumen dan tahapan proses:
- Konfirmasi (Nodin & Surat)
- Usulan KPKNL
- SPTJM & Surat Pernyataan
- Penilaian KPKNL
- Perjanjian & Kontrak
- Dan lain-lain

##### 🔹 Group 16: Financial & Status Tracking (15 kolom)
- `biaya_sewa` (Biaya per periode)
- `total_biaya_sewa` (Total keseluruhan)
- `tanggal_mulai`, `tanggal_berakhir`
- `status_sewa` (ENUM: draft/review/approved/active/completed/cancelled/expired)
- `total_pendapatan_terealisasi` (Sudah dibayar)
- `total_pendapatan_outstanding` (Belum dibayar)
- `periode_pembayaran_ke` (Periode saat ini)
- `total_periode_pembayaran` (Total periode)
- `tanggal_aktivasi`, `tanggal_penyelesaian`
- `catatan_pembayaran`
- `cancelled_at`, `cancelled_by`, `cancelled_reason`

**Indexes:**
- PRIMARY KEY (`id`)

**Timestamps:** Tidak menggunakan `created_at`/`updated_at` (disabled)

**SQL File:** `database/sql-script/pemanfaatan/bmn_pemanfaatan.sql`

---

### 🔗 Entity Relationship Diagram

```
┌─────────────────────────────────┐
│         bagian                  │
│  (Master Data Departemen)       │
├─────────────────────────────────┤
│ • id (PK) VARCHAR(11)           │
│ • iddeputi                      │
│ • idbiro                        │
│ • uraianbagian                  │
│ • status (on/off)               │
└─────────────┬───────────────────┘
              │
              │ 1
              │
              │ N (belongsTo)
              │
┌─────────────▼───────────────────┐
│  bmn_pengajuanrkbmnbagian       │
│  (Pengajuan RKBMN)              │
├─────────────────────────────────┤
│ • id (PK) VARCHAR(11)           │
│ • id_bagian_pengusul (FK)       │◄────┐
│ • id_biro_pengusul (FK)         │◄────┤
│ • id_bagian_pelaksana (FK)      │◄────┤ FK ke bagian.id
│ • id_biro_pelaksana (FK)        │◄────┘
│ • kode_jenis_pengajuan          │
│ • tahun_anggaran                │
│ • status                        │
│ • total_anggaran                │
│ • ...60+ kolom lainnya          │
└─────────────────────────────────┘


┌─────────────────────────────────┐
│    bmn_pemanfaatan              │
│    (Pemanfaatan BMN)            │
│    ** TABEL TERPISAH **         │
├─────────────────────────────────┤
│ • id (PK)                       │
│ • nama_mitra_penyewa            │
│ • jenis_mitra                   │
│ • status_sewa                   │
│ • biaya_sewa                    │
│ • total_pendapatan_terealisasi  │
│ • total_pendapatan_outstanding  │
│ • ...160+ kolom lainnya         │
│                                 │
│ (No FK to other tables)         │
└─────────────────────────────────┘
```

---

## 🛠️ Teknologi

### Backend

| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **PHP** | 8.2+ | Backend language |
| **Laravel** | 12.x | PHP Framework |
| **MySQL** | 8.0+ | Database (compatible with 5.7+) |
| **Composer** | 2.x | Dependency manager |

### Frontend

| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **TailwindCSS** | 4.0 | CSS Framework |
| **Vite** | 7.0 | Build tool & dev server |
| **Axios** | 1.11+ | HTTP client untuk AJAX |
| **Chart.js** | Latest | Charting library |
| **JavaScript** | ES6+ | Vanilla JS |

### Libraries & Packages

**Backend:**
- `barryvdh/laravel-dompdf` ^3.1 - PDF generation
- `phpoffice/phpspreadsheet` ^5.2 - Excel export/import
- `doctrine/dbal` ^4.3 - Database abstraction layer

**Development:**
- `laravel/pail` - Log viewer
- `laravel/tinker` - REPL console
- `laravel/sail` - Docker environment (optional)

---

## 🚀 Instalasi & Setup

### Prerequisites

Pastikan sistem Anda sudah terinstall:
- ✅ PHP 8.2 atau lebih tinggi
- ✅ Composer 2.x
- ✅ Node.js 16.x atau lebih tinggi
- ✅ MySQL 8.0+ / MariaDB 10.x
- ✅ Git

### Langkah 1: Clone Repository

```bash
git clone <repository-url> dashboard-bmn
cd dashboard-bmn
```

### Langkah 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Langkah 3: Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Langkah 4: Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bmn_dashboard
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Langkah 5: Create Database & Import SQL

**1. Buat database:**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE bmn_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**2. Import SQL files ke database `bmn_dashboard` (PENTING: ikuti urutan ini!)**

```bash
# Import tabel master bagian (untuk Modul 1 & 2)
mysql -u root -p bmn_dashboard < database/sql-script/shared/bagian.sql

# Import tabel pengajuan RKBMN (untuk Modul 1 & 2)
mysql -u root -p bmn_dashboard < database/sql-script/shared/bmn_pengajuanrkbmnbagian.sql

# Import tabel pemanfaatan (untuk Modul 3)
mysql -u root -p bmn_dashboard < database/sql-script/pemanfaatan/bmn_pemanfaatan.sql
```

**Catatan:**
- Semua tabel diimport ke **database yang sama** (`bmn_dashboard`)
- Urutan penting karena `bmn_pengajuanrkbmnbagian` memiliki foreign key ke `bagian`
- Folder `shared/` dan `pemanfaatan/` hanya untuk organisasi file, bukan database terpisah

### Langkah 6: Setup Storage

```bash
# Create symbolic link untuk storage
php artisan storage:link
```

Ini akan membuat folder `public/storage` yang link ke `storage/app/public` untuk akses file upload.

### Langkah 7: Build Assets

```bash
# Untuk development (with hot reload)
npm run dev

# Untuk production (minified)
npm run build
```

### Langkah 8: Start Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

## ⚙️ Konfigurasi

### File Upload Configuration

Edit `config/filesystems.php` jika perlu mengubah storage configuration:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### File Upload Limits

Sesuaikan di `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
```

### Session & Cache

Sistem menggunakan database untuk session dan cache. Pastikan tabel sudah ada:

```bash
php artisan session:table
php artisan cache:table
php artisan queue:table
php artisan migrate
```

---

## 📖 Panduan Penggunaan

### Modul 1: Dashboard Pengajuan RKBMN

**1. Akses Dashboard**
- Buka browser: `http://localhost:8000/`

**2. Lihat Statistik**
- Statistik cards akan menampilkan:
  - Total pengajuan
  - Pending approvals
  - Approved count
  - Rejected count
  - Total budget approved

**3. Filter Data**
- Pilih **Jenis Pengajuan** (R1-R6)
- Pilih **Bagian** dari dropdown
- Pilih **Status** (Draft/Approved/Rejected)
- Pilih **Tahun Anggaran**
- Set **Range Anggaran** (Min & Max)
- Klik **Apply Filter**

**4. Lihat Data Terfilter**
- Data akan muncul di tabel
- Support pagination
- Klik row untuk detail

---

### Modul 2: Dashboard Statistik

**1. Akses Dashboard**
- Buka: `http://localhost:8000/statistical-dashboard`

**2. View Charts**
- **Bar Chart**: Hover untuk lihat detail
- **Pie Chart**: Click segment untuk highlight
- **Line Chart**: Track trends over time

**3. Apply Filters**
- Sama seperti Dashboard Pengajuan
- Plus: Filter ATR/Non-ATR
- Plus: Filter Skema Pengadaan

**4. Export Data**

**Export to Excel:**
```
Klik tombol "Export to Excel"
→ File .xlsx akan terdownload
→ Contains all filtered data dengan formatting
```

**Export to PDF:**
```
Klik tombol "Export to PDF"
→ File .pdf akan terdownload
→ Includes charts & tables
```

---

### Modul 3: Monitoring Pemanfaatan

**1. Akses Dashboard**
- Buka: `http://localhost:8000/utilization-dashboard`

**2. Create New Utilization**

```
Klik tombol "Tambah Data Baru"
→ Modal form akan muncul
→ Fill Tab 1: Informasi Penyewa (REQUIRED)
  - PIC Penyewa & No. HP
  - PIC Administrasi BMN & No. HP
  - Nama Mitra Penyewa
  - Jenis Mitra (pilih dari dropdown)
  - Jenis Usulan (pilih dari dropdown)
→ Klik "Simpan"
→ Data tersimpan dengan status DRAFT
```

**3. Complete the 5 Stages**

**Stage 2-5:** Edit data yang sudah dibuat dan lengkapi setiap tab sesuai tahapan proses.

---

## 📁 Struktur Folder

```
dashboard-bmn/
│
├── app/
│   ├── Http/Controllers/
│   │   ├── BmnDashboardController.php          # Modul 1
│   │   ├── BmnStatisticalDashboardController.php # Modul 2
│   │   └── BmnUtilizationController.php        # Modul 3
│   └── Models/
│       ├── Bagian.php
│       ├── BmnPengajuanrkbmnbagian.php
│       └── BmnPemanfaatan.php
│
├── database/
│   └── sql-script/           # SQL Import Files
│       ├── shared/           # For Modul 1 & 2
│       │   ├── bagian.sql
│       │   └── bmn_pengajuanrkbmnbagian.sql
│       └── pemanfaatan/      # For Modul 3
│           └── bmn_pemanfaatan.sql
│
├── resources/
│   └── views/bmn/
│       ├── dashboard.blade.php              # Modul 1
│       ├── statistical_dashboard.blade.php  # Modul 2
│       └── utilization_dashboard.blade.php  # Modul 3
│
├── routes/
│   └── web.php               # Web routes
│
├── storage/app/public/uploads/
│   └── pemanfaatan/          # Upload directory
│
└── public/storage/           # Symlink ke storage/app/public
```

---

## 🔌 API Endpoints

### Modul 1: Dashboard Pengajuan RKBMN

| Method | Route | Deskripsi |
|--------|-------|-----------|
| GET | `/` | Main dashboard view |

### Modul 2: Dashboard Statistik

| Method | Route | Deskripsi |
|--------|-------|-----------|
| GET | `/statistical-dashboard` | Statistical view |
| GET | `/statistical-dashboard/export-excel` | Export to Excel |
| GET | `/statistical-dashboard/export-pdf` | Export to PDF |

### Modul 3: Monitoring Pemanfaatan

| Method | Route | Deskripsi |
|--------|-------|-----------|
| GET | `/utilization-dashboard` | List all utilization data |
| POST | `/utilization-dashboard` | Create new utilization |
| GET | `/utilization-dashboard/{id}` | Show single utilization |
| PUT | `/utilization-dashboard/{id}` | Update utilization |
| DELETE | `/utilization-dashboard/{id}` | Delete utilization |
| POST | `/utilization-dashboard/{id}/toggle-complete` | Toggle completion status |

---

## 🐛 Troubleshooting

### Issue 1: Error "Base table or view not found"

**Symptom:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'bmn_dashboard.xxx' doesn't exist
```

**Solution:**
```bash
# Pastikan semua SQL sudah diimport dengan urutan yang benar
mysql -u root -p bmn_dashboard < database/sql-script/shared/bagian.sql
mysql -u root -p bmn_dashboard < database/sql-script/shared/bmn_pengajuanrkbmnbagian.sql
mysql -u root -p bmn_dashboard < database/sql-script/pemanfaatan/bmn_pemanfaatan.sql
```

---

### Issue 2: File Upload Error

**Solution:**
```bash
# Set correct permissions
chmod -R 775 storage/
php artisan storage:link
```

---

### Issue 3: Vite Not Loading

**Solution:**
```bash
# Development
npm run dev

# Production
npm run build
```

---

## ❓ FAQ

**Q1: Apakah sistem ini support multi-user authentication?**
A: Saat ini sistem belum memiliki authentication. Sistem bersifat single-user.

**Q2: Bagaimana cara backup database?**
A:
```bash
mysqldump -u root -p bmn_dashboard > backup_$(date +%Y%m%d).sql
```

**Q3: Apakah sistem responsive?**
A: Ya, sistem menggunakan TailwindCSS yang fully responsive.

---

## 📄 License

This project is proprietary software developed for internal use.

**© 2024 - Dashboard BMN**

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/12.x)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Vite Documentation](https://vitejs.dev)
- [Chart.js Documentation](https://www.chartjs.org/docs)

---

**Selamat menggunakan Dashboard BMN! 🎉**
