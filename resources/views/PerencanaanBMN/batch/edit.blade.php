{{-- resources/views/PerencanaanBMN/Batch/EditFormReguler.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Batch Pengajuan Sitangguh</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('batch.index') }}">Batch Sitangguh</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('batch.show', $batch->id) }}">Detail Batch</a></li>
                        <li class="breadcrumb-item active">Edit Batch</li>
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

            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Edit Batch: {{ $batch->kode_batch }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.update', $batch->id) }}" method="POST" id="editBatchForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="keterangan">Keterangan Batch:</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Masukkan keterangan atau catatan untuk batch ini...">{{ $batch->keterangan }}</textarea>
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
                                        @forelse($availablePengajuan as $item)
                                            <tr>
                                                <td>
                                                    <div class="icheck-primary">
                                                        <input type="checkbox" name="pengajuan_ids[]" id="pengajuan_{{ $item->id }}" value="{{ $item->id }}" class="pengajuan-checkbox" {{ in_array($item->id, $selectedPengajuanIds) ? 'checked' : '' }}>
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
                                                <td colspan="7" class="text-center">Tidak ada pengajuan yang tersedia untuk ditambahkan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="saveBatchBtn">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('batch.show', $batch->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <span class="text-muted ml-2" id="selected-count">0 pengajuan dipilih</span>
                        </div>
                    </form>
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
        toggleSubmitButton();
    });

    // Handle individual checkbox changes
    $('.pengajuan-checkbox').on('change', function() {
        updateSelectedCount();
        toggleSubmitButton();

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

    function toggleSubmitButton() {
        var count = $('.pengajuan-checkbox:checked').length;
        $('#saveBatchBtn').prop('disabled', count === 0);
    }

    // Form validation
    $('#editBatchForm').on('submit', function(e) {
        var count = $('.pengajuan-checkbox:checked').length;
        if (count === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 pengajuan untuk batch.');
            return false;
        }

        return true;
    });

    // Initialize on page load
    updateSelectedCount();
    toggleSubmitButton();
});
</script>
@endpush
