@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <h1>Pengajuan RKBMN Bagian Non SBSK</h1>

            <div class="mb-3 row">
                <div class="col-sm-6">
                    <label for="biro">Biro:</label>
                    <input type="text" id="biro" class="form-control" value="{{ $uraianBiro }}" readonly>
                </div>
                <div class="col-sm-6">
                    <label for="operator">Operator Bagian:</label>
                    <input type="text" id="operator" class="form-control" value="{{ $uraianBagian }}" readonly>
                </div>
            </div>

            <form action="{{ route('pengajuan.reguler.store') }}" method="POST" id="formCreatePengajuan">
                @csrf

                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="tahun_anggaran">Tahun Anggaran</label>
                        <select name="tahun_anggaran" id="tahun_anggaran" class="form-control" required>
                            <option value="">-- Pilih Tahun Anggaran --</option>
                            {{-- <option value="{{ $tahunAnggaran }}">{{ $tahunAnggaran }}</option> --}}
                            <option value="{{ $tahunAnggaran + 1 }}">{{ $tahunAnggaran + 1 }}</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="tipe_pengajuan">Tipe Pengajuan</label>
                        <select name="tipe_pengajuan_display" id="tipe_pengajuan_display" class="form-control" required readonly disabled>
                            <option value="revisi">Revisi Anggaran Barang</option>
                            <option value="usulan">Usulan Anggaran Barang</option>
                        </select>
                        <input type="hidden" name="tipe_pengajuan" id="tipe_pengajuan_hidden" value="">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <label for="keterangan_editable">Tujuan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="4" placeholder="Masukkan tujuan..."></textarea>
                    </div>
                </div>

                <div id="infoUmum">
                    <div class="form-group">
                        <label for="id_bagian_pelaksana">Pelaksana Pengadaan</label>
                        <select name="id_bagian_pelaksana" id="id_bagian_pelaksana" class="form-control" required>
                            <option value="">-- Pilih Bagian Pelaksana --</option>
                            @foreach($pelaksanaOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->uraianbagian }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h3>Input Barang dan Pemeliharaan</h3>

                <!-- Responsive Table Container -->
                <div class="table-responsive-custom">
                    <div class="table-scroll-hint">
                        <i class="fa fa-info-circle"></i>
                        <span class="d-none d-md-inline">Geser tabel ke kiri/kanan untuk melihat semua kolom</span>
                        <span class="d-md-none">Geser tabel ke kiri/kanan untuk melihat semua kolom</span>
                    </div>

                    <table class="table table-bordered table-hover" id="tabelBarang">
                        <thead class="table-header-custom">
                            <tr>
                                <th class="col-no">No</th>
                                <th class="col-barang">Barang</th>
                                <th class="col-keterangan">Keterangan</th>
                                <th class="col-qty">Qty</th>
                                <th class="col-harga">Harga Satuan</th>
                                <th class="col-total">Total Harga</th>
                                <th class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="barang-row" data-index="0">
                                <td class="nomor text-center">1</td>
                                <td class="col-barang">
                                    <select name="barang[0][kode_barang]" class="form-control dropdown-barang">
                                        <option value="">-- Pilih Barang --</option>
                                    </select>
                                </td>
                                <td class="col-keterangan">
                                    <textarea name="barang[0][keterangan_barang]" class="form-control keterangan-input"
                                        rows="2" placeholder="Masukkan keterangan barang..."></textarea>
                                </td>
                                <td class="col-qty">
                                    <input type="number" name="barang[0][kuantitas]" class="form-control kuantitas-input text-center" min="1" value="1">
                                </td>
                                <td class="col-harga">
                                    <input type="text" name="barang[0][harga]" class="form-control harga-input text-end" data-raw-value="0" placeholder="0">
                                </td>
                                <td class="col-total">
                                    <input type="text" name="barang[0][total]" class="form-control total-input text-end bg-light" readonly>
                                </td>
                                <td class="col-aksi text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Hapus baris">
                                        <i class="fa fa-trash"></i>
                                        <span class="d-none d-lg-inline ms-1">Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="tambahBaris">
                    <i class="fa fa-plus"></i> Tambah Baris
                </button>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Pengajuan
                    </button>
                    <a href="{{ route('pengajuan.reguler.index') }}" class="btn btn-light">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

