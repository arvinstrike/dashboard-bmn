{{--resources/views/PerencanaanBMN/Bagian/koordinator_sbsk/ReviewPageKoordinatorSBSK.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review Pengajuan RKBMN SBSK</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('koordinator_sbsk.index') }}">Review SBSK</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- Banner Status Global --}}
            <div class="alert mb-3" id="status-banner">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle mr-2 fa-lg" id="status-icon"></i>
                    <span id="status-message"></span>
                </div>
                {{-- Alasan Penolakan Container --}}
                <div id="rejection-reason-container" class="mt-2" style="display: none;">
                    <hr>
                    <p class="mb-1 font-weight-bold">Histori Alasan Penolakan:</p>
                    <div class="p-2 bg-light border rounded">
                        <p class="mb-0" id="rejection-reason-text" style="font-size: 0.9rem; font-style: italic;"></p>
                    </div>
                </div>
            </div>

            <!-- Hidden input untuk menyimpan status pengajuan -->
            <input type="hidden" id="current-status-pengajuan" value="{{ $data->status }}">

            {{-- Informasi Pengajuan, Bagian, dan Anggaran --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Kode:</label>
                                <span class="d-block">{{ $data->kode_jenis_pengajuan }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Skema:</label>
                                <span class="d-block">{{ $data->skema }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Tahun Anggaran:</label>
                                <span class="d-block">{{ $data->tahun_anggaran }}</span>
                            </div>
                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Status Pengajuan:</label>
                                <span class="d-block">
                                    @php
                                    $statusClass = 'badge-secondary';
                                    if($data->status === 'Diajukan ke Koordinator') {
                                        $statusClass = 'badge-warning';
                                    } elseif($data->status === 'Disetujui oleh Koordinator') {
                                        $statusClass = 'badge-success';
                                    } elseif($data->status === 'Ditolak oleh Koordinator') {
                                        $statusClass = 'badge-danger';
                                    }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $data->status }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Informasi Pengusul</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Biro:</label>
                                <span class="d-block">{{ $data->biroPengusul->uraianbiro ?? '-' }}</span>
                            </div>
                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Bagian:</label>
                                <span class="d-block">{{ $data->bagianPengusul->uraianbagian ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Informasi Anggaran</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Kuantitas:</label>
                                <span class="d-block">{{ $data->kuantitas }} Unit</span>
                            </div>
                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Total:</label>
                                <span class="d-block">{{ 'Rp ' . number_format($data->total_anggaran, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Spesifikasi Khusus (Dinamis) --}}
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">Spesifikasi Khusus Pengajuan</h6>
                </div>
                <div class="card-body" id="spesifikasi-content">
                    {{-- Menggunakan kembali komponen review dari modul pengajuan --}}
                    @include('PerencanaanBMN.Bagian.pengajuanrkbmn.components.review.ReviewComponent' . substr($data->kode_jenis_pengajuan, 0, 2), ['detailData' => $detailData])
                </div>
            </div>

            {{-- Panel Informasi Barang & Dokumen --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Informasi Barang</h6>
                        </div>
                        <div class="card-body">
                            {{-- SBSK Hierarchy --}}
                            <div class="row">
                                <div class="col-md-6"><div class="page-row"><label class="page-label">Golongan:</label><span class="page-value">{{ $barangInfo['golongan'] ?? '-' }}</span></div></div>
                                <div class="col-md-6"><div class="page-row"><label class="page-label">Bidang:</label><span class="page-value">{{ $barangInfo['bidang'] ?? '-' }}</span></div></div>
                                <div class="col-md-6"><div class="page-row"><label class="page-label">Kelompok:</label><span class="page-value">{{ $barangInfo['kelompok'] ?? '-' }}</span></div></div>
                                <div class="col-md-6"><div class="page-row"><label class="page-label">Sub Kelompok:</label><span class="page-value">{{ $barangInfo['sub_kelompok'] ?? '-' }}</span></div></div>
                                <div class="col-md-12"><div class="page-row"><label class="page-label">Barang:</label><span class="page-value">{{ $barangInfo['barang'] ?? '-' }}</span></div></div>
                            </div>

                            <hr>

                            {{-- Informasi Anggaran --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="page-row">
                                        <label class="page-label">Kuantitas:</label>
                                        <span class="page-value">{{ $data->kuantitas ?? 0 }} unit</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="page-row">
                                        <label class="page-label">Harga Satuan:</label>
                                        <span class="page-value price-highlight">{{ $data->harga_barang ? 'Rp ' . number_format($data->harga_barang, 0, ',', '.') : 'Rp 0' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="page-row">
                                        <label class="page-label">Total Anggaran:</label>
                                        <span class="page-value price-highlight">{{ $data->total_anggaran ? 'Rp ' . number_format($data->total_anggaran, 0, ',', '.') : 'Rp 0' }}</span>
                                        <div class="calculation-detail">
                                            {{ $data->kuantitas ?? 0 }} unit × {{ $data->harga_barang ? 'Rp ' . number_format($data->harga_barang, 0, ',', '.') : 'Rp 0' }} = {{ $data->total_anggaran ? 'Rp ' . number_format($data->total_anggaran, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Uraian Barang --}}
                            @if($data->uraian_barang)
                            <hr>
                            <div class="page-row">
                                <label class="page-label">Spesifikasi Tambahan/Uraian Barang:</label>
                                <div class="page-value p-2 bg-light border rounded">{{ $data->uraian_barang }}</div>
                            </div>
                            @endif

                            {{-- Keterangan --}}
                            <div class="page-row">
                                <label class="page-label">Keterangan:</label>
                                <div class="page-value p-2 bg-light border rounded">
                                    @if($data->keterangan)
                                        {{ $data->keterangan }}
                                    @else
                                        <span class="text-muted font-italic">Tidak ada keterangan tambahan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Dokumen Pendukung</h6>
                        </div>
                        <div class="card-body">
                            {{-- Preview & Download Dokumen Pendukung --}}
                            @if($data->dokumen_pendukung)
                                <!-- Document Status & Actions -->
                                <div id="document-info-koordinator" class="mb-3">
                                    <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                        <i class="fas fa-file-pdf text-danger mr-3 fa-2x"></i>
                                        <div class="flex-grow-1">
                                            <div id="document-name-koordinator" class="font-weight-bold">dokumen_pendukung.pdf</div>
                                            <div class="small text-muted">Dokumen dari pengusul</div>
                                        </div>
                                    </div>

                                    <div class="btn-group btn-block mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-preview-dokumen-btn">
                                            <i class="fas fa-eye mr-1"></i> Preview
                                        </button>
                                        <a href="{{ route('pengajuanrkbmnbagian.downloadDokumen', $data->id) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                </div>

                                <!-- PDF Preview Container (Hidden by default) -->
                                <div id="pdf-preview-dokumen-container" class="mt-3" style="display: none;">
                                    <div class="pdf-preview-wrapper">
                                        <iframe id="pdf-preview-dokumen-frame" src="{{ route('pengajuanrkbmnbagian.previewDokumen', $data->id) }}" style="width: 100%; height: 400px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light text-center p-2 mb-2">
                                    <i class="fas fa-file-upload fa-2x text-muted mb-2 d-block"></i>
                                    Dok. Pendukung tidak tersedia
                                </div>
                            @endif

                            {{-- Tombol Download BA Final (jika sudah co-sign) --}}
                            @if($isCoSigned)
                                <hr>
                                <a href="{{ route('koordinator_sbsk.download_ba_final', $data->id) }}" class="btn btn-success btn-block">
                                    <i class="fas fa-check-double mr-1"></i> Download BA Final
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Verifikasi Berita Acara --}}
            <div class="card mb-3" id="berita-acara-section">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-file-signature mr-2"></i>Verifikasi Berita Acara SBSK</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Informasi:</strong> Verifikasi Berita Acara hanya diperlukan jika pengajuan <strong>disetujui</strong> oleh koordinator. Jika pengajuan ditolak, verifikasi ini tidak diperlukan.
                    </div>
                    <div id="berita-acara-verification" class="mb-2">
                        <div class="verification-status mb-2">
                            <h6 class="font-weight-bold mb-2">Status Tanda Tangan Berita Acara:</h6>
                            <div id="status-berita-acara">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-edit text-primary mr-2"></i>
                                    <span id="operator-signed-status">
                                        @if($data->berita_acara_sbsk_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Operator ({{ $data->bagianPengusul->uraianbagian ?? '-' }})
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Operator ({{ $data->bagianPengusul->uraianbagian ?? '-' }})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-shield {{ $data->berita_acara_sbsk_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="koordinator-signed-status">
                                        @if($isCoSigned)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3" id="berita-acara-actions">
                                @if($isCoSigned)
                                    <a href="{{ route('koordinator_sbsk.download_ba_final', $data->id) }}" class="btn btn-success">
                                        <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Review Koordinator --}}
            <div class="card mb-3" id="form-review-koordinator">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-check-double mr-2"></i>Review Koordinator (Keputusan Final)</h6>
                </div>
                <div class="card-body" id="review-card-body">
                    <form id="form-review">
                        @csrf
                        <div id="status-review-container" class="form-group">
                            <label class="font-weight-bold">Status Review:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="status_setuju" name="status" value="Disetujui oleh Koordinator" class="custom-control-input" checked>
                                <label class="custom-control-label" for="status_setuju">
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Disetujui oleh Koordinator</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="status_tolak" name="status" value="Ditolak oleh Koordinator" class="custom-control-input">
                                <label class="custom-control-label" for="status_tolak">
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Ditolak oleh Koordinator</span>
                                </label>
                            </div>
                        </div>

                        <div id="alasan-penolakan-container" class="form-group mt-3" style="display:none;">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Perhatian:</strong> Berita Acara yang sudah ditandatangani akan <strong>dihapus</strong> dari sistem jika pengajuan ditolak.
                            </div>
                            <label for="alasan_koordinator_bmn" class="font-weight-bold">Alasan Penolakan:</label>
                            <textarea id="alasan_koordinator_bmn" name="alasan_koordinator_bmn" class="form-control" rows="4" placeholder="Berikan alasan penolakan pengajuan ini..."></textarea>
                        </div>

                        <hr>

                        <div class="form-group mt-3">
                            <button type="button" class="btn btn-info" id="preview-ba-btn">
                                <i class="fas fa-eye mr-1"></i> Preview & Kirim Verifikasi Berita Acara
                            </button>
                            <button type="button" class="btn btn-secondary" id="simpan-review-btn" data-id="{{ $data->id }}" disabled>
                                <i class="fas fa-lock mr-1"></i> Simpan Review
                            </button>
                            <a href="{{ route('koordinator_sbsk.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                    <!-- Pesan ketika card disabled -->
                    <div id="review-disabled-message" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Catatan:</strong> Review pengajuan tidak dapat dilakukan karena pengajuan sudah tidak dalam status review koordinator.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Berita Acara PDF Preview and Magic Link -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">
                        <i class="fas fa-file-signature mr-2"></i> Preview Berita Acara SBSK
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="pdf-container" style="height: 600px; border: 1px solid #ddd; position: relative;">
                                <div class="text-center p-5" id="pdf-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat dokumen...</p>
                                </div>
                                <iframe id="pdf-preview-ba" src="" style="width: 100%; height: 100%; display: none;"></iframe>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Informasi Dokumen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-1"></i> Silakan periksa dokumen Berita Acara dengan teliti sebelum mengirim magic link verifikasi untuk co-signing.
                                    </div>
                                    <div class="verification-details">
                                        <h6 class="font-weight-bold mb-3">Detail Pengajuan:</h6>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Kode Pengajuan:</div>
                                            <div class="col-7"><span class="font-weight-bold">{{ $data->kode_jenis_pengajuan }}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Bagian Pengusul:</div>
                                            <div class="col-7"><span class="font-weight-bold">{{ $data->bagianPengusul->uraianbagian ?? '-' }}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Total Anggaran:</div>
                                            <div class="col-7"><span class="font-weight-bold text-success">Rp {{ number_format($data->total_anggaran, 0, ',', '.') }}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Tanggal:</div>
                                            <div class="col-7"><span class="font-weight-bold">{{ date('d F Y') }}</span></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-paper-plane mr-1"></i> <strong>Magic link verifikasi</strong> akan dikirim ke WhatsApp Koordinator BMN (Eselon III) untuk proses co-signing tanda tangan elektronik.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="send-magic-link-modal-btn">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Magic Link Verifikasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .disabled-card {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }

    .disabled-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.5);
        z-index: 10;
    }

    .card-body .p-2 {
        padding: 8px 12px !important;
    }

    /* === STYLING DARI OPERATOR === */
    .page-row { margin-bottom: 10px; }
    .page-label { display: block; font-weight: bold; margin-bottom: 0px; }
    .page-value { display: block; padding: 5px; font-size: 1rem; color: #495057; background-color: #fff; border-bottom: 1px solid #ced4da; }
    .calculation-detail { font-size: 0.9rem; color: #6c757d; font-style: italic; margin-top: 5px; }
    .price-highlight { color: #28a745; font-weight: bold; }

    /* Document Preview Styling */
    .document-preview-area {
        background-color: #f8f9fa;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .document-status-alert {
        border-left: 4px solid #007bff;
    }
    .document-status-alert.success {
        border-left-color: #28a745;
        background-color: #f0fff0;
        color: #0f5132;
    }
    .document-status-alert.warning {
        border-left-color: #ffc107;
        background-color: #fffbf0;
    }
    .pdf-preview-wrapper {
        background-color: #f8f9fa;
        border-radius: 4px;
        position: relative;
        min-height: 200px;
    }
    .pdf-preview-frame {
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    #document-info {
        transition: all 0.3s ease;
    }
    #no-document-placeholder {
        background-color: #f8f9fa;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .fade-element {
        transition: opacity 0.3s ease-in-out;
    }
    .fade-element.hide {
        opacity: 0;
    }
    .fade-element.show {
        opacity: 1;
    }

    /* Verification & Modal Styling */
    .pdf-container {
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }
    #pdf-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    .verification-details {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        border-left: 4px solid #007bff;
    }
    .verification-details .row {
        margin-bottom: 8px;
    }
    .verification-details .col-5 {
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .modal-xl {
            margin: 0.5rem;
            max-width: none;
        }
        .pdf-container {
            height: 400px !important;
            margin-bottom: 15px;
        }
        .modal-body .row {
            flex-direction: column;
        }
        .modal-body .col-md-8,
        .modal-body .col-md-4 {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const pengajuanId = "{{ $data->id }}";
    const csrfToken = "{{ csrf_token() }}";
    const isCoSigned = {{ $isCoSigned ? 'true' : 'false' }};
    const currentStatus = $('#current-status-pengajuan').val();
    const rejectionReason = "{{ $data->alasan_koordinator_bmn ?? '' }}";

    // ========= FUNGSI SET STATUS BANNER DINAMIS =========
    function setStatusBanner(status, rejectionReason) {
        const banner = $('#status-banner');
        const icon = $('#status-icon');
        const message = $('#status-message');
        const rejectionContainer = $('#rejection-reason-container');
        const rejectionText = $('#rejection-reason-text');

        let alertClass = 'alert-info', iconClass = 'fas fa-info-circle', statusMessage = `Status pengajuan: ${status}`;
        rejectionContainer.hide(); // Sembunyikan secara default

        if (status === 'Diajukan ke Koordinator') {
            alertClass = 'alert-warning';
            iconClass = 'fas fa-clock';
            statusMessage = 'Pengajuan menunggu review dari Koordinator SBSK. Silakan periksa kelengkapan dokumen dan data sebelum memberikan keputusan.';
        } else if (status === 'Disetujui oleh Koordinator') {
            alertClass = 'alert-success';
            iconClass = 'fas fa-check-circle';
            statusMessage = 'Pengajuan telah disetujui oleh Koordinator SBSK. Proses selanjutnya dapat dilanjutkan.';
        } else if (status === 'Ditolak oleh Koordinator') {
            alertClass = 'alert-danger';
            iconClass = 'fas fa-times-circle';
            statusMessage = 'Pengajuan telah ditolak oleh Koordinator SBSK.';
        }

        // Tampilkan alasan penolakan jika ada
        if (rejectionReason && rejectionReason.trim() !== '') {
            rejectionContainer.show();
            rejectionText.text(rejectionReason);
        }

        banner.removeClass('alert-info alert-warning alert-success alert-danger').addClass(alertClass);
        icon.attr('class', iconClass + ' mr-2 fa-lg');
        message.text(statusMessage);
    }

    // ========= FUNGSI UNTUK MENGECEK STATUS PENGAJUAN =========
    function checkPengajuanStatus() {
        // Status yang diizinkan untuk melakukan review
        const allowedStatuses = ['Diajukan ke Koordinator'];
        return allowedStatuses.includes(currentStatus);
    }

    // ========= FUNGSI UNTUK DISABLE/ENABLE CARD =========
    function toggleCardAccess() {
        const isStatusAllowed = checkPengajuanStatus();

        if (!isStatusAllowed) {
            // Disable card review pengajuan
            $('#review-card-body').addClass('disabled-card');
            $('#form-review input, #form-review textarea, #form-review button').prop('disabled', true);
            $('#review-disabled-message').show();
        } else {
            // Enable normal functionality
            $('#review-card-body').removeClass('disabled-card');
            $('#form-review input, #form-review textarea').prop('disabled', false);
            $('#review-disabled-message').hide();
        }
    }

    function updateUI() {
        // Cek dulu apakah status diizinkan untuk review
        if (!checkPengajuanStatus()) {
            return; // Jika tidak diizinkan, skip update UI
        }

        const statusTerpilih = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_koordinator_bmn').val().trim();

        // Reset state
        $('#simpan-review-btn').prop('disabled', true).removeClass('btn-primary btn-success').addClass('btn-secondary');

        if (statusTerpilih === 'Disetujui oleh Koordinator') {
            // SHOW: Card Berita Acara & Tombol Preview
            $('#berita-acara-section').slideDown();
            $('#preview-ba-btn').show();

            // HIDE: Alasan Penolakan
            $('#alasan-penolakan-container').hide();

            // Simpan button: Hanya enabled jika BA sudah co-sign
            if (isCoSigned) {
                $('#simpan-review-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                $('#preview-ba-btn').prop('disabled', true);
            } else {
                $('#preview-ba-btn').prop('disabled', false);
            }
        } else {
            // Status Ditolak
            // HIDE: Card Berita Acara & Tombol Preview
            $('#berita-acara-section').slideUp();
            $('#preview-ba-btn').hide();

            // SHOW: Alasan Penolakan
            $('#alasan-penolakan-container').show();

            // Simpan button: Enabled jika alasan diisi
            if (alasanPenolakan !== '') {
                $('#simpan-review-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            }
        }
    }

    // ========= TOGGLE PREVIEW DOKUMEN PENDUKUNG =========
    $('#toggle-preview-dokumen-btn').on('click', function() {
        const container = $('#pdf-preview-dokumen-container');
        const btn = $(this);

        if (container.is(':visible')) {
            container.slideUp();
            btn.html('<i class="fas fa-eye mr-1"></i> Preview');
        } else {
            container.slideDown();
            btn.html('<i class="fas fa-eye-slash mr-1"></i> Tutup Preview');
        }
    });

    // ========= PREVIEW BERITA ACARA - SHOW MODAL =========
    $('#preview-ba-btn').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Preview BA tidak dapat dilakukan karena status: ' + currentStatus, 'warning');
            return;
        }

        // Load PDF preview BA (operator signed)
        const pdfUrl = "{{ route('pengajuanrkbmnbagian.previewBeritaAcara', $data->id) }}";
        $('#pdf-loading').show();
        $('#pdf-preview-ba').hide();
        $('#pdf-preview-ba').attr('src', pdfUrl);

        $('#pdf-preview-ba').on('load', function() {
            $('#pdf-loading').fadeOut();
            $('#pdf-preview-ba').fadeIn();
        });

        $('#pdfPreviewModal').modal('show');
    });

    // Reset modal ketika ditutup
    $('#pdfPreviewModal').on('hidden.bs.modal', function () {
        $('#pdf-preview-ba').attr('src', '');
        $('#pdf-loading').show();
        $('#pdf-preview-ba').hide();
    });

    // Jalankan inisialisasi saat halaman dimuat
    setStatusBanner(currentStatus, rejectionReason);
    toggleCardAccess();
    updateUI();

    // Event listeners hanya jika status diizinkan
    if (checkPengajuanStatus()) {
        $('input[name="status"]').on('change', updateUI);
        $('#alasan_koordinator_bmn').on('input', updateUI);
    }

    // ========= KIRIM MAGIC LINK DARI MODAL =========
    $('#send-magic-link-modal-btn').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Magic Link tidak dapat dikirim karena status pengajuan: ' + currentStatus, 'warning');
            return;
        }

        const btn = $(this);
        Swal.fire({
            title: 'Kirim Link Co-Sign?',
            text: 'Link untuk co-signing Berita Acara akan dikirim ke Eselon III Koordinator BMN.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
                $.ajax({
                    url: `{{ route('koordinator_sbsk.send_magic_link', $data->id) }}`,
                    type: 'POST',
                    data: { _token: csrfToken },
                    success: (res) => {
                        $('#pdfPreviewModal').modal('hide');
                        Swal.fire('Berhasil!', res.message, 'success').then(() => {
                           location.reload();
                        });
                    },
                    error: (xhr) => {
                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Kirim Magic Link Verifikasi');
                    }
                });
            }
        });
    });

    // Aksi simpan review
    $('#simpan-review-btn').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Pengajuan tidak dapat direview karena statusnya: ' + currentStatus, 'warning');
            return;
        }

        const status = $('input[name="status"]:checked').val();
        const alasan = $('#alasan_koordinator_bmn').val();

        // Validasi frontend: jika setuju, pastikan sudah co-sign
        if (status === 'Disetujui oleh Koordinator' && !isCoSigned) {
            Swal.fire('Aksi Ditolak!', 'Anda harus melakukan co-sign Berita Acara terlebih dahulu sebelum menyetujui pengajuan ini.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Tindakan',
            text: `Anda yakin ingin menyelesaikan review dengan status "${status}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ route('koordinator_sbsk.update_review', $data->id) }}`,
                    type: 'POST',
                    data: { _token: csrfToken, status: status, alasan_koordinator_bmn: alasan },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            window.location.href = "{{ route('koordinator_sbsk.index') }}";
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = xhr.responseJSON.message || 'Terjadi kesalahan.';
                        if(errors && errors.alasan_koordinator_bmn){
                            errorMsg = errors.alasan_koordinator_bmn[0];
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
