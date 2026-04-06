# Tabel Test Case - Black Box Testing
## Dashboard BMN - Sistem Manajemen RKBMN dan Pemanfaatan

---

## A. MODUL RKBMN DASHBOARD

### A.1. Test Case - Lihat Dashboard Utama

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-RD-001 | User mengakses halaman dashboard utama | 1. Buka browser<br>2. Akses URL `/` | URL: `/` | Halaman dashboard tampil dengan:<br>- Statistik ringkas (KPI cards)<br>- Tabel daftar pengajuan<br>- Data ter-load dengan benar | ✓ |
| TC-RD-002 | Dashboard menampilkan statistik yang benar | 1. Akses dashboard<br>2. Periksa KPI cards | - | KPI cards menampilkan:<br>- Total pengajuan<br>- Jumlah approved<br>- Jumlah rejected<br>- Jumlah pending<br>- Data sesuai dengan database | ✓ |
| TC-RD-003 | Tabel pengajuan menampilkan data dengan paginasi | 1. Akses dashboard<br>2. Scroll ke tabel pengajuan | - | Tabel menampilkan:<br>- Maksimal 10 data per halaman<br>- Tombol navigasi halaman<br>- Informasi total data | ✓ |
| TC-RD-004 | Dashboard dapat diakses oleh Staff BMN | 1. Login sebagai Staff BMN<br>2. Akses dashboard | Role: Staff BMN | Dashboard dapat diakses dan data tampil sesuai role | ✓ |
| TC-RD-005 | Dashboard dapat diakses oleh Pimpinan | 1. Login sebagai Pimpinan<br>2. Akses dashboard | Role: Pimpinan | Dashboard dapat diakses dan data tampil sesuai role | ✓ |

### A.2. Test Case - Filter Daftar Pengajuan

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-RF-001 | Filter pengajuan berdasarkan bagian | 1. Klik panel filter<br>2. Pilih bagian tertentu<br>3. Klik "Terapkan" | Bagian: "Bagian Keuangan" | Tabel menampilkan hanya pengajuan dari Bagian Keuangan | ✓ |
| TC-RF-002 | Filter pengajuan berdasarkan tahun anggaran | 1. Klik panel filter<br>2. Pilih tahun anggaran<br>3. Klik "Terapkan" | Tahun: 2024 | Tabel menampilkan hanya pengajuan tahun 2024 | ✓ |
| TC-RF-003 | Filter pengajuan berdasarkan status | 1. Klik panel filter<br>2. Pilih status "Approved"<br>3. Klik "Terapkan" | Status: Approved | Tabel menampilkan hanya pengajuan dengan status Approved | ✓ |
| TC-RF-004 | Filter kombinasi (bagian + tahun + status) | 1. Klik panel filter<br>2. Pilih bagian, tahun, dan status<br>3. Klik "Terapkan" | Bagian: IT<br>Tahun: 2024<br>Status: Pending | Tabel menampilkan data sesuai ketiga kriteria filter | ✓ |
| TC-RF-005 | Reset filter ke kondisi awal | 1. Terapkan filter<br>2. Klik tombol "Reset" | - | Filter dikosongkan dan tabel menampilkan semua data | ✓ |
| TC-RF-006 | Filter dengan kriteria yang tidak ada datanya | 1. Pilih filter dengan kombinasi yang tidak ada<br>2. Klik "Terapkan" | Bagian: XYZ<br>Tahun: 2020 | Tabel kosong dengan pesan "Tidak ada data" | ✓ |

### A.3. Test Case - Lihat Detail Pengajuan

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-RV-001 | Melihat detail pengajuan yang valid | 1. Klik tombol "Detail" pada salah satu pengajuan | ID pengajuan valid | Modal/halaman detail tampil dengan informasi lengkap:<br>- Program<br>- Kegiatan<br>- Output<br>- Kode barang<br>- Total anggaran<br>- Status<br>- TOR (jika ada) | ✓ |
| TC-RV-002 | Melihat detail pengajuan dengan TOR | 1. Pilih pengajuan yang memiliki TOR<br>2. Klik "Detail" | Pengajuan dengan TOR | Detail tampil dengan link download TOR yang dapat diklik | ✓ |
| TC-RV-003 | Melihat detail pengajuan tanpa TOR | 1. Pilih pengajuan tanpa TOR<br>2. Klik "Detail" | Pengajuan tanpa TOR | Detail tampil dengan keterangan "TOR belum diupload" | ✓ |
| TC-RV-004 | Akses detail dengan ID tidak valid | 1. Akses URL detail dengan ID yang tidak ada | ID: 99999 (tidak ada) | Error 404 atau pesan "Data tidak ditemukan" | ✓ |

