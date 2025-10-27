{{--resources/views/PerencanaanBMN/Bagian/koordinator_sbsk/DashboardKoordinatorSBSK.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <!-- Pesan status -->
                <div class="col-sm-6">
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <!-- Breadcrumb -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pengajuan RKBMN SBSK</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Main -->
    <div class="content">
        <div class="container">
            <h1>[Koordinator Pengadaan] Dashboard Pengajuan RKBMN SBSK</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <!-- Font Awesome (jika memang diperlukan khusus di halaman ini) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Tabel Pengajuan -->
    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengajuan untuk Review Koordinator</h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">Total: <span id="total-count">{{ count($pengajuan) }}</span> pengajuan</span>
                    </div>
                </div>
                <div class="card-body">
                    <table id="pengajuanTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode / Jenis</th>
                                <th>Uraian Barang</th>
                                <th>Bagian Pengusul</th>
                                <th>Total Anggaran</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            <tr>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="0" placeholder="Filter kode..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="1" placeholder="Filter uraian..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="2" placeholder="Filter bagian..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="3" placeholder="Filter total..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="4" placeholder="Filter tanggal..."></th>
                                <th>
                                    <select class="form-control form-control-sm filter-input" data-column="5">
                                        <option value="">Semua Status</option>
                                        <option value="Diajukan ke Koordinator">Diajukan ke Koordinator</option>
                                        <option value="Disetujui oleh Koordinator">Disetujui oleh Koordinator</option>
                                        <option value="Ditolak oleh Koordinator">Ditolak oleh Koordinator</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $jenisMap = [
                                    'R1' => 'Gedung Kantor',
                                    'R2' => 'Pendidikan',
                                    'R3' => 'Rumah Negara',
                                    'R4' => 'Kendaraan Jabatan',
                                    'R5' => 'Kendaraan Operasional',
                                    'R6' => 'Kendaraan Fungsional'
                                ];
                            @endphp
                            @forelse($pengajuan as $p)
                                <tr>
                                    <td>
                                        <strong>SBSK-{{ $p->id }}</strong><br>
                                        <small class="text-muted">{{ $jenisMap[substr($p->kode_jenis_pengajuan, 0, 2)] ?? 'Lainnya' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $p->uraian_barang ?? '-' }}">
                                            {{ $p->uraian_barang ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}">
                                            {{ $p->bagianPengusul->uraianbagian ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($p->total_anggaran ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @php
                                            $status = $p->status;
                                            $badgeClass = '';
                                            switch($status) {
                                                case 'Diajukan ke Koordinator':
                                                    $badgeClass = 'badge-warning';
                                                    break;
                                                case 'Disetujui oleh Koordinator':
                                                    $badgeClass = 'badge-success';
                                                    break;
                                                case 'Ditolak oleh Koordinator':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                                default:
                                                    $badgeClass = 'badge-light';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('koordinator_sbsk.review', $p->id) }}" class="btn btn-info btn-sm mr-1" title="Review Pengajuan">
                                                <i class="fas fa-eye mr-1"></i>Review
                                            </a>
                                            @if($status == 'Disetujui oleh Koordinator' && $p->berita_acara_sbsk_signed_path)
                                                @php
                                                    $finalBaPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $p->berita_acara_sbsk_signed_path);
                                                @endphp
                                                @if(Storage::disk('public')->exists($finalBaPath))
                                                    <a href="{{ route('koordinator_sbsk.download_ba_final', $p->id) }}" class="btn btn-success btn-sm" title="Download Berita Acara">
                                                        <i class="fas fa-download mr-1"></i>BA
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <div class="text-muted">Belum ada pengajuan untuk direview</div>
                                        <small class="text-muted">Pengajuan akan muncul ketika ada pengajuan baru dari operator</small>
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

<style>
/* Custom classes for table striping that we'll manage with JavaScript */
#pengajuanTable tbody tr.bg-stripe-even {
    background-color: #ffffff;
}
#pengajuanTable tbody tr.bg-stripe-odd {
    background-color: rgba(0,0,0,.05);
}

/* Action buttons horizontal alignment */
#pengajuanTable td .d-flex {
    gap: 0.25rem;
}

/* Untuk browser yang tidak support gap */
#pengajuanTable td .d-flex .btn + .btn {
    margin-left: 0.25rem;
}

