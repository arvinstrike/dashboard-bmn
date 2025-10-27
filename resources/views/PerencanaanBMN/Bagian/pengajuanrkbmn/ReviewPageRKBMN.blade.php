@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review Pengajuan RKBMN</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('pengajuanrkbmnbagian.index') }}">Pengajuan RKBMN</a></li>
                        <li class="breadcrumb-item active">Review</li>
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
                {{-- REVISI: Perbaikan struktur dan styling untuk alasan penolakan --}}
                <div id="rejection-reason-container" class="mt-2" style="display: none;">
                    <hr>
                    <p class="mb-1 font-weight-bold">Alasan Penolakan dari Koordinator:</p>
                    <div class="p-2 bg-light border rounded">
                        <p class="mb-0" id="rejection-reason-text" style="font-size: 0.9rem; font-style: italic;"></p>
                    </div>
                </div>
                 {{-- AKHIR REVISI --}}
            </div>

            <style>
                .page-row { margin-bottom: 10px; }
                .page-label { display: block; font-weight: bold; margin-bottom: 0px; }
                .page-value { display: block; padding: 5px; font-size: 1rem; color: #495057; background-color: #fff; border-bottom: 1px solid #ced4da; }
                .calculation-detail { font-size: 0.9rem; color: #6c757d; font-style: italic; margin-top: 5px; }
                .price-highlight { color: #28a745; font-weight: bold; }

                /* Document Upload Styling */
                .document-upload-area {
                    background-color: #f8f9fa;
                    border-radius: 4px;
                    transition: all 0.3s ease;
                }
                .document-upload-area:hover {
                    box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
                .highlight-upload {
                    background-color: #e8f4ff !important;
                    border: 2px dashed #007bff !important;
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

                /* Verification Styling */
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
                #confirm-verification-button {
                    background-color: #28a745;
                    border-color: #28a745;
                    font-weight: 500;
                }
                #confirm-verification-button:hover {
                    background-color: #218838;
                    border-color: #1e7e34;
                }
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.2); }
                    100% { transform: scale(1); }
                }
                #confirm-verification-button .fas {
                    animation: pulse 1.5s infinite;
                }
                #passphrase-input-modal {
                    border-left: 4px solid #007bff;
                    transition: all 0.3s ease;
                }
                #passphrase-input-modal:focus {
                    box-shadow: none;
                    border-color: #007bff;
                    border-left-width: 8px;
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

            {{-- Panel Informasi Atas --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3"><div class="card-header bg-primary text-white"><h6 class="mb-0">Informasi Pengajuan</h6></div>
                        <div class="card-body">
                            <div class="page-row"><label class="page-label">Kode Pengajuan:</label><span class="page-value">{{ $data->kode_jenis_pengajuan ?? '' }}</span></div>
                            <div class="page-row"><label class="page-label">Skema:</label><span class="page-value">{{ $data->skema ?? '' }}</span></div>
                            <div class="page-row"><label class="page-label">Tahun Anggaran:</label><span class="page-value">{{ $data->tahun_anggaran ?? '' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3"><div class="card-header bg-info text-white"><h6 class="mb-0">Informasi Bagian</h6></div>
                        <div class="card-body">
                            <div class="page-row"><label class="page-label">Biro Pengusul:</label><span class="page-value">{{ $data->biroPengusul->uraianbiro ?? '' }}</span></div>
                            <div class="page-row"><label class="page-label">Bagian Pengusul:</label><span class="page-value">{{ $data->bagianPengusul->uraianbagian ?? '' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="card mb-3"><div class="card-header bg-secondary text-white"><h6 class="mb-0">Dokumen Pendukung</h6></div>
                        <div class="card-body">
                             @if($data->dokumen_pendukung)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger fa-2x mr-3"></i>
                                    <div class="flex-grow-1"><p class="font-weight-bold mb-0">Dokumen tersedia</p></div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="downloadDocument()"><i class="fas fa-download"></i> Download</button>
                                </div>
                            @else
                                <div class="text-center text-muted py-3"><i class="fas fa-times-circle fa-2x mb-2"></i><p>Belum ada dokumen</p></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Spesifikasi Khusus (Dinamis) --}}
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark"><h6 class="mb-0" id="spesifikasi-title">Spesifikasi Khusus</h6></div>
                <div class="card-body" id="spesifikasi-content"><div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Memuat...</div></div>
            </div>

            {{-- Panel Informasi Barang --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Informasi Barang dan Anggaran</h6></div>
                <div class="card-body">
                    {{-- SBSK Hierarchy --}}
                    <div class="row">
                        <div class="col-md-6"><div class="page-row"><label class="page-label">Golongan:</label><span class="page-value" id="golongan"></span></div></div>
                        <div class="col-md-6"><div class="page-row"><label class="page-label">Bidang:</label><span class="page-value" id="bidang"></span></div></div>
                        <div class="col-md-6"><div class="page-row"><label class="page-label">Kelompok:</label><span class="page-value" id="kelompok"></span></div></div>
                        <div class="col-md-6"><div class="page-row"><label class="page-label">Sub Kelompok:</label><span class="page-value" id="sub-kelompok"></span></div></div>
                        <div class="col-md-12"><div class="page-row"><label class="page-label">Barang:</label><span class="page-value" id="barang"></span></div></div>
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
                                <span class="page-value price-highlight" id="harga-satuan">{{ $data->harga_barang ? 'Rp ' . number_format($data->harga_barang, 0, ',', '.') : 'Rp 0' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="page-row">
                                <label class="page-label">Total Anggaran:</label>
                                <span class="page-value price-highlight" id="total-anggaran">{{ $data->total_anggaran ? 'Rp ' . number_format($data->total_anggaran, 0, ',', '.') : 'Rp 0' }}</span>
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

            {{-- Dokumen Pendukung --}}
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-file-upload mr-2"></i>Dokumen Pendukung</h6>
                </div>
                <div class="card-body">
                    <!-- Status Dokumen Bar -->
                    <div class="alert alert-primary document-status-alert mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle mr-2 fa-lg"></i>
                            <span id="document-status-message">Silakan unggah dokumen pendukung untuk melengkapi pengajuan Anda.</span>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Upload Area (Kiri) -->
                        <div class="col-md-5">
                            <div class="document-upload-area p-3 mb-3 border rounded">
                                <h6 class="font-weight-bold mb-3 text-primary">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Dokumen
                                </h6>

                                <form id="dokumen-upload-form" enctype="multipart/form-data">
                                    <div class="custom-file mb-3">
                                        <input type="file" class="custom-file-input" id="dokumen-file" name="dokumen" accept="application/pdf"
                                            @if(!in_array($data->status, ['Draft', 'Ditolak oleh Koordinator'])) disabled @endif>
                                        <label class="custom-file-label" for="dokumen-file">
                                            @if(!in_array($data->status, ['Draft', 'Ditolak oleh Koordinator']))
                                                Tidak dapat diubah (Status: {{ $data->status }})
                                            @else
                                                Pilih file PDF...
                                            @endif
                                        </label>
                                        <div class="invalid-feedback">File harus berformat PDF dan berukuran maksimal 5MB.</div>
                                    </div>

                                    <div class="progress mb-3" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    @if(in_array($data->status, ['Draft', 'Ditolak oleh Koordinator']))
                                        <div class="upload-guide small text-muted">
                                            <i class="fas fa-lightbulb mr-1"></i> Tip: Dokumen akan otomatis terunggah setelah Anda memilih file
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-lock mr-1"></i> Dokumen tidak dapat diubah karena pengajuan sudah diajukan.
                                        </div>
                                    @endif
                                </form>

                                @if(in_array($data->status, ['Draft', 'Ditolak oleh Koordinator']))
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Dokumen harus dalam format PDF dan berukuran maksimal 5MB.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Preview & File Info Area (Kanan) -->
                        <div class="col-md-7">
                            <div class="document-preview-area">
                                <h6 class="font-weight-bold mb-3 text-primary">
                                    <i class="fas fa-file-pdf mr-2"></i><span id="preview-area-title">Dokumen Saat Ini</span>
                                </h6>

                                <!-- Document Status & Actions -->
                                <div id="no-document-placeholder" class="text-center py-5">
                                    <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada dokumen yang diunggah</p>
                                    <p class="small">Dokumen pendukung diperlukan untuk melengkapi pengajuan.</p>
                                </div>

                                <!-- Current Document Info (Hidden by default, shown when document exists) -->
                                <div id="document-info" class="mb-3" style="display: none;">
                                    <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                        <i class="fas fa-file-pdf text-danger mr-3 fa-2x"></i>
                                        <div class="flex-grow-1">
                                            <div id="document-name" class="font-weight-bold">dokumen_pendukung.pdf</div>
                                            <div id="document-upload-time" class="small text-muted">Diunggah pada: <span id="upload-datetime">-</span></div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" id="toggle-preview-btn">
                                                <i class="fas fa-eye mr-1"></i> Preview
                                            </button>
                                        </div>
                                    </div>

                                    <div class="btn-group btn-block">
                                        <a href="#" class="btn btn-info" id="download-dokumen-btn">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                        @if(in_array($data->status, ['Draft', 'Ditolak oleh Koordinator']))
                                            <button type="button" class="btn btn-danger" id="delete-dokumen-btn">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- PDF Preview Container (Hidden by default) -->
                                <div id="pdf-preview-container" class="mt-3" style="display: none;">
                                    <div class="pdf-preview-wrapper">
                                        <div class="pdf-loading text-center py-5" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Memuat dokumen...</p>
                                        </div>
                                        <iframe id="pdf-preview-frame" class="pdf-preview-frame" src="" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL Verifikasi Berita Acara --}}
            <div class="card mb-3">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-file-signature mr-2"></i>Verifikasi Berita Acara</h6>
                </div>
                <div class="card-body">
                    <div id="berita-acara-verification" class="mb-2">
                        <div class="verification-status mb-2">
                            <h6 class="font-weight-bold mb-2">Status Tanda Tangan Berita Acara:</h6>
                            <div id="status-berita-acara">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-user-edit text-primary mr-2"></i>
                                    <span id="operator-signed-status"><i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Operator</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-user-shield text-secondary mr-2"></i>
                                    <span id="koordinator-signed-status"><i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Koordinator</span>
                                </div>
                            </div>
                            <div class="mt-3 mb-2" id="berita-acara-actions">
                                <button type="button" class="btn btn-outline-primary" id="verify-berita-acara-button">
                                    <i class="fas fa-file-signature mr-1"></i> Preview & Tanda Tangani Berita Acara
                                </button>
                                <button type="button" class="btn btn-outline-success d-none" id="download-berita-acara-signed-button">
                                    <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer Aksi (Action Footer) --}}
    <div class="content-footer">
        <div class="container-fluid">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('pengajuanrkbmnbagian.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
                        <div id="action-buttons"></div>
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
                        <i class="fas fa-file-signature mr-2"></i> Preview Berita Acara
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
                                <iframe id="pdf-preview" src="" style="width: 100%; height: 100%; display: none;"></iframe>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Informasi Dokumen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-1"></i> Silakan periksa dokumen Berita Acara dengan teliti sebelum mengirim magic link verifikasi.
                                    </div>
                                    <div class="verification-details">
                                        <h6 class="font-weight-bold mb-3">Detail Pengajuan:</h6>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Kode Pengajuan:</div>
                                            <div class="col-7"><span class="font-weight-bold" id="detail-nomor-pengajuan">{{ $data->kode_jenis_pengajuan }}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Bagian Pengusul:</div>
                                            <div class="col-7"><span class="font-weight-bold" id="detail-bagian-pengusul">{{ $data->bagianPengusul->uraianbagian ?? '-' }}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Tanggal:</div>
                                            <div class="col-7"><span class="font-weight-bold" id="detail-tanggal">{{ date('d F Y') }}</span></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-paper-plane mr-1"></i> <strong>Magic link verifikasi</strong> akan dikirim ke WhatsApp Penanggung Jawab (Eselon III) untuk proses tanda tangan elektronik.
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
                    <button type="button" class="btn btn-success" id="send-magic-link-button">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Magic Link Verifikasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const pengajuanData = @json($data);
    const detailData = @json($detailData ?? null);
    const barangInfo = @json($barangInfo ?? null);

    function initializeReviewPage(data, detailData, barangInfo) {
        setStatusBanner(data.status, data.alasan_koordinator_bmn); // Teruskan alasan penolakan
        loadSpesifikasiKhusus(data.kode_jenis_pengajuan, data, detailData);
        setBarangInfo(barangInfo);
        setActionButtons(data.status, data.id, data.berita_acara_sbsk_signed_path);
    }

    function setStatusBanner(status, rejectionReason) {
        const banner = document.getElementById('status-banner');
        const icon = document.getElementById('status-icon');
        const message = document.getElementById('status-message');
        const rejectionContainer = document.getElementById('rejection-reason-container');
        const rejectionText = document.getElementById('rejection-reason-text');

        let alertClass = 'alert-info', iconClass = 'fas fa-info-circle', statusMessage = `Status pengajuan: ${status}`;
        rejectionContainer.style.display = 'none'; // Sembunyikan secara default

        if (status === 'Draft') {
            alertClass = 'alert-secondary'; iconClass = 'fas fa-edit';
            statusMessage = 'Pengajuan masih dalam status draft. Silakan review sebelum diajukan.';
        } else if (status.includes('Diajukan')) {
            alertClass = 'alert-warning'; iconClass = 'fas fa-clock';
            statusMessage = 'Pengajuan telah dikirim dan sedang dalam proses review.';
        } else if (status === 'Ditolak oleh Koordinator') {
            alertClass = 'alert-danger'; iconClass = 'fas fa-times-circle';
            statusMessage = 'Pengajuan ditolak oleh Koordinator. Silakan edit pengajuan untuk diperbaiki dan diajukan kembali.';
            // Tampilkan alasan penolakan jika ada
            if (rejectionReason) {
                rejectionContainer.style.display = 'block';
                rejectionText.textContent = rejectionReason;
            }
        } else if (status.includes('Ditolak')) {
             alertClass = 'alert-danger'; iconClass = 'fas fa-times-circle';
            statusMessage = `Pengajuan ditolak. Silakan edit pengajuan untuk diperbaiki dan diajukan kembali.`;
        } else if (status.includes('Disetujui')) {
            alertClass = 'alert-success'; iconClass = 'fas fa-check-circle';
            statusMessage = 'Pengajuan telah disetujui.';
        }

        banner.className = `alert ${alertClass} mb-3`;
        icon.className = `${iconClass} mr-2 fa-lg`;
        message.textContent = statusMessage;
    }

    function loadSpesifikasiKhusus(kodeJenisLengkap, data, detailData) {
        const content = document.getElementById('spesifikasi-content');
        fetch(`{{ url('pengajuanrkbmnbagian/review-component') }}/${kodeJenisLengkap}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ data, detailData })
        })
        .then(response => response.json())
        .then(result => { content.innerHTML = result.success ? result.html : '<div class="alert alert-warning">Gagal memuat detail.</div>'; })
        .catch(error => { content.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan.</div>'; });
    }

    function setBarangInfo(barangInfo) {
        document.getElementById('golongan').textContent = barangInfo?.golongan || '-';
        document.getElementById('bidang').textContent = barangInfo?.bidang || '-';
        document.getElementById('kelompok').textContent = barangInfo?.kelompok || '-';
        document.getElementById('sub-kelompok').textContent = barangInfo?.sub_kelompok || '-';
        document.getElementById('barang').textContent = barangInfo?.barang || '-';
    }

    function setActionButtons(status, id, signedPath) {
        const container = document.getElementById('action-buttons');
        let buttons = '';
        const editableStatuses = ['Draft', 'Ditolak oleh Koordinator'];

        if (editableStatuses.includes(status)) {
            buttons = `<div class="btn-group">
                <button type="button" class="btn btn-warning" onclick="editPengajuan(${id})"><i class="fas fa-edit mr-1"></i>Edit</button>
                <button type="button" class="btn btn-danger" onclick="hapusPengajuan(${id})"><i class="fas fa-trash mr-1"></i>Hapus</button>`;

            // Tombol ajukan hanya muncul jika status draft atau ditolak & BA sudah TTD
            const isSigned = signedPath && signedPath.length > 0;
            const disabledAttr = isSigned ? '' : 'disabled';
            const titleAttr = isSigned ? '' : 'data-toggle="tooltip" title="Berita Acara harus ditandatangani terlebih dahulu"';

            buttons += `<button type="button" class="btn btn-success" onclick="ajukanPengajuan(${id})" ${disabledAttr} ${titleAttr}>
                            <i class="fas fa-paper-plane mr-1"></i>Ajukan Kembali
                        </button>`;

            buttons += `</div>`;
        }
        container.innerHTML = buttons;

        $('[data-toggle="tooltip"]').tooltip();
    }

    initializeReviewPage(pengajuanData, detailData, barangInfo);
});

// KUMPULAN FUNGSI AKSI (GLOBAL)
function editPengajuan(id) {
    window.location.href = `{{ url('pengajuanrkbmnbagian') }}/${id}/edit`;
}

function ajukanPengajuan(id) {
    Swal.fire({
        title: 'Konfirmasi Pengajuan', text: 'Anda yakin ingin mengajukan kembali ke Koordinator BMN?', icon: 'question',
        showCancelButton: true, confirmButtonText: 'Ya, Ajukan!', cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('pengajuanrkbmnbagian') }}/${id}/submit`, {
                method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            })
            .then(response => response.json()).then(res => {
                if (res.success) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', res.message || 'Gagal mengajukan.', 'error');
                }
            });
        }
    });
}

function downloadDocument() {
    window.open(`{{ url('pengajuanrkbmnbagian') }}/{{ $data->id }}/download-dokumen`, '_blank');
}

function hapusPengajuan(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus', text: 'Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const deleteUrl = `{{ url('pengajuanrkbmnbagian') }}/${id}`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(response => {
                if (!response.ok) { return response.json().then(err => { throw new Error(err.message || 'Error server.'); }); }
                return response.json();
            })
            .then(res => {
                if (res.status === 'berhasil') {
                    Swal.fire('Terhapus!', 'Pengajuan berhasil dihapus.', 'success').then(() => window.location.href = '{{ route("pengajuanrkbmnbagian.index") }}');
                } else { throw new Error(res.message || 'Gagal menghapus data.'); }
            })
            .catch(error => { Swal.fire('Gagal!', error.message, 'error'); });
        }
    });
}

