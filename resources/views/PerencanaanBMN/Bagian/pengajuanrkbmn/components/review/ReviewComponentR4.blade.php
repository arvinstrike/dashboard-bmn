{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/review/ReviewComponentR4.blade.php --}}

<div class="row">
    {{-- UPDATED: Layout changed to col-md-4 --}}
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Klasifikasi Pejabat:</label>
            <span class="page-value">{{ $detailData->klasifikasi_pejabat ?? '' }}</span>
        </div>
    </div>
    {{-- UPDATED: Layout changed to col-md-4 --}}
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Spesifikasi Kendaraan:</label>
            <span class="page-value">{{ $detailData->spesifikasi_kendaraan ?? '' }}</span>
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
