# 📄 Implementasi: Surat Konfirmasi Perpanjangan Sewa

## 🎯 Overview

Dokumen ini menjelaskan implementasi fitur generate dokumen untuk **Surat Konfirmasi Perpanjangan Sewa** menggunakan library PHPWord dengan template DOCX.

---

## ✅ Yang Sudah Diimplementasikan

### 1. **Controller Update** (`app/Http/Controllers/Utilization/DocumentController.php`)

#### A. Placeholder Mapping (`getPlaceholdersForType()`)

Menambahkan case baru untuk `surat_konfirmasi_perpanjangan_sewa` dengan mapping placeholder:

| Placeholder Template | Database Field | Format |
|---------------------|----------------|---------|
| `${NOMOR_SURAT}` | `surat_konfirmasi_nomor` | Plain text |
| `${TANGGAL_SURAT}` | `surat_konfirmasi_tanggal` | Format Indonesia (DD Month YYYY) |
| `${PERUNTUKAN_SURAT}` | `surat_konfirmasi_peruntukan_surat` | Plain text |
| `${TUJUAN_SURAT}` | `surat_konfirmasi_tujuan_surat` | Plain text |
| `${NOMOR_PERJANJIAN_SEWA_LAMA_DPR}` | `surat_konfirmasi_nomor_perjanjian_lama_dpr` | Plain text |
| `${NOMOR_PERJANJIAN_SEWA_LAMA_MITRA}` | `surat_konfirmasi_nomor_perjanjian_lama_mitra` | Plain text |
| `${TANGGAL_BERAKHIR}` | `surat_konfirmasi_tanggal_berakhir` | Format Indonesia |
| `${TANGGAL_KONFIRMASI_TERAKHIR}` | `surat_konfirmasi_tanggal_konfirmasi_terakhir` | Format Indonesia |
| `${NAMA_KASUB}` | `surat_konfirmasi_kasub_nama` | Plain text |
| `${NOMOR_KASUB}` | `surat_konfirmasi_kasub_nomor` | Plain text |

**Kode yang ditambahkan:**
```php
case 'surat_konfirmasi_perpanjangan_sewa':
    $placeholders['{{NOMOR_SURAT}}'] = $utilization->surat_konfirmasi_nomor ?? 'N/A';
    $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal);
    $placeholders['{{PERUNTUKAN_SURAT}}'] = $utilization->surat_konfirmasi_peruntukan_surat ?? 'N/A';
    $placeholders['{{TUJUAN_SURAT}}'] = $utilization->surat_konfirmasi_tujuan_surat ?? 'N/A';
    $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_DPR}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_dpr ?? 'N/A';
    $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_MITRA}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_mitra ?? 'N/A';
    $placeholders['{{TANGGAL_BERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_berakhir);
    $placeholders['{{TANGGAL_KONFIRMASI_TERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir);
    $placeholders['{{NAMA_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nama ?? 'N/A';
    $placeholders['{{NOMOR_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nomor ?? 'N/A';
    break;
```

#### B. Validation (`validateDocumentData()`)

Required fields untuk dokumen ini:
- `surat_konfirmasi_nomor`
- `surat_konfirmasi_tanggal`
- `surat_konfirmasi_peruntukan_surat`
- `surat_konfirmasi_tujuan_surat`
- `surat_konfirmasi_nomor_perjanjian_lama_dpr`
- `surat_konfirmasi_nomor_perjanjian_lama_mitra`
- `surat_konfirmasi_tanggal_berakhir`
- `surat_konfirmasi_tanggal_konfirmasi_terakhir`
- `surat_konfirmasi_kasub_nama`
- `surat_konfirmasi_kasub_nomor`

#### C. Document Readiness Check (`checkDocumentReadiness()`)

Menambahkan check untuk memastikan semua field required sudah terisi sebelum dokumen bisa di-generate.

#### D. Document Title (`getDocumentTitle()`)

```php
'surat_konfirmasi_perpanjangan_sewa' => 'Surat Konfirmasi Perpanjangan Sewa'
```

