# 🔧 TROUBLESHOOTING: Surat Konfirmasi Perpanjangan Sewa

## ❌ Masalah: Dokumen Yang Di-generate Hanya Plain Text

Jika dokumen yang di-generate hanya berisi plain text seperti ini:

```
Surat Konfirmasi Perpanjangan Sewa
Nama Mitra: Bank Mandiri
Jenis Mitra: Perusahaan
...
```

Ini berarti sistem menggunakan **fallback simple document** karena template tidak bisa di-load atau ada error.

---

## ✅ SOLUSI

### 1. **Pastikan Mengklik Button Yang BENAR**

Ada **DUA** button di halaman documents:

#### ❌ SALAH - "Surat Konfirmasi" (Simple)
- Type: `surat_konfirmasi`
- Required fields: Hanya 2 (nomor & tanggal)
- **TIDAK MENGGUNAKAN TEMPLATE LENGKAP**

#### ✅ BENAR - "Surat Konfirmasi Perpanjangan Sewa" (Lengkap)
- Type: `surat_konfirmasi_perpanjangan_sewa`
- Required fields: Semua 10 fields
- **MENGGUNAKAN TEMPLATE LENGKAP**

**PASTIKAN ANDA MENGKLIK BUTTON YANG KEDUA!**

---

### 2. **Isi SEMUA 10 Required Fields**

Sebelum generate, pastikan semua field ini sudah terisi:

1. ✅ Nomor Surat (`surat_konfirmasi_nomor`)
2. ✅ Tanggal Surat (`surat_konfirmasi_tanggal`)
3. ✅ Peruntukan Surat (`surat_konfirmasi_peruntukan_surat`)
4. ✅ Tujuan Surat (`surat_konfirmasi_tujuan_surat`)
5. ✅ Nomor Perjanjian Lama DPR (`surat_konfirmasi_nomor_perjanjian_lama_dpr`)
6. ✅ Nomor Perjanjian Lama Mitra (`surat_konfirmasi_nomor_perjanjian_lama_mitra`)
7. ✅ Tanggal Berakhir (`surat_konfirmasi_tanggal_berakhir`)
8. ✅ Tanggal Konfirmasi Terakhir (`surat_konfirmasi_tanggal_konfirmasi_terakhir`)
9. ✅ Nama Kasub (`surat_konfirmasi_kasub_nama`)
10. ✅ Nomor Kasub (`surat_konfirmasi_kasub_nomor`)

Jika ada field yang kosong, akan muncul `N/A` di dokumen.

---

### 3. **Cek Log Laravel**

Jika masih menghasilkan plain text, cek log untuk melihat error:

```bash
tail -f storage/logs/laravel.log
```

Cari pesan seperti:
- `Template not found for type`
- `Error generating Word document`
- `Loading template from`

---

### 4. **Verify Template File Exists**

Pastikan file template ada di:
```
resources/template/surat_konfirmasi_perpanjangan_sewa.docx
```

Test dengan command:
```bash
php -r "echo file_exists('resources/template/surat_konfirmasi_perpanjangan_sewa.docx') ? 'EXISTS' : 'NOT FOUND';"
```

---

## 🧪 Test Template (Untuk Developer)

Jalankan test script untuk verify template bisa di-load:

```bash
php test_generate.php
```

Jika berhasil, akan membuat file `test_output_surat_konfirmasi.docx` dengan semua placeholder terganti.

---

## 📝 Cara Generate Yang Benar

1. **Buka halaman documents**: `/utilization-dashboard/{id}/documents`

2. **Scroll ke section "Surat Konfirmasi Perpanjangan Sewa"** (bukan yang "Surat Konfirmasi" biasa)

3. **Pastikan status menunjukkan "Ready"** (hijau)
   - Jika "Missing Data" (merah), klik "Lengkapi Data" untuk isi semua field

4. **Klik button "Generate"** pada card "Surat Konfirmasi Perpanjangan Sewa"

5. **Dokumen akan terdownload** dengan format template yang benar

---

## 🔍 Debug Checklist

Jika masih bermasalah, cek satu per satu:

- [ ] Template file exists di `resources/template/surat_konfirmasi_perpanjangan_sewa.docx`
- [ ] Semua 10 required fields sudah terisi di database
- [ ] Mengklik button "Surat Konfirmasi Perpanjangan Sewa" (bukan "Surat Konfirmasi")
- [ ] Status document menunjukkan "Ready" (hijau)
- [ ] Tidak ada error di `storage/logs/laravel.log`
- [ ] PHPWord library ter-install dengan benar (`composer show phpoffice/phpword`)

---

## 💡 Perbedaan Antara Dua Button

| Feature | Surat Konfirmasi (Simple) | Surat Konfirmasi Perpanjangan Sewa (Lengkap) |
|---------|---------------------------|----------------------------------------------|
| Type | `surat_konfirmasi` | `surat_konfirmasi_perpanjangan_sewa` |
| Required Fields | 2 fields | 10 fields |
| Template | ✅ Menggunakan template | ✅ Menggunakan template |
| Placeholders | Hanya 2 | Semua 10 |
| Output | Template dengan beberapa N/A | Template dengan semua data |

**REKOMENDASI: Gunakan yang "Perpanjangan Sewa" (lengkap) untuk hasil terbaik!**

---

**Last Updated:** 23 November 2025  
**Status:** ✅ Template verified working  
**Test Result:** ✅ Document generation successful

