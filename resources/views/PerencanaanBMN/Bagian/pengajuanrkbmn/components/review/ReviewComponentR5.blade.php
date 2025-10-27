{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/review/ReviewComponentR5.blade.php --}}
<div class="row">
    {{-- UPDATED: Layout changed to col-md-4 --}}
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Jenis Satker:</label>
            <span class="page-value">{{ $detailData->jenis_satker ?? '' }}</span>
        </div>
    </div>
    {{-- UPDATED: Layout changed to col-md-4 --}}
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Jenis Kendaraan:</label>
            <span class="page-value">{{ $detailData->jenis_kendaraan ?? '' }}</span>
        </div>
    </div>
    {{-- ADDED: Skema Pengadaan section --}}
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Skema Pengadaan:</label>
            <span class="page-value">{{ ucfirst($data->skema ?? '') }}</span>
        </div>
    </div>
</div>