<!-- JavaScript Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }
    .select2-dropdown {
        min-width: 300px;
    }
    .barang-row td {
        vertical-align: middle;
    }
    #keterangan_editable:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
    .keterangan-input {
        resize: vertical;
        height: 38px !important;        /* Fixed height sama dengan input lain */
        min-height: 38px !important;    /* Minimum height tetap */
        max-height: 120px;              /* Maximum height untuk resize */
        font-size: 0.85rem;
        padding: 6px 8px;               /* Padding yang konsisten */
        line-height: 1.4;               /* Line height yang baik untuk readability */
        overflow-y: auto;               /* Scroll vertikal jika teks terlalu panjang */
        box-sizing: border-box;         /* Include padding dalam perhitungan height */
    }

    /* =============================================================================
       HORIZONTAL SCROLL TABLE STYLES
       ============================================================================= */

    .table-responsive-custom {
        overflow-x: auto;
        overflow-y: visible;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background: white;
        position: relative;
    }

    .table-scroll-hint {
        padding: 8px 15px;
        font-size: 0.85rem;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .table-scroll-hint i {
        margin-right: 5px;
    }

    #tabelBarang {
        margin-bottom: 0;
        min-width: 1200px; /* Minimum width untuk memastikan semua kolom terlihat dengan baik */
        white-space: nowrap;
    }

    .table-header-custom th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Column Widths - Optimized untuk readability */
    .col-no {
        width: 60px;
        min-width: 60px;
        max-width: 60px;
    }

    .col-barang {
        width: 280px;
        min-width: 280px;
        max-width: 280px;
    }

    .col-keterangan {
        width: 220px; /* Diperkecil dari 250px */
        min-width: 220px;
        max-width: 220px;
    }

    .col-qty {
        width: 100px;
        min-width: 100px;
        max-width: 100px;
    }

    .col-harga {
        width: 180px;
        min-width: 180px;
        max-width: 180px;
    }

    .col-total {
        width: 180px;
        min-width: 180px;
        max-width: 180px;
    }

    .col-aksi {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    /* Table Cell Styling */
    #tabelBarang td {
        vertical-align: middle;
        padding: 8px 6px; /* Diperkecil dari 12px 8px */
        border-color: #e9ecef;
        white-space: normal; /* Allow text wrapping in cells */
    }

    /* Form Controls dalam Table */
    #tabelBarang .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        font-size: 0.9rem;
        width: 100%;
    }

    #tabelBarang .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    #tabelBarang .keterangan-input {
        height: 38px !important;        /* Konsisten dengan form-control lain */
        min-height: 38px !important;
        font-size: 0.85rem;
        padding: 6px 8px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    #tabelBarang .keterangan-input:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        outline: none;
    }

    /* Untuk mobile, ukuran sedikit dikurangi */
    @media (max-width: 768px) {
        .keterangan-input,
        #tabelBarang .keterangan-input {
            height: 34px !important;
            min-height: 34px !important;
            font-size: 0.8rem;
            padding: 5px 6px;
        }
    }

    #tabelBarang .kuantitas-input,
    #tabelBarang .harga-input,
    #tabelBarang .total-input {
        font-weight: 500;
    }

    /* Styling untuk readonly total field */
    #tabelBarang .total-input {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .table-responsive-custom {
            border-radius: 0.25rem;
        }

        .table-scroll-hint {
            font-size: 0.8rem;
            padding: 6px 10px;
        }

        #tabelBarang {
            min-width: 1000px; /* Reduced untuk mobile */
        }

        #tabelBarang td {
            padding: 6px 4px; /* Padding lebih kecil untuk mobile */
        }

        #tabelBarang .form-control {
            height: 38px;                   /* Tinggi standar untuk semua input */
            padding: 6px 8px;
            font-size: 0.9rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        #tabelBarang .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        #tabelBarang .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 4px 8px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        #tabelBarang .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

        #tabelBarang .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .col-barang {
            width: 250px;
            min-width: 250px;
        }

        .col-keterangan {
            width: 180px; /* Diperkecil untuk mobile */
            min-width: 180px;
        }

        #tabelBarang .keterangan-input {
            min-height: 28px; /* Lebih kecil di mobile */
        }
    }

    /* Scroll Bar Styling */
    .table-responsive-custom::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive-custom::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-responsive-custom::-webkit-scrollbar-thumb {
        background: #6c757d;
    }

    .table-responsive-custom::-webkit-scrollbar-thumb:hover {
        background: #495057;
    }

    /* Select2 adjustments untuk table */
    .table-responsive-custom .select2-container {
        width: 100% !important;
        z-index: 1050; /* Higher z-index */
    }

    .table-responsive-custom .select2-container--open .select2-dropdown {
        width: 100% !important;          /* ikuti lebar sel */
        min-width: 0 !important;         /* buang batas 300 px */
        max-width: calc(100vw - 32px) !important; /* jangan melebihi viewport */
        box-sizing: border-box;
    }

    .table-responsive-custom .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
    }

    /* Fix dropdown positioning and z-index */
    .select2-dropdown {
        z-index: 1060 !important; /* Even higher for dropdown */
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }

    /* Ensure dropdown appears above table */
    .select2-container--open .select2-dropdown {
        z-index: 1060 !important;
    }

    /* Fix for dropdown positioning in scrollable container */
    .table-responsive-custom {
        position: static !important; /* Changed from relative */
    }

    /* Select2 results styling */
    .select2-results__options {
        max-height: 300px !important;
        overflow-y: auto;
    }

    /* Group styling */
    .select2-results__group {
        padding: 8px 12px;
        font-weight: bold;
        color: #495057;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Option styling */
    .select2-results__option {
        white-space: normal;             /* override bawaan nowrap */
        word-break: break-word;
    }

    .select2-results__option--highlighted {
        background-color: #007bff !important;
        color: white !important;
    }
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
$(document).ready(function() {
    // Configuration
    const config = {
        tahunSession: parseInt("{{ $tahunAnggaran }}"),
        routes: {
            barangOptions: "{{ route('barang.getOptions') }}"
        },
        // Item statis pemeliharaan
        staticPemeliharaan: [
            {
                id: 'Pemeliharaan Gedung dan Bangunan',
                text: 'Pemeliharaan Gedung dan Bangunan',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Gedung dan Bangunan Lainnya',
                text: 'Pemeliharaan Gedung dan Bangunan Lainnya',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Peralatan dan Mesin',
                text: 'Pemeliharaan Peralatan dan Mesin',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Peralatan dan Mesin Lainnya',
                text: 'Pemeliharaan Peralatan dan Mesin Lainnya',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Jalan',
                text: 'Pemeliharaan Jalan',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Irigasi',
                text: 'Pemeliharaan Irigasi',
                category: 'belanjaPemeliharaan'
            },
            {
                id: 'Pemeliharaan Jaringan',
                text: 'Pemeliharaan Jaringan',
                category: 'belanjaPemeliharaan'
            }
        ],
        staticPemeliharaanPersediaan: [
            {
                id: 'Persediaan Pemeliharaan Gedung Bangunan',
                text: 'Persediaan Pemeliharaan Gedung Bangunan',
                category: 'belanjaPemeliharaanPersediaan'
            },
            {
                id: 'Persediaan Pemeliharaan Peralatan dan Mesin',
                text: 'Persediaan Pemeliharaan Peralatan dan Mesin',
                category: 'belanjaPemeliharaanPersediaan'
            },
            {
                id: 'Persediaan Pemeliharaan Jalan dan Jembatan',
                text: 'Persediaan Pemeliharaan Jalan dan Jembatan',
                category: 'belanjaPemeliharaanPersediaan'
            },
            {
                id: 'Persediaan Pemeliharaan Irigasi',
                text: 'Persediaan Pemeliharaan Irigasi',
                category: 'belanjaPemeliharaanPersediaan'
            },
            {
                id: 'Persediaan Pemeliharaan Jaringan',
                text: 'Persediaan Pemeliharaan Jaringan',
                category: 'belanjaPemeliharaanPersediaan'
            },
            {
                id: 'Persediaan Pemeliharaan Lainnya',
                text: 'Persediaan Pemeliharaan Lainnya',
                category: 'belanjaPemeliharaanPersediaan'
            }
        ]
    };

    // Global variables
    let allBarangOptions = [];
    let isInitializing = true;
    const sessionStorageKey = 'pengajuanFormData'; // Key untuk sessionStorage
    let autoSaveInterval; // Variable to hold the interval for auto-save

    // Utility functions
    const utils = {
        formatNumber: function(value) {
            if (value === null || value === undefined || value === '') return '';
            let numValue = parseFloat(String(value).replace(/[^0-9]/g, ''));
            if (isNaN(numValue)) return '';
            // Format dengan pemisah ribuan titik (format Indonesia)
            return numValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        removeFormat: function(value) {
            // Hapus semua titik (pemisah ribuan) dan karakter non-numeric
            return String(value).replace(/\./g, '').replace(/[^0-9]/g, '');
        },

        parseNumber: function(value) {
            const cleaned = this.removeFormat(value);
            return parseFloat(cleaned) || 0;
        },

        showLoading: function() {
            $('#loadingOverlay').show();
        },

        hideLoading: function() {
            $('#loadingOverlay').hide();
        },

        showAlert: function(type, title, message) {
            Swal.fire({
                title: title,
                text: message,
                icon: type,
                confirmButtonText: 'OK'
            });
        }
    };

    // Row template function - UPDATED dengan kolom keterangan dan styling yang konsisten
    function createRowTemplate(index) {
        return `
            <tr class="barang-row" data-index="${index}">
                <td class="nomor text-center">${index + 1}</td>
                <td class="col-barang">
                    <select name="barang[${index}][kode_barang]" class="form-control dropdown-barang">
                        <option value="">-- Pilih Barang --</option>
                    </select>
                </td>
                <td class="col-keterangan">
                    <textarea name="barang[${index}][keterangan_barang]" class="form-control keterangan-input"
                        rows="2" placeholder="Masukkan keterangan barang..."></textarea>
                </td>
                <td class="col-qty">
                    <input type="number" name="barang[${index}][kuantitas]" class="form-control kuantitas-input text-center" min="1" value="1">
                </td>
                <td class="col-harga">
                    <input type="text" name="barang[${index}][harga]" class="form-control harga-input text-end" data-raw-value="0" placeholder="0">
                </td>
                <td class="col-total">
                    <input type="text" name="barang[${index}][total]" class="form-control total-input text-end bg-light" readonly>
                </td>
                <td class="col-aksi text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Hapus baris">
                        <i class="fa fa-trash"></i>
                        <span class="d-none d-lg-inline ms-1">Hapus</span>
                    </button>
                </td>
            </tr>
        `;
    }

    // Select2 initialization dengan grouping - TIDAK ADA VALIDASI DUPLIKASI
    function initializeSelect2(selector) {
        if (!selector || selector.length === 0) return;

        selector.each(function() {
            const $select = $(this);

            // Destroy existing Select2 if present
            if ($select.hasClass("select2-hidden-accessible")) {
                $select.select2('destroy');
            }

            // Prepare data dengan grouping
            const groupedData = [
                {
                    text: '📋 BELANJA PEMELIHARAAN',
                    children: allBarangOptions.filter(item => item.category === 'belanjaPemeliharaan')
                },
                {
                    text: '🏗️ BELANJA PEMELIHARAAN PERSEDIAAN',
                    children: allBarangOptions.filter(item => item.category === 'belanjaPemeliharaanPersediaan')
                },
                {
                    text: '📦 BARANG',
                    children: allBarangOptions.filter(item => item.category === 'database')
                }
            ];

            // Initialize Select2 dengan grouping dan styling
            $select.select2({
                placeholder: "-- Pilih Barang --",
                allowClear: false,
                width: '100%',
                dropdownParent: $('body'), // PERBAIKAN: Attach ke body untuk menghindari z-index issues
                data: groupedData,
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (data) {
                    if (data.loading) return data.text;

                    if (data.children) {
                        // Group header
                        return $('<span class="select2-results__group">' + data.text + '</span>');
                    }

                    // Individual options dengan styling berdasarkan category
                    let className = 'select2-results__option--database';
                    if (data.category === 'belanjaPemeliharaan') {
                        className = 'select2-results__option--pemeliharaan';
                    } else if (data.category === 'belanjaPemeliharaanPersediaan') {
                        className = 'select2-results__option--pemeliharaan-persediaan';
                    }

                    return $('<span class="' + className + '">' + data.text + '</span>');
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });
        });
    }

    // Fetch barang options dan gabungkan dengan item statis
    function fetchBarangOptions() {
        utils.showLoading();

        return $.ajax({
            url: config.routes.barangOptions,
            type: "GET",
            dataType: "json",
            success: function(data) {
                console.log("Data from database:", data);

                // Konversi data dari database
                const dbOptions = data.map(function(item) {
                    return {
                        id: item.kd_brg,
                        text: item.ur_sskel + ' (' + item.kd_brg + ')',
                        category: 'database'
                    };
                });

                // Gabungkan dengan item statis (item pemeliharaan di atas)
                allBarangOptions = [
                    ...config.staticPemeliharaan,
                    ...config.staticPemeliharaanPersediaan,
                    ...dbOptions
                ];

                console.log("All barang options:", allBarangOptions);

                // Initialize all existing dropdowns
                initializeSelect2($('.dropdown-barang'));
                isInitializing = false;

                // Restore form data after dropdowns are ready
                restoreFormData();

                // Show success message
                console.log(`Loaded ${config.staticPemeliharaan.length} static items and ${dbOptions.length} database items`);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching barang options:", error);

                // Jika gagal fetch dari database, gunakan item statis saja
                allBarangOptions = [
                    ...config.staticPemeliharaan,
                    ...config.staticPemeliharaanPersediaan
                ];
                initializeSelect2($('.dropdown-barang'));
                isInitializing = false;

                // Restore form data even if fetching failed
                restoreFormData();

                utils.showAlert('warning', 'Peringatan',
                    'Gagal memuat data barang dari database. Hanya item pemeliharaan yang tersedia. ' +
                    'Error: ' + (xhr.responseJSON?.message || error)
                );
            },
            complete: function() {
                utils.hideLoading();
            }
        });
    }

    // Calculate row total
    function calculateRowTotal($row) {
        const quantity = parseInt($row.find('.kuantitas-input').val()) || 0;
        const priceInput = $row.find('.harga-input');
        const rawPrice = utils.parseNumber(priceInput.val());
        const total = quantity * rawPrice;

        // Store raw value
        priceInput.attr('data-raw-value', rawPrice);

        // Update total display
        $row.find('.total-input').val(utils.formatNumber(total));
    }

    // Reindex all rows
    function reindexRows() {
        $('#tabelBarang tbody tr').each(function(index) {
            const $row = $(this);

            // Update row number
            $row.find('.nomor').text(index + 1);
            $row.attr('data-index', index);

            // Update form field names
            $row.find('input, select, textarea').each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                if (name && name.includes('[')) {
                    const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    $field.attr('name', newName);
                }
            });
        });
    }

    // Function to save form data to sessionStorage
    function saveFormData() {
        const formData = {
            tahun_anggaran: $('#tahun_anggaran').val(),
            tipe_pengajuan_display: $('#tipe_pengajuan_display').val(),
            tipe_pengajuan_hidden: $('#tipe_pengajuan_hidden').val(),
            keterangan: $('#keterangan').val(),
            id_bagian_pelaksana: $('#id_bagian_pelaksana').val(),
            barangItems: []
        };

        $('#tabelBarang .barang-row').each(function() {
            const $row = $(this);
            const barang = $row.find('.dropdown-barang').val();
            // Only save rows that have a selected item
            if (barang) {
                formData.barangItems.push({
                    kode_barang: barang,
                    keterangan_barang: $row.find('.keterangan-input').val(),
                    kuantitas: $row.find('.kuantitas-input').val(),
                    harga: $row.find('.harga-input').attr('data-raw-value') // Save raw value
                });
            }
        });

        sessionStorage.setItem(sessionStorageKey, JSON.stringify(formData));
        // console.log("Form data saved to sessionStorage:", formData); // Uncomment for debugging auto-save
    }

    // Function to restore form data from sessionStorage
    function restoreFormData() {
        const savedData = sessionStorage.getItem(sessionStorageKey);
        if (savedData) {
            const formData = JSON.parse(savedData);
            console.log("Restoring form data from sessionStorage:", formData);

            // Restore header fields
            $('#tahun_anggaran').val(formData.tahun_anggaran).trigger('change');
            $('#tipe_pengajuan_display').val(formData.tipe_pengajuan_display);
            $('#tipe_pengajuan_hidden').val(formData.tipe_pengajuan_hidden);
            $('#keterangan').val(formData.keterangan);
            $('#keteranganInput').val(formData.keterangan);
            $('#id_bagian_pelaksana').val(formData.id_bagian_pelaksana);

            // Restore barang items
            const $tableBody = $('#tabelBarang tbody');
            $tableBody.empty(); // Clear existing rows

            if (formData.barangItems && formData.barangItems.length > 0) {
                formData.barangItems.forEach(function(item, index) {
                    const $newRow = $(createRowTemplate(index));
                    $tableBody.append($newRow);

                    // Initialize Select2 for the new row's dropdown
                    initializeSelect2($newRow.find('.dropdown-barang'));

                    // Set values
                    $newRow.find('.dropdown-barang').val(item.kode_barang).trigger('change');
                    $newRow.find('.keterangan-input').val(item.keterangan_barang);
                    $newRow.find('.kuantitas-input').val(item.kuantitas);
                    $newRow.find('.harga-input').val(utils.formatNumber(item.harga)).attr('data-raw-value', item.harga);
                    calculateRowTotal($newRow);
                });
            } else {
                // If no items were saved, add one empty row
                $tableBody.append($(createRowTemplate(0)));
                initializeSelect2($tableBody.find('.dropdown-barang'));
            }

            reindexRows(); // Ensure correct indexing after restore

            // Clear sessionStorage after restoration
            sessionStorage.removeItem(sessionStorageKey);
        }
    }

    // Tahun anggaran change
    $('#tahun_anggaran').on('change', function() {
        const tahun = parseInt($(this).val());
        const $tipePengajuanDisplay = $('#tipe_pengajuan_display');
        const $tipePengajuanHidden = $('#tipe_pengajuan_hidden');

        if (tahun === config.tahunSession) {
            $tipePengajuanDisplay.val('revisi').prop('disabled', true);
            $tipePengajuanHidden.val('revisi');
        } else if (tahun === config.tahunSession + 1) {
            $tipePengajuanDisplay.val('usulan').prop('disabled', true);
            $tipePengajuanHidden.val('usulan');
        } else {
            $tipePengajuanDisplay.val('').prop('disabled', true);
            $tipePengajuanHidden.val('');
        }
    }).trigger('change');

    // Price input formatting
    $(document).on('input', '.harga-input', function() {
        const $input = $(this);
        const rawValue = utils.removeFormat($input.val());
        const numericValue = utils.parseNumber($input.val());

        // Format dan tampilkan
        $input.val(utils.formatNumber(numericValue));
        $input.attr('data-raw-value', numericValue);

        // Hitung total
        calculateRowTotal($input.closest('tr'));
    });

    $(document).on('blur', '.harga-input', function() {
        const $input = $(this);
        const numericValue = utils.parseNumber($input.val());
        $input.val(utils.formatNumber(numericValue));
        $input.attr('data-raw-value', numericValue);
    });

    // Prevent non-numeric input (except dots for thousands separator)
    $(document).on('keypress', '.harga-input', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // Quantity input
    $(document).on('input change', '.kuantitas-input', function() {
        const $input = $(this);
        let kuantitas = parseInt($input.val()) || 1;

        if (kuantitas < 1) {
            kuantitas = 1;
            $input.val(kuantitas);
        }

        calculateRowTotal($input.closest('tr'));
    });

    // Dropdown barang change - TANPA VALIDASI DUPLIKASI
    $(document).on('change', '.dropdown-barang', function() {
        if (!isInitializing) {
            // Log selected item untuk debugging
            const selectedId = $(this).val();
            const selectedText = $(this).find('option:selected').text();
            console.log(`Selected: ${selectedId} - ${selectedText}`);
        }
    });

    // Add row
    $('#tambahBaris').on('click', function() {
        const $tableBody = $('#tabelBarang tbody');
        const currentRowCount = $tableBody.find('tr').length;
        const newIndex = currentRowCount;

        // Create new row
        const $newRow = $(createRowTemplate(newIndex));
        $tableBody.append($newRow);

        // Initialize Select2 on new dropdown
        initializeSelect2($newRow.find('.dropdown-barang'));

        // Focus on the new dropdown
        $newRow.find('.dropdown-barang').select2('open');
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        const $rows = $('#tabelBarang tbody tr');

        if ($rows.length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
        } else {
            utils.showAlert('warning', 'Peringatan', 'Minimal harus ada satu baris barang.');
        }
    });

    // Form submission
    $('#formCreatePengajuan').on('submit', function(e) {
        e.preventDefault();

        // Validate form
        let isValid = true;
        let errorMessages = [];

        // Check if at least one row has complete data
        let hasValidRow = false;
        let validRowCount = 0;

        $('#tabelBarang .barang-row').each(function() {
            const $row = $(this);
            const barang = $row.find('.dropdown-barang').val();
            const kuantitas = parseInt($row.find('.kuantitas-input').val()) || 0;
            const harga = utils.parseNumber($row.find('.harga-input').attr('data-raw-value') || '0');

            // Skip baris kosong (tidak ada barang dipilih)
            if (!barang || barang === '') {
                return true; // continue
            }

            // Validasi baris yang ada barangnya
            if (kuantitas <= 0) {
                errorMessages.push(`Baris ${$row.find('.nomor').text()}: Kuantitas harus lebih dari 0.`);
                isValid = false;
            }

            if (harga <= 0) {
                errorMessages.push(`Baris ${$row.find('.nomor').text()}: Harga harus lebih dari 0.`);
                isValid = false;
            }

            if (barang && kuantitas > 0 && harga > 0) {
                hasValidRow = true;
                validRowCount++;
            }
        });

        if (!isValid) {
            utils.showAlert('error', 'Validasi Error', errorMessages.join('<br>'));
            return;
        }

        if (!hasValidRow) {
            utils.showAlert('error', 'Validasi Error', 'Minimal harus ada satu baris barang yang lengkap (barang dipilih, kuantitas > 0, dan harga > 0).');
            return;
        }

        // Show confirmation with valid row count
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: `Akan menyimpan ${validRowCount} item barang. Baris kosong akan diabaikan. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    // Function untuk submit form
    function submitForm() {
        // Convert formatted values to raw values for submission
        const originalValues = {};
        $('.harga-input').each(function(index) {
            const $input = $(this);
            originalValues[index] = $input.val();
            const rawValue = $input.attr('data-raw-value') || utils.parseNumber($input.val());
            $input.val(rawValue);
        });

        const formData = new FormData($('#formCreatePengajuan')[0]);

        // Restore formatted values immediately (ini penting agar tampilan tidak berubah sebelum submit)
        $('.harga-input').each(function(index) {
            $(this).val(originalValues[index]);
        });

        // Submit form
        utils.showLoading();

        $.ajax({
            url: $('#formCreatePengajuan').attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                utils.hideLoading();

                // Clear sessionStorage on successful submission
                // INI ADALAH BARIS KRUSIAL YANG HARUS DIPASTIKAN
                sessionStorage.removeItem(sessionStorageKey);
                clearInterval(autoSaveInterval); // Stop auto-save on successful submission

                if (response.status === 'berhasil') {
                    Swal.fire({
                        title: 'Sukses',
                        text: response.message || 'Data pengajuan berhasil disimpan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            // REDIRECT KE HALAMAN DAFTAR ATAU CREATE BARU
                            // JANGAN GUNAKAN window.location.reload() KECUALI ANDA INGIN FORM KOSONG TAPI TIDAK MENGHAPUS SESI
                            window.location.href = "{{ route('pengajuan.reguler.index') }}"; // Contoh: kembali ke daftar
                            // ATAU jika ingin langsung ke form baru yang kosong setelah submit:
                            // window.location.href = "{{ route('pengajuan.reguler.create') }}";
                        }
                    });
                } else {
                    let errorMessage = response.message || 'Data pengajuan gagal disimpan.';
                    if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join('<br>');
                    }

                    Swal.fire({
                        title: 'Gagal!',
                        html: errorMessage,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                utils.hideLoading();

                let errorMessage = 'Terjadi kesalahan yang tidak diketahui.';

                if (xhr.status === 419) { // CSRF Token Mismatch
                    // Save form data before reloading (ini masih relevan untuk kasus CSRF)
                    saveFormData();
                    errorMessage = 'Sesi Anda telah berakhir. Mohon refresh halaman untuk melanjutkan. Data Anda telah disimpan sementara.';
                    utils.showAlert('error', 'Sesi Kedaluwarsa', errorMessage).then(() => {
                        window.location.reload(); // Disarankan reload halaman untuk mengatasi CSRF
                    });
                    return; // Stop further error processing
                }

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }

                Swal.fire({
                    title: 'Error!',
                    html: errorMessage,
                    icon: 'error'
                });
            }
        });
    }


    // Initialize
    function init() {
        console.log("Initializing form with static pemeliharaan items...");

        // Set default value for tahun_anggaran
        $('#tahun_anggaran').trigger('change');

        // Fetch barang options (includes static items) and then restore data
        fetchBarangOptions();

        // Start auto-save interval (e.g., every 15 seconds)
        autoSaveInterval = setInterval(saveFormData, 15000); // Save every 15 seconds

        // Save data before the page unloads
        $(window).on('beforeunload', function() {
            saveFormData();
        });

        // Log configuration for debugging
        console.log("Static pemeliharaan items:", config.staticPemeliharaan);
    }

    // Start initialization
    init();
});

</script>
@endsection
