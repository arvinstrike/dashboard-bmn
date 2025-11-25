# 📁 Template Folder - Dashboard BMN

Folder ini berisi template Word (.docx) untuk generate dokumen otomatis menggunakan **PHPWord TemplateProcessor**.

---

## 🚀 Cara Kerja Sistem Generate Dokumen

### Flow Proses:
1. **User** klik tombol "Generate" di halaman `/utilization-dashboard/{id}/documents`
2. **Controller** (`BmnDocumentController.php`) memproses request:
   - Load template DOCX dari folder `resources/template/`
   - Ambil data dari database (`BmnPemanfaatan` model)
   - Replace placeholder di template dengan data actual
   - Generate file DOCX baru
   - Return file untuk di-download
3. **PHPWord TemplateProcessor** melakukan replacement:
   - Placeholder di template: `${VARIABLE}`
   - Controller define: `{{VARIABLE}}` → di-strip jadi `VARIABLE`
   - TemplateProcessor: `setValue('VARIABLE', value)` → replace `${VARIABLE}` di DOCX

### Teknologi:
- **Library**: PHPOffice/PHPWord (TemplateProcessor)
- **Format Placeholder di Template**: `${VARIABLE}` (UPPERCASE)
- **Format di Controller**: `{{VARIABLE}}` (di-strip saat processing)
- **Output**: DOCX file (bisa dibuka di Microsoft Word / LibreOffice)

---

## 📄 Template yang Tersedia

Sistem mendukung **12 jenis dokumen** yang dikelompokkan dalam 4 tahapan:

### 1️⃣ Tahap Konfirmasi (2 dokumen)
- `surat_konfirmasi.docx` - Surat Konfirmasi Perpanjangan Sewa
- `nodin_konfirmasi.docx` - Nodin Konfirmasi Perpanjangan Sewa

### 2️⃣ Tahap Usulan (5 dokumen)
- `nodin_berjenjang.docx` - Nodin Berjenjang
- `surat_usulan_kpknl.docx` - Surat Usulan Sewa KPKNL
- `sptjm.docx` - SPTJM (Surat Pernyataan Tanggung Jawab Mutlak)
- `surat_pernyataan.docx` - Surat Pernyataan
- `daftar_bmn.docx` - Daftar BMN (TODO: belum diimplementasi)

### 3️⃣ Tahap Penilaian KPKNL (2 dokumen)
- `nodin_persetujuan_kpknl.docx` - Nodin Persetujuan KPKNL (TODO)
- `surat_invoice.docx` - Surat Invoice

### 4️⃣ Tahap Perjanjian (3 dokumen)
- `nodin_ttd.docx` - Nodin TTD (Permohonan TTD Perjanjian)
- `nodin_internal.docx` - Nodin Internal (Berjenjang Internal)
- `perjanjian.docx` - Perjanjian Sewa

---

## 🔧 Placeholder yang Tersedia

### Placeholder UMUM (tersedia di semua template):

| Placeholder | Nilai dari Database | Contoh Output |
|------------|---------------------|---------------|
| `${TANGGAL}` | Tanggal hari ini | 19 November 2025 |
| `${NAMA_MITRA}` | nama_mitra_penyewa | PT. Maju Jaya |
| `${JENIS_MITRA}` | jenis_mitra | Badan Usaha |
| `${JENIS_USULAN}` | jenis_usulan | Sewa |

### Placeholder SPESIFIK per Document Type:

#### 1. `nodin_berjenjang.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_NODIN}` | nodin_berjenjang_nomor | 1001/AA-23/B/2025 |
| `${TANGGAL_NODIN}` | nodin_berjenjang_tanggal_mulai + tanggal_selesai | (10 September 2025 – 9 September 2026) |
| `${JANGKA_WAKTU}` | Calculated from date range | 12 bulan / 1 tahun |
| `${MITRA_PERUNTUKAN}` | nodin_berjenjang_peruntukan | Sewa Kantor |
| `${NOMINAL_SURAT}` | nodin_berjenjang_nominal | Rp 250.000.000 |

#### 2. `surat_konfirmasi.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_SURAT}` | surat_konfirmasi_nomor | 100/SK/2025 |
| `${TANGGAL_SURAT}` | surat_konfirmasi_tanggal | 15 November 2025 |
| `${TUJUAN_SURAT}` | surat_konfirmasi_tujuan | Kepala KPKNL Jakarta I |

