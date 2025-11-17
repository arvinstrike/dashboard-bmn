# Custom Alert System - Documentation

## Overview
Sistem alert custom yang profesional dan modern untuk menggantikan SweetAlert2. Dirancang dengan animasi smooth, desain modern, dan mudah digunakan.

## Fitur
- ✅ Alert modal dengan 4 tipe: Success, Error, Warning, Info
- ✅ Toast notification (popup kecil di pojok kanan atas)
- ✅ Animasi smooth dan modern
- ✅ Responsive design
- ✅ Dark mode support
- ✅ Backward compatible dengan Swal API
- ✅ Ringan dan cepat (tidak perlu library eksternal)

## Instalasi
Custom alert sudah terinstall otomatis. Untuk menggunakannya di halaman blade, tambahkan:

```blade
{{-- Di dalam <head> tag --}}
@include('includes.custom-alert')
```

## Cara Penggunaan

### 1. Alert Modal (Popup Besar)

#### Success Alert
```javascript
// Menggunakan Swal (backward compatible)
Swal.fire({
    title: 'Berhasil!',
    text: 'Data berhasil disimpan',
    icon: 'success',
    confirmButtonText: 'OK'
});

// Atau shorthand
Swal.success('Berhasil!', 'Data berhasil disimpan');

// Atau helper function
showSuccess('Data berhasil disimpan');
showSuccess('Data berhasil disimpan', 'Sukses!'); // custom title
```

#### Error Alert
```javascript
Swal.fire({
    title: 'Error!',
    text: 'Gagal menyimpan data',
    icon: 'error',
    confirmButtonText: 'OK'
});

// Atau
Swal.error('Error!', 'Gagal menyimpan data');
showError('Gagal menyimpan data');
```

#### Warning Alert
```javascript
Swal.fire({
    title: 'Peringatan!',
    text: 'Data akan dihapus permanent',
    icon: 'warning',
    confirmButtonText: 'OK'
});

// Atau
Swal.warning('Peringatan!', 'Data akan dihapus permanent');
showWarning('Data akan dihapus permanent');
```

#### Info Alert
```javascript
Swal.fire({
    title: 'Informasi',
    text: 'Silakan lengkapi data terlebih dahulu',
    icon: 'info',
    confirmButtonText: 'OK'
});

// Atau
Swal.info('Informasi', 'Silakan lengkapi data terlebih dahulu');
showInfo('Silakan lengkapi data terlebih dahulu');
```

### 2. Toast Notification (Popup Kecil)

Toast notification muncul di pojok kanan atas dan otomatis hilang setelah beberapa detik.

```javascript
// Success Toast
CustomAlert.toast({
    title: 'Berhasil!',
    message: 'Data berhasil disimpan',
    icon: 'success',
    duration: 3000 // 3 detik (optional)
});

// Atau helper function
successToast('Data berhasil disimpan');
successToast('Data berhasil disimpan', 'Sukses!'); // custom title

// Error Toast
errorToast('Gagal menyimpan data');

// Warning Toast
warningToast('Perhatian: Data belum lengkap');

// Info Toast
infoToast('Proses sedang berlangsung');
```

### 3. Confirmation Dialog

#### Konfirmasi Umum
```javascript
confirmAction({
    title: 'Apakah Anda yakin?',
    message: 'Tindakan ini tidak dapat dibatalkan',
    confirmText: 'Ya, Lanjutkan',
    cancelText: 'Batal',
    onConfirm: function() {
        // Kode yang dijalankan jika user klik "Ya"
        console.log('User confirmed');
        // Lanjutkan proses...
    },
    onCancel: function() {
        // Kode yang dijalankan jika user klik "Batal" (optional)
        console.log('User cancelled');
    }
});
```

#### Konfirmasi Delete (Warna Merah dengan Button Batal)
```javascript
// Konfirmasi delete dengan icon merah dan 2 tombol (Ya, Hapus | Batal)
confirmDelete({
    itemName: 'data pengajuan BMN',
    onConfirm: function() {
        // Kode yang dijalankan ketika user klik "Ya, Hapus"
        $.ajax({
            url: '/api/delete/123',
            method: 'DELETE',
            success: function(response) {
                successToast('Data berhasil dihapus');
                // Reload table atau redirect
            },
            error: function() {
                errorToast('Gagal menghapus data');
            }
        });
    },
    onCancel: function() {
        // Optional: Kode yang dijalankan ketika user klik "Batal"
        infoToast('Penghapusan dibatalkan');
    }
});
```

**Catatan**:
- Icon berwarna **MERAH** (error) untuk menunjukkan tindakan berbahaya
- Tombol "Ya, Hapus" berwarna merah
- Tombol "Batal" berwarna abu-abu (cancel button)
- User bisa klik "Batal", klik diluar modal, atau tekan ESC untuk membatalkan

## Contoh Penggunaan di Laravel Blade

### Session Flash Messages
```blade
{{-- Di bagian bawah halaman sebelum </body> --}}

@if (session('success'))
    <script>
        showSuccess('{{ session('success') }}');
    </script>
@endif

@if (session('error'))
    <script>
        showError('{{ session('error') }}');
    </script>
@endif

@if (session('info'))
    <script>
        showInfo('{{ session('info') }}');
    </script>
@endif

@if (session('warning'))
    <script>
        showWarning('{{ session('warning') }}');
    </script>
@endif
```

