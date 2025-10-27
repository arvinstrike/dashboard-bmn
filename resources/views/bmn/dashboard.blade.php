<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengajuan BMN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-primary-rgb: 60, 90, 255; /* A modern blue */
            --bs-body-bg: #f0f2f5; /* Light grey background */
            --bs-body-font-family: 'Inter', sans-serif;
            --bs-card-border-radius: .75rem;
            --bs-card-box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: none;
            box-shadow: var(--bs-card-box-shadow);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table {
            --bs-table-striped-bg: #f8f9fc;
        }
        .table th {
            font-weight: 600;
            color: #667085;
        }
        .table td {
            vertical-align: middle;
        }

        .badge-status {
            font-size: .8rem;
            font-weight: 600;
            padding: .4em .8em;
        }
        
        .pagination .page-link {
            color: rgb(var(--bs-primary-rgb));
        }
        .pagination .page-item.active .page-link {
            background-color: rgb(var(--bs-primary-rgb));
            border-color: rgb(var(--bs-primary-rgb));
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container-fluid p-4">
        <h1 class="mb-4">Dashboard Pengajuan RKBMN</h1>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel me-2"></i>Filter Pengajuan
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bmn.dashboard') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="bagian" class="form-label fw-bold">Bagian Pengusul</label>
                        <select name="bagian" id="bagian" class="form-select">
                            <option value="">Semua Bagian</option>
                            @foreach($bagians as $bagian)
                                <option value="{{ $bagian->id_bagian_pengusul }}" {{ ($filters['bagian'] ?? '') == $bagian->id_bagian_pengusul ? 'selected' : '' }}>
                                    Bagian {{ $bagian->id_bagian_pengusul }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->status }}" {{ ($filters['status'] ?? '') == $status->status ? 'selected' : '' }}>
                                    {{ ucfirst($status->status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="anggaran_min" class="form-label fw-bold">Anggaran Min.</label>
                        <input type="number" name="anggaran_min" id="anggaran_min" class="form-control" placeholder="Rp 1.000.000" value="{{ $filters['anggaran_min'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="anggaran_max" class="form-label fw-bold">Anggaran Maks.</label>
                        <input type="number" name="anggaran_max" id="anggaran_max" class="form-control" placeholder="Rp 50.000.000" value="{{ $filters['anggaran_max'] ?? '' }}">
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2"></i>Terapkan Filter</button>
                        <a href="{{ route('bmn.dashboard') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table me-2"></i>Daftar Pengajuan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Tgl Pengajuan</th>
                                <th class="p-3">Bagian</th>
                                <th class="p-3">Uraian Barang</th>
                                <th class="p-3">Tahun Anggaran</th>
                                <th class="p-3 text-end">Total Anggaran</th>
                                <th class="p-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $pengajuan)
                            <tr>
                                <td class="p-3">{{ $pengajuans->firstItem() + $loop->index }}</td>
                                <td class="p-3">{{ \Illuminate\Support\Carbon::parse($pengajuan->tanggal_pengajuan)->translatedFormat('d M Y') }}</td>
                                <td class="p-3">Bagian {{ $pengajuan->id_bagian_pengusul }}</td>
                                <td class="p-3">{{ \Illuminate\Support\Str::limit($pengajuan->uraian_barang, 50) }}</td>
                                <td class="p-3">{{ $pengajuan->tahun_anggaran }}</td>
                                <td class="p-3 text-end">Rp {{ number_format($pengajuan->total_anggaran, 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <span class="badge rounded-pill 
                                        @switch(strtolower($pengajuan->status))
                                            @case('approved') text-bg-success @break
                                            @case('completed') text-bg-primary @break
                                            @case('rejected') text-bg-danger @break
                                            @case('draft') text-bg-secondary @break
                                            @case('pending') text-bg-warning @break
                                            @case('in_progress') text-bg-info @break
                                            @default text-bg-light @break
                                        @endswitch badge-status">
                                        {{ ucfirst($pengajuan->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center p-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <h5 class="mt-3">Data Tidak Ditemukan</h5>
                                    <p class="text-muted">Coba ubah atau reset filter Anda.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pengajuans->hasPages())
            <div class="card-footer bg-white">
                {{ $pengajuans->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
