@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pengajuan RKBMN</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('pengajuanrkbmnbagian.index') }}">Pengajuan RKBMN</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- Menampilkan alasan penolakan jika ada --}}
            @if($data->status === 'Ditolak oleh Koordinator' && $data->alasan_koordinator_bmn)
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Pengajuan Ditolak!</h5>
                    <strong>Alasan:</strong> {{ $data->alasan_koordinator_bmn }}
                </div>
            @endif

            <form action="{{ route('pengajuanrkbmnbagian.update', $data->id) }}" method="POST" id="formEditRKBMN" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Information Card --}}
                 <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title">Informasi Dasar Pengajuan</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tahun Anggaran</label>
                                {{-- Mengubah nilai agar selalu tahun berjalan + 2, sama seperti di form create --}}
                                <input type="text"
                                       class="form-control"
                                       name="tahun_anggaran" {{-- Pastikan ada atribut 'name' agar nilainya ikut ter-update --}}
                                       value="{{ date('Y') + 2 }}"
                                       readonly>
                                <input type="hidden" id="jenistabel_hidden" value="{{ $jenis }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Kode Pengajuan</label>
                                <input type="text" class="form-control" value="{{ $data->kode_jenis_pengajuan }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dynamic Form Container (akan diisi oleh AJAX) --}}
                <div id="dynamic-form-container">
                    <div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Memuat form...</p></div>
                </div>

                {{-- Document Upload Section --}}
                <div class="card mb-4" id="document-section">
                     <div class="card-header bg-info text-white">
                         <h3 class="card-title mb-0"><i class="fas fa-file-upload mr-2"></i>Dokumen Pendukung</h3>
                     </div>
                     <div class="card-body">
                         <div id="existing-doc-container" style="{{ !$data->dokumen_pendukung ? 'display: none;' : '' }}">
                             <div class="alert alert-success d-flex justify-content-between align-items-center">
                                 <span class="text-truncate" style="max-width: 70%;">
                                     <i class="fas fa-file-pdf mr-2"></i>
                                     <strong id="existing-doc-name">{{ basename($data->dokumen_pendukung) }}</strong>
                                 </span>
                                 <div>
                                     <a href="{{ route('pengajuanrkbmnbagian.downloadDokumen', $data->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                         <i class="fas fa-download"></i> Download
                                     </a>
                                     <button type="button" class="btn btn-sm btn-danger" id="delete-doc-btn">
                                         <i class="fas fa-trash"></i> Hapus
                                     </button>
                                 </div>
                             </div>
                         </div>
                         <div id="upload-doc-container" style="{{ $data->dokumen_pendukung ? 'display: none;' : '' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                File harus dalam format PDF dengan ukuran maksimal 5MB.
                            </div>
                            <div class="form-group">
                                <label for="dokumen_pendukung"><i class="fas fa-paperclip mr-1"></i> Upload Dokumen Baru</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf">
                                    <label class="custom-file-label" for="dokumen_pendukung">Pilih file PDF...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb mr-1"></i> Tip: Upload dokumen baru hanya jika diperlukan perubahan.
                                </small>
                            </div>
                         </div>
                     </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card">
                    <div class="card-body text-right">
                        <a href="{{ route('pengajuanrkbmnbagian.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const pengajuanId = {{ $data->id }};
    const jenisForm = '{{ $jenis }}';
    const csrfToken = '{{ csrf_token() }}';
    let sbskCodes = {}; // Akan diisi oleh AJAX

    $(document).on('change', '#dokumen_pendukung', function() {
        const file = this.files[0];
        // Cari label yang berhubungan dengan input ini
        const label = $(this).siblings('.custom-file-label');

        if (file) {
            // Langsung perbarui teks label dengan nama file yang dipilih
            label.text(file.name);
            console.log('Label diperbarui secara manual ke:', file.name);
        } else {
            // Jika tidak ada file, kembalikan ke teks default
            label.text('Pilih file...');
        }
    });

    // ==========================================
    // 1. MEMUAT KOMPONEN EDIT SECARA DINAMIS
    // ==========================================
    $.ajax({
        url: `{{ route('pengajuanrkbmnbagian.get_edit_form_component', $data->id) }}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#dynamic-form-container').html(response.html);
                sbskCodes = response.sbskCodes || {};

                console.log('SBSK Codes received:', sbskCodes);

                // FIXED: Initialize cascading dropdowns untuk edit mode
                initializeCascadingDropdownsEdit();

                // Panggil skrip spesifik komponen setelah dropdown ready
                if (typeof window[`initialize${jenisForm}EditScripts`] === 'function') {
                    window[`initialize${jenisForm}EditScripts`]();
                }
            } else {
                 $('#dynamic-form-container').html(`<div class="alert alert-danger">${response.message || 'Gagal memuat form.'}</div>`);
            }
        },
        error: function(xhr) {
            $('#dynamic-form-container').html(`<div class="alert alert-danger">Terjadi kesalahan server saat memuat form. Silakan coba lagi.</div>`);
        }
    });

    // ==========================================
    // 2. FIXED DROPDOWN BERTINGKAT UNTUK EDIT MODE
    // ==========================================
    function initializeCascadingDropdownsEdit() {
        console.log('Initializing cascading dropdowns for edit mode...');

        // Setup event listeners untuk cascade (same as create)
        $('#golongan').off('change.edit').on('change.edit', function() {
            loadDropdownEdit('bidang', [$(this).val()]);
        });
        $('#bidang').off('change.edit').on('change.edit', function() {
            loadDropdownEdit('kelompok', [$('#golongan').val(), $(this).val()]);
        });
        $('#kelompok').off('change.edit').on('change.edit', function() {
            loadDropdownEdit('subkelompok', [$('#golongan').val(), $('#bidang').val(), $(this).val()]);
        });
        $('#subkelompok').off('change.edit').on('change.edit', function() {
            loadDropdownEdit('barang', [$('#golongan').val(), $('#bidang').val(), $('#kelompok').val(), $(this).val()]);
        });
        $('#barang').off('change.edit').on('change.edit', function() {
            $('#kode_barang').val($(this).val());
        });

        // Start the cascade with existing data
        loadInitialDropdownChain();
    }

    /**
     * Load initial dropdown chain with pre-population (FIXED)
     */
    function loadInitialDropdownChain() {
        console.log('Loading initial dropdown chain with sbskCodes:', sbskCodes);

        // FIXED: Golongan sudah ada di DOM, tidak perlu AJAX call
        // Step 1: Pre-select golongan (sudah ada di DOM)
        if (sbskCodes.gol) {
            $('#golongan').val(sbskCodes.gol);

            // Step 2: Load bidang dan pre-select
            loadDropdownEdit('bidang', [sbskCodes.gol], function() {
                if (sbskCodes.bid) {
                    $('#bidang').val(sbskCodes.bid);

                    // Step 3: Load kelompok dan pre-select
                    loadDropdownEdit('kelompok', [sbskCodes.gol, sbskCodes.bid], function() {
                        if (sbskCodes.kel) {
                            $('#kelompok').val(sbskCodes.kel);

                            // Step 4: Load subkelompok dan pre-select
                            loadDropdownEdit('subkelompok', [sbskCodes.gol, sbskCodes.bid, sbskCodes.kel], function() {
                                if (sbskCodes.skel) {
                                    $('#subkelompok').val(sbskCodes.skel);

                                    // Step 5: Load barang dan pre-select (final)
                                    loadDropdownEdit('barang', [sbskCodes.gol, sbskCodes.bid, sbskCodes.kel, sbskCodes.skel], function() {
                                        if (sbskCodes.brg) {
                                            $('#barang').val(sbskCodes.brg);
                                            $('#kode_barang').val(sbskCodes.brg);
                                        }
                                        console.log('Dropdown cascade completed with pre-population');
                                    });
                                }
                            });
                        }
                    });
                }
            });
        } else {
            console.warn('No golongan code found in sbskCodes');
        }
    }

    /**
     * Load dropdown data untuk edit mode (FIXED - No golongan route)
     */
    function loadDropdownEdit(target, params, callback) {
        // FIXED: Skip golongan karena tidak ada route
        if (target === 'golongan') {
            console.warn('Golongan should not be loaded via AJAX - already in DOM');
            return;
        }

        const $dropdown = $(`#${target}`);
        $dropdown.html('<option value="">Memuat...</option>').prop('disabled', true);

        const url = `{{ url('pengajuanrkbmnbagian/dropdown') }}/${target}${params.length > 0 ? '/' + params.join('/') : ''}?jenis=${jenisForm}`;

        console.log(`Loading ${target} from:`, url);

        $.get(url, function(data) {
            console.log(`${target} data received:`, data);

            $dropdown.html('<option value="">-- Pilih --</option>');
            if (data && data.length > 0) {
                data.forEach(opt => {
                    $dropdown.append(`<option value="${opt.value}">${opt.text}</option>`);
                });
            }
            $dropdown.prop('disabled', false);

            // Execute callback if provided
            if (typeof callback === 'function') {
                callback();
            }

        }).fail(function(xhr) {
            console.error(`Failed to load ${target}:`, xhr);
            $dropdown.html('<option value="">Gagal memuat</option>');
        });
    }

    // ==========================================
    // 3. EXISTING FUNCTIONALITY (Document & Submit)
    // ==========================================
    $(document).on('click', '#delete-doc-btn', function() {
        Swal.fire({
            title: 'Hapus Dokumen?',
            text: "Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ route('pengajuanrkbmnbagian.delete_document', $data->id) }}`,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        // =================================================================
                        // LOGIKA BARU DITERAPKAN DI SINI (MENIRU CREATE FORM)
                        // =================================================================

                        // 1. Reset value dari input file untuk membersihkannya
                        $('#dokumen_pendukung').val('');

                        // 2. Reset teks pada label agar kembali ke default
                        $('.custom-file-label[for="dokumen_pendukung"]').text('Pilih file...');

                        // 3. Hancurkan (destroy) instance lama dan inisialisasi ulang library
                        //    Ini adalah langkah paling penting.
                        if (typeof bsCustomFileInput !== 'undefined') {
                            bsCustomFileInput.destroy();
                            bsCustomFileInput.init();
                        }

                        // 4. Atur visibilitas kontainer
                        $('#existing-doc-container').hide();
                        $('#upload-doc-container').show();

                        // 5. Tampilkan notifikasi sukses
                        Swal.fire('Terhapus!', res.message, 'success');

                        // =================================================================
                        // AKHIR DARI LOGIKA BARU
                        // =================================================================
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.message || 'Gagal menghapus dokumen.', 'error');
                    }
                });
            }
        });
    });

    $('#formEditRKBMN').on('submit', function(e) {
        e.preventDefault();
        const form = this; // Gunakan raw DOM element
        const url = $(form).attr('action');
        const submitButton = $('#submitBtn');

        // Buat FormData dari form
        const formData = new FormData(form);

        // =================================================================
        // PERUBAHAN UTAMA: Tambahkan file secara manual untuk memastikan
        // =================================================================
        const fileInput = document.getElementById('dokumen_pendukung');

        // Cek jika ada file yang dipilih di dalam input
        if (fileInput && fileInput.files.length > 0) {
            // Hapus dulu key 'dokumen_pendukung' yang mungkin sudah ada (untuk jaga-jaga)
            formData.delete('dokumen_pendukung');
            // Tambahkan (append) file yang sebenarnya ada di input. Ini cara paling pasti.
            formData.append('dokumen_pendukung', fileInput.files[0]);

            // Anda bisa cek di console browser (F12) untuk memastikan file ditambahkan
            console.log('File ditemukan dan ditambahkan secara manual ke FormData:', fileInput.files[0]);
        } else {
            console.log('Tidak ada file baru yang dipilih untuk di-upload.');
        }
        // =================================================================
        // AKHIR PERUBAHAN
        // =================================================================

        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData, // formData sekarang dijamin berisi file (jika dipilih)
            processData: false,
            contentType: false,
            success: function(response) {
                submitButton.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Perubahan');
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    }).then(() => {
                        window.location.href = "{{ route('pengajuanrkbmnbagian.index') }}";
                    });
                }
            },
            error: function(xhr) {
                submitButton.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Perubahan');
                const errors = xhr.responseJSON.errors;
                let errorMsg = '<ul>';
                if (errors) {
                    $.each(errors, function(key, value) {
                        errorMsg += '<li>' + value[0] + '</li>';
                    });
                } else {
                    errorMsg += '<li>' + (xhr.responseJSON.message || 'Terjadi kesalahan tidak diketahui.') + '</li>';
                }
                errorMsg += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: errorMsg
                });
            }
        });
    });

    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }

        /**
     * Helper untuk format Rupiah
     */
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Gunakan event delegation pada 'document' agar berfungsi untuk elemen
    // Event 'input' untuk memformat angka menjadi Rupiah saat mengetik atau paste
    $(document).on('input', '.currency-input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            $(this).val(formatRupiah(value));
        } else {
            $(this).val('');
        }
    });

        $(document).on('keydown', '.currency-input', function(e) {
        // Izinkan tombol fungsional (backspace, delete, panah, tab, dll.)
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Izinkan: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Izinkan: panah home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        // Pastikan bahwa input adalah angka dan hentikan jika bukan
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
