# Rencana Pengembangan Fitur `/utilization-dashboard`

Berikut adalah rencana pengembangan untuk fitur tambah pemanfaatan pada `/utilization-dashboard`.

## Alur Kerja Umum

Fitur ini akan dibagi menjadi dua tahap utama:

1.  **Tahap 1: Penambahan Data Awal (Informasi Penyewa)**
    -   Pengguna menekan tombol "Tambah Pemanfaatan".
    -   Hanya form **Informasi Penyewa** yang muncul.
    -   Setelah diisi, data disimpan dan sebuah record pemanfaatan baru dibuat dengan status "Draft" atau "Belum Lengkap".
    -   Pengguna kembali ke halaman utama dashboard pemanfaatan.

2.  **Tahap 2: Melengkapi Data Rinci**
    -   Dari daftar pemanfaatan, pengguna dapat memilih record yang masih "Draft".
    -   Pengguna menekan tombol "Lengkapi Data".
    -   Pengguna akan diarahkan ke halaman dengan 4 tab tersisa ('Konfirmasi', 'Usulan Pemanfaatan', 'Penilaian KPKNL', dan 'Perjanjian') untuk mengisi sisa data secara bertahap.

---

## Rincian Field per Tahap

### **Tahap 1: Form Informasi Penyewa**

Form ini adalah satu-satunya bagian yang diisi saat pembuatan data awal.

-   **PIC Penyewa**: Text input.
-   **PIC Administrasi BMN**: Text input.
-   **Nomor HP PIC Penyewa**: Text input (nomor telepon).
-   **Nomor PIC Administrasi BMN**: Text input (nomor telepon).
-   **Nama Mitra Penyewa**: Text input.
-   **Jenis Mitra**: Dropdown dengan pilihan:
    -   Perusahaan
    -   Yayasan
    -   Koperasi
    -   Perseorangan
-   **Jenis Usulan**: Dropdown dengan pilihan:
    -   Perpanjangan
    -   Usulan Baru
-   **Keterangan/Uraian**: Text area.
-   **Peruntukan Sewa**: Text area (dibuat cukup generik untuk berbagai keperluan sewa).

### **Tahap 2: Melengkapi Data (Tab 2-5)**

#### 1. Tab: Konfirmasi

Tab ini digunakan untuk melengkapi data konfirmasi perpanjangan sewa.

-   **Nodin Konfirmasi Perpanjangan Sewa**
    -   Nomor Surat: Text input.
    -   Tanggal Surat: Date picker.
    -   Mitra dan Peruntukan Sewa: Text input (diambil dari data sebelumnya).
    -   Tanggal Berakhir Sewa: Date picker.
-   **Surat Konfirmasi Perpanjangan Sewa**
    -   Nomor Surat: Text input.
    -   Tujuan Surat: Text input.
    -   Peruntukan Sewa: Text input.
    -   Nomor Perjanjian Sewa Lama: Text input.
    -   Tanggal Berakhir: Date picker.
    -   Nama & Nomor Penanggung Jawab (Kasub): Text input.
    -   Upload Lampiran Surat Konfirmasi: File upload.
-   **Upload Dokumen Pendukung**
    -   Surat Usulan Sewa/Perpanjangan Sewa dari Mitra: File upload.
    -   NPWP: File upload.
    -   KTP Penandatangan Perjanjian: File upload.
    -   NIB: File upload.

#### 2. Tab: Usulan Pemanfaatan

Tab ini berisi detail usulan pemanfaatan BMN.

-   **Nodin Berjenjang**
    -   Mitra Penyewa: Text input.
    -   Peruntukan: Text input.
-   **Surat Usulan Sewa KPKNL**
    -   Nomor & Tanggal Surat: Text input & Date picker.
    -   Hal Surat (PT): Text input.
    -   Tujuan Surat: Text input.
    -   Isi Surat: Text area (pre-filled template: "Nama mitra: [nama], Tanggal berakhir: [tanggal]").
-   **Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)**
    -   Nomor & Tanggal Surat: Text input & Date picker.
    -   Kode Barang, NUP, Luasan Sewa, Lokasi Sewa: Text inputs.
-   **Surat Pernyataan**
    -   Nomor & Tanggal Surat: Text input & Date picker.
    -   Kode Barang, NUP, Luasan Sewa, Lokasi Sewa: Text inputs.
-   **Daftar BMN yang Diusulkan** (Tabel dinamis)
    -   Kolom: No, Kode Barang, NUP, Jenis BMN, Luas Keseluruhan BMN (m²), Nilai Perolehan BMN (Rp), Dicatat di SIMAK, Objek Sewa, Lokasi, Penyewa, Peruntukan, Usulan Luas Disewakan (m²), Usulan Jangka Waktu, Usulan Besaran Sewa (Rp).
-   **Upload Dokumen Usulan**
    -   Upload PSP, KIB, Surat Usulan Sewa/SPTJM/Surat Pernyataan (ttd): File uploads.

#### 3. Tab: Penilaian KPKNL

Tab untuk mencatat hasil penilaian dari KPKNL.

-   **Upload Dokumen Penilaian**:
    -   Jadwal Penilaian, BASL, Persetujuan KPKNL: File uploads.
-   **Nodin Penyampaian Surat Persetujuan KPKNL**
    -   Nomor & Tanggal Surat, Tujuan, Nomor & Tanggal Persetujuan, Periode Sewa, Nominal, Mitra, Nama & Nomor Penanggung Jawab (Kasub): Text/Date inputs.
-   **Surat Penyampaian Invoice**
    -   Nomor & Tanggal Surat, Tujuan, Nomor & Tanggal Persetujuan, Periode Sewa, Nominal, Mitra, Nama & Nomor Penanggung Jawab (Kasub): Text/Date inputs.
-   **Upload Kode Billing**: File upload.

#### 4. Tab: Perjanjian

Tahap akhir untuk mengunggah dokumen perjanjian.

-   **Upload Dokumen Final**:
    -   Bukti Bayar, Perjanjian Sewa: File uploads.
-   **Detail Perjanjian**
    -   Logo Penyewa (opsional), Mitra, Peruntukan, Gedung, Hari/Tanggal Penandatanganan, Detail Pihak Kedua: Text/File inputs.
-   **Nodin Permohonan Ttd Perjanjian Kepada Mitra**
    -   Nomor & Tanggal Sewa, Tujuan, Mitra, Judul Perjanjian: Text/Date inputs.
-   **Nodin Berjenjang Internal**
    -   Nomor & Tanggal Surat, Mitra, Judul Perjanjian, Nomor Perjanjian, Detail Persetujuan Sewa: Text/Date inputs.
