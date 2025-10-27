{{-- resources/views/PerencanaanBMN/Bagian/DashboardKertasKerjaSakti/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Kertas Kerja SAKTI</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard KK SAKTI</li>
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
                                Status Dashboard KK SAKTI
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-database"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Records</span>
                                            <span class="info-box-number" id="totalRecords">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-layer-group"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Batches</span>
                                            <span class="info-box-number" id="totalBatches">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-coins"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Nilai</span>
                                            <span class="info-box-number" id="totalNilai">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-secondary">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Latest Upload</span>
                                            <span class="info-box-number" id="latestUpload">-</span>
                                            <small class="text-muted" id="latestUploadTime">-</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Upload Excel Interface -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-upload mr-2"></i>
                                Upload Excel Kertas Kerja SAKTI
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="excelFile">Select Excel File:</label>
                                        <input type="file" id="excelFile" class="form-control-file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Format: .xlsx or .xls (Max: 10MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sheetName">Sheet Name:</label>
                                        <input type="text" id="sheetName" class="form-control" value="KK_2025" placeholder="KK_2025">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button id="uploadBtn" class="btn btn-success btn-block">
                                            <i class="fas fa-upload mr-1"></i>Upload Excel
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button id="refreshStats" class="btn btn-outline-secondary btn-block">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="uploadProgress" class="progress mt-3" style="display: none;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" id="progressBar">
                                    <span id="progressText">0%</span>
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
                                Data Explorer - KK SAKTI
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Advanced Filter Panel -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Filter Data
                                        <button id="toggleAdvancedFilters" class="btn btn-sm btn-outline-secondary float-right">
                                            <i class="fas fa-chevron-down"></i> Advanced
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Basic Filters -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Global Search:</label>
                                            <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Search in all fields...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Batch:</label>
                                            <select id="filterBatch" class="form-control form-control-sm">
                                                <option value="">All Batches</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Kode Barang:</label>
                                            <input type="text" id="filterKodeBarang" class="form-control form-control-sm" placeholder="Kode Barang">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">NUP:</label>
                                            <input type="text" id="filterNup" class="form-control form-control-sm" placeholder="NUP Awal/Akhir">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Actions:</label>
                                            <div>
                                                <button id="applyFilter" class="btn btn-primary btn-sm mr-1">
                                                    <i class="fas fa-filter mr-1"></i>Filter
                                                </button>
                                                <button id="clearFilter" class="btn btn-secondary btn-sm mr-1">
                                                    <i class="fas fa-times mr-1"></i>Clear
                                                </button>
                                                <button id="exportBartender" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-excel mr-1"></i>Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Advanced Filters -->
                                    <div id="advancedFilters" class="row" style="display: none;">
                                        <div class="col-md-3">
                                            <label class="form-label">Date Range:</label>
                                            <div class="d-flex">
                                                <input type="date" id="filterDateFrom" class="form-control form-control-sm mr-1">
                                                <input type="date" id="filterDateTo" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Jenis Transaksi:</label>
                                            <select id="filterJenisTransaksi" class="form-control form-control-sm">
                                                <option value="">All Types</option>
                                                <option value="Pembelian">Pembelian</option>
                                                <option value="Koreksi Susulan">Koreksi Susulan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Min Nilai:</label>
                                            <input type="number" id="filterMinNilai" class="form-control form-control-sm" placeholder="Min nilai">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Max Nilai:</label>
                                            <input type="number" id="filterMaxNilai" class="form-control form-control-sm" placeholder="Max nilai">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Status Pelabelan:</label>
                                            <select id="filterStatusLabel" class="form-control form-control-sm">
                                                <option value="">All Status</option>
                                                <option value="Belum">Belum Dilabel</option>
                                                <option value="Sudah">Sudah Dilabel</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats & Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span id="recordCount" class="badge badge-primary mr-2">0 records</span>
                                        <span id="recordStats" class="text-muted small"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <div class="d-inline-block mr-2">
                                        <label class="form-label mb-0">Show:</label>
                                        <select id="perPageSelect" class="form-control form-control-sm d-inline-block" style="width: auto;">
                                            <option value="25">25</option>
                                            <option value="50" selected>50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <button id="refreshData" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span id="recordCount" class="badge badge-primary">0 records</span>
                                    </div>
                                    <div>
                                        <button id="refreshData" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <table id="kksaktiTable" class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%"></th>
                                            <th class="sortable" data-sort="kode_barang">Kode Barang</th>
                                            <th class="sortable" data-sort="nup_awal">NUP Range</th>
                                            <th class="sortable" data-sort="uraian_barang">Uraian Barang</th>
                                            <th class="sortable" data-sort="nilai_total">Nilai Total</th>
                                            <th class="sortable" data-sort="tanggal">Tanggal</th>
                                            <th class="sortable" data-sort="upload_batch">Batch</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kksaktiTableBody">
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

            <!-- SECTION 4: Data Bermasalah Management -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Data Bermasalah - Perlu Perbaikan
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Error Statistics -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Errors</span>
                                            <span class="info-box-number" id="totalErrors">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Need Review</span>
                                            <span class="info-box-number" id="needsReview">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Fixed Errors</span>
                                            <span class="info-box-number" id="fixedErrors">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-list"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Error Types</span>
                                            <span class="info-box-number" id="errorTypes">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Data Controls -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Kelola Data Bermasalah
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Filter Error Type:</label>
                                            <select id="filterErrorType" class="form-control form-control-sm">
                                                <option value="">All Error Types</option>
                                                <option value="MISSING_MANDATORY">Missing Mandatory</option>
                                                <option value="INVALID_NUP_FORMAT">Invalid NUP Format</option>
                                                <option value="INVALID_DATE_FORMAT">Invalid Date Format</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter Batch:</label>
                                            <select id="filterErrorBatch" class="form-control form-control-sm">
                                                <option value="">All Batches</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Search:</label>
                                            <input type="text" id="filterErrorSearch" class="form-control form-control-sm" placeholder="Kode Barang, NUP...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Actions:</label>
                                            <div>
                                                <button id="applyErrorFilter" class="btn btn-primary btn-sm mr-1">
                                                    <i class="fas fa-search mr-1"></i>Filter
                                                </button>
                                                <button id="exportErrors" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Export data error dalam format Excel yang mudah diperbaiki">
                                                    <i class="fas fa-download mr-1"></i>Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Excel Export Information Panel -->
                            <div class="card mb-3 border-info">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle text-info mr-2"></i>
                                        Informasi Format Excel Export
                                        <button class="btn btn-sm btn-outline-secondary float-right" id="toggleExcelInfo">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body" id="excelInfoPanel" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="text-primary"><i class="fas fa-palette mr-1"></i>Color Coding Excel:</h6>
                                            <div class="mb-2">
                                                <span class="badge" style="background-color: #D6DCE5; color: #333;">Gray</span>
                                                <small class="ml-2">ID Column (Jangan diubah)</small>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge" style="background-color: #FFE699; color: #333;">Yellow</span>
                                                <small class="ml-2">Field Mandatory (wajib diisi)</small>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge" style="background-color: #FF6B6B; color: white;">Red</span>
                                                <small class="ml-2">Error Information (hanya info)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-success"><i class="fas fa-list-check mr-1"></i>Mandatory Fields (*):</h6>
                                            <ul class="list-unstyled small">
                                                <li><i class="fas fa-check text-success mr-1"></i>MAK</li>
                                                <li><i class="fas fa-check text-success mr-1"></i>Nilai Satuan</li>
                                                <li><i class="fas fa-check text-success mr-1"></i>Kode Transaksi</li>
                                                <li><i class="fas fa-check text-success mr-1"></i>Jenis Transaksi</li>
                                                <li><i class="fas fa-check text-success mr-1"></i>Kode Barang</li>
                                                <li><i class="fas fa-check text-success mr-1"></i>NUP Awal & Akhir (hanya angka)</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Petunjuk Perbaikan:</h6>
                                            <ol class="small">
                                                <li>Download file Excel error</li>
                                                <li>Perbaiki data di kolom yang berwarna <strong>kuning</strong></li>
                                                <li>Jangan ubah kolom <strong>ID (abu-abu)</strong></li>
                                                <li>Kolom <strong>merah</strong> hanya informasi saja</li>
                                                <li>NUP harus berupa angka saja</li>
                                                <li>Upload kembali setelah diperbaiki</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-lightbulb mr-2"></i>
                                        <strong>Tips:</strong> File Excel sudah dilengkapi dengan color coding dan header yang jelas.
                                        Field yang berwarna kuning adalah field mandatory yang perlu diperbaiki.
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Fix Data Interface -->
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-upload mr-2"></i>
                                        Upload Data Perbaikan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fixDataFile">Select Fixed Excel File:</label>
                                                <input type="file" id="fixDataFile" class="form-control-file" accept=".xlsx,.xls">
                                                <small class="form-text text-muted">Upload file Excel yang sudah diperbaiki</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fixSheetName">Sheet Name:</label>
                                                <input type="text" id="fixSheetName" class="form-control" value="Worksheet 1" placeholder="Worksheet 1">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button id="uploadFixBtn" class="btn btn-success btn-block">
                                                    <i class="fas fa-upload mr-1"></i>Upload Fix
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button id="refreshErrorData" class="btn btn-outline-secondary btn-block">
                                                    <i class="fas fa-sync-alt"></i> Refresh
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fix Upload Progress -->
                                    <div id="fixUploadProgress" class="progress mt-3" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" id="fixProgressBar">
                                            <span id="fixProgressText">0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Data Table -->
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span id="errorRecordCount" class="badge badge-danger">0 error records</span>
                                    </div>
                                    <div>
                                        <button id="refreshErrorData" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <table id="errorTable" class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%"></th>
                                            <th class="sortable" data-sort="kode_barang">Kode Barang</th>
                                            <th class="sortable" data-sort="nup_awal">NUP Range</th>
                                            <th class="sortable" data-sort="error_type">Error Type</th>
                                            <th class="sortable" data-sort="error_messages">Error Messages</th>
                                            <th class="sortable" data-sort="upload_batch">Batch</th>
                                            <th class="sortable" data-sort="created_at">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody id="errorTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-check-circle mb-2"></i><br>Tidak ada data bermasalah
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Error Pagination -->
                            <div id="errorPaginationContainer" class="d-flex justify-content-between align-items-center mt-3" style="display: none !important;">
                                <span id="errorPaginationInfo" class="text-muted">Showing 0 to 0 of 0 entries</span>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="errorPaginationNav"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Batch Management -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-layer-group mr-2"></i>
                                Batch History & Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="batchHistoryContainer">
                                <!-- Batch history will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.info-box {
    display: block;
    min-height: 90px;
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
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box .info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
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

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
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

.batch-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 10px;
    padding: 15px;
}

.batch-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
class KKSaktiDashboard {
    constructor() {
        this.currentPage = 1;
        this.currentSortBy = 'id';
        this.currentSortDir = 'desc';
        this.isLoading = false;
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialData();
    }

    loadInitialData() {
        this.loadDashboardStats();
        this.loadBatchHistory();
        this.loadBatchOptions();
        this.loadData(1);
        this.loadErrorStats();
        this.loadErrorData(1);
    }

    bindEvents() {
        // Upload functionality
        $('#uploadBtn').click(() => this.handleUpload());

        // Search with debounce
        $('#filterSearch').on('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadData(1), 800);
        });

        // Filter actions
        $('#applyFilter').click(() => this.loadData(1));
        $('#clearFilter').click(() => this.clearAllFilters());
        $('#refreshData, #refreshStats').click(() => this.refreshAll());

        // Advanced filter toggle
        $('#toggleAdvancedFilters').click(() => this.toggleAdvancedFilters());

        // Export functionality
        $('#exportBartender').click(() => this.handleExport());

        // Table interactions
        $('#kksaktiTableBody').on('click', '.btn-expand', (e) => this.toggleRowDetails(e));
        $('#kksaktiTable').on('click', '.sortable', (e) => this.handleSort(e));

        // Pagination
        $(document).on('click', '#paginationNav a.page-link', (e) => this.handlePagination(e));

        // Per page change
        $('#perPageSelect').change(() => this.loadData(1));

        // Responsive table scroll
        this.handleResponsiveTable();

        // Error management events
        $('#applyErrorFilter').click(() => this.loadErrorData(1));
        $('#exportErrors').click(() => this.handleErrorExport());
        $('#uploadFixBtn').click(() => this.handleFixUpload());
        $('#refreshErrorData').click(() => this.refreshErrorData());
        $('#errorTable').on('click', '.sortable', (e) => this.handleErrorSort(e));
        $('#errorTableBody').on('click', '.btn-expand', (e) => this.toggleErrorRowDetails(e));

        // Error search with debounce
        $('#filterErrorSearch').on('input', () => {
            clearTimeout(this.errorSearchTimeout);
            this.errorSearchTimeout = setTimeout(() => this.loadErrorData(1), 800);
        });

        // Excel info panel toggle
        $('#toggleExcelInfo').click(() => this.toggleExcelInfoPanel());
    }

    // Dashboard Stats
    loadDashboardStats() {
        $.get('{{ route("kksakti.stats") }}')
        .done((response) => {
            if (response.status === 'success') {
                this.updateStatsDisplay(response.data);
            }
        })
        .fail(() => this.showToast('Failed to load dashboard stats', 'error'));
    }

    updateStatsDisplay(data) {
        $('#totalRecords').text(data.total_records.toLocaleString());
        $('#totalBatches').text(data.total_batches);
        $('#totalNilai').text('Rp ' + (data.total_nilai || 0).toLocaleString());
        $('#latestUpload').text(data.latest_batch || '-');

        if (data.date_range.latest) {
            $('#latestUploadTime').text(new Date(data.date_range.latest).toLocaleDateString());
        }

        // Update record stats
        if (data.total_records > 0) {
            const avgNilai = Math.round((data.total_nilai || 0) / data.total_records);
            $('#recordStats').text(`Avg: Rp ${avgNilai.toLocaleString()}`);
        }
    }

    // Data Loading with Enhanced Filtering
    loadData(page = 1) {
        if (this.isLoading) return;

        const filters = this.collectFilters();
        filters.page = page;
        filters.per_page = $('#perPageSelect').val();
        filters.sort_by = this.currentSortBy;
        filters.sort_dir = this.currentSortDir;

        this.isLoading = true;
        this.showLoading();

        $.get('{{ route("kksakti.data") }}', filters)
        .done((response) => {
            if (response.status === 'success') {
                this.renderTable(response.data);
                this.renderPagination(response.pagination);
                this.updateRecordCount(response.data, response.pagination);

                if (page === 1) {
                    this.showToast(`Loaded ${response.data.length} records`, 'success', 2000);
                }
            }
        })
        .fail((xhr) => {
            this.showError('Error loading data');
            this.showToast(`Error: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
        })
        .always(() => {
            this.isLoading = false;
            this.hideLoading();
        });
    }

    collectFilters() {
        const filters = {};

        // Basic filters
        const search = $('#filterSearch').val().trim();
        if (search) filters.search = search;

        const batch = $('#filterBatch').val();
        if (batch) filters.batch_id = batch;

        const kodeBarang = $('#filterKodeBarang').val().trim();
        if (kodeBarang) filters.kode_barang = kodeBarang;

        const nup = $('#filterNup').val().trim();
        if (nup) filters.nup = nup;

        // Advanced filters
        if ($('#advancedFilters').is(':visible')) {
            const dateFrom = $('#filterDateFrom').val();
            if (dateFrom) filters.date_from = dateFrom;

            const dateTo = $('#filterDateTo').val();
            if (dateTo) filters.date_to = dateTo;

            const jenisTransaksi = $('#filterJenisTransaksi').val();
            if (jenisTransaksi) filters.jenis_transaksi = jenisTransaksi;

            const minNilai = $('#filterMinNilai').val();
            if (minNilai) filters.min_nilai = minNilai;

            const maxNilai = $('#filterMaxNilai').val();
            if (maxNilai) filters.max_nilai = maxNilai;

            const statusLabel = $('#filterStatusLabel').val();
            if (statusLabel) filters.status_pelabelan = statusLabel;
        }

        return filters;
    }

    // Enhanced Table Rendering
    renderTable(data) {
        const tbody = $('#kksaktiTableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-inbox mb-2"></i><br>No data found</td></tr>');
            $('#paginationContainer').hide();
            return;
        }

        data.forEach((item, index) => {
            const row = this.createTableRow(item, index);
            tbody.append(row.main).append(row.detail);
        });
    }

    createTableRow(item, index) {
        const rowId = `row-${index}`;
        const detailRowId = `details-${index}`;

        const mainRow = $(`
            <tr id="${rowId}" class="main-row" data-detail-id="${detailRowId}">
                <td>
                    <button class="btn btn-xs btn-outline-secondary btn-expand" title="View Details">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </td>
                <td>
                    <span class="font-weight-bold">${item.kode_barang || '-'}</span>
                    ${this.createStatusBadge(item)}
                </td>
                <td>
                    <span class="badge badge-outline-primary">${this.getNupRange(item)}</span>
                </td>
                <td class="text-truncate" style="max-width: 250px;" title="${item.uraian_barang || '-'}">
                    ${item.uraian_barang || '-'}
                    ${item.merk_tipe_bmn ? `<small class="text-muted d-block">${item.merk_tipe_bmn}</small>` : ''}
                </td>
                <td class="text-right">
                    <strong>${this.formatRupiah(item.nilai_total)}</strong>
                    ${item.jumlah > 1 ? `<small class="text-muted d-block">x${item.jumlah}</small>` : ''}
                </td>
                <td>${this.formatDate(item.tanggal)}</td>
                <td><span class="badge badge-secondary">Batch #${item.upload_batch}</span></td>
            </tr>
        `);

        const detailRow = $(`
            <tr id="${detailRowId}" class="details-row" style="display: none;">
                <td colspan="7">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary">Financial Info</h6>
                            <strong>Nilai Satuan:</strong> ${this.formatRupiah(item.nilai_satuan)}<br>
                            <strong>Nilai Total:</strong> ${this.formatRupiah(item.nilai_total)}<br>
                            <strong>Nilai SPM:</strong> ${this.formatRupiah(item.nilai_spm)}<br>
                            <strong>Jumlah:</strong> ${item.jumlah || 1}
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-success">Transaction Info</h6>
                            <strong>Jenis:</strong> ${item.jenis_transaksi || '-'}<br>
                            <strong>MAK:</strong> ${item.mak || '-'}<br>
                            <strong>Kode Transaksi:</strong> ${item.kode_transaksi || '-'}<br>
                            <strong>SPBY:</strong> ${item.spby || '-'}
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-warning">Location & Status</h6>
                            <strong>Lokasi:</strong> ${item.lokasi || '-'}<br>
                            <strong>Bagian:</strong> ${item.bagian || '-'}<br>
                            <strong>Status Label:</strong> ${item.status_pelabelan || 'Belum'}<br>
                            ${item.tanggal_pelabelan ? `<strong>Tgl Label:</strong> ${this.formatDate(item.tanggal_pelabelan)}` : ''}
                        </div>
                    </div>
                    ${item.keterangan ? `<div class="mt-2"><strong>Keterangan:</strong> ${item.keterangan}</div>` : ''}
                </td>
            </tr>
        `);

        return { main: mainRow, detail: detailRow };
    }

    createStatusBadge(item) {
        let statusClass = 'secondary';
        let statusText = 'Unknown';

        if (item.status_pelabelan) {
            if (item.status_pelabelan.toLowerCase().includes('sudah')) {
                statusClass = 'success';
                statusText = 'Labeled';
            } else if (item.status_pelabelan.toLowerCase().includes('belum')) {
                statusClass = 'warning';
                statusText = 'Pending';
            }
        }

        return `<small class="badge badge-${statusClass} ml-1">${statusText}</small>`;
    }

    // UI Enhancements
    toggleAdvancedFilters() {
        const $panel = $('#advancedFilters');
        const $toggle = $('#toggleAdvancedFilters');
        const $icon = $toggle.find('i');

        if ($panel.is(':visible')) {
            $panel.slideUp();
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $toggle.html('<i class="fas fa-chevron-down"></i> Advanced');
        } else {
            $panel.slideDown();
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $toggle.html('<i class="fas fa-chevron-up"></i> Advanced');
        }
    }

    toggleRowDetails(e) {
        const $btn = $(e.target).closest('.btn-expand');
        const detailRowId = $btn.closest('tr').data('detail-id');
        const $detailRow = $('#' + detailRowId);
        const $icon = $btn.find('i');

        if ($detailRow.is(':visible')) {
            $detailRow.slideUp(200);
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else {
            $detailRow.slideDown(200);
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    }

    handleResponsiveTable() {
        $(window).on('resize', () => {
            if ($(window).width() < 768) {
                $('.table-responsive').addClass('table-responsive-sm');
            } else {
                $('.table-responsive').removeClass('table-responsive-sm');
            }
        }).trigger('resize');
    }

    // Enhanced Upload with Better Progress
    handleUpload() {
        const fileInput = $('#excelFile')[0];
        if (!fileInput.files[0]) {
            this.showToast('Please select an Excel file', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('excel_file', fileInput.files[0]);
        formData.append('sheet_name', $('#sheetName').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        const $btn = $('#uploadBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        $('#uploadProgress').show();

        this.animateProgress();

        $.ajax({
            url: '{{ route("kksakti.upload") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 600000,
            success: (response) => {
                this.completeProgress();

                if (response.status === 'success') {
                    this.showToast(`✅ Success! ${response.data.processed_records} records processed, ${response.data.skipped_records} skipped`, 'success', 7000);
                    this.refreshAll();
                    $('#excelFile').val('');
                } else {
                    this.showToast(`❌ Upload failed: ${response.message}`, 'error');
                }
            },
            error: (xhr) => {
                this.completeProgress();
                this.showToast(`❌ Upload failed: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            },
            complete: () => {
                $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i>Upload Excel');
                setTimeout(() => $('#uploadProgress').hide(), 2000);
            }
        });
    }

    animateProgress() {
        let progress = 0;
        this.progressInterval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 90) progress = 90;
            $('#progressBar').css('width', progress + '%');
            $('#progressText').text(Math.round(progress) + '%');
        }, 800);
    }

    completeProgress() {
        clearInterval(this.progressInterval);
        $('#progressBar').css('width', '100%');
        $('#progressText').text('100%');
    }

    // Utility Methods
    refreshAll() {
        this.loadDashboardStats();
        this.loadBatchHistory();
        this.loadBatchOptions();
        this.loadData(1);
        this.refreshErrorData();
    }

    refreshErrorData() {
        this.loadErrorStats();
        this.loadErrorData(1);
    }

    clearAllFilters() {
        $('#filterSearch, #filterKodeBarang, #filterNup, #filterDateFrom, #filterDateTo, #filterMinNilai, #filterMaxNilai').val('');
        $('#filterBatch, #filterJenisTransaksi, #filterStatusLabel').val('');
        this.loadData(1);
    }

    showLoading() {
        $('#kksaktiTableBody').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary mr-2"></div>Loading data...</td></tr>');
    }

    hideLoading() {
        // Handled in renderTable
    }

    showError(message) {
        $('#kksaktiTableBody').html(`<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mb-2"></i><br>${message}</td></tr>`);
    }

    updateRecordCount(data, pagination) {
        $('#recordCount').text(`${data.length} of ${pagination.total} records`);

        if (pagination.total > 0) {
            const showing = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;
            $('#paginationInfo').text(showing);
        }
    }

    // Keep existing methods for batch management, export, etc.
    loadBatchHistory() {
        $.get('{{ route("kksakti.batch.history") }}')
        .done((response) => {
            if (response.status === 'success') {
                const container = $('#batchHistoryContainer');
                container.empty();

                if (response.data.length === 0) {
                    container.html('<div class="text-center text-muted py-3"><i class="fas fa-inbox mb-2"></i><br>No batch history found.</div>');
                    return;
                }

                response.data.forEach((batch) => {
                    const batchCard = this.createBatchCard(batch);
                    container.append(batchCard);
                });
            }
        });
    }

    createBatchCard(batch) {
        return $(`
            <div class="batch-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-layer-group text-primary mr-1"></i>
                            Batch #${batch.upload_batch}
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-calendar mr-1"></i>
                            ${new Date(batch.uploaded_at).toLocaleString()}
                        </small>
                    </div>
                    <div class="text-right">
                        <div><i class="fas fa-database text-info mr-1"></i><strong>${batch.total_records}</strong> records</div>
                        <div class="text-muted"><i class="fas fa-coins text-warning mr-1"></i>Rp ${(batch.total_nilai || 0).toLocaleString()}</div>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary mr-1" onclick="dashboard.filterByBatch(${batch.upload_batch})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="dashboard.deleteBatch(${batch.upload_batch})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
    }

    loadBatchOptions() {
        $.get('{{ route("kksakti.batch.history") }}')
        .done((response) => {
            if (response.status === 'success') {
                const select = $('#filterBatch');
                select.find('option:not(:first)').remove();

                response.data.forEach((batch) => {
                    select.append(`<option value="${batch.upload_batch}">Batch #${batch.upload_batch} (${batch.total_records} records)</option>`);
                });
            }
        });
    }

    handleSort(e) {
        const sortBy = $(e.target).data('sort');

        if (this.currentSortBy === sortBy) {
            this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            this.currentSortBy = sortBy;
            this.currentSortDir = 'asc';
        }

        this.loadData(1);
    }

    handlePagination(e) {
        e.preventDefault();
        const page = parseInt($(e.target).data('page'));
        if (page && page !== this.currentPage && !this.isLoading) {
            this.loadData(page);
        }
    }

    handleExport() {
        const filters = this.collectFilters();
        const params = new URLSearchParams();

        Object.keys(filters).forEach(key => {
            if (filters[key]) params.append(key, filters[key]);
        });

        window.location.href = '{{ route("kksakti.export.bartender") }}?' + params.toString();
        this.showToast('Export started...', 'info');
    }

    renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#paginationContainer').hide();
            return;
        }

        $('#paginationContainer').show();

        let paginationHtml = '';

        // Previous
        paginationHtml += `<li class="page-item ${pagination.current_page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                <i class="fas fa-chevron-left"></i>
            </a></li>`;

        // Pages
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        if (startPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        if (endPage < pagination.last_page) {
            if (endPage < pagination.last_page - 1) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
        }

        // Next
        paginationHtml += `<li class="page-item ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                <i class="fas fa-chevron-right"></i>
            </a></li>`;

        $('#paginationNav').html(paginationHtml);
        this.currentPage = pagination.current_page;
    }

    // Utility helper methods
    formatRupiah(value) {
        if (!value || value === 0) return 'Rp 0';
        return 'Rp ' + parseInt(value).toLocaleString('id-ID');
    }

    formatDate(dateString) {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('id-ID');
    }

    getNupRange(item) {
        if (item.nup_awal === item.nup_akhir) {
            return item.nup_awal;
        }
        return `${item.nup_awal} - ${item.nup_akhir}`;
    }

    showToast(message, type = 'info', duration = 5000) {
        const toastId = 'toast_' + Date.now();
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };

        const toast = $(`
            <div id="${toastId}" class="toast toast-${type} p-3 animated fadeInRight" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-${icons[type]} mr-2"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close ml-2" onclick="$('#${toastId}').fadeOut()" style="background: none; border: none; color: inherit;">&times;</button>
                </div>
            </div>
        `);

        $('body').append(toast);
        toast.fadeIn(300);
        setTimeout(() => $('#' + toastId).fadeOut(300, function() { $(this).remove(); }), duration);
    }

    // Public methods for global access
    filterByBatch(batchId) {
        $('#filterBatch').val(batchId);
        this.loadData(1);
        this.showToast(`Filtering by Batch #${batchId}`, 'info');
    }

    deleteBatch(batchId) {
        if (!confirm(`Delete Batch #${batchId}? This action cannot be undone.`)) return;

        $.ajax({
            url: '{{ route("kksakti.batch.delete", ":id") }}'.replace(':id', batchId),
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: (response) => {
                if (response.status === 'success') {
                    this.showToast(response.message, 'success');
                    this.refreshAll();
                }
            },
            error: (xhr) => {
                this.showToast(`Delete failed: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            }
        });
    }

    // Error Management Methods
    loadErrorStats() {
        $.get('{{ route("kksakti.error.stats") }}')
        .done((response) => {
            if (response.status === 'success') {
                this.updateErrorStatsDisplay(response.data);
            }
        })
        .fail(() => this.showToast('Failed to load error stats', 'error'));
    }

    updateErrorStatsDisplay(data) {
        $('#totalErrors').text(data.total_errors.toLocaleString());
        $('#needsReview').text(data.needs_review.toLocaleString());
        $('#fixedErrors').text(data.fixed_errors.toLocaleString());
        $('#errorTypes').text(Object.keys(data.error_by_type).length);

        // Update error batch options
        const $errorBatchSelect = $('#filterErrorBatch');
        $errorBatchSelect.find('option:not(:first)').remove();

        // Add batches that have errors
        this.loadBatchOptions($errorBatchSelect, true);
    }

    loadErrorData(page = 1) {
        if (this.isErrorLoading) return;

        const filters = this.collectErrorFilters();
        filters.page = page;
        filters.per_page = 50;
        filters.sort_by = this.currentErrorSortBy || 'created_at';
        filters.sort_dir = this.currentErrorSortDir || 'desc';

        this.isErrorLoading = true;
        this.showErrorLoading();

        $.get('{{ route("kksakti.error.data") }}', filters)
        .done((response) => {
            if (response.status === 'success') {
                this.renderErrorTable(response.data);
                this.renderErrorPagination(response.pagination);
                this.updateErrorRecordCount(response.data, response.pagination);
            }
        })
        .fail((xhr) => {
            this.showErrorTableError('Error loading error data');
            this.showToast(`Error: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
        })
        .always(() => {
            this.isErrorLoading = false;
            this.hideErrorLoading();
        });
    }

    collectErrorFilters() {
        const filters = {};

        const errorType = $('#filterErrorType').val();
        if (errorType) filters.error_type = errorType;

        const batch = $('#filterErrorBatch').val();
        if (batch) filters.batch_id = batch;

        const search = $('#filterErrorSearch').val().trim();
        if (search) filters.search = search;

        return filters;
    }

    renderErrorTable(data) {
        const tbody = $('#errorTableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-check-circle mb-2"></i><br>Tidak ada data bermasalah</td></tr>');
            $('#errorPaginationContainer').hide();
            return;
        }

        data.forEach((item, index) => {
            const row = this.createErrorTableRow(item, index);
            tbody.append(row.main).append(row.detail);
        });
    }

    createErrorTableRow(item, index) {
        const rowId = `error-row-${index}`;
        const detailRowId = `error-details-${index}`;

        const mainRow = $(`
            <tr id="${rowId}" class="main-row" data-detail-id="${detailRowId}">
                <td>
                    <button class="btn btn-xs btn-outline-secondary btn-expand" title="View Details">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </td>
                <td>
                    <span class="font-weight-bold">${item.kode_barang || '-'}</span>
                    ${this.createErrorStatusBadge(item.error_type)}
                </td>
                <td>
                    <span class="badge badge-outline-primary">${this.getNupRange(item)}</span>
                </td>
                <td>
                    <span class="badge badge-danger">${item.error_type || 'UNKNOWN'}</span>
                </td>
                <td class="text-truncate" style="max-width: 200px;" title="${item.error_messages || '-'}">
                    ${item.error_messages || '-'}
                </td>
                <td><span class="badge badge-secondary">Batch #${item.upload_batch}</span></td>
                <td>${this.formatDate(item.created_at)}</td>
            </tr>
        `);

        const detailRow = $(`
            <tr id="${detailRowId}" class="details-row" style="display: none;">
                <td colspan="7">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">Error Information</h6>
                            <strong>Error Type:</strong> ${item.error_type || 'Unknown'}<br>
                            <strong>Error Messages:</strong><br>
                            <div class="alert alert-danger p-2 mt-1">
                                ${this.formatErrorMessages(item.error_messages)}
                            </div>
                            <strong>Needs Review:</strong> ${item.needs_review ? 'Yes' : 'No'}<br>
                            <strong>Created:</strong> ${new Date(item.created_at).toLocaleString()}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">Record Details</h6>
                            <strong>Uraian Barang:</strong> ${item.uraian_barang || '-'}<br>
                            <strong>Nilai Total:</strong> ${this.formatRupiah(item.nilai_total)}<br>
                            <strong>Nama PT:</strong> ${item.nama_pt || '-'}<br>
                            <strong>Bagian:</strong> ${item.bagian || '-'}
                        </div>
                    </div>
                    ${item.error_details ? `<div class="mt-2"><strong>Technical Details:</strong><br><pre class="text-muted small">${JSON.stringify(item.error_details, null, 2)}</pre></div>` : ''}
                </td>
            </tr>
        `);

        return { main: mainRow, detail: detailRow };
    }

    createErrorStatusBadge(errorType) {
        let badgeClass = 'danger';
        let badgeText = 'Error';

        switch (errorType) {
            case 'MISSING_MANDATORY':
                badgeClass = 'warning';
                badgeText = 'Missing';
                break;
            case 'INVALID_NUP_FORMAT':
                badgeClass = 'danger';
                badgeText = 'Invalid NUP';
                break;
            case 'INVALID_DATE_FORMAT':
                badgeClass = 'info';
                badgeText = 'Date Format';
                break;
        }

        return `<small class="badge badge-${badgeClass} ml-1">${badgeText}</small>`;
    }

    formatErrorMessages(messages) {
        if (!messages) return 'No error message';

        return messages.split(';').map(msg =>
            `<div><i class="fas fa-exclamation-circle text-danger mr-1"></i>${msg.trim()}</div>`
        ).join('');
    }

    handleErrorExport() {
        const filters = this.collectErrorFilters();
        const params = new URLSearchParams();

        Object.keys(filters).forEach(key => {
            if (filters[key]) params.append(key, filters[key]);
        });

        // Show loading state
        const $btn = $('#exportErrors');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span>Exporting...');

        // Create form to submit for file download
        const form = $('<form>', {
            'method': 'GET',
            'action': '{{ route("kksakti.export.errors") }}'
        });

        // Add filter parameters to form
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': filters[key]
                }));
            }
        });

        // Submit form to trigger download
        form.appendTo('body').submit().remove();

        // Show success message after a delay
        setTimeout(() => {
            this.showToast('Export dimulai! File akan segera didownload.', 'success');
            $btn.prop('disabled', false).html(originalText);
        }, 1000);
    }

    handleFixUpload() {
        const fileInput = $('#fixDataFile')[0];
        if (!fileInput.files[0]) {
            this.showToast('Please select a fixed Excel file', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('excel_file', fileInput.files[0]);
        formData.append('sheet_name', $('#fixSheetName').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        const $btn = $('#uploadFixBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        $('#fixUploadProgress').show();

        this.animateFixProgress();

        $.ajax({
            url: '{{ route("kksakti.upload.fix") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 600000,
            success: (response) => {
                this.completeFixProgress();

                if (response.status === 'success') {
                    this.showToast(`✅ Success! ${response.data.processed} records fixed, ${response.data.notFound} not found`, 'success', 7000);
                    this.refreshAll();
                    $('#fixDataFile').val('');
                } else {
                    this.showToast(`❌ Fix upload failed: ${response.message}`, 'error');
                }
            },
            error: (xhr) => {
                this.completeFixProgress();
                this.showToast(`❌ Fix upload failed: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            },
            complete: () => {
                $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i>Upload Fix');
                setTimeout(() => $('#fixUploadProgress').hide(), 2000);
            }
        });
    }

    animateFixProgress() {
        let progress = 0;
        this.fixProgressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $('#fixProgressBar').css('width', progress + '%');
            $('#fixProgressText').text(Math.round(progress) + '%');
        }, 800);
    }

    completeFixProgress() {
        clearInterval(this.fixProgressInterval);
        $('#fixProgressBar').css('width', '100%');
        $('#fixProgressText').text('100%');
    }

    toggleErrorRowDetails(e) {
        const $btn = $(e.target).closest('.btn-expand');
        const detailRowId = $btn.closest('tr').data('detail-id');
        const $detailRow = $('#' + detailRowId);
        const $icon = $btn.find('i');

        if ($detailRow.is(':visible')) {
            $detailRow.slideUp(200);
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else {
            $detailRow.slideDown(200);
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    }

    handleErrorSort(e) {
        const sortBy = $(e.target).data('sort');

        if (this.currentErrorSortBy === sortBy) {
            this.currentErrorSortDir = this.currentErrorSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            this.currentErrorSortBy = sortBy;
            this.currentErrorSortDir = 'asc';
        }

        this.loadErrorData(1);
    }

    renderErrorPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            $('#errorPaginationContainer').hide();
            return;
        }

        $('#errorPaginationContainer').show();
        // Similar pagination logic as main table
        // Implementation similar to renderPagination but for error data
    }

    updateErrorRecordCount(data, pagination) {
        $('#errorRecordCount').text(`${data.length} of ${pagination.total} error records`);

        if (pagination.total > 0) {
            const showing = `Showing ${pagination.from}-${pagination.to} of ${pagination.total} error records`;
            $('#errorPaginationInfo').text(showing);
        }
    }

    showErrorLoading() {
        $('#errorTableBody').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-danger mr-2"></div>Loading error data...</td></tr>');
    }

    hideErrorLoading() {
        // Handled in renderErrorTable
    }

    showErrorTableError(message) {
        $('#errorTableBody').html(`<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mb-2"></i><br>${message}</td></tr>`);
    }

    toggleExcelInfoPanel() {
        const $panel = $('#excelInfoPanel');
        const $toggle = $('#toggleExcelInfo');
        const $icon = $toggle.find('i');

        if ($panel.is(':visible')) {
            $panel.slideUp();
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $panel.slideDown();
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    }
}

// Initialize dashboard when document is ready
$(document).ready(function() {
    window.dashboard = new KKSaktiDashboard();
});
</script>
@endsection
