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
                        <li class="breadcrumb-item active">Pengajuan RKBMN Bagian Reguler</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Main -->
    <div class="content">
        <div class="container">
            <h1>Dashboard Pengajuan Reguler</h1>
            {{-- <p><strong>Tahun Anggaran:</strong> {{ $tahunanggaran }}</p> --}}
            <a href="{{ route('pengajuan.reguler.create') }}" class="btn btn-primary mb-3" id="createPengajuanBtn">
                <i class="fas fa-plus mr-2"></i>Tambah Data Pengajuan
            </a>
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
                    <h3 class="card-title">Daftar Pengajuan Reguler</h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">Total: {{ count($pengajuan) }} pengajuan</span>
                    </div>
                </div>
                <div class="card-body">
                    <table id="pengajuanTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode Pengajuan</th>
                                <th>Tahun Anggaran</th>
                                <th>Tipe Pengajuan</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                            <tr>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="0" placeholder="Filter kode..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="1" placeholder="Filter tahun..."></th>
                                <th>
                                    <select class="form-control form-control-sm filter-input" data-column="2">
                                        <option value="">Semua Tipe</option>
                                        <option value="Revisi">Revisi</option>
                                        <option value="Usulan">Usulan</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control form-control-sm filter-input" data-column="2">
                                        <option value="">Semua Status</option>
                                        @if(isset($uniqueStatuses))
                                            @foreach($uniqueStatuses as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="4" placeholder="Filter pembuat..."></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="5" placeholder="Filter tanggal..."></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $p)
                                <tr>
                                    <td>
                                        {{ $p->kode_pengajuan ?? 'REG-'.str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>{{ $p->tahun_anggaran }}</td>
                                    <td>{{ ucfirst($p->tipe_pengajuan) }}</td>
                                    <td>
                                        @php
                                            $status = $p->status_pengajuan ?: 'Draft';
                                            $badgeClass = '';
                                            switch($status) {
                                                case 'Draft':
                                                    $badgeClass = 'badge-secondary';
                                                    break;
                                                case 'Diajukan ke Unit Pelaksana':
                                                    $badgeClass = 'badge-info';
                                                    break;
                                                case 'Diajukan ke Koordinator':
                                                    $badgeClass = 'badge-warning';
                                                    break;
                                                case 'Disetujui':
                                                    $badgeClass = 'badge-success';
                                                    break;
                                                case 'Ditolak Pelaksana':
                                                case 'Ditolak oleh Koordinator':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                                default:
                                                    $badgeClass = 'badge-light';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>{{ $p->created_by ?: 'Unknown' }}</td>
                                    <td>
                                        {{ $p->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('pengajuan.reguler.review', $p->id) }}" class="btn btn-info btn-sm mr-1" title="Review Pengajuan">
                                                Review
                                            </a>
                                            @if(in_array($p->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan']))
                                                <a href="{{ route('pengajuan.reguler.edit', $p->id) }}" class="btn btn-warning btn-sm edit-pengajuan-btn" title="Edit Pengajuan">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <div class="text-muted">Belum ada pengajuan dibuat</div>
                                        <small class="text-muted">Klik tombol "Tambah Data Pengajuan" untuk membuat pengajuan baru</small>
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
        max-width: 150px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionStorageKey = 'pengajuanFormData'; // Pastikan key sama dengan halaman create

    // Function to clear session storage
    function clearSessionStorage() {
        sessionStorage.removeItem(sessionStorageKey);
        console.log("sessionStorage cleared for key:", sessionStorageKey);
    }

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

    // Event listener for "Tambah Data Pengajuan" button
    const createPengajuanBtn = document.getElementById('createPengajuanBtn');
    if (createPengajuanBtn) {
        createPengajuanBtn.addEventListener('click', function(event) {
            clearSessionStorage(); // Clear before navigating to create form
        });
    }

    // Event listener for all "Edit" buttons
    const editPengajuanBtns = document.querySelectorAll('.edit-pengajuan-btn');
    editPengajuanBtns.forEach(btn => {
        btn.addEventListener('click', function(event) {
            clearSessionStorage(); // Clear before navigating to edit form
            // No need to preventDefault or redirect here,
            // as the default link behavior will handle navigation.
        });
    });


    // Function to apply striping to visible rows
    function applyTableStriping() {
        const table = document.getElementById('pengajuanTable');
        // Handle cases where table might not exist or be empty
        if (!table || !table.getElementsByTagName('tbody')[0]) return;

        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let visibleCount = 0;

        for (let i = 0; i < rows.length; i++) {
            // Skip the "no data" row if it's the only one and has colspan
            if (rows[i].cells.length === 1 && rows[i].cells[0].getAttribute('colspan')) {
                continue;
            }

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
                    if (j === 3) { // Status column - get text from badge
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