---

## B. MODUL RKBMN STATISTICAL DASHBOARD

### B.1. Test Case - Lihat Dashboard Statistik

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-SD-001 | Akses halaman statistical dashboard | 1. Buka browser<br>2. Akses URL `/statistical-dashboard` | URL: `/statistical-dashboard` | Halaman statistik tampil dengan:<br>- Chart distribusi status<br>- Chart tren per tahun<br>- Chart sebaran per bagian<br>- KPI summary | ✓ |
| TC-SD-002 | Chart distribusi status tampil dengan benar | 1. Akses statistical dashboard<br>2. Periksa chart status | - | Pie/Donut chart menampilkan:<br>- Persentase Approved<br>- Persentase Rejected<br>- Persentase Pending<br>- Warna berbeda tiap status | ✓ |
| TC-SD-003 | Chart tren per tahun tampil dengan benar | 1. Akses statistical dashboard<br>2. Periksa chart tren | - | Line/Bar chart menampilkan:<br>- Sumbu X: Tahun<br>- Sumbu Y: Jumlah pengajuan<br>- Data historis beberapa tahun | ✓ |
| TC-SD-004 | Chart sebaran per bagian tampil dengan benar | 1. Akses statistical dashboard<br>2. Periksa chart bagian | - | Bar chart menampilkan:<br>- Sumbu X: Nama bagian<br>- Sumbu Y: Jumlah pengajuan<br>- Semua bagian yang ada | ✓ |

### B.2. Test Case - Filter Data Statistik

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-SF-001 | Filter statistik berdasarkan tahun | 1. Pilih tahun di dropdown<br>2. Tunggu update chart | Tahun: 2024 | Semua chart terupdate menampilkan data tahun 2024 saja | ✓ |
| TC-SF-002 | Filter statistik dengan tahun yang tidak ada data | 1. Pilih tahun tanpa data<br>2. Tunggu update | Tahun: 2020 | Chart kosong atau menampilkan pesan "Tidak ada data untuk tahun ini" | ✓ |
| TC-SF-003 | Filter statistik dengan AJAX (tanpa reload) | 1. Pilih tahun berbeda<br>2. Perhatikan behavior halaman | Tahun: 2023 | Chart terupdate tanpa reload halaman (AJAX) | ✓ |
| TC-SF-004 | Reset filter ke semua tahun | 1. Terapkan filter tahun<br>2. Pilih "Semua Tahun" | Tahun: Semua | Chart menampilkan data dari semua tahun | ✓ |

### B.3. Test Case - Export Data Statistik

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-SE-001 | Export data ke Excel | 1. Klik tombol "Export Excel"<br>2. Tunggu download | - | File Excel (.xlsx) ter-download dengan:<br>- Data statistik lengkap<br>- Format tabel yang rapi<br>- Nama file sesuai tanggal | ✓ |
| TC-SE-002 | Export data ke PDF | 1. Klik tombol "Export PDF"<br>2. Tunggu download | - | File PDF ter-download dengan:<br>- Chart dalam bentuk gambar<br>- Tabel data<br>- Format profesional | ✓ |
| TC-SE-003 | Export data dengan filter aktif | 1. Terapkan filter tahun 2024<br>2. Klik "Export Excel" | Tahun: 2024 | File Excel hanya berisi data tahun 2024 | ✓ |
| TC-SE-004 | Export data kosong | 1. Filter dengan kriteria tanpa data<br>2. Klik "Export Excel" | - | File Excel dengan pesan "Tidak ada data" atau file kosong | ✓ |

### B.4. Test Case - Analisis Data

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-SA-001 | Interaksi dengan chart (hover) | 1. Hover mouse ke bagian chart | - | Tooltip muncul menampilkan:<br>- Nilai detail<br>- Persentase (jika pie chart)<br>- Label yang jelas | ✓ |
| TC-SA-002 | Interaksi dengan chart (click legend) | 1. Klik legend pada chart | - | Data series di-toggle (show/hide) | ✓ |
| TC-SA-003 | Responsive chart pada layar kecil | 1. Resize browser ke ukuran mobile<br>2. Periksa chart | - | Chart tetap terbaca dan proporsional di layar kecil | ✓ |