// Preview dan Download BA
function previewSignedBA(id) {
    // Menambahkan timestamp sebagai "cache buster" untuk memastikan file terbaru yang diambil
    const timestamp = new Date().getTime();
    const url = `{{ url('pengajuanrkbmnbagian') }}/${id}/preview-signed-ba?v=${timestamp}`;

    $('#pdfPreviewFrame').attr('src', url);
    $('#previewModal').modal('show');
}

$('#previewModal').on('hidden.bs.modal', function () {
    $('#pdfPreviewFrame').attr('src', '');
});

function downloadSignedBA(id) {
    window.location.href = `{{ url('pengajuanrkbmnbagian') }}/${id}/download-signed-ba`;
}

// ==========================================
// DOCUMENT UPLOAD FUNCTIONALITY
// ==========================================
$(document).ready(function() {
    const pengajuanId = {{ $data->id }};
    const csrfToken = '{{ csrf_token() }}';

    // Initialize document section based on existing data
    initializeDocumentSection();

    function initializeDocumentSection() {
        const status = '{{ $data->status }}';
        const isEditable = ['Draft', 'Ditolak oleh Koordinator'].includes(status);

        // Disable upload area jika tidak editable
        if (!isEditable) {
            $('#dokumen-file').prop('disabled', true);
            $('.document-upload-area').addClass('bg-light').css('opacity', '0.6');
        }

        @if($data->dokumen_pendukung)
            showDocumentInfo({
                name: '{{ basename($data->dokumen_pendukung) }}',
                path: '{{ $data->dokumen_pendukung }}',
                uploaded_at: '{{ date("d/m/Y H:i", strtotime($data->updated_at)) }}'
            }, isEditable);
        @else
            showNoDocumentPlaceholder(isEditable);
        @endif
    }

    function showDocumentInfo(doc, isEditable) {
        $('#no-document-placeholder').hide();
        $('#document-info').show();
        $('#document-name').text(doc.name);
        $('#upload-datetime').text(doc.uploaded_at);
        $('#download-dokumen-btn').attr('href', `{{ url('pengajuanrkbmnbagian') }}/${pengajuanId}/download-dokumen`);

        // Update status alert based on editability
        if (isEditable) {
            $('#document-status-message').text('Dokumen pendukung telah diunggah.');
            $('.document-status-alert').removeClass('alert-primary alert-info').addClass('alert-success success');
        } else {
            $('#document-status-message').text('Dokumen pendukung telah diunggah (tidak dapat diubah).');
            $('.document-status-alert').removeClass('alert-primary alert-success').addClass('alert-info');
        }
    }

    function showNoDocumentPlaceholder(isEditable) {
        $('#no-document-placeholder').show();
        $('#document-info').hide();
        $('#pdf-preview-container').hide();

        // Update status alert based on editability
        if (isEditable) {
            $('#document-status-message').text('Silakan unggah dokumen pendukung untuk melengkapi pengajuan Anda.');
            $('.document-status-alert').removeClass('alert-success alert-warning').addClass('alert-primary');
        } else {
            $('#document-status-message').text('Belum ada dokumen pendukung. Dokumen tidak dapat diunggah karena status pengajuan.');
            $('.document-status-alert').removeClass('alert-primary alert-success').addClass('alert-warning');
        }
    }

    // Auto-upload on file selection
    $('#dokumen-file').on('change', function() {
        const file = this.files[0];
        if (!file) return;

        // Validasi status - HANYA Draft atau Ditolak yang boleh upload
        const status = '{{ $data->status }}';
        const isEditable = ['Draft', 'Ditolak oleh Koordinator'].includes(status);

        if (!isEditable) {
            Swal.fire('Tidak Diizinkan', 'Dokumen tidak dapat diubah karena status pengajuan adalah: ' + status, 'warning');
            $(this).val('');
            return;
        }

        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire('Error', 'File harus berformat PDF', 'error');
            $(this).val('');
            return;
        }

        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire('Error', 'Ukuran file tidak boleh lebih dari 5MB', 'error');
            $(this).val('');
            return;
        }

        // Upload file
        uploadDocument(file);
    });

    function uploadDocument(file) {
        const formData = new FormData();
        formData.append('dokumen', file);
        formData.append('_token', csrfToken);

        // Show progress
        $('.progress').show();
        $('.progress-bar').css('width', '0%');

        $.ajax({
            url: `{{ url('pengajuanrkbmnbagian') }}/${pengajuanId}/upload-dokumen`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        $('.progress-bar').css('width', percentComplete + '%').attr('aria-valuenow', percentComplete);
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                $('.progress').hide();
                if (response.success) {
                    Swal.fire('Berhasil!', response.message, 'success');

                    // Cek status untuk parameter isEditable
                    const status = '{{ $data->status }}';
                    const isEditable = ['Draft', 'Ditolak oleh Koordinator'].includes(status);

                    showDocumentInfo({
                        name: response.filename,
                        path: response.path,
                        uploaded_at: new Date().toLocaleString('id-ID')
                    }, isEditable);

                    $('#dokumen-file').val('');
                    $('.custom-file-label').text('Pilih file PDF...');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function(xhr) {
                $('.progress').hide();
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengunggah dokumen';
                Swal.fire('Error!', errorMsg, 'error');
            }
        });
    }

    // Toggle preview
    $('#toggle-preview-btn').on('click', function() {
        const $container = $('#pdf-preview-container');
        if ($container.is(':visible')) {
            $container.hide();
            $(this).html('<i class="fas fa-eye mr-1"></i> Preview');
        } else {
            $('.pdf-loading').show();
            $('#pdf-preview-frame').hide();
            $container.show();

            const previewUrl = `{{ url('pengajuanrkbmnbagian') }}/${pengajuanId}/preview-dokumen`;
            $('#pdf-preview-frame').on('load', function() {
                $('.pdf-loading').hide();
                $(this).show();
            }).attr('src', previewUrl);

            $(this).html('<i class="fas fa-eye-slash mr-1"></i> Hide Preview');
        }
    });

    // Delete document
    $('#delete-dokumen-btn').on('click', function() {
        Swal.fire({
            title: 'Hapus Dokumen?',
            text: 'Anda yakin ingin menghapus dokumen ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('pengajuanrkbmnbagian') }}/${pengajuanId}/delete-document`,
                    type: 'DELETE',
                    data: { _token: csrfToken },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success');

                            // Cek status untuk parameter isEditable
                            const status = '{{ $data->status }}';
                            const isEditable = ['Draft', 'Ditolak oleh Koordinator'].includes(status);

                            showNoDocumentPlaceholder(isEditable);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message || 'Gagal menghapus dokumen', 'error');
                    }
                });
            }
        });
    });
});