### AJAX Success/Error
```javascript
// Contoh AJAX Delete dengan Konfirmasi
$('.btn-delete').click(function() {
    const id = $(this).data('id');
    const itemName = $(this).data('name');

    // Tampilkan konfirmasi delete (warna merah dengan button batal)
    confirmDelete({
        itemName: itemName,
        onConfirm: function() {
            // User klik "Ya, Hapus" - lakukan delete
            $.ajax({
                url: `/api/bmn/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        successToast('Data berhasil dihapus!');
                        // Reload DataTable
                        $('#myTable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    errorToast('Gagal menghapus data: ' + xhr.responseJSON.message);
                }
            });
        },
        onCancel: function() {
            // User klik "Batal" (optional)
            console.log('Delete cancelled');
        }
    });
});

// Contoh AJAX Create/Update
$('#formPengajuan').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: $(this).serialize(),
        success: function(response) {
            if (response.status === 'berhasil') {
                Swal.fire({
                    title: 'Sukses',
                    text: 'Data Berhasil Disimpan',
                    icon: 'success'
                }).then(function() {
                    window.location.href = '/dashboard';
                });
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                let errorMessage = '';
                Object.keys(errors).forEach(key => {
                    errorMessage += errors[key][0] + '\n';
                });
                showError(errorMessage, 'Validasi Gagal');
            } else {
                showError('Terjadi kesalahan sistem', 'Error!');
            }
        }
    });
});
```

## API Reference

### Swal.fire(options)
```javascript
Swal.fire({
    title: 'string',           // Judul alert
    text: 'string',            // Pesan alert
    icon: 'success|error|warning|info', // Tipe icon
    confirmButtonText: 'string' // Text tombol (default: 'OK')
});
```

### Helper Functions
```javascript
// Modal Alerts
showSuccess(message, title = 'Berhasil!')
showError(message, title = 'Error!')
showWarning(message, title = 'Peringatan!')
showInfo(message, title = 'Informasi')

// Toast Notifications
successToast(message, title = 'Berhasil!')
errorToast(message, title = 'Error!')
warningToast(message, title = 'Peringatan!')
infoToast(message, title = 'Info')

// Confirmation
confirmAction({
    title: 'string',
    message: 'string',
    confirmText: 'string',
    cancelText: 'string',
    onConfirm: function() {},
    onCancel: function() {}
})

confirmDelete({
    itemName: 'string',
    onConfirm: function() {},
    onCancel: function() {}
})
```

## Kustomisasi

### Mengubah Warna/Style
Edit file: `public/css/custom-alert.css`

```css
:root {
    --alert-success: #10b981;  /* Warna success */
    --alert-error: #ef4444;    /* Warna error */
    --alert-warning: #f59e0b;  /* Warna warning */
    --alert-info: #3b82f6;     /* Warna info */
}
```

### Mengubah Durasi Toast
```javascript
CustomAlert.toast({
    title: 'Berhasil!',
    message: 'Data tersimpan',
    icon: 'success',
    duration: 5000 // 5 detik
});
```

## Tips & Best Practices

1. **Gunakan Toast untuk feedback cepat**: Untuk operasi yang berhasil/gagal tapi tidak kritis, gunakan toast notification.

2. **Gunakan Modal untuk operasi penting**: Untuk konfirmasi delete, error kritis, atau informasi penting, gunakan modal.

3. **Consistency**: Gunakan title yang konsisten (misal: "Berhasil!", "Error!", dll).

4. **Error Messages**: Berikan pesan error yang jelas dan actionable.

5. **Success Redirect**: Untuk operasi create/update yang sukses, tampilkan alert kemudian redirect:
   ```javascript
   Swal.fire({
       title: 'Berhasil!',
       text: 'Data tersimpan',
       icon: 'success'
   }).then(function() {
       window.location.href = '/dashboard';
   });
   ```

## Troubleshooting

### Alert tidak muncul
- Pastikan sudah include `@include('includes.custom-alert')` di head
- Check console browser untuk error JavaScript
- Pastikan file `custom-alert.js` dan `alert-helpers.js` ter-load

### Styling tidak sesuai
- Clear browser cache
- Check apakah `custom-alert.css` ter-load
- Periksa CSS conflict dengan library lain

### Toast muncul di posisi salah
- Check CSS z-index
- Pastikan tidak ada CSS yang override `.custom-alert-toast`

## Migration dari SweetAlert2

Kode yang menggunakan `Swal.fire()` akan tetap berfungsi karena sistem ini backward compatible dengan SweetAlert2 API.

Contoh migrasi:
```javascript
// Kode lama (SweetAlert2)
Swal.fire({
    title: 'Success',
    text: 'Data saved',
    icon: 'success'
});

// Akan otomatis bekerja dengan sistem baru tanpa perubahan!
```

## Support

Jika menemukan bug atau membutuhkan fitur tambahan, silakan hubungi tim development.

---

**Version**: 1.0.0
**Last Updated**: 2025
**Compatibility**: Modern browsers (Chrome, Firefox, Safari, Edge)