/* Small enhancements */
.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.card-header {
    background-color: #ffffff;
    border-bottom: 1px solid #dee2e6;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Badge styling */
.badge {
    font-size: 0.75em;
}

/* Table column widths */
#pengajuanTable th:nth-child(1),
#pengajuanTable td:nth-child(1) {
    min-width: 140px;
}

#pengajuanTable th:nth-child(2),
#pengajuanTable td:nth-child(2) {
    min-width: 200px;
}

#pengajuanTable th:nth-child(3),
#pengajuanTable td:nth-child(3) {
    min-width: 150px;
    max-width: 200px;
}

#pengajuanTable th:nth-child(4),
#pengajuanTable td:nth-child(4) {
    min-width: 120px;
}

#pengajuanTable th:nth-child(5),
#pengajuanTable td:nth-child(5) {
    min-width: 110px;
}

#pengajuanTable th:nth-child(6),
#pengajuanTable td:nth-child(6) {
    min-width: 140px;
}

#pengajuanTable th:nth-child(7),
#pengajuanTable td:nth-child(7) {
    min-width: 120px;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .d-flex .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    .text-truncate {
        max-width: 100px !important;
    }

    #pengajuanTable th,
    #pengajuanTable td {
        min-width: auto !important;
        max-width: none !important;
    }
}

/* Hover effect for rows */
#pengajuanTable tbody tr:hover {
    background-color: rgba(0,123,255,0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply initial striping to the table
    applyTableStriping();

    // Get all filter inputs
    const filterInputs = document.querySelectorAll('.filter-input');

    // Add event listener to each filter input
    filterInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            filterTable();
        });

        // For select elements, use change event
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', function() {
                filterTable();
            });
        }
    });

    // Function to apply striping to visible rows
    function applyTableStriping() {
        const table = document.getElementById('pengajuanTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let visibleCount = 0;

        for (let i = 0; i < rows.length; i++) {
            if (rows[i].style.display !== 'none') {
                // Remove any existing stripe classes
                rows[i].classList.remove('bg-stripe-even', 'bg-stripe-odd');

                // Add appropriate class based on visible count
                if (visibleCount % 2 === 0) {
                    rows[i].classList.add('bg-stripe-even');
                } else {
                    rows[i].classList.add('bg-stripe-odd');
                }

                visibleCount++;
            }
        }

        // Update total count badge
        document.getElementById('total-count').textContent = visibleCount;
    }

    function filterTable() {
        // Get table and rows
        const table = document.getElementById('pengajuanTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        // Get filter values (convert to lowercase for case-insensitive comparison)
        const filters = [];
        filterInputs.forEach(input => {
            filters[parseInt(input.getAttribute('data-column'))] = input.value.toLowerCase();
        });

        // Check each row against filters
        for (let i = 0; i < rows.length; i++) {
            let rowVisible = true;
            const cells = rows[i].getElementsByTagName('td');

            // Skip if this is the empty state row
            if (cells.length === 1 && cells[0].getAttribute('colspan')) {
                continue;
            }

            // Check each cell that has a corresponding filter
            for (let j = 0; j < cells.length - 1; j++) { // Skip the last column (Actions)
                if (filters[j] && filters[j] !== '') {
                    let cellText = '';

                    // Handle different cell contents
                    if (j === 5) { // Status column - get text from badge
                        const badge = cells[j].querySelector('.badge');
                        cellText = badge ? badge.textContent.toLowerCase() : '';
                    } else {
                        cellText = cells[j].textContent.toLowerCase();
                    }

                    if (cellText.indexOf(filters[j]) === -1) {
                        rowVisible = false;
                        break;
                    }
                }
            }

            // Show or hide the row based on filter result
            rows[i].style.display = rowVisible ? '' : 'none';
        }

        // After filtering, reapply striping to maintain the pattern
        applyTableStriping();
    }
});
</script>
@endsection
