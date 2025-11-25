# 📄 Dokumentasi: Sistem Generate Dokumen Pemanfaatan BMN

## 🎯 Overview

Sistem ini menggunakan **PHPWord TemplateProcessor** untuk generate dokumen Word (.docx) dari template dengan placeholder yang akan diganti dengan data dari database.

---

## 📁 Struktur File

### **Template Files** (`resources/template/`)

```
resources/template/
├── nodin_berjenjang.docx          ← TEMPLATE YANG DIPAKAI (dengan ${} placeholders)
├── nodin-berjenjang.pdf           ← Reference PDF (untuk lihat format yang diinginkan)
└── _archive/                      ← Backup dan versi lama (tidak dipakai)
    ├── nodin-berjenjang.doc
    ├── nodin-berjenjang.docx      (versi lama dengan {{}} placeholders)
    └── nodin-berjenjang.docx.original
```

**⚠️ PENTING**: Template yang **BENAR-BENAR DIPAKAI** adalah `nodin_berjenjang.docx` (dengan underscore `_`)

---

## 🔧 Cara Kerja Sistem

### **1. Flow Generate Dokumen**

```
User Request
    ↓
Route: POST /utilization-dashboard/{id}/documents/generate/{type}
    ↓
BmnDocumentController@generate()
    ↓
├─ Validasi data required fields
├─ Cek template file exists
├─ Load template dengan TemplateProcessor
├─ Get placeholder values dari database
├─ Replace ${PLACEHOLDER} dengan data
├─ Save ke temporary file
└─ Download dokumen
```

### **2. Controller Architecture**

Sistem menggunakan **separation of concerns** dengan 2 controller:

**A. `BmnUtilizationController.php`** - CRUD Operations
- Menangani create, read, update, delete untuk data pemanfaatan BMN
- Menangani business logic seperti toggle complete, check completeness
- Tidak menangani document generation

**B. `BmnDocumentController.php`** - Document Generation
- Dedicated controller untuk semua operasi generate dokumen
- Clean separation dari business logic
- Mudah maintain dan extend untuk tipe dokumen baru

### **3. Document Controller Methods** (`app/Http/Controllers/BmnDocumentController.php`)

#### **Method Utama:**

**`generate($id, $type)`**
- Entry point untuk generate dokumen
- Validasi data dengan `validateDocumentData()`
- Call `generateWordDocument()` untuk actual generation

**`generateWordDocument($utilization, $type)`**
- **CORE METHOD** untuk generate dokumen
- Menggunakan `TemplateProcessor` dari PHPWord
- Flow:
  ```php
  1. Cari template file
     - Coba dengan underscore: nodin_berjenjang.docx
     - Coba dengan dash: nodin-berjenjang.docx (fallback)

  2. Load template
     $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

  3. Get placeholder values
     $placeholders = $this->getPlaceholdersForType($utilization, $type);

  4. Replace placeholders
     foreach ($placeholders as $placeholder => $value) {
         $placeholderName = str_replace(['{{', '}}'], '', $placeholder);
         $templateProcessor->setValue($placeholderName, $value);
     }

  5. Save dan download
     $templateProcessor->saveAs($tempPath);
     return response()->download($tempPath)->deleteFileAfterSend(true);
  ```

**`getPlaceholdersForType($utilization, $type)`**
- Return array mapping placeholder ke nilai dari database
- Untuk `nodin_berjenjang`:
  ```php
  [
      '{{NO_NODIN}}' => $utilization->nodin_berjenjang_nomor,
      '{{TANGGAL_NODIN}}' => formatDateIndonesia($utilization->nodin_berjenjang_tanggal),
      '{{MITRA_PERUNTUKAN}}' => $utilization->nodin_berjenjang_peruntukan,
      '{{NOMINAL_SURAT}}' => 'Rp ' . number_format($utilization->nodin_berjenjang_nominal)
  ]
  ```

**`checkDocumentReadiness($utilization)`**
- Cek field mana yang sudah lengkap untuk tiap tipe dokumen
- Return status: 'ready', 'incomplete', atau 'not_started'

**`generateAll($id)`**
- Generate semua dokumen yang statusnya 'ready'
- Return ZIP file berisi semua dokumen

---

## 📝 Template Format

### **Placeholder Format di Template**

Template menggunakan format **`${VARIABLE}`** (BUKAN `{{VARIABLE}}`):