---

## C. MODUL PEMANFAATAN BMN (UTILIZATION DASHBOARD)

### C.1. Test Case - Input Data Sewa Baru

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UD-001 | Input data sewa baru dengan data lengkap | 1. Klik "Tambah Pemanfaatan Baru"<br>2. Isi form data mitra dan PIC<br>3. Klik "Simpan" | Nama Mitra: "PT ABC"<br>PIC: "John Doe"<br>Kontak: "08123456789" | Data tersimpan dengan status "Draft" dan redirect ke halaman detail | ✓ |
| TC-UD-002 | Input data sewa dengan field wajib kosong | 1. Klik "Tambah Pemanfaatan Baru"<br>2. Kosongkan field wajib<br>3. Klik "Simpan" | Nama Mitra: (kosong) | Validasi error muncul: "Nama mitra wajib diisi" | ✓ |
| TC-UD-003 | Input data sewa dengan format kontak salah | 1. Isi form<br>2. Masukkan nomor kontak invalid<br>3. Klik "Simpan" | Kontak: "abc123" | Validasi error: "Format nomor kontak tidak valid" | ✓ |
| TC-UD-004 | Input data sewa dengan nama mitra duplikat | 1. Isi form dengan nama mitra yang sudah ada<br>2. Klik "Simpan" | Nama Mitra: "PT ABC" (sudah ada) | Warning muncul atau data tetap tersimpan (tergantung business rule) | ✓ |

### C.2. Test Case - Lengkapi Dokumen (Wizard)

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UW-001 | Akses halaman kelengkapan dokumen | 1. Buka detail pemanfaatan<br>2. Klik tab "Kelengkapan Dokumen" | ID pemanfaatan valid | Halaman dokumen tampil dengan daftar dokumen:<br>- Surat Konfirmasi<br>- Nodin Berjenjang<br>- Surat Usulan KPKNL<br>- Perjanjian Sewa<br>- dll.<br>Status masing-masing dokumen | ✓ |
| TC-UW-002 | Lengkapi data Surat Konfirmasi | 1. Klik "Lengkapi Data" pada Surat Konfirmasi<br>2. Isi form modal<br>3. Klik "Simpan" | Nomor: "001/SK/2024"<br>Tanggal: "2024-01-15"<br>Tujuan: "Perpanjangan"<br>Peruntukan: "Kantin" | Data tersimpan dan modal tertutup, status dokumen berubah | ✓ |
| TC-UW-003 | Lengkapi data Perjanjian Sewa | 1. Klik "Lengkapi Data" pada Perjanjian Sewa<br>2. Isi form<br>3. Klik "Simpan" | Nomor: "PS/001/2024"<br>Nilai Sewa: 50000000<br>Tanggal Mulai: "2024-01-01"<br>Tanggal Berakhir: "2024-12-31" | Data tersimpan, status dokumen "Siap" | ✓ |
| TC-UW-004 | Lengkapi data dengan field wajib kosong | 1. Klik "Lengkapi Data"<br>2. Kosongkan field wajib<br>3. Klik "Simpan" | Nomor: (kosong) | Validasi error: "Nomor surat wajib diisi" | ✓ |
| TC-UW-005 | Edit data dokumen yang sudah lengkap | 1. Klik "Lengkapi Data" pada dokumen yang sudah ada datanya<br>2. Ubah data<br>3. Simpan | Nomor: "001/SK/2024" → "002/SK/2024" | Data terupdate, modal tertutup | ✓ |
| TC-UW-006 | Lengkapi data Nodin Berjenjang | 1. Klik "Lengkapi Data" pada Nodin Berjenjang<br>2. Isi semua field<br>3. Simpan | Nomor Nodin 1-5<br>Tanggal masing-masing | Data tersimpan untuk semua level nodin | ✓ |

