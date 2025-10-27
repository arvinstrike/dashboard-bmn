{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmnreguler/ReviewPageReguler.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Review Pengajuan Reguler</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pengajuan.reguler.index') }}">Pengajuan Reguler</a>
                            </li>
                            <li class="breadcrumb-item active">Review</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="alert alert-info mb-3" id="status-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle mr-2 fa-lg"></i>
                        <span id="status-message">Silakan review pengajuan reguler sebelum dikirim ke Unit Pelaksana.</span>
                    </div>
                </div>

                <style>
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

                    .document-upload-area {
                        background-color: #f8f9fa;
                        border-radius: 4px;
                        transition: all 0.3s ease;
                    }

                    .document-upload-area:hover {
                        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

                    /* Action Footer Styling */
                    .action-footer {
                        background-color: #f8f9fa;
                        border-top: 1px solid #dee2e6;
                        padding: 20px;
                        margin-top: 30px;
                        border-radius: 0 0 8px 8px;
                        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
                    }

                    .action-footer .btn {
                        margin: 0 5px;
                        min-width: 120px;
                    }

                    @media (max-width: 768px) {
                        .action-footer .btn {
                            width: 100%;
                            margin: 5px 0;
                        }

                        .action-footer .btn-group-mobile {
                            display: flex;
                            flex-direction: column;
                        }
                    }
                </style>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Informasi Pengajuan</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="page-row p-2">
                                    <label class="page-label">Nomor Pengajuan:</label>
                                    <span class="page-value" id="id"></span>
                                </div>
                                <div class="page-row p-2">
                                    <label class="page-label">Tipe Pengajuan:</label>
                                    <span class="page-value" id="tipe-pengajuan"></span>
                                </div>
                                <div class="page-row p-2">
                                    <label class="page-label">Jenis Formulir:</label>
                                    <span class="page-value">Pengajuan Reguler</span>
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
                            </div>
                        </div>
                    </div>

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

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Daftar Barang</h6>
                    </div>
                    <div class="card-body">
                        <div id="detil-pengajuan-container">
                            {{-- <h6 class="font-weight-bold">Daftar Barang</h6> --}}
                            <div class="table-responsive">
                                <table class="table table-items" id="tabel-detil-pengajuan">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Kode Barang</th>
                                            <th width="20%">Deskripsi</th>
                                            <th width="25%">Keterangan</th>
                                            <th width="10%">Kuantitas</th>
                                            <th width="10%">Harga</th>
                                            <th width="10%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detil-items">
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
                            <div class="table-responsive">
                                <table class="table table-items" id="tabel-detil-revisi">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Kode Barang</th>
                                            <th width="20%">Deskripsi</th>
                                            <th width="25%">Keterangan</th>
                                            <th width="10%">Kuantitas</th>
                                            <th width="10%">Harga</th>
                                            <th width="10%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="revisi-items">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Total Anggaran:</th>
                                            <th id="grand-total-revisi"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Tujuan Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <div class="page-row">
                            {{-- <label class="page-label">Keterangan:</label> --}}
                            <div class="page-value p-2" id="keterangan"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-file-upload mr-2"></i>Dokumen Pendukung</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary document-status-alert mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle mr-2 fa-lg"></i>
                                <span id="document-status-message">Silakan unggah dokumen pendukung untuk melengkapi
                                    pengajuan Anda.</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="document-upload-area p-3 mb-3 border rounded">
                                    <h6 class="font-weight-bold mb-3 text-primary">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Dokumen
                                    </h6>

                                    <form id="dokumen-upload-form" enctype="multipart/form-data">
                                        <div class="custom-file mb-3">
                                            <input type="file" class="custom-file-input" id="dokumen-file"
                                                name="dokumen" accept="application/pdf">
                                            <label class="custom-file-label" for="dokumen-file">Pilih file PDF...</label>
                                            <div class="invalid-feedback">File harus berformat PDF dan berukuran maksimal
                                                5MB.</div>
                                        </div>

                                        <div class="progress mb-3" style="display: none;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>

                                        <div class="upload-guide small text-muted">
                                            <i class="fas fa-lightbulb mr-1"></i> Tip: Dokumen akan otomatis terunggah
                                            setelah Anda memilih file
                                        </div>

                                        <button type="button" class="btn btn-primary d-none" id="upload-dokumen-btn"
                                            data-id="{{ $pengajuan->id ?? request()->route('id') }}">
                                            <i class="fas fa-upload mr-1"></i> Upload Dokumen
                                        </button>
                                    </form>

                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Dokumen harus dalam format PDF dan
                                        berukuran maksimal 5MB.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="document-preview-area">
                                    <h6 class="font-weight-bold mb-3 text-primary">
                                        <i class="fas fa-file-pdf mr-2"></i><span id="preview-area-title">Dokumen Saat
                                            Ini</span>
                                    </h6>

                                    <div id="no-document-placeholder" class="text-center py-5">
                                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada dokumen yang diunggah</p>
                                        <p class="small">Dokumen pendukung diperlukan untuk melengkapi pengajuan.</p>
                                    </div>

                                    <div id="document-info" class="mb-3" style="display: none;">
                                        <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                            <i class="fas fa-file-pdf text-danger mr-3 fa-2x"></i>
                                            <div class="flex-grow-1">
                                                <div id="document-name" class="font-weight-bold">dokumen_pendukung.pdf
                                                </div>
                                                <div id="document-upload-time" class="small text-muted">Diunggah pada:
                                                    <span id="upload-datetime">-</span>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mr-1"
                                                    id="toggle-preview-btn">
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

                                    <div id="pdf-preview-container" class="mt-3" style="display: none;">
                                        <div class="pdf-preview-wrapper">
                                            <div class="pdf-loading text-center py-5" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <p class="mt-2">Memuat dokumen...</p>
                                            </div>
                                            <iframe id="pdf-preview-frame" class="pdf-preview-frame" src=""
                                                style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <div class="page-value p-3 bg-light border rounded" id="alasan-penolakan-koordinator">
                                </div>
                            </div>
                            <div class="page-row" id="penolakan-perencanaan-container" style="display: none;">
                                <label class="page-label">Alasan Penolakan Unit Perencanaan:</label>
                                <div class="page-value p-3 bg-light border rounded" id="alasan-penolakan-perencanaan">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3" id="berita-acara-section">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="fas fa-file-signature mr-2"></i>Verifikasi Berita Acara</h6>
                    </div>
                    <div class="card-body">
                        <div id="berita-acara-verification" class="mb-2">
                            <div class="verification-status mb-2">
                                <h6 class="font-weight-bold mb-2">Status Tanda Tangan Berita Acara:</h6>
                                <div id="status-berita-acara">
                                    <div class="d-flex align-items-center mb-1">
                                        {{-- <i class="fas fa-user-edit text-primary mr-2"></i> --}}
                                        <span id="operator-signed-status">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            Belum ditandatangani oleh Operator [Loading...]
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        {{-- <i class="fas fa-user-check text-secondary mr-2"></i> --}}
                                        <span id="pelaksana-signed-status">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            Belum ditandatangani oleh Pelaksana [Loading...]
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        {{-- <i class="fas fa-user-shield text-secondary mr-2"></i> --}}
                                        <span id="koordinator-signed-status">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            Belum ditandatangani oleh Koordinator Administrasi Barang Milik Negara
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        {{-- <i class="fas fa-user-cog text-secondary mr-2"></i> --}}
                                        <span id="perencanaan-signed-status">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            Belum ditandatangani oleh Operator Bagian Perencanaan
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 mb-2" id="berita-acara-actions">
                                    <button type="button" class="btn btn-outline-success d-none"
                                        id="download-berita-acara-signed-button">
                                        <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="verification-buttons" class="mb-4">
                </div>
            </div>
        </div>

        <div class="action-footer">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <small><i class="fas fa-info-circle mr-1"></i>Pastikan semua dokumen sudah diverifikasi sebelum
                            mengirim pengajuan</small>
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('pengajuan.reguler.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="torPreviewModal" tabindex="-1" role="dialog"
            aria-labelledby="torPreviewModalLabel" aria-hidden="true">
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
                                <div class="pdf-container"
                                    style="height: 600px; border: 1px solid #ddd; position: relative;">
                                    <div class="text-center p-5" id="tor-pdf-loading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Memuat dokumen TOR...</p>
                                    </div>
                                    <iframe id="tor-pdf-preview" src=""
                                        style="width: 100%; height: 100%; display: none;"></iframe>
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
                                                <label for="tor-passphrase-input-modal"><i class="fas fa-key mr-1"></i>
                                                    Passphrase:</label>
                                                <input type="password" id="tor-passphrase-input-modal"
                                                    class="form-control" placeholder="Masukkan passphrase">
                                                <small class="form-text text-muted">Passphrase diperlukan untuk
                                                    menandatangani dokumen TOR secara elektronik.</small>
                                            </div>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda telah
                                                memeriksa dokumen TOR dengan teliti sebelum menandatanganinya.
                                            </div>
                                            <div class="verification-details mt-3">
                                                <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Nomor Pengajuan</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="tor-detail-nomor-pengajuan"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Tahun Anggaran</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="tor-detail-tahun-anggaran"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Bagian Pengusul</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="tor-detail-bagian-pengusul"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Tanggal</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="tor-detail-tanggal"></span></div>
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

        <div class="modal fade" id="lampiranPreviewModal" tabindex="-1" role="dialog"
            aria-labelledby="lampiranPreviewModalLabel" aria-hidden="true">
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
                                <div class="pdf-container"
                                    style="height: 600px; border: 1px solid #ddd; position: relative;">
                                    <div class="text-center p-5" id="lampiran-pdf-loading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Memuat dokumen Lampiran...</p>
                                    </div>
                                    <iframe id="lampiran-pdf-preview" src=""
                                        style="width: 100%; height: 100%; display: none;"></iframe>
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
                                                <label for="lampiran-passphrase-input-modal"><i
                                                        class="fas fa-key mr-1"></i> Passphrase:</label>
                                                <input type="password" id="lampiran-passphrase-input-modal"
                                                    class="form-control" placeholder="Masukkan passphrase">
                                                <small class="form-text text-muted">Passphrase diperlukan untuk
                                                    menandatangani dokumen Lampiran secara elektronik.</small>
                                            </div>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda telah
                                                memeriksa dokumen Lampiran dengan teliti sebelum menandatanganinya.
                                            </div>
                                            <div class="verification-details mt-3">
                                                <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Nomor Pengajuan</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="lampiran-detail-nomor-pengajuan"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Tahun Anggaran</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="lampiran-detail-tahun-anggaran"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Bagian Pengusul</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="lampiran-detail-bagian-pengusul"></span></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-5">Total Anggaran</div>
                                                    <div class="col-7"><span class="font-weight-bold"
                                                            id="lampiran-detail-total-anggaran"></span></div>
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
            // Toast notification configuration
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

            // Formatting helper functions
            function formatRupiah(angka) {
                if (typeof angka !== 'number') {
                    angka = parseFloat(angka) || 0;
                }

                const number_string = angka.toString();
                const split = number_string.split('.');
                const sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    const separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return number_string.split('.')[1] != undefined ?
                    rupiah + ',' + number_string.split('.')[1] : rupiah;
            }

            // Show toast notification helper
            function showToast(icon, title) {
                Toast.fire({
                    icon,
                    title
                });
            }

            // Update status info based on current status
            function updateStatusInfo(status) {
                let icon = 'info-circle';
                let alertClass = 'info';
                let message = 'Silakan review pengajuan reguler sebelum melakukan tindakan.';

                if (status === 'Draft') {
                    icon = 'pencil-alt';
                    alertClass = 'secondary';
                    message = 'Pengajuan masih dalam status draft.';
                } else if (status === 'Diajukan ke Unit Pelaksana') {
                    icon = 'paper-plane';
                    alertClass = 'primary';
                    message = 'Pengajuan telah dikirim ke Unit Pelaksana dan sedang dalam proses review.';
                } else if (status === 'Diajukan ke Koordinator') {
                    icon = 'chevron-up';
                    alertClass = 'primary';
                    message = 'Pengajuan telah diajukan ke Koordinator.';
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

            function calculateItemTotal(price, quantity) {
                return parseFloat(price) * parseInt(quantity);
            }

            function updateTableTotals() {
                let grandTotal = 0;
                $('#tabel-detil-pengajuan tbody tr').each(function() {
                    const quantity = parseInt($(this).find('td:eq(3)').text()) || 0;
                    const price = parseFloat($(this).find('td:eq(4)').text().replace(/[^\d,]/g, '').replace(
                        ',', '.')) || 0;
                    const total = calculateItemTotal(price, quantity);
                    $(this).find('td:eq(5)').text('Rp ' + formatRupiah(total));
                    grandTotal += total;
                });
                $('#grand-total').text('Rp ' + formatRupiah(grandTotal));
            }

            function validateAndFixCalculations() {
                let totalFixed = 0;
                let needsCorrection = false;
                const isRevisiActive = $('#detil-revisi-container').is(':visible');
                const tableSelector = isRevisiActive ? '#tabel-detil-revisi tbody tr' :
                    '#tabel-detil-pengajuan tbody tr';

                $(tableSelector).each(function() {
                    if ($(this).find('td').length <= 1) return;
                    const quantity = parseInt($(this).find('td:eq(3)').text().trim()) || 0;
                    const priceText = $(this).find('td:eq(4)').text().trim();
                    const price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                    const correctTotal = quantity * price;
                    const currentTotalText = $(this).find('td:eq(5)').text().trim();
                    const currentTotal = parseFloat(currentTotalText.replace(/[^\d,]/g, '').replace(',',
                        '.')) || 0;

                    if (Math.abs(correctTotal - currentTotal) > 0.1) {
                        $(this).find('td:eq(5)').text('Rp ' + formatRupiah(correctTotal));
                        needsCorrection = true;
                    }

                    totalFixed += correctTotal;
                });

                const grandTotalSelector = isRevisiActive ? '#grand-total-revisi' : '#grand-total';
                const currentGrandTotalText = $(grandTotalSelector).text().trim();
                const currentGrandTotal = parseFloat(currentGrandTotalText.replace(/[^\d,]/g, '').replace(',',
                    '.')) || 0;

                if (Math.abs(totalFixed - currentGrandTotal) > 0.1) {
                    $(grandTotalSelector).text('Rp ' + formatRupiah(totalFixed));
                    $('#total-anggaran').text('Rp ' + formatRupiah(totalFixed));
                }

                if (needsCorrection) {
                    console.log('Beberapa perhitungan total telah dikoreksi');
                }
            }

            function toggleTablesBasedOnPengajuanType(tipeType, totalAnggaranPengajuan, totalAnggaranRevisi) {
                console.log("Toggling tables for type:", tipeType);
                console.log("Total pengajuan:", totalAnggaranPengajuan);
                console.log("Total revisi:", totalAnggaranRevisi);

                if (tipeType.toLowerCase() === 'revisi') {
                    $('#detil-pengajuan-container').hide();
                    $('#detil-revisi-container').show();
                    $('#total-anggaran').text('Rp ' + formatRupiah(totalAnggaranRevisi));
                } else {
                    $('#detil-pengajuan-container').show();
                    $('#detil-revisi-container').hide();
                    $('#total-anggaran').text('Rp ' + formatRupiah(totalAnggaranPengajuan));
                }
            }

            function updateBeritaAcaraStatus(data) {
                // console.log('Updating berita acara status:', data);

                // Only update if it's Usulan type
                if (data.tipe_pengajuan === 'Usulan') {
                    // Format nama bagian untuk ditampilkan
                    const bagianPengusul = data.bagian_pengusul || 'Bagian Tidak Diketahui';
                    const bagianPelaksana = data.bagian_pelaksana || 'Bagian Tidak Diketahui';

                    // Reset semua status container ke kondisi default
                    $('#operator-signed-status').parent().removeClass('text-primary text-success text-warning')
                        .addClass('text-muted');
                    $('#pelaksana-signed-status').parent().removeClass('text-primary text-success text-warning')
                        .addClass('text-muted');
                    $('#koordinator-signed-status').parent().removeClass('text-primary text-success text-warning')
                        .addClass('text-muted');
                    $('#perencanaan-signed-status').parent().removeClass('text-primary text-success text-warning')
                        .addClass('text-muted');

                    // Operator - Step 1
                    if (data.berita_acara_operator_signed === true) {
                        $('#operator-signed-status').html(
                            `<i class="fas fa-check-circle text-success"></i> <span class="text-success font-weight-bold">Ditandatangani oleh Operator ${bagianPengusul}</span>`
                        );
                        $('#operator-signed-status').parent().removeClass('text-muted').addClass('text-success');

                        // Aktifkan step selanjutnya
                        $('#pelaksana-signed-status').parent().removeClass('text-muted').addClass('text-primary');
                    } else {
                        $('#operator-signed-status').html(
                            `<i class="fas fa-times-circle text-danger"></i> <span class="text-muted">Belum ditandatangani oleh Operator ${bagianPengusul}</span>`
                        );
                    }

                    // Pelaksana - Step 2 (hanya aktif jika step 1 selesai)
                    if (data.berita_acara_operator_signed === true) {
                        if (data.berita_acara_pelaksana_signed === true) {
                            $('#pelaksana-signed-status').html(
                                `<i class="fas fa-check-circle text-success"></i> <span class="text-success font-weight-bold">Ditandatangani oleh Pelaksana ${bagianPelaksana}</span>`
                            );
                            $('#pelaksana-signed-status').parent().removeClass('text-primary text-muted').addClass(
                                'text-success');

                            // Aktifkan step selanjutnya
                            $('#koordinator-signed-status').parent().removeClass('text-muted').addClass(
                                'text-primary');
                        } else {
                            $('#pelaksana-signed-status').html(
                                `<i class="fas fa-clock text-warning"></i> <span class="text-primary">Menunggu tanda tangan Pelaksana ${bagianPelaksana}</span>`
                            );
                        }
                    } else {
                        $('#pelaksana-signed-status').html(
                            `<i class="fas fa-times-circle text-muted"></i> <span class="text-muted">Belum ditandatangani oleh Pelaksana ${bagianPelaksana}</span>`
                        );
                    }

                    // Koordinator - Step 3 (hanya aktif jika step 1 & 2 selesai)
                    if (data.berita_acara_operator_signed === true && data.berita_acara_pelaksana_signed === true) {
                        if (data.berita_acara_koordinator_signed === true) {
                            $('#koordinator-signed-status').html(
                                '<i class="fas fa-check-circle text-success"></i> <span class="text-success font-weight-bold">Ditandatangani oleh Koordinator BAGIAN ADMINISTRASI BARANG MILIK NEGARA</span>'
                            );
                            $('#koordinator-signed-status').parent().removeClass('text-primary text-muted')
                                .addClass('text-success');

                            // Aktifkan step selanjutnya
                            $('#perencanaan-signed-status').parent().removeClass('text-muted').addClass(
                                'text-primary');
                        } else {
                            $('#koordinator-signed-status').html(
                                '<i class="fas fa-clock text-warning"></i> <span class="text-primary">Menunggu tanda tangan Koordinator BAGIAN ADMINISTRASI BARANG MILIK NEGARA</span>'
                            );
                        }
                    } else {
                        $('#koordinator-signed-status').html(
                            '<i class="fas fa-times-circle text-muted"></i> <span class="text-muted">Belum ditandatangani oleh Koordinator BAGIAN ADMINISTRASI BARANG MILIK NEGARA</span>'
                        );
                    }

                    // Perencanaan - Step 4 (hanya aktif jika step 1, 2, & 3 selesai)
                    if (data.berita_acara_operator_signed === true &&
                        data.berita_acara_pelaksana_signed === true &&
                        data.berita_acara_koordinator_signed === true) {
                        if (data.berita_acara_perencanaan_signed === true) {
                            $('#perencanaan-signed-status').html(
                                '<i class="fas fa-check-circle text-success"></i> <span class="text-success font-weight-bold">Ditandatangani oleh Bagian Perencanaan</span>'
                            );
                            $('#perencanaan-signed-status').parent().removeClass('text-primary text-muted')
                                .addClass('text-success');
                        } else {
                            $('#perencanaan-signed-status').html(
                                '<i class="fas fa-clock text-warning"></i> <span class="text-primary">Menunggu tanda tangan Bagian Perencanaan</span>'
                            );
                        }
                    } else {
                        $('#perencanaan-signed-status').html(
                            '<i class="fas fa-times-circle text-muted"></i> <span class="text-muted">Belum ditandatangani oleh Bagian Perencanaan</span>'
                        );
                    }

                    // Download button - hanya tampil jika semua sudah ditandatangani
                    if (data.berita_acara_signed_path ||
                        data.berita_acara_operator_signed === true ||
                        data.berita_acara_pelaksana_signed === true ||
                        data.berita_acara_koordinator_signed === true ||
                        data.berita_acara_perencanaan_signed === true) {
                        $('#download-berita-acara-signed-button').removeClass('d-none').data('id', data.id);
                    } else {
                        $('#download-berita-acara-signed-button').addClass('d-none');
                    }
                }
            }


            function renderVerificationButtons(data) {
                const $verificationButtons = $('#verification-buttons');
                $verificationButtons.empty();

                const canVerify = ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator',
                    'Ditolak oleh Perencanaan'
                ].includes(data.status_pengajuan);

                if (canVerify) {
                    let statusInfoHtml = '';
                    let documentsNeeded = [];

                    // Different document requirements based on type
                    if (data.tipe_pengajuan === 'Revisi') {
                        // For Revisi: Only Lampiran + Dokumen Pendukung
                        documentsNeeded = ['lampiran'];

                        const lampiranStatus = data.lampiran_signed_path ?
                            '<i class="fas fa-check-circle text-success"></i> Lampiran terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> Lampiran belum diverifikasi';

                        const dokumenPendukungStatus = (data.dokumen_pendukung && data.dokumen_pendukung !== null &&
                                data.dokumen_pendukung !== '') ?
                            '<i class="fas fa-check-circle text-success"></i> Dokumen Pendukung terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> Dokumen Pendukung belum diverifikasi';

                        const allDocumentsVerified = data.lampiran_signed_path &&
                            (data.dokumen_pendukung && data.dokumen_pendukung !== null && data.dokumen_pendukung !==
                                '');

                        statusInfoHtml = `
                    <div class="verification-status mb-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i>Status Verifikasi Dokumen</h6>
                            </div>
                            <div class="card-body">
                                <small class="d-block mb-2">${lampiranStatus}</small>
                                <small class="d-block mb-2">${dokumenPendukungStatus}</small>
                                ${!allDocumentsVerified ?
                            '<div class="alert alert-warning mt-2 mb-0"><small><i class="fas fa-info-circle"></i> Verifikasi Lampiran dan upload Dokumen Pendukung untuk dapat mengirim pengajuan revisi</small></div>' :
                            '<div class="alert alert-success mt-2 mb-0"><small><i class="fas fa-check-circle"></i> Semua dokumen terverifikasi, pengajuan siap dikirim</small></div>'}
                            </div>
                        </div>
                    </div>
                `;
                    } else {
                        // For Usulan: All documents (Berita Acara, TOR, Lampiran, Dokumen Pendukung)
                        documentsNeeded = ['berita_acara', 'tor', 'lampiran'];

                        const torStatus = data.tor_signed_path ?
                            '<i class="fas fa-check-circle text-success"></i> TOR terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> TOR belum diverifikasi';

                        const lampiranStatus = data.lampiran_signed_path ?
                            '<i class="fas fa-check-circle text-success"></i> Lampiran terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> Lampiran belum diverifikasi';

                        const beritaAcaraStatus = (data.berita_acara_operator_signed === true) ?
                            '<i class="fas fa-check-circle text-success"></i> Berita Acara terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> Berita Acara belum diverifikasi';

                        const dokumenPendukungStatus = (data.dokumen_pendukung && data.dokumen_pendukung !== null &&
                                data.dokumen_pendukung !== '') ?
                            '<i class="fas fa-check-circle text-success"></i> Dokumen Pendukung terverifikasi' :
                            '<i class="fas fa-times-circle text-danger"></i> Dokumen Pendukung belum diverifikasi';

                        const allDocumentsVerified = data.tor_signed_path &&
                            data.lampiran_signed_path &&
                            (data.berita_acara_operator_signed === true) &&
                            (data.dokumen_pendukung && data.dokumen_pendukung !== null && data.dokumen_pendukung !==
                                '');

                        statusInfoHtml = `
                    <div class="verification-status mb-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i>Status Verifikasi Dokumen</h6>
                            </div>
                            <div class="card-body">
                                <small class="d-block mb-2">${torStatus}</small>
                                <small class="d-block mb-2">${lampiranStatus}</small>
                                <small class="d-block mb-2">${beritaAcaraStatus}</small>
                                <small class="d-block mb-2">${dokumenPendukungStatus}</small>
                                ${!allDocumentsVerified ?
                            '<div class="alert alert-warning mt-2 mb-0"><small><i class="fas fa-info-circle"></i> Verifikasi semua dokumen untuk dapat mengirim pengajuan</small></div>' :
                            '<div class="alert alert-success mt-2 mb-0"><small><i class="fas fa-check-circle"></i> Semua dokumen terverifikasi, pengajuan siap dikirim</small></div>'}
                            </div>
                        </div>
                    </div>
                `;
                    }

                    // Check if any documents need verification
                    let needsVerification = false;
                    if (data.tipe_pengajuan === 'Revisi') {
                        needsVerification = !data.lampiran_signed_path;
                    } else {
                        needsVerification = !data.tor_signed_path || !data.lampiran_signed_path || !(data
                            .berita_acara_operator_signed === true);
                    }

                    const hasDokumenPendukung = data.dokumen_pendukung && data.dokumen_pendukung !== null && data
                        .dokumen_pendukung !== ''; //
                    let magicLinkSection = ''; //

                    if (data.tipe_pengajuan === 'Usulan') { //
                        const needsVerification = !data.tor_signed_path || !data.lampiran_signed_path || !(data
                            .berita_acara_operator_signed === true); //
                        const documentsNeeded = ['berita_acara', 'tor', 'lampiran']; //

                        magicLinkSection = `
                    <div class="magic-link-verification mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-magic mr-2"></i>Verifikasi Digital</h6>
                            </div>
                            <div class="card-body">`; //

                        if (hasDokumenPendukung) { //
                            magicLinkSection += `
                                ${needsVerification ? `
                                                                            <p class="mb-3">
                                                                                <i class="fas fa-info-circle text-info mr-2"></i>
                                                                                Kirim link verifikasi digital ke Eselon III untuk menandatangani dokumen secara elektronik.
                                                                            </p>
                                                                            <div class="row">
                                                                                <div class="col-md-8">
                                                                                    <div class="alert alert-info mb-2">
                                                                                        <small>
                                                                                            <strong>Dokumen yang akan ditandatangani:</strong><br>
                                                                                            ${documentsNeeded.map(doc => `• ${formatDocumentName(doc)}`).join('<br>')}
                                                                                        </small>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 d-flex align-items-center">
                                                                                    <button type="button" class="btn btn-primary btn-block" id="send-magic-link-btn" data-id="${data.id}">
                                                                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Pengajuan Verifikasi
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        ` : `
                                                                            <div class="alert alert-success mb-0">
                                                                                <i class="fas fa-check-circle mr-2"></i>
                                                                                Semua dokumen telah terverifikasi. Tidak perlu verifikasi tambahan.
                                                                            </div>
                                                                        `}
                    `; //
                        } else {
                            magicLinkSection += `
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Dokumen Pendukung Diperlukan.</strong><br>
                                    Silakan unggah dokumen pendukung terlebih dahulu untuk dapat mengirim Link verifikasi.
                                </div>
                    `; //
                        }


                        magicLinkSection += `
                                <div id="magic-link-status" style="display: none;" class="mt-3">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span id="magic-link-message">Link Verifikasi telah dikirim. Menunggu verifikasi...</span>
                                        <br>
                                        <small id="magic-link-timer" class="font-weight-bold"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `; //
                    }

                    $verificationButtons.append(statusInfoHtml);
                    $verificationButtons.append(magicLinkSection);
                }
            }

            // Add helper function to format document names
            function formatDocumentName(docType) {
                const names = {
                    'berita_acara': 'Berita Acara',
                    'tor': 'Terms of Reference (TOR)',
                    'lampiran': 'Lampiran'
                };
                return names[docType] || docType;
            }

            // Load data pengajuan
            function loadPengajuan() {
                const id = {{ $pengajuan ? $pengajuan->id : request()->route('id') }};
                const baseUrl = '{{ url('pengajuanrkbmnbagianreguler') }}';

                console.log("Loading pengajuan data:", id);

                $.ajax({
                    url: `${baseUrl}/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log("Response:", response);

                        if (response.success) {
                            const data = response.data;

                            // Set global variable for pengajuan type
                            pengajuanType = data.tipe_pengajuan;

                            // MODIFIED: Show/hide sections based on type
                            if (data.tipe_pengajuan === 'Revisi') {
                                $('#berita-acara-section').hide();
                                console.log('Hiding Berita Acara section for Revisi type');
                            } else {
                                $('#berita-acara-section').show();
                                console.log('Showing Berita Acara section for Usulan type');
                            }

                            console.log("Status dokumen:", {
                                "berita_acara_operator_signed": data
                                    .berita_acara_operator_signed,
                                "berita_acara_operator_signed_path": data
                                    .berita_acara_operator_signed_path,
                                "tor_signed_path": data.tor_signed_path,
                                "lampiran_signed_path": data.lampiran_signed_path,
                                "dokumen_pendukung": data.dokumen_pendukung
                            });

                            renderVerificationButtons(data);
                            updateBeritaAcaraStatus(data);

                            // PERUBAHAN: Panggil fungsi baru untuk me-render area dokumen
                            renderDocumentArea(data);

                            // Populate page with data
                            $('#id').text(data.id);
                            $('#tipe-pengajuan').text(data.tipe_pengajuan);
                            $('#tahun-anggaran').text(data.tahun_anggaran);

                            // Format status with badge
                            let statusClass = 'badge-secondary';
                            if (data.status_pengajuan === 'Diajukan ke Unit Pelaksana') {
                                statusClass = 'badge-info';
                            } else if (data.status_pengajuan === 'Diajukan ke Koordinator') {
                                statusClass = 'badge-info';
                            } else if (data.status_pengajuan === 'Disetujui') {
                                statusClass = 'badge-success';
                            } else if (data.status_pengajuan.includes('Ditolak')) {
                                statusClass = 'badge-danger';
                            }

                            $('#status-pengajuan').html(
                                `<span class="badge ${statusClass}">${data.status_pengajuan}</span>`
                            );
                            updateStatusInfo(data.status_pengajuan);

                            $('#tanggal-pengajuan').text(data.tanggal_pengajuan);
                            $('#bagian-pengusul').text(data.bagian_pengusul);
                            $('#biro-pengusul').text(data.biro_pengusul);
                            $('#bagian-pelaksana').text(data.bagian_pelaksana);
                            $('#biro-pelaksana').text(data.biro_pelaksana);
                            $('#keterangan').text(data.keterangan);

                            // Show appropriate sections based on type
                            if (data.tipe_pengajuan === 'Revisi') {
                                $('#pengenal-section').show();
                                $('#akun-section').hide();
                                $('#kode-pengenal').text(data.kode_pengenal);
                            } else {
                                $('#pengenal-section').hide();
                                $('#akun-section').show();
                                $('#akun').text(data.kode_akun !== '-' ? data.kode_akun :
                                    'Belum diisi pengusul/pelaksana bagian');
                            }

                            // Populate detail pengajuan
                            if (data.detil_pengajuan && data.detil_pengajuan.length > 0) {
                                let html = '';
                                data.detil_pengajuan.forEach(function(item) {
                                    html += `<tr>
                                <td>${item.no}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.deskripsi}</td>
                                <td>${item.keterangan_barang}</td>
                                <td class="text-right">${item.kuantitas}</td>
                                <td class="text-right">Rp ${formatRupiah(item.harga)}</td>
                                <td class="text-right">Rp ${formatRupiah(item.total)}</td>
                            </tr>`;
                                });
                                $('#detil-items').html(html);
                                $('#grand-total').text('Rp ' + formatRupiah(data
                                    .total_anggaran_pengajuan));
                            } else {
                                $('#detil-items').html(
                                    '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>'
                                );
                                $('#grand-total').text('Rp 0');
                            }

                            // Populate detail revisi
                            if (data.detil_revisi && data.detil_revisi.length > 0) {
                                let html = '';
                                data.detil_revisi.forEach(function(item) {
                                    html += `<tr>
                                <td>${item.no}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.deskripsi}</td>
                                <td>${item.keterangan_barang}</td>
                                <td class="text-right">${item.kuantitas}</td>
                                <td class="text-right">Rp ${formatRupiah(item.harga)}</td>
                                <td class="text-right">Rp ${formatRupiah(item.total)}</td>
                            </tr>`;
                                });
                                $('#revisi-items').html(html);
                                $('#grand-total-revisi').text('Rp ' + formatRupiah(data
                                    .total_anggaran_revisi));
                            } else {
                                $('#revisi-items').html(
                                    '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>'
                                );
                                $('#grand-total-revisi').text('Rp 0');
                            }

                            // Toggle tables based on pengajuan type
                            if (data.tipe_pengajuan === 'Revisi') {
                                $('#detil-pengajuan-container').hide();
                                $('#detil-revisi-container').show();
                                $('#total-anggaran').text('Rp ' + formatRupiah(data
                                    .total_anggaran_revisi));
                            } else {
                                $('#detil-pengajuan-container').show();
                                $('#detil-revisi-container').hide();
                                $('#total-anggaran').text('Rp ' + formatRupiah(data
                                    .total_anggaran_pengajuan));
                            }

                            // Show rejection status if any
                            if (data.status_pengajuan.includes('Ditolak')) {
                                $('#status-penolakan').show();
                                $('#status-ditolak').text(data.status_pengajuan);

                                if (data.alasan_penolakan_pelaksana) {
                                    $('#penolakan-pelaksana-container').show();
                                    $('#alasan-penolakan-pelaksana').text(data
                                        .alasan_penolakan_pelaksana);
                                } else {
                                    $('#penolakan-pelaksana-container').hide();
                                }

                                if (data.alasan_penolakan_koordinator) {
                                    $('#penolakan-koordinator-container').show();
                                    $('#alasan-penolakan-koordinator').text(data
                                        .alasan_penolakan_koordinator);
                                } else {
                                    $('#penolakan-koordinator-container').hide();
                                }
                            } else {
                                $('#status-penolakan').hide();
                            }

                            // Menampilkan alasan penolakan Unit Perencanaan
                            if (data.alasan_penolakan_perencanaan) {
                                $('#alasan-penolakan-perencanaan').text(data
                                    .alasan_penolakan_perencanaan);
                                $('#penolakan-perencanaan-container').show();
                                isDitolak = true;
                            }

                            // Set ID for berita acara download button
                            $('#download-berita-acara-signed-button').data('id', data.id);

                            // Update action buttons in footer
                            let actionButtons = $('.action-buttons');
                            actionButtons.empty();

                            // Always add back button first
                            actionButtons.append(`<a href="{{ route('pengajuan.reguler.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>`);

                            // MODIFIED: Check document completeness based on type
                            if (['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator',
                                    'Ditolak oleh Perencanaan'
                                ].includes(data.status_pengajuan)) {
                                const hasDokumenPendukung = data.dokumen_pendukung && data
                                    .dokumen_pendukung !== null && data.dokumen_pendukung !== '';
                                let allDocumentsComplete = false;

                                if (data.tipe_pengajuan === 'Revisi') {
                                    // For Revisi: Only need Lampiran + Dokumen Pendukung
                                    allDocumentsComplete = data.lampiran_signed_path &&
                                        hasDokumenPendukung;
                                } else {
                                    // For Usulan: Need all documents
                                    // Changed: Check berita_acara_operator_signed instead of path
                                    allDocumentsComplete = (data.berita_acara_operator_signed ===
                                            true) &&
                                        data.tor_signed_path &&
                                        data.lampiran_signed_path &&
                                        hasDokumenPendukung;
                                }

                                if (allDocumentsComplete) {
                                    actionButtons.append(`<button type="button" class="btn btn-success" id="kirim-pengajuan-button" data-id="${data.id}">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>`);
                                    console.log(
                                        "Tombol Kirim ditampilkan - semua dokumen sudah lengkap");
                                } else {
                                    let tooltip =
                                        "Untuk mengirim pengajuan, pastikan dokumen telah dilengkapi: ";
                                    let missingDocs = [];

                                    if (data.tipe_pengajuan === 'Usulan') {
                                        // Changed: Check berita_acara_operator_signed instead of path
                                        if (!(data.berita_acara_operator_signed === true))
                                            missingDocs.push("Berita Acara");
                                        if (!data.tor_signed_path)
                                            missingDocs.push("TOR");
                                    }
                                    if (!data.lampiran_signed_path)
                                        missingDocs.push("Lampiran");
                                    if (!hasDokumenPendukung)
                                        missingDocs.push("Dokumen Pendukung");

                                    tooltip += missingDocs.join(", ");

                                    console.log(
                                        "Tombol Kirim tidak ditampilkan - dokumen belum lengkap:",
                                        missingDocs);

                                    actionButtons.append(`<button type="button" class="btn btn-secondary" disabled
                                data-toggle="tooltip" data-placement="top"
                                title="${tooltip}">
                                <i class="fas fa-lock mr-1"></i> Kirim Pengajuan
                            </button>`);

                                    $('[data-toggle="tooltip"]').tooltip();
                                }

                                actionButtons.append(`<button type="button" class="btn btn-danger" id="hapus-pengajuan-button" data-id="${data.id}">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>`);
                            }

                            // Add download buttons for verified documents
                            if (data.tipe_pengajuan === 'Usulan' && data.tor_signed_path && !
                                actionButtons.find('#download-tor-button').length) {
                                actionButtons.append(`<button type="button" class="btn btn-info" id="download-tor-button" data-id="${data.id}">
                            <i class="fas fa-download mr-1"></i>Download TOR
                        </button>`);
                            }

                            if (data.lampiran_signed_path && !actionButtons.find(
                                    '#download-lampiran-button').length) {
                                actionButtons.append(`<button type="button" class="btn btn-primary" id="download-lampiran-button" data-id="${data.id}">
                            <i class="fas fa-file-alt mr-1"></i>Download Lampiran
                        </button>`);
                            }

                            if (data.dokumen_rekomendasi_bmn) {
                                const downloadRekomendasiUrl =
                                    `{{ url('koordinator_nonsbsk') }}/${data.id}/download-rekomendasi`;
                                actionButtons.append(`
                            <a href="${downloadRekomendasiUrl}" target="_blank" class="btn btn-success" title="Download Surat Rekomendasi yang dibuat oleh Koordinator">
                                <i class="fas fa-file-check mr-1"></i> Download Surat Rekomendasi
                            </a>
                        `);
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

            // Event handler untuk tombol Verify TOR
            $(document).on('click', '#verify-tor-button', function() {
                if (pengajuanType !== 'Usulan') {
                    console.log('TOR verification not available for Revisi type');
                    return;
                }

                const id = {{ $pengajuan->id ?? 'null' }};
                if (!id || id === null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID Pengajuan tidak ditemukan'
                    });
                    return;
                }
                $(this).data('id', id);

                // Rest of TOR verification logic remains the same
                $('#tor-detail-nomor-pengajuan').text(id);
                $('#tor-detail-tahun-anggaran').text($('#tahun-anggaran').text());
                $('#tor-detail-bagian-pengusul').text($('#bagian-pengusul').text());
                $('#tor-detail-tanggal').text(new Date().toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }));

                $('#tor-pdf-loading').show();
                $('#tor-pdf-preview').hide();
                $('#torPreviewModal').modal('show');

                const previewUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/preview-tor`;
                $('#tor-pdf-preview').on('load', function() {
                    $('#tor-pdf-loading').hide();
                    $('#tor-pdf-preview').show();
                }).attr('src', previewUrl);

                $('#tor-passphrase-input-modal').val('');
            });

            // Event handler untuk tombol Verify Lampiran
            $(document).on('click', '#verify-lampiran-button', function() {
                const id = {{ $pengajuan->id ?? 'null' }};
                if (!id || id === null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID Pengajuan tidak ditemukan'
                    });
                    return;
                }
                $(this).data('id', id);

                // Lampiran verification logic remains the same
                $('#lampiran-detail-nomor-pengajuan').text(id);
                $('#lampiran-detail-tahun-anggaran').text($('#tahun-anggaran').text());
                $('#lampiran-detail-bagian-pengusul').text($('#bagian-pengusul').text());
                $('#lampiran-detail-total-anggaran').text($('#total-anggaran').text());

                $('#lampiran-pdf-loading').show();
                $('#lampiran-pdf-preview').hide();
                $('#lampiranPreviewModal').modal('show');

                const previewUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/preview-lampiran`;
                $('#lampiran-pdf-preview').on('load', function() {
                    $('#lampiran-pdf-loading').hide();
                    $('#lampiran-pdf-preview').show();
                }).attr('src', previewUrl);

                $('#lampiran-passphrase-input-modal').val('');
            });

            // Add handler for the confirm TOR verification button
            $('#confirm-tor-verification-button').on('click', function() {
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
                fetch(`${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/verifikasi-tor`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            passphrase
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message ||
                                    'Terjadi kesalahan saat verifikasi TOR');
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

            // Add handler for the confirm Lampiran verification button
            $('#confirm-lampiran-verification-button').on('click', function() {
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
                fetch(`${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/verifikasi-lampiran`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            passphrase
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message ||
                                    'Terjadi kesalahan saat verifikasi Lampiran');
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

            // Handle PDF loading error
            $(document).on('error', '#tor-pdf-preview, #lampiran-pdf-preview', function() {
                const loadingId = this.id === 'tor-pdf-preview' ? 'tor-pdf-loading' :
                    'lampiran-pdf-loading';
                const retryId = this.id === 'tor-pdf-preview' ? 'retry-load-tor-pdf' :
                    'retry-load-lampiran-pdf';

                $(`#${loadingId}`).html(`
            <div class="text-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p>Gagal memuat dokumen. Silakan coba lagi.</p>
                <button class="btn btn-outline-primary btn-sm mt-2" id="${retryId}">
                    <i class="fas fa-sync mr-1"></i> Coba Lagi
                </button>
            </div>
        `);
            });

            // Retry loading PDF functions
            $(document).on('click', '#retry-load-tor-pdf', function() {
                const id = $('#verify-tor-button').data('id');
                const previewUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/preview-tor`;

                $('#tor-pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat ulang dokumen TOR...</p>
        `);

                $('#tor-pdf-preview').attr('src', previewUrl);
            });

            $(document).on('click', '#retry-load-lampiran-pdf', function() {
                const id = $('#verify-lampiran-button').data('id');
                const previewUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/preview-lampiran`;

                $('#lampiran-pdf-loading').html(`
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat ulang dokumen Lampiran...</p>
        `);

                $('#lampiran-pdf-preview').attr('src', previewUrl);
            });

            // Add enter key support for passphrase inputs
            $(document).on('keyup', '#tor-passphrase-input-modal', function(e) {
                if (e.key === 'Enter') {
                    $('#confirm-tor-verification-button').click();
                }
            });

            $(document).on('keyup', '#lampiran-passphrase-input-modal', function(e) {
                if (e.key === 'Enter') {
                    $('#confirm-lampiran-verification-button').click();
                }
            });

            // Clean up when modals are hidden
            $('#torPreviewModal, #lampiranPreviewModal').on('hidden.bs.modal', function() {
                // Clear iframe source to prevent continued loading
                const previewId = this.id === 'torPreviewModal' ? 'tor-pdf-preview' :
                    'lampiran-pdf-preview';
                $(`#${previewId}`).attr('src', '');
            });

            // Add visual feedback when entering passphrase
            $('#tor-passphrase-input-modal, #lampiran-passphrase-input-modal').on('input', function() {
                const buttonId = this.id === 'tor-passphrase-input-modal' ?
                    'confirm-tor-verification-button' : 'confirm-lampiran-verification-button';

                if ($(this).val().length > 0) {
                    $(`#${buttonId}`)
                        .addClass('btn-success')
                        .removeClass('btn-primary')
                        .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen');
                } else {
                    $(`#${buttonId}`)
                        .removeClass('btn-success')
                        .addClass('btn-primary')
                        .html('<i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen');
                }
            });

            // Event untuk tombol Download TOR
            $(document).on('click', '#download-tor-button', function() {
                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/download-tor`;

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

            // Event untuk tombol Download Lampiran
            $(document).on('click', '#download-lampiran-button', function() {
                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/download-lampiran`;

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

            // Event untuk tombol Download Berita Acara tertandatangani
            $(document).on('click', '#download-berita-acara-signed-button', function() {
                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pengajuanrkbmnbagianreguler/${id}/download-berita-acara-signed`;

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

            // Event untuk tombol Kirim Pengajuan
            $(document).on('click', '#kirim-pengajuan-button', function() {
                const id = $(this).data('id');
                const kirimUrl = `{{ url('pengajuanrkbmnbagianreguler') }}/${id}/kirim`;
                const confirmText = 'Apakah Anda yakin ingin mengajukan ini ke Unit Pelaksana?';

                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    text: confirmText,
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
                                        localStorage.setItem('pengajuanSuccess',
                                            'true');
                                        localStorage.setItem('pengajuanId', id);
                                        window.location.href =
                                            "{{ route('pengajuan.reguler.index') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message ||
                                            'Terjadi kesalahan saat mengirim pengajuan'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Status:', xhr.status);
                                console.error('Response:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error ' + xhr.status,
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat mengirim pengajuan'
                                });
                            }
                        });
                    }
                });
            });

            // Event untuk tombol Hapus Pengajuan
            $(document).on('click', '#hapus-pengajuan-button', function() {
                const id = $(this).data('id');
                const baseUrl = "{{ url('pengajuanrkbmnbagianreguler') }}";

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
                                        window.location.href =
                                            "{{ route('pengajuan.reguler.index') }}";
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
                                let errorMessage =
                                    'Terjadi kesalahan saat menghapus pengajuan';

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

            // Handle initial showing and hiding of sections based on URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const showSection = urlParams.get('section');
            if (showSection === 'dokumen') {
                $('#dokumen-upload-section').addClass('show active');
                $('#dokumen-tab').addClass('active');
            }

            // === DOKUMEN UPLOAD FUNCTIONALITY ===

            // ID pengajuan dari data attribute
            const pengajuanId = {{ $pengajuan->id ?? request()->route('id') }};
            const baseUrlReguler = window.location.origin;
            const apiPathReguler = `/pengajuanrkbmnbagianreguler/${pengajuanId}`;

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
                        const previewUrl = `${baseUrlReguler}${apiPathReguler}/preview-dokumen`;
                        showDocumentPreview(previewUrl);
                    }

                    $previewContainer.slideDown(300);
                    $btnIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                    $(this).html('<i class="fas fa-eye-slash mr-1"></i> Tutup');
                }
            });

            // Retry preview jika gagal
            $(document).on('click', '#retry-preview-btn', function() {
                const previewUrl = `${baseUrlReguler}${apiPathReguler}/preview-dokumen`;
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
                    updateDocumentStatusMessage('success',
                        'Dokumen pendukung berhasil diunggah dan siap digunakan.');

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
                    $('#download-dokumen-btn').attr('href', `${baseUrlReguler}${apiPathReguler}/download-dokumen`);

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
                    url: `${baseUrlReguler}${apiPathReguler}/upload-dokumen`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                $progressBar.width(percent + '%').attr('aria-valuenow',
                                    percent);
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

                            loadPengajuan();
                        } else {
                            $progress.hide();
                            $progressBar.width('0%').attr('aria-valuenow', 0);

                            // Update status message
                            updateDocumentStatusMessage('warning',
                                'Gagal mengunggah dokumen. Silakan coba lagi.');

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Upload',
                                text: response.message ||
                                    'Terjadi kesalahan saat upload dokumen.'
                            });
                        }

                        $('#dokumen-file').prop('disabled', false);


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
                            text: 'Terjadi kesalahan saat upload dokumen: ' + (xhr.responseJSON
                                ?.message || error)
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
                            url: `${baseUrlReguler}${apiPathReguler}/delete-dokumen`,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                // PERUBAHAN: Ganti manipulasi DOM manual dengan memanggil loadPengajuan()
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message ||
                                            'Dokumen berhasil dihapus.'
                                    }).then(() => {
                                        // Muat ulang semua data untuk memastikan konsistensi UI
                                        loadPengajuan();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message ||
                                            'Terjadi kesalahan saat menghapus dokumen.'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Delete error:', xhr.responseText);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus dokumen: ' +
                                        (xhr.responseJSON?.message || error)
                                });
                            }
                        });
                    }
                });
            });

            // PERUBAHAN: Fungsi baru untuk mengatur seluruh area dokumen pendukung
            function renderDocumentArea(data) {
                // Tentukan status mana yang mengizinkan modifikasi dokumen
                const allowedStatuses = ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator',
                    'Ditolak oleh Perencanaan'
                ];
                const canModify = allowedStatuses.includes(data.status_pengajuan);
                const hasDocument = data.dokumen_pendukung && data.dokumen_pendukung !== null && data
                    .dokumen_pendukung !== '';
                const apiPathReguler = `/pengajuanrkbmnbagianreguler/${data.id}`;

                // Tampilkan info dokumen yang ada jika file sudah diunggah
                if (hasDocument) {
                    $('#no-document-placeholder').hide();
                    $('#document-info').show();

                    const fileName = data.dokumen_pendukung.split('/').pop();
                    $('#document-name').text(fileName);
                    // Waktu upload tidak tersedia di response utama, jadi kita biarkan placeholder
                    $('#upload-datetime').text('-');
                    $('#download-dokumen-btn').attr('href', `${baseUrlReguler}${apiPathReguler}/download-dokumen`);

                } else {
                    // Tampilkan placeholder jika tidak ada dokumen
                    $('#no-document-placeholder').show();
                    $('#document-info').hide();
                }

                // Logika untuk mengizinkan atau menonaktifkan upload/hapus
                if (canModify) {
                    // Jika status mengizinkan, tampilkan form upload
                    $('#dokumen-upload-form').show();

                    // Hanya tampilkan tombol hapus jika ada dokumen
                    if (hasDocument) {
                        $('#delete-dokumen-btn').show();
                    } else {
                        $('#delete-dokumen-btn').hide();
                    }

                    // Atur pesan status sesuai kondisi
                    if (hasDocument) {
                        updateDocumentStatusMessage('success',
                            'Dokumen pendukung tersedia. Anda dapat menggantinya dengan mengunggah file baru.');
                    } else {
                        updateDocumentStatusMessage('warning',
                            'Belum ada dokumen pendukung. Silakan unggah dokumen.');
                    }

                    // Inisialisasi fungsi drag & drop karena upload diizinkan
                    initDragAndDrop();

                } else {
                    // Jika status TIDAK mengizinkan, sembunyikan form upload dan tombol hapus
                    $('#dokumen-upload-form').hide();
                    $('#delete-dokumen-btn').hide();

                    // Beri pesan kepada pengguna mengapa fungsi dinonaktifkan
                    if (hasDocument) {
                        updateDocumentStatusMessage('info',
                            'Dokumen pendukung tidak dapat diubah karena pengajuan sedang dalam proses.');
                    } else {
                        updateDocumentStatusMessage('warning',
                            'Dokumen pendukung tidak dapat diunggah karena pengajuan sedang dalam proses.');
                    }
                }
            }


            // Function to show Magic Link status and countdown
            function showMagicLinkStatus(expiresAt) {
                const $status = $('#magic-link-status');
                const $message = $('#magic-link-message');
                const $timer = $('#magic-link-timer');

                $status.show();

                // Calculate expiry time
                const expiryTime = new Date(expiresAt).getTime();

                // Update countdown every second
                const countdown = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = expiryTime - now;

                    if (distance > 0) {
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        $timer.text(`Sisa waktu: ${minutes} menit ${seconds} detik`);
                    } else {
                        clearInterval(countdown);
                        $status.find('.alert').removeClass('alert-warning').addClass('alert-danger');
                        $message.html(
                            '<i class="fas fa-times-circle mr-2"></i>Link verifikasi telah kedaluwarsa');
                        $timer.text('');

                        // Show send button again
                        $('#send-magic-link-btn').show().prop('disabled', false)
                            .html('<i class="fas fa-paper-plane mr-2"></i>Kirim link verifikasi');
                    }
                }, 1000);
            }

            // Add event handler for Magic Link button
            $(document).on('click', '#send-magic-link-btn', function() {
                const id = $(this).data('id');
                const btn = $(this);

                Swal.fire({
                    title: 'Konfirmasi Pengiriman Link Verifikasi',
                    text: 'Link verifikasi akan dikirim ke Eselon III melalui WhatsApp. Pastikan nomor WhatsApp sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#007bff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        btn.prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...');

                        Swal.fire({
                            title: 'Mengirim Link Verifikasi...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/pengajuanrkbmnbagianreguler/${id}/send-magic-link-verification`,
                            type: 'POST',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Link Verifikasi Terkirim!',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        // Show status and start countdown
                                        showMagicLinkStatus(response
                                            .expires_at);
                                        btn.hide(); // Hide the send button
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message ||
                                            'Terjadi kesalahan saat mengirim Link Verifikasi'
                                    });
                                    btn.prop('disabled', false).html(
                                        '<i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error ' + xhr.status,
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat mengirim Link Verifikasi'
                                });
                                btn.prop('disabled', false).html(
                                    '<i class="fas fa-paper-plane mr-2"></i>Kirim Link Verifikasi'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
