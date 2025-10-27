{{--resources/views/PerencanaanBMN/Bagian/koordinator_nonsbsk/review.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review Pengajuan Non SBSK - Unit Perencanaan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('perencanaan.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Review Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Alert Status -->
            <div class="alert alert-warning mb-3" id="page-status-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calculator mr-2 fa-lg"></i>
                    <span id="page-status-message"><strong>Peran Anda:</strong> Verifikasi dan pastikan kode akun sudah benar sesuai dengan klasifikasi anggaran yang berlaku.</span>
                </div>
            </div>

            <style>
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

                /* Highlight styles untuk bagian akun */
                .akun-highlight-card {
                    border: 3px solid #ffc107 !important;
                    box-shadow: 0 0 15px rgba(255, 193, 7, 0.3) !important;
                    animation: pulse-highlight 2s infinite;
                }

                .akun-highlight-header {
                    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
                    color: #000 !important;
                    font-weight: bold !important;
                }

                .akun-section-highlight {
                    background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
                    border: 2px solid #ffc107 !important;
                    border-radius: 8px !important;
                    padding: 15px !important;
                    margin: 10px 0 !important;
                    position: relative;
                }

                .akun-section-highlight::before {
                    content: "⚠️ PERHATIAN: Verifikasi Akun";
                    position: absolute;
                    top: -12px;
                    left: 15px;
                    background: #ffc107;
                    color: #000;
                    padding: 2px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: bold;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .akun-value {
                    font-size: 1.2em !important;
                    font-weight: bold !important;
                    color: #d63384 !important;
                    background: rgba(255, 255, 255, 0.8);
                    padding: 8px 12px;
                    border-radius: 6px;
                    border-left: 5px solid #d63384;
                    display: inline-block;
                    min-width: 150px;
                }

                .perencanaan-focus-badge {
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    padding: 5px 12px;
                    border-radius: 20px;
                    font-size: 0.85em;
                    font-weight: bold;
                    display: inline-block;
                    margin-bottom: 10px;
                }

                @keyframes pulse-highlight {
                    0% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.3); }
                    50% { box-shadow: 0 0 25px rgba(255, 193, 7, 0.6); }
                    100% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.3); }
                }

                .akun-validation-checklist {
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 8px;
                    padding: 15px;
                    margin-top: 10px;
                }

                .akun-validation-item {
                    padding: 5px 0;
                    border-bottom: 1px dotted #dee2e6;
                }

                .akun-validation-item:last-child {
                    border-bottom: none;
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

                #confirm-verification-button {
                    background-color: #28a745;
                    border-color: #28a745;
                    font-weight: 500;
                }

                #confirm-verification-button:hover {
                    background-color: #218838;
                    border-color: #1e7e34;
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
            </style>

            <div class="row">
                <!-- Informasi Umum - Kolom Kiri -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Nomor Pengajuan:</label>
                                <span class="d-block" id="page-id">{{ $pengajuan->id }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Tipe Pengajuan:</label>
                                <span class="d-block" id="page-tipe-pengajuan">{{ ucfirst($pengajuan->tipe_pengajuan) }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Tahun Anggaran:</label>
                                <span class="d-block" id="page-tahun-anggaran">{{ $pengajuan->tahun_anggaran }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Status Pengajuan:</label>
                                <span class="d-block" id="page-status-pengajuan">
                                    @php
                                    $statusClass = 'badge-secondary';
                                    if($pengajuan->status_pengajuan === 'Diajukan ke Koordinator') {
                                        $statusClass = 'badge-success';
                                    } elseif($pengajuan->status_pengajuan === 'Disetujui') {
                                        $statusClass = 'badge-success';
                                    } elseif(strpos($pengajuan->status_pengajuan, 'Ditolak') !== false) {
                                        $statusClass = 'badge-danger';
                                    }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $pengajuan->status_pengajuan }}</span>
                                </span>
                            </div>
                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Tanggal Pengajuan:</label>
                                <span class="d-block" id="page-tanggal-pengajuan">{{ $pengajuan->created_at->format('d-m-Y H:i') }}</span>
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
                            @php
                            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
                            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
                            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
                            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana ?? 0)->first();
                            @endphp
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Bagian Pengusul:</label>
                                <span class="d-block" id="page-bagian-pengusul">{{ $bagianPengusul ? $bagianPengusul->uraianbagian : '-' }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Biro Pengusul:</label>
                                <span class="d-block" id="page-biro-pengusul">{{ $biroPengusul ? $biroPengusul->uraianbiro : '-' }}</span>
                            </div>
                            <div class="p-2 border-bottom">
                                <label class="font-weight-bold mb-0">Bagian Pelaksana:</label>
                                <span class="d-block" id="page-bagian-pelaksana">{{ $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Anggaran - Kolom Kanan (HIGHLIGHTED) -->
                <div class="col-md-4">
                    <div class="card mb-3 akun-highlight-card">
                        <div class="card-header akun-highlight-header">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                VERIFIKASI AKUN ANGGARAN
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="perencanaan-focus-badge mx-2 mt-2">
                                <i class="fas fa-user-check mr-1"></i>
                                Fokus Review: Unit Perencanaan
                            </div>

                            @if($pengajuan->tipe_pengajuan === 'revisi')
                            <div class="p-2 border-bottom akun-section-highlight" id="pengenal-section">
                                <label class="font-weight-bold mb-0">Kode Pengenal (untuk Revisi):</label>
                                <span class="d-block akun-value" id="page-kode-pengenal">{{ $pengajuan->kode_pengenal ?: '-' }}</span>
                            </div>
                            @else
                            <div class="p-2 border-bottom akun-section-highlight" id="akun-section">
                                <label class="font-weight-bold mb-0">
                                    <i class="fas fa-search mr-1"></i>
                                    Kode Akun (untuk Usulan):
                                </label>
                                <span class="d-block akun-value" id="page-akun">{{ $pengajuan->kode_akun ?: '-' }}</span>

                                <!-- Checklist Validasi Akun -->
                                <div class="akun-validation-checklist mt-2">
                                    <small class="font-weight-bold text-muted">Checklist Validasi:</small>
                                    <div class="akun-validation-item">
                                        <i class="fas fa-square-check text-muted mr-1"></i>
                                        <small>Sesuai dengan jenis barang/perlengkapan</small>
                                    </div>
                                    <div class="akun-validation-item">
                                        <i class="fas fa-square-check text-muted mr-1"></i>
                                        <small>Mengikuti klasifikasi akun yang berlaku</small>
                                    </div>
                                    <div class="akun-validation-item">
                                        <i class="fas fa-square-check text-muted mr-1"></i>
                                        <small>Format kode akun benar</small>
                                    </div>
                                    <div class="akun-validation-item">
                                        <i class="fas fa-square-check text-muted mr-1"></i>
                                        <small>Sesuai dengan anggaran yang tersedia</small>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @php
                            // Calculate total based on pengajuan type
                            $totalAnggaran = 0;
                            if($pengajuan->tipe_pengajuan === 'usulan') {
                                foreach($pengajuan->detilPengajuan as $item) {
                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                }
                            } else {
                                foreach($pengajuan->detilRevisi as $item) {
                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                }
                            }

                            // Format as currency
                            $formattedTotal = number_format($totalAnggaran, 0, ',', '.');
                            @endphp

                            <div class="p-2" style="background: rgba(40, 167, 69, 0.1); border-radius: 0 0 8px 8px;">
                                <label class="font-weight-bold mb-0">Total Anggaran:</label>
                                <span class="d-block font-weight-bold text-success" style="font-size: 1.1em;" id="page-total-anggaran">Rp {{ $formattedTotal }}</span>
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
                    @if($pengajuan->tipe_pengajuan === 'usulan')
                    <div id="page-detil-pengajuan-container">
                        <h6 class="font-weight-bold">Daftar Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            @php
                                $showImageColumn = ($pengajuan->tahun_anggaran == session('tahunanggaran'));
                            @endphp
                            <table class="table table-bordered table-striped" id="tabel-detil-pengajuan">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="{{ $showImageColumn ? '20%' : '20%' }}">Kode Barang</th>
                                        <th width="{{ $showImageColumn ? '20%' : '20%' }}">Deskripsi</th>
                                        <th width="{{ $showImageColumn ? '15%' : '20%' }}">Keterangan</th>
                                        <th width="{{ $showImageColumn ? '10%' : '10%' }}">Kuantitas</th>
                                        <th width="{{ $showImageColumn ? '10%' : '10%' }}">Harga</th>
                                        <th width="{{ $showImageColumn ? '15%' : '10%' }}">Total</th>
                                        @if($showImageColumn)
                                            <th width="20%">Gambar</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalUsulan = 0; @endphp
                                    @forelse($pengajuan->detilPengajuan as $index => $item)
                                        @php
                                        $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                                        $itemTotal = $item->kuantitas * $item->harga;
                                        $totalUsulan += $itemTotal;
                                        @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $barang ? $barang->ur_sskel : 'Pengajuan Pemeliharaan' }}</td>
                                        <td>{{ $item->keterangan_barang }}</td>
                                        <td class="text-right">{{ $item->kuantitas }}</td>
                                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                        @if($showImageColumn)
                                        <td>
                                            @if($item->path_image)
                                            <a href="{{ asset('storage/' . $item->path_image) }}" data-toggle="lightbox" data-gallery="gallery-pengajuan">
                                                <img src="{{ asset('storage/' . $item->path_image) }}"
                                                    class="img-thumbnail" style="max-height: 100px; cursor: pointer;">
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $showImageColumn ? '7' : '6' }}" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Anggaran:</th>
                                        <th colspan="{{ $showImageColumn ? '2' : '2' }}">Rp {{ number_format($totalUsulan, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                    <div id="page-detil-revisi-container">
                        <h6 class="font-weight-bold">Daftar Revisi Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabel-detil-revisi">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Kode Perlengkapan</th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="20%">Keterangan</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="10%">Harga</th>
                                        <th width="10%">Total</th>
                                        <th width="20%">Gambar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalRevisi = 0; @endphp
                                    @forelse($pengajuan->detilRevisi as $index => $item)
                                        @php
                                        $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                                        $itemTotal = $item->kuantitas * $item->harga;
                                        $totalRevisi += $itemTotal;
                                        @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $barang ? $barang->ur_sskel : 'Pengajuan Pemeliharaan' }}</td>
                                        <td>{{ $item->keterangan_barang }}</td>
                                        <td class="text-right">{{ $item->kuantitas }}</td>
                                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                        <td>
                                            @if($item->path_image)
                                            <a href="{{ asset('storage/' . $item->path_image) }}" data-toggle="lightbox" data-gallery="gallery-revisi">
                                                <img src="{{ asset('storage/' . $item->path_image) }}"
                                                    class="img-thumbnail" style="max-height: 100px; cursor: pointer;">
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Anggaran Revisi:</th>
                                        <th colspan="2">Rp {{ number_format($totalRevisi, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Keterangan Tambahan -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Keterangan Tambahan</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan:</label>
                        <div class="p-2 border rounded bg-light">{{ $pengajuan->keterangan ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Container untuk status penolakan -->
            @if(strpos($pengajuan->status_pengajuan, 'Ditolak') !== false)
            <div id="page-status-penolakan">
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Status Penolakan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Status:</label>
                            <span class="d-block font-weight-bold text-danger" id="page-status-ditolak">{{ $pengajuan->status_pengajuan }}</span>
                        </div>

                        @if($pengajuan->alasan_penolakan_pelaksana)
                        <div class="form-group" id="page-penolakan-pelaksana-container">
                            <label class="font-weight-bold">Alasan Penolakan Pelaksana:</label>
                            <div class="p-3 bg-light border rounded" id="page-alasan-penolakan-pelaksana">
                                {{ $pengajuan->alasan_penolakan_pelaksana }}
                            </div>
                        </div>
                        @endif

                        @if($pengajuan->alasan_penolakan_koordinator)
                        <div class="form-group" id="page-penolakan-koordinator-container">
                            <label class="font-weight-bold">Alasan Penolakan Koordinator:</label>
                            <div class="p-3 bg-light border rounded" id="page-alasan-penolakan-koordinator">
                                {{ $pengajuan->alasan_penolakan_koordinator }}
                            </div>
                        </div>
                        @endif

                        @if($pengajuan->alasan_penolakan_perencanaan)
                        <div class="form-group" id="page-penolakan-perencanaan-container">
                            <label class="font-weight-bold">Alasan Penolakan Unit Perencanaan:</label>
                            <div class="p-3 bg-light border rounded" id="page-alasan-penolakan-perencanaan">
                                {{ $pengajuan->alasan_penolakan_perencanaan }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Histori Penolakan Perencanaan (hanya untuk Perencanaan) -->
            @if($pengajuan->alasan_penolakan_perencanaan)
            <div id="status-penolakan-histori">
                <div class="card border-info mb-3">
                    <div class="card-header bg-info text-white">
                        @if(strpos($pengajuan->status_pengajuan, 'Ditolak oleh Perencanaan') !== false)
                            <h6 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Status Penolakan</h6>
                        @else
                            <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Histori Penolakan Terakhir</h6>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Alasan Penolakan Unit Perencanaan:</label>
                            <div class="p-3 bg-light border rounded">
                                {{ $pengajuan->alasan_penolakan_perencanaan }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informasi Berita Acara -->
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
                                    <i class="fas fa-user-check text-primary mr-2"></i>
                                    <span id="operator-signed-status">
                                        @if($pengajuan->berita_acara_operator_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Operator ({{ $bagianPengusul->uraianbagian ?? '-'  }})
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Operator ({{ $bagianPengusul->uraianbagian ?? '-'  }})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    {{-- REFACTOR: Dynamic class berdasarkan status operator --}}
                                    <i class="fas fa-user-check {{ $pengajuan->berita_acara_operator_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="pelaksana-signed-status">
                                        @if($pengajuan->berita_acara_pelaksana_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    {{-- REFACTOR: Dynamic class berdasarkan status pelaksana --}}
                                    <i class="fas fa-user-check {{ $pengajuan->berita_acara_pelaksana_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="koordinator-signed-status">
                                        {{-- REFACTOR: Check berdasarkan timestamp bukan path --}}
                                        @if($pengajuan->berita_acara_koordinator_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @endif
                                    </span>
                                </div>
                                {{-- perencanaan --}}
                                <div class="d-flex align-items-center mb-1">
                                    {{-- REFACTOR: Dynamic class berdasarkan status perencanaan --}}
                                    <i class="fas fa-user-check {{ $pengajuan->berita_acara_perencanaan_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="perencanaan-signed-status">
                                        {{-- REFACTOR: Check berdasarkan timestamp bukan path --}}
                                        @if($pengajuan->berita_acara_perencanaan_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Bagian Perencanaan
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Bagian Perencanaan
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 mb-2" id="berita-acara-actions">
                                {{-- Tombol Kirim Link muncul jika Koordinator sudah ttd, tapi Perencanaan belum --}}
                                @if($pengajuan->berita_acara_koordinator_signed_date && !$pengajuan->berita_acara_perencanaan_signed_date)
                                    <button type="button" class="btn btn-primary" id="send-ba-link-btn" data-id="{{ $pengajuan->id }}">
                                        <i class="fas fa-signature mr-1"></i> Verifikasi & Tanda Tangani BA
                                    </button>
                                @endif

                                {{-- Tombol Download muncul jika Perencanaan sudah ttd --}}
                                @if($pengajuan->berita_acara_perencanaan_signed_date)
                                    <a href="{{ route('perencanaan.downloadBeritaAcaraSigned', $pengajuan->id) }}" class="btn btn-success" id="download-berita-acara-signed-button">
                                        <i class="fas fa-download mr-1"></i> Download Berita Acara
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dokumen status --}}
            {{-- <div class="card mb-3">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i>Status Verifikasi Dokumen</h6>
                </div>
                <div class="card-body">
                    <div id="verification-status-checklist">
                        <!-- Status Berita Acara -->
                        <div class="mb-1">
                            @if($pengajuan->berita_acara_koordinator_signed_path)
                                <i class="fas fa-check-circle text-success"></i> Berita Acara telah ditandatangani koordinator
                            @else
                                <i class="fas fa-times-circle text-danger"></i> Berita Acara belum ditandatangani koordinator
                            @endif
                        </div>

                        <!-- Status Review -->
                        <div class="mb-1 status-review-check">
                            <i class="fas fa-times-circle text-danger"></i> Review akun belum dilengkapi
                        </div>

                        <!-- Status message -->
                        <div class="mt-2">
                            @if($pengajuan->berita_acara_koordinator_signed_path)
                                <small class="text-info"><i class="fas fa-info-circle"></i> Lengkapi review akun untuk menyimpan</small>
                            @else
                                <small class="text-info"><i class="fas fa-info-circle"></i> Pastikan berita acara sudah ditandatangani oleh koordinator</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Form Review Unit Perencanaan -->
            <div class="card mb-3 mt-3" id="form-review-perencanaan">
                <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: #000;">
                    <h6 class="mb-0"><i class="fas fa-calculator mr-2"></i>Review Akun Anggaran - Unit Perencanaan</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Tugas Anda:</strong> Verifikasi bahwa kode akun yang digunakan sudah sesuai dengan klasifikasi anggaran dan jenis barang/perlengkapan yang diajukan.
                    </div>

                    <form id="form-review" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Status Review Akun:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="status_terima" name="status" value="Terima" class="custom-control-input" checked>
                                <label class="custom-control-label" for="status_terima">
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Akun Sudah Benar</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="status_tolak" name="status" value="Ditolak" class="custom-control-input">
                                <label class="custom-control-label" for="status_tolak">
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Akun Perlu Diperbaiki</span>
                                </label>
                            </div>
                        </div>

                        <div id="alasan-penolakan-container" class="form-group mt-3" style="display:none;">
                            <label for="alasan_penolakan" class="font-weight-bold">Keterangan Perbaikan Akun:</label>
                            <textarea id="alasan_penolakan" name="alasan_penolakan" class="form-control" rows="4" placeholder="Jelaskan kesalahan pada kode akun dan berikan kode akun yang benar..."></textarea>
                            <div class="invalid-feedback">Mohon berikan keterangan perbaikan akun.</div>
                            <small class="text-muted">Contoh: "Kode akun 521211 tidak sesuai. Untuk laptop, gunakan kode akun 521213 - Peralatan dan Mesin"</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="button" class="btn btn-secondary" id="simpan-review-button" data-id="{{ $pengajuan->id }}" disabled>
                                <i class="fas fa-lock mr-1"></i> Simpan Review Akun
                            </button>
                            <a href="{{ route('perencanaan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="form-group mt-4">
                @if ($pengajuan->status_pengajuan === 'Diajukan ke Unit Perencanaan dengan Rekomendasi' && !empty($pengajuan->dokumen_rekomendasi_bmn))
                    <a href="{{ route('koordinator.download_rekomendasi', $pengajuan->id) }}" target="_blank" class="btn btn-success mr-2" title="Download Surat Rekomendasi yang dibuat oleh Koordinator">
                        <i class="fas fa-file-check mr-1"></i> Download Surat Rekomendasi
                    </a>
                @endif
                <button type="button" class="btn btn-info mr-2" id="download-tor-button" data-id="{{ $pengajuan->id }}">
                    <i class="fas fa-download mr-1"></i> Download TOR
                </button>
                <button type="button" class="btn btn-primary mr-2" id="download-lampiran-button" data-id="{{ $pengajuan->id }}">
                    <i class="fas fa-file-alt mr-1"></i> Download Lampiran
                </button>

                @if($pengajuan->dokumen_pendukung)
                <button type="button" class="btn btn-info mr-2" id="download-dokumen-pendukung-button" data-id="{{ $pengajuan->id }}">
                    <i class="fas fa-file-download mr-1"></i> Download Dokumen Pendukung
                </button>
                @endif


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
                                                    <div class="col-5">Nomor Pengajuan</div>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // Fungsi validasi form untuk Unit Perencanaan
    function validateReviewForm() {
        // Cek status yang dipilih
        const status = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_penolakan').val().trim();

        // Cek apakah berita acara sudah di e-sign oleh perencanaan
        const perencanaanSigned = $('#perencanaan-signed-status').text().includes('Ditandatangani');

        // Validasi: jika status ditolak, harus ada alasan
        const alasanValid = (status !== 'Ditolak') || (status === 'Ditolak' && alasanPenolakan !== '');

        // PERBAIKAN: Jika status adalah "Ditolak", tidak perlu verifikasi BA
        const perencanaanSignatureValid = (status === 'Ditolak') || perencanaanSigned;

        // Button hanya aktif jika:
        // 1. Berita acara ditandatangani oleh perencanaan (KECUALI jika ditolak)
        // 2. Alasan valid (jika ditolak, harus ada alasan)
        if (perencanaanSignatureValid && alasanValid) {
            $('#simpan-review-button').prop('disabled', false)
                .removeClass('btn-secondary').addClass('btn-success')
                .html('<i class="fas fa-calculator mr-1"></i> Simpan Review Akun');
            return true;
        } else {
            // Set tombol ke disabled state
            $('#simpan-review-button').prop('disabled', true)
                .removeClass('btn-success').addClass('btn-secondary')
                .html('<i class="fas fa-lock mr-1"></i> Simpan Review Akun');

            // Generate tooltip message
            let tooltipMsg = "Untuk menyimpan review akun, pastikan: ";
            let missingItems = [];

            // PERBAIKAN: Hanya check tanda tangan jika status bukan "Ditolak"
            if (status !== 'Ditolak' && !perencanaanSigned) {
                missingItems.push("Berita Acara sudah ditandatangani oleh Unit Perencanaan");
            }

            if (status === 'Ditolak' && alasanPenolakan === '') {
                missingItems.push("Keterangan perbaikan akun telah diisi");
            }

            tooltipMsg += missingItems.join(", ");

            // Set tooltip
            $('#simpan-review-button').attr('data-toggle', 'tooltip')
                .attr('data-placement', 'top')
                .attr('title', tooltipMsg);

            // Initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            return false;
        }
    }

    function updateReviewStatus() {
        const status = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_penolakan').val().trim();

        // Cek apakah berita acara sudah ditandatangani perencanaan
        const perencanaanSigned = $('#perencanaan-signed-status').text().includes('Ditandatangani');

        // Validasi: jika status ditolak, harus ada alasan
        const alasanValid = (status !== 'Ditolak') || (status === 'Ditolak' && alasanPenolakan !== '');

        // Default menampilkan review sebagai dilengkapi/belum berdasarkan status yang dipilih
        if (alasanValid) {
            $('.status-review-check').html('<i class="fas fa-check-circle text-success"></i> Review akun dilengkapi (' +
                (status === 'Terima' ? 'Akun Sudah Benar' : 'Akun Perlu Diperbaiki') + ')');
        } else {
            $('.status-review-check').html('<i class="fas fa-times-circle text-danger"></i> Review akun belum dilengkapi (Keterangan Perbaikan Wajib Diisi)');
        }

        // Update status message keseluruhan
        // PERBAIKAN: Jika status "Ditolak", tidak perlu requirement tanda tangan perencanaan
        if (status === 'Ditolak') {
            if (alasanValid) {
                $('#verification-status-checklist .mt-2').html('<small class="text-success"><i class="fas fa-check-circle"></i> Review penolakan siap diproses</small>');
            } else {
                $('#verification-status-checklist .mt-2').html('<small class="text-info"><i class="fas fa-info-circle"></i> Lengkapi keterangan perbaikan akun untuk menyimpan</small>');
            }
        } else {
            // Untuk status "Terima" tetap perlu tanda tangan perencanaan
            if (perencanaanSigned && alasanValid) {
                $('#verification-status-checklist .mt-2').html('<small class="text-success"><i class="fas fa-check-circle"></i> Semua verifikasi lengkap, review akun siap diproses</small>');
            } else {
                let missingItems = [];
                if (!perencanaanSigned) {
                    missingItems.push("tanda tangan unit perencanaan pada berita acara");
                }

                if (missingItems.length > 0) {
                    $('#verification-status-checklist .mt-2').html('<small class="text-info"><i class="fas fa-info-circle"></i> Lengkapi: ' + missingItems.join(", ") + '</small>');
                } else {
                    $('#verification-status-checklist .mt-2').html('<small class="text-info"><i class="fas fa-info-circle"></i> Lengkapi review akun untuk menyimpan</small>');
                }
            }
        }
    }

    // Initialize form validation and status on page load
    validateReviewForm();
    updateReviewStatus();

    // Validasi form saat status berubah
    $('input[name="status"]').on('change', function() {
        validateReviewForm();
        updateReviewStatus();
    });

    // Validasi form saat alasan penolakan berubah
    $('#alasan_penolakan').on('input', function() {
        validateReviewForm();
        updateReviewStatus();
    });

    // Toggle alasan penolakan
    $('input[name="status"]').on('change', function() {
        if ($(this).val() === 'Ditolak') {
            $('#alasan-penolakan-container').slideDown();
        } else {
            $('#alasan-penolakan-container').slideUp();
        }
    });

    // Simpan review handler
    $('#simpan-review-button').on('click', function(event) {
        // Prevent default only if validation fails
        if (!validateReviewForm()) {
            event.preventDefault();

            // PERBAIKAN: Hanya check tanda tangan jika bukan penolakan
            const status = $('input[name="status"]:checked').val();
            const perencanaanSigned = $('#perencanaan-signed-status').text().includes('Ditandatangani');

            // Hanya tampilkan pesan tanda tangan jika status bukan "Ditolak"
            if (status !== 'Ditolak' && !perencanaanSigned) {
                Swal.fire({
                    title: 'Tanda Tangan Diperlukan',
                    text: 'Berita Acara harus ditandatangani oleh Unit Perencanaan sebelum menyimpan review akun',
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
                });
            }
            return;
        }

        const id = $(this).data('id');
        const status = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_penolakan').val();

        // Validation
        let isValid = true;

        if (status === 'Ditolak' && !alasanPenolakan.trim()) {
            $('#alasan_penolakan').addClass('is-invalid');
            isValid = false;
        } else {
            $('#alasan_penolakan').removeClass('is-invalid');
        }

        if (!isValid) {
            return;
        }

        // Confirmation message based on the selected status
        let confirmMessage = '';
        if (status === 'Terima') {
            confirmMessage = 'Apakah Anda yakin bahwa kode akun sudah benar dan sesuai?';
        } else {
            confirmMessage = 'Apakah Anda yakin akan menolak pengajuan ini karena kode akun perlu diperbaiki?';
        }

        // Submit form using AJAX
        Swal.fire({
            title: 'Konfirmasi Review Akun',
            text: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Display loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create FormData object
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('status', status);
                formData.append('alasan_penolakan', alasanPenolakan || '');

                $.ajax({
                    url: `${window.location.origin}/perencanaan_bmn/${id}/update-review`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Redirect ke halaman index setelah sukses
                                window.location.href = `${window.location.origin}/perencanaan_bmn`;
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat menyimpan review',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();

                        let errorMessage = 'Terjadi kesalahan pada server';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();
                            errorMessage = errorMessages.join(', ');
                        } else if (xhr.status === 400) {
                            // Handle business logic errors
                            errorMessage = xhr.responseJSON?.message || 'Permintaan tidak valid';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Event handler untuk tombol Download Berita Acara Tertandatangani
    $('#download-berita-acara-signed-button').on('click', function() {
        const id = $(this).data('id');
        const downloadUrl = `${window.location.origin}/perencanaan_bmn/${id}/download-ba-signed`;

        // Tampilkan loading spinner
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen Berita Acara tertandatangani',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Buka URL download di tab baru
        window.open(downloadUrl, '_blank');

        // Tutup loading spinner setelah 2 detik
        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Event handler untuk tombol Download TOR
    $('#download-tor-button').on('click', function() {
        const id = $(this).data('id');
        const downloadUrl = `${window.location.origin}/perencanaan_bmn/${id}/download-tor`;

        // Tampilkan loading spinner
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen TOR',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Buka URL download di tab baru
        window.open(downloadUrl, '_blank');

        // Tutup loading spinner setelah 2 detik
        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Event handler untuk tombol Download Lampiran
    $('#download-lampiran-button').on('click', function() {
        const id = $(this).data('id');
        const downloadUrl = `${window.location.origin}/perencanaan_bmn/${id}/download-lampiran`;

        // Tampilkan loading spinner
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen lampiran',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Buka URL download di tab baru
        window.open(downloadUrl, '_blank');

        // Tutup loading spinner setelah 2 detik
        setTimeout(() => {
            Swal.close();
        }, 2000);
    });

    // Initialize Ekko Lightbox for image previews (if available)
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    // Event handler untuk tombol Download Dokumen Pendukung
    $(document).on('click', '#download-dokumen-pendukung-button', function() {
        const id = $(this).data('id');
        const downloadUrl = `${window.location.origin}/perencanaan_bmn/${id}/download-dokumen`;

        // Tampilkan loading spinner
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan dokumen pendukung',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Buka URL download di tab baru
        window.open(downloadUrl, '_blank');

        // Tutup loading spinner setelah 2 detik
        setTimeout(() => {
            Swal.close();
        }, 2000);
    });
    $('#send-ba-link-btn').on('click', function() {
        const btn = $(this);
        const pengajuanId = btn.data('id');

        Swal.fire({
            title: 'Kirim Link Verifikasi?',
            text: "Link e-sign Berita Acara akan dikirim melalui WhatsApp kepada Eselon III Perencanaan. Lanjutkan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');

                $.ajax({
                    url: `{{ route('perencanaan.sendBAMagicLink', ['id' => $pengajuan->id]) }}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terkirim!', response.message, 'success');
                            btn.text('Link Telah Dikirim').removeClass('btn-primary').addClass('btn-secondary');
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                            btn.prop('disabled', false).html('<i class="fas fa-signature"></i> Verifikasi & Tanda Tangani BA');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan jaringan.';
                        Swal.fire('Error', errorMsg, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-signature"></i> Verifikasi & Tanda Tangani BA');
                    }
                });
            }
        });
    });
});
</script>
@endsection
