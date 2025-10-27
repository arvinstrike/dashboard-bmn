{{--resources/views/PerencanaanBMN/Bagian/pengajuanrkbmnbagiannonsbsk/ReviewPageOperator.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review Pengajuan Non SBSK</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
                        <li class="breadcrumb-item active">Review</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Alert Info -->
            <div class="alert alert-info mb-3" id="status-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle mr-2 fa-lg"></i>
                    <span id="status-message">Silakan review pengajuan Anda sebelum dikirim ke Unit Pelaksana.</span>
                </div>
            </div>

            <style>
                /* Styling for review page */
                .page-row {
                    margin-bottom: 10px;
                }
                .page-label {
                    display: block;
                    font-weight: bold;
                    margin-bottom: 0px;
                }
                .page-value {
                    display: block;
                    padding: 5px;
                    font-size: 1rem;
                    line-height: 1.5;
                    color: #495057;
                    background-color: #fff;
                    background-clip: padding-box;
                    border-bottom: 1px solid #ced4da;
                }
                .card-header.bg-danger {
                    background-color: #dc3545 !important;
                }
                #status-penolakan .card {
                    margin-top: 15px;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                #status-penolakan .page-value.p-3 {
                    white-space: pre-line;
                    font-size: 0.95rem;
                    color: #333;
                }
                #status-ditolak {
                    font-size: 1.1rem;
                }
                .status-icon {
                    margin-right: 5px;
                }
                .table-items {
                    width: 100%;
                    border-collapse: collapse;
                }
                .table-items th, .table-items td {
                    padding: 8px;
                    border: 1px solid #ddd;
                }
                .table-items th {
                    background-color: #f8f9fa;
                }
                .badge-draft {
                    background-color: #6c757d;
                    color: white;
                }
                .badge-diajukan {
                    background-color: #17a2b8;
                    color: white;
                }
                .badge-disetujui {
                    background-color: #28a745;
                    color: white;
                }
                .badge-ditolak {
                    background-color: #dc3545;
                    color: white;
                }
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

                @media (max-width: 767.98px) {
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

                /* Improve button styling */
                #confirm-verification-button {
                    background-color: #28a745;
                    border-color: #28a745;
                    font-weight: 500;
                }

                #confirm-verification-button:hover {
                    background-color: #218838;
                    border-color: #1e7e34;
                }

                /* Add pulse animation to signature icon */
                @keyframes pulse {
                    0% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.2);
                    }
                    100% {
                        transform: scale(1);
                    }
                }

                #confirm-verification-button .fas {
                    animation: pulse 1.5s infinite;
                }

                /* Improve form element styling */
                #passphrase-input-modal {
                    border-left: 4px solid #007bff;
                    transition: all 0.3s ease;
                }

                #passphrase-input-modal:focus {
                    box-shadow: none;
                    border-color: #007bff;
                    border-left-width: 8px;
                }

                .alert-warning {
                    border-left: 4px solid #ffc107;
                }

                /* Add a nice signature position indicator */
                /*.signature-position {*/
                /*    position: absolute;*/
                /*    bottom: 50px;*/
                /*    right: 50px;*/
                /*    border: 2px dashed #007bff;*/
                /*    padding: 15px 25px;*/
                /*    border-radius: 4px;*/
                /*    color: rgba(0,0,0,0.5);*/
                /*    background-color: rgba(0, 123, 255, 0.1);*/
                /*    font-style: italic;*/
                /*}*/
            </style>

            <div class="row">
                <!-- Informasi Umum - Kolom Kiri -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="page-row p-2">
                                <label class="page-label">Kode Pengajuan:</label>
                                <span class="page-value" id="kode_pengajuan"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Tipe Pengajuan:</label>
                                <span class="page-value" id="tipe-pengajuan"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Tahun Anggaran:</label>
                                <span class="page-value" id="tahun-anggaran"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Status Pengajuan:</label>
                                <span class="page-value" id="status-pengajuan"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Tanggal Pengajuan:</label>
                                <span class="page-value" id="tanggal-pengajuan"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Bagian - Kolom Tengah -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Informasi Bagian</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="page-row p-2">
                                <label class="page-label">Bagian Pengusul:</label>
                                <span class="page-value" id="bagian-pengusul"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Biro Pengusul:</label>
                                <span class="page-value" id="biro-pengusul"></span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Bagian Pelaksana:</label>
                                <span class="page-value" id="bagian-pelaksana"></span>
                            </div>
                            {{-- <div class="page-row p-2">
                                <label class="page-label">Biro Pelaksana:</label>
                                <span class="page-value" id="biro-pelaksana"></span>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Informasi Anggaran - Kolom Kanan -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Informasi Anggaran</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="page-row p-2" id="pengenal-section">
                                <label class="page-label">Kode Pengenal (untuk Revisi):</label>
                                <span class="page-value" id="kode-pengenal"></span>
                            </div>
                            <div class="page-row p-2" id="akun-section">
                                <label class="page-label">Akun:</label>
                                <span class="page-value" id="akun">Belum diisi pengusul/pelaksana bagian</span>
                            </div>
                            <div class="page-row p-2">
                                <label class="page-label">Total Anggaran:</label>
                                <span class="page-value" id="total-anggaran"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Item Pengajuan -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Detail Item Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div id="detil-pengajuan-container">
                        <h6 class="font-weight-bold">Daftar Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            <table class="table table-items" id="tabel-detil-pengajuan">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Kode Barang</th>
                                        <th width="30%">Deskripsi</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="10%">Harga</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detil-items">
                                    <!-- Baris akan diisi oleh JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Anggaran:</th>
                                        <th id="grand-total"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div id="detil-revisi-container" style="display: none;">
                        <h6 class="font-weight-bold">Daftar Revisi Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            <table class="table table-items" id="tabel-detil-revisi">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Kode Barang</th>
                                        <th width="30%">Deskripsi</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="10%">Harga</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="revisi-items">
                                    <!-- Baris akan diisi oleh JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Anggaran Revisi:</th>
                                        <th id="grand-total-revisi"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan Tambahan -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Keterangan Tambahan</h6>
                </div>
                <div class="card-body">
                    <div class="page-row">
                        <label class="page-label">Keterangan:</label>
                        <div class="page-value p-2" id="keterangan"></div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Pendukung -->
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
                                        <input type="file" class="custom-file-input" id="dokumen-file" name="dokumen" accept="application/pdf">
                                        <label class="custom-file-label" for="dokumen-file">Pilih file PDF...</label>
                                        <div class="invalid-feedback">File harus berformat PDF dan berukuran maksimal 5MB.</div>
                                    </div>

                                    <div class="progress mb-3" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <div class="upload-guide small text-muted">
                                        <i class="fas fa-lightbulb mr-1"></i> Tip: Dokumen akan otomatis terunggah setelah Anda memilih file
                                    </div>

                                    <!-- Upload button disembunyikan karena kita implementasi auto-upload -->
                                    <button type="button" class="btn btn-primary d-none" id="upload-dokumen-btn" data-id="{{ $pengajuan->id ?? request()->route('id') }}">
                                        <i class="fas fa-upload mr-1"></i> Upload Dokumen
                                    </button>
                                </form>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Dokumen harus dalam format PDF dan berukuran maksimal 5MB.
                                </div>
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
                                        <button type="button" class="btn btn-danger" id="delete-dokumen-btn">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
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

            <style>
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

                /* Animasi Highlight untuk Area Upload saat Drag & Drop */
                .highlight-upload {
                    background-color: #e8f4ff !important;
                    border: 2px dashed #007bff !important;
                }

                /* Transisi fade untuk elemen saat tampil/sembunyi */
                .fade-element {
                    transition: opacity 0.3s ease-in-out;
                }

                .fade-element.hide {
                    opacity: 0;
                }

                .fade-element.show {
                    opacity: 1;
                }
            </style>

            <!-- Container untuk status penolakan -->
            <div id="status-penolakan" style="display: none;">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Status Penolakan</h5>
                    </div>
                    <div class="card-body">
                        <div class="page-row">
                            <label class="page-label">Status:</label>
                            <span class="page-value font-weight-bold text-danger" id="status-ditolak"></span>
                        </div>
                        <div class="page-row" id="penolakan-pelaksana-container" style="display: none;">
                            <label class="page-label">Alasan Penolakan Pelaksana:</label>
                            <div class="page-value p-3 bg-light border rounded" id="alasan-penolakan-pelaksana"></div>
                        </div>
                        <div class="page-row" id="penolakan-koordinator-container" style="display: none;">
                            <label class="page-label">Alasan Penolakan Koordinator:</label>
                            <div class="page-value p-3 bg-light border rounded" id="alasan-penolakan-koordinator"></div>
                        </div>
                    </div>
                </div>
            </div>

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
                                    <i class="fas fa-user-check text-secondary mr-2"></i>
                                    <span id="pelaksana-signed-status"><i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Pelaksana</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-user-shield text-secondary mr-2"></i>
                                    <span id="koordinator-signed-status"><i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Koordinator</span>
                                </div>
                            </div>
                            <div class="mt-3 mb-2" id="berita-acara-actions">
                                <button type="button" class="btn btn-outline-primary" id="verify-berita-acara-button">
                                    <i class="fas fa-file-signature mr-1"></i> Tanda Tangani Berita Acara
                                </button>
                                <button type="button" class="btn btn-outline-success d-none" id="download-berita-acara-signed-button">
                                    <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mb-4">
                <div id="verification-buttons">
                    <!-- Tombol verifikasi akan dirender oleh JavaScript -->
                </div>
                <div class="action-buttons">
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
                    <!-- Tombol aksi akan ditambahkan secara dinamis oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for PDF Preview and Verification -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">
                        <i class="fas fa-file-signature mr-2"></i> Verifikasi Berita Acara
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
                                    <h6 class="mb-0">Informasi Tanda Tangan</h6>
                                </div>
                                <div class="card-body">
                                    <form id="verification-form">
                                        <div class="form-group">
                                            <label for="passphrase-input-modal"><i class="fas fa-key mr-1"></i> Passphrase:</label>
                                            <input type="password" id="passphrase-input-modal" class="form-control" placeholder="Masukkan passphrase">
                                            <small class="form-text text-muted">Passphrase diperlukan untuk menandatangani dokumen secara elektronik.</small>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda telah memeriksa dokumen dengan teliti sebelum menandatanganinya.
                                        </div>
                                        <div class="verification-details mt-3">
                                            <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                            <div class="row no-gutters">
                                                <div class="col-5">Kode Pengajuan</div>
                                                <div class="col-7"><span class="font-weight-bold" id="detail-nomor-pengajuan"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Bagian Pengusul</div>
                                                <div class="col-7"><span class="font-weight-bold" id="detail-bagian-pengusul"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Tanggal</div>
                                                <div class="col-7"><span class="font-weight-bold" id="detail-tanggal"></span></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-verification-button">
                        <i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for TOR PDF Preview and Verification -->
    <div class="modal fade" id="torPreviewModal" tabindex="-1" role="dialog" aria-labelledby="torPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="torPreviewModalLabel">
                        <i class="fas fa-file-signature mr-2"></i> Verifikasi Dokumen TOR
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="pdf-container" style="height: 600px; border: 1px solid #ddd; position: relative;">
                                <div class="text-center p-5" id="tor-pdf-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat dokumen TOR...</p>
                                </div>
                                <iframe id="tor-pdf-preview" src="" style="width: 100%; height: 100%; display: none;"></iframe>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Informasi Tanda Tangan TOR</h6>
                                </div>
                                <div class="card-body">
                                    <form id="tor-verification-form">
                                        <div class="form-group">
                                            <label for="tor-passphrase-input-modal"><i class="fas fa-key mr-1"></i> Passphrase:</label>
                                            <input type="password" id="tor-passphrase-input-modal" class="form-control" placeholder="Masukkan passphrase">
                                            <small class="form-text text-muted">Passphrase diperlukan untuk menandatangani dokumen TOR secara elektronik.</small>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda telah memeriksa dokumen TOR dengan teliti sebelum menandatanganinya.
                                        </div>
                                        <div class="verification-details mt-3">
                                            <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                            <div class="row no-gutters">
                                                <div class="col-5">Kode Pengajuan</div>
                                                <div class="col-7"><span class="font-weight-bold" id="tor-detail-nomor-pengajuan"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Tahun Anggaran</div>
                                                <div class="col-7"><span class="font-weight-bold" id="tor-detail-tahun-anggaran"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Bagian Pengusul</div>
                                                <div class="col-7"><span class="font-weight-bold" id="tor-detail-bagian-pengusul"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Tanggal</div>
                                                <div class="col-7"><span class="font-weight-bold" id="tor-detail-tanggal"></span></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-tor-verification-button">
                        <i class="fas fa-signature mr-1"></i> Tanda Tangani TOR
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Lampiran PDF Preview and Verification -->
    <div class="modal fade" id="lampiranPreviewModal" tabindex="-1" role="dialog" aria-labelledby="lampiranPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="lampiranPreviewModalLabel">
                        <i class="fas fa-file-signature mr-2"></i> Verifikasi Dokumen Lampiran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="pdf-container" style="height: 600px; border: 1px solid #ddd; position: relative;">
                                <div class="text-center p-5" id="lampiran-pdf-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat dokumen Lampiran...</p>
                                </div>
                                <iframe id="lampiran-pdf-preview" src="" style="width: 100%; height: 100%; display: none;"></iframe>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Informasi Tanda Tangan Lampiran</h6>
                                </div>
                                <div class="card-body">
                                    <form id="lampiran-verification-form">
                                        <div class="form-group">
                                            <label for="lampiran-passphrase-input-modal"><i class="fas fa-key mr-1"></i> Passphrase:</label>
                                            <input type="password" id="lampiran-passphrase-input-modal" class="form-control" placeholder="Masukkan passphrase">
                                            <small class="form-text text-muted">Passphrase diperlukan untuk menandatangani dokumen Lampiran secara elektronik.</small>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda telah memeriksa dokumen Lampiran dengan teliti sebelum menandatanganinya.
                                        </div>
                                        <div class="verification-details mt-3">
                                            <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                            <div class="row no-gutters">
                                                <div class="col-5">Nomor Pengajuan</div>
                                                <div class="col-7"><span class="font-weight-bold" id="lampiran-detail-nomor-pengajuan"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Tahun Anggaran</div>
                                                <div class="col-7"><span class="font-weight-bold" id="lampiran-detail-tahun-anggaran"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Bagian Pengusul</div>
                                                <div class="col-7"><span class="font-weight-bold" id="lampiran-detail-bagian-pengusul"></span></div>
                                            </div>
                                            <div class="row no-gutters">
                                                <div class="col-5">Total Anggaran</div>
                                                <div class="col-7"><span class="font-weight-bold" id="lampiran-detail-total-anggaran"></span></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-lampiran-verification-button">
                        <i class="fas fa-signature mr-1"></i> Tanda Tangani Lampiran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Fungsi untuk menampilkan toast notification
    function showToast(icon, title) {
        Toast.fire({
            icon: icon,
            title: title
        });
    }

    function formatRupiah(angka) {
        if (typeof angka !== 'number') {
            angka = parseFloat(angka) || 0;
        }

        var number_string = angka.toString(),
            split = number_string.split('.'),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return 'Rp ' + rupiah + (split[1] ? ',' + split[1] : '');
    }

    function calculateItemTotal(price, quantity) {
        return parseFloat(price) * parseInt(quantity);
    }

    function updateTableTotals() {
        let grandTotal = 0;
        $('#tabel-detil-pengajuan tbody tr').each(function() {
            const quantity = parseInt($(this).find('td:eq(3)').text()) || 0;
            const price = parseFloat($(this).find('td:eq(4)').text().replace(/[^\d]/g, '')) || 0;
            const total = calculateItemTotal(price, quantity);
            $(this).find('td:eq(5)').text(formatRupiah(total));
            grandTotal += total;
        });
        $('#grand-total').text(formatRupiah(grandTotal));
    }

    function updateStatusInfo(status) {
        let icon = 'info-circle';
        let alertClass = 'info';
        let message = 'Silakan review pengajuan Anda sebelum dikirim ke Unit Pelaksana.';

        if (status === 'Diajukan ke Unit Pelaksana') {
            icon = 'paper-plane';
            alertClass = 'primary';
            message = 'Pengajuan telah dikirim ke Unit Pelaksana dan sedang dalam proses review.';
        } else if (status === 'Disetujui') {
            icon = 'check-circle';
            alertClass = 'success';
            message = 'Pengajuan telah disetujui.';
        } else if (status.includes('Ditolak')) {
            icon = 'times-circle';
            alertClass = 'danger';
            message = 'Pengajuan ditolak. Silakan perbaiki dan ajukan kembali.';
        }

        $('#status-info').removeClass().addClass(`alert alert-${alertClass} mb-3`);
        $('#status-info i').removeClass().addClass(`fas fa-${icon} mr-2 fa-lg`);
        $('#status-message').text(message);
    }

    function validateAndFixCalculations() {
        let totalFixed = 0;
        let needsCorrection = false;
        const isRevisiActive = $('#detil-revisi-container').is(':visible');
        const tableSelector = isRevisiActive ? '#tabel-detil-revisi tbody tr' : '#tabel-detil-pengajuan tbody tr';

        $(tableSelector).each(function() {
            if ($(this).find('td').length <= 1) return;
            const quantity = parseInt($(this).find('td:eq(3)').text().trim()) || 0;
            const priceText = $(this).find('td:eq(4)').text().trim();
            const price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
            const correctTotal = quantity * price;
            const currentTotalText = $(this).find('td:eq(5)').text().trim();
            const currentTotal = parseFloat(currentTotalText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

            if (Math.abs(correctTotal - currentTotal) > 0.1) {
                $(this).find('td:eq(5)').text(formatRupiah(correctTotal));
                needsCorrection = true;
            }

            totalFixed += correctTotal;
        });

        const grandTotalSelector = isRevisiActive ? '#grand-total-revisi' : '#grand-total';
        const currentGrandTotalText = $(grandTotalSelector).text().trim();
        const currentGrandTotal = parseFloat(currentGrandTotalText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

        if (Math.abs(totalFixed - currentGrandTotal) > 0.1) {
            $(grandTotalSelector).text(formatRupiah(totalFixed));
            $('#total-anggaran').text(formatRupiah(totalFixed));
        }

        if (needsCorrection) {
            console.log('Beberapa perhitungan total telah dikoreksi');
        }
    }

    function toggleTablesBasedOnPengajuanType(tipeType, totalAnggaranPengajuan, totalAnggaranRevisi) {
        if (tipeType.toLowerCase() === 'revisi') {
            $('#detil-pengajuan-container').hide();
            $('#detil-revisi-container').show();
            $('#total-anggaran').text(formatRupiah(totalAnggaranRevisi));
        } else {
            $('#detil-pengajuan-container').show();
            $('#detil-revisi-container').hide();
            $('#total-anggaran').text(formatRupiah(totalAnggaranPengajuan));
        }
    }

    function updateBeritaAcaraStatus(data) {
        console.log('Updating berita acara status:', data);

        // Pengecekan ganda menggunakan flag boolean dan path
        if (data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path) {
            $('#operator-signed-status').html('<i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Operator');
            $('#pelaksana-signed-status').parent().removeClass('text-secondary').addClass('text-primary');
            $('#download-berita-acara-signed-button').removeClass('d-none').data('id', data.id);
            $('#verify-berita-acara-button').addClass('d-none');
        }

        if (data.berita_acara_pelaksana_signed === true || data.berita_acara_pelaksana_signed_path) {
            $('#pelaksana-signed-status').html('<i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Pelaksana');
            $('#koordinator-signed-status').parent().removeClass('text-secondary').addClass('text-primary');
        }

        if (data.berita_acara_koordinator_signed === true || data.berita_acara_koordinator_signed_path) {
            $('#koordinator-signed-status').html('<i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Koordinator');
        }
    }

    function renderVerificationButtons(data) {
        const $verificationButtons = $('#verification-buttons');
        $verificationButtons.empty();

        const canVerify = ['Draft', 'Ditolak Pelaksana', 'Ditolak Koordinator'].includes(data.status_pengajuan);

        let statusInfoHtml = '';
        if (canVerify) {
            const torVerified = data.tor_signed_path ?
                '<i class="fas fa-check-circle text-success"></i> TOR terverifikasi' :
                '<i class="fas fa-times-circle text-danger"></i> TOR belum diverifikasi';

            const lampiranVerified = data.lampiran_signed_path ?
                '<i class="fas fa-check-circle text-success"></i> Lampiran terverifikasi' :
                '<i class="fas fa-times-circle text-danger"></i> Lampiran belum diverifikasi';

            // Tambahkan checklist untuk e-sign berita acara
            const beritaAcaraVerified = (data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path) ?
                '<i class="fas fa-check-circle text-success"></i> Berita Acara terverifikasi' :
                '<i class="fas fa-times-circle text-danger"></i> Berita Acara belum diverifikasi';

            // Tambahkan checklist untuk dokumen pendukung
            const dokumenPendukungVerified = data.dokumen_pendukung ?
                '<i class="fas fa-check-circle text-success"></i> Dokumen Pendukung terverifikasi' :
                '<i class="fas fa-times-circle text-danger"></i> Dokumen Pendukung belum diverifikasi';

            // Cek apakah semua dokumen telah terverifikasi
            const allDocumentsVerified = data.tor_signed_path &&
                                        data.lampiran_signed_path &&
                                        (data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path) &&
                                        data.dokumen_pendukung;

            statusInfoHtml = `
                <div class="verification-status mb-2">
                    <small class="d-block mb-1">${torVerified}</small>
                    <small class="d-block mb-1">${lampiranVerified}</small>
                    <small class="d-block mb-1">${beritaAcaraVerified}</small>
                    <small class="d-block mb-1">${dokumenPendukungVerified}</small>
                    ${!allDocumentsVerified ?
                        '<small class="text-info"><i class="fas fa-info-circle"></i> Verifikasi semua dokumen untuk dapat mengirim pengajuan</small>' :
                        '<small class="text-success"><i class="fas fa-check-circle"></i> Semua dokumen terverifikasi, pengajuan siap dikirim</small>'}
                </div>
            `;

            $verificationButtons.append(statusInfoHtml);

            if (!data.tor_signed_path) {
                const verifyTorBtn = $('<button>', {
                    type: 'button',
                    class: 'btn btn-outline-primary mr-2',
                    id: 'verify-tor-button'
                }).html('<i class="fas fa-file-signature mr-1"></i> Verifikasi TOR');
                $verificationButtons.append(verifyTorBtn);
            }

            if (!data.lampiran_signed_path) {
                const verifyLampiranBtn = $('<button>', {
                    type: 'button',
                    class: 'btn btn-outline-primary mr-2',
                    id: 'verify-lampiran-button'
                }).html('<i class="fas fa-file-signature mr-1"></i> Verifikasi Lampiran');
                $verificationButtons.append(verifyLampiranBtn);
            }
        }

        let actionButtons = $('.action-buttons');
        actionButtons.find('#download-tor-button, #download-lampiran-button').remove();

        if (data.tor_signed_path) {
            actionButtons.append(`
                <button type="button" class="btn btn-info mr-2" id="download-tor-button">
                    <i class="fas fa-download mr-1"></i> Download TOR
                </button>
            `);
        }

        if (data.lampiran_signed_path) {
            actionButtons.append(`
                <button type="button" class="btn btn-primary mr-2" id="download-lampiran-button">
                    <i class="fas fa-download mr-1"></i> Download Lampiran
                </button>
            `);
        }
    }

    // Load data pengajuan
    function loadPengajuan() {
        const id = {{ $pengajuan ? $pengajuan->id : request()->route('id') }};
        const baseUrl = '{{ url("pengajuanrkbmnbagiannonsbsk") }}';
        validateAndFixCalculations();

        $.ajax({
            url: `${baseUrl}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;

                    // Tambahkan debugging untuk memeriksa nilai status dokumen
                    console.log("Status dokumen:", {
                        "berita_acara_operator_signed": data.berita_acara_operator_signed,
                        "berita_acara_operator_signed_path": data.berita_acara_operator_signed_path,
                        "tor_signed_path": data.tor_signed_path,
                        "lampiran_signed_path": data.lampiran_signed_path,
                        "dokumen_pendukung": data.dokumen_pendukung
                    });

                    renderVerificationButtons(data);

                    // Populate page dengan data
                    $('#kode_pengajuan').text(data.kode_pengajuan);
                    $('#tipe-pengajuan').text(data.tipe_pengajuan);
                    $('#tahun-anggaran').text(data.tahun_anggaran);

                    // Format status dengan badge
                    let statusClass = 'badge-secondary';
                    if (data.status_pengajuan === 'Diajukan') {
                        statusClass = 'badge-info';
                    } else if (data.status_pengajuan === 'Disetujui') {
                        statusClass = 'badge-success';
                    } else if (data.status_pengajuan.includes('Ditolak')) {
                        statusClass = 'badge-danger';
                    }

                    $('#status-pengajuan').html(`<span class="badge ${statusClass}">${data.status_pengajuan}</span>`);
                    updateStatusInfo(data.status_pengajuan);

                    $('#tanggal-pengajuan').text(data.tanggal_pengajuan);
                    $('#bagian-pengusul').text(data.bagian_pengusul);
                    $('#biro-pengusul').text(data.biro_pengusul);
                    $('#bagian-pelaksana').text(data.bagian_pelaksana);
                    $('#biro-pelaksana').text(data.biro_pelaksana);
                    $('#keterangan').text(data.keterangan);
                    $('#total-anggaran').text(formatRupiah(data.total_anggaran_pengajuan));

                    // Tampilkan sesuai tipe pengajuan
                    if (data.tipe_pengajuan === 'Revisi') {
                        $('#pengenal-section').show();
                        $('#akun-section').hide();
                        $('#kode-pengenal').text(data.kode_pengenal);
                    } else {
                        $('#pengenal-section').hide();
                        $('#akun-section').show();

                        // Update akun information
                        if (data.akun && data.akun !== '-') {
                            $('#akun').text(data.akun);
                        } else {
                            $('#akun').text('Belum diisi pengusul/pelaksana bagian');
                        }
                    }

                    // Populate detail pengajuan
                    const detilPengajuan = data.detil_pengajuan;
                    let totalAnggaranPengajuan = 0;
                    if (detilPengajuan && detilPengajuan.length > 0) {
                        let html = '';

                        detilPengajuan.forEach(function(item) {
                            // Hitung total yang benar untuk tiap item
                            const itemTotal = parseFloat(item.harga) * parseInt(item.kuantitas);
                            totalAnggaranPengajuan += itemTotal;

                            html += `<tr>
                                <td>${item.no}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.deskripsi}</td>
                                <td class="text-right">${item.kuantitas}</td>
                                <td class="text-right">${formatRupiah(item.harga)}</td>
                                <td class="text-right">${formatRupiah(itemTotal)}</td>
                            </tr>`;
                        });

                        $('#detil-items').html(html);
                        $('#grand-total').text(formatRupiah(totalAnggaranPengajuan));
                    } else {
                        $('#detil-items').html('<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
                        $('#grand-total').text('Rp 0');
                    }

                    // Populate detail revisi jika ada
                    let totalAnggaranRevisi = 0;
                    const detilRevisi = data.detil_revisi;
                    if (detilRevisi && detilRevisi.length > 0) {
                        let html = '';

                        detilRevisi.forEach(function(item) {
                            // Hitung total yang benar
                            const itemTotal = parseFloat(item.harga) * parseInt(item.kuantitas);
                            totalAnggaranRevisi += itemTotal;

                            html += `<tr>
                                <td>${item.no}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.deskripsi}</td>
                                <td class="text-right">${item.kuantitas}</td>
                                <td class="text-right">${formatRupiah(item.harga)}</td>
                                <td class="text-right">${formatRupiah(itemTotal)}</td>
                            </tr>`;
                        });

                        $('#revisi-items').html(html);
                        $('#grand-total-revisi').text(formatRupiah(totalAnggaranRevisi));
                    }

                    // Update tampilan total anggaran untuk menampilkan total pengajuan
                    toggleTablesBasedOnPengajuanType(data.tipe_pengajuan, totalAnggaranPengajuan, totalAnggaranRevisi);

                    // Tampilkan status penolakan
                    if (data.status_pengajuan.includes('Ditolak')) {
                        $('#status-penolakan').show();
                        $('#status-ditolak').text(data.status_pengajuan);

                        if (data.alasan_penolakan_pelaksana) {
                            $('#penolakan-pelaksana-container').show();
                            $('#alasan-penolakan-pelaksana').text(data.alasan_penolakan_pelaksana);
                        }

                        if (data.alasan_penolakan_koordinator) {
                            $('#penolakan-koordinator-container').show();
                            $('#alasan-penolakan-koordinator').text(data.alasan_penolakan_koordinator);
                        }
                    }

                    // Set ID untuk tombol verifikasi berita acara
                    $('#verify-berita-acara-button').data('id', data.id);
                    $('#download-berita-acara-signed-button').data('id', data.id);

                    const userRole = 'operator'; // Sesuaikan dengan peran pengguna yang login (operator/pelaksana/koordinator)

                    if (userRole === 'operator') {
                        // Operator hanya dapat menandatangani jika belum ditandatangani dan status pengajuan sesuai
                        // Perbaikan: periksa keduanya, flag boolean dan path file
                        if (!(data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path) &&
                            ['Draft', 'Ditolak Pelaksana', 'Ditolak Koordinator'].includes(data.status_pengajuan)) {
                            $('#verify-berita-acara-button').removeClass('d-none');
                        } else {
                            $('#verify-berita-acara-button').addClass('d-none');
                        }
                    } else {
                        // Peran lain belum dapat menandatangani (implementasi berikutnya)
                        $('#verify-berita-acara-button').addClass('d-none');
                    }

                    // Update status tanda tangan berita acara
                    updateBeritaAcaraStatus(data);

                    // Tambahkan tombol aksi sesuai status
                    let actionButtons = $('.action-buttons');

                    // Hapus tombol aksi yang mungkin sudah ada
                    actionButtons.empty();

                    // Tambahkan tombol kembali
                    actionButtons.append(`<a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">Kembali</a>`);

                    if (['Draft', 'Ditolak Pelaksana', 'Ditolak Koordinator'].includes(data.status_pengajuan)) {
                        // Tombol Kirim - hanya muncul jika semua dokumen sudah diverifikasi
                        // Perbaikan: periksa berita acara dengan menggunakan flag boolean atau path
                        if ((data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path) &&
                            data.tor_signed_path &&
                            data.lampiran_signed_path &&
                            data.dokumen_pendukung) {

                            actionButtons.append(`<button type="button" class="btn btn-success ml-2" id="kirim-pengajuan-button">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>`);

                            // // Tambahkan indikator bahwa semua dokumen sudah diverifikasi
                            // if (!$('#verification-status-complete').length) {
                            //     actionButtons.after(`<div id="verification-status-complete" class="alert alert-success mt-3">
                            //         <i class="fas fa-check-circle mr-2"></i> Semua dokumen telah terverifikasi, pengajuan siap dikirim.
                            //     </div>`);
                            // }
                        } else {
                            // Tombol Kirim disabled dengan pesan tooltip
                            let tooltip = "Untuk mengirim pengajuan, pastikan semua dokumen telah dilengkapi: ";
                            let missingDocs = [];

                            // Perbaikan: periksa keduanya (flag boolean dan path)
                            if (!(data.berita_acara_operator_signed === true || data.berita_acara_operator_signed_path))
                                missingDocs.push("Berita Acara");
                            if (!data.tor_signed_path)
                                missingDocs.push("TOR");
                            if (!data.lampiran_signed_path)
                                missingDocs.push("Lampiran");
                            if (!data.dokumen_pendukung)
                                missingDocs.push("Dokumen Pendukung");

                            tooltip += missingDocs.join(", ");

                            actionButtons.append(`<button type="button" class="btn btn-secondary ml-2" disabled
                                data-toggle="tooltip" data-placement="top"
                                title="${tooltip}">
                                <i class="fas fa-lock mr-1"></i> Kirim Pengajuan
                            </button>`);

                            // Aktifkan tooltip
                            $('[data-toggle="tooltip"]').tooltip();

                            // // Tambahkan indikator dokumen yang belum dilengkapi
                            // if (!$('#verification-status-incomplete').length) {
                            //     actionButtons.after(`<div id="verification-status-incomplete" class="alert alert-warning mt-3">
                            //         <i class="fas fa-exclamation-triangle mr-2"></i> Dokumen yang belum dilengkapi: ${missingDocs.join(", ")}
                            //     </div>`);
                            // }
                        }

                        // Tombol Hapus
                        actionButtons.append(`<button type="button" class="btn btn-danger ml-2" id="hapus-pengajuan-button">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>`);
                    } else {
                        // Untuk status selain draft/ditolak, tetap tampilkan tombol download TOR dan Lampiran
                        if (!actionButtons.find('#download-tor-button').length) {
                            actionButtons.append(`<button type="button" class="btn btn-info ml-2" id="download-tor-button">
                                <i class="fas fa-download mr-1"></i>Download TOR
                            </button>`);
                        }

                        if (!actionButtons.find('#download-lampiran-button').length) {
                            actionButtons.append(`<button type="button" class="btn btn-primary ml-2" id="download-lampiran-button">
                                <i class="fas fa-file-alt mr-1"></i>Download Lampiran
                            </button>`);
                        }

                        // Tambahkan tombol Berita Acara sesuai status
                        if (data.berita_acara_operator_signed_path) {
                            actionButtons.append(`
                                <button type="button" class="btn btn-secondary ml-2" id="download-berita-acara-signed-button">
                                    <i class="fas fa-file-alt mr-1"></i> Download Berita Acara (Signed)
                                </button>
                            `);
                        } else {
                            actionButtons.append(`
                                <button type="button" class="btn btn-warning ml-2" id="download-berita-acara-button">
                                    <i class="fas fa-file-alt mr-1"></i> Download Berita Acara
                                </button>
                            `);
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Terjadi kesalahan saat memuat data'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data: ' + error
                });
            }
        });
    }

    // Load initial data
    loadPengajuan();

    // Event untuk tombol Kirim Pengajuan
    $(document).on('click', '#kirim-pengajuan-button', function() {
        const id = {{ $pengajuan->id ?? request()->route('id') }};
        const kirimUrl = `{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/kirim`;

        Swal.fire({
            title: 'Konfirmasi Pengajuan',
            text: 'Apakah Anda yakin ingin mengajukan ini ke Unit Pelaksana?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: kirimUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Pengajuan Berhasil!',
                                text: 'Status pengajuan telah berubah menjadi "Diajukan ke Unit Pelaksana"',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false
                            }).then((result) => {
                                localStorage.setItem('pengajuanSuccess', 'true');
                                localStorage.setItem('pengajuanId', id);
                                window.location.href = "{{ route('pengajuan.index') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat mengirim pengajuan'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error ' + xhr.status,
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim pengajuan'
                        });
                    }
                });
            }
        });
    });

    // Event untuk tombol Hapus Pengajuan
    $(document).on('click', '#hapus-pengajuan-button', function() {
        const id = {{ $pengajuan->id ?? request()->route('id') }};
        const baseUrl = "{{ url('pengajuanrkbmnbagiannonsbsk') }}";

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                window.location.href = "{{ route('pengajuan.index') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        let errorMessage = 'Terjadi kesalahan saat menghapus pengajuan';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });

    // Event handler untuk tombol Verifikasi TOR
    $(document).on('click', '#verify-tor-button', function() {
        const pengajuanId = {{ $pengajuan->id ?? 'null' }};
        if (!pengajuanId || pengajuanId === null) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID Pengajuan tidak ditemukan'
            });
            return;
        }
        $(this).data('id', pengajuanId);

        // Populate document details
        $('#tor-detail-nomor-pengajuan').text(pengajuanId);
        $('#tor-detail-tahun-anggaran').text($('#tahun-anggaran').text());
        $('#tor-detail-bagian-pengusul').text($('#bagian-pengusul').text());
        $('#tor-detail-tanggal').text(new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }));

        // Show loading spinner
        $('#tor-pdf-loading').show();
        $('#tor-pdf-preview').hide();

        // Open the modal
        $('#torPreviewModal').modal('show');

        // Generate the preview URL
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${pengajuanId}/preview-tor`;

        // Load the PDF preview
        $('#tor-pdf-preview').on('load', function() {
            $('#tor-pdf-loading').hide();
            $('#tor-pdf-preview').show();
        }).attr('src', previewUrl);

        // Clear previous passphrase
        $('#tor-passphrase-input-modal').val('');
    });

    $(document).on('click', '#confirm-tor-verification-button', function() {
        const id = $('#verify-tor-button').data('id');
        const passphrase = $('#tor-passphrase-input-modal').val();

        if (!passphrase) {
            // Show validation error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Passphrase tidak boleh kosong!'
            });
            return;
        }

        // Close the modal
        $('#torPreviewModal').modal('hide');

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menandatangani dokumen TOR',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send the verification request
        fetch(`{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/verifikasi-tor`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ passphrase })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan saat verifikasi TOR');
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload pengajuan data to update UI
                    loadPengajuan();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Verifikasi TOR gagal',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Gagal: ${error.message}`,
                confirmButtonText: 'OK'
            });
        });
    });

    // Handle TOR PDF loading error
    $(document).on('error', '#tor-pdf-preview', function() {
        $('#tor-pdf-loading').html(`
            <div class="text-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p>Gagal memuat dokumen TOR. Silakan coba lagi.</p>
                <button class="btn btn-outline-primary btn-sm mt-2" id="retry-load-tor-pdf">
                    <i class="fas fa-sync mr-1"></i> Coba Lagi
                </button>
            </div>
        `);
    });

    // Retry loading TOR PDF
    $(document).on('click', '#retry-load-tor-pdf', function() {
        const id = $('#verify-tor-button').data('id');
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/preview-tor`;

        $('#tor-pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat ulang dokumen TOR...</p>
        `);

        $('#tor-pdf-preview').attr('src', previewUrl);
    });

    // Add enter key support for TOR passphrase input
    $(document).on('keyup', '#tor-passphrase-input-modal', function(e) {
        if (e.key === 'Enter') {
            $('#confirm-tor-verification-button').click();
        }
    });

    // Clean up when TOR modal is hidden
    $('#torPreviewModal').on('hidden.bs.modal', function () {
        // Clear iframe source to prevent continued loading
        $('#tor-pdf-preview').attr('src', '');
    });

    // Add visual feedback when entering TOR passphrase
    $('#tor-passphrase-input-modal').on('input', function() {
        if ($(this).val().length > 0) {
            $('#confirm-tor-verification-button')
                .addClass('btn-success')
                .removeClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani TOR');
        } else {
            $('#confirm-tor-verification-button')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani TOR');
        }
    });

    // Add keyboard shortcut (Alt+T) to trigger TOR signing
    $(document).on('keydown', function(e) {
        // Alt+T for "Tanda Tangani"
        if (e.altKey && e.key === 't' && $('#torPreviewModal').hasClass('show')) {
            $('#confirm-tor-verification-button').click();
        }
    });

    // Show keyboard shortcut hint for TOR
    $('#torPreviewModal').on('shown.bs.modal', function() {
        const keyboardHint = $('<div class="text-muted small mt-2 text-center">')
            .text('Keyboard shortcut: Alt+T untuk menandatangani');
        $('.modal-footer', this).prepend(keyboardHint);
    });

    // Event handler untuk tombol Verifikasi Lampiran
    $(document).on('click', '#verify-lampiran-button', function() {
        const pengajuanId = {{ $pengajuan->id ?? 'null' }};
        // Gunakan ID dari variabel lokal, bukan dari data-id button
        if (!pengajuanId || pengajuanId === null) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID Pengajuan tidak ditemukan'
            });
            return;
        }
        $(this).data('id', pengajuanId);

        // Populate document details
        $('#lampiran-detail-nomor-pengajuan').text(pengajuanId);
        $('#lampiran-detail-tahun-anggaran').text($('#tahun-anggaran').text());
        $('#lampiran-detail-bagian-pengusul').text($('#bagian-pengusul').text());
        $('#lampiran-detail-total-anggaran').text($('#total-anggaran').text());

        // Show loading spinner
        $('#lampiran-pdf-loading').show();
        $('#lampiran-pdf-preview').hide();

        // Open the modal
        $('#lampiranPreviewModal').modal('show');

        // Generate the preview URL
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${pengajuanId}/preview-lampiran`;

        // Load the PDF preview
        $('#lampiran-pdf-preview').on('load', function() {
            $('#lampiran-pdf-loading').hide();
            $('#lampiran-pdf-preview').show();
        }).attr('src', previewUrl);

        // Clear previous passphrase
        $('#lampiran-passphrase-input-modal').val('');
    });

    // Add handler for the confirm button in the Lampiran modal
    $(document).on('click', '#confirm-lampiran-verification-button', function() {
        const id = $('#verify-lampiran-button').data('id');
        const passphrase = $('#lampiran-passphrase-input-modal').val();

        if (!passphrase) {
            // Show validation error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Passphrase tidak boleh kosong!'
            });
            return;
        }

        // Close the modal
        $('#lampiranPreviewModal').modal('hide');

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menandatangani dokumen Lampiran',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send the verification request
        fetch(`{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/verifikasi-lampiran`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ passphrase })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan saat verifikasi Lampiran');
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload pengajuan data to update UI
                    loadPengajuan();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Verifikasi Lampiran gagal',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Gagal: ${error.message}`,
                confirmButtonText: 'OK'
            });
        });
    });

    // Handle Lampiran PDF loading error
    $(document).on('error', '#lampiran-pdf-preview', function() {
        $('#lampiran-pdf-loading').html(`
            <div class="text-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p>Gagal memuat dokumen Lampiran. Silakan coba lagi.</p>
                <button class="btn btn-outline-primary btn-sm mt-2" id="retry-load-lampiran-pdf">
                    <i class="fas fa-sync mr-1"></i> Coba Lagi
                </button>
            </div>
        `);
    });

    // Retry loading Lampiran PDF
    $(document).on('click', '#retry-load-lampiran-pdf', function() {
        const id = $('#verify-lampiran-button').data('id');
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/preview-lampiran`;

        $('#lampiran-pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat ulang dokumen Lampiran...</p>
        `);

        $('#lampiran-pdf-preview').attr('src', previewUrl);
    });

    // Add enter key support for Lampiran passphrase input
    $(document).on('keyup', '#lampiran-passphrase-input-modal', function(e) {
        if (e.key === 'Enter') {
            $('#confirm-lampiran-verification-button').click();
        }
    });

    // Clean up when Lampiran modal is hidden
    $('#lampiranPreviewModal').on('hidden.bs.modal', function () {
        // Clear iframe source to prevent continued loading
        $('#lampiran-pdf-preview').attr('src', '');
    });

    // Add visual feedback when entering Lampiran passphrase
    $('#lampiran-passphrase-input-modal').on('input', function() {
        if ($(this).val().length > 0) {
            $('#confirm-lampiran-verification-button')
                .addClass('btn-success')
                .removeClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Lampiran');
        } else {
            $('#confirm-lampiran-verification-button')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Lampiran');
        }
    });

    // Add keyboard shortcut (Alt+T) to trigger Lampiran signing
    $(document).on('keydown', function(e) {
        // Alt+T for "Tanda Tangani"
        if (e.altKey && e.key === 't' && $('#lampiranPreviewModal').hasClass('show')) {
            $('#confirm-lampiran-verification-button').click();
        }
    });

    // Show keyboard shortcut hint for Lampiran
    $('#lampiranPreviewModal').on('shown.bs.modal', function() {
        const keyboardHint = $('<div class="text-muted small mt-2 text-center">')
            .text('Keyboard shortcut: Alt+T untuk menandatangani');
        $('.modal-footer', this).prepend(keyboardHint);
    });

    // Event handler untuk tombol Download TOR
    $(document).on('click', '#download-tor-button', function() {
        const id = {{ $pengajuan->id ?? request()->route('id') }};
        const downloadUrl = `{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/download-tor`;

        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen TOR',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        window.open(downloadUrl, '_blank');

        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Event handler untuk tombol Download Lampiran
    $(document).on('click', '#download-lampiran-button', function() {
        const id = {{ $pengajuan->id ?? request()->route('id') }};
        const downloadUrl = `{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/download-lampiran`;

        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen lampiran',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        window.open(downloadUrl, '_blank');

        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Event handler untuk tombol Verify Berita Acara
    $(document).on('click', '#verify-berita-acara-button', function() {
        const id = $(this).data('id');

        // Populate document details
        $('#detail-nomor-pengajuan').text(id);
        $('#detail-bagian-pengusul').text($('#bagian-pengusul').text());
        $('#detail-tanggal').text(new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }));

        // Show loading spinner
        $('#pdf-loading').show();
        $('#pdf-preview').hide();

        // Open the modal
        $('#pdfPreviewModal').modal('show');

        // Generate the preview URL (this should be created as a new route - see explanation below)
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/preview-berita-acara`;

        // Load the PDF preview
        $('#pdf-preview').on('load', function() {
            $('#pdf-loading').hide();
            $('#pdf-preview').show();
        }).attr('src', previewUrl);

        // Clear previous passphrase
        $('#passphrase-input-modal').val('');
    });

    // Add handler for the confirm button in the modal
    $(document).on('click', '#confirm-verification-button', function() {
        const id = $('#verify-berita-acara-button').data('id');
        const passphrase = $('#passphrase-input-modal').val();

        if (!passphrase) {
            // Show validation error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Passphrase tidak boleh kosong!'
            });
            return;
        }

        // Close the modal
        $('#pdfPreviewModal').modal('hide');

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menandatangani dokumen',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send the verification request
        fetch(`{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/verifikasi-berita-acara`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ passphrase })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan saat verifikasi');
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload pengajuan data to update UI
                    loadPengajuan();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Verifikasi gagal',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Gagal: ${error.message}`,
                confirmButtonText: 'OK'
            });
        });
    });

    // Event handler untuk tombol Download Berita Acara Tertandatangani
    $(document).on('click', '#download-berita-acara-signed-button', function() {
        const id = {{ $pengajuan->id ?? request()->route('id') }};
        const downloadUrl = `{{ url('pengajuanrkbmnbagiannonsbsk') }}/${id}/download-berita-acara-signed`;

        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen Berita Acara tertandatangani',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        window.open(downloadUrl, '_blank');

        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Handle PDF loading error
    $(document).on('error', '#pdf-preview', function() {
        $('#pdf-loading').html(`
            <div class="text-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p>Gagal memuat dokumen. Silakan coba lagi.</p>
                <button class="btn btn-outline-primary btn-sm mt-2" id="retry-load-pdf">
                    <i class="fas fa-sync mr-1"></i> Coba Lagi
                </button>
            </div>
        `);
    });

    // Retry loading PDF
    $(document).on('click', '#retry-load-pdf', function() {
        const id = $('#verify-berita-acara-button').data('id');
        const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/preview-berita-acara`;

        $('#pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat ulang dokumen...</p>
        `);

        $('#pdf-preview').attr('src', previewUrl);
    });

    // Add enter key support for passphrase input
    $(document).on('keyup', '#passphrase-input-modal', function(e) {
        if (e.key === 'Enter') {
            $('#confirm-verification-button').click();
        }
    });

    // Show tooltip about the signing position
    // let signatureTooltipTimeout;
    // $('#pdfPreviewModal').on('shown.bs.modal', function () {
    //     // Clear any existing tooltips
    //     clearTimeout(signatureTooltipTimeout);
    //
    //     // Add a signature position indicator
    //     setTimeout(() => {
    //         const signaturePosition = $('<div class="signature-position">')
    //             .html('<i class="fas fa-signature mr-1"></i> Posisi tanda tangan Anda');
    //
    //         $('.pdf-container').append(signaturePosition);
    //
    //         signaturePosition.fadeIn('slow');
    //
    //         // Remove the indicator after 5 seconds
    //         signatureTooltipTimeout = setTimeout(() => {
    //             signaturePosition.fadeOut('slow', function() {
    //                 $(this).remove();
    //             });
    //         }, 5000);
    //     }, 2000);
    // });

    // Clean up when modal is hidden
    $('#pdfPreviewModal').on('hidden.bs.modal', function () {
        // Clear signature position indicator
        clearTimeout(signatureTooltipTimeout);
        $('.signature-position').remove();

        // Clear iframe source to prevent continued loading
        $('#pdf-preview').attr('src', '');
    });

    // Add visual feedback when entering passphrase
    $('#passphrase-input-modal').on('input', function() {
        if ($(this).val().length > 0) {
            $('#confirm-verification-button')
                .addClass('btn-success')
                .removeClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen');
        } else {
            $('#confirm-verification-button')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen');
        }
    });

    // Add keyboard shortcut (Alt+T) to trigger signing
    $(document).on('keydown', function(e) {
        // Alt+T for "Tanda Tangani"
        if (e.altKey && e.key === 't' && $('#pdfPreviewModal').hasClass('show')) {
            $('#confirm-verification-button').click();
        }
    });

    // Show keyboard shortcut hint
    $('#pdfPreviewModal').on('shown.bs.modal', function() {
        const keyboardHint = $('<div class="text-muted small mt-2 text-center">')
            .text('Keyboard shortcut: Alt+T untuk menandatangani');
        $('.modal-footer').prepend(keyboardHint);
    });

    $('#dokumen-file').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.custom-file-label').html(fileName);

            // Validate file type and size
            const fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];

                // Check file type
                if (file.type !== 'application/pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Dokumen harus dalam format PDF.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }

                // Check file size (5MB = 5 * 1024 * 1024)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }
            }
        } else {
            $(this).next('.custom-file-label').html('Pilih file PDF...');
        }
    });

    // Upload document
    $('#upload-dokumen-btn').on('click', function() {
        const id = $(this).data('id');
        const fileInput = $('#dokumen-file')[0];

        if (!fileInput.files || !fileInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih File',
                text: 'Silakan pilih file PDF untuk diupload.'
            });
            return;
        }

        // Create FormData object
        const formData = new FormData();
        formData.append('dokumen', fileInput.files[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show loading progress
        const $progress = $('.progress');
        const $progressBar = $('.progress-bar');
        $progress.show();

        // Disable the upload button
        $('#upload-dokumen-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...');

        // Send Ajax request
        $.ajax({
            url: `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/upload-dokumen`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $progressBar.width(percent + '%').attr('aria-valuenow', percent);
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    // Reset progress bar
                    $progress.hide();
                    $progressBar.width('0%').attr('aria-valuenow', 0);

                    // Reset file input
                    fileInput.value = '';
                    $('.custom-file-label').html('Pilih file PDF...');

                    // Update UI to show the document is uploaded
                    updateDokumenUI(response);

                    // Show success message
                    showToast('success', response.message);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Upload',
                        text: response.message || 'Terjadi kesalahan saat upload dokumen.'
                    });
                }

                // Re-enable upload button
                $('#upload-dokumen-btn').prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Upload Dokumen');
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', xhr.responseText);

                // Re-enable upload button
                $('#upload-dokumen-btn').prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Upload Dokumen');

                // Reset progress bar
                $progress.hide();
                $progressBar.width('0%').attr('aria-valuenow', 0);

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: 'Terjadi kesalahan saat upload dokumen: ' + (xhr.responseJSON?.message || error)
                });
            }
        });
    });

    // Delete document
    $(document).on('click', '#delete-dokumen-btn', function() {
        const id = $('#upload-dokumen-btn').data('id');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus dokumen pendukung ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghapus dokumen',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send Ajax request
                $.ajax({
                    url: `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/delete-dokumen`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI to remove document link
                            $('#no-dokumen-text').text('Belum ada dokumen').show();
                            $('#dokumen-actions').hide();

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Dokumen berhasil dihapus.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menghapus dokumen.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr.responseText);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus dokumen: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });

    // Download document
    $(document).on('click', '#download-dokumen-btn', function(e) {
        e.preventDefault();
        const id = $('#upload-dokumen-btn').data('id');
        const downloadUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/download-dokumen`;

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Menyiapkan dokumen untuk didownload',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Open download URL in new window
        window.open(downloadUrl, '_blank');

        // Close loading dialog after 1.5 seconds
        setTimeout(() => {
            Swal.close();
        }, 1500);
    });

    // Function to update document UI based on response
    function updateDokumenUI(response) {
        if (response.success && response.file_path) {
            $('#no-dokumen-text').text('Dokumen pendukung tersedia').show();
            $('#dokumen-actions').show();

            // Update download link
            $('#download-dokumen-btn').attr('href', `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${$('#upload-dokumen-btn').data('id')}/download-dokumen`);
        }
    }

    // Check if document already exists on page load
    // function checkExistingDocument() {
    //     const id = $('#upload-dokumen-btn').data('id');

    //     $.ajax({
    //         url: `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/check-dokumen`,
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.has_document) {
    //                 $('#no-dokumen-text').text('Dokumen pendukung tersedia').show();
    //                 $('#dokumen-actions').show();

    //                 // Update download link
    //                 $('#download-dokumen-btn').attr('href', `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/download-dokumen`);
    //             } else {
    //                 $('#no-dokumen-text').text('Belum ada dokumen').show();
    //                 $('#dokumen-actions').hide();
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error checking document:', xhr.responseText);
    //         }
    //     });
    // }

    // // Call this function on page load
    // // Call this function on page load
    // checkExistingDocument();
});

$(document).ready(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // ID pengajuan dari data attribute
    const pengajuanId = $('#upload-dokumen-btn').data('id');
    const baseUrl = window.location.origin;
    const apiPath = `/pengajuanrkbmnbagiannonsbsk/${pengajuanId}`;

    // Function untuk menampilkan toast notification
    function showToast(icon, title) {
        Toast.fire({
            icon: icon,
            title: title
        });
    }

    // Fungsi untuk memperbarui pesan status dokumen
    function updateDocumentStatusMessage(status, message) {
        const $statusAlert = $('.document-status-alert');

        // Reset classes
        $statusAlert.removeClass('alert-primary alert-success alert-warning alert-danger');

        // Tambahkan class sesuai status
        if (status === 'success') {
            $statusAlert.addClass('alert-success success');
            $statusAlert.find('i').removeClass().addClass('fas fa-check-circle mr-2 fa-lg');
        } else if (status === 'warning') {
            $statusAlert.addClass('alert-warning warning');
            $statusAlert.find('i').removeClass().addClass('fas fa-exclamation-triangle mr-2 fa-lg');
        } else if (status === 'danger') {
            $statusAlert.addClass('alert-danger');
            $statusAlert.find('i').removeClass().addClass('fas fa-times-circle mr-2 fa-lg');
        } else {
            $statusAlert.addClass('alert-primary');
            $statusAlert.find('i').removeClass().addClass('fas fa-info-circle mr-2 fa-lg');
        }

        // Update pesan
        $('#document-status-message').text(message);
    }

    // Fungsi untuk menampilkan preview dokumen
    function showDocumentPreview(url) {
        // Tampilkan loading
        $('#pdf-preview-container').show();
        $('.pdf-loading').show();
        $('#pdf-preview-frame').hide();

        // Set URL untuk iframe
        $('#pdf-preview-frame').attr('src', url);

        // Event handler untuk iframe load
        $('#pdf-preview-frame').on('load', function() {
            $('.pdf-loading').hide();
            $('#pdf-preview-frame').show();
        });

        // Event handler untuk error loading
        $('#pdf-preview-frame').on('error', function() {
            $('.pdf-loading').html(`
                <div class="text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p>Gagal memuat dokumen. Silakan coba lagi.</p>
                    <button class="btn btn-outline-primary btn-sm mt-2" id="retry-preview-btn">
                        <i class="fas fa-sync mr-1"></i> Coba Lagi
                    </button>
                </div>
            `);
        });
    }

    // Toggle preview dokumen
    $(document).on('click', '#toggle-preview-btn', function() {
        const $previewContainer = $('#pdf-preview-container');
        const $btnIcon = $(this).find('i');

        if ($previewContainer.is(':visible')) {
            $previewContainer.slideUp(300);
            $btnIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).html('<i class="fas fa-eye mr-1"></i> Preview');
        } else {
            // Jika dokumen belum dimuat, muat sekarang
            if ($('#pdf-preview-frame').attr('src') === '') {
                const previewUrl = `${baseUrl}${apiPath}/preview-dokumen`;
                showDocumentPreview(previewUrl);
            }

            $previewContainer.slideDown(300);
            $btnIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).html('<i class="fas fa-eye-slash mr-1"></i> Tutup');
        }
    });

    // Retry preview jika gagal
    $(document).on('click', '#retry-preview-btn', function() {
        const previewUrl = `${baseUrl}${apiPath}/preview-dokumen`;
        $('.pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat dokumen...</p>
        `);
        $('#pdf-preview-frame').attr('src', previewUrl);
    });

    // Fungsi untuk memperbarui UI dokumen
    function updateDocumentUI(response) {
        if (response.success && response.file_path) {
            // Update status
            updateDocumentStatusMessage('success', 'Dokumen pendukung berhasil diunggah dan siap digunakan.');

            // Perbarui informasi dokumen
            $('#no-document-placeholder').hide();
            $('#document-info').show();

            // Ekstrak nama file dari path
            const fileName = response.file_name || response.file_path.split('/').pop();
            $('#document-name').text(fileName);

            // Tampilkan waktu upload jika ada
            if (response.upload_time) {
                $('#upload-datetime').text(response.upload_time);
            } else {
                $('#upload-datetime').text('Baru saja');
            }

            // Update link download
            $('#download-dokumen-btn').attr('href', `${baseUrl}${apiPath}/download-dokumen`);

            // Reset preview jika ada
            $('#pdf-preview-container').hide();
            $('#pdf-preview-frame').attr('src', '');
        }
    }

    // Inisialisasi drag and drop
    function initDragAndDrop() {
        const $uploadArea = $('.document-upload-area');

        // Prevent default untuk drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area saat drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            $uploadArea.addClass('highlight-upload');
        }

        function unhighlight() {
            $uploadArea.removeClass('highlight-upload');
        }

        // Handle drop
        $uploadArea[0].addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                // Process only the first file
                const file = files[0];

                // Check if file is PDF
                if (file.type === 'application/pdf') {
                    // Update label
                    $('.custom-file-label').text(file.name);

                    // Set file to input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    $('#dokumen-file')[0].files = dataTransfer.files;

                    // Trigger upload
                    uploadDocument(file);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Dokumen harus dalam format PDF.'
                    });
                }
            }
        }
    }

    // Auto-upload saat file dipilih
    $('#dokumen-file').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.custom-file-label').html(fileName);

            // Validate file type and size
            const fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];

                // Check file type
                if (file.type !== 'application/pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Dokumen harus dalam format PDF.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }

                // Check file size (5MB = 5 * 1024 * 1024)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }

                // Auto-upload jika validasi berhasil
                uploadDocument(file);
            }
        } else {
            $(this).next('.custom-file-label').html('Pilih file PDF...');
        }
    });

    // Fungsi untuk mengunggah dokumen
    function uploadDocument(file) {
        const formData = new FormData();
        formData.append('dokumen', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show loading progress
        const $progress = $('.progress');
        const $progressBar = $('.progress-bar');
        $progress.show();

        // Update status message
        updateDocumentStatusMessage('primary', 'Sedang mengunggah dokumen, mohon tunggu...');

        // Disable the file input temporarily
        $('#dokumen-file').prop('disabled', true);

        // Send Ajax request
        $.ajax({
            url: `${baseUrl}${apiPath}/upload-dokumen`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $progressBar.width(percent + '%').attr('aria-valuenow', percent);
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    // Reset progress bar
                    $progress.hide();
                    $progressBar.width('0%').attr('aria-valuenow', 0);

                    // Reset file input
                    $('#dokumen-file').val('');
                    $('.custom-file-label').html('Pilih file PDF...');

                    // Update UI to show the document is uploaded
                    updateDocumentUI(response);

                    // Show success message
                    showToast('success', response.message || 'Dokumen berhasil diunggah');
                } else {
                    $progress.hide();
                    $progressBar.width('0%').attr('aria-valuenow', 0);

                    // Update status message
                    updateDocumentStatusMessage('warning', 'Gagal mengunggah dokumen. Silakan coba lagi.');

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Upload',
                        text: response.message || 'Terjadi kesalahan saat upload dokumen.'
                    });
                }

                // Re-enable the file input
                $('#dokumen-file').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', xhr.responseText);

                // Reset progress bar
                $progress.hide();
                $progressBar.width('0%').attr('aria-valuenow', 0);

                // Update status message
                updateDocumentStatusMessage('danger', 'Terjadi kesalahan saat upload dokumen.');

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: 'Terjadi kesalahan saat upload dokumen: ' + (xhr.responseJSON?.message || error)
                });

                // Re-enable the file input
                $('#dokumen-file').prop('disabled', false);
            }
        });
    }

    // Delete document
    $(document).on('click', '#delete-dokumen-btn', function() {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus dokumen pendukung ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghapus dokumen',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send Ajax request
                $.ajax({
                    url: `${baseUrl}${apiPath}/delete-dokumen`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reset UI
                            $('#document-info').hide();
                            $('#no-document-placeholder').show();
                            $('#pdf-preview-container').hide();
                            $('#pdf-preview-frame').attr('src', '');

                            // Update status message
                            updateDocumentStatusMessage('warning', 'Dokumen pendukung telah dihapus. Silakan unggah dokumen baru.');

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Dokumen berhasil dihapus.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menghapus dokumen.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr.responseText);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus dokumen: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });

    // Cek dokumen yang sudah ada saat halaman dimuat
    function checkExistingDocument() {
        $.ajax({
            url: `${baseUrl}${apiPath}/check-dokumen`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.has_document) {
                    // Update status
                    updateDocumentStatusMessage('success', 'Dokumen pendukung tersedia dan siap digunakan.');

                    // Show document info
                    $('#no-document-placeholder').hide();
                    $('#document-info').show();

                    // Update document name if available
                    if (response.file_name) {
                        $('#document-name').text(response.file_name);
                    } else {
                        $('#document-name').text('dokumen_pendukung.pdf');
                    }

                    // Update upload time if available
                    if (response.upload_time) {
                        $('#upload-datetime').text(response.upload_time);
                    } else {
                        $('#upload-datetime').text('-');
                    }

                    // Update download link
                    $('#download-dokumen-btn').attr('href', `${baseUrl}${apiPath}/download-dokumen`);
                } else {
                    // No document, show placeholder
                    $('#no-document-placeholder').show();
                    $('#document-info').hide();

                    // Update status message
                    updateDocumentStatusMessage('warning', 'Belum ada dokumen pendukung. Silakan unggah dokumen untuk melengkapi pengajuan Anda.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking document:', xhr.responseText);
                updateDocumentStatusMessage('danger', 'Gagal memeriksa status dokumen. Silakan refresh halaman.');
            }
        });
    }

    // Initialize document area
    function initDocumentArea() {
        // Check existing document
        checkExistingDocument();

        // Initialize drag and drop
        initDragAndDrop();
    }

    // Initialize on page load
    initDocumentArea();
});
// Script untuk menangani unggah dokumen
$(document).ready(function() {
    // Konfigurasi URL yang benar menggunakan helper Laravel
    const pengajuanId = {{ $pengajuan->id ?? request()->route('id') }};

    // Toast notification
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Fungsi untuk menampilkan toast notification
    function showToast(icon, title) {
        Toast.fire({
            icon: icon,
            title: title
        });
    }

    // Fungsi untuk memperbarui pesan status dokumen
    function updateDocumentStatusMessage(status, message) {
        const $statusAlert = $('.document-status-alert');

        // Reset classes
        $statusAlert.removeClass('alert-primary alert-success alert-warning alert-danger');

        // Tambahkan class sesuai status
        if (status === 'success') {
            $statusAlert.addClass('alert-success success');
            $statusAlert.find('i').removeClass().addClass('fas fa-check-circle mr-2 fa-lg');
        } else if (status === 'warning') {
            $statusAlert.addClass('alert-warning warning');
            $statusAlert.find('i').removeClass().addClass('fas fa-exclamation-triangle mr-2 fa-lg');
        } else if (status === 'danger') {
            $statusAlert.addClass('alert-danger');
            $statusAlert.find('i').removeClass().addClass('fas fa-times-circle mr-2 fa-lg');
        } else {
            $statusAlert.addClass('alert-primary');
            $statusAlert.find('i').removeClass().addClass('fas fa-info-circle mr-2 fa-lg');
        }

        // Update pesan
        $('#document-status-message').text(message);
    }

    // Fungsi untuk menampilkan preview dokumen
    function showDocumentPreview(url) {
        // Tampilkan loading
        $('#pdf-preview-container').show();
        $('.pdf-loading').show();
        $('#pdf-preview-frame').hide();

        // Set URL untuk iframe
        $('#pdf-preview-frame').attr('src', url);

        // Event handler untuk iframe load
        $('#pdf-preview-frame').on('load', function() {
            $('.pdf-loading').hide();
            $('#pdf-preview-frame').show();
        });

        // Event handler untuk error loading
        $('#pdf-preview-frame').on('error', function() {
            $('.pdf-loading').html(`
                <div class="text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p>Gagal memuat dokumen. Silakan coba lagi.</p>
                    <button class="btn btn-outline-primary btn-sm mt-2" id="retry-preview-btn">
                        <i class="fas fa-sync mr-1"></i> Coba Lagi
                    </button>
                </div>
            `);
        });
    }

    // Toggle preview dokumen
    $(document).on('click', '#toggle-preview-btn', function() {
        const $previewContainer = $('#pdf-preview-container');
        const $btnIcon = $(this).find('i');
        const previewUrl = "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/preview-dokumen";

        if ($previewContainer.is(':visible')) {
            $previewContainer.slideUp(300);
            $btnIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).html('<i class="fas fa-eye mr-1"></i> Preview');
        } else {
            // Jika dokumen belum dimuat, muat sekarang
            if ($('#pdf-preview-frame').attr('src') === '') {
                showDocumentPreview(previewUrl);
            }

            $previewContainer.slideDown(300);
            $btnIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).html('<i class="fas fa-eye-slash mr-1"></i> Tutup');
        }
    });

    // Retry preview jika gagal
    $(document).on('click', '#retry-preview-btn', function() {
        const previewUrl = "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/preview-dokumen";
        $('.pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat dokumen...</p>
        `);
        $('#pdf-preview-frame').attr('src', previewUrl);
    });

    // Fungsi untuk memperbarui UI dokumen
    function updateDocumentUI(response) {
        if (response.success && response.file_path) {
            // Update status
            updateDocumentStatusMessage('success', 'Dokumen pendukung berhasil diunggah dan siap digunakan.');

            // Perbarui informasi dokumen
            $('#no-document-placeholder').hide();
            $('#document-info').show();

            // Ekstrak nama file dari path
            const fileName = response.file_name || response.file_path.split('/').pop();
            $('#document-name').text(fileName);

            // Tampilkan waktu upload jika ada
            if (response.upload_time) {
                $('#upload-datetime').text(response.upload_time);
            } else {
                $('#upload-datetime').text('Baru saja');
            }

            // Update link download
            $('#download-dokumen-btn').attr('href', "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/download-dokumen");

            // Reset preview jika ada
            $('#pdf-preview-container').hide();
            $('#pdf-preview-frame').attr('src', '');

            // Refresh data pengajuan untuk memperbarui status dokumen
            loadPengajuan();
        }
    }

    // Inisialisasi drag and drop
    function initDragAndDrop() {
        const $uploadArea = $('.document-upload-area');

        // Prevent default untuk drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area saat drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            $uploadArea[0].addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            $uploadArea.addClass('highlight-upload');
        }

        function unhighlight() {
            $uploadArea.removeClass('highlight-upload');
        }

        // Handle drop
        $uploadArea[0].addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                // Process only the first file
                const file = files[0];

                // Check if file is PDF
                if (file.type === 'application/pdf') {
                    // Update label
                    $('.custom-file-label').text(file.name);

                    // Set file to input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    $('#dokumen-file')[0].files = dataTransfer.files;

                    // Trigger upload
                    uploadDocument(file);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Dokumen harus dalam format PDF.'
                    });
                }
            }
        }
    }

    // Auto-upload saat file dipilih
    $('#dokumen-file').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.custom-file-label').html(fileName);

            // Validate file type and size
            const fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];

                // Check file type
                if (file.type !== 'application/pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Dokumen harus dalam format PDF.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }

                // Check file size (5MB = 5 * 1024 * 1024)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB.'
                    });
                    fileInput.value = '';
                    $(this).next('.custom-file-label').html('Pilih file PDF...');
                    return;
                }

                // Auto-upload jika validasi berhasil
                uploadDocument(file);
            }
        } else {
            $(this).next('.custom-file-label').html('Pilih file PDF...');
        }
    });

    // Fungsi untuk mengunggah dokumen
    function uploadDocument(file) {
        const formData = new FormData();
        formData.append('dokumen', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show loading progress
        const $progress = $('.progress');
        const $progressBar = $('.progress-bar');
        $progress.show();

        // Update status message
        updateDocumentStatusMessage('primary', 'Sedang mengunggah dokumen, mohon tunggu...');

        // Disable the file input temporarily
        $('#dokumen-file').prop('disabled', true);

        // Log untuk debugging
        console.log('Mengunggah dokumen ke:', "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/upload-dokumen");

        // Send Ajax request
        $.ajax({
            url: "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/upload-dokumen",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $progressBar.width(percent + '%').attr('aria-valuenow', percent);
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                console.log('Response upload:', response);

                if (response.success) {
                    // Reset progress bar
                    $progress.hide();
                    $progressBar.width('0%').attr('aria-valuenow', 0);

                    // Reset file input
                    $('#dokumen-file').val('');
                    $('.custom-file-label').html('Pilih file PDF...');

                    // Update UI to show the document is uploaded
                    updateDocumentUI(response);

                    // Show success message
                    showToast('success', response.message || 'Dokumen berhasil diunggah');
                } else {
                    $progress.hide();
                    $progressBar.width('0%').attr('aria-valuenow', 0);

                    // Update status message
                    updateDocumentStatusMessage('warning', 'Gagal mengunggah dokumen. Silakan coba lagi.');

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Upload',
                        text: response.message || 'Terjadi kesalahan saat upload dokumen.'
                    });
                }

                // Re-enable the file input
                $('#dokumen-file').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);

                // Reset progress bar
                $progress.hide();
                $progressBar.width('0%').attr('aria-valuenow', 0);

                // Update status message
                updateDocumentStatusMessage('danger', 'Terjadi kesalahan saat upload dokumen.');

                // Detailed error message
                let errorMessage = 'Terjadi kesalahan saat upload dokumen';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status) {
                    errorMessage += ' (Status: ' + xhr.status + ')';
                }

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: errorMessage
                });

                // Re-enable the file input
                $('#dokumen-file').prop('disabled', false);
            }
        });
    }

    // Delete document
    $(document).on('click', '#delete-dokumen-btn', function() {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus dokumen pendukung ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghapus dokumen',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send Ajax request
                $.ajax({
                    url: "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/delete-dokumen",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reset UI
                            $('#document-info').hide();
                            $('#no-document-placeholder').show();
                            $('#pdf-preview-container').hide();
                            $('#pdf-preview-frame').attr('src', '');

                            // Update status message
                            updateDocumentStatusMessage('warning', 'Dokumen pendukung telah dihapus. Silakan unggah dokumen baru.');

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Dokumen berhasil dihapus.'
                            });

                            // Refresh data pengajuan
                            loadPengajuan();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menghapus dokumen.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr.responseText);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus dokumen: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });

    // Cek dokumen yang sudah ada saat halaman dimuat
    function checkExistingDocument() {
        console.log('Memeriksa dokumen yang ada...');

        $.ajax({
            url: "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/check-dokumen",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Hasil pemeriksaan dokumen:', response);

                if (response.success && response.has_document) {
                    // Update status
                    updateDocumentStatusMessage('success', 'Dokumen pendukung tersedia dan siap digunakan.');

                    // Show document info
                    $('#no-document-placeholder').hide();
                    $('#document-info').show();

                    // Update document name if available
                    if (response.file_name) {
                        $('#document-name').text(response.file_name);
                    } else {
                        $('#document-name').text('dokumen_pendukung.pdf');
                    }

                    // Update upload time if available
                    if (response.upload_time) {
                        $('#upload-datetime').text(response.upload_time);
                    } else {
                        $('#upload-datetime').text('-');
                    }

                    // Update download link
                    $('#download-dokumen-btn').attr('href', "{{ url('pengajuanrkbmnbagiannonsbsk') }}/" + pengajuanId + "/download-dokumen");
                } else {
                    // No document, show placeholder
                    $('#no-document-placeholder').show();
                    $('#document-info').hide();

                    // Update status message
                    updateDocumentStatusMessage('warning', 'Belum ada dokumen pendukung. Silakan unggah dokumen untuk melengkapi pengajuan Anda.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking document:', xhr.responseText);
                updateDocumentStatusMessage('danger', 'Gagal memeriksa status dokumen. Silakan refresh halaman.');
            }
        });
    }

    // Initialize document area
    function initDocumentArea() {
        // Check existing document
        checkExistingDocument();

        // Initialize drag and drop
        initDragAndDrop();
    }

    // Initialize on page load
    initDocumentArea();
});
</script>
@endsection
