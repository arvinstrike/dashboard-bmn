{{-- resources/views/PerencanaanBMN/batch/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard Monitoring Batch Sitangguh</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('batch.index') }}">Batch Sitangguh</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Filter Area -->
            <div class="card mb-3">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">Filter Data</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.dashboard') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Mulai:</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Akhir:</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                        <a href="{{ route('batch.dashboard') }}" class="btn btn-default">
                                            <i class="fas fa-sync-alt mr-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistik Batch -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalBatch }}</h3>
                            <p>Total Batch</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="{{ route('batch.index') }}" class="small-box-footer">
                            Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $totalDraft }}</h3>
                            <p>Batch Draft</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <a href="{{ route('batch.index', ['status' => 'draft']) }}" class="small-box-footer">
                            Lihat <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $totalDikirim }}</h3>
                            <p>Batch Dikirim</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <a href="{{ route('batch.index', ['status' => 'dikirim']) }}" class="small-box-footer">
                            Lihat <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalSelesai }}</h3>
                            <p>Batch Selesai</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('batch.index', ['status' => 'selesai']) }}" class="small-box-footer">
                            Lihat <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $totalDiproses }}</h3>
                            <p>Batch Diproses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <a href="{{ route('batch.index', ['status' => 'diproses']) }}" class="small-box-footer">
                            Lihat <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $totalDitolak }}</h3>
                            <p>Batch Ditolak</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <a href="{{ route('batch.index', ['status' => 'ditolak']) }}" class="small-box-footer">
                            Lihat <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Nilai Batch (30 hari terakhir)</span>
                            <span class="info-box-number">Rp {{ number_format($totalNilai30Hari ?? 0, 0, ',', '.') }}</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                {{ $totalBatch30Hari ?? 0 }} batch dalam 30 hari terakhir
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Grafik Status Batch -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">Distribusi Status Batch</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik Batch Bulanan -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Batch per Bulan (6 Bulan Terakhir)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Batch Terbaru -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Batch Terbaru</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode Batch</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Total Nilai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestBatches as $batch)
                                            <tr>
                                                <td>{{ $batch->kode_batch }}</td>
                                                <td>{{ $batch->tanggal_dibuat->format('d-m-Y') }}</td>
                                                <td>
                                                    @if($batch->status == 'draft')
                                                        <span class="badge badge-secondary">Draft</span>
                                                    @elseif($batch->status == 'dikirim')
                                                        <span class="badge badge-primary">Dikirim</span>
                                                    @elseif($batch->status == 'diproses')
                                                        <span class="badge badge-warning">Diproses</span>
                                                    @elseif($batch->status == 'selesai')
                                                        <span class="badge badge-success">Selesai</span>
                                                    @elseif($batch->status == 'ditolak')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($batch->total_nilai_batch ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('batch.show', $batch->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada batch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('batch.index') }}" class="btn btn-sm btn-default">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <!-- Batch Nilai Terbesar -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Batch dengan Nilai Terbesar</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode Batch</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Total Nilai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($highestValueBatches as $batch)
                                            <tr>
                                                <td>{{ $batch->kode_batch }}</td>
                                                <td>{{ $batch->tanggal_dibuat->format('d-m-Y') }}</td>
                                                <td>
                                                    @if($batch->status == 'draft')
                                                        <span class="badge badge-secondary">Draft</span>
                                                    @elseif($batch->status == 'dikirim')
                                                        <span class="badge badge-primary">Dikirim</span>
                                                    @elseif($batch->status == 'diproses')
                                                        <span class="badge badge-warning">Diproses</span>
                                                    @elseif($batch->status == 'selesai')
                                                        <span class="badge badge-success">Selesai</span>
                                                    @elseif($batch->status == 'ditolak')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($batch->total_nilai_batch ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('batch.show', $batch->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada batch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('batch.index', ['sort' => 'nilai', 'order' => 'desc']) }}" class="btn btn-sm btn-default">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Status Chart
    var ctx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Dikirim', 'Diproses', 'Selesai', 'Ditolak'],
            datasets: [{
                data: [{{ $totalDraft }}, {{ $totalDikirim }}, {{ $totalDiproses }}, {{ $totalSelesai }}, {{ $totalDitolak }}],
                backgroundColor: [
                    '#6c757d',  // secondary
                    '#007bff',  // primary
                    '#ffc107',  // warning
                    '#28a745',  // success
                    '#dc3545'   // danger
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Monthly Chart
    var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) !!},
            datasets: [{
                label: 'Jumlah Batch',
                data: {!! json_encode($monthlyData ?? [0, 0, 0, 0, 0, 0]) !!},
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