```
Contoh dalam template nodin_berjenjang.docx:

NOTA DINAS
NOMOR: ${NO_NODIN}

Tanggal: ${TANGGAL_NODIN}

... dengan ${MITRA_PERUNTUKAN} berupa ...
... sebesar ${NOMINAL_SURAT} ...
```

### **Available Placeholders untuk Nodin Berjenjang**

| Placeholder | Database Field | Format Output | Contoh |
|------------|----------------|---------------|---------|
| `${NO_NODIN}` | `nodin_berjenjang_nomor` | Plain text | 1001/AA-23/B/2025 |
| `${TANGGAL_NODIN}` | `nodin_berjenjang_tanggal` | Format Indonesia | 20 November 2025 |
| `${MITRA_PERUNTUKAN}` | `nodin_berjenjang_peruntukan` | Plain text | Sewa |
| `${NOMINAL_SURAT}` | `nodin_berjenjang_nominal` | Format Rupiah | Rp 250.000 |
| `${NAMA_MITRA}` | `nama_mitra_penyewa` | Plain text | PT ABC |
| `${JENIS_MITRA}` | `jenis_mitra` | Plain text | Perusahaan |
| `${JENIS_USULAN}` | `jenis_usulan` | Plain text | Perpanjangan |
| `${TANGGAL}` | Current date | Format Indonesia | 19 November 2025 |

---

## 🗄️ Database Schema

### **Table: `bmn_pemanfaatan`**

Field untuk Nodin Berjenjang:
```sql
nodin_berjenjang_nomor        VARCHAR(100)   -- Nomor nodin
nodin_berjenjang_tanggal      DATE           -- Tanggal nodin
nodin_berjenjang_peruntukan   VARCHAR(255)   -- Peruntukan (Sewa/dll)
nodin_berjenjang_nominal      DECIMAL(15,2)  -- Nominal dalam rupiah
nodin_berjenjang_mitra        VARCHAR(255)   -- Nama mitra (opsional)
```

---

## 🚀 Cara Menggunakan

### **1. User Flow**

1. Buka `/utilization-dashboard/{id}`
2. Isi data di **Tab 3: Usulan Pemanfaatan** → Section "Nodin Berjenjang":
   - Nomor Nodin
   - Tanggal Nodin
   - Peruntukan
   - Nominal
3. Klik menu **"Documents"** atau akses `/utilization-dashboard/{id}/documents`
4. Klik tombol **"Generate"** untuk Nodin Berjenjang
5. Dokumen akan otomatis terdownload

### **2. API Endpoints**

**Generate Single Document:**
```
POST /utilization-dashboard/{id}/documents/generate/{type}
```
Contoh: `POST /utilization-dashboard/{id}/documents/generate/nodin_berjenjang`

**Generate All Documents:**
```
POST /utilization-dashboard/{id}/documents/generate-all
```

**Document Status Page:**
```
GET /utilization-dashboard/{id}/documents
```

**Response:**
- Success: Download file .docx (atau .zip untuk generate-all)
- Error: JSON dengan error message

---

## 🔧 Troubleshooting

### **Problem: Dokumen tidak menggunakan template (hanya plain text)**

**Penyebab:**
1. Template file tidak ditemukan
2. Placeholder di template tidak match (pakai `{{}}` bukan `${}`)
3. Error saat load template (fallback ke `createSimpleWordDocument()`)

**Solusi:**
1. Cek file exists: `resources/template/nodin_berjenjang.docx`
2. Cek placeholder format: harus `${VARIABLE}` bukan `{{VARIABLE}}`
3. Cek log di `storage/logs/laravel.log`

### **Problem: Placeholder tidak terganti**

**Penyebab:**
1. Nama placeholder tidak match
2. Data di database kosong/null

**Solusi:**
1. Cek nama placeholder di template match dengan di `getPlaceholdersForType()`
2. Cek data di database sudah terisi
3. Cek log untuk lihat nilai yang di-set

### **Cek Log**

```bash
tail -f storage/logs/laravel.log
```

Log yang berguna:
- `Set template variable {name} = {value}` - Nilai yang di-replace
- `Template not found for type: {type}` - Template tidak ditemukan
- `Error generating Word document: {error}` - Error saat generate

---

## 📋 Checklist: Menambah Dokumen Baru

