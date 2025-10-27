{{--resources/views/monitoring/DashboardPageReguler.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-chart-line mr-2"></i>
                        Monitoring Pengajuan BMN Non-SBSK
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('monitoring.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Monitoring Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <style>
                .status-badge {
                    font-size: 0.75rem;
                    padding: 0.375rem 0.75rem;
                    border-radius: 0.25rem;
                    white-space: nowrap;
                }

                .progress-mini {
                    height: 6px;
                    border-radius: 3px;
                    margin-bottom: 5px;
                }

                .btn-group-sm .btn {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
                    border-radius: 0.2rem;
                }

                /* Custom typography classes */
                .text-semibold {
                    font-weight: 600;
                }

                .text-medium {
                    font-weight: 500;
                }

                @media (max-width: 768px) {
                    .table-responsive {
                        font-size: 0.875rem;
                    }

                    .btn-group-sm .btn {
                        padding: 0.125rem 0.25rem;
                        font-size: 0.6rem;
                    }
                }
            </style>

            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="fas fa-search mr-2"></i>
                                Pencarian Data
                            </h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="input-group input-group-sm" style="width: 250px; display: inline-flex;">
                                <input type="text" class="form-control" id="search-input" placeholder="Cari pengajuan...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="refresh-data">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Daftar Pengajuan
                            </h3>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-secondary">
                                Total: <span id="showing-count">{{ $pengajuan->count() }}</span> pengajuan
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="pengajuan-table" class="table mb-0">
                            <thead>
                                <tr>
                                    <th width="4%">No</th>
                                    <th width="8%">Kode Pengajuan</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="6%">Tahun</th>
                                    <th width="8%">Tipe</th>
                                    <th width="18%">Bagian Pengusul</th>
                                    <th width="18%">Bagian Pelaksana</th>
                                    <th width="12%">Total Anggaran</th>
                                    <th width="14%">Status</th>
                                    <th width="12%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuan as $index => $item)
                                @php
                                    $all_statuses = [
                                        'Draft',
                                        'Diajukan ke Unit Pelaksana',
                                        'Diajukan ke Koordinator',
                                        'Diajukan ke Unit Perencanaan',
                                        'Disetujui'
                                    ];

                                    $current_status = $item->status_pengajuan ?: 'Draft';
                                    $is_rejected = strpos($current_status, 'Ditolak') !== false;

                                    $progress_percentage = 0;
                                    $status_badge_class = 'secondary'; // Default badge color

                                    if ($is_rejected) {
                                        $progress_percentage = 0;
                                        $status_badge_class = 'danger';
                                    } else {
                                        $status_index = array_search($current_status, $all_statuses);
                                        if ($status_index !== false) {
                                            $progress_percentage = (($status_index + 1) / count($all_statuses)) * 100;
                                            if ($current_status === 'Disetujui') {
                                                $status_badge_class = 'success';
                                            } elseif (strpos($current_status, 'Diajukan') !== false) {
                                                $status_badge_class = 'warning';
                                            } elseif ($current_status === 'Draft') {
                                                $status_badge_class = 'info'; // Or primary, depending on preference
                                            }
                                        }
                                    }
                                @endphp
                                <tr data-status="{{ $item->status_pengajuan }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $item->kode_pengajuan }}</span>
                                    </td>
                                    <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-center">{{ $item->tahun_anggaran }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->tipe_pengajuan === 'usulan' ? 'primary' : 'success' }}">
                                            @if($item->tipe_pengajuan === 'usulan')
                                                Pengajuan Anggaran
                                            @elseif($item->tipe_pengajuan === 'revisi')
                                                Pembayaran
                                            @else
                                                {{ ucfirst($item->tipe_pengajuan) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-semibold text-dark">{{ $item->bagian_pengusul_nama ?: '-' }}</span>
                                            @if($item->biro_pengusul_nama)
                                                <br><small class="text-muted">{{ $item->biro_pengusul_nama }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-semibold text-dark">{{ $item->bagian_pelaksana_nama ?: '-' }}</span>
                                            @if($item->biro_pelaksana_nama)
                                                <br><small class="text-muted">{{ $item->biro_pelaksana_nama }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <span class="text-semibold text-success">Rp {{ number_format($item->total_anggaran, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $status_badge_class }} status-badge">
                                            {{ $current_status }}
                                        </span>
                                        <div class="progress progress-mini mt-1">
                                            <div class="progress-bar bg-{{ $status_badge_class }}"
                                                style="width: {{ $progress_percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ round($progress_percentage) }}%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary quick-view-btn"
                                                     data-id="{{ $item->id }}" title="Quick View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('monitoring.review', $item->id) }}"
                                               class="btn btn-outline-success" title="Review">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada pengajuan</h5>
                                        <p class="text-muted">Belum ada pengajuan yang dibuat.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span class="text-muted">
                                Last updated: {{ date('d/m/Y H:i:s') }}
                            </span>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                Data diurutkan berdasarkan tanggal terbaru
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quickViewModalLabel">
                    <i class="fas fa-eye mr-2"></i> Quick View - Pengajuan #<span id="modal-pengajuan-id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <a href="#" class="btn btn-primary" id="view-full-review">
                    <i class="fas fa-clipboard-check mr-1"></i> Review Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const reviewRouteBase = @json(route('monitoring.review', ['id' => 'PLACEHOLDER_ID']));
</script>

<script>
$(document).ready(function() {
    let allRows = $('#pengajuan-table tbody tr:not(:last-child)'); // Exclude empty state row

    // Search functionality
    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        searchTable(searchTerm);
    });

    // Search table
    function searchTable(searchTerm) {
        let visibleCount = 0;

        allRows.each(function() {
            const rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        updateShowingCount(visibleCount);
    }

    // Update showing count
    function updateShowingCount(count) {
        $('#showing-count').text(count);
    }

    // Quick view functionality
    $('.quick-view-btn').on('click', function() {
        const pengajuanId = $(this).data('id');
        showQuickView(pengajuanId);
    });

    // Refresh functionality
    $('#refresh-data').on('click', function() {
        Swal.fire({
            title: 'Refreshing...',
            text: 'Memperbarui data pengajuan',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Refresh page after 2 seconds
        setTimeout(() => {
            location.reload();
        }, 2000);
    });

    function showQuickView(pengajuanId) {
        $('#modal-pengajuan-id').text(pengajuanId);
        $('#quickViewModal').modal('show');

        // Reset modal content
        $('#modal-body-content').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading data...</p>
            </div>
        `);

        // Get table data for comparison (from current row)
        const tableRow = $(`.quick-view-btn[data-id="${pengajuanId}"]`).closest('tr');
        const tableKode = tableRow.find('td:eq(1) .badge').text(); // Kode dari tabel

        // Load pengajuan data
        $.ajax({
            url: `{{ route('monitoring.show', '') }}/${pengajuanId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;

                    // Fallback: use table data if AJAX data incomplete
                    if (!data.kode_pengajuan && tableKode) {
                        data.kode_pengajuan = tableKode;
                    }

                    const html = createQuickViewHTML(data);
                    $('#modal-body-content').html(html);
                    $('#view-full-review').attr('href', reviewRouteBase.replace('PLACEHOLDER_ID', pengajuanId));

                } else {
                    $('#modal-body-content').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#modal-body-content').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Gagal memuat data pengajuan.
                    </div>
                `);
            }
        });
    }

    function createQuickViewHTML(data) {
        // Convert tipe pengajuan label
        let tipeLabel = 'Draft';
        let badgeClass = 'secondary';

        // Pastikan tipe_pengajuan ada dan dalam format lowercase
        const tipeRaw = (data.tipe_pengajuan || '').toString().toLowerCase();

        if (tipeRaw === 'usulan') {
            tipeLabel = 'Pengajuan Anggaran';
            badgeClass = 'primary';
        } else if (tipeRaw === 'revisi') {
            tipeLabel = 'Pembayaran';
            badgeClass = 'success';
        } else if (tipeRaw) {
            // Untuk tipe lainnya, gunakan format title case
            tipeLabel = tipeRaw.charAt(0).toUpperCase() + tipeRaw.slice(1);
        }

        // Safe access untuk semua data dengan fallback yang lebih lengkap
        const kodeDisplay = data.kode_pengajuan || data.kode || data.pengajuan_kode || data.no_pengajuan || data.nomor_pengajuan || data.id || 'N/A';
        const tahunDisplay = data.tahun_anggaran || data.tahun || data.anggaran_tahun || new Date().getFullYear();
        const statusDisplay = data.status_pengajuan || data.status || data.pengajuan_status || 'Draft';
        const tanggalDisplay = data.created_at ? new Date(data.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
        const bagianPengusulDisplay = data.bagian_pengusul_nama || data.bagian_pengusul || data.pengusul_bagian || data.nama_bagian_pengusul || '-';
        const biroPengusulDisplay = data.biro_pengusul_nama || data.biro_pengusul || data.pengusul_biro || data.nama_biro_pengusul || '-';
        const bagianPelaksanaDisplay = data.bagian_pelaksana_nama || data.bagian_pelaksana || data.pelaksana_bagian || data.nama_bagian_pelaksana || '-';
        const totalAnggaran = (data.total_anggaran_pengajuan || 0) + (data.total_anggaran_revisi || 0) || data.total_anggaran || data.anggaran_total || 0;
        const keteranganDisplay = data.keterangan || data.catatan || data.deskripsi || '';

        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle mr-1"></i> Informasi Umum</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-medium text-dark" width="40%">Kode:</td><td>${kodeDisplay}</td></tr>
                                <tr><td class="text-medium text-dark">Tipe:</td><td><span class="badge badge-${badgeClass}">${tipeLabel}</span></td></tr>
                                <tr><td class="text-medium text-dark">Tahun:</td><td>${tahunDisplay}</td></tr>
                                <tr><td class="text-medium text-dark">Status:</td><td><span class="badge badge-secondary">${statusDisplay}</span></td></tr>
                                <tr><td class="text-medium text-dark">Tanggal:</td><td>${tanggalDisplay}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-building mr-1"></i> Bagian Terkait</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-medium text-dark" width="40%">Pengusul:</td><td>${bagianPengusulDisplay}</td></tr>
                                <tr><td class="text-medium text-dark">Biro:</td><td>${biroPengusulDisplay}</td></tr>
                                <tr><td class="text-medium text-dark">Pelaksana:</td><td>${bagianPelaksanaDisplay}</td></tr>
                                <tr><td class="text-medium text-dark">Total:</td><td class="text-semibold text-success">Rp ${new Intl.NumberFormat('id-ID').format(totalAnggaran)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            ${keteranganDisplay && keteranganDisplay !== '-' && keteranganDisplay.trim() !== '' ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-comment mr-1"></i> Keterangan</h6>
                            </div>
                            <div class="card-body p-3">
                                <p class="mb-0">${keteranganDisplay}</p>
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
        `;
    }
});
</script>
@endsection