#### E. Filename Generation (`getDocumentFilename()`)

```php
'surat_konfirmasi_perpanjangan_sewa' => 'Surat_Konfirmasi_Perpanjangan_Sewa'
```

Output filename: `Surat_Konfirmasi_Perpanjangan_Sewa_{NamaMitra}.docx`

#### F. Fallback Document (`createSimpleWordDocument()`)

Jika template tidak ditemukan, sistem akan membuat dokumen sederhana dengan semua data yang tersedia.

### 2. **Model Update** (`app/Models/BmnPemanfaatan.php`)

Menambahkan date casting untuk field:
```php
'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'date'
```

Ini memastikan field tanggal di-handle dengan benar oleh Laravel.

---

## 📋 Cara Menggunakan

### **Step 1: Buat Template DOCX**

1. Buat file template di: `resources/template/surat_konfirmasi_perpanjangan_sewa.docx`
2. Gunakan placeholder format `${VARIABLE}` (BUKAN `{{VARIABLE}}`)
3. Contoh isi template:

```
SURAT KONFIRMASI PERPANJANGAN SEWA

Nomor: ${NOMOR_SURAT}
Tanggal: ${TANGGAL_SURAT}

Kepada Yth.
${TUJUAN_SURAT}

Perihal: ${PERUNTUKAN_SURAT}

Dengan hormat,
Berdasarkan perjanjian sewa sebelumnya:
- Nomor Perjanjian (DPR): ${NOMOR_PERJANJIAN_SEWA_LAMA_DPR}
- Nomor Perjanjian (Mitra): ${NOMOR_PERJANJIAN_SEWA_LAMA_MITRA}

Yang akan berakhir pada tanggal ${TANGGAL_BERAKHIR}, dengan ini kami konfirmasikan 
bahwa konfirmasi terakhir dilakukan pada ${TANGGAL_KONFIRMASI_TERAKHIR}.

Hormat kami,

${NAMA_KASUB}
NIP. ${NOMOR_KASUB}
```

### **Step 2: Input Data di Dashboard**

1. Buka `/utilization-dashboard/{id}`
2. Isi semua field di **Tab 2: Konfirmasi** → Section "Surat Konfirmasi":
   - Nomor Surat
   - Tanggal Surat
   - Peruntukan Surat
   - Tujuan Surat
   - Nomor Perjanjian Lama (DPR)
   - Nomor Perjanjian Lama (Mitra)
   - Tanggal Berakhir
   - Tanggal Konfirmasi Terakhir
   - Nama Kasub
   - Nomor Kasub

### **Step 3: Generate Dokumen**

1. Klik menu **"Documents"** atau akses `/utilization-dashboard/{id}/documents`
2. Cari dokumen **"Surat Konfirmasi Perpanjangan Sewa"**
3. Jika semua field sudah terisi, status akan menunjukkan **"Ready"**
4. Klik tombol **"Generate"**
5. Dokumen akan otomatis terdownload dalam format `.docx`

---

## 🔧 API Endpoint

### Generate Document

```
POST /utilization-dashboard/{id}/documents/generate/surat_konfirmasi_perpanjangan_sewa
```

**Response Success:**
- Download file: `Surat_Konfirmasi_Perpanjangan_Sewa_{NamaMitra}.docx`

**Response Error (Data Tidak Lengkap):**
```json
{
    "success": false,
    "message": "Data tidak lengkap: surat_konfirmasi_nomor, surat_konfirmasi_tanggal"
}
```

---

## 🎓 Referensi Implementasi

Implementasi ini mengikuti pola yang sama dengan **Nodin Berjenjang**:

1. ✅ Menggunakan PHPWord TemplateProcessor
2. ✅ Template format `${VARIABLE}`
3. ✅ Validasi required fields
4. ✅ Format tanggal Indonesia
5. ✅ Fallback jika template tidak ada
6. ✅ Logging untuk debugging

---

## 📝 Database Fields