Jika ingin menambah tipe dokumen baru (misal: `surat_konfirmasi`):

### **1. Buat Template File**
- [ ] Buat file `.docx` di `resources/template/`
- [ ] Nama file: `{type}.docx` (misal: `surat_konfirmasi.docx`)
- [ ] Gunakan placeholder format `${VARIABLE}`

### **2. Update Controller**

File: `app/Http/Controllers/BmnDocumentController.php`

**a. Tambah case di `getPlaceholdersForType()`**
```php
case 'surat_konfirmasi':
    $placeholders['{{NO_SURAT}}'] = $utilization->surat_konfirmasi_nomor ?? 'N/A';
    $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal);
    // ... dst
    break;
```

**b. Tambah validation di `validateDocumentData()`**
```php
'surat_konfirmasi' => ['surat_konfirmasi_nomor', 'surat_konfirmasi_tanggal'],
```

**c. Tambah check di `checkDocumentReadiness()`**
```php
'surat_konfirmasi' => $this->checkDocumentFields($utilization, [
    'surat_konfirmasi_nomor', 'surat_konfirmasi_tanggal'
]),
```

### **3. Update Database**
- [ ] Tambah field yang diperlukan di migration
- [ ] Tambah ke `$fillable` di model `BmnPemanfaatan`
- [ ] Tambah casting jika perlu (date, decimal, dll)

### **4. Update View**
- [ ] Tambah form input di `utilization_dashboard.blade.php`
- [ ] Tambah button generate di `utilization_documents.blade.php`

### **5. Testing**
- [ ] Test generate dokumen
- [ ] Verify template terpakai (ada logo, header, format)
- [ ] Verify placeholder terganti dengan data dari DB

---

## 📚 Library yang Digunakan

**PHPWord** v1.4
- Package: `phpoffice/phpword`
- Documentation: https://phpword.readthedocs.io/
- Class: `\PhpOffice\PhpWord\TemplateProcessor`

**Key Methods:**
```php
// Load template
$template = new TemplateProcessor($templatePath);

// Set single value
$template->setValue('VARIABLE', $value);

// Set multiple values
foreach ($placeholders as $key => $value) {
    $template->setValue($key, $value);
}

// Save to file
$template->saveAs($outputPath);
```

---

## 🎓 Tips & Best Practices

### **1. Template Design**
- ✅ Gunakan styles di Word (Heading 1, Normal, dll) agar konsisten
- ✅ Test template dengan placeholder dummy sebelum integrate
- ✅ Simpan backup template original di folder `_archive/`

### **2. Placeholder Naming**
- ✅ Gunakan UPPERCASE: `${NO_SURAT}` bukan `${no_surat}`
- ✅ Gunakan underscore: `${NO_SURAT}` bukan `${NO-SURAT}`
- ✅ Nama jelas dan deskriptif

### **3. Error Handling**
- ✅ Always validate required fields sebelum generate
- ✅ Log semua error untuk debugging
- ✅ Provide clear error message ke user

### **4. Performance**
- ✅ Template processor cukup cepat untuk dokumen normal
- ✅ Gunakan queue jika generate banyak dokumen sekaligus
- ✅ Clean up temp files dengan `deleteFileAfterSend(true)`

---

## 📞 Support

Jika ada pertanyaan atau masalah:
1. Cek dokumentasi ini
2. Cek log file di `storage/logs/laravel.log`
3. Cek PHPWord documentation
4. Debug dengan `\Log::info()` di controller

---

**Last Updated:** 19 November 2025
**Version:** 2.0 (Refactored - Separate Document Controller)
**Author:** Dashboard BMN Development Team

---

## 📝 Changelog

### Version 2.0 (19 November 2025)
- ♻️ **Refactoring:** Pisahkan document generation ke `BmnDocumentController`
- ♻️ **Clean Architecture:** `BmnUtilizationController` sekarang hanya handle CRUD
- ✨ **New Feature:** Generate all documents at once (ZIP download)
- 📚 **Documentation:** Update semua referensi ke controller baru
- 🔧 **Routes:** Update endpoint dari `/generate/` ke `/documents/generate/`

### Version 1.0 (19 November 2025)
- ✨ Initial implementation dengan PHPWord TemplateProcessor
- ✨ Template conversion dari `{{}}` ke `${}` format
- 📁 Template cleanup dan organization
