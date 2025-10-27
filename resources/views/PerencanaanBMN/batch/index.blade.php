{{-- resources/views/PerencanaanBMN/Batch/DashboardPageReguler.blade.php (Fixed) --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard Batch Pengajuan Sitangguh</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Batch Sitangguh</li>
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

            <!-- Quick Links -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('batch.dashboard') }}" class="btn btn-info mr-2">
                        <i class="fas fa-chart-bar mr-1"></i> Dashboard Monitoring
                    </a>
                </div>
            </div>

            <!-- Card untuk pembuatan batch baru -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Buat Batch Baru</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.store') }}" method="POST" id="createBatchForm">
                        @csrf
                        <div class="form-group">
                            <label for="keterangan">Keterangan Batch:</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Masukkan keterangan atau catatan untuk batch ini..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Pilih Pengajuan:</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="pengajuanTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" id="check-all">
                                                    <label for="check-all"></label>
                                                </div>
                                            </th>
                                            <th>ID</th>
                                            <th>Tanggal</th>
                                            <th>Bagian Pengusul</th>
                                            <th>Tipe</th>
                                            <th>Total Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengajuan as $item)
                                            <tr>
                                                <td>
                                                    <div class="icheck-primary">
                                                        <input type="checkbox" name="pengajuan_ids[]" id="pengajuan_{{ $item->id }}" value="{{ $item->id }}" class="pengajuan-checkbox">
                                                        <label for="pengajuan_{{ $item->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $item->bagian_pengusul ?? '-' }}</td>
                                                <td>{{ ucfirst($item->tipe_pengajuan) }}</td>
                                                <td>Rp {{ number_format($item->total_nilai ?? 0, 0, ',', '.') }}</td>
                                                <td><span class="badge badge-success">{{ $item->status_pengajuan }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada pengajuan yang tersedia untuk dibatch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="createBatchBtn">
                                <i class="fas fa-box-open mr-1"></i> Buat Batch
                            </button>
                            <span class="text-muted ml-2" id="selected-count">0 pengajuan dipilih</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card untuk monitoring batch -->
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Monitoring Batch</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Batch</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                    <th>Jumlah Pengajuan</th>
                                    <th>Total Nilai</th>
                                    <th>Terakhir Update</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
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
                                        <td>{{ $batch->details->count() }}</td>
                                        <td>Rp {{ number_format($batch->total_nilai_batch ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $batch->updated_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('batch.show', $batch->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            @if($batch->status == 'draft')
                                                <a href="{{ route('batch.edit', $batch->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('batch.send', $batch->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin mengirim batch ini ke Sitangguh?')">
                                                        <i class="fas fa-paper-plane"></i> Kirim
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('batch.pdf', $batch->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada batch yang dibuat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $batches->links() }}
                    </div>
                </div>
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

    // Check/uncheck all checkboxes
    $('#check-all').on('change', function() {
        $('.pengajuan-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });

    // Handle individual checkbox changes
    $('.pengajuan-checkbox').on('change', function() {
        updateSelectedCount();

        // If not all checkboxes are checked, uncheck the "check-all" checkbox
        if (!$(this).prop('checked')) {
            $('#check-all').prop('checked', false);
        }

        // If all checkboxes are checked, check the "check-all" checkbox
        if ($('.pengajuan-checkbox:checked').length === $('.pengajuan-checkbox').length) {
            $('#check-all').prop('checked', true);
        }
    });

    function updateSelectedCount() {
        var count = $('.pengajuan-checkbox:checked').length;
        $('#selected-count').text(count + ' pengajuan dipilih');
    }

    // Form validation
    $('#createBatchForm').on('submit', function(e) {
        var count = $('.pengajuan-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 pengajuan untuk membuat batch.');
            return false;
        }

        return true;
    });

    // Initialize counter
    updateSelectedCount();
});
</script>
@endpush
