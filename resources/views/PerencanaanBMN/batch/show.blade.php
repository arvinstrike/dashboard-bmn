{{-- resources/views/PerencanaanBMN/Batch/show.blade.php (Updated) --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Batch Pengajuan Sitangguh</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('batch.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('batch.index') }}">Batch Sitangguh</a></li>
                        <li class="breadcrumb-item active">Detail Batch</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <!-- Informasi Batch -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Informasi Batch</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Kode Batch</th>
                                    <td>{{ $batch->kode_batch }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
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
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $batch->tanggal_dibuat->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $batch->creator->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Total Nilai Batch</th>
                                    <td>Rp {{ number_format($batch->total_nilai_batch ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $batch->keterangan ?? '-' }}</td>
                                </tr>
                                @if($batch->tanggal_dikirim)
                                <tr>
                                    <th>Tanggal Dikirim</th>
                                    <td>{{ $batch->tanggal_dikirim->format('d-m-Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($batch->tanggal_diproses)
                                <tr>
                                    <th>Tanggal Diproses</th>
                                    <td>{{ $batch->tanggal_diproses->format('d-m-Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($batch->tanggal_selesai)
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td>{{ $batch->tanggal_selesai->format('d-m-Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('batch.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            @if($batch->status == 'draft')
                                <a href="{{ route('batch.edit', $batch->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('batch.send', $batch->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengirim batch ini ke Sitangguh?')">
                                        <i class="fas fa-paper-plane mr-1"></i> Kirim ke Sitangguh
                                    </button>
                                </form>
                            @endif

                            @if($batch->status == 'dikirim')
                                <!-- Simulasi tombol sitangguh -->
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#prosesModal">
                                    <i class="fas fa-cogs mr-1"></i> Simulasi: Proses Sitangguh
                                </button>
                            @endif

                            <a href="{{ route('batch.pdf', $batch->id) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Timeline Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($batch->logs->sortByDesc('created_at') as $log)
                                    <div class="time-label">
                                        <span class="bg-secondary">{{ $log->created_at->format('d-m-Y') }}</span>
                                    </div>
                                    <div>
                                        @if($log->aktivitas == 'pembuatan')
                                            <i class="fas fa-plus bg-primary"></i>
                                        @elseif($log->aktivitas == 'pengiriman')
                                            <i class="fas fa-paper-plane bg-success"></i>
                                        @elseif($log->aktivitas == 'pemrosesan')
                                            <i class="fas fa-cogs bg-warning"></i>
                                        @elseif($log->aktivitas == 'update')
                                            <i class="fas fa-edit bg-info"></i>
                                        @elseif($log->aktivitas == 'penolakan')
                                            <i class="fas fa-times bg-danger"></i>
                                        @elseif($log->aktivitas == 'penerimaan')
                                            <i class="fas fa-check bg-success"></i>
                                        @else
                                            <i class="fas fa-info-circle bg-info"></i>
                                        @endif
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                                            <h3 class="timeline-header">
                                                {{ ucfirst($log->aktivitas) }} oleh {{ $log->user->name ?? 'N/A' }}
                                            </h3>
                                            <div class="timeline-body">
                                                {{ $log->deskripsi }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Pengajuan dalam Batch -->
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Daftar Pengajuan dalam Batch</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pengajuanTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>ID Pengajuan</th>
                                    <th>Tanggal</th>
                                    <th>Bagian Pengusul</th>
                                    <th>Tipe</th>
                                    <th>Total Nilai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->details->sortBy('urutan') as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->pengajuan->id }}</td>
                                        <td>{{ $detail->pengajuan->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $detail->pengajuan->bagian_pengusul ?? '-' }}</td>
                                        <td>{{ ucfirst($detail->pengajuan->tipe_pengajuan) }}</td>
                                        <td>Rp {{ number_format($detail->pengajuan->total_nilai ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            @if($detail->status_pengajuan_di_batch == 'draft')
                                                <span class="badge badge-secondary">Draft</span>
                                            @elseif($detail->status_pengajuan_di_batch == 'dikirim')
                                                <span class="badge badge-primary">Dikirim</span>
                                            @elseif($detail->status_pengajuan_di_batch == 'diproses')
                                                <span class="badge badge-warning">Diproses</span>
                                            @elseif($detail->status_pengajuan_di_batch == 'selesai')
                                                <span class="badge badge-success">Selesai</span>
                                            @elseif($detail->status_pengajuan_di_batch == 'ditolak')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $detail->pengajuan->id }}">
                                                <i class="fas fa-eye"></i> Lihat
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada pengajuan dalam batch ini</td>
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

<!-- Modal untuk Proses Sitangguh -->
<div class="modal fade" id="prosesModal" tabindex="-1" role="dialog" aria-labelledby="prosesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prosesModalLabel">Simulasi Proses Sitangguh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Simulasi aksi dari sistem Sitangguh. Pilih aksi:</p>

                <div class="form-group">
                    <form action="{{ route('batch.reject', $batch->id) }}" method="POST" id="rejectForm">
                        @csrf
                        @method('PATCH')
                        <label for="alasan_penolakan">Alasan Penolakan (jika ditolak):</label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="3"></textarea>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" form="rejectForm" class="btn btn-danger">
                    <i class="fas fa-times mr-1"></i> Tolak Batch
                </button>
                <form action="{{ route('batch.accept', $batch->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i> Terima Batch
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#pengajuanTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });
});
</script>
@endpush