// ==========================================
// BERITA ACARA VERIFICATION FUNCTIONALITY
// ==========================================
$(document).ready(function() {
    const pengajuanId = {{ $data->id }};

    // Initialize BA status
    initializeBeritaAcaraStatus();

    function initializeBeritaAcaraStatus() {
        @if(!empty($data->berita_acara_sbsk_signed_path))
            // Update status operator
            $('#operator-signed-status').html('<i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Operator');
            $('#koordinator-signed-status').parent().removeClass('text-secondary').addClass('text-primary');

            // Ubah teks tombol preview menjadi "Preview Berita Acara Tertandatangani"
            $('#verify-berita-acara-button')
                .html('<i class="fas fa-eye mr-1"></i> Preview Berita Acara Tertandatangani');

            // Disable tombol kirim magic link di modal
            $('#send-magic-link-button')
                .prop('disabled', true)
                .removeClass('btn-success')
                .addClass('btn-secondary')
                .html('<i class="fas fa-check mr-1"></i> Sudah Ditandatangani');

            @php
                $finalBaPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $data->berita_acara_sbsk_signed_path);
                $isCoSigned = Storage::disk('public')->exists($finalBaPath);
            @endphp

            @if($isCoSigned)
                $('#koordinator-signed-status').html('<i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Koordinator');
                $('#download-berita-acara-signed-button').removeClass('d-none').data('id', pengajuanId);
                $('#verify-berita-acara-button').addClass('d-none');
            @endif
        @endif
    }

    // Verify Berita Acara Button - Show Preview Modal
    $(document).on('click', '#verify-berita-acara-button', function() {
        const id = pengajuanId;

        // Show loading
        $('#pdf-loading').show();
        $('#pdf-preview').hide();

        // Open modal
        $('#pdfPreviewModal').modal('show');

        // Load PDF
        const previewUrl = `{{ url('pengajuanrkbmnbagian') }}/${id}/preview-berita-acara`;
        $('#pdf-preview').on('load', function() {
            $('#pdf-loading').hide();
            $('#pdf-preview').show();
        }).attr('src', previewUrl);
    });

    // Send Magic Link Button
    $(document).on('click', '#send-magic-link-button', function() {
        const btn = $(this);

        Swal.fire({
            title: 'Kirim Magic Link?',
            text: 'Link verifikasi akan dikirim ke WhatsApp Penanggung Jawab (Eselon III) untuk menandatangani Berita Acara.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                // Close modal first
                $('#pdfPreviewModal').modal('hide');

                // Show loading
                Swal.fire({
                    title: 'Mengirim Magic Link...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Send magic link via AJAX
                $.ajax({
                    url: `{{ url('pengajuanrkbmnbagian') }}/${pengajuanId}/send-magic-link-sbsk`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Magic link berhasil dikirim ke WhatsApp',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal mengirim magic link', 'error');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Terjadi kesalahan saat mengirim magic link';
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }
        });
    });

    // Clean up when modal is hidden
    $('#pdfPreviewModal').on('hidden.bs.modal', function () {
        $('#pdf-preview').attr('src', '');
    });

    // Download signed BA
    $(document).on('click', '#download-berita-acara-signed-button', function() {
        const id = $(this).data('id');
        window.location.href = `{{ url('pengajuanrkbmnbagian') }}/${id}/download-signed-ba`;
    });
});
</script>
@endsection