#### 3. `nodin_konfirmasi.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_NODIN}` | nodin_konfirmasi_nomor | 200/NK/2025 |
| `${TANGGAL_NODIN}` | nodin_konfirmasi_tanggal | 16 November 2025 |
| `${MITRA_PERUNTUKAN}` | nodin_konfirmasi_mitra_peruntukan | Sewa Gedung |

#### 4. `surat_usulan_kpknl.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_SURAT}` | surat_usulan_kpknl_nomor | 300/SU/2025 |
| `${TANGGAL_SURAT}` | surat_usulan_kpknl_tanggal | 17 November 2025 |
| `${TUJUAN_SURAT}` | surat_usulan_kpknl_tujuan | KPKNL Jakarta I |

#### 5. `sptjm.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_SPTJM}` | sptjm_nomor | 400/SPTJM/2025 |
| `${TANGGAL_SPTJM}` | sptjm_tanggal | 18 November 2025 |
| `${KODE_BARANG}` | sptjm_kode_barang | 1.3.2.05.01.0001 |

#### 6. `surat_pernyataan.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_SURAT}` | surat_pernyataan_nomor | 500/SP/2025 |
| `${TANGGAL_SURAT}` | surat_pernyataan_tanggal | 19 November 2025 |
| `${KODE_BARANG}` | surat_pernyataan_kode_barang | 1.3.2.05.01.0001 |

#### 7. `surat_invoice.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_SURAT}` | surat_invoice_nomor | 600/INV/2025 |
| `${TANGGAL_SURAT}` | surat_invoice_tanggal | 20 November 2025 |
| `${NOMINAL_SURAT}` | surat_invoice_nominal | Rp 150.000.000 |

#### 8. `nodin_ttd.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_NODIN}` | nodin_ttd_nomor | 700/TTD/2025 |
| `${TANGGAL_NODIN}` | nodin_ttd_tanggal | 21 November 2025 |
| `${JUDUL_PERJANJIAN}` | nodin_ttd_judul_perjanjian | Perjanjian Sewa Kantor |

#### 9. `nodin_internal.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_NODIN}` | nodin_internal_nomor | 800/NI/2025 |
| `${TANGGAL_NODIN}` | nodin_internal_tanggal | 22 November 2025 |
| `${JUDUL_PERJANJIAN}` | nodin_internal_judul_perjanjian | Perjanjian Sewa Gedung |

#### 10. `perjanjian.docx`
| Placeholder | Database Field | Contoh |
|------------|----------------|--------|
| `${NO_PERJANJIAN}` | perjanjian_nomor | 900/PRJ/2025 |
| `${TANGGAL_PERJANJIAN}` | perjanjian_tanggal_penandatanganan | 23 November 2025 |
| `${JANGKA_WAKTU_NILAI}` | jangka_waktu_nilai | 12 |
| `${JANGKA_WAKTU_SATUAN}` | jangka_waktu_satuan | bulan |

---

## ➕ Cara Menambah Template Baru

### Step 1: Buat Template DOCX

1. Buka Microsoft Word / LibreOffice Writer
2. Design dokumen sesuai format yang diinginkan (header, logo, konten)
3. Sisipkan placeholder dengan format **`${NAMA_PLACEHOLDER}`** (UPPERCASE)
   - Contoh: `${NO_SURAT}`, `${TANGGAL}`, `${NAMA_MITRA}`
4. Save file dengan nama `{tipe}.docx`
   - Gunakan **underscore** (`_`), bukan dash (`-`)
   - Contoh: `nodin_berjenjang.docx` ✅ (bukan `nodin-berjenjang.docx` ❌)
5. Upload ke folder `resources/template/`

### Step 2: Update Controller

Edit file `app/Http/Controllers/BmnDocumentController.php`:

#### A. Method `getPlaceholdersForType()` (line ~195)

Tambah case baru di switch statement:

```php
case 'nama_dokumen_baru':
    $placeholders['{{NO_SURAT}}'] = $utilization->nama_dokumen_baru_nomor ?? 'N/A';
    $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->nama_dokumen_baru_tanggal);
    // ... tambah placeholder lainnya
    break;
```

