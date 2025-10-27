{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/review/ReviewComponentR6.blade.php --}}


<div class="row">
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Jenis Satker:</label>
            <span class="page-value">{{ $detailData->jenis_satker ?? '' }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Jenis Kendaraan:</label>
            <span class="page-value">{{ $detailData->jenis_kendaraan ?? '' }}</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-row">
            <label class="page-label">Skema Pengadaan:</label>
            <span class="page-value">{{ ucfirst($data->skema ?? '') }}</span>
        </div>
    </div>
</div>


{{-- Alert success dengan detail kendaraan terpilih --}}
@if(isset($detailData->detail_kendaraan))
<div class="alert alert-success mt-3">
    <h6 class="font-weight-bold mb-3"><i class="fas fa-check-circle mr-2"></i>Detail Kendaraan Fungsional Terpilih</h6>
    <div class="row">
        @if(isset($detailData->detail_kendaraan['kapasitas']))
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-users text-success mr-2"></i>
                <div>
                    <small class="text-muted">Kapasitas:</small><br>
                    <strong>{{ $detailData->detail_kendaraan['kapasitas'] }}</strong>
                </div>
            </div>
        </div>
        @endif
        @if(isset($detailData->detail_kendaraan['penggunaan']))
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-cog text-success mr-2"></i>
                <div>
                    <small class="text-muted">Penggunaan:</small><br>
                    <strong>{{ $detailData->detail_kendaraan['penggunaan'] }}</strong>
                </div>
            </div>
        </div>
        @endif
        @if(isset($detailData->detail_kendaraan['fitur_utama']))
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-star text-success mr-2"></i>
                <div>
                    <small class="text-muted">Fitur Utama:</small><br>
                    <strong>{{ $detailData->detail_kendaraan['fitur_utama'] }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>
    @if(isset($detailData->detail_kendaraan['spesifikasi_lengkap']))
    <hr>
    <div class="row">
        <div class="col-12">
            <small class="text-muted">Spesifikasi Lengkap:</small><br>
            <p class="mb-0">{{ $detailData->detail_kendaraan['spesifikasi_lengkap'] }}</p>
        </div>
    </div>
    @endif
</div>
@endif