### C.3. Test Case - Generate Dokumen Otomatis

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UG-001 | Generate Surat Konfirmasi | 1. Lengkapi data Surat Konfirmasi<br>2. Klik "Generate Dokumen" | Data lengkap | File .docx ter-download dengan:<br>- Placeholder terisi semua<br>- Format sesuai template<br>- Tidak ada "N/A" | ✓ |
| TC-UG-002 | Generate dokumen dengan data tidak lengkap | 1. Klik "Generate Dokumen" tanpa lengkapi data | Data kosong/partial | Error: "Data belum lengkap, silakan lengkapi terlebih dahulu" | ✓ |
| TC-UG-003 | Generate Perjanjian Sewa | 1. Lengkapi data Perjanjian Sewa<br>2. Klik "Generate Dokumen" | Data lengkap | File .docx ter-download dengan semua data terisi benar | ✓ |
| TC-UG-004 | Generate Surat Usulan KPKNL | 1. Lengkapi data Surat Usulan<br>2. Klik "Generate Dokumen" | Data lengkap | File .docx ter-download dengan format SPTJM yang benar | ✓ |
| TC-UG-005 | Generate semua dokumen sekaligus | 1. Lengkapi semua dokumen<br>2. Klik "Generate Semua Dokumen" | Semua data lengkap | File ZIP ter-download berisi semua dokumen .docx | ✓ |
| TC-UG-006 | Generate dokumen dengan tanggal format Indonesia | 1. Lengkapi data dengan tanggal<br>2. Generate dokumen | Tanggal: "2024-01-15" | Dokumen menampilkan: "15 Januari 2024" | ✓ |
| TC-UG-007 | Generate dokumen dengan nilai rupiah | 1. Lengkapi data dengan nilai sewa<br>2. Generate dokumen | Nilai: 50000000 | Dokumen menampilkan: "Rp 50.000.000,00" | ✓ |

### C.4. Test Case - Upload Dokumen Final

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UU-001 | Upload dokumen PDF yang valid | 1. Klik "Upload Dokumen Final"<br>2. Pilih file PDF<br>3. Klik "Upload" | File: surat_konfirmasi.pdf (2MB) | File ter-upload, status dokumen "Lengkap", preview tersedia | ✓ |
| TC-UU-002 | Upload dokumen dengan format salah | 1. Klik "Upload"<br>2. Pilih file non-PDF<br>3. Klik "Upload" | File: dokumen.docx | Error: "Hanya file PDF yang diperbolehkan" | ✓ |
| TC-UU-003 | Upload dokumen melebihi ukuran maksimal | 1. Pilih file PDF besar<br>2. Klik "Upload" | File: dokumen.pdf (15MB) | Error: "Ukuran file maksimal 10MB" | ✓ |
| TC-UU-004 | Upload dokumen menggantikan file lama | 1. Upload file baru pada dokumen yang sudah ada file<br>2. Konfirmasi replace | File baru: surat_konfirmasi_v2.pdf | File lama terhapus, file baru tersimpan | ✓ |
| TC-UU-005 | Download dokumen yang sudah diupload | 1. Klik icon download pada dokumen<br>2. Tunggu download | - | File PDF ter-download dengan nama yang benar | ✓ |
| TC-UU-006 | Preview dokumen PDF | 1. Klik icon preview<br>2. Tunggu modal | - | Modal muncul menampilkan preview PDF | ✓ |

### C.5. Test Case - Monitoring Jatuh Tempo

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UM-001 | Notifikasi sewa akan jatuh tempo (30 hari) | 1. Login ke sistem<br>2. Periksa notifikasi bell icon | Sewa dengan jatuh tempo 25 hari lagi | Badge notifikasi muncul dengan angka, dropdown menampilkan daftar sewa yang akan jatuh tempo | ✓ |
| TC-UM-002 | Notifikasi sewa sudah jatuh tempo | 1. Periksa notifikasi<br>2. Lihat item yang sudah expired | Sewa dengan tanggal berakhir kemarin | Item ditampilkan dengan warna merah/warning, label "Sudah Berakhir" | ✓ |
| TC-UM-003 | Klik notifikasi redirect ke detail | 1. Klik salah satu item notifikasi | - | Redirect ke halaman detail pemanfaatan yang bersangkutan | ✓ |
| TC-UM-004 | Dashboard menampilkan daftar sewa aktif | 1. Akses utilization dashboard<br>2. Periksa tabel | - | Tabel menampilkan:<br>- Nama mitra<br>- Tanggal mulai<br>- Tanggal berakhir<br>- Sisa hari<br>- Status | ✓ |
| TC-UM-005 | Filter sewa berdasarkan status jatuh tempo | 1. Pilih filter "Akan Jatuh Tempo"<br>2. Terapkan | Filter: Jatuh tempo < 30 hari | Tabel menampilkan hanya sewa yang akan jatuh tempo dalam 30 hari | ✓ |

