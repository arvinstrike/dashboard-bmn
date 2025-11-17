<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengajuan RKBMN Non SBSK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Alert System --}}
    @include('includes.custom-alert')
</head>
<body>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!-- Pesan status -->
                    <div class="col-sm-6">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                    <!-- Breadcrumb -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Pengajuan RKBMN Bagian Non SBSK</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Main -->
        <div class="content">
            <div class="container-fluid">
                <h1>[PERENCANAAN] Dashboard Pengajuan RKBMN Non SBSK</h1>
                <p><strong>Tahun Anggaran:</strong> {{ $tahunanggaran }}</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
        </div>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

        @php
            // Define unique statuses untuk filter tabel pertama
            $uniqueStatusesMenungguEsign = $pengajuanMenungguEsignKepala->pluck('status_pengajuan')->unique()->sort();
        @endphp

        <!-- Tabel Pengajuan menunggu esign oleh Kepala Bagian -->
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">
                            <i class="fas fa-hourglass-half mr-2"></i>Pengajuan menunggu esign oleh Kepala Bagian
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light">Total: {{ count($pengajuanMenungguEsignKepala) }} pengajuan</span>
                        </div>
                    </div>

                    <!-- Filter Section (Outside of table) -->
                    <div class="card-body pb-2">
                        <div class="filter-section">
                            <h6 class="text-muted mb-2"><i class="fas fa-filter mr-1"></i>Filter & Pencarian:</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="small text-muted">Kode Pengajuan:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableMenungguEsign" data-column="0"
                                           placeholder="Filter kode...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Total Nilai:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableMenungguEsign" data-column="1"
                                           placeholder="Filter nilai...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Status:</label>
                                    <select class="form-control form-control-sm external-filter"
                                            data-table="pengajuanTableMenungguEsign" data-column="2">
                                        <option value="">Semua Status</option>
                                        @foreach($uniqueStatusesMenungguEsign as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pengusul:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableMenungguEsign" data-column="3"
                                           placeholder="Filter bagian...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pelaksana:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableMenungguEsign" data-column="4"
                                           placeholder="Filter pelaksana...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Aksi:</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-block clear-filters"
                                            data-table="pengajuanTableMenungguEsign">
                                        <i class="fas fa-times mr-1"></i>Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="pengajuanTableMenungguEsign" class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode Pengajuan</th>
                                        <th>Total Nilai</th>
                                        <th>Status</th>
                                        <th>Bagian Pengusul</th>
                                        <th>Bagian Pelaksana</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengajuanMenungguEsignKepala as $p)
                                        <tr class="{{ $loop->even ? 'bg-stripe-even' : 'bg-stripe-odd' }}">
                                            <td>
                                                <div style="min-width: 80px;">
                                                    {{ $p->kode_pengajuan }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    Rp {{ number_format($p->total_nilai, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 120px;">
                                                    <span class="badge badge-block
                                                        @if($p->status_pengajuan == 'Draft') badge-secondary
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Pelaksana') badge-primary
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Koordinator') badge-info
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Perencanaan') badge-warning
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Perencanaan dengan Rekomendasi') badge-warning
                                                        @elseif($p->status_pengajuan == 'Disetujui') badge-success
                                                        @elseif(str_contains($p->status_pengajuan, 'Ditolak')) badge-danger
                                                        @else badge-secondary
                                                        @endif">
                                                        {{ $p->status_pengajuan }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    {{ $p->created_at instanceof \Carbon\Carbon ? $p->created_at->format('d/m/Y H:i') : $p->created_at }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm d-sm-flex flex-sm-row" style="min-width: 180px;">
                                                    {{-- Button Review (tetap ada) --}}
                                                    <a href="{{ route('perencanaan.review', $p->id) }}"
                                                        class="btn btn-info btn-sm mb-1 mb-sm-0 mr-sm-1" title="Review Pengajuan">
                                                        <i class="fas fa-eye"></i><span class="d-none d-lg-inline ml-1">Review</span>
                                                    </a>

                                                    {{-- BUTTON VERIFIKASI - HANYA UNTUK KEPALA BAGIAN PERENCANAAN DI TABEL INI --}}
                                                    @php
                                                        // Check apakah user yang login adalah kepala bagian perencanaan (eselon III, satker 657)
                                                        // UNTUK TESTING: comment baris query di bawah, uncomment baris testing
                                                        $isKepalaBagianPerencanaan = DB::table('pegawai')
                                                            ->where('nama', Auth::user()->nama)
                                                            ->where('eselon', 'III')
                                                            ->where('id_satker', 657)
                                                            ->exists();

                                                        // UNTUK TESTING - UNCOMMENT BARIS DI BAWAH:
                                                        // $isKepalaBagianPerencanaan = true;

                                                        // Button verifikasi hanya muncul untuk status yang sudah diajukan ke unit perencanaan
                                                        // dan berada di tabel "menunggu esign kepala bagian"
                                                        $showVerifikasiButton = $isKepalaBagianPerencanaan &&
                                                                               in_array($p->status_pengajuan, ['Diajukan ke Unit Perencanaan', 'Diajukan ke Unit Perencanaan dengan Rekomendasi']) &&
                                                                               is_null($p->berita_acara_perencanaan_signed_date); // Belum ditandatangani oleh perencanaan
                                                    @endphp

                                                    @if($showVerifikasiButton)
                                                        <button type="button"
                                                                class="btn btn-success btn-sm mb-1 mb-sm-0 mr-sm-1 verifikasi-button"
                                                                title="Verifikasi E-Sign Berita Acara"
                                                                data-id="{{ $p->id }}"
                                                                data-toggle="modal"
                                                                data-target="#verifikasiModal">
                                                            <i class="fas fa-signature"></i><span class="d-none d-lg-inline ml-1">Verifikasi</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                                <div class="text-muted">Tidak ada pengajuan yang menunggu e-sign</div>
                                                <small class="text-muted">Semua pengajuan telah di e-sign</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pengajuan Belum Di E-Sign -->
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2"></i>Pengajuan Belum Di E-Sign
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light">Total: {{ count($pengajuanBelumSign) }} pengajuan</span>
                        </div>
                    </div>

                    <!-- Filter Section untuk Tabel 2 -->
                    <div class="card-body pb-2">
                        <div class="filter-section">
                            <h6 class="text-muted mb-2"><i class="fas fa-filter mr-1"></i>Filter & Pencarian:</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="small text-muted">Kode Pengajuan:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableBelumSign" data-column="0"
                                           placeholder="Filter kode...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Total Nilai:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableBelumSign" data-column="1"
                                           placeholder="Filter nilai...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Status:</label>
                                    <select class="form-control form-control-sm external-filter"
                                            data-table="pengajuanTableBelumSign" data-column="2">
                                        <option value="">Semua Status</option>
                                        @foreach($uniqueStatusesBelumSign as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pengusul:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableBelumSign" data-column="3"
                                           placeholder="Filter bagian...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pelaksana:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableBelumSign" data-column="4"
                                           placeholder="Filter pelaksana...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Aksi:</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-block clear-filters"
                                            data-table="pengajuanTableBelumSign">
                                        <i class="fas fa-times mr-1"></i>Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="pengajuanTableBelumSign" class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode Pengajuan</th>
                                        <th>Total Nilai</th>
                                        <th>Status</th>
                                        <th>Bagian Pengusul</th>
                                        <th>Bagian Pelaksana</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengajuanBelumSign as $p)
                                        <tr class="{{ $loop->even ? 'bg-stripe-even' : 'bg-stripe-odd' }}">
                                            <td>
                                                <div style="min-width: 80px;">
                                                    {{ $p->kode_pengajuan }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    Rp {{ number_format($p->total_nilai, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 120px;">
                                                    <span class="badge badge-block
                                                        @if($p->status_pengajuan == 'Draft') badge-secondary
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Pelaksana') badge-primary
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Koordinator') badge-info
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Perencanaan') badge-warning
                                                        @elseif($p->status_pengajuan == 'Diajukan ke Unit Perencanaan dengan Rekomendasi') badge-warning
                                                        @elseif($p->status_pengajuan == 'Disetujui') badge-success
                                                        @elseif(str_contains($p->status_pengajuan, 'Ditolak')) badge-danger
                                                        @else badge-secondary
                                                        @endif">
                                                        {{ $p->status_pengajuan }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    {{ $p->created_at instanceof \Carbon\Carbon ? $p->created_at->format('d/m/Y H:i') : $p->created_at }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm d-sm-flex flex-sm-row" style="min-width: 140px;">
                                                    <a href="{{ route('perencanaan.review', $p->id) }}"
                                                        class="btn btn-info btn-sm mb-1 mb-sm-0 mr-sm-1" title="Review Pengajuan">
                                                        <i class="fas fa-eye"></i><span class="d-none d-lg-inline ml-1">Review</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                                                <div class="text-muted">Belum ada pengajuan yang perlu di-review</div>
                                                <small class="text-muted">Pengajuan baru akan muncul di sini</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pengajuan Sudah Di E-Sign -->
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle mr-2"></i>Pengajuan Sudah Di E-Sign
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light">Total: {{ count($pengajuanSudahSign) }} pengajuan</span>
                        </div>
                    </div>

                    <!-- Filter Section untuk Tabel 3 -->
                    <div class="card-body pb-2">
                        <div class="filter-section">
                            <h6 class="text-muted mb-2"><i class="fas fa-filter mr-1"></i>Filter & Pencarian:</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="small text-muted">Kode Pengajuan:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableSudahSign" data-column="0"
                                           placeholder="Filter kode...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Total Nilai:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableSudahSign" data-column="1"
                                           placeholder="Filter nilai...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Status:</label>
                                    <select class="form-control form-control-sm external-filter"
                                            data-table="pengajuanTableSudahSign" data-column="2">
                                        <option value="">Semua Status</option>
                                        @foreach($uniqueStatusesSudahSign as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pengusul:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableSudahSign" data-column="3"
                                           placeholder="Filter bagian...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Bagian Pelaksana:</label>
                                    <input type="text" class="form-control form-control-sm external-filter"
                                           data-table="pengajuanTableSudahSign" data-column="4"
                                           placeholder="Filter pelaksana...">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted">Aksi:</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-block clear-filters"
                                            data-table="pengajuanTableSudahSign">
                                        <i class="fas fa-times mr-1"></i>Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="pengajuanTableSudahSign" class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode Pengajuan</th>
                                        <th>Total Nilai</th>
                                        <th>Status</th>
                                        <th>Bagian Pengusul</th>
                                        <th>Bagian Pelaksana</th>
                                        <th>Tanggal E-Sign</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengajuanSudahSign as $p)
                                        <tr class="{{ $loop->even ? 'bg-stripe-even' : 'bg-stripe-odd' }}">
                                            <td>
                                                <div style="min-width: 80px;">
                                                    {{ $p->kode_pengajuan }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    Rp {{ number_format($p->total_nilai, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 120px;">
                                                    <span class="badge badge-block badge-success">
                                                        {{ $p->status_pengajuan }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="min-width: 120px; max-width: 150px;"
                                                    title="{{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}">
                                                    {{ $p->bagianPelaksana->uraianbagian ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width: 90px;">
                                                    {{ $p->berita_acara_perencanaan_signed_date && $p->berita_acara_perencanaan_signed_date instanceof \Carbon\Carbon ? $p->berita_acara_perencanaan_signed_date->format('d/m/Y H:i') : ($p->berita_acara_perencanaan_signed_date ?: '-') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm d-sm-flex flex-sm-row" style="min-width: 140px;">
                                                    <a href="{{ route('perencanaan.review', $p->id) }}"
                                                        class="btn btn-info btn-sm mb-1 mb-sm-0 mr-sm-1" title="Review Pengajuan">
                                                        <i class="fas fa-eye"></i><span class="d-none d-lg-inline ml-1">Review</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                                                <div class="text-muted">Belum ada pengajuan yang sudah di e-sign</div>
                                                <small class="text-muted">Pengajuan yang sudah di e-sign akan muncul di sini</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi E-Sign -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1" role="dialog" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="verifikasiModalLabel">
                        <i class="fas fa-signature mr-2"></i> Verifikasi Dokumen - Unit Perencanaan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Verifikasi Dokumen:</strong> Silakan review semua dokumen di bawah ini sebelum melakukan tanda tangan elektronik.
                            </div>

                            <!-- Informasi Pengajuan -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Informasi Pengajuan</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Nomor Pengajuan:</strong> <span id="modal-nomor-pengajuan"></span></small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Status:</strong> <span id="modal-status"></span></small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Bagian Pengusul:</strong> <span id="modal-bagian-pengusul"></span></small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Total Nilai:</strong> <span id="modal-total-nilai"></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Dokumen -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-eye mr-2"></i>Preview Dokumen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Catatan:</strong> Unit Perencanaan hanya menandatangani <strong>Berita Acara</strong>.
                                        Dokumen lain hanya untuk review/preview.
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-warning btn-sm btn-block preview-doc-btn"
                                                    data-doc-type="berita_acara">
                                                <i class="fas fa-signature mr-1"></i> Preview Berita Acara (Akan Ditandatangani)
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block preview-doc-btn"
                                                    data-doc-type="tor">
                                                <i class="fas fa-file-contract mr-1"></i> Preview TOR (Review Only)
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block preview-doc-btn"
                                                    data-doc-type="lampiran">
                                                <i class="fas fa-paperclip mr-1"></i> Preview Lampiran (Review Only)
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block preview-doc-btn"
                                                    data-doc-type="dokumen_pendukung">
                                                <i class="fas fa-folder mr-1"></i> Preview Dokumen Pendukung (Review Only)
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Area Preview Dokumen -->
                                    <div class="mt-3">
                                        <iframe id="document-preview" src="" style="width: 100%; height: 400px; border: 1px solid #ddd; display: none;"></iframe>
                                        <div id="preview-loading" style="display: none; text-align: center; padding: 50px;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Memuat preview dokumen...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Passphrase -->
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-key mr-2"></i>Tanda Tangan Elektronik - Berita Acara</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-3">
                                        <i class="fas fa-signature mr-1"></i>
                                        <strong>Yang akan ditandatangani:</strong> Hanya <strong>Berita Acara</strong> saja.
                                        <br>Dokumen lain (TOR, Lampiran, dll) hanya untuk review dan tidak perlu ditandatangani oleh Unit Perencanaan.
                                    </div>

                                    <form id="verifikasi-form">
                                        <div class="form-group">
                                            <label for="passphrase-input"><i class="fas fa-lock mr-1"></i> Passphrase:</label>
                                            <input type="password" id="passphrase-input" class="form-control"
                                                   placeholder="Masukkan passphrase untuk tanda tangan berita acara" required>
                                            <small class="form-text text-muted">
                                                Passphrase diperlukan untuk menandatangani berita acara secara elektronik.
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="confirm-verification">
                                                <label class="custom-control-label" for="confirm-verification">
                                                    Saya telah memeriksa semua dokumen dan menyetujui untuk menandatangani berita acara secara elektronik
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirm-verification-button" disabled>
                        <i class="fas fa-signature mr-1"></i> Tandatangani Berita Acara
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS Styling -->
    <style>
        /* Filter section styling */
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .external-filter {
            margin-bottom: 5px;
        }

        .external-filter:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Enhanced responsive table styling */
        .table-responsive {
            border-radius: 0.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, .05);
        }

        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        /* Table header styling */
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Badge styling improvements */
        .badge-block {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        /* Button group improvements */
        .btn-group-vertical .btn,
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
        }

        /* Custom classes for table striping */
        .table tbody tr.bg-stripe-even {
            background-color: #ffffff;
        }

        .table tbody tr.bg-stripe-odd {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Card styling */
        .card-header {
            border-bottom: 1px solid #dee2e6;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-wrap {
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Hover effect for rows */
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        /* Space between tables */
        .content + .content {
            margin-top: 2rem;
        }

        /* Header colors */
        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .bg-success {
            background-color: #28a745 !important;
            color: #fff;
        }

        .bg-info {
            background-color: #17a2b8 !important;
            color: #fff;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.3rem;
            }

            .btn-group-vertical .btn {
                margin-bottom: 0.2rem;
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }

            .badge-block {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }

            /* Stack buttons vertically on mobile */
            .btn-group-vertical .btn {
                display: block;
                width: 100%;
            }

            .filter-section .col-md-2 {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 576px) {
            .table th,
            .table td {
                padding: 0.25rem;
            }

            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .filter-section {
                padding: 10px;
            }
        }

        /* Loading states */
        .btn.loading {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        // Ganti bagian JavaScript yang ada dengan kode di bawah ini:

    $(document).ready(function() {
        let currentPengajuanId = null;

        // Enhanced DataTables configuration
        const tableOptions = {
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json",
                "emptyTable": "Tidak ada data yang tersedia",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "initComplete": function(settings, json) {
                console.log('DataTable initialized:', settings.sTableId);
            }
        };

        // Initialize DataTables dan simpan reference
        const dataTablesInstances = {
            'pengajuanTableMenungguEsign': null,
            'pengajuanTableBelumSign': null,
            'pengajuanTableSudahSign': null
        };

        // Initialize each table
        try {
            dataTablesInstances['pengajuanTableMenungguEsign'] = $('#pengajuanTableMenungguEsign').DataTable(tableOptions);
            console.log('Table 1 initialized successfully');
        } catch (error) {
            console.error('Error initializing table 1:', error);
        }

        try {
            dataTablesInstances['pengajuanTableBelumSign'] = $('#pengajuanTableBelumSign').DataTable(tableOptions);
            console.log('Table 2 initialized successfully');
        } catch (error) {
            console.error('Error initializing table 2:', error);
        }

        try {
            dataTablesInstances['pengajuanTableSudahSign'] = $('#pengajuanTableSudahSign').DataTable(tableOptions);
            console.log('Table 3 initialized successfully');
        } catch (error) {
            console.error('Error initializing table 3:', error);
        }

        // Custom search function untuk exact match status
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // Get table ID
            const tableId = settings.sTableId;

            // Get filter value for status column (column index 2)
            const statusFilter = $(`.external-filter[data-table="${tableId}"][data-column="2"]`).val();

            // Jika tidak ada filter status atau "Semua Status", tampilkan semua
            if (!statusFilter || statusFilter === '') {
                return true;
            }

            // Get raw HTML dari kolom status (index 2)
            const statusHtml = data[2] || '';

            // Extract text dari HTML badge menggunakan jQuery
            const $tempDiv = $('<div>').html(statusHtml);
            const statusText = $tempDiv.find('.badge').text().trim() || $tempDiv.text().trim();

            console.log('Comparing:', statusText, 'vs', statusFilter);

            // Exact match comparison
            return statusText === statusFilter;
        });

        // Standard external filter functionality untuk kolom selain status
        $(document).on('keyup change', '.external-filter', function() {
            const tableId = $(this).data('table');
            const columnIndex = $(this).data('column');
            const value = this.value;

            console.log(`Filter applied - Table: ${tableId}, Column: ${columnIndex}, Value: ${value}`);

            try {
                if (dataTablesInstances[tableId]) {
                    // Untuk kolom status (index 2), gunakan custom search function
                    if (columnIndex == 2) {
                        // Trigger custom search dengan redraw
                        dataTablesInstances[tableId].draw();
                    } else {
                        // Untuk kolom lain gunakan search biasa
                        dataTablesInstances[tableId].column(columnIndex).search(value).draw();
                    }
                    console.log(`Filter applied successfully to ${tableId}`);
                } else {
                    console.error(`DataTable instance not found for ${tableId}`);
                }
            } catch (error) {
                console.error('Error applying filter:', error);
            }
        });

        // Clear filters functionality - enhanced
        $(document).on('click', '.clear-filters', function() {
            const tableId = $(this).data('table');

            console.log(`Clearing filters for table: ${tableId}`);

            try {
                // Clear all filter inputs for this table
                $(`.external-filter[data-table="${tableId}"]`).val('');

                // Clear all searches in DataTable
                if (dataTablesInstances[tableId]) {
                    dataTablesInstances[tableId].search('').columns().search('').draw();
                    console.log(`Filters cleared successfully for ${tableId}`);
                } else {
                    console.error(`DataTable instance not found for ${tableId}`);
                }
            } catch (error) {
                console.error('Error clearing filters:', error);
            }
        });

        // Handle click button verifikasi dengan improved error handling
        $(document).on('click', '.verifikasi-button', function() {
            currentPengajuanId = $(this).data('id');
            console.log('Verifikasi button clicked for ID:', currentPengajuanId);

            // Reset form
            $('#passphrase-input').val('');
            $('#confirm-verification').prop('checked', false);
            $('#confirm-verification-button').prop('disabled', true);
            $('#document-preview').hide();
            $('#preview-loading').hide();

            // Load informasi pengajuan ke modal
            loadPengajuanInfo(currentPengajuanId);
        });

        // Handle checkbox confirmation
        $('#confirm-verification').on('change', function() {
            const isChecked = $(this).is(':checked');
            const hasPassphrase = $('#passphrase-input').val().length > 0;
            $('#confirm-verification-button').prop('disabled', !(isChecked && hasPassphrase));
        });

        // Handle passphrase input
        $('#passphrase-input').on('input', function() {
            const hasPassphrase = $(this).val().length > 0;
            const isConfirmed = $('#confirm-verification').is(':checked');
            $('#confirm-verification-button').prop('disabled', !(hasPassphrase && isConfirmed));
        });

        // Handle preview dokumen
        $(document).on('click', '.preview-doc-btn', function() {
            const docType = $(this).data('doc-type');
            if (!currentPengajuanId) {
                console.error('No current pengajuan ID');
                return;
            }

            console.log('Preview document:', docType, 'for ID:', currentPengajuanId);

            $('#document-preview').hide();
            $('#preview-loading').show();

            let previewUrl = '';
            switch(docType) {
                case 'berita_acara':
                    previewUrl = `/perencanaan_bmn/${currentPengajuanId}/preview-berita-acara`;
                    break;
                case 'tor':
                    previewUrl = `/perencanaan_bmn/${currentPengajuanId}/preview-tor`;
                    break;
                case 'lampiran':
                    previewUrl = `/perencanaan_bmn/${currentPengajuanId}/preview-lampiran`;
                    break;
                case 'dokumen_pendukung':
                    previewUrl = `/perencanaan_bmn/${currentPengajuanId}/preview-dokumen-pendukung`;
                    break;
                default:
                    alert('Tipe dokumen tidak dikenali');
                    $('#preview-loading').hide();
                    return;
            }

            $('#document-preview').attr('src', previewUrl);
            $('#document-preview').on('load', function() {
                $('#preview-loading').hide();
                $('#document-preview').show();
                console.log('Document preview loaded successfully');
            });
        });

        $('#document-preview').on('error', function() {
            $('#preview-loading').hide();
            console.error('Failed to load document preview');
            alert('Gagal memuat preview dokumen. File mungkin tidak tersedia.');
        });

        // Handle konfirmasi verifikasi
        $('#confirm-verification-button').on('click', function() {
            if (!currentPengajuanId) {
                console.error('No current pengajuan ID');
                return;
            }

            const passphrase = $('#passphrase-input').val();
            if (!passphrase) {
                alert('Harap masukkan passphrase');
                return;
            }

            console.log('Starting verification process for ID:', currentPengajuanId);

            // Disable button dan show loading
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

            // Kirim request verifikasi
            $.ajax({
                url: `/perencanaan_bmn/${currentPengajuanId}/verifikasi-esign`,
                method: 'POST',
                data: {
                    passphrase: passphrase,
                    documents: ['berita_acara'], // Hanya berita acara yang ditandatangani oleh perencanaan
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Verification response:', response);
                    if (response.success) {
                        alert('Verifikasi berhasil! Berita acara telah ditandatangani.');
                        $('#verifikasiModal').modal('hide');
                        // Refresh halaman atau update UI
                        location.reload();
                    } else {
                        alert('Verifikasi gagal: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Verification error:', xhr, status, error);
                    let errorMessage = 'Terjadi kesalahan sistem';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMessage);
                },
                complete: function() {
                    $('#confirm-verification-button').prop('disabled', false).html('<i class="fas fa-signature mr-1"></i> Tandatangani Berita Acara');
                }
            });
        });

        // Function untuk load informasi pengajuan
        function loadPengajuanInfo(pengajuanId) {
            try {
                // Ambil data dari tabel menunggu esign (tabel pertama)
                const row = $(`#pengajuanTableMenungguEsign .verifikasi-button[data-id="${pengajuanId}"]`).closest('tr');

                if (row.length === 0) {
                    console.error('Row not found for pengajuan ID:', pengajuanId);
                    return;
                }

                $('#modal-nomor-pengajuan').text(row.find('td:eq(0)').text().trim());
                $('#modal-total-nilai').text(row.find('td:eq(1)').text().trim());

                // Untuk status, ambil text dari badge
                const statusBadge = row.find('td:eq(2) .badge');
                $('#modal-status').text(statusBadge.length ? statusBadge.text().trim() : row.find('td:eq(2)').text().trim());

                $('#modal-bagian-pengusul').text(row.find('td:eq(3)').text().trim());

                console.log('Pengajuan info loaded successfully');
            } catch (error) {
                console.error('Error loading pengajuan info:', error);
            }
        }

        // Debug functions
        window.debugDataTables = function() {
            console.log('=== DataTables Debug Info ===');
            Object.keys(dataTablesInstances).forEach(tableId => {
                const instance = dataTablesInstances[tableId];
                console.log(`${tableId}:`, instance ? 'Initialized' : 'Not initialized');
                if (instance) {
                    console.log(`  - Rows: ${instance.rows().count()}`);
                    console.log(`  - Columns: ${instance.columns().count()}`);
                }
            });
        };

        window.testStatusFilter = function(tableId, statusValue) {
            console.log(`Testing status filter - Table: ${tableId}, Status: ${statusValue}`);
            $(`.external-filter[data-table="${tableId}"][data-column="2"]`).val(statusValue).trigger('change');
        };

        window.debugStatusColumn = function(tableId) {
            console.log('=== Status Column Debug ===');
            if (dataTablesInstances[tableId]) {
                const data = dataTablesInstances[tableId].rows().data();
                for (let i = 0; i < Math.min(5, data.length); i++) {
                    const statusHtml = data[i][2];
                    const $tempDiv = $('<div>').html(statusHtml);
                    const statusText = $tempDiv.find('.badge').text().trim();
                    console.log(`Row ${i}: HTML="${statusHtml}" -> Text="${statusText}"`);
                }
            }
        };

        // Initialize complete message
        console.log('Dashboard initialization complete with HTML-aware exact match filter');
        console.log('Available debug functions: debugDataTables(), testStatusFilter(tableId, status), debugStatusColumn(tableId)');
    });
    </script>

    {{-- Session-based Alerts --}}
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
</body>
</html>
