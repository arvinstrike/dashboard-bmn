{{--resources/views/monitoring/review.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Review Pengajuan #{{ $pengajuan->id }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('monitoring.index') }}">Monitoring</a></li>
                        <li class="breadcrumb-item active">Review Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <style>
                .review-card {
                    border-radius: 10px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    margin-bottom: 20px;
                }

                .review-header {
                    border-radius: 10px 10px 0 0;
                    font-weight: 600;
                }

                .status-timeline {
                    position: relative;
                    padding-left: 30px;
                    margin: 20px 0;
                }

                .status-timeline::before {
                    content: '';
                    position: absolute;
                    left: 15px;
                    top: 0;
                    bottom: 0;
                    width: 2px;
                    background: #dee2e6;
                }

                .timeline-item {
                    position: relative;
                    padding-bottom: 20px;
                }

                .timeline-item::before {
                    content: '';
                    position: absolute;
                    left: -22px;
                    top: 5px;
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background: #dee2e6;
                    border: 2px solid white;
                }

                .timeline-item.completed::before {
                    background: #28a745;
                }

                .timeline-item.current::before {
                    background: #007bff;
                    animation: pulse 2s infinite;
                }

                .timeline-item.rejected::before {
                    background: #dc3545;
                }

                @keyframes pulse {
                    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
                    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
                }

                .summary-box {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border-radius: 10px;
                    padding: 20px;
                    margin-bottom: 20px;
                }

                .detail-section {
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 15px;
                }

                .rejection-alert {
                    background: linear-gradient(135deg, #ff6b6b, #ffa8a8);
                    color: white;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 15px;
                }
            </style>

            <!-- Summary Box -->
            <div class="summary-box">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            {{ ucfirst($pengajuan->tipe_pengajuan) }} - {{ $pengajuan->tahun_anggaran }}
                        </h4>
                        <p class="mb-1 opacity-90">
                            <i class="fas fa-building mr-1"></i>
                            @php
                            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
                            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
                            @endphp
                            {{ $bagianPengusul ? $bagianPengusul->uraianbagian : '-' }}
                            →
                            {{ $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-' }}
                        </p>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-calendar mr-1"></i>
                            Diajukan: {{ $pengajuan->created_at ? $pengajuan->created_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <h2 class="mb-1">
                            @php
                            $totalAnggaran = 0;
                            if($pengajuan->tipe_pengajuan === 'usulan') {
                                foreach($pengajuan->detilPengajuan as $item) {
                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                }
                            } else {
                                foreach($pengajuan->detilRevisi as $item) {
                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                }
                            }
                            @endphp
                            Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                        </h2>
                        <div>
                            @php
                                $statusClass = 'secondary';
                                if($pengajuan->status_pengajuan === 'Disetujui' || $pengajuan->status_pengajuan === 'Disetujui Koordinator BMN') {
                                    $statusClass = 'success';
                                } elseif(strpos($pengajuan->status_pengajuan, 'Ditolak') !== false) {
                                    $statusClass = 'danger';
                                } elseif(strpos($pengajuan->status_pengajuan, 'Diajukan') !== false) {
                                    $statusClass = 'warning';
                                }
                            @endphp
                            <span class="badge badge-{{ $statusClass }} badge-lg px-3 py-2">
                                {{ $pengajuan->status_pengajuan ?: 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Information -->
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="card review-card">
                        <div class="card-header review-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-section">
                                        <h6 class="font-weight-bold text-primary">Data Umum</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td width="40%">Kode Pengajuan:</td>
                                                <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Tipe:</td>
                                                <td>
                                                    <span class="badge badge-{{ $pengajuan->tipe_pengajuan === 'usulan' ? 'primary' : 'info' }}">
                                                        {{ ucfirst($pengajuan->tipe_pengajuan) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tahun Anggaran:</td>
                                                <td><strong>{{ $pengajuan->tahun_anggaran }}</strong></td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-section">
                                        <h6 class="font-weight-bold text-success">Kode & Anggaran</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            @if($pengajuan->tipe_pengajuan === 'revisi')
                                            <tr>
                                                <td width="40%">Kode Pengenal:</td>
                                                <td><code>{{ $pengajuan->kode_pengenal ?: '-' }}</code></td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td width="40%">Kode Akun:</td>
                                                <td><code>{{ $pengajuan->kode_akun ?: '-' }}</code></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>Total Anggaran:</td>
                                                <td><strong class="text-success">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Barang:</td>
                                                <td>
                                                    @if($pengajuan->tipe_pengajuan === 'usulan')
                                                        {{ $pengajuan->detilPengajuan->count() }} item
                                                    @else
                                                        {{ $pengajuan->detilRevisi->count() }} item
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Information -->
                            <div class="detail-section">
                                <h6 class="font-weight-bold text-info">Informasi Bagian</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        @php
                                        $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
                                        @endphp
                                        <strong>Bagian Pengusul:</strong><br>
                                        {{ $bagianPengusul ? $bagianPengusul->uraianbagian : '-' }}<br>
                                        <small class="text-muted">{{ $biroPengusul ? $biroPengusul->uraianbiro : '-' }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                        $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana ?? 0)->first();
                                        @endphp
                                        <strong>Bagian Pelaksana:</strong><br>
                                        {{ $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-' }}<br>
                                        <small class="text-muted">{{ $biroPelaksana ? $biroPelaksana->uraianbiro : '-' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            @if($pengajuan->keterangan)
                            <div class="detail-section">
                                <h6 class="font-weight-bold text-secondary">Tujuan Pengajuan</h6>
                                <p class="mb-0">{{ $pengajuan->keterangan }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Detail Items -->
                    <div class="card review-card">
                        <div class="card-header review-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Detail Item Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            @if($pengajuan->tipe_pengajuan === 'usulan' && count($pengajuan->detilPengajuan) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="20%">Kode Barang</th>
                                                <th width="20%">Deskripsi</th>
                                                <th width="20%">Keterangan</th>
                                                <th width="10%">Qty</th>
                                                <th width="15%">Harga</th>
                                                <th width="15%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pengajuan->detilPengajuan as $index => $item)
                                                @php
                                                $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                                                    ->where('kode_perlengkapan', $item->kode_perlengkapan)
                                                    ->first();
                                                $barang = DB::table('t_brg')
                                                    ->where('kd_brg', $item->kode_barang)
                                                    ->first();

                                                $itemTotal = $item->kuantitas * $item->harga;
                                                @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $item->kode_barang }}</code></td>
                                                <td>{{ $barang ? $barang->ur_sskel : '-' }}</td>
                                                <td>{{ $item ? $item->keterangan_barang : '-' }}</td>
                                                <td class="text-center">{{ $item->kuantitas }}</td>
                                                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td class="text-right"><strong>Rp {{ number_format($itemTotal, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th colspan="6" class="text-right">Total Anggaran:</th>
                                                <th class="text-right">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @elseif($pengajuan->tipe_pengajuan === 'revisi' && count($pengajuan->detilRevisi) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="15%">Kode</th>
                                                <th width="35%">Deskripsi</th>
                                                <th width="10%">Qty</th>
                                                <th width="17.5%">Harga</th>
                                                <th width="17.5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pengajuan->detilRevisi as $index => $item)
                                                @php
                                                $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                                                    ->where('kode_perlengkapan', $item->kode_perlengkapan)
                                                    ->first();
                                                @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $item->kode_perlengkapan }}</code></td>
                                                <td>{{ $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-' }}</td>
                                                <td class="text-center">{{ $item->kuantitas }}</td>
                                                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td class="text-right"><strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th colspan="5" class="text-right">Total Anggaran Revisi:</th>
                                                <th class="text-right">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada detail item</h5>
                                    <p class="text-muted">Belum ada item yang ditambahkan ke pengajuan ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Status & Actions -->
                <div class="col-md-4">
                    <!-- Status Timeline -->
                    <div class="card review-card">
                        <div class="card-header review-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Progress Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="status-timeline">
                                @php
                                    $currentStatus = $pengajuan->status_pengajuan;
                                    $statuses = [
                                        'Draft' => 'Draft',
                                        'Diajukan ke Unit Pelaksana' => 'Diajukan ke Unit Pelaksana',
                                        'Diajukan ke Unit Koordinator' => 'Diajukan ke Unit Koordinator',
                                        'Diajukan ke Unit Perencanaan' => 'Diajukan ke Unit Perencanaan',
                                        'Disetujui' => 'Disetujui'
                                    ];

                                    $statusOrder = array_keys($statuses);
                                    $currentIndex = array_search($currentStatus, $statusOrder);
                                    if($currentIndex === false) $currentIndex = -1;

                                    $isRejected = strpos($currentStatus, 'Ditolak') !== false;
                                @endphp

                                @foreach($statuses as $key => $label)
                                    @php
                                        $index = array_search($key, $statusOrder);
                                        $class = '';
                                        if ($isRejected && $index >= $currentIndex) {
                                            $class = 'rejected';
                                        } elseif($index < $currentIndex) {
                                            $class = 'completed';
                                        } elseif($index == $currentIndex) {
                                            $class = 'current';
                                        }
                                    @endphp
                                    <div class="timeline-item {{ $class }}">
                                        <strong>{{ $label }}</strong>
                                        @if($index == $currentIndex)
                                            <span class="badge badge-primary ml-2">Saat Ini</span>
                                        @elseif($index < $currentIndex)
                                            <span class="badge badge-success ml-2">✓</span>
                                        @endif
                                        @if($isRejected && $index == $currentIndex)
                                            <span class="badge badge-danger ml-2">Ditolak</span>
                                        @endif
                                    </div>
                                @endforeach

                                @if($isRejected)
                                    <div class="timeline-item rejected">
                                        <strong>{{ $currentStatus }}</strong>
                                        <span class="badge badge-danger ml-2">Final</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reasons (if any) -->
                    @if(strpos($pengajuan->status_pengajuan, 'Ditolak') !== false)
                    <div class="rejection-alert">
                        <h6 class="mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Alasan Penolakan</h6>

                        @if($pengajuan->alasan_penolakan_pelaksana)
                        <div class="mb-2">
                            <strong>Pelaksana:</strong>
                            <p class="mb-1 opacity-90">{{ $pengajuan->alasan_penolakan_pelaksana }}</p>
                        </div>
                        @endif

                        @if($pengajuan->alasan_penolakan_koordinator)
                        <div class="mb-2">
                            <strong>Koordinator:</strong>
                            <p class="mb-1 opacity-90">{{ $pengajuan->alasan_penolakan_koordinator }}</p>
                        </div>
                        @endif

                        @if($pengajuan->alasan_penolakan_perencanaan)
                        <div class="mb-0">
                            <strong>Unit Perencanaan:</strong>
                            <p class="mb-0 opacity-90">{{ $pengajuan->alasan_penolakan_perencanaan }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="card review-card">
                        <div class="card-header review-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs mr-2"></i>Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('monitoring.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Monitoring
                                </a>

                                <!-- Add more action buttons here if needed -->
                                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print mr-1"></i> Print Review
                                </button>

                                <button type="button" class="btn btn-outline-success" onclick="refreshData()">
                                    <i class="fas fa-sync mr-1"></i> Refresh Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card review-card">
                        <div class="card-header review-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Quick Stats</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary mb-1">
                                        @if($pengajuan->tipe_pengajuan === 'usulan')
                                            {{ $pengajuan->detilPengajuan->count() }}
                                        @else
                                            {{ $pengajuan->detilRevisi->count() }}
                                        @endif
                                    </h4>
                                    <small class="text-muted">Total Item</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">
                                        {{ $pengajuan->created_at ? $pengajuan->created_at->diffForHumans() : '-' }}
                                    </h4>
                                    <small class="text-muted">Umur Pengajuan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data pengajuan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>

<style>
@media print {
    .content-header,
    .breadcrumb,
    .btn,
    .card-header {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }

    .summary-box {
        background: #f8f9fa !important;
        color: #000 !important;
        border: 2px solid #000 !important;
    }
}
</style>
@endsection
