{{-- resources/views/PerencanaanBMN/Bagian/SimanDashboardPage.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard SIMAN Assets</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard SIMAN</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="toast-container"></div>

    <div class="content">
        <div class="container-fluid">

            <!-- SECTION 1: Header Dashboard with Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Status Dashboard SIMAN
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary">
                                            <i class="fas fa-database"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Records</span>
                                            <span class="info-box-number" id="totalRecordsGlobal">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon" id="syncStatusIcon">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Last Sync</span>
                                            <span class="info-box-number" id="lastSyncStatus">-</span>
                                            <small class="text-muted" id="lastSyncTime">-</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Asset Overview Cards -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white cursor-pointer" data-toggle="collapse" data-target="#assetOverviewCollapse">
                            <h5 class="mb-0">
                                <i class="fas fa-th-large mr-2"></i>
                                Asset Overview
                                <i class="fas fa-chevron-right float-right" id="assetOverviewToggleIcon"></i>
                            </h5>
                        </div>
                        <div id="assetOverviewCollapse" class="collapse">
                            <div class="card-body">
                                <div class="row" id="assetCardsContainer">
                                    <!-- Asset cards will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Data Explorer -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-search mr-2"></i>
                                <span id="explorerTitle">Asset Data Explorer</span>
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Asset Type Selector & Actions -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Select Asset Type:</label>
                                    <select id="assetTypeSelect" class="form-control">
                                        @foreach($assetTypes as $key => $name)
                                            <option value="{{ $key }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Records per page:</label>
                                    <select id="perPageSelect" class="form-control">
                                        <option value="25" selected>25 records</option>
                                        <option value="50">50 records</option>
                                        <option value="100">100 records</option>
                                        <option value="200">200 records</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Actions:</label>
                                    <div>
                                        <button id="fetchFromAPI" class="btn btn-warning mr-2">
                                            <i class="fas fa-download mr-1"></i>Fetch API
                                        </button>
                                        <button id="loadData" class="btn btn-success">
                                            <i class="fas fa-sync-alt mr-1"></i>Load Data
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Smart Filter Panel -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header cursor-pointer" data-toggle="collapse" data-target="#filterCollapse">
                                            <h6 class="mb-0">
                                                <i class="fas fa-filter mr-2"></i>Filter Data
                                                <span class="badge badge-secondary ml-2" id="activeFilterBadge" style="display: none;">Active</span>
                                                <i class="fas fa-chevron-right float-right" id="filterToggleIcon"></i>
                                            </h6>
                                        </div>
                                        <div id="filterCollapse" class="collapse">
                                            <div class="card-body">
                                                <!-- Common Filters -->
                                                <div class="row" id="commonFilters">
                                                <div class="col-md-2">
                                                    <label class="form-label">Global Search:</label>
                                                    <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Search...">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Tahun Perolehan:</label>
                                                    <input type="number" id="filterTahunPerolehan" class="form-control form-control-sm" placeholder="Contoh: 2023">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Kode Barang:</label>
                                                    <input type="text" id="filterKodeBarang" class="form-control form-control-sm" placeholder="Kode Barang">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">NUP:</label>
                                                    <input type="number" id="filterNup" class="form-control form-control-sm" placeholder="Masukkan NUP">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Kondisi:</label>
                                                    <select id="filterKondisi" class="form-control form-control-sm">
                                                        <option value="">All</option>
                                                        <option value="B">Baik</option>
                                                        <option value="RR">Rusak Ringan</option>
                                                        <option value="RB">Rusak Berat</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div>
                                                        <button id="applyFilter" class="btn btn-primary btn-sm mr-1">Filter</button>
                                                        <button id="clearFilter" class="btn btn-secondary btn-sm">Reset</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Asset-Specific Filters (Dynamic) -->
                                            <div class="row mt-3" id="specificFilters" style="display: none;">
                                                <div class="col-12">
                                                    <hr>
                                                    <h6>Asset-Specific Filters:</h6>
                                                    <div class="row" id="specificFiltersContent">
                                                        <!-- Dynamic content -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="fetchProgress" class="progress mb-3" style="display: none;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" id="progressBar">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span id="recordCount" class="badge badge-primary">0 records</span>
                                    </div>
                                    <div>
                                        <button id="exportExcel" class="btn btn-sm btn-success mr-1">
                                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                                        </button>
                                        <button id="exportCsv" class="btn btn-sm btn-info mr-1">
                                            <i class="fas fa-file-csv mr-1"></i> Export CSV
                                        </button>
                                        <button id="refreshData" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <table id="simanTable" class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        </thead>
                                    <tbody id="simanTableBody">
                                        </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3" style="display: none !important;">
                                <span id="paginationInfo" class="text-muted">Showing 0 to 0 of 0 entries</span>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="paginationNav"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PSP Tools Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white cursor-pointer" data-toggle="collapse" data-target="#pspToolsCollapse">
                            <h5 class="mb-0">
                                <i class="fas fa-tools mr-2"></i>
                                Tools & Reports - Validasi Non-PSP
                                <i class="fas fa-chevron-right float-right" id="pspToggleIcon"></i>
                            </h5>
                        </div>
                        <div id="pspToolsCollapse" class="collapse">
                            <div class="card-body">
                            <!-- Filter Controls -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Filter Options:</h6>

                                    <!-- Filter Nilai Perolehan -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Nilai Perolehan:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="filterNilai" id="nilaiKurang100" value="<100" checked>
                                                    <label class="form-check-label" for="nilaiKurang100">
                                                        < 100 Juta
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="filterNilai" id="nilaiLebih100" value=">=100">
                                                    <label class="form-check-label" for="nilaiLebih100">
                                                        ≥ 100 Juta
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Kode Barang:</label>
                                            <input type="text" id="pspFilterKodeBarang" class="form-control form-control-sm" placeholder="Contoh: 3010101001">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">NUP Spesifik:</label>
                                            <input type="number" id="pspFilterNup" class="form-control form-control-sm" placeholder="Cari NUP spesifik (opsional)">
                                        </div>

                                        <!-- Filter Range Tahun -->
                                        <div class="col-md-2">
                                            <label class="form-label">Tahun Perolehan:</label>
                                            <div class="d-flex align-items-center">
                                                <input type="number" id="tahunDari" class="form-control form-control-sm"
                                                       placeholder="Dari" min="1900" max="2099" style="width: 100px;">
                                                <span class="mx-2">-</span>
                                                <input type="number" id="tahunSampai" class="form-control form-control-sm"
                                                       placeholder="Sampai" min="1900" max="2099" style="width: 100px;">
                                            </div>
                                        </div>

                                        <!-- Records per Page -->
                                        <div class="col-md-2">
                                            <label class="form-label">Records per page:</label>
                                            <select id="pspPerPage" class="form-control form-control-sm" style="width: 150px;">
                                                <option value="25" selected>25 records</option>
                                                <option value="50">50 records</option>
                                                <option value="100">100 records</option>
                                                <option value="250">250 records</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="row">
                                        <div class="col-12">
                                            <button id="validatePspBtn" class="btn btn-primary mr-2">
                                                <i class="fas fa-filter mr-1"></i> Run Validation
                                            </button>
                                            <button id="downloadPspBtn" class="btn btn-success" disabled>
                                                <i class="fas fa-file-excel mr-1"></i> Download Excel
                                            </button>
                                            <button id="generatePspDocBtn" class="btn btn-info ml-2">
                                                <i class="fas fa-file-pdf mr-1"></i> Generate Dokumen PSP
                                            </button>
                                            <button id="generateSptjmBtn" class="btn btn-warning ml-2">
                                                <i class="fas fa-file-alt mr-1"></i> Generate SPTJM Lampiran
                                            </button>
                                            <button id="clearPspFilter" class="btn btn-secondary">
                                                <i class="fas fa-undo mr-1"></i> Reset Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Results Section -->
                            <div id="pspResultSection" style="display: none;">
                                <hr>

                                <!-- Result Stats -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Hasil Validasi:</h6>
                                            <span id="pspRecordCount" class="badge badge-primary">0 records</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading Indicator -->
                                <div id="pspLoadingIndicator" style="display: none;">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                                        <span>Loading data...</span>
                                    </div>
                                </div>

                                <!-- Result Table -->
                                <div class="table-responsive">
                                    <table id="pspResultTable" class="table table-sm table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="10%">Kode Barang</th>
                                                <th width="15%">NUP Range</th>
                                                <th width="25%">Nama Barang</th>
                                                <th width="15%">Merk</th>
                                                <th width="8%">Qty</th>
                                                <th width="12%">Nilai Satuan</th>
                                                <th width="10%">Total Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pspResultTableBody">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div id="pspPaginationContainer" class="d-flex justify-content-between align-items-center mt-3" style="display: none;">
                                    <span id="pspPaginationInfo" class="text-muted">Showing 0 to 0 of 0 entries</span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="pspPaginationNav"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="generatePspDocModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Generate Dokumen PSP</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Form Input -->
                            <form id="generatePspDocForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="documentType">Jenis Dokumen:</label>
                                            <select id="documentType" class="form-control" required>
                                                <option value="">Pilih Jenis Dokumen</option>
                                                <!-- Options will be loaded via AJAX -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nomorSurat">Nomor Surat:</label>
                                            <input type="text" id="nomorSurat" class="form-control"
                                                   placeholder="Contoh: 001/KN.02.04/01/2025" required>
                                            <small class="form-text text-muted">Format: nomor/kode.bagian/bulan/tahun</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional fields for document customization -->
                                <div class="row" id="additionalFields" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenisBmn">Jenis BMN:</label>
                                            <select id="jenisBmn" class="form-control">
                                                <option value="Peralatan dan Mesin">Peralatan dan Mesin</option>
                                                <option value="Gedung dan Bangunan">Gedung dan Bangunan</option>
                                                <option value="Jalan, Irigasi dan Jaringan">Jalan, Irigasi dan Jaringan</option>
                                                <option value="Aset Tetap Lainnya">Aset Tetap Lainnya</option>
                                                <option value="Aset Tak Berwujud">Aset Tak Berwujud</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kategoriNilai">Kategori Nilai:</label>
                                            <select id="kategoriNilai" class="form-control">
                                                <option value="sampai dengan Rp100.000.000,-">≤ Rp 100.000.000</option>
                                                <option value="di atas Rp100.000.000,-">> Rp 100.000.000</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional fields for Surat KPKNL -->
                                <div class="row" id="suratKpknlFields" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nomorReferensi">Nomor Referensi:</label>
                                            <input type="text" id="nomorReferensi" class="form-control"
                                                   placeholder="Contoh: B/8916/KN.02.04/07/2025">
                                            <small class="form-text text-muted">Nomor surat yang direferensikan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalReferensi">Tanggal Referensi:</label>
                                            <input type="text" id="tanggalReferensi" class="form-control"
                                                   placeholder="Contoh: 3 Juli 2025">
                                            <small class="form-text text-muted">Tanggal surat yang direferensikan</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="previewPspDocBtn" class="btn btn-primary mr-2">
                                            <i class="fas fa-eye mr-1"></i> Preview
                                        </button>
                                        <button type="button" id="downloadPspDocBtn" class="btn btn-success" disabled>
                                            <i class="fas fa-download mr-1"></i> Download PDF
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Preview Area -->
                            <div id="previewArea" class="mt-4" style="display: none;">
                                <h6>Preview Dokumen:</h6>
                                <div class="border" style="height: 500px; overflow-y: auto; padding: 15px;" id="previewContent">
                                    <!-- Preview content will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPTJM Lampiran Modal -->
            <div class="modal fade" id="generateSptjmModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Generate SPTJM Lampiran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Info Section -->
                            <div class="alert alert-info" id="sptjmPreviewInfo" style="display: none;">
                                <h6>Preview Information:</h6>
                                <ul class="mb-0">
                                    <li>Total Records: <strong id="sptjmTotalRecords">-</strong></li>
                                    <li>Total PDFs: <strong id="sptjmTotalPdfs">-</strong></li>
                                    <li>Estimated Size: <strong id="sptjmEstimatedSize">-</strong> MB</li>
                                </ul>
                            </div>

                            <!-- Form Input -->
                            <form id="generateSptjmForm">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sptjmNomorSurat">Nomor Surat: <span class="text-danger">*</span></label>
                                            <input type="text" id="sptjmNomorSurat" class="form-control"
                                                   placeholder="Contoh: 001/KN.02.04/01/2025" required>
                                            <small class="form-text text-muted">Format: nomor/kode.bagian/bulan/tahun</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="previewSptjmInfoBtn" class="btn btn-primary mr-2">
                                            <i class="fas fa-eye mr-1"></i> Preview Info
                                        </button>
                                        <button type="button" id="generateSptjmLampiranBtn" class="btn btn-success" disabled>
                                            <i class="fas fa-download mr-1"></i> Generate PDF
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="alert alert-warning mt-3">
                                <strong>Catatan:</strong>
                                <ul class="mb-0">
                                    <li>Dokumen akan menggunakan filter yang sama dengan validasi PSP</li>
                                    <li>Jika data lebih dari 10.000 record, akan dibuat multiple PDF dalam format ZIP</li>
                                    <li>Pastikan filter sudah diisi dan data sudah divalidasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPTJM Generation Loading Modal -->
            <div class="modal fade" id="sptjmLoadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <h5 class="mb-3">Generating SPTJM Lampiran</h5>
                            <p class="text-muted mb-0">
                                Please wait while we process your request.<br>
                                This may take several minutes for large datasets.
                            </p>
                            <div class="alert alert-info mt-4 mx-4">
                                <small>
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Do not close this window or refresh the page.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
/* Cursor pointer for collapsible headers */
.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    background-color: rgba(0,0,0,0.05);
}

/* Sticky table header with max-height */
.table-responsive {
    max-height: 600px;
    overflow-y: auto;
    position: relative;
    scroll-behavior: smooth;
}

#simanTable thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* Compact info-box */
.info-box {
    display: block;
    min-height: 80px;
    background: #fff;
    width: 100%;
    border-radius: 10px;
    margin-bottom: 15px;
}

.info-box .info-box-icon {
    border-top-left-radius: 10px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 10px;
    display: block;
    float: left;
    height: 80px;
    width: 80px;
    text-align: center;
    font-size: 38px;
    line-height: 80px;
    background: rgba(0,0,0,0.2);
}

.info-box .info-box-content {
    padding: 5px 10px;
    margin-left: 80px;
}

.info-box .info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box .info-box-number {
    font-size: 18px;
    font-weight: bold;
}

.asset-card {
    transition: transform 0.2s;
    cursor: pointer;
}

.asset-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.health-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.health-good { background-color: #28a745; }
.health-warning { background-color: #ffc107; }
.health-error { background-color: #dc3545; }
.health-unknown { background-color: #6c757d; }

.toast-container {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    max-width: 350px;
}

.toast {
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.toast-success { background: #28a745; color: white; }
.toast-error { background: #dc3545; color: white; }
.toast-warning { background: #ffc107; color: #212529; }
.toast-info { background: #17a2b8; color: white; }

/* Compact table styling */
.table td {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    vertical-align: middle;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    padding: 0.65rem 0.75rem;
}

/* Compact card-body */
.card-body {
    padding: 1rem;
}

.badge-kondisi {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.sortable {
    cursor: pointer;
}
.sortable:hover {
    background-color: #e9ecef;
}
.sort-icon {
    margin-left: 5px;
    color: #aaa;
}
.details-row td {
    background-color: #f8f9fa;
    padding: 15px !important;
}
.detail-content {
    display: flex;
    flex-wrap: wrap;
}
.detail-item {
    flex: 0 0 50%; /* Tampilkan 2 kolom */
    padding: 5px;
}
.detail-item strong {
    display: block;
    color: #555;
    font-size: 0.8rem;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentAssetType = 'alat_angkutan';
    let isLoading = false;
    let currentSortBy = 'id';
    let currentSortDir = 'desc';

    let currentPspPage = 1;
    let pspFilters = {};
    let isLoadingPsp = false;


    // Set default tahun
    const currentYear = new Date().getFullYear();
    $('#tahunDari').val(2010);
    $('#tahunSampai').val(currentYear);

    // Template URLs
    const dataUrlTemplate = "{{ route('siman.data', ['assetType' => 'PLACEHOLDER']) }}";
    const fetchUrlTemplate = "{{ route('siman.fetch', ['assetType' => 'PLACEHOLDER']) }}";
    const statsUrl = "{{ route('siman.stats') }}";
    const metadataUrlTemplate = "{{ route('siman.metadata', ['assetType' => 'PLACEHOLDER']) }}";

    // Initialize dashboard
    loadDashboardStats();
    loadAssetCards();
    loadAssetMetadata(currentAssetType);
    loadData(1);

    // Toast notification system
    function showToast(message, type = 'info', duration = 5000) {
        const toastId = 'toast_' + Date.now();
        const toast = $(`
            <div id="${toastId}" class="toast toast-${type} p-3" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
                <div class="d-flex align-items-center">
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close ml-2" onclick="$('#${toastId}').fadeOut()" style="background: none; border: none; color: inherit;">&times;</button>
                </div>
            </div>
        `);
        $('body').append(toast);
        toast.fadeIn(300);
        setTimeout(() => $('#' + toastId).fadeOut(300, function() { $(this).remove(); }), duration);
    }

    window.hideToast = function(toastId) {
        $(`#${toastId}`).fadeOut(300, function() { $(this).remove(); });
    }

    // Load dashboard stats (v2.0 - no decrypt counts)
    function loadDashboardStats() {
        $.get(statsUrl)
        .done(function(response) {
            if (response.status === 'success') {
                const data = response.data;

                // Update global stats (plain data only)
                let totalRecords = 0;
                Object.values(data.asset_stats).forEach(stat => {
                    if (stat.table_exists) {
                        totalRecords += stat.total_records;
                    }
                });

                $('#totalRecordsGlobal').text(totalRecords.toLocaleString());

                // Update sync status
                const syncStatus = data.latest_sync.status;
                const syncIcon = $('#syncStatusIcon');

                if (syncStatus === 'completed') {
                    syncIcon.removeClass().addClass('info-box-icon bg-success');
                    $('#lastSyncStatus').text('Success');
                } else if (syncStatus === 'failed') {
                    syncIcon.removeClass().addClass('info-box-icon bg-danger');
                    $('#lastSyncStatus').text('Failed');
                } else {
                    syncIcon.removeClass().addClass('info-box-icon bg-secondary');
                    $('#lastSyncStatus').text('Unknown');
                }

                if (data.latest_sync.end_time) {
                    $('#lastSyncTime').text(new Date(data.latest_sync.end_time).toLocaleString());
                }
            }
        })
        .fail(function() {
            showToast('Failed to load dashboard stats', 'error');
        });
    }

    // Load asset cards
    function loadAssetCards() {
        $.get(statsUrl)
        .done(function(response) {
            if (response.status === 'success') {
                const stats = response.data.asset_stats;
                const container = $('#assetCardsContainer');
                container.empty();

                Object.entries(stats).forEach(([type, data]) => {
                    const healthClass = getHealthClass(data);

                    const card = $(`
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="card asset-card border-light" data-asset-type="${type}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-1">${data.display_name}</h6>
                                        <span class="health-indicator ${healthClass}"></span>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">Total Records</small>
                                        <div class="font-weight-bold h5">${data.total_records.toLocaleString()}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    container.append(card);
                });

                // Add click handlers
                $('.asset-card').click(function() {
                    const assetType = $(this).data('asset-type');
                    switchToAssetType(assetType);
                });
            }
        });
    }

    function getHealthClass(data) {
        if (!data.table_exists) return 'health-unknown';
        if (data.total_records === 0) return 'health-warning';
        const decryptPercent = (data.decrypted_records / data.total_records) * 100;
        if (decryptPercent >= 80) return 'health-good';
        if (decryptPercent >= 50) return 'health-warning';
        return 'health-error';
    }

    // Switch asset type
    function switchToAssetType(assetType) {
        currentAssetType = assetType;
        $('#assetTypeSelect').val(assetType);
        $('#explorerTitle').text(`Asset Data Explorer - ${$('#assetTypeSelect option:selected').text()}`);

        loadAssetMetadata(assetType);
        loadData(1);

        // Scroll to explorer
        $('html, body').animate({
            scrollTop: $('#explorerTitle').closest('.card').offset().top - 20
        }, 500);
    }

    // Load asset-specific metadata for filters
    function loadAssetMetadata(assetType) {
        const url = metadataUrlTemplate.replace('PLACEHOLDER', assetType);

        $.get(url)
        .done(function(response) {
            if (response.status === 'success' && response.data.specific_filters) {
                const filters = response.data.specific_filters;
                const container = $('#specificFiltersContent');
                container.empty();

                if (Object.keys(filters).length > 0) {
                    Object.entries(filters).forEach(([key, config]) => {
                        let input = '';
                        if (config.type === 'text') {
                            input = `<input type="text" id="filter_${key}" class="form-control form-control-sm" placeholder="${config.label}">`;
                        } else if (config.type === 'number') {
                            input = `<input type="number" id="filter_${key}" class="form-control form-control-sm" placeholder="${config.label}">`;
                        } else if (config.type === 'select') {
                            input = `<select id="filter_${key}" class="form-control form-control-sm"><option value="">${config.label}</option></select>`;
                        } else if (config.type === 'range') {
                            input = `
                                <div class="row">
                                    <div class="col-6"><input type="number" id="filter_${key}_min" class="form-control form-control-sm" placeholder="Min"></div>
                                    <div class="col-6"><input type="number" id="filter_${key}_max" class="form-control form-control-sm" placeholder="Max"></div>
                                </div>
                            `;
                        }

                        const filterCol = $(`
                            <div class="col-md-3">
                                <label class="form-label">${config.label}:</label>
                                ${input}
                            </div>
                        `);

                        container.append(filterCol);
                    });

                    $('#specificFilters').show();
                } else {
                    $('#specificFilters').hide();
                }
            } else {
                $('#specificFilters').hide();
            }
        });
    }

    // Load data function
    function loadData(page = 1) {
        if (isLoading) return;

        const assetType = $('#assetTypeSelect').val();
        const perPage = $('#perPageSelect').val();
        const filters = {
            search: $('#filterSearch').val().trim(),
            tanggal_perolehan: $('#filterTahunPerolehan').val().trim(),
            kode_barang: $('#filterKodeBarang').val().trim(),
            kondisi: $('#filterKondisi').val(),
            nup: $('#filterNup').val().trim()
        };

        const headers = [
            { key: '', label: '' },
            { key: 'kode_barang', label: 'Kode Barang' },
            { key: 'nama_barang', label: 'Nama Barang' },
            { key: 'nup', label: 'NUP' },
            { key: 'kondisi', label: 'Kondisi' },
            { key: 'kuantitas', label: 'Kuantitas' },
            { key: 'merk', label: 'Merk' },
            { key: 'tanggal_perolehan', label: 'Tahun Perolehan' }
        ];

        let headerHtml = '<tr>';
        headers.forEach(header => {
            if (header.key) {
                let icon = '';
                if (currentSortBy === header.key) {
                    icon = currentSortDir === 'asc' ? '<i class="fas fa-sort-up sort-icon"></i>' : '<i class="fas fa-sort-down sort-icon"></i>';
                }
                headerHtml += `<th class="sortable" data-sort="${header.key}">${header.label} ${icon}</th>`;
            } else {
                headerHtml += `<th>${header.label}</th>`; // Kolom yang tidak bisa di-sort
            }
        });
        headerHtml += '</tr>';
        $('#simanTable thead').html(headerHtml);

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (filters[key] === '') delete filters[key];
        });

        isLoading = true;
        showLoading();

        const url = dataUrlTemplate.replace('PLACEHOLDER', assetType);

        $.get(url, {
            page: page,
            per_page: perPage,
            sortBy: currentSortBy,
            sortDir: currentSortDir,
            ...filters
        })
        .done(function(response) {
            const data = response.data || [];
            const pagination = response.pagination;

            const tbody = $('#simanTableBody');
            tbody.empty();

            if (data && data.length > 0) {
                data.forEach((item, index) => {
                    const rowId = `row-${index}`;
                    const detailRowId = `details-${index}`;

                    // Baris data utama
                    // Ganti template string mainRow
                    const mainRow = `
                        <tr id="${rowId}" class="main-row" data-detail-id="${detailRowId}">
                            <td>
                                <button class="btn btn-xs btn-outline-secondary btn-expand" title="Lihat Detail">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </td>
                            <td>${item.kode_barang || '-'}</td>
                            <td class="text-truncate" style="max-width: 250px;" title="${item.nama_barang || '-'}">${item.nama_barang || '-'}</td>
                            <td>${item.nup || '-'}</td>
                            <td><span class="badge ${getKondisiBadgeClass(item.kondisi)}">${getKondisiText(item.kondisi)}</span></td>
                            <td>${item.kuantitas || '-'}</td>
                            <td class="text-truncate" style="max-width: 150px;" title="${item.merk || '-'}">${item.merk || '-'}</td>
                            <td>${item.tanggal_perolehan ? new Date(item.tanggal_perolehan).getFullYear() : '-'}</td>
                        </tr>
                    `;

                    // Baris detail yang tersembunyi
                    const detailRow = `
                        <tr id="${detailRowId}" class="details-row" style="display: none;">
                            <td colspan="8">
                                <div class="detail-content">
                                    <div class="detail-item"><strong>Nilai Perolehan:</strong> ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.nilai_perolehan || 0)}</div>
                                    <div class="detail-item"><strong>Nilai Buku:</strong> ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.nilai_buku || 0)}</div>
                                    <div class="detail-item"><strong>Tgl. Perolehan:</strong> ${item.tanggal_perolehan ? new Date(item.tanggal_perolehan).toLocaleDateString('id-ID') : '-'}</div>
                                    <div class="detail-item"><strong>Status Penggunaan:</strong> ${item.status_penggunaan || '-'}</div>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(mainRow + detailRow);
                });
                $('#recordCount').text(`${data.length} of ${pagination.total} records`);
                renderPagination(pagination);
                showToast(`Loaded ${data.length} records`, 'success', 2000);
            } else {
                tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No data found</td></tr>');
                $('#recordCount').text('0 records');
                $('#paginationContainer').hide();
                showToast('No data found', 'warning');
            }
        })
        .fail(function(xhr) {
            $('#simanTableBody').html(`<tr><td colspan="7" class="text-center text-danger py-4">Error loading data</td></tr>`);
            showToast(`Error loading data: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
        })
        .always(function() {
            isLoading = false;
            hideLoading();
        });
    }

    function showLoading() {
        $('#simanTableBody').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary mr-2"></div>Loading data...</td></tr>');
    }

    function hideLoading() {
        // Loading state handled in loadData success/fail
    }

    function renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#paginationContainer').hide();
            return;
        }

        $('#paginationContainer').show();
        const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
        $('#paginationInfo').text(`Showing ${start} to ${end} of ${pagination.total} entries`);

        let paginationHtml = '';

        // Previous
        paginationHtml += `<li class="page-item ${pagination.current_page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a></li>`;

        // Pages
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        // Next
        paginationHtml += `<li class="page-item ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a></li>`;

        $('#paginationNav').html(paginationHtml);
        currentPage = pagination.current_page;
    }

    function getKondisiBadgeClass(kondisi) {
        switch(kondisi) {
            case 'B': return 'badge-success';
            case 'RR': return 'badge-warning';
            case 'RB': return 'badge-danger';
            default: return 'badge-secondary';
        }
    }

    function getKondisiText(kondisi) {
        switch(kondisi) {
            case 'B': return 'Baik';
            case 'RR': return 'Rusak Ringan';
            case 'RB': return 'Rusak Berat';
            default: return kondisi || '-';
        }
    }

        // Main Function: Validate PSP Data
    function validatePspData(page = 1) {
        // Prevent double click
        if (isLoadingPsp) {
            showPspToast('Proses sedang berjalan, harap tunggu...', 'info');
            return;
        }

        // Validate input first
        if (!validateInput()) {
            return;
        }

        // Get filter values
        pspFilters = {
            filter_nilai: $('input[name="filterNilai"]:checked').val(),
            tahun_dari: $('#tahunDari').val(),
            tahun_sampai: $('#tahunSampai').val(),
            per_page: $('#pspPerPage').val(),
            page: page,
            nup: $('#pspFilterNup').val().trim(),
            kode_barang: $('#pspFilterKodeBarang').val().trim()
        };

        // Set loading state
        isLoadingPsp = true;
        $('#validatePspBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

        // Show result section with loading
        $('#pspResultSection').show();
        $('#pspLoadingIndicator').show();
        $('#pspResultTableBody').empty();
        $('#pspPaginationContainer').hide();

        // AJAX request with timeout
        $.ajax({
            url: "{{ route('siman.validate_psp') }}",
            type: 'GET',
            data: pspFilters,
            timeout: 600000, // 10 minutes timeout
            success: function(response) {
                if (response.status === 'success') {
                    renderPspTable(response.data);
                    renderPspPagination(response.pagination);

                    // Enable download button
                    $('#downloadPspBtn').prop('disabled', false);

                    // Update record count
                    const totalRecords = response.pagination.total || 0;
                    $('#pspRecordCount').text(totalRecords.toLocaleString('id-ID') + ' records');

                    // Store current page
                    currentPspPage = response.pagination.current_page;

                    // Show success message
                    if (page === 1) {
                        const filterText = pspFilters.filter_nilai === '<100' ? '< 100 Juta' : '≥ 100 Juta';
                        showPspToast(`Berhasil memuat ${totalRecords.toLocaleString('id-ID')} data dengan filter ${filterText}`, 'success');
                    }
                } else {
                    $('#pspResultTableBody').html(
                        '<tr><td colspan="8" class="text-center text-danger py-3">' +
                        '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                        (response.message || 'Terjadi kesalahan saat memuat data') +
                        '</td></tr>'
                    );
                    showPspToast(response.message || 'Gagal memuat data', 'error');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Terjadi kesalahan saat memuat data';

                if (status === 'timeout') {
                    errorMessage = 'Request timeout. Data terlalu besar, coba kurangi range tahun.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Silakan coba lagi.';
                } else if (xhr.status === 0) {
                    errorMessage = 'Koneksi terputus. Periksa jaringan Anda.';
                }

                $('#pspResultTableBody').html(
                    '<tr><td colspan="8" class="text-center text-danger py-3">' +
                    '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                    errorMessage +
                    '</td></tr>'
                );

                showPspToast(errorMessage, 'error');
            },
            complete: function() {
                // Reset loading state
                isLoadingPsp = false;
                $('#pspLoadingIndicator').hide();
                $('#validatePspBtn').prop('disabled', false).html('<i class="fas fa-filter mr-1"></i> Run Validation');
            }
        });
    }

    // Function to render table
    function renderPspTable(data) {
        const tbody = $('#pspResultTableBody');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.html(
                '<tr><td colspan="8" class="text-center text-muted py-3">' +
                '<i class="fas fa-inbox mr-2"></i>Tidak ada data yang sesuai dengan filter' +
                '</td></tr>'
            );
            $('#downloadPspBtn').prop('disabled', true);
            return;
        }

        // Calculate starting number
        const perPage = parseInt($('#pspPerPage').val());
        const startNo = ((currentPspPage - 1) * perPage) + 1;

        // Render rows
        data.forEach(function(item, index) {
            const row = $('<tr>');
            row.append(`<td class="text-center">${startNo + index}</td>`);
            row.append(`<td class="text-center">${item.kode_barang || '-'}</td>`);
            row.append(`<td class="text-center">${item.nup_awal} - ${item.nup_akhir}</td>`);
            row.append(`<td>${item.nama_barang || '-'}</td>`);
            row.append(`<td>${item.merk || '-'}</td>`);
            row.append(`<td class="text-center">${item.kuantitas || 0}</td>`);
            row.append(`<td class="text-right">${formatRupiah(item.nilai_perolehan_pertama)}</td>`);
            row.append(`<td class="text-right font-weight-bold">${formatRupiah(item.jumlah_nilai)}</td>`);
            tbody.append(row);
        });
    }

    // Function to render pagination
    function renderPspPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#pspPaginationContainer').hide();
            return;
        }

        $('#pspPaginationContainer').show();

        // Update info
        const from = pagination.from || 0;
        const to = pagination.to || 0;
        const total = pagination.total || 0;

        $('#pspPaginationInfo').text(
            `Showing ${from.toLocaleString('id-ID')} to ${to.toLocaleString('id-ID')} of ${total.toLocaleString('id-ID')} entries`
        );

        // Build pagination
        let paginationHtml = '';
        const currentPage = pagination.current_page;
        const lastPage = pagination.last_page;

        // Previous
        paginationHtml += `
            <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link psp-page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            </li>
        `;

        // Page numbers (simplified)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(lastPage, currentPage + 2);

        // First page
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link psp-page-link" href="#" data-page="1">1</a>
                </li>
            `;
            if (startPage > 2) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page range
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link psp-page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Last page
        if (endPage < lastPage) {
            if (endPage < lastPage - 1) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link psp-page-link" href="#" data-page="${lastPage}">${lastPage}</a>
                </li>
            `;
        }

        // Next
        paginationHtml += `
            <li class="page-item ${currentPage >= lastPage ? 'disabled' : ''}">
                <a class="page-link psp-page-link" href="#" data-page="${currentPage + 1}">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        $('#pspPaginationNav').html(paginationHtml);
    }

    // Function to format rupiah
    function formatRupiah(angka) {
        if (!angka || angka === 0) return 'Rp 0';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Helper: Show Toast
    function showPspToast(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const toast = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert"
                 style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);

        $('body').append(toast);
        setTimeout(() => toast.fadeOut(400, function() { $(this).remove(); }), 5000);
    }
    // Validate Input
    function validateInput() {
        const tahunDari = $('#tahunDari').val();
        const tahunSampai = $('#tahunSampai').val();

        // Check empty
        if (!tahunDari || !tahunSampai) {
            showPspToast('Mohon lengkapi filter tahun perolehan', 'warning');
            return false;
        }

        // Check valid year range
        const yearFrom = parseInt(tahunDari);
        const yearTo = parseInt(tahunSampai);

        if (yearFrom < 1900 || yearFrom > 2099) {
            showPspToast('Tahun awal tidak valid (1900-2099)', 'warning');
            $('#tahunDari').focus();
            return false;
        }

        if (yearTo < 1900 || yearTo > 2099) {
            showPspToast('Tahun akhir tidak valid (1900-2099)', 'warning');
            $('#tahunSampai').focus();
            return false;
        }

        if (yearFrom > yearTo) {
            showPspToast('Tahun awal tidak boleh lebih besar dari tahun akhir', 'warning');
            $('#tahunDari').focus();
            return false;
        }

        return true;
    }


    $('#simanTable').on('click', '.sortable', function() {
        const sortBy = $(this).data('sort');

        if (currentSortBy === sortBy) {
            // Jika kolom sama, balik arah urutan
            currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            // Jika kolom baru, urutkan dari asc
            currentSortBy = sortBy;
            currentSortDir = 'asc';
        }

        loadData(1); // Muat ulang data dengan urutan baru
    });

    $('#assetTypeSelect').change(function() {
        switchToAssetType($(this).val());
    });

    $('#loadData, #refreshData').click(() => loadData(1));
    $('#applyFilter').click(() => loadData(1));
    $('#clearFilter').click(function() {
        $('#filterSearch, #filterKodeKL, #filterSatker, #filterNup').val('');
        $('#filterKondisi').val('');
        loadData(1);
    });

    // Pagination clicks
    $(document).on('click', '#paginationNav a.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && !isLoading) {
            loadData(page);
        }
    });

    // API Fetch
    $('#fetchFromAPI').click(function() {
        if (isLoading) return;

        const assetType = $('#assetTypeSelect').val();
        const assetName = $('#assetTypeSelect option:selected').text();

        if (!confirm(`Fetch ${assetName} data from API?\n\nThis may take 2-10 minutes.`)) return;

        isLoading = true;
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Fetching...');

        $('#fetchProgress').show();
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 85) progress = 85;
            $('#progressBar').css('width', progress + '%');
            $('#progressText').text(Math.round(progress) + '%');
        }, 1000);

        const url = fetchUrlTemplate.replace('PLACEHOLDER', assetType);

        $.post(url)
        .done(function(response) {
            clearInterval(progressInterval);
            $('#progressBar').css('width', '100%');
            $('#progressText').text('100%');

            if (response.status === 'success') {
                const records = response.data.inserted_records || 0;
                showToast(`✅ Success! ${records.toLocaleString()} records fetched`, 'success', 7000);
                loadDashboardStats();
                loadAssetCards();
                loadData();
            } else {
                showToast(`❌ Error: ${response.message}`, 'error');
            }
        })
        .fail(function(xhr) {
            clearInterval(progressInterval);
            showToast(`❌ Fetch failed: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
        })
        .always(function() {
            isLoading = false;
            $('#fetchFromAPI').prop('disabled', false).html('<i class="fas fa-download mr-1"></i>Fetch API');
            $('#fetchProgress').hide();
            setTimeout(() => $('#progressBar').css('width', '0%'), 1000);
        });
    });

    // [REMOVED] Decrypt button handler - no longer needed in v2.0

    // PSP Validation
    $('#validatePspBtn').click(function() {
        validatePspData(1);
    });

    $('#downloadPspBtn').click(function() {
        // Ambil filter values terkini
        const downloadParams = {
            filter_nilai: $('input[name="filterNilai"]:checked').val(),
            tahun_dari: $('#tahunDari').val(),
            tahun_sampai: $('#tahunSampai').val(),
            nup: $('#pspFilterNup').val().trim(),
            kode_barang: $('#pspFilterKodeBarang').val().trim()

        };

        // Validasi sebelum download
        if (!downloadParams.tahun_dari || !downloadParams.tahun_sampai) {
            alert('Filter tahun harus diisi terlebih dahulu');
            return;
        }

        // Log untuk debugging
        console.log('Download params:', downloadParams);

        // Build URL dengan parameters
        const params = new URLSearchParams(downloadParams);
        const downloadUrl = "{{ route('siman.download_psp') }}?" + params.toString();

        // Trigger download
        window.location.href = downloadUrl;

        // Optional: Show notification
        showToast('Proses download dimulai. File akan segera terunduh...', 'info', 3000);
    });


    // Auto-refresh dashboard stats every 30 seconds
    setInterval(loadDashboardStats, 30000);

    // Event handler untuk Row Expansion
    $('#simanTableBody').on('click', '.btn-expand', function() {
        const detailRowId = $(this).closest('tr').data('detail-id');
        const detailRow = $('#' + detailRowId);
        const icon = $(this).find('i');

        if (detailRow.is(':visible')) {
            detailRow.fadeOut(200);
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else {
            detailRow.fadeIn(200);
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    });

    // Event handler untuk tombol Export
    $('#exportExcel, #exportCsv').click(function() {
        const format = $(this).attr('id') === 'exportExcel' ? 'xlsx' : 'csv';
        const assetType = $('#assetTypeSelect').val();

        // Mengumpulkan filter yang sedang aktif
        const params = new URLSearchParams({
            search: $('#filterSearch').val().trim(),
            tanggal_perolehan: $('#filterTahunPerolehan').val().trim(),
            kode_barang: $('#filterKodeBarang').val().trim(),
            kondisi: $('#filterKondisi').val(),
            nup: $('#filterNup').val().trim()
        });

        // Membuat URL untuk diunduh
        let url = `{{ route('siman.export', ['assetType' => 'PLACEHOLDER', 'format' => 'FORMATPLACEHOLDER']) }}`;
        url = url.replace('PLACEHOLDER', assetType).replace('FORMATPLACEHOLDER', format);

        // Memicu download
        window.location.href = `${url}?${params.toString()}`;
    });

        // Event: Run Validation button
    $('#validatePspBtn').click(function() {
        validatePspData(1);
    });

    // Event: Pagination click
    $(document).on('click', '.psp-page-link', function(e) {
        e.preventDefault();

        if ($(this).parent().hasClass('disabled')) {
            return false;
        }

        const page = parseInt($(this).data('page'));
        if (page && page !== currentPspPage) {
            // Scroll to top of result
            $('html, body').animate({
                scrollTop: $('#pspResultSection').offset().top - 100
            }, 300);

            validatePspData(page);
        }
    });

    // Event: Download button
    $('#downloadPspBtn').click(function() {
        if ($(this).prop('disabled')) {
            showPspToast('Jalankan validasi terlebih dahulu', 'warning');
            return;
        }

        // Get current filter values
        const downloadParams = {
            filter_nilai: $('input[name="filterNilai"]:checked').val(),
            tahun_dari: $('#tahunDari').val(),
            tahun_sampai: $('#tahunSampai').val()
        };

        // Final validation
        if (!validateInput()) {
            return;
        }

        // Show loading on button
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating Excel...');

        // Build download URL
        const params = new URLSearchParams(downloadParams);
        const downloadUrl = "{{ route('siman.download_psp') }}?" + params.toString();

        // Trigger download
        window.location.href = downloadUrl;

        // Show notification
        showPspToast('Download dimulai. File Excel sedang digenerate...', 'info');

        // Reset button after delay
        setTimeout(function() {
            btn.prop('disabled', false).html(originalText);
        }, 3000);
    });

    // Event: Clear filter button
    $('#clearPspFilter').click(function() {
        // Reset values
        $('#nilaiKurang100').prop('checked', true);
        $('#tahunDari').val(2010);
        $('#tahunSampai').val(currentYear);
        $('#pspPerPage').val(100);
        $('#pspFilterNup').val('');
        $('#pspFilterKodeBarang').val('');

        // Hide results
        $('#pspResultSection').fadeOut(200);
        $('#downloadPspBtn').prop('disabled', true);

        // Reset variables
        currentPspPage = 1;
        pspFilters = {};

        showPspToast('Filter berhasil direset', 'info');
    });

    // Event: Per page change
    $('#pspPerPage').change(function() {
        if ($('#pspResultSection').is(':visible') && !isLoadingPsp) {
            showPspToast('Memuat ulang data dengan jumlah records baru...', 'info');
            validatePspData(1);
        }
    });

    // Event: Enter key on year inputs
    $('#tahunDari, #tahunSampai').keypress(function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            $('#validatePspBtn').click();
        }
    });

    // Event: Year input validation
    $('#tahunDari, #tahunSampai').on('input', function() {
        const value = $(this).val();
        if (value && (parseInt(value) < 1900 || parseInt(value) > 2099)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
// PSP Document Generation - Tambahkan setelah existing JavaScript functions

    // Variables for document generation
    let currentDocumentData = null;

    // Event: Generate PSP Document button
    $('#generatePspDocBtn').click(function() {
        loadDocumentTypes();
        $('#generatePspDocModal').modal('show');
    });

    // Load document types
    function loadDocumentTypes() {
        $.get("{{ route('siman.generate_psp_doc_form') }}")
        .done(function(response) {
            if (response.status === 'success') {
                const select = $('#documentType');
                select.empty().append('<option value="">Pilih Jenis Dokumen</option>');

                Object.entries(response.data.document_types).forEach(([key, value]) => {
                    select.append(`<option value="${key}">${value}</option>`);
                });
            }
        })
        .fail(function() {
            showToast('Gagal memuat jenis dokumen', 'error');
        });
    }

    // Event: Document type change - show/hide additional fields
    $('#documentType').change(function() {
        const documentType = $(this).val();

        // Show additional fields for certain document types
        if (documentType && ['nodin_eselon_iii', 'nodin_eselon_ii', 'nodin_eselon_i', 'nodin_penetapan'].includes(documentType)) {
            $('#additionalFields').slideDown();
            $('#suratKpknlFields').slideUp();
        } else if (documentType === 'surat_kpknl') {
            $('#additionalFields').slideDown();
            $('#suratKpknlFields').slideDown();
        } else {
            $('#additionalFields').slideUp();
            $('#suratKpknlFields').slideUp();
        }

        // Reset preview
        $('#previewArea').hide();
        $('#downloadPspDocBtn').prop('disabled', true);
        currentDocumentData = null;
    });

    // Event: Preview document
    $('#previewPspDocBtn').click(function() {
        const documentType = $('#documentType').val();
        const nomorSurat = $('#nomorSurat').val().trim();

        if (!documentType || !nomorSurat) {
            showToast('Mohon lengkapi jenis dokumen dan nomor surat', 'warning');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');

        // Prepare form data
        const formData = {
            document_type: documentType,
            nomor_surat: nomorSurat
        };

        // Add additional data if fields are visible
        if ($('#additionalFields').is(':visible')) {
            formData.jenis_bmn = $('#jenisBmn').val();
            formData.kategori_nilai = $('#kategoriNilai').val();
        }

        // Add Surat KPKNL specific data if fields are visible
        if ($('#suratKpknlFields').is(':visible')) {
            formData.nomor_referensi = $('#nomorReferensi').val();
            formData.tanggal_referensi = $('#tanggalReferensi').val();
        }

        $.ajax({
            url: "{{ route('siman.preview_psp_doc') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    // Store current document data for download
                    currentDocumentData = formData;

                    // Show preview
                    $('#previewContent').html(response.data.html);
                    $('#previewArea').slideDown();
                    $('#downloadPspDocBtn').prop('disabled', false);

                    showToast('Preview berhasil dimuat', 'success');
                } else {
                    showToast('Gagal memuat preview: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat preview';
                showToast('Error: ' + message, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Event: Download document
    $('#downloadPspDocBtn').click(function() {
        if (!currentDocumentData) {
            showToast('Silakan preview dokumen terlebih dahulu', 'warning');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating...');

        // Create form for download
        const form = $('<form>', {
            method: 'POST',
            action: "{{ route('siman.download_psp_doc') }}"
        });

        // Add CSRF token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        // Add all document data
        Object.entries(currentDocumentData).forEach(([key, value]) => {
            form.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        });

        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();

        // Reset button after delay
        setTimeout(function() {
            btn.prop('disabled', false).html(originalText);
        }, 3000);

        showToast('Download dimulai...', 'info');
    });

    // Event: Modal reset when closed
    $('#generatePspDocModal').on('hidden.bs.modal', function() {
        $('#generatePspDocForm')[0].reset();
        $('#additionalFields').hide();
        $('#suratKpknlFields').hide();
        $('#previewArea').hide();
        $('#downloadPspDocBtn').prop('disabled', true);
        currentDocumentData = null;
    });

    // Event: Input changes - reset preview
    $('#nomorSurat, #jenisBmn, #kategoriNilai, #nomorReferensi, #tanggalReferensi').on('input change', function() {
        $('#previewArea').hide();
        $('#downloadPspDocBtn').prop('disabled', true);
        currentDocumentData = null;
    });

    // =================
    // SPTJM LAMPIRAN GENERATION
    // =================

    // Event: Generate SPTJM button
    $('#generateSptjmBtn').click(function() {
        // Validate that PSP validation has been run
        if (!validateInput()) {
            showToast('Harap isi filter tahun terlebih dahulu', 'warning');
            return;
        }

        $('#generateSptjmModal').modal('show');
    });

    // Event: Preview SPTJM Info button
    $('#previewSptjmInfoBtn').click(function() {
        if (!validateInput()) {
            showToast('Filter tahun perolehan harus diisi terlebih dahulu', 'warning');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');

        // Get current filter values
        const previewParams = {
            filter_nilai: $('input[name="filterNilai"]:checked').val(),
            tahun_dari: $('#tahunDari').val(),
            tahun_sampai: $('#tahunSampai').val(),
            nup: $('#pspFilterNup').val().trim(),
            kode_barang: $('#pspFilterKodeBarang').val().trim()
        };

        $.ajax({
            url: "{{ route('siman.preview_sptjm_info') }}",
            method: 'GET',
            data: previewParams,
            success: function(response) {
                if (response.status === 'success') {
                    // Update preview info
                    $('#sptjmTotalRecords').text(response.data.total_records.toLocaleString('id-ID'));
                    $('#sptjmTotalPdfs').text(response.data.total_pdfs);
                    $('#sptjmEstimatedSize').text(response.data.estimated_size_mb);

                    // Show preview info
                    $('#sptjmPreviewInfo').slideDown();

                    // Enable generate button
                    $('#generateSptjmLampiranBtn').prop('disabled', false);

                    showToast('Preview info berhasil dimuat', 'success');
                } else {
                    showToast('Gagal memuat preview info: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat preview';
                showToast('Error: ' + message, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Event: Generate SPTJM Lampiran button
    $('#generateSptjmLampiranBtn').click(function() {
        const nomorSurat = $('#sptjmNomorSurat').val().trim();

        if (!nomorSurat) {
            showToast('Nomor surat harus diisi', 'warning');
            $('#sptjmNomorSurat').focus();
            return;
        }

        if (!validateInput()) {
            showToast('Filter tahun perolehan tidak valid', 'warning');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating...');

        // Hide the form modal and show loading modal
        $('#generateSptjmModal').modal('hide');
        $('#sptjmLoadingModal').modal('show');

        // Create form for download
        const form = $('<form>', {
            method: 'POST',
            action: "{{ route('siman.generate_sptjm_lampiran') }}"
        });

        // Add CSRF token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        // Add nomor surat
        form.append($('<input>', {
            type: 'hidden',
            name: 'nomor_surat',
            value: nomorSurat
        }));

        // Add filter parameters
        form.append($('<input>', {
            type: 'hidden',
            name: 'filter_nilai',
            value: $('input[name="filterNilai"]:checked').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'tahun_dari',
            value: $('#tahunDari').val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'tahun_sampai',
            value: $('#tahunSampai').val()
        }));

        const nup = $('#pspFilterNup').val().trim();
        if (nup) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'nup',
                value: nup
            }));
        }

        const kodeBarang = $('#pspFilterKodeBarang').val().trim();
        if (kodeBarang) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'kode_barang',
                value: kodeBarang
            }));
        }

        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();

        // Hide loading modal after download has started
        // For large datasets, increase timeout to allow processing time
        setTimeout(function() {
            $('#sptjmLoadingModal').modal('hide');
            btn.prop('disabled', false).html(originalText);
            showToast('Download complete. Check your downloads folder.', 'success');
        }, 10000); // 10 seconds timeout for processing and download
    });

    // Event: Modal reset when closed
    $('#generateSptjmModal').on('hidden.bs.modal', function() {
        $('#generateSptjmForm')[0].reset();
        $('#sptjmPreviewInfo').hide();
        $('#generateSptjmLampiranBtn').prop('disabled', true);
    });

    // Event: Input nomor surat changes - reset preview
    $('#sptjmNomorSurat').on('input', function() {
        $('#generateSptjmLampiranBtn').prop('disabled', true);
    });

    // ==========================================
    // COLLAPSE TOGGLE ICON BEHAVIORS
    // ==========================================

    // Asset Overview collapse toggle
    $('#assetOverviewCollapse').on('show.bs.collapse', function() {
        $('#assetOverviewToggleIcon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }).on('hide.bs.collapse', function() {
        $('#assetOverviewToggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
    });

    // Filter collapse toggle
    $('#filterCollapse').on('show.bs.collapse', function() {
        $('#filterToggleIcon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }).on('hide.bs.collapse', function() {
        $('#filterToggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
    });

    // PSP Tools collapse toggle
    $('#pspToolsCollapse').on('show.bs.collapse', function() {
        $('#pspToggleIcon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
    }).on('hide.bs.collapse', function() {
        $('#pspToggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
    });

    // Show active filter badge when filter is applied
    $('#applyFilter').click(function() {
        const hasActiveFilter = $('#filterSearch').val() ||
                               $('#filterTahunPerolehan').val() ||
                               $('#filterKodeBarang').val() ||
                               $('#filterNup').val() ||
                               $('#filterKondisi').val();

        if (hasActiveFilter) {
            $('#activeFilterBadge').show();
        } else {
            $('#activeFilterBadge').hide();
        }
    });

    $('#clearFilter').click(function() {
        $('#activeFilterBadge').hide();
    });

    // Auto-expand PSP section when validation button is clicked
    $('#validatePspBtn').click(function() {
        if (!$('#pspToolsCollapse').hasClass('show')) {
            $('#pspToolsCollapse').collapse('show');
        }
    });
});
</script>
@endsection
