{{--resources/views/PerencanaanBMN/Bagian/koordinator_nonsbsk/ReviewPageKoordinatorNonSBSK.blade.php--}}
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
                        <li class="breadcrumb-item"><a href="{{ route('koordinator.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Review Pengajuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Alert Status -->
            <div class="alert alert-info mb-3" id="page-status-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span id="page-status-message">Silakan review pengajuan ini.</span>
                </div>
            </div>

            <style>
                .pdf-container {
                    border-radius: 4px;
                    overflow: hidden;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                    margin-top: 15px;
                    border-left: 3px solid #007bff;
                }

                .verification-details .row {
                    margin-bottom: 5px;
                }

                .verification-details .col-5 {
                    color: #6c757d;
                }

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

                .card-body .p-2 {
                    padding: 8px 12px !important;
                }
            </style>

            <!-- Hidden input untuk menyimpan status pengajuan -->
            <input type="hidden" id="current-status-pengajuan" value="{{ $pengajuan->status_pengajuan }}">

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
                                <label class="font-weight-bold mb-0">Jenis Formulir:</label>
                                <span class="d-block" id="page-jenis-formulir">{{ $pengajuan->jenis_formulir ?: 'Non SBSK' }}</span>
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
                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Bagian Pelaksana:</label>
                                <span class="d-block" id="page-bagian-pelaksana">{{ $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-' }}</span>
                            </div>
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
                            @if($pengajuan->tipe_pengajuan === 'revisi')
                            <div class="p-2 border-bottom" id="pengenal-section">
                                <label class="font-weight-bold mb-0">Kode Pengenal (untuk Revisi):</label>
                                <span class="d-block" id="page-kode-pengenal">{{ $pengajuan->kode_pengenal ?: '-' }}</span>
                            </div>
                            @else
                            <div class="p-2 border-bottom" id="akun-section">
                                <label class="font-weight-bold mb-0">Akun (untuk Usulan):</label>
                                <span class="d-block" id="page-akun">{{ $pengajuan->kode_akun ?: '-' }}</span>
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
                            $formattedTotal = number_format($totalAnggaran, 0, ',', '.');
                            @endphp

                            <div class="p-2">
                                <label class="font-weight-bold mb-0">Total Anggaran:</label>
                                <span class="d-block" id="page-total-anggaran">Rp {{ $formattedTotal }}</span>
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
                    <div id="detil-pengajuan-container" @if($pengajuan->tipe_pengajuan !== 'usulan') style="display:none" @endif>
                        <h6 class="font-weight-bold">Daftar Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            @php
                                $isReguler = ($pengajuan->jenis_formulir === 'Pengajuan Reguler');
                            @endphp
                            <table class="table table-items table-bordered" id="tabel-detil-pengajuan">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">{{ $isReguler ? 'Kode Barang/Pemeliharaan' : 'Kode Perlengkapan' }}</th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="25%">Keterangan</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="12%">Harga</th>
                                        <th width="13%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detil-items">
                                    @php
                                        $totalAnggaranPengajuan = 0;
                                    @endphp
                                    @if($pengajuan->tipe_pengajuan === 'usulan' && count($pengajuan->detilPengajuan) > 0)
                                        @foreach($pengajuan->detilPengajuan as $index => $item)
                                            @php
                                                if ($isReguler) {
                                                    $barangData = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                                                    $kodeDisplay = $item->kode_barang;
                                                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                                                    if ($barangData && !empty($barangData->ur_sskel)) {
                                                        $deskripsiDisplay = $barangData->ur_sskel;
                                                    } else {
                                                        // Jika tidak ditemukan atau kosong, gunakan default
                                                        $deskripsiDisplay = 'Pengajuan Pemeliharaan';
                                                    }
                                                    $keteranganBarang = $item->keterangan_barang ?: '-';
                                                } else {
                                                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')->where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                                                    $kodeDisplay = $item->kode_perlengkapan;
                                                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                                                    if ($perlengkapan && !empty($perlengkapan->deskripsi_perlengkapan)) {
                                                        $deskripsiDisplay = $perlengkapan->deskripsi_perlengkapan;
                                                    } else {
                                                        // Jika tidak ditemukan atau kosong, gunakan default
                                                        $deskripsiDisplay = 'Pengajuan Pemeliharaan';
                                                    }
                                                    $keteranganBarang = $item->keterangan_barang ?: '-';
                                                }
                                                $itemTotal = $item->kuantitas * $item->harga;
                                                $totalAnggaranPengajuan += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $kodeDisplay }}</td>
                                                <td>{{ $deskripsiDisplay }}</td>
                                                <td>{{ $keteranganBarang }}</td>
                                                <td>{{ $item->kuantitas }}</td>
                                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">Total Anggaran</th>
                                        <th id="grand-total">Rp {{ number_format($totalAnggaranPengajuan ?? 0, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div id="detil-revisi-container" @if($pengajuan->tipe_pengajuan !== 'revisi') style="display:none" @endif>
                        <h6 class="font-weight-bold">Daftar Revisi Barang/Perlengkapan</h6>
                        <div class="table-responsive">
                            <table class="table table-items" id="tabel-detil-revisi">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">{{ $isReguler ? 'Kode Barang/Pemeliharaan' : 'Kode Perlengkapan' }}</th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="25%">Keterangan</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="12%">Harga</th>
                                        <th width="13%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="revisi-items">
                                    @php
                                        $totalAnggaranRevisi = 0;
                                    @endphp
                                    @if($pengajuan->tipe_pengajuan === 'revisi' && count($pengajuan->detilRevisi) > 0)
                                        @foreach($pengajuan->detilRevisi as $index => $item)
                                            @php
                                                if ($isReguler) {
                                                    $barangData = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                                                    $kodeDisplay = $item->kode_barang;
                                                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                                                    if ($barangData && !empty($barangData->ur_sskel)) {
                                                        $deskripsiDisplay = $barangData->ur_sskel;
                                                    } else {
                                                        // Jika tidak ditemukan atau kosong, gunakan default
                                                        $deskripsiDisplay = 'Pengajuan Pemeliharaan';
                                                    }
                                                    $keteranganBarang = $item->keterangan_barang ?: '-';
                                                } else {
                                                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')->where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                                                    $kodeDisplay = $item->kode_perlengkapan;
                                                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                                                    if ($perlengkapan && !empty($perlengkapan->deskripsi_perlengkapan)) {
                                                        $deskripsiDisplay = $perlengkapan->deskripsi_perlengkapan;
                                                    } else {
                                                        // Jika tidak ditemukan atau kosong, gunakan default
                                                        $deskripsiDisplay = 'Pengajuan Pemeliharaan';
                                                    }
                                                    $keteranganBarang = $item->keterangan_barang ?: '-';
                                                }
                                                $itemTotal = $item->kuantitas * $item->harga;
                                                $totalAnggaranRevisi += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $kodeDisplay }}</td>
                                                <td>{{ $deskripsiDisplay }}</td>
                                                <td>{{ $keteranganBarang }}</td>
                                                <td class="text-right">{{ $item->kuantitas }}</td>
                                                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">Total Anggaran:</th>
                                        <th id="grand-total-revisi">Rp {{ number_format($totalAnggaranRevisi ?? 0, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tujuan Pengajuan -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Tujuan Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="p-2 border rounded bg-light">{{ $pengajuan->keterangan ?: '-' }}</div>
                </div>
            </div>

            <!-- Status Penolakan -->
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

            <!-- Histori Penolakan Koordinator -->
            @if($pengajuan->alasan_penolakan_koordinator && strpos($pengajuan->status_pengajuan, 'Ditolak oleh Koordinator') === false)
            <div id="status-penolakan-histori-koordinator">
                <div class="card border-warning mb-3">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Histori Penolakan Koordinator</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Alasan Penolakan Koordinator:</label>
                            <div class="p-3 bg-light border rounded">
                                {{ $pengajuan->alasan_penolakan_koordinator }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Histori Penolakan Perencanaan -->
            @if($pengajuan->alasan_penolakan_perencanaan && strpos($pengajuan->status_pengajuan, 'Ditolak oleh Perencanaan') === false)
            <div id="status-penolakan-histori-perencanaan">
                <div class="card border-info mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Histori Penolakan Perencanaan</h6>
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

            <!-- Verifikasi Berita Acara -->
            @php
                $showBeritaAcara = true;
                if ($pengajuan->jenis_formulir === 'Pengajuan Reguler' && $pengajuan->tipe_pengajuan === 'revisi') {
                    $showBeritaAcara = false;
                }
            @endphp

            @if($showBeritaAcara)
            <div class="card mb-3" id="berita-acara-section">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-file-signature mr-2"></i>Verifikasi Berita Acara</h6>
                </div>
                <div class="card-body">
                    <div id="berita-acara-verification" class="mb-2">
                        <div class="verification-status mb-2">
                            <h6 class="font-weight-bold mb-2">Status Tanda Tangan Berita Acara:</h6>
                            <div id="status-berita-acara">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-edit text-primary mr-2"></i>
                                    <span id="operator-signed-status">
                                        @if($pengajuan->berita_acara_operator_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Operator ({{ $bagianPengusul->uraianbagian ?? '-'  }})
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Operator ({{ $bagianPengusul->uraianbagian ?? '-'  }})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-check {{ $pengajuan->berita_acara_operator_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="pelaksana-signed-status">
                                        @if($pengajuan->berita_acara_pelaksana_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-shield {{ $pengajuan->berita_acara_pelaksana_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="koordinator-signed-status">
                                        @if($pengajuan->berita_acara_koordinator_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Koordinator Bagian Administrasi Barang Milik Negara
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-check {{ $pengajuan->berita_acara_perencanaan_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                    <span id="perencanaan-signed-status">
                                        @if($pengajuan->berita_acara_perencanaan_signed_date)
                                            <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh Bagian Perencanaan
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani oleh Bagian Perencanaan
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3" id="berita-acara-actions">
                                @if($pengajuan->berita_acara_koordinator_signed_date)
                                <button type="button" class="btn btn-outline-success" id="download-berita-acara-signed-button" data-id="{{ $pengajuan->id }}">
                                    <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Review Koordinator -->
            <div class="card mb-3" id="form-review-koordinator">
                <!-- Card Dokumen Rekomendasi -->
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Dokumen Rekomendasi (Opsional)</h6>
                </div>
                <div class="card-body" id="rekomendasi-card-body">
                    <div id="rekomendasi-status-info" class="alert" role="alert"></div>
                    <div id="rekomendasi-actions-container" class="mt-2">
                        <button type="button" class="btn btn-warning" id="regenerate-rekomendasi-button">
                            <i class="fas fa-edit mr-1"></i> Buat/Ubah Surat Rekomendasi
                        </button>
                        <button type="button" class="btn btn-info" id="preview-rekomendasi-button" style="display: none;">
                            <i class="fas fa-eye mr-1"></i> Preview Surat Rekomendasi
                        </button>
                        <button type="button" class="btn btn-success" id="download-rekomendasi-button" style="display: none;">
                            <i class="fas fa-download mr-1"></i> Download Surat Rekomendasi
                        </button>
                    </div>
                    <!-- Pesan ketika card disabled -->
                    <div id="rekomendasi-disabled-message" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Catatan:</strong> Dokumen rekomendasi tidak dapat diakses karena pengajuan sudah tidak dalam status review koordinator.
                    </div>
                </div>

                <!-- Card Review Pengajuan -->
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-check-circle mr-2"></i>Review Pengajuan (Keputusan Final)</h6>
                </div>
                <div class="card-body" id="review-card-body">
                    <form id="form-review">
                        @csrf
                        <div id="status-review-container" class="form-group">
                            <label class="font-weight-bold">Status Review:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="status_terima" name="status" value="Terima" class="custom-control-input" checked>
                                <label class="custom-control-label" for="status_terima">
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Terima Pengajuan</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="status_tolak" name="status" value="Ditolak" class="custom-control-input">
                                <label class="custom-control-label" for="status_tolak">
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Tolak Pengajuan</span>
                                </label>
                            </div>
                        </div>

                        <div id="alasan-penolakan-container" class="form-group mt-3" style="display:none;">
                            <label for="alasan_penolakan" class="font-weight-bold">Alasan Penolakan:</label>
                            <textarea id="alasan_penolakan" name="alasan_penolakan" class="form-control" rows="4" placeholder="Berikan alasan penolakan pengajuan ini..."></textarea>
                        </div>

                        <hr>

                        <div class="form-group mt-3">
                            <button type="button" class="btn btn-info" id="send-magic-link-button">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Magic Link Verifikasi
                            </button>
                            <button type="button" class="btn btn-secondary" id="simpan-review-button" data-id="{{ $pengajuan->id }}" disabled>
                                <i class="fas fa-lock mr-1"></i> Simpan Review
                            </button>
                            <a href="{{ route('koordinator.index') }}" class="btn btn-secondary">
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

            <!-- Download Buttons -->
            <div class="form-group mt-3">
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

        <!-- Modal PDF Preview -->
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
                                            <div class="verification-details">
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
                        <button type="button" class="btn btn-success" id="confirm-verification-button">
                            <i class="fas fa-signature mr-1"></i> Tanda Tangani Dokumen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rekomendasi -->
<div class="modal fade" id="rekomendasiModal" tabindex="-1" role="dialog" aria-labelledby="rekomendasiModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="rekomendasiModalLabel"><i class="fas fa-edit mr-2"></i>Buat Dokumen Rekomendasi Perubahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-rekomendasi">
                    <h6 class="font-weight-bold">Detail Rekomendasi Perubahan Akun</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 15%;">Kode Barang / Pemeliharaan</th>
                                    <th style="width: 15%;">Deskripsi Barang</th>
                                    <th style="width: 15%;">Keterangan</th>
                                    <th style="width: 15%;">Akun Semula</th>
                                    <th style="width: 20%;">Rekomendasi Akun Menjadi</th>
                                    <th style="width: 20%;">Keterangan Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-rekomendasi-items">
                                <tr>
                                    <td colspan="6" class="text-center p-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Memuat data...</span>
                                        </div>
                                        <p class="mt-2">Memuat data item...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="generate-rekomendasi-pdf-button">
                    <i class="fas fa-file-pdf mr-1"></i> Generate & Simpan Dokumen Rekomendasi
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const pengajuanId = {{ $pengajuan->id }};
    const csrfToken = '{{ csrf_token() }}';
    const baseUrl = `{{ url('/') }}`;
    const currentStatus = $('#current-status-pengajuan').val();

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
            // Disable card rekomendasi
            $('#rekomendasi-card-body').addClass('disabled-card');
            $('#rekomendasi-actions-container').hide();
            $('#rekomendasi-disabled-message').show();

            // Disable card review pengajuan
            $('#review-card-body').addClass('disabled-card');
            $('#form-review input, #form-review textarea, #form-review button').prop('disabled', true);
            $('#review-disabled-message').show();

            // Update pesan status halaman
            $('#page-status-message').text('Pengajuan ini sudah tidak dapat direview karena statusnya: ' + currentStatus);
            $('#page-status-info').removeClass('alert-info').addClass('alert-warning');
        } else {
            // Enable normal functionality
            $('#rekomendasi-card-body').removeClass('disabled-card');
            $('#rekomendasi-actions-container').show();
            $('#rekomendasi-disabled-message').hide();

            $('#review-card-body').removeClass('disabled-card');
            $('#form-review input, #form-review textarea').prop('disabled', false);
            $('#review-disabled-message').hide();

            // Pesan normal
            $('#page-status-message').text('Silakan review pengajuan ini.');
            $('#page-status-info').removeClass('alert-warning').addClass('alert-info');
        }
    }

    function updateRekomendasiStatus(message, type = 'info') {
        const rekomenInfo = $('#rekomendasi-status-info');
        rekomenInfo.removeClass('alert-info alert-warning alert-success alert-danger').addClass(`alert-${type}`);
        rekomenInfo.html(message);
    }

    function updateUI() {
        // Cek dulu apakah status diizinkan untuk review
        if (!checkPengajuanStatus()) {
            return; // Jika tidak diizinkan, skip update UI
        }

        const statusTerpilih = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_penolakan').val().trim();
        const isBeritaAcaraSigned = "{{ !empty($pengajuan->berita_acara_koordinator_signed_date) }}";
        const isRekomendasiGenerated = "{{ !empty($pengajuan->dokumen_rekomendasi_bmn) }}";
        const isRekomendasiSigned = "{{ !empty($pengajuan->rekomendasi_signed_date) }}";

        $('#alasan-penolakan-container').hide();
        $('#simpan-review-button').prop('disabled', true).removeClass('btn-primary btn-success').addClass('btn-secondary');
        $('#send-magic-link-button').prop('disabled', false);
        $('#regenerate-rekomendasi-button, #preview-rekomendasi-button, #download-rekomendasi-button').hide();

        if (isRekomendasiSigned === '1') {
            updateRekomendasiStatus('<strong>Status:</strong> Surat Rekomendasi telah dibuat dan diverifikasi. Dokumen tidak dapat diubah.', 'success');
            $('#preview-rekomendasi-button').show();
            $('#download-rekomendasi-button').show();
        } else if (isRekomendasiGenerated === '1') {
            updateRekomendasiStatus('<strong>Status:</strong> Surat Rekomendasi sudah dibuat namun BELUM diverifikasi (e-sign).', 'info');
            $('#regenerate-rekomendasi-button').show();
            $('#preview-rekomendasi-button').show();
        } else {
            updateRekomendasiStatus('<strong>Status:</strong> Surat Rekomendasi belum dibuat. Anda dapat membuatnya jika pengajuan ini memerlukan koreksi akun.', 'warning');
            $('#regenerate-rekomendasi-button').show();
        }

        if (statusTerpilih === 'Terima') {
            let allDocsSigned = (isBeritaAcaraSigned === '1') && (isRekomendasiGenerated === '1' ? isRekomendasiSigned === '1' : true);
            if (allDocsSigned) {
                $('#simpan-review-button').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                $('#send-magic-link-button').prop('disabled', true);
            }
        } else {
            $('#alasan-penolakan-container').show();
            if (alasanPenolakan !== '') {
                $('#simpan-review-button').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            }
        }
    }

    // Jalankan pengecekan status saat halaman dimuat
    toggleCardAccess();
    updateUI();

    // Event listeners hanya jika status diizinkan
    if (checkPengajuanStatus()) {
        $('input[name="status"]').on('change', updateUI);
        $('#alasan_penolakan').on('input', updateUI);
    }

    $('#simpan-review-button').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Pengajuan tidak dapat direview karena statusnya: ' + currentStatus, 'warning');
            return;
        }

        const status = $('input[name="status"]:checked').val();
        const alasanPenolakan = $('#alasan_penolakan').val();
        const confirmMessage = (status === 'Terima') ?
            'Apakah Anda yakin ingin MENYETUJUI pengajuan ini dan meneruskannya ke Unit Perencanaan?' :
            'Apakah Anda yakin ingin MENOLAK pengajuan ini?';

        Swal.fire({
            title: 'Konfirmasi Tindakan',
            text: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/koordinator_nonsbsk/${pengajuanId}/update-review`,
                    type: 'POST',
                    data: { _token: csrfToken, status: status, alasan_penolakan: alasanPenolakan },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() =>
                            window.location.href = "{{ route('koordinator.index') }}");
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', (xhr.responseJSON?.message || 'Terjadi kesalahan.'), 'error');
                    }
                });
            }
        });
    });

    $('#send-magic-link-button').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Magic Link tidak dapat dikirim karena status pengajuan: ' + currentStatus, 'warning');
            return;
        }

        let documentsToSign = ['berita_acara'];
        let docDescription = "Berita Acara";
        if ("{{ !empty($pengajuan->dokumen_rekomendasi_bmn) }}" === '1') {
            documentsToSign.push('surat_rekomendasi');
            docDescription = "Berita Acara dan Surat Rekomendasi";
        }
        Swal.fire({
            title: 'Kirim Magic Link Verifikasi?',
            html: `Link untuk menandatangani <b>${docDescription}</b> akan dikirim via WhatsApp. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ route('koordinator.sendMagicLink', ['id' => $pengajuan->id]) }}`,
                    type: 'POST',
                    data: { _token: csrfToken, documents_to_sign: documentsToSign },
                    success: (response) => Swal.fire('Terkirim!', response.message, 'success'),
                    error: (xhr) => Swal.fire('Error!', (xhr.responseJSON?.message || 'Terjadi kesalahan.'), 'error')
                });
            }
        });
    });

    $('#regenerate-rekomendasi-button').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Rekomendasi tidak dapat dibuat karena status pengajuan: ' + currentStatus, 'warning');
            return;
        }

        const tbody = $('#tabel-rekomendasi-items');
        $('#rekomendasiModal').modal('show');
        tbody.html(`<tr><td colspan="6" class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2">Mengambil data item...</p></td></tr>`);
        $.ajax({
            url: `{{ route('koordinator.get_items_for_recommendation', ['id' => $pengajuan->id]) }}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.items.length > 0) {
                    tbody.empty();
                    const akunDropdownHtml = `
                        <select name="akun_menjadi" class="form-control akun-menjadi-dropdown" style="width: 100%;">
                            <option value="">-- Pilih Akun Baru --</option>
                            <optgroup label="Belanja Modal Tanah">
                                <option value="531111">531111</option>
                                <option value="531112">531112</option>
                                <option value="531113">531113</option>
                                <option value="531114">531114</option>
                                <option value="531115">531115</option>
                                <option value="531116">531116</option>
                                <option value="531117">531117</option>
                            </optgroup>
                            <optgroup label="Belanja Modal Peralatan dan Mesin">
                                <option value="532111">532111</option>
                                <option value="532112">532112</option>
                                <option value="532113">532113</option>
                                <option value="532114">532114</option>
                                <option value="532115">532115</option>
                                <option value="532116">532116</option>
                                <option value="532117">532117</option>
                                <option value="532118">532118</option>
                            </optgroup>
                            <optgroup label="Belanja Modal Gedung dan Bangunan">
                                <option value="533111">533111</option>
                                <option value="533112">533112</option>
                                <option value="533113">533113</option>
                                <option value="533114">533114</option>
                                <option value="533115">533115</option>
                                <option value="533116">533116</option>
                                <option value="533117">533117</option>
                                <option value="533118">533118</option>
                                <option value="533121">533121</option>
                            </optgroup>
                            <optgroup label="Belanja Modal JIJ">
                                <option value="534111">534111</option>
                                <option value="534112">534112</option>
                                <option value="534113">534113</option>
                                <option value="534114">534114</option>
                                <option value="534115">534115</option>
                                <option value="534116">534116</option>
                                <option value="534117">534117</option>
                                <option value="534118">534118</option>
                                <option value="534121">534121</option>
                                <option value="534122">534122</option>
                                <option value="534123">534123</option>
                                <option value="534124">534124</option>
                                <option value="534125">534125</option>
                                <option value="534126">534126</option>
                                <option value="534127">534127</option>
                                <option value="534128">534128</option>
                                <option value="534131">534131</option>
                                <option value="534132">534132</option>
                                <option value="534133">534133</option>
                                <option value="534134">534134</option>
                                <option value="534135">534135</option>
                                <option value="534136">534136</option>
                                <option value="534137">534137</option>
                                <option value="534138">534138</option>
                                <option value="534141">534141</option>
                                <option value="534151">534151</option>
                                <option value="534161">534161</option>
                            </optgroup>
                            <optgroup label="Belanja Modal Lainnya">
                                <option value="536111">536111</option>
                                <option value="536112">536112</option>
                                <option value="536113">536113</option>
                                <option value="536114">536114</option>
                                <option value="536115">536115</option>
                                <option value="536116">536116</option>
                                <option value="536117">536117</option>
                                <option value="536118">536118</option>
                                <option value="536121">536121</option>
                            </optgroup>
                            <optgroup label="Belanja Ekstrakomptabel">
                                <option value="521252">521252</option>
                                <option value="521253">521253</option>
                                <option value="521254">521254</option>
                            </optgroup>
                            <optgroup label="Belanja Persediaan">
                                <option value="521811">521811</option>
                                <option value="521812">521812</option>
                                <option value="521813">521813</option>
                                <option value="521821">521821</option>
                                <option value="521822">521822</option>
                                <option value="521831">521831</option>
                                <option value="521832">521832</option>
                            </optgroup>
                            <optgroup label="Belanja Persediaan Pemeliharaan">
                                <option value="523112">523112</option>
                                <option value="523123">523123</option>
                                <option value="523134">523134</option>
                                <option value="523135">523135</option>
                                <option value="523136">523136</option>
                                <option value="523191">523191</option>
                            </optgroup>
                            <optgroup label="Belanja Pemeliharaan">
                                <option value="523111">523111</option>
                                <option value="523119">523119</option>
                                <option value="523121">523121</option>
                                <option value="523129">523129</option>
                                <option value="523132">523132</option>
                                <option value="523133">523133</option>
                                <option value="523199">523199</option>
                            </optgroup>
                            <optgroup label="Belanja Penambahan Nilai Peralatan dan Mesin">
                                <option value="532121">532121</option>
                            </optgroup>
                        </select>
                    `;
                    response.items.forEach(item => {
                        tbody.append(`
                            <tr data-item-id="${item.id}">
                                <td class="item-kode-barang"><strong>${item.kode_barang}</strong></td>
                                <td class="item-deskripsi">${item.deskripsi}</td>
                                <td class="item-keterangan">${item.keterangan}</td>
                                <td class="item-akun-semula"><strong>${item.akun_semula}</strong></td>
                                <td>${akunDropdownHtml}</td>
                                <td><input type="text" class="form-control" name="keterangan_rekomendasi" placeholder="Alasan perubahan"></td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center p-5">Tidak ada item yang dapat direkomendasikan.</td></tr>');
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="6" class="text-center p-5 text-danger">Gagal memuat data item. Silakan coba lagi.</td></tr>');
            }
        });
    });

    $('#generate-rekomendasi-pdf-button').on('click', function() {
        if (!checkPengajuanStatus()) {
            Swal.fire('Tidak Diizinkan', 'Rekomendasi tidak dapat dibuat karena status pengajuan: ' + currentStatus, 'warning');
            return;
        }

        let recommendedItems = [];
        $('#tabel-rekomendasi-items tr').each(function() {
            const row = $(this);
            const akunMenjadi = row.find('select[name="akun_menjadi"]').val();
            if (row.data('item-id') && akunMenjadi) {
                recommendedItems.push({
                    id: row.data('item-id'),
                    kode_barang: row.find('.item-kode-barang').text().trim(),
                    deskripsi: row.find('.item-deskripsi').text().trim(),
                    keterangan_barang: row.find('.item-keterangan').text().trim(),
                    akun_semula: row.find('.item-akun-semula').text().trim(),
                    akun_menjadi: akunMenjadi,
                    keterangan_rekomendasi: row.find('input[name="keterangan_rekomendasi"]').val().trim()
                });
            }
        });
        if (recommendedItems.length === 0) {
            Swal.fire('Validasi Gagal', 'Anda harus memilih "Rekomendasi Akun Menjadi" setidaknya untuk satu item.', 'warning');
            return;
        }
        $.ajax({
            url: `{{ route('koordinator.generate_rekomendasi', ['id' => $pengajuan->id]) }}`,
            type: 'POST',
            data: { _token: csrfToken, items: recommendedItems },
            success: function(response) {
                if (response.success) {
                    $('#rekomendasiModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', (xhr.responseJSON?.message || 'Terjadi kesalahan saat membuat dokumen.'), 'error');
            }
        });
    });

    $('#download-rekomendasi-button').on('click', function() {
        window.open(`{{ route('koordinator.download_rekomendasi', $pengajuan->id) }}`, '_blank');
    });

    $('#preview-rekomendasi-button').on('click', () =>
        window.open(`{{ route('koordinator.preview_rekomendasi', $pengajuan->id) }}`, '_blank'));

    function setupDownloadButton(buttonId, docType) {
        $(buttonId).on('click', function() {
            const id = $(this).data('id');
            const downloadUrl = `${baseUrl}/koordinator_nonsbsk/${id}/download-${docType}`;
            window.open(downloadUrl, '_blank');
        });
    }

    setupDownloadButton('#download-tor-button', 'tor');
    setupDownloadButton('#download-lampiran-button', 'lampiran');
    if ($('#download-dokumen-pendukung-button').length) {
        setupDownloadButton('#download-dokumen-pendukung-button', 'dokumen');
    }
    if ($('#download-berita-acara-signed-button').length) {
        $('#download-berita-acara-signed-button').on('click', function() {
            window.open(`${baseUrl}/koordinator_nonsbsk/${$(this).data('id')}/download-berita-acara-signed`, '_blank');
        });
    }
});
</script>

@endsection
