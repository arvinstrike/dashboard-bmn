<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengajuan BMN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Custom Alert System --}}
    @include('includes.custom-alert')

    <style>
        :root {
            --bs-primary-rgb: 79, 70, 229;
            /* Indigo */
            --bs-body-font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #F7F9FC;
        }

        /* Minimalist Stat Card */
        .stat-card {
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 1.5rem;
            background-color: #fff;
            transition: all 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-card .stat-title {
            font-weight: 500;
            color: #6B7280;
            /* Muted gray */
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .stat-card .stat-title .bi {
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            /* Consistent font size */
            font-weight: 600;
            color: #111827;
            margin-top: 0.25rem;
        }

        /* Highlight with Border */
        .stat-card-highlight-border {
            border-left: 5px solid rgb(var(--bs-primary-rgb));
        }

        .stat-card-highlight-border .stat-title .bi,
        .stat-card-highlight-border .stat-value {
            color: rgb(var(--bs-primary-rgb));
        }

        /* Icon Colors */
        .text-primary-dark {
            color: #2563eb !important;
        }

        .text-warning-dark {
            color: #ea580c !important;
        }

        .text-success-dark {
            color: #059669 !important;
        }

        .text-danger-dark {
            color: #dc3545 !important;
        }

        .text-gray-dark {
            color: #6B7280 !important;
        }

        /* Filter Section */
        .filter-header a {
            text-decoration: none;
            color: inherit;
        }

        .filter-header .chevron-icon {
            transition: transform 0.3s ease;
        }

        .filter-header a[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }

        /*
          Dropdown Fix: This is a persistent issue where an ancestor element
          is clipping the native <select> dropdowns. This set of rules attempts
          to override any 'overflow: hidden' property on all potential parent
          containers within the filter card.
        */
        .card.mb-4,
        .card.mb-4 .collapse,
        .card.mb-4 .card-body {
            overflow: visible !important;
        }

        /* Table Styles */
        .table-custom {
            --bs-table-striped-bg: #F9FAFB;
            --bs-table-border-color: #E5E7EB;
        }

        .table-custom thead th {
            font-weight: 600;
            color: #6B7280;
            background-color: #F9FAFB;
            border-bottom-width: 1px;
            font-size: 0.875rem;
        }

        .table-custom tbody tr:hover {
            background-color: #F3F4F6;
        }

        .table-custom td {
            vertical-align: middle;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        /* Badge Styles */
        .badge-status {
            font-size: .8rem;
            font-weight: 500;
            padding: .4em .8em;
            border-radius: 9999px;
        }

        .badge-green {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-red {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .badge-yellow {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-blue {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .badge-gray {
            background-color: #F3F4F6;
            color: #374151;
        }

        .barang-kode {
            font-family: monospace;
            font-size: 0.9em;
            color: #1d2939;
            font-weight: 500;
        }

        .barang-uraian {
            font-size: 0.85em;
            color: #667085;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-4 p-lg-5">

        <h1 class="h2 fw-bold mb-4" style="color: rgb(var(--bs-primary-rgb));">Dashboard Pengajuan RKBMN</h1>

        <!-- Stat Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4 mb-4">
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-journal-text text-primary-dark"></i>
                        Total Pengajuan
                    </p>
                    <p class="stat-value mb-0">{{ $stats['total_pengajuan'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-clock-history text-warning-dark"></i>
                        Menunggu Persetujuan
                    </p>
                    <p class="stat-value mb-0">{{ $stats['menunggu_persetujuan'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-check2-circle text-success-dark"></i>
                        Disetujui
                    </p>
                    <p class="stat-value mb-0">{{ $stats['disetujui'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i
                            class="bi bi-x-circle @if ($stats['ditolak'] > 0) text-danger-dark @else text-gray-dark @endif"></i>
                        Ditolak
                    </p>
                    <p class="stat-value mb-0">{{ $stats['ditolak'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card stat-card-highlight-border h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-wallet2"></i>
                        Anggaran Disetujui
                    </p>
                    <p class="stat-value mb-0">
                        @php
                            $anggaran = $stats['anggaran_disetujui'];
                            if ($anggaran >= 1000000000000) {
                                echo 'Rp ' . number_format($anggaran / 1000000000000, 2, ',', '.') . 'T';
                            } elseif ($anggaran >= 1000000000) {
                                echo 'Rp ' . number_format($anggaran / 1000000000, 2, ',', '.') . 'M';
                            } else {
                                echo 'Rp ' . number_format($anggaran / 1000000, 2, ',', '.') . 'JT';
                            }
                        @endphp
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header filter-header">
                <a href="#filterCollapse" data-bs-toggle="collapse" role="button" aria-expanded="true"
                    aria-controls="filterCollapse" class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-funnel me-2"></i>Filter Pengajuan</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </a>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body p-4">
                    <form action="{{ route('bmn.dashboard') }}" method="GET" class="row g-3">
                        <!-- Filter fields here -->
                        <div class="col-md-4">
                            <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan</label>
                            <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-select">
                                <option value="">Semua Jenis</option>
                                @foreach ($jenisPengajuanOptions as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ ($filters['jenis_pengajuan'] ?? '') == $k ? 'selected' : '' }}>{{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="bagian" class="form-label">Bagian Pengusul</label>
                            <select name="bagian" id="bagian" class="form-select">
                                <option value="">Semua Bagian</option>
                                @foreach ($bagianOptions as $b)
                                    <option value="{{ $b->uraianbagian }}"
                                        {{ ($filters['bagian'] ?? '') == $b->uraianbagian ? 'selected' : '' }}>
                                        {{ $b->uraianbagian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $s)
                                    <option value="{{ $s->status }}"
                                        {{ ($filters['status'] ?? '') == $s->status ? 'selected' : '' }}>
                                        {{ ucfirst($s->status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tahun_anggaran" class="form-label">Tahun Anggaran</label>
                            <select name="tahun_anggaran" id="tahun_anggaran" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunAnggaranOptions as $t)
                                    <option value="{{ $t->tahun_anggaran }}"
                                        {{ ($filters['tahun_anggaran'] ?? '') == $t->tahun_anggaran ? 'selected' : '' }}>
                                        {{ $t->tahun_anggaran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="anggaran_min" class="form-label">Anggaran Min.</label>
                            <input type="number" name="anggaran_min" id="anggaran_min" class="form-control"
                                value="{{ $filters['anggaran_min'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="anggaran_max" class="form-label">Anggaran Maks.</label>
                            <input type="number" name="anggaran_max" id="anggaran_max" class="form-control"
                                value="{{ $filters['anggaran_max'] ?? '' }}">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Terapkan
                                Filter</button>
                            <a href="{{ route('bmn.dashboard') }}" class="btn btn-outline-secondary"><i
                                    class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table me-2"></i>Daftar Pengajuan
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Tgl Pengajuan</th>
                            <th class="p-3">Bagian</th>
                            <th class="p-3">Jenis Pengajuan</th>
                            <th class="p-3">Barang</th>
                            <th class="p-3">Tahun Anggaran</th>
                            <th class="p-3 text-end">Total Anggaran</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $pengajuan)
                            <tr>
                                <td class="p-3 text-muted">{{ $pengajuans->firstItem() + $loop->index }}</td>
                                <td class="p-3">
                                    {{ \Illuminate\Support\Carbon::parse($pengajuan->tanggal_pengajuan)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-3">
                                    {{ $pengajuan->nama_bagian_pengusul }}
                                </td>
                                <td class="p-3">@php
                                    $jenis = substr($pengajuan->kode_jenis_pengajuan, 0, 2);
                                    echo $jenisPengajuanOptions[$jenis] ?? '-';
                                @endphp</td>
                                <td class="p-3">
                                    <div class="barang-kode">{{ $pengajuan->kode_barang }}</div>
                                    <div class="barang-uraian">
                                        {{ \Illuminate\Support\Str::limit($pengajuan->uraian_barang, 70) }}</div>
                                </td>
                                <td class="p-3">{{ $pengajuan->tahun_anggaran }}</td>
                                <td class="p-3 text-end fw-semibold">Rp
                                    {{ number_format($pengajuan->total_anggaran, 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <span
                                        class="badge-status @switch(strtolower($pengajuan->status)) @case('approved') @case('completed') badge-green @break @case('rejected') badge-red @break @case('pending') @case('in_progress') badge-yellow @break @default badge-gray @endswitch">
                                        {{ ucfirst($pengajuan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center p-5"><i
                                        class="bi bi-inbox fs-1 text-muted"></i>
                                    <h5 class="mt-3">Data Tidak Ditemukan</h5>
                                    <p class="text-muted">Coba ubah atau reset filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pengajuans->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <div><small class="text-muted">Menampilkan {{ $pengajuans->firstItem() }} sampai
                            {{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }} data</small></div>
                    <div>{{ $pengajuans->appends(request()->query())->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
    @vite('resources/js/app.js')
</body>

</html>