Semua field sudah ada di tabel `bmn_pemanfaatan` melalui migration:
- `2025_11_23_110352_add_complete_surat_konfirmasi_fields_to_bmn_pemanfaatan_table.php`

Fields yang digunakan:
```sql
surat_konfirmasi_nomor                          VARCHAR(255)
surat_konfirmasi_tanggal                        DATE
surat_konfirmasi_peruntukan_surat               TEXT
surat_konfirmasi_tujuan_surat                   TEXT
surat_konfirmasi_nomor_perjanjian_lama_dpr      TEXT
surat_konfirmasi_nomor_perjanjian_lama_mitra    TEXT
surat_konfirmasi_tanggal_berakhir               DATE
surat_konfirmasi_tanggal_konfirmasi_terakhir    DATE
surat_konfirmasi_kasub_nama                     TEXT
surat_konfirmasi_kasub_nomor                    TEXT
```

---

## ✨ Fitur yang Sudah Terimplementasi

- ✅ Placeholder mapping untuk 10 variabel
- ✅ Validasi data sebelum generate
- ✅ Format tanggal Indonesia otomatis
- ✅ Document readiness check
- ✅ Fallback document jika template tidak ada
- ✅ Error handling dan logging
- ✅ Date casting di model
- ✅ Filename generation yang descriptive

---

## 🔍 Testing

### Test Checklist:

1. **Template Test:**
   - [ ] Buat template `surat_konfirmasi_perpanjangan_sewa.docx` di `resources/template/`
   - [ ] Pastikan menggunakan placeholder `${VARIABLE}` bukan `{{VARIABLE}}`
   - [ ] Test template di Word untuk memastikan format OK

2. **Data Test:**
   - [ ] Input semua required fields di dashboard
   - [ ] Verify data tersimpan di database

3. **Generation Test:**
   - [ ] Generate dokumen dari dashboard
   - [ ] Verify semua placeholder terganti dengan data dari DB
   - [ ] Verify format tanggal dalam bahasa Indonesia
   - [ ] Verify filename sesuai format

4. **Error Handling Test:**
   - [ ] Test generate dengan data tidak lengkap (harus error)
   - [ ] Test generate tanpa template (harus fallback ke simple document)
   - [ ] Check log file untuk error messages

---

## 📞 Troubleshooting

### Problem: Placeholder tidak terganti

**Solusi:**
1. Pastikan template menggunakan `${VARIABLE}` bukan `{{VARIABLE}}`
2. Pastikan nama placeholder di template match dengan di controller
3. Check log: `storage/logs/laravel.log`

### Problem: Template tidak ditemukan

**Solusi:**
1. Pastikan file ada di: `resources/template/surat_konfirmasi_perpanjangan_sewa.docx`
2. Pastikan nama file exact match (case-sensitive di Linux)
3. Sistem akan fallback ke simple document jika template tidak ada

### Problem: Data tidak lengkap

**Solusi:**
1. Isi semua 10 required fields di dashboard
2. Check validation error message untuk tahu field mana yang kosong

---

## 📚 Dokumentasi Terkait

- [DOKUMENTASI_GENERATE_DOKUMEN.md](./DOKUMENTASI_GENERATE_DOKUMEN.md) - Dokumentasi umum sistem generate dokumen
- [PHPWord Documentation](https://phpword.readthedocs.io/) - Library documentation

---

**Last Updated:** 23 November 2025  
**Version:** 1.0  
**Author:** Dashboard BMN Development Team

---

## 🎉 Summary

Fitur generate dokumen untuk **Surat Konfirmasi Perpanjangan Sewa** sudah **SELESAI DIIMPLEMENTASIKAN** dengan:

✅ 10 placeholder variables  
✅ Complete validation  
✅ Date formatting  
✅ Error handling  
✅ Fallback mechanism  
✅ Logging support  

**Next Step:** Buat template DOCX di `resources/template/surat_konfirmasi_perpanjangan_sewa.docx` dan test generate!
