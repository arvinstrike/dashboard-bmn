# Contoh Konfirmasi Delete dengan Warna Merah

## Perubahan yang Dilakukan

### ✅ Sebelum (Warning - Kuning)
- Icon warning berwarna kuning/orange
- Hanya 1 tombol
- User bingung cara membatalkan

### ✨ Sesudah (Error - Merah)
- **Icon error berwarna MERAH** ❌
- **2 tombol**: "Ya, Hapus" (merah) dan "Batal" (abu-abu)
- User jelas bisa klik tombol "Batal"
- Bisa juga klik diluar modal atau tekan ESC

## Cara Menggunakan

### 1. Konfirmasi Delete Sederhana
```javascript
confirmDelete({
    itemName: 'data pengajuan BMN ini',
    onConfirm: function() {
        // Lakukan penghapusan
        console.log('Data dihapus');
    }
});
```

### 2. Konfirmasi Delete dengan AJAX
```javascript
$('.btn-delete').click(function() {
    const id = $(this).data('id');
    const itemName = $(this).data('name');

    confirmDelete({
        itemName: itemName,
        onConfirm: function() {
            // User klik "Ya, Hapus"
            $.ajax({
                url: `/bmn/delete/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    successToast('Data berhasil dihapus!');
                    location.reload(); // atau reload table
                },
                error: function(xhr) {
                    errorToast('Gagal menghapus: ' + xhr.responseJSON.message);
                }
            });
        },
        onCancel: function() {
            // User klik "Batal" (optional)
            infoToast('Penghapusan dibatalkan');
        }
    });
});
```

### 3. Konfirmasi Delete untuk DataTable
```javascript
// Dalam DataTable columns definition
{
    data: null,
    render: function(data, type, row) {
        return `<button class="btn btn-danger btn-sm btn-delete"
                        data-id="${row.id}"
                        data-name="${row.nama}">
                    <i class="bi bi-trash"></i> Hapus
                </button>`;
    }
}

// Event handler
$(document).on('click', '.btn-delete', function() {
    const id = $(this).data('id');
    const nama = $(this).data('name');

    confirmDelete({
        itemName: nama,
        onConfirm: function() {
            $.ajax({
                url: `/api/data/${id}`,
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    successToast('Data berhasil dihapus!');
                    $('#myTable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    errorToast('Gagal menghapus data');
                }
            });
        }
    });
});
```

### 4. Konfirmasi Delete dengan Form Submit
```javascript
$('#formDelete').submit(function(e) {
    e.preventDefault();

    const form = $(this);
    const itemName = form.find('input[name="item_name"]').val();

    confirmDelete({
        itemName: itemName,
        onConfirm: function() {
            // Submit form setelah konfirmasi
            form.off('submit').submit();
        }
    });
});
```

## Fitur Button Batal

User bisa membatalkan delete dengan 3 cara:
1. **Klik tombol "Batal"** (abu-abu)
2. **Klik di luar modal** (area gelap)
3. **Tekan tombol ESC** di keyboard

Semua cara di atas akan menutup modal dan memanggil callback `onCancel` jika ada.

## Styling

### Icon dan Button
- **Icon**: Lingkaran merah dengan icon X (error)
- **Button "Ya, Hapus"**:
  - Background: Gradient merah (#ef4444 → #dc2626)
  - Hover: Naik sedikit dengan shadow lebih besar
  - Posisi: Kanan

- **Button "Batal"**:
  - Background: Putih
  - Border: Abu-abu (#e5e7eb)
  - Color: Abu-abu gelap (#6b7280)
  - Hover: Background abu-abu terang, naik sedikit
  - Posisi: Kiri

### Animasi
- Modal muncul dengan fade in + scale up
- Icon dengan pulse effect
- Ripple effect di belakang icon
- Button hover dengan lift effect

## Testing

Untuk test langsung, buka:
**http://localhost/alert-demo.html**

Klik tombol "Delete Confirmation" untuk melihat alert dengan:
- Icon merah ❌
- 2 tombol (Ya, Hapus | Batal)
- Animasi smooth

## Contoh Implementasi di Blade

```blade
<table id="myTable" class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->nama }}</td>
            <td>
                <button class="btn btn-danger btn-sm"
                        onclick="deleteItem({{ $item->id }}, '{{ $item->nama }}')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
function deleteItem(id, nama) {
    confirmDelete({
        itemName: nama,
        onConfirm: function() {
            // Show loading
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass"></i> Menghapus...';

            // AJAX Delete
            fetch(`/bmn/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successToast('Data berhasil dihapus!');
                    // Remove row with animation
                    btn.closest('tr').style.transition = 'all 0.3s';
                    btn.closest('tr').style.opacity = '0';
                    setTimeout(() => {
                        btn.closest('tr').remove();
                    }, 300);
                } else {
                    errorToast(data.message || 'Gagal menghapus data');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-trash"></i> Hapus';
                }
            })
            .catch(error => {
                errorToast('Terjadi kesalahan sistem');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash"></i> Hapus';
            });
        },
        onCancel: function() {
            console.log('Delete cancelled by user');
        }
    });
}
</script>
```

## Tips Best Practices

1. **Gunakan nama item yang jelas** dalam confirmDelete agar user tahu apa yang akan dihapus:
   ```javascript
   // ❌ Kurang jelas
   confirmDelete({ itemName: 'data ini' })

   // ✅ Jelas
   confirmDelete({ itemName: 'Pengajuan BMN - Laptop HP (2025)' })
   ```

2. **Berikan feedback setelah delete**:
   ```javascript
   onConfirm: function() {
       // Delete...
       successToast('Data berhasil dihapus!'); // Good!
   }
   ```

3. **Handle error dengan baik**:
   ```javascript
   .catch(error => {
       errorToast('Gagal menghapus: ' + error.message);
   })
   ```

4. **Disable button saat proses delete** untuk prevent double click:
   ```javascript
   onConfirm: function() {
       btn.disabled = true;
       // ... AJAX delete
   }
   ```

5. **Update UI setelah delete berhasil**:
   ```javascript
   success: function() {
       successToast('Data dihapus!');
       // Option 1: Reload page
       location.reload();

       // Option 2: Reload DataTable
       $('#table').DataTable().ajax.reload();

       // Option 3: Remove row dengan animasi
       $row.fadeOut(300, function() { $(this).remove(); });
   }
   ```

## Troubleshooting

### Alert tidak muncul
- Pastikan sudah include custom-alert di head: `@include('includes.custom-alert')`
- Check console untuk error JavaScript

### Tombol tidak berfungsi
- Pastikan callback `onConfirm` sudah didefinisikan dengan benar
- Check apakah ada error di dalam callback

### Styling tidak sesuai
- Clear browser cache
- Pastikan file `custom-alert.css` sudah ter-load
- Check apakah ada CSS conflict

---

**Updated**: 2025
**Version**: 1.0