**PENTING**:
- Key array gunakan format `{{VARIABLE}}` (akan di-strip otomatis)
- Gunakan `?? 'N/A'` untuk handle null values
- Tanggal gunakan `formatDateIndonesia()` untuk format Indonesia

#### B. Method `validateDocumentData()` (line ~473)

Tambah required fields validation:

```php
$requiredFieldsMap = [
    // ... existing entries
    'nama_dokumen_baru' => ['nama_dokumen_baru_nomor', 'nama_dokumen_baru_tanggal'],
];
```

#### C. Method `checkDocumentReadiness()` (line ~395)

Tambah status check:

```php
return [
    // ... existing entries
    'nama_dokumen_baru' => $this->checkDocumentFields($utilization, [
        'nama_dokumen_baru_nomor', 'nama_dokumen_baru_tanggal'
    ]),
];
```

#### D. Method `getDocumentTitle()` (line ~368)

Tambah title mapping:

```php
$titles = [
    // ... existing entries
    'nama_dokumen_baru' => 'Nama Dokumen Baru yang Bagus',
];
```

#### E. Method `getDocumentFilename()` (line ~518)

Tambah filename mapping:

```php
$typeNames = [
    // ... existing entries
    'nama_dokumen_baru' => 'Nama_Dokumen_Baru',
];
```

### Step 3: Update View

Edit file `resources/views/utilization/documents.blade.php`:

Tambah entry baru di array `documentTypes`:

```javascript
const documentTypes = [
    // ... existing entries
    { type: 'nama_dokumen_baru', title: 'Nama Dokumen Baru', phase: 'konfirmasi' },
];
```

**Phases**: `konfirmasi`, `usulan`, `penilaian`, `perjanjian`

### Step 4: Test

1. Akses halaman `/utilization-dashboard/{id}/documents`
2. Pastikan dokumen baru muncul di list
3. Klik "Generate PDF" → harus download DOCX
4. Buka file DOCX → cek apakah placeholder sudah ter-replace dengan data

---

## 🔍 Troubleshooting

### Template not found
**Error**: `Template not found for type: nama_dokumen`

**Solusi**:
- Pastikan file ada di `resources/template/{type}.docx`
- Nama file harus match dengan `$type` di controller
- Gunakan underscore, bukan dash

### Placeholder tidak ter-replace
**Error**: Placeholder masih muncul sebagai `${VARIABLE}` di hasil generate

**Solusi**:
- Cek di controller apakah placeholder sudah didefinisikan di `getPlaceholdersForType()`
- Pastikan format di template: `${VARIABLE}` (bukan `{VARIABLE}` atau `{{VARIABLE}}`)
- Check log Laravel: `storage/logs/laravel.log` untuk melihat nilai yang di-set

### Data tidak lengkap
**Error**: `Data tidak lengkap: field_name`

**Solusi**:
- Field required belum diisi di database
- Update data di halaman detail utilization
- Atau ubah validation di `validateDocumentData()` jika field memang optional

---

## 📚 Dokumentasi Lengkap

- **Dokumentasi Generate Dokumen**: `DOKUMENTASI_GENERATE_DOKUMEN.md` (root project)
- **API Routes**: Lihat `routes/web.php`
- **Controller Source**: `app/Http/Controllers/BmnDocumentController.php`
- **View Source**: `resources/views/utilization/documents.blade.php`

---

## 💡 Tips & Best Practices

1. **Placeholder Naming**: Gunakan UPPERCASE dan descriptive names
   - ✅ `${NOMOR_SURAT}`, `${TANGGAL_PENANDATANGANAN}`
   - ❌ `${ns}`, `${tgl}`

2. **Template Design**:
   - Test template dengan dummy data dulu
   - Pastikan formatting (bold, italic, alignment) sudah benar
   - Gunakan styles (Heading 1, Heading 2) untuk consistency

3. **Error Handling**:
   - Selalu gunakan `?? 'N/A'` untuk nullable fields
   - Log important steps untuk debugging
   - Provide fallback jika template tidak ada

4. **Performance**:
   - Template disimpan di `storage/app/temp/` lalu di-cleanup otomatis
   - File di-stream langsung ke user (tidak disimpan permanent)
   - Generate on-demand (tidak pre-generate)

---

**Last Updated**: November 2025
**Maintainer**: Dashboard BMN Development Team
