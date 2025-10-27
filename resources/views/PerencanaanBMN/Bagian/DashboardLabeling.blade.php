{{-- resources/views/PerencanaanBMN/Bagian/DashboardLabeling.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Labeling BMN</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard Labeling</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="toast-container"></div>

    <div class="content">
        <div class="container-fluid">

            <!-- SECTION 1: Dashboard Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Status Dashboard Labeling
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-database"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Data</span>
                                            <span class="info-box-number" id="totalData">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Belum Cetak</span>
                                            <span class="info-box-number" id="belumCetak">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sudah Cetak</span>
                                            <span class="info-box-number" id="sudahCetak">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Belum Label</span>
                                            <span class="info-box-number" id="belumLabel">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sudah Label</span>
                                            <span class="info-box-number" id="sudahLabel">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary">
                                            <i class="fas fa-clipboard-check"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Siap Label</span>
                                            <span class="info-box-number" id="siapLabel">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Generate Data Labeling -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs mr-2"></i>
                                Generate Data Labeling dari KK SAKTI
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="generateBatchId">Batch KK SAKTI:</label>
                                        <select id="generateBatchId" class="form-control">
                                            <option value="">Semua Batch</option>
                                        </select>
                                        <small class="form-text text-muted">Pilih batch atau kosongkan untuk semua</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="onlyValidFixed" checked>
                                            <label class="form-check-label" for="onlyValidFixed">
                                                Hanya data Valid/Fixed
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button id="generateBtn" class="btn btn-success btn-block">
                                            <i class="fas fa-magic mr-1"></i>Generate Data Labeling
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button id="refreshStats" class="btn btn-outline-secondary btn-block">
                                            <i class="fas fa-sync-alt"></i> Refresh Stats
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="generateProgress" class="progress mt-3" style="display: none;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" id="genProgressBar">
                                    <span id="genProgressText">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Data Explorer - Labeling -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-search mr-2"></i>
                                Data Explorer - Labeling
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Filter Panel -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Filter Data
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Search:</label>
                                            <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="NUP, Kode Barang, Uraian...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Area:</label>
                                            <select id="filterArea" class="form-control form-control-sm">
                                                <option value="">Semua Area</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Gedung:</label>
                                            <select id="filterGedung" class="form-control form-control-sm">
                                                <option value="">Semua Gedung</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Ruangan:</label>
                                            <select id="filterRuangan" class="form-control form-control-sm">
                                                <option value="">Semua Ruangan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Status Cetak:</label>
                                            <select id="filterStatusCetak" class="form-control form-control-sm">
                                                <option value="">Semua Status</option>
                                                <option value="belum_cetak">Belum Cetak</option>
                                                <option value="sudah_cetak">Sudah Cetak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Status Label:</label>
                                            <select id="filterStatusLabel" class="form-control form-control-sm">
                                                <option value="">Semua Status</option>
                                                <option value="belum_label">Belum Label</option>
                                                <option value="sudah_label">Sudah Label</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Bulan Tahun Anggaran:</label>
                                            <select id="filterBulanDok" class="form-control form-control-sm">
                                                <option value="">Semua Bulan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <label class="form-label">Actions:</label>
                                            <div>
                                                <button id="applyFilter" class="btn btn-primary btn-sm mr-1">
                                                    <i class="fas fa-filter mr-1"></i>Filter
                                                </button>
                                                <button id="clearFilter" class="btn btn-secondary btn-sm mr-1">
                                                    <i class="fas fa-times mr-1"></i>Clear
                                                </button>
                                                <button id="exportBartender" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-excel mr-1"></i>Export Bartender
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <input type="checkbox" id="selectAll" class="mr-2">
                                            <label for="selectAll" class="mb-0">Select All</label>
                                            <span id="selectedCount" class="badge badge-primary ml-2">0 selected</span>
                                        </div>
                                        <div>
                                            <button id="bulkMarkCetak" class="btn btn-warning btn-sm mr-1" disabled>
                                                <i class="fas fa-print mr-1"></i>Mark as Cetak
                                            </button>
                                            <button id="bulkMarkLabel" class="btn btn-success btn-sm mr-1" disabled>
                                                <i class="fas fa-tag mr-1"></i>Mark as Label
                                            </button>
                                            <button id="bulkDelete" class="btn btn-danger btn-sm" disabled>
                                                <i class="fas fa-trash mr-1"></i>Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span id="recordCount" class="badge badge-primary">0 records</span>
                                    </div>
                                    <div>
                                        <label class="form-label mb-0 mr-2">Show:</label>
                                        <select id="perPageSelect" class="form-control form-control-sm d-inline-block mr-2" style="width: auto;">
                                            <option value="25">25</option>
                                            <option value="50" selected>50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <button id="refreshData" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <table id="labelingTable" class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="3%"><input type="checkbox" id="selectAllHeader"></th>
                                            <th width="5%"></th>
                                            <th class="sortable" data-sort="kode_barang">Kode Barang</th>
                                            <th class="sortable" data-sort="nup">NUP</th>
                                            <th class="sortable" data-sort="uraian_barang">Uraian Barang</th>
                                            <th>Lokasi</th>
                                            <th class="sortable" data-sort="status_cetak">Cetak</th>
                                            <th class="sortable" data-sort="status_label">Label</th>
                                        </tr>
                                    </thead>
                                    <tbody id="labelingTableBody">
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

            <!-- SECTION 4: Statistik per Lokasi -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Breakdown per Lokasi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="lokasiStatsContainer" class="table-responsive">
                                <!-- Lokasi stats will be loaded here -->
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

.details-row td {
    background-color: #f8f9fa;
    padding: 15px !important;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
class LabelingDashboard {
    constructor() {
        this.currentPage = 1;
        this.currentSortBy = 'id';
        this.currentSortDir = 'desc';
        this.isLoading = false;
        this.searchTimeout = null;
        this.selectedIds = new Set();
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialData();
    }

    loadInitialData() {
        this.loadStats();
        this.loadBatchOptions();
        this.loadLokasiOptions();
        this.loadBulanDokOptions();
        this.loadData(1);
    }

    bindEvents() {
        // Generate functionality
        $('#generateBtn').click(() => this.handleGenerate());

        // Search with debounce
        $('#filterSearch').on('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadData(1), 800);
        });

        // Filter actions
        $('#applyFilter').click(() => this.loadData(1));
        $('#clearFilter').click(() => this.clearAllFilters());
        $('#refreshData, #refreshStats').click(() => this.refreshAll());

        // Export functionality
        $('#exportBartender').click(() => this.handleExport());

        // Table interactions
        $('#labelingTableBody').on('click', '.btn-expand', (e) => this.toggleRowDetails(e));
        $('#labelingTable').on('click', '.sortable', (e) => this.handleSort(e));

        // Pagination
        $(document).on('click', '#paginationNav a.page-link', (e) => this.handlePagination(e));

        // Per page change
        $('#perPageSelect').change(() => this.loadData(1));

        // Checkbox selection
        $('#selectAll, #selectAllHeader').change((e) => this.handleSelectAll(e));
        $(document).on('change', '.row-checkbox', (e) => this.handleRowCheckbox(e));

        // Bulk actions
        $('#bulkMarkCetak').click(() => this.handleBulkMarkCetak());
        $('#bulkMarkLabel').click(() => this.handleBulkMarkLabel());
        $('#bulkDelete').click(() => this.handleBulkDelete());
    }

    // Stats Methods
    loadStats() {
        $.get('{{ route("labeling.stats") }}')
        .done((response) => {
            if (response.status === 'success') {
                this.updateStatsDisplay(response.data);
                this.renderLokasiStats(response.stats_by_lokasi);
            }
        })
        .fail(() => this.showToast('Failed to load stats', 'error'));
    }

    updateStatsDisplay(data) {
        $('#totalData').text(data.total.toLocaleString());
        $('#belumCetak').text(data.belum_cetak.toLocaleString());
        $('#sudahCetak').text(data.sudah_cetak.toLocaleString());
        $('#belumLabel').text(data.belum_label.toLocaleString());
        $('#sudahLabel').text(data.sudah_label.toLocaleString());
        $('#siapLabel').text(data.siap_label.toLocaleString());
    }

    renderLokasiStats(data) {
        const container = $('#lokasiStatsContainer');

        if (data.length === 0) {
            container.html('<div class="text-center text-muted py-3">Tidak ada data lokasi</div>');
            return;
        }

        let table = `
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Gedung</th>
                        <th>Ruangan</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Sudah Cetak</th>
                        <th class="text-right">Sudah Label</th>
                        <th class="text-right">Progress</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.forEach(item => {
            const cetakPct = item.total > 0 ? Math.round((item.sudah_cetak / item.total) * 100) : 0;
            const labelPct = item.total > 0 ? Math.round((item.sudah_label / item.total) * 100) : 0;

            table += `
                <tr>
                    <td>${item.area || '-'}</td>
                    <td>${item.gedung || '-'}</td>
                    <td>${item.ruangan || '-'}</td>
                    <td class="text-right"><strong>${item.total}</strong></td>
                    <td class="text-right">${item.sudah_cetak}</td>
                    <td class="text-right">${item.sudah_label}</td>
                    <td class="text-right">
                        <small class="text-muted">Cetak: ${cetakPct}% | Label: ${labelPct}%</small>
                    </td>
                </tr>
            `;
        });

        table += '</tbody></table>';
        container.html(table);
    }

    // Data Loading
    loadData(page = 1) {
        if (this.isLoading) return;

        const filters = this.collectFilters();
        filters.page = page;
        filters.per_page = $('#perPageSelect').val();
        filters.sort_by = this.currentSortBy;
        filters.sort_dir = this.currentSortDir;

        this.isLoading = true;
        this.showLoading();

        $.get('{{ route("labeling.data") }}', filters)
        .done((response) => {
            if (response.status === 'success') {
                this.renderTable(response.data);
                this.renderPagination(response.pagination);
                this.updateRecordCount(response.data, response.pagination);
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

        const search = $('#filterSearch').val().trim();
        if (search) filters.search = search;

        const area = $('#filterArea').val();
        if (area) filters.area = area;

        const gedung = $('#filterGedung').val();
        if (gedung) filters.gedung = gedung;

        const ruangan = $('#filterRuangan').val();
        if (ruangan) filters.ruangan = ruangan;

        const statusCetak = $('#filterStatusCetak').val();
        if (statusCetak) filters.status_cetak = statusCetak;

        const statusLabel = $('#filterStatusLabel').val();
        if (statusLabel) filters.status_label = statusLabel;

        const bulanDok = $('#filterBulanDok').val();
        if (bulanDok) filters.bulan_dok = bulanDok;

        return filters;
    }

    renderTable(data) {
        const tbody = $('#labelingTableBody');
        tbody.empty();
        this.selectedIds.clear();
        this.updateBulkButtons();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox mb-2"></i><br>No data found</td></tr>');
            $('#paginationContainer').hide();
            return;
        }

        data.forEach((item, index) => {
            const row = this.createTableRow(item, index);
            tbody.append(row.main).append(row.detail);
        });

        $('#selectAll, #selectAllHeader').prop('checked', false);
    }

    createTableRow(item, index) {
        const rowId = `row-${index}`;
        const detailRowId = `details-${index}`;

        const cetakBadge = item.status_cetak === 'sudah_cetak'
            ? '<span class="badge badge-success"> Sudah Cetak</span>'
            : '<span class="badge badge-warning">Belum Cetak</span>';

        const labelBadge = item.status_label === 'sudah_label'
            ? '<span class="badge badge-success"> Sudah Label</span>'
            : '<span class="badge badge-warning">Belum Label</span>';

        const mainRow = $(`
            <tr id="${rowId}" class="main-row" data-detail-id="${detailRowId}">
                <td>
                    <input type="checkbox" class="row-checkbox" data-id="${item.id}">
                </td>
                <td>
                    <button class="btn btn-xs btn-outline-secondary btn-expand" title="View Details">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </td>
                <td><span class="font-weight-bold">${item.kode_barang}</span></td>
                <td><span class="badge badge-primary">${item.nup}</span></td>
                <td class="text-truncate" style="max-width: 250px;" title="${item.uraian_barang || '-'}">
                    ${item.uraian_barang || '-'}
                </td>
                <td>
                    <small>${[item.area, item.gedung, item.ruangan].filter(Boolean).join(' - ') || '-'}</small>
                </td>
                <td>${cetakBadge}</td>
                <td>${labelBadge}</td>
            </tr>
        `);

        const detailRow = $(`
            <tr id="${detailRowId}" class="details-row" style="display: none;">
                <td colspan="8">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Detail Barang</h6>
                            <strong>Kode Barang:</strong> ${item.kode_barang}<br>
                            <strong>NUP:</strong> ${item.nup}<br>
                            <strong>Uraian:</strong> ${item.uraian_barang || '-'}<br>
                            <strong>Merek:</strong> ${item.merek || '-'}<br>
                            <strong>Tahun Perolehan:</strong> ${item.tahun_perolehan || '-'}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Lokasi & Status</h6>
                            <strong>Area:</strong> ${item.area || '-'}<br>
                            <strong>Gedung:</strong> ${item.gedung || '-'}<br>
                            <strong>Ruangan:</strong> ${item.ruangan || '-'}<br>
                            <strong>Status Cetak:</strong> ${item.status_cetak}<br>
                            ${item.tanggal_cetak ? `<strong>Tanggal Cetak:</strong> ${new Date(item.tanggal_cetak).toLocaleString()}<br>` : ''}
                            <strong>Status Label:</strong> ${item.status_label}
                        </div>
                    </div>
                </td>
            </tr>
        `);

        return { main: mainRow, detail: detailRow };
    }

    // Generate Methods
    handleGenerate() {
        const batchId = $('#generateBatchId').val();
        const onlyValidFixed = $('#onlyValidFixed').is(':checked');

        const $btn = $('#generateBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        $('#generateProgress').show();

        this.animateGenerateProgress();

        $.ajax({
            url: '{{ route("labeling.generate") }}',
            type: 'POST',
            data: {
                batch_id: batchId || null,
                only_valid_fixed: onlyValidFixed ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.completeGenerateProgress();

                if (response.status === 'success') {
                    this.showToast(` Generated ${response.data.processed} records! (${response.data.skipped} skipped)`, 'success', 7000);
                    this.refreshAll();
                } else {
                    this.showToast(`❌ Generation failed: ${response.message}`, 'error');
                }
            },
            error: (xhr) => {
                this.completeGenerateProgress();
                this.showToast(`❌ Generation failed: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            },
            complete: () => {
                $btn.prop('disabled', false).html('<i class="fas fa-magic mr-1"></i>Generate Data Labeling');
                setTimeout(() => $('#generateProgress').hide(), 2000);
            }
        });
    }

    animateGenerateProgress() {
        let progress = 0;
        this.genProgressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $('#genProgressBar').css('width', progress + '%');
            $('#genProgressText').text(Math.round(progress) + '%');
        }, 500);
    }

    completeGenerateProgress() {
        clearInterval(this.genProgressInterval);
        $('#genProgressBar').css('width', '100%');
        $('#genProgressText').text('100%');
    }

    // Checkbox & Bulk Actions
    handleSelectAll(e) {
        const isChecked = $(e.target).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);

        if (isChecked) {
            $('.row-checkbox').each((i, el) => {
                this.selectedIds.add(parseInt($(el).data('id')));
            });
        } else {
            this.selectedIds.clear();
        }

        this.updateBulkButtons();
    }

    handleRowCheckbox(e) {
        const id = parseInt($(e.target).data('id'));
        const isChecked = $(e.target).is(':checked');

        if (isChecked) {
            this.selectedIds.add(id);
        } else {
            this.selectedIds.delete(id);
        }

        this.updateBulkButtons();
    }

    updateBulkButtons() {
        const count = this.selectedIds.size;
        $('#selectedCount').text(`${count} selected`);

        const hasSelection = count > 0;
        $('#bulkMarkCetak, #bulkMarkLabel, #bulkDelete').prop('disabled', !hasSelection);
    }

    handleBulkMarkCetak() {
        if (this.selectedIds.size === 0) return;

        if (!confirm(`Mark ${this.selectedIds.size} records as CETAK?`)) return;

        $.ajax({
            url: '{{ route("labeling.mark.cetak") }}',
            type: 'POST',
            data: {
                ids: Array.from(this.selectedIds),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.status === 'success') {
                    this.showToast(response.message, 'success');
                    this.refreshAll();
                    this.selectedIds.clear();
                    this.updateBulkButtons();
                }
            },
            error: (xhr) => {
                this.showToast(`Error: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            }
        });
    }

    handleBulkMarkLabel() {
        if (this.selectedIds.size === 0) return;

        if (!confirm(`Mark ${this.selectedIds.size} records as LABEL?`)) return;

        $.ajax({
            url: '{{ route("labeling.mark.label") }}',
            type: 'POST',
            data: {
                ids: Array.from(this.selectedIds),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.status === 'success') {
                    this.showToast(response.message, 'success');
                    this.refreshAll();
                    this.selectedIds.clear();
                    this.updateBulkButtons();
                }
            },
            error: (xhr) => {
                this.showToast(`Error: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            }
        });
    }

    handleBulkDelete() {
        if (this.selectedIds.size === 0) return;

        if (!confirm(`DELETE ${this.selectedIds.size} records? This action cannot be undone.`)) return;

        $.ajax({
            url: '{{ route("labeling.bulk.delete") }}',
            type: 'POST',
            data: {
                ids: Array.from(this.selectedIds),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.status === 'success') {
                    this.showToast(response.message, 'success');
                    this.refreshAll();
                    this.selectedIds.clear();
                    this.updateBulkButtons();
                }
            },
            error: (xhr) => {
                this.showToast(`Error: ${xhr.responseJSON?.message || 'Unknown error'}`, 'error');
            }
        });
    }

    // Options Loading
    loadBatchOptions() {
        $.get('{{ route("labeling.batch.options") }}')
        .done((response) => {
            if (response.status === 'success') {
                const select = $('#generateBatchId');
                select.find('option:not(:first)').remove();

                response.data.forEach((batch) => {
                    select.append(`<option value="${batch.upload_batch}">Batch #${batch.upload_batch} (${batch.valid_count}/${batch.total} valid)</option>`);
                });
            }
        });
    }

    loadLokasiOptions() {
        $.get('{{ route("labeling.lokasi.options") }}')
        .done((response) => {
            if (response.status === 'success') {
                // Populate area
                const areaSelect = $('#filterArea');
                areaSelect.find('option:not(:first)').remove();
                response.data.areas.forEach(area => {
                    areaSelect.append(`<option value="${area}">${area}</option>`);
                });

                // Populate gedung
                const gedungSelect = $('#filterGedung');
                gedungSelect.find('option:not(:first)').remove();
                response.data.gedungs.forEach(gedung => {
                    gedungSelect.append(`<option value="${gedung}">${gedung}</option>`);
                });

                // Populate ruangan
                const ruanganSelect = $('#filterRuangan');
                ruanganSelect.find('option:not(:first)').remove();
                response.data.ruangans.forEach(ruangan => {
                    ruanganSelect.append(`<option value="${ruangan}">${ruangan}</option>`);
                });
            }
        });
    }

    loadBulanDokOptions() {
        $.get('{{ route("labeling.bulan.options") }}')
        .done((response) => {
            if (response.status === 'success') {
                const select = $('#filterBulanDok');
                select.find('option:not(:first)').remove();

                response.data.forEach(item => {
                    select.append(`<option value="${item.value}">${item.label}</option>`);
                });
            }
        });
    }

    // Export
    handleExport() {
        const filters = this.collectFilters();
        const params = new URLSearchParams();

        Object.keys(filters).forEach(key => {
            if (filters[key]) params.append(key, filters[key]);
        });

        window.location.href = '{{ route("labeling.export.bartender") }}?' + params.toString();
        this.showToast('Export started...', 'info');
    }

    // Utility Methods
    refreshAll() {
        this.loadStats();
        this.loadData(1);
    }

    clearAllFilters() {
        $('#filterSearch').val('');
        $('#filterArea, #filterGedung, #filterRuangan, #filterStatusCetak, #filterStatusLabel, #filterBulanDok').val('');
        this.loadData(1);
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

    updateRecordCount(data, pagination) {
        $('#recordCount').text(`${data.length} of ${pagination.total} records`);

        if (pagination.total > 0) {
            const showing = `Showing ${pagination.from}-${pagination.to} of ${pagination.total}`;
            $('#paginationInfo').text(showing);
        }
    }

    showLoading() {
        $('#labelingTableBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary mr-2"></div>Loading data...</td></tr>');
    }

    hideLoading() {
        // Handled in renderTable
    }

    showError(message) {
        $('#labelingTableBody').html(`<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mb-2"></i><br>${message}</td></tr>`);
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
}

// Initialize dashboard when document is ready
$(document).ready(function() {
    window.labelingDashboard = new LabelingDashboard();
});
</script>
@endsection
