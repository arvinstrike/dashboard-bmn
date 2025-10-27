{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/review/ReviewComponentR1.blade.php --}}

<div class="row">
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Klasifikasi Bangunan:</label>
            <span class="page-value">{{ $detailData->klasifikasi_bangunan ?? '' }}</span>
        </div>
        <div class="page-row">
            <label class="page-label">Klasifikasi Pejabat:</label>
            <span class="page-value">{{ $detailData->klasifikasi_pejabat ?? '' }}</span>
        </div>
        <div class="page-row">
            <label class="page-label">Lokasi:</label>
            <span class="page-value">{{ $detailData->lokasi ?? '' }}</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Luas Ruang Kerja:</label>
            <span class="page-value">{{ $detailData->luas_ruang_kerja ?? 0 }} m²</span>
        </div>
        <div class="page-row">
            <label class="page-label">Luas Ruang Tamu:</label>
            <span class="page-value">{{ $detailData->luas_ruang_tamu ?? 0 }} m²</span>
        </div>
        <div class="page-row">
            <label class="page-label">Luas Ruang Rapat:</label>
            <span class="page-value">{{ $detailData->luas_ruang_rapat ?? 0 }} m²</span>
        </div>
    </div>
</div>

@php
    $roomFields = [
        'luas_ruang_kerja' => 'Ruang Kerja',
        'luas_ruang_tamu' => 'Ruang Tamu',
        'luas_ruang_rapat' => 'Ruang Rapat',
        'luas_ruang_tunggu' => 'Ruang Tunggu',
        'luas_ruang_istirahat' => 'Ruang Istirahat',
        'luas_ruang_sekretaris' => 'Ruang Sekretaris',
        'luas_ruang_simpan' => 'Ruang Simpan',
        'luas_ruang_toilet' => 'Ruang Toilet',
        'luas_ruang_rapat_utama' => 'Ruang Rapat Utama'
    ];
    $hasRoomData = false;
    foreach($roomFields as $field => $label) {
        if(isset($detailData->$field) && $detailData->$field > 0) {
            $hasRoomData = true;
            break;
        }
    }
@endphp

@if($hasRoomData)
<hr>
<h6 class="font-weight-bold">Detail Luas Ruangan</h6>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Jenis Ruangan</th>
                <th>Luas (m²)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roomFields as $field => $label)
                @if(isset($detailData->$field) && $detailData->$field > 0)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $detailData->$field }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endif
