# 📘 Dokumentasi Lengkap Proyek Dashboard BMN

Dokumentasi ini mencakup detail teknis, struktur database, kamus data, dan diagram alur untuk sistem Dashboard BMN.

---

## 📑 Daftar Isi
1.  [Arsitektur & Diagram](#1-arsitektur--diagram)
    *   [Entity Relationship Diagram (ERD)](#erd-entity-relationship-diagram)
    *   [Class Diagram](#class-diagram)
    *   [Activity Diagram: Generate Dokumen](#activity-diagram)
2.  [Kamus Data Detail (Data Dictionary)](#2-kamus-data-detail)
    *   [Tabel Utama: `bmn_pemanfaatan`](#tabel-bmn_pemanfaatan)
    *   [Tabel Relasi: `perjanjian_sewa`](#tabel-perjanjian_sewa)
3.  [Peta Implementasi Codebase](#3-peta-implementasi-codebase)

---

## 1. Arsitektur & Diagram

### ERD (Entity Relationship Diagram)
Visualisasi hubungan antar tabel dalam database.

```
+---------------------+       1-to-1       +----------------------------------+
|                     |------------------->| surat_konfirmasi_perpanjangan_sewa|
|                     |                    |----------------------------------|
|                     |       1-to-1       +------------------+---------------+
|                     |------------------->| nodin_berjenjang |
|                     |                    +------------------+
|   bmn_pemanfaatan   |       1-to-1       +--------------------------+
|      (MASTER)       |------------------->| surat_usulan_kpknl_sptjm |
|                     |                    +--------------------------+
|                     |       1-to-1       +-------------------------+
|                     |------------------->| nodin_persetujuan_kpknl |
|                     |                    +-------------------------+
|                     |       1-to-1       +-----------------+
|                     |------------------->| perjanjian_sewa |
+----------+----------+                    +-----------------+
           |
           | 1-to-Many
           v
+---------------------+
|     daftar_bmn      |
+---------------------+
```

### Class Diagram
Struktur Model dan Relasi dalam Laravel (Eloquent).

```
+------------------+
|  BmnPemanfaatan  |
+------------------+
| id               |<>--------(hasOne)--------+ PerjanjianSewa
| status_sewa      |<>--------(hasOne)--------+ SuratKonfirmasiPerpanjanganSewa
| pic_penyewa      |<>--------(hasOne)--------+ NodinBerjenjang
| is_complete      |<>--------(hasOne)--------+ SuratUsulanKpknlSptjm
| ...              |<>--------(hasMany)-------+ DaftarBmn
+------------------+
        ^
        | (Uses)
+--------------------------+
| BmnUtilizationController |
+--------------------------+
| index()                  |
| store()                  |
| uploadDocuments()        |
| saveDocumentData()       |
+--------------------------+
```

### Activity Diagram
Alur proses **Generate Dokumen** (misal: Surat Konfirmasi).

```
[ USER ]                [ SYSTEM / CONTROLLER ]          [ DATABASE ]
   |                               |                          |
   +--- Klik "Generate" ---------->|                          |
                                   |-- Request Data ID:49 --->|
                                   |                          |-- Query Relasi (withDocuments) -->
                                   |<-- Return Model Data ----|
                                   |                          |
                                   |-- Load Template (.docx) -|
                                   |-- Replace ${variable} ---|
                                   |-- Save Temporary File ---|
                                   |                          |
   |<-- Download File (.docx) -----|                          |
```

---

## 2. Kamus Data Detail

Bagian ini menjelaskan setiap field, tipe datanya, dan di mana field tersebut digunakan dalam aplikasi (Controller, View, Form).

### Tabel: `bmn_pemanfaatan`
Tabel induk yang menyimpan status utama dan data umum penyewa.

| Nama Field | Tipe Data | Deskripsi | Penggunaan di View (Form/Modal) | Penggunaan di Controller |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | BIGINT (PK) | Primary Key | Hidden Input (`#edit_id`) | Parameter Route, `findOrFail($id)` |
| **`status_sewa`** | VARCHAR | Status workflow (draft, active, dll) | Badge Status di Dashboard | Filter `scopeActive`, `scopeDraft` |
| **`is_complete`** | BOOLEAN | Penanda kelengkapan data | Statistik "Data Lengkap" | `checkCompleteness()` |
| **`pic_penyewa`** | VARCHAR | Nama Person In Charge (Penyewa) | Modal "Informasi Penyewa" > Input PIC | `update()` |
| **`nomor_hp_pic_penyewa`** | VARCHAR | No HP PIC | Modal "Informasi Penyewa" > Input No HP | `update()` |
| **`nama_mitra_penyewa`** | VARCHAR | Nama Perusahaan/Instansi Mitra | Modal "Informasi Penyewa" > Input Mitra | `update()`, Ditampilkan di Tabel Dashboard |
| **`jenis_mitra`** | VARCHAR | Jenis (Perorangan/Badan Usaha) | Modal "Informasi Penyewa" > Dropdown | `update()` |
| **`peruntukan_sewa`** | VARCHAR | Tujuan sewa (mis: Kantin, ATM) | Modal "Informasi Penyewa" > Input Peruntukan | `update()` |
| **`surat_konfirmasi_nomor`** | VARCHAR | Nomor Surat Konfirmasi | Modal "Surat Konfirmasi" | `saveDocumentData('surat-konfirmasi')` |
| **`surat_konfirmasi_tanggal`** | DATE | Tanggal Surat Konfirmasi | Modal "Surat Konfirmasi" | `saveDocumentData('surat-konfirmasi')` |
| **`dokumen_ktp_penandatangan`**| VARCHAR | Path file KTP (Upload) | Modal "Lengkapi Data" > Tab 1 | `uploadDocuments()` |
| **`dokumen_npwp`** | VARCHAR | Path file NPWP (Upload) | Modal "Lengkapi Data" > Tab 1 | `uploadDocuments()` |
| **`dokumen_psp`** | VARCHAR | Path file PSP (Upload) | Modal "Lengkapi Data" > Tab 2 | `uploadDocuments()` |
| **`surat_invoice_nomor`** | VARCHAR | Nomor Invoice/Tagihan | Modal "Surat Invoice" | `saveDocumentData('surat-invoice')` |
| **`surat_invoice_nominal`** | DECIMAL | Nominal Tagihan | Modal "Surat Invoice" | `saveDocumentData('surat-invoice')` |

### Tabel: `perjanjian_sewa`
Tabel anak yang menyimpan detail kontrak spesifik. **Penting:** Data pendapatan diambil dari sini.

| Nama Field | Tipe Data | Deskripsi | Penggunaan di View (Form/Modal) | Penggunaan di Controller |
| :--- | :--- | :--- | :--- | :--- |
| **`pemanfaatan_id`** | INT (FK) | Foreign Key | - | Relasi `$utilization->perjanjianSewa` |
| **`nomor_surat`** | VARCHAR | Nomor Perjanjian (PKS) | Modal "Perjanjian Sewa" > Input Nomor | `saveDocumentData('perjanjian-sewa')` |
| **`tanggal_surat`** | DATE | Tanggal TTD Perjanjian | Modal "Perjanjian Sewa" > Input Tanggal | `saveDocumentData('perjanjian-sewa')` |
| **`mitra_penyewa`** | VARCHAR | Nama Pihak Kedua (Mitra) | Modal "Perjanjian Sewa" > Input Mitra | `saveDocumentData('perjanjian-sewa')` |
| **`nilai_sewa`** | DECIMAL | Nilai Sewa dalam Kontrak | Modal "Perjanjian Sewa" > Input Nilai | `saveDocumentData('perjanjian-sewa')` |
| **`objek_gedung`** | VARCHAR | Nama Gedung/Lokasi | Modal "Perjanjian Sewa" > Input Gedung | `saveDocumentData('perjanjian-sewa')` |
| **`objek_luas`** | DOUBLE | Luas (m2) | Modal "Perjanjian Sewa" > Input Luas | `saveDocumentData('perjanjian-sewa')` |
| **`dokumen_perjanjian`** | VARCHAR | Path File Perjanjian (PDF) | Modal "Lengkapi Data" > Tab 4 (Final) | `uploadDocuments()` (Disimpan ke tabel ini) |
| **`dokumen_bukti_bayar`** | VARCHAR | Path File Bukti Bayar | Modal "Lengkapi Data" > Tab 4 (Final) | `uploadDocuments()` (Disimpan ke tabel ini) |
| **`nilai_pendapatan_bukti_bayar`**| DECIMAL | **Realisasi Pendapatan** | Modal "Lengkapi Data" > Input Nominal | `uploadDocuments()`, Statistik Dashboard |

---

## 3. Peta Implementasi Codebase

### A. Controller: `BmnUtilizationController.php`
Mengatur logika bisnis utama.

*   **`index()`**:
    *   Mengambil data `BmnPemanfaatan` dengan eager loading (`withDocuments`).
    *   Menghitung notifikasi sewa yang akan berakhir (H-30).
    *   Return view: `utilization.dashboard`.
*   **`store(Request $request)`**:
    *   Menangani input data baru ("Tambah Data").
    *   Validasi input dasar (PIC, Mitra, dll).
    *   Create record baru di `bmn_pemanfaatan`.
*   **`uploadDocuments(Request $request, $id)`**:
    *   Menangani upload file dari Modal "Lengkapi Data".
    *   **Logika Penting**: Memisahkan file mana yang masuk ke `bmn_pemanfaatan` dan mana yang masuk ke `perjanjian_sewa`.
    *   Mengupdate status `is_complete`.
*   **`saveDocumentData(Request $request, $id, $type)`**:
    *   Menangani penyimpanan data form dari modal-modal spesifik (Surat Konfirmasi, Nodin, Perjanjian).
    *   Menggunakan `switch($type)` untuk menentukan tabel tujuan penyimpanan.
    *   Contoh: Jika `$type == 'perjanjian-sewa'`, simpan ke model `PerjanjianSewa`.

### B. Controller: `BmnDocumentController.php`
Mengatur logika generasi dokumen Word.

*   **`generate(Request $request, $id, $type)`**:
    *   Fungsi generik untuk membuat dokumen.
    *   Load template dari `resources/template/template-[type].docx`.
    *   Mapping data dari database ke variabel template (misal: `$data->nomor_surat` ke `${nomor_surat}`).
    *   Menggunakan library `PhpWord`.

### C. View: `dashboard.blade.php`
Tampilan utama.

*   **Tabel Data**: Menggunakan DataTables (custom JS) untuk menampilkan list pemanfaatan.
*   **Statistik**: Menghitung total pendapatan secara real-time menggunakan JS (`updateStats`), mengambil data dari `nilai_pendapatan_bukti_bayar`.

### D. View: `documents.blade.php`
Berisi semua Modal Form dan logika Frontend.

*   **Modal**: Setiap jenis dokumen (Konfirmasi, Nodin, Perjanjian) memiliki modal ID sendiri (misal `#modal-perjanjian-sewa`).
*   **JavaScript**:
    *   `initFormHandlers()`: Menangani submit form via AJAX.
    *   `updateButtonStatuses()`: Mengecek kelengkapan data untuk mengubah warna tombol (Kuning -> Hijau).
    *   `uploadDocuments()`: Mengirim file via AJAX `FormData`.

---
*Dokumentasi ini diperbarui terakhir pada: 29 November 2025*
