{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/CreateFormRKBMN.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pengajuan RKBMN Bagian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('pengajuanrkbmnbagian.index') }}">Pengajuan RKBMN</a></li>
                        <li class="breadcrumb-item active">Tambah Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            {{-- Header Info Section --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="biro">Biro:</label>
                            <input type="text" id="biro" class="form-control" value="{{ $uraianBiro ?? 'Biro tidak tersedia' }}" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="operator">Operator Bagian:</label>
                            <input type="text" id="operator" class="form-control" value="{{ $uraianBagian ?? 'Bagian tidak tersedia' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Form --}}
            <form action="{{ route('pengajuanrkbmnbagian.store') }}" method="POST" id="formCreateRKBMN" enctype="multipart/form-data">
                @csrf

                {{-- Basic Information Card --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Dasar Pengajuan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun_anggaran">Tahun Anggaran <span class="text-danger">*</span></label>
                                    {{-- Mengubah dropdown menjadi input yang otomatis terisi tahun berjalan + 2 dan tidak bisa diubah --}}
                                    <input type="text"
                                           name="tahun_anggaran"
                                           id="tahun_anggaran"
                                           class="form-control"
                                           value="{{ date('Y') + 2 }}"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenistabel">Jenis Pengajuan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="jenistabel" id="jenistabel" required>
                                        <option value="">-- Pilih Jenis Pengajuan --</option>
                                        <option value="R1">R1 - Tanah dan/atau Bangunan Perkantoran</option>
{{--                                        <option value="R2">R2 - Tanah dan/atau Bangunan Pendidikan</option>--}}
                                        <option value="R3">R3 - Tanah dan/atau Bangunan Gedung Rumah Negara</option>
                                        <option value="R4">R4 - Kendaraan Jabatan</option>
                                        <option value="R5">R5 - Kendaraan Operasional</option>
                                        <option value="R6">R6 - Kendaraan Fungsional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dynamic Form Container --}}
                <div id="dynamic-form-container">
                    {{-- Form fields will be loaded here dynamically based on jenis pengajuan --}}
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Silakan pilih jenis pengajuan untuk melanjutkan
                    </div>
                </div>

                {{-- Document Upload Section --}}
                <div class="card mb-4" id="document-section" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-file-upload mr-2"></i>Dokumen Pendukung</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Penting:</strong> Dokumen pendukung diperlukan untuk melengkapi pengajuan. File harus dalam format PDF dengan ukuran maksimal 5MB.
                        </div>
                        <div class="form-group">
                            <label for="dokumen_pendukung"><i class="fas fa-paperclip mr-1"></i> Upload Dokumen Pendukung</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf">
                                <label class="custom-file-label" for="dokumen_pendukung">Pilih file PDF...</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb mr-1"></i> Tip: Pastikan dokumen sudah final sebelum diunggah untuk menghindari revisi berulang.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card">
                    <div class="card-body">
                        <div class="text-right">
                            <a href="{{ route('pengajuanrkbmnbagian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save mr-1"></i>Simpan Pengajuan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <p>Memuat form...</p>
    </div>
</div>

<style>
/* Loading overlay styles */
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.loading-spinner {
    text-align: center;
    color: white;
}

.loading-spinner p {
    margin-top: 20px;
    font-size: 16px;
}

/* Form styling */
.required-field {
    border-left: 3px solid #dc3545;
}

.form-group label.required:after {
    content: " *";
    color: #dc3545;
}

/* Currency input styling */
.currency-input {
    text-align: right;
}

/* Custom card styling */
.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body .row .col-md-4,
    .card-body .row .col-md-6 {
        margin-bottom: 15px;
    }
}
</style>

<script>
$(document).ready(function() {

    // ==========================================
    // INITIALIZATION
    // ==========================================

    let currentJenisForm = null;
    let formDataCache = {};

    // Initialize form components
    initializeFormComponents();

    /**
     * Initialize all form components and event listeners
     */
    function initializeFormComponents() {
        // Initialize file input
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }

        // Setup event listeners
        setupEventListeners();

        console.log('CreateFormRKBMN: Initialized successfully');
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Jenis pengajuan change event
        $('#jenistabel').on('change', handleJenisPengajuanChange);

        // Form submission
        $('#formCreateRKBMN').on('submit', handleFormSubmission);

        // File input change
        $('#dokumen_pendukung').on('change', handleFileInputChange);

        // Prevent accidental form leave
        $(window).on('beforeunload', handleBeforeUnload);
    }

    // ==========================================
    // EVENT HANDLERS
    // ==========================================

    /**
     * Handle jenis pengajuan change
     */
    function handleJenisPengajuanChange() {
        const selectedJenis = $(this).val();

        if (!selectedJenis) {
            resetDynamicForm();
            return;
        }

        // Cache current form data before switching
        if (currentJenisForm) {
            cacheFormData(currentJenisForm);
        }

        // Load new form
        loadDynamicForm(selectedJenis);
        currentJenisForm = selectedJenis;
    }

    /**
     * Handle form submission
     */
    function handleFormSubmission(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return false;
        }

        // Show loading
        showLoadingState();

        // Submit form via AJAX
        submitFormData();
    }

    /**
     * Handle file input change
     */
    function handleFileInputChange() {
        const file = this.files[0];
        const label = $(this).next('.custom-file-label');

        if (file) {
            // Validate file
            if (!validateFile(file)) {
                $(this).val('');
                label.text('Pilih file...');
                return;
            }

            label.text(file.name);
        } else {
            label.text('Pilih file...');
        }
    }

    /**
     * Handle before unload warning
     */
    function handleBeforeUnload(e) {
        if (isFormDirty()) {
            const message = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            e.returnValue = message;
            return message;
        }
    }

    // ==========================================
    // DYNAMIC FORM LOADING
    // ==========================================

    /**
     * Load dynamic form based on jenis pengajuan
     */
    function loadDynamicForm(jenisForm) {
        showLoadingOverlay();

        // FIXED: Reset submit button ke kondisi default sebelum load form baru
        resetSubmitButton();

        // AJAX call to load form component
        $.ajax({
            url: "{{ route('pengajuanrkbmnbagian.get-form-component') }}",
            method: 'GET',
            data: { jenis: jenisForm },
            success: function(response) {
                $('#dynamic-form-container').html(response.html);
                $('#document-section').show();

                // FIXED: Reset dokumen pendukung setiap kali berpindah form
                resetDocumentSection();

                // Restore cached data if available
                if (formDataCache[jenisForm]) {
                    restoreFormData(jenisForm);
                }

                // CRITICAL: Initialize form-specific components
                initializeFormSpecificComponents(jenisForm);

                // CRITICAL: Re-initialize component-specific scripts
                reinitializeComponentScripts(jenisForm);

                hideLoadingOverlay();

                console.log('Form component loaded successfully for:', jenisForm);
            },
            error: function(xhr, status, error) {
                console.error('Error loading form component:', error);
                showErrorMessage('Gagal memuat form. Silakan coba lagi.');
                hideLoadingOverlay();
            }
        });
    }

    /**
     * Reset dynamic form to initial state
     */
    function resetDynamicForm() {
        $('#dynamic-form-container').html(`
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i>
                Silakan pilih jenis pengajuan untuk melanjutkan
            </div>
        `);
        $('#document-section').hide();

        // FIXED: Reset submit button when clearing form
        resetSubmitButton();

        // FIXED: Reset dokumen pendukung
        resetDocumentSection();

        currentJenisForm = null;
    }

    /**
     * FIXED: Reset submit button ke kondisi default
     */
    function resetSubmitButton() {
        $('#submitBtn')
            .prop('disabled', false)
            .removeClass('btn-warning btn-secondary')
            .addClass('btn-success')
            .html('<i class="fas fa-save mr-1"></i>Simpan Pengajuan');

        console.log('Submit button reset to default state');
    }

    /**
     * FIXED: Reset document section ketika berpindah form
     */
    function resetDocumentSection() {
        // Reset file input
        $('#dokumen_pendukung').val('');

        // Reset label
        $('.custom-file-label').text('Pilih file...');

        // Reinitialize file input plugin
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.destroy();
            bsCustomFileInput.init();
        }

        console.log('Document section reset');
    }

    /**
     * Initialize form-specific components based on jenis form
     */
    function initializeFormSpecificComponents(jenisForm) {
        // Initialize Select2 dropdowns if present
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih opsi...'
            });
        }

        // Initialize currency inputs
        initializeCurrencyInputs();

        // Initialize form-specific validations
        initializeFormValidations(jenisForm);

        console.log(`Form components initialized for: ${jenisForm}`);
    }

    /**
     * Re-initialize component-specific scripts after AJAX load
     */
    function reinitializeComponentScripts(jenisForm) {
        console.log('Reinitializing scripts for:', jenisForm);

        // Clear any existing handlers to prevent duplicate bindings
        $('#golongan, #bidang, #kelompok, #subkelompok, #barang').off('change');

        // Initialize scripts based on jenis
        if (jenisForm === 'R1' || jenisForm === 'R3' || jenisForm === 'R4' || jenisForm === 'R5' || jenisForm === 'R6') {
            initializeSBSKScripts();
        }

        if (jenisForm === 'R1') {
            initializeR1Scripts();
        } else if (jenisForm === 'R3') {
            initializeR3Scripts();
        } else if (jenisForm === 'R4') {
            initializeR4Scripts();
        } else if (jenisForm === 'R5') {
            initializeR5Scripts();
        } else if (jenisForm === 'R6') {
            initializeR6Scripts();
        }

        console.log('Scripts reinitialized for:', jenisForm);
    }

    // ==========================================
    // FIXED SBSK DROPDOWN FUNCTIONALITY
    // ==========================================

    /**
     * Initialize SBSK scripts with FIXED dropdown logic
     */
    function initializeSBSKScripts() {
        console.log('Initializing SBSK scripts...');

        // Setup cascading dropdowns
        setupSBSKDropdowns();

        // Setup calculation for price fields
        $('#kuantitas, #harga_barang').off('input').on('input', calculateTotal);

        console.log('SBSK scripts initialized');
    }

    /**
     * FIXED: Setup SBSK cascading dropdowns dengan format {value, text}
     */
    function setupSBSKDropdowns() {
        console.log('Setting up SBSK dropdowns...');

        // Golongan change event
        $('#golongan').on('change', function() {
            console.log('Golongan changed to:', $(this).val());
            const kdGol = $(this).val();
            if (kdGol) {
                loadBidang(kdGol);
                resetDownstreamDropdowns(['bidang', 'kelompok', 'subkelompok', 'barang']);
            } else {
                resetDownstreamDropdowns(['bidang', 'kelompok', 'subkelompok', 'barang']);
            }
        });

        // Bidang change event
        $('#bidang').on('change', function() {
            console.log('Bidang changed to:', $(this).val());
            const kdBid = $(this).val();
            const kdGol = $('#golongan').val();
            if (kdBid && kdGol) {
                loadKelompok(kdGol, kdBid);
                resetDownstreamDropdowns(['kelompok', 'subkelompok', 'barang']);
            } else {
                resetDownstreamDropdowns(['kelompok', 'subkelompok', 'barang']);
            }
        });

        // Kelompok change event
        $('#kelompok').on('change', function() {
            console.log('Kelompok changed to:', $(this).val());
            const kdKel = $(this).val();
            const kdBid = $('#bidang').val();
            const kdGol = $('#golongan').val();
            if (kdKel && kdBid && kdGol) {
                loadSubkelompok(kdGol, kdBid, kdKel);
                resetDownstreamDropdowns(['subkelompok', 'barang']);
            } else {
                resetDownstreamDropdowns(['subkelompok', 'barang']);
            }
        });

        // Subkelompok change event
        $('#subkelompok').on('change', function() {
            console.log('Subkelompok changed to:', $(this).val());
            const kdSkel = $(this).val();
            const kdKel = $('#kelompok').val();
            const kdBid = $('#bidang').val();
            const kdGol = $('#golongan').val();
            if (kdSkel && kdKel && kdBid && kdGol) {
                loadBarang(kdGol, kdBid, kdKel, kdSkel);
                resetDownstreamDropdowns(['barang']);
            } else {
                resetDownstreamDropdowns(['barang']);
            }
        });

        // Barang change event
        $('#barang').on('change', function() {
            console.log('Barang changed to:', $(this).val());
            const selectedOption = $(this).find('option:selected');
            const kodeBarang = selectedOption.data('kode') || $(this).val();
            $('#kode_barang').val(kodeBarang);
        });

        console.log('SBSK dropdown events attached successfully');
    }

    /**
     * FIXED: Load dropdown functions dengan error handling
     */
    function loadBidang(kdGol) {
        loadDropdownData('bidang', `{{ url('pengajuanrkbmnbagian/dropdown/bidang') }}/${kdGol}?jenis=${currentJenisForm}`);
    }

    function loadKelompok(kdGol, kdBid) {
        loadDropdownData('kelompok', `{{ url('pengajuanrkbmnbagian/dropdown/kelompok') }}/${kdGol}/${kdBid}?jenis=${currentJenisForm}`);
    }

    function loadSubkelompok(kdGol, kdBid, kdKel) {
        loadDropdownData('subkelompok', `{{ url('pengajuanrkbmnbagian/dropdown/subkelompok') }}/${kdGol}/${kdBid}/${kdKel}?jenis=${currentJenisForm}`);
    }

    function loadBarang(kdGol, kdBid, kdKel, kdSkel) {
        loadDropdownData('barang', `{{ url('pengajuanrkbmnbagian/dropdown/barang') }}/${kdGol}/${kdBid}/${kdKel}/${kdSkel}`);
    }

    /**
     * FIXED: Generic dropdown loader untuk format {value, text}
     */
    function loadDropdownData(target, url) {
        const $dropdown = $(`#${target}`);

        $dropdown.html('<option value="">Loading...</option>').prop('disabled', true);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                console.log(`${target} response received:`, response);

                // Handle different response formats
                let data = Array.isArray(response) ? response : (response.data || []);

                if (!Array.isArray(data)) {
                    console.error(`Invalid response format for ${target}:`, response);
                    $dropdown.html('<option value="">Error: Invalid data format</option>');
                    return;
                }

                populateDropdown(target, data);
                $dropdown.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error(`Error loading ${target}:`, xhr.responseText);
                $dropdown.html('<option value="">Error loading data</option>');
                showErrorMessage(`Error loading ${target}: ${error}`);
            }
        });
    }

    /**
     * FIXED: Populate dropdown dengan format {value, text}
     */
    function populateDropdown(target, data) {
        const $dropdown = $(`#${target}`);
        const defaultText = getDefaultOptionText(target);

        $dropdown.html(`<option value="">${defaultText}</option>`);

        if (!data || data.length === 0) {
            $dropdown.append('<option value="" disabled>-- Tidak ada data --</option>');
            return;
        }

        data.forEach(function(item) {
            // FIXED: Menggunakan format {value, text} dari controller
            if (item.value && item.text) {
                $dropdown.append(`<option value="${item.value}">${item.text}</option>`);
            } else {
                console.warn('Invalid item format:', item);
            }
        });

        // Auto-select jika hanya ada 1 option (sesuai SBSK rules)
        if (data.length === 1) {
            $dropdown.val(data[0].value).trigger('change');
        }
    }

    function resetDownstreamDropdowns(dropdowns) {
        dropdowns.forEach(function(dropdown) {
            const defaultText = getDefaultOptionText(dropdown);
            $(`#${dropdown}`).html(`<option value="">${defaultText}</option>`).prop('disabled', true);
        });

        // Reset kode barang jika barang direset
        if (dropdowns.includes('barang')) {
            $('#kode_barang').val('');
        }
    }

    function getDefaultOptionText(target) {
        const texts = {
            'bidang': '-- Pilih Bidang --',
            'kelompok': '-- Pilih Kelompok --',
            'subkelompok': '-- Pilih Sub Kelompok --',
            'barang': '-- Pilih Barang --'
        };
        return texts[target] || '-- Pilih --';
    }

    function calculateTotal() {
        const kuantitas = parseInt($('#kuantitas').val()) || 0;
        const harga = parseFloat($('#harga_barang').val().replace(/[^\d]/g, '')) || 0;
        const total = kuantitas * harga;
        $('#total_anggaran').val(formatRupiah(total));
    }

    // ==========================================
    // FORM-SPECIFIC INITIALIZERS
    // ==========================================

    function initializeR1Scripts() {
        if (typeof window.initializeR1Scripts === 'function') {
            window.initializeR1Scripts();
        }
        console.log('R1 specific scripts initialized');
    }

    function initializeR3Scripts() {
        if (typeof window.initializeR3Scripts === 'function') {
            window.initializeR3Scripts();
        }
        console.log('R3 specific scripts initialized');
    }

    function initializeR4Scripts() {
        if (typeof window.initializeR4Scripts === 'function') {
            window.initializeR4Scripts();
        }
        console.log('R4 specific scripts initialized');
    }

    function initializeR5Scripts() {
        if (typeof window.initializeR5Scripts === 'function') {
            window.initializeR5Scripts();
        }
        console.log('R5 specific scripts initialized');
    }

    function initializeR6Scripts() {
        if (typeof window.initializeR6Scripts === 'function') {
            window.initializeR6Scripts();
        }
        console.log('R6 specific scripts initialized');
    }

    // ==========================================
    // FORM UTILITIES (Keep existing methods)
    // ==========================================

    function cacheFormData(jenisForm) {
        const formData = {};
        $(`#dynamic-form-container input, #dynamic-form-container select, #dynamic-form-container textarea`).each(function() {
            const name = $(this).attr('name');
            if (name) {
                formData[name] = $(this).val();
            }
        });
        formDataCache[jenisForm] = formData;
        console.log('Cached form data for:', jenisForm, formData);
    }

    function restoreFormData(jenisForm) {
        const cachedData = formDataCache[jenisForm];
        if (cachedData) {
            Object.keys(cachedData).forEach(function(name) {
                const $field = $(`[name="${name}"]`);
                if ($field.length && cachedData[name]) {
                    $field.val(cachedData[name]);
                }
            });
            console.log('Restored form data for:', jenisForm);
        }
    }

    function initializeCurrencyInputs() {
        // Event 'input' ini tetap dipertahankan untuk memformat angka menjadi rupiah
        // dan membersihkan input jika ada teks yang di-paste.
        $('.currency-input').on('input', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatRupiah(value));
            } else {
                // Jika input kosong, pastikan nilainya benar-benar kosong
                $(this).val('');
            }
        });

        // TAMBAHAN: Event 'keydown' untuk mencegah input selain angka
        $('.currency-input').on('keydown', function(e) {
            // Izinkan tombol-tombol fungsional (backspace, delete, panah, tab, dll.)
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                // Izinkan: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Izinkan: panah home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                return; // Jangan lakukan apa-apa
            }
            // Pastikan bahwa input adalah angka dan hentikan jika bukan
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }

    function validateForm() {
        let isValid = true;
        const errors = [];

        // Basic validation
        if (!$('#tahun_anggaran').val()) {
            errors.push('Tahun anggaran harus dipilih');
            isValid = false;
        }

        if (!$('#jenistabel').val()) {
            errors.push('Jenis pengajuan harus dipilih');
            isValid = false;
        }

        if (!isValid) {
            showValidationErrors(errors);
        }

        return isValid;
    }

    function validateFile(file) {
        // Check file type
        if (file.type !== 'application/pdf') {
            showErrorMessage('File harus berformat PDF');
            return false;
        }

        // Check file size (10MB)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            showErrorMessage('Ukuran file tidak boleh lebih dari 10MB');
            return false;
        }

        return true;
    }

    function submitFormData() {
        const formData = new FormData($('#formCreateRKBMN')[0]);

        $.ajax({
            url: $('#formCreateRKBMN').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoadingState();

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengajuan RKBMN berhasil disimpan.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('pengajuanrkbmnbagian.index') }}";
                    });
                } else {
                    showErrorMessage(response.message || 'Terjadi kesalahan saat menyimpan data');
                }
            },
            error: function(xhr) {
                hideLoadingState();
                handleAjaxError(xhr);
            }
        });
    }

    // ==========================================
    // UI HELPERS
    // ==========================================

    function showLoadingOverlay() {
        $('#loading-overlay').show();
    }

    function hideLoadingOverlay() {
        $('#loading-overlay').hide();
    }

    function showLoadingState() {
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');
    }

    function hideLoadingState() {
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Pengajuan');
    }

    function showValidationErrors(errors) {
        const errorHtml = errors.map(error => `<li>${error}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: `<ul style="text-align: left;">${errorHtml}</ul>`
        });
    }

    function showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    function handleAjaxError(xhr) {
        let errorMessage = 'Terjadi kesalahan yang tidak diketahui.';

        if (xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('<br>');
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    html: errorMessage
                });
                return;
            } else if (xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
        }

        showErrorMessage(errorMessage);
    }

    function isFormDirty() {
        return $('#dynamic-form-container').find('input, select, textarea').length > 0;
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function initializeFormValidations(jenisForm) {
        // Placeholder for form-specific validations
        console.log('Form validations initialized for:', jenisForm);
    }

    console.log('CreateFormRKBMN: Script loaded successfully');
});
</script>
@endsection