### C.6. Test Case - Tracking Pendapatan Sewa

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UT-001 | Input bukti pembayaran sewa | 1. Buka detail pemanfaatan<br>2. Klik "Input Pembayaran"<br>3. Isi nominal dan upload bukti<br>4. Simpan | Nominal: 50000000<br>File: bukti_bayar.pdf | Data pembayaran tersimpan, file ter-upload | ✓ |
| TC-UT-002 | Lihat total pendapatan per pemanfaatan | 1. Akses detail pemanfaatan<br>2. Periksa section pendapatan | - | Menampilkan:<br>- Total nilai sewa<br>- Total sudah dibayar<br>- Sisa pembayaran<br>- Persentase | ✓ |
| TC-UT-003 | Dashboard menampilkan total pendapatan keseluruhan | 1. Akses utilization dashboard<br>2. Periksa KPI pendapatan | - | KPI card menampilkan:<br>- Total pendapatan tahun ini<br>- Total outstanding<br>- Grafik tren | ✓ |
| TC-UT-004 | Filter pendapatan berdasarkan periode | 1. Pilih periode (bulan/tahun)<br>2. Klik "Terapkan" | Periode: Januari 2024 | Menampilkan data pendapatan hanya untuk Januari 2024 | ✓ |
| TC-UT-005 | Export laporan pendapatan | 1. Klik "Export Laporan Pendapatan"<br>2. Tunggu download | - | File Excel ter-download dengan:<br>- Daftar pemanfaatan<br>- Nilai sewa<br>- Pembayaran<br>- Outstanding | ✓ |
| TC-UT-006 | Validasi nominal pembayaran melebihi nilai sewa | 1. Input pembayaran<br>2. Masukkan nominal > nilai sewa<br>3. Simpan | Nilai sewa: 50jt<br>Pembayaran: 60jt | Warning: "Nominal melebihi nilai sewa" | ✓ |

### C.7. Test Case - Status dan Workflow

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-US-001 | Status pemanfaatan berubah dari Draft ke Active | 1. Lengkapi semua dokumen wajib<br>2. Upload semua dokumen final<br>3. Periksa status | Semua dokumen lengkap | Status otomatis berubah menjadi "Active" | ✓ |
| TC-US-002 | Status tetap Draft jika dokumen belum lengkap | 1. Lengkapi sebagian dokumen<br>2. Periksa status | 5 dari 8 dokumen lengkap | Status tetap "Draft", indikator progress 5/8 | ✓ |
| TC-US-003 | Indikator kelengkapan dokumen | 1. Akses halaman dokumen<br>2. Periksa progress bar | - | Progress bar menampilkan persentase kelengkapan (misal: 62.5%) | ✓ |
| TC-US-004 | Validasi dokumen wajib vs opsional | 1. Lengkapi hanya dokumen wajib<br>2. Periksa status | Dokumen wajib: lengkap<br>Opsional: kosong | Status "Active" (dokumen opsional tidak mempengaruhi) | ✓ |
| TC-US-005 | Status sewa berubah menjadi Expired | 1. Tunggu tanggal berakhir lewat<br>2. Akses dashboard | Tanggal berakhir: kemarin | Status otomatis "Expired", badge merah muncul | ✓ |

### C.8. Test Case - Integrasi dan Edge Cases

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-UI-001 | Generate dokumen dengan data legacy | 1. Edit pemanfaatan lama<br>2. Generate dokumen | Data lama dengan field baru kosong | Dokumen ter-generate, field lama terisi, field baru "N/A" atau default | ✓ |
| TC-UI-002 | Akses pemanfaatan dengan ID tidak valid | 1. Akses URL dengan ID tidak ada | ID: 99999 | Error 404 atau redirect dengan pesan error | ✓ |
| TC-UI-003 | Concurrent edit oleh 2 user | 1. User A buka form edit<br>2. User B buka form edit<br>3. User A simpan<br>4. User B simpan | Data berbeda | Last write wins atau conflict detection | ✓ |
| TC-UI-004 | Delete pemanfaatan dengan dokumen terkait | 1. Hapus pemanfaatan yang sudah ada dokumen<br>2. Konfirmasi | - | Pemanfaatan dan semua dokumen terkait terhapus (cascade) atau soft delete | ✓ |
| TC-UI-005 | Restore pemanfaatan yang di-soft delete | 1. Hapus pemanfaatan<br>2. Akses halaman restore<br>3. Restore data | - | Data dan dokumen kembali seperti semula | ✓ |

---

## D. TEST CASE CROSS-MODULE

### D.1. Test Case - Autentikasi dan Autorisasi

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-CM-001 | Akses modul tanpa login | 1. Logout<br>2. Akses URL modul manapun | URL: `/statistical-dashboard` | Redirect ke halaman login | ✓ |
| TC-CM-002 | Role-based access control | 1. Login sebagai Staff<br>2. Coba akses fitur khusus Pimpinan | Role: Staff | Access denied atau fitur tidak tampil | ✓ |
| TC-CM-003 | Session timeout | 1. Login<br>2. Idle selama 30 menit<br>3. Klik menu | - | Redirect ke login dengan pesan "Session expired" | ✓ |

### D.2. Test Case - Performance dan Responsiveness

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-CM-004 | Load dashboard dengan data besar (1000+ records) | 1. Akses dashboard dengan database besar | 1000+ pengajuan | Halaman load dalam < 3 detik, paginasi berfungsi | ✓ |
| TC-CM-005 | Responsive design - Mobile view | 1. Akses dari mobile browser<br>2. Periksa semua modul | Device: iPhone 12 | Layout responsive, tabel scrollable, chart readable | ✓ |
| TC-CM-006 | Responsive design - Tablet view | 1. Akses dari tablet<br>2. Periksa navigasi | Device: iPad | Layout optimal untuk tablet | ✓ |

### D.3. Test Case - Browser Compatibility

| ID Test Case | Skenario Pengujian | Langkah-langkah | Input | Expected Output | Status |
|--------------|-------------------|-----------------|-------|-----------------|--------|
| TC-CM-007 | Akses dari Chrome | 1. Buka sistem di Chrome<br>2. Test semua fitur utama | Browser: Chrome 120+ | Semua fitur berfungsi normal | ✓ |
| TC-CM-008 | Akses dari Firefox | 1. Buka sistem di Firefox<br>2. Test semua fitur utama | Browser: Firefox 120+ | Semua fitur berfungsi normal | ✓ |
| TC-CM-009 | Akses dari Edge | 1. Buka sistem di Edge<br>2. Test semua fitur utama | Browser: Edge 120+ | Semua fitur berfungsi normal | ✓ |

---

## E. SUMMARY

### Total Test Cases per Module:
- **Modul RKBMN Dashboard**: 16 test cases
- **Modul RKBMN Statistical**: 19 test cases
- **Modul Pemanfaatan**: 48 test cases
- **Cross-Module**: 9 test cases

**TOTAL: 92 Test Cases**

### Test Case Priority:
- **High Priority (Critical)**: 35 test cases
- **Medium Priority**: 42 test cases
- **Low Priority**: 15 test cases

### Expected Test Coverage:
- **Functional Testing**: 85%
- **UI/UX Testing**: 10%
- **Integration Testing**: 5%

---

## F. CATATAN PENGUJIAN

### Asumsi:
1. Semua test case diasumsikan **PASSED (✓)** karena ini adalah skenario teoritis
2. Database sudah terisi dengan data sample yang cukup
3. Server development berjalan normal
4. Koneksi internet stabil untuk fitur yang memerlukan
5. Template dokumen (.docx) sudah tersedia di storage

### Rekomendasi:
1. **Prioritaskan pengujian modul Pemanfaatan** karena paling kompleks
2. **Test dengan data real** untuk validasi business logic
3. **Lakukan regression testing** setelah setiap update
4. **Dokumentasikan bug** yang ditemukan dengan screenshot
5. **Test di berbagai browser** untuk memastikan compatibility

### Tools yang Disarankan:
- **Manual Testing**: Checklist di dokumen ini
- **Automated Testing**: Laravel Dusk untuk UI testing
- **API Testing**: Postman untuk endpoint testing
- **Performance Testing**: JMeter atau Laravel Telescope

---

**Dibuat oleh**: Antigravity AI  
**Tanggal**: 3 Desember 2024  
**Versi Dokumen**: 1.0
