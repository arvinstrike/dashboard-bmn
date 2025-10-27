{{-- resources/views/PerencanaanBMN/Bagian/pelaksana_nonsbsk/ReviewPagePelaksana.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Review Pengajuan Non SBSK</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('pelaksana.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Review Pengajuan</li>
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
                        <span id="status-message">Silakan review pengajuan sebelum melakukan tindakan.</span>
                    </div>
                </div>

                <style>
                    /* Styling untuk page review */
                    .review-row {
                        margin-bottom: 10px;
                    }

                    .review-label {
                        display: block;
                        font-weight: bold;
                        margin-bottom: 0px;
                    }

                    .review-value {
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

                    #status-penolakan .review-value.p-3 {
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

                    .table-items th,
                    .table-items td {
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

                    .item-image-container {
                        position: relative;
                    }

                    .custom-file {
                        margin-bottom: 0.5rem;
                    }

                    .progress {
                        height: 0.5rem;
                    }

                    .remove-image {
                        position: absolute;
                        top: 0;
                        right: 0;
                        padding: 0.1rem 0.3rem;
                        border-radius: 0 0 0 0.25rem;
                    }
                </style>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Informasi Pengajuan</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="review-row p-2">
                                    <label class="review-label">Nomor Pengajuan:</label>
                                    <span class="review-value" id="id">{{ $pengajuan->id }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Tipe Pengajuan:</label>
                                    <span class="review-value"
                                        id="tipe-pengajuan">{{ ucfirst($pengajuan->tipe_pengajuan) }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Jenis Formulir:</label>
                                    <span class="review-value"
                                        id="jenis-formulir">{{ $pengajuan->jenis_formulir ?: 'Non SBSK' }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Tahun Anggaran:</label>
                                    <span class="review-value" id="tahun-anggaran">{{ $pengajuan->tahun_anggaran }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Status Pengajuan:</label>
                                    <span class="review-value" id="status-pengajuan">
                                        @php
                                            $statusClass = 'badge-secondary';
                                            if ($pengajuan->status_pengajuan === 'Diajukan ke Unit Pelaksana') {
                                                $statusClass = 'badge-info';
                                            } elseif ($pengajuan->status_pengajuan === 'Diajukan ke Koordinator') {
                                                $statusClass = 'badge-success';
                                            } elseif (strpos($pengajuan->status_pengajuan, 'Ditolak') !== false) {
                                                $statusClass = 'badge-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $pengajuan->status_pengajuan }}</span>
                                    </span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Tanggal Pengajuan:</label>
                                    <span class="review-value"
                                        id="tanggal-pengajuan">{{ $pengajuan->created_at->format('d-m-Y H:i') }}</span>
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
                                <div class="review-row p-2">
                                    <label class="review-label">Bagian Pengusul:</label>
                                    <span class="review-value"
                                        id="bagian-pengusul">{{ $bagianPengusul->uraianbagian ?? '-' }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Biro Pengusul:</label>
                                    <span class="review-value"
                                        id="biro-pengusul">{{ $biroPengusul->uraianbiro ?? '-' }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Bagian Pelaksana:</label>
                                    <span class="review-value"
                                        id="bagian-pelaksana">{{ $bagianPelaksana->uraianbagian ?? '-' }}</span>
                                </div>
                                {{-- <div class="review-row p-2">
                                <label class="review-label">Biro Pelaksana:</label>
                                <span class="review-value" id="biro-pelaksana">{{ $biroPelaksana->uraianbiro ?? '-' }}</span>
                            </div> --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Informasi Anggaran</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="review-row p-2" id="pengenal-section"
                                    @if ($pengajuan->tipe_pengajuan !== 'revisi') style="display:none" @endif>
                                    <label class="review-label">Kode Pengenal (untuk Revisi):</label>
                                    <span class="review-value"
                                        id="kode-pengenal">{{ $pengajuan->kode_pengenal ?: '-' }}</span>
                                </div>
                                <div class="review-row p-2" id="akun-section"
                                    @if ($pengajuan->tipe_pengajuan !== 'usulan') style="display:none" @endif>
                                    <label class="review-label">Akun (untuk Usulan):</label>
                                    <span class="review-value" id="akun">{{ $pengajuan->kode_akun ?: '-' }}</span>
                                </div>
                                <div class="review-row p-2">
                                    <label class="review-label">Total Anggaran:</label>
                                    <span class="review-value" id="total-anggaran">
                                        @php
                                            $totalAnggaran = 0;
                                            if (
                                                $pengajuan->tipe_pengajuan === 'usulan' &&
                                                count($pengajuan->detilPengajuan) > 0
                                            ) {
                                                foreach ($pengajuan->detilPengajuan as $item) {
                                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                                }
                                            } elseif (
                                                $pengajuan->tipe_pengajuan === 'revisi' &&
                                                count($pengajuan->detilRevisi) > 0
                                            ) {
                                                foreach ($pengajuan->detilRevisi as $item) {
                                                    $totalAnggaran += $item->kuantitas * $item->harga;
                                                }
                                            }
                                            echo 'Rp ' . number_format($totalAnggaran, 0, ',', '.');
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Daftar Barang/Perlengkapan</h6>
                    </div>
                    <div class="card-body">
                        <div id="detil-pengajuan-container" @if ($pengajuan->tipe_pengajuan !== 'usulan') style="display:none" @endif>
                            <div class="table-responsive">
                                @php
                                    $showImageColumn = $pengajuan->tahun_anggaran == session('tahunanggaran');
                                    $isReguler = $pengajuan->jenis_formulir === 'Pengajuan Reguler';
                                @endphp
                                <table class="table table-items" id="tabel-detil-pengajuan">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="{{ $showImageColumn ? '20%' : '15%' }}">
                                                {{ $isReguler ? 'Kode Barang' : 'Kode Perlengkapan' }}
                                            </th>
                                            <th width="{{ $showImageColumn ? '20%' : '15%' }}">Deskripsi</th>
                                            <th width="{{ $showImageColumn ? '20%' : '25%' }}">Keterangan</th>
                                            <th width="{{ $showImageColumn ? '10%' : '10%' }}">Kuantitas</th>
                                            <th width="{{ $showImageColumn ? '10%' : '15%' }}">Harga</th>
                                            <th width="{{ $showImageColumn ? '15%' : '20%' }}">Total</th>
                                            @if ($showImageColumn)
                                                <th width="20%">Gambar</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="detil-items">
                                        @php
                                            $totalAnggaranPengajuan = 0;
                                        @endphp
                                        @if ($pengajuan->tipe_pengajuan === 'usulan' && count($pengajuan->detilPengajuan) > 0)
                                            @foreach ($pengajuan->detilPengajuan as $index => $item)
                                                @php
                                                    if ($isReguler) {
                                                        // Untuk pengajuan reguler, ambil dari tabel t_brg
                                                        $barangData = DB::table('t_brg')
                                                            ->where('kd_brg', $item->kode_barang)
                                                            ->first();
                                                        $kodeDisplay = $item->kode_barang;
                                                        $deskripsiDisplay = $barangData ? $barangData->ur_sskel : 'Pengajuan Pemeliharaan';
                                                    } else {
                                                        // Untuk pengajuan non-SBSK, ambil dari tabel perlengkapan
                                                        $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                                                            ->where('kode_perlengkapan', $item->kode_perlengkapan)
                                                            ->first();
                                                        $kodeDisplay = $item->kode_perlengkapan;
                                                        $deskripsiDisplay = $perlengkapan
                                                            ? $perlengkapan->deskripsi_perlengkapan
                                                            : 'Pengajuan Pemeliharaan';
                                                    }
                                                    $itemTotal = $item->kuantitas * $item->harga;
                                                    $totalAnggaranPengajuan += $itemTotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $kodeDisplay }}</td>
                                                    <td>{{ $deskripsiDisplay }}</td>
                                                    <td>{{ $item->keterangan_barang ?: '-' }}</td>
                                                    <td class="text-right">{{ $item->kuantitas }}</td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                    <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                                    </td>
                                                    @if ($showImageColumn)
                                                        <td>
                                                            <div class="item-image-container"
                                                                data-item-id="{{ $item->id }}">
                                                                @if ($item->path_image)
                                                                    <div class="mb-2">
                                                                        <img src="{{ asset('storage/' . $item->path_image) }}"
                                                                            class="img-thumbnail"
                                                                            style="max-height: 100px;">
                                                                    </div>
                                                                @endif
                                                                @if (in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana', 'Ditolak oleh Koordinator']))
                                                                    <div class="custom-file">
                                                                        <input type="file"
                                                                            class="custom-file-input item-image-input"
                                                                            id="image_{{ $item->id }}"
                                                                            name="image_{{ $item->id }}"
                                                                            data-item-id="{{ $item->id }}"
                                                                            accept="image/*">
                                                                        <label class="custom-file-label"
                                                                            for="image_{{ $item->id }}">Pilih
                                                                            gambar</label>
                                                                    </div>
                                                                    <div class="progress mt-2" style="display: none;">
                                                                        <div class="progress-bar" role="progressbar"
                                                                            style="width: 0%"></div>
                                                                    </div>
                                                                    <small class="form-text text-muted">Max: 5MB, Format:
                                                                        JPG, PNG, GIF</small>
                                                                    <div class="invalid-feedback">Gambar wajib diupload.
                                                                    </div>
                                                                @else
                                                                    @if (!$item->path_image)
                                                                        <span class="text-muted">Tidak ada gambar</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="{{ $showImageColumn ? '7' : '6' }}" class="text-center">
                                                    Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-right">Total Anggaran:</th>
                                            <th id="grand-total" colspan="{{ $showImageColumn ? '2' : '2' }}">Rp
                                                {{ number_format($totalAnggaranPengajuan ?? 0, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div id="detil-revisi-container" @if ($pengajuan->tipe_pengajuan !== 'revisi') style="display:none" @endif>
                            <h6 class="font-weight-bold">Daftar Revisi Barang/Perlengkapan</h6>
                            <div class="table-responsive">
                                <table class="table table-items" id="tabel-detil-revisi">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">{{ $isReguler ? 'Kode Barang' : 'Kode Perlengkapan' }}</th>
                                            <th width="20%">Deskripsi</th>
                                            <th width="10%">Kuantitas</th>
                                            <th width="10%">Harga</th>
                                            <th width="15%">Total</th>
                                            <th width="20%">Gambar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="revisi-items">
                                        @php
                                            $totalAnggaranRevisi = 0;
                                        @endphp
                                        @if ($pengajuan->tipe_pengajuan === 'revisi' && count($pengajuan->detilRevisi) > 0)
                                            @foreach ($pengajuan->detilRevisi as $index => $item)
                                                @php
                                                    if ($isReguler) {
                                                        // Untuk pengajuan reguler, ambil dari tabel t_brg
                                                        $barangData = DB::table('t_brg')
                                                            ->where('kd_brg', $item->kode_barang)
                                                            ->first();
                                                        $kodeDisplay = $item->kode_barang;
                                                        $deskripsiDisplay = $barangData ? $barangData->ur_sskel : 'Pengajuan Pemeliharaan';
                                                    } else {
                                                        // Untuk pengajuan non-SBSK, ambil dari tabel perlengkapan
                                                        $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                                                            ->where('kode_perlengkapan', $item->kode_perlengkapan)
                                                            ->first();
                                                        $kodeDisplay = $item->kode_perlengkapan;
                                                        $deskripsiDisplay = $perlengkapan
                                                            ? $perlengkapan->deskripsi_perlengkapan
                                                            : 'Pengajuan Pemeliharaan';
                                                    }
                                                    $itemTotal = $item->kuantitas * $item->harga;
                                                    $totalAnggaranRevisi += $itemTotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $kodeDisplay }}</td>
                                                    <td>{{ $deskripsiDisplay }}</td>
                                                    <td class="text-right">{{ $item->kuantitas }}</td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                    <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <div class="item-image-container"
                                                            data-item-id="{{ $item->id }}">
                                                            @if ($item->path_image)
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $item->path_image) }}"
                                                                        class="img-thumbnail" style="max-height: 100px;">
                                                                </div>
                                                            @endif
                                                            @if (in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana', 'Ditolak oleh Koordinator']))
                                                                <div class="custom-file">
                                                                    <input type="file"
                                                                        class="custom-file-input item-image-input"
                                                                        id="image_{{ $item->id }}"
                                                                        name="image_{{ $item->id }}"
                                                                        data-item-id="{{ $item->id }}"
                                                                        accept="image/*">
                                                                    <label class="custom-file-label"
                                                                        for="image_{{ $item->id }}">Pilih
                                                                        gambar</label>
                                                                </div>
                                                                <div class="progress mt-2" style="display: none;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: 0%"></div>
                                                                </div>
                                                                <small class="form-text text-muted">Max: 5MB, Format: JPG,
                                                                    PNG, GIF</small>
                                                                <div class="invalid-feedback">Gambar wajib diupload.</div>
                                                            @else
                                                                @if (!$item->path_image)
                                                                    <span class="text-muted">Tidak ada gambar</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
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
                                            <th colspan="5" class="text-right">Total Anggaran:</th>
                                            <th id="grand-total-revisi" colspan="2">Rp
                                                {{ number_format($totalAnggaranRevisi ?? 0, 0, ',', '.') }}</th>
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
                        <div class="review-row">
                            {{-- tambahain console log isi dari keterngan --}}
                            <script>
                                console.log("Keterangan: ", "{{ $pengajuan->keterangan }}");
                            </script>
                            <div class="review-value p-2" id="keterangan">{{ $pengajuan->keterangan ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                @if (strpos($pengajuan->status_pengajuan, 'Ditolak') !== false)
                    <div id="status-penolakan">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Status Penolakan</h5>
                            </div>
                            <div class="card-body">
                                <div class="review-row">
                                    <label class="review-label">Status:</label>
                                    <span class="review-value font-weight-bold text-danger"
                                        id="status-ditolak">{{ $pengajuan->status_pengajuan }}</span>
                                </div>
                                @if ($pengajuan->alasan_penolakan_pelaksana)
                                    <div class="review-row" id="penolakan-pelaksana-container">
                                        <label class="review-label">Alasan Penolakan Pelaksana:</label>
                                        <div class="review-value p-3 bg-light border rounded"
                                            id="alasan-penolakan-pelaksana">{{ $pengajuan->alasan_penolakan_pelaksana }}
                                        </div>
                                    </div>
                                @endif
                                @if ($pengajuan->alasan_penolakan_koordinator)
                                    <div class="review-row" id="penolakan-koordinator-container">
                                        <label class="review-label">Alasan Penolakan Koordinator:</label>
                                        <div class="review-value p-3 bg-light border rounded"
                                            id="alasan-penolakan-koordinator">
                                            {{ $pengajuan->alasan_penolakan_koordinator }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Histori Penolakan Pelaksana (hanya untuk Pelaksana) -->
                @if($pengajuan->alasan_penolakan_pelaksana)
                <div id="status-penolakan-histori">
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning text-white">
                            @if(strpos($pengajuan->status_pengajuan, 'Ditolak Pelaksana') !== false)
                                <h6 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Status Penolakan</h6>
                            @else
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Histori Penolakan Terakhir</h6>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="review-row">
                                <label class="review-label">Alasan Penolakan Pelaksana:</label>
                                <div class="review-value p-3 bg-light border rounded">
                                    {{ $pengajuan->alasan_penolakan_pelaksana }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @php
                    $showBeritaAcara = true;
                    if ($pengajuan->jenis_formulir === 'Pengajuan Reguler' && $pengajuan->tipe_pengajuan === 'revisi') {
                        $showBeritaAcara = false;
                    }
                @endphp

                @if ($showBeritaAcara)
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
                                            <i class="fas fa-user-edit text-primary mr-2"></i>
                                            <span id="operator-signed-status">
                                                @if ($pengajuan->berita_acara_operator_signed_date)
                                                    <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh
                                                    Operator ({{ $bagianPengusul->uraianbagian ?? '-' }})
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani
                                                    oleh Operator ({{ $bagianPengusul->uraianbagian ?? '-' }})
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            {{-- REFACTOR: Dynamic class berdasarkan status operator --}}
                                            <i
                                                class="fas fa-user-check {{ $pengajuan->berita_acara_operator_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                            <span id="pelaksana-signed-status">
                                                @if ($pengajuan->berita_acara_pelaksana_signed_date)
                                                    <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh
                                                    Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani
                                                    oleh Pelaksana ({{ $bagianPelaksana->uraianbagian ?? '-' }})
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            {{-- REFACTOR: Dynamic class berdasarkan status pelaksana --}}
                                            <i
                                                class="fas fa-user-shield {{ $pengajuan->berita_acara_pelaksana_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                            <span id="koordinator-signed-status">
                                                {{-- REFACTOR: Check berdasarkan timestamp bukan path --}}
                                                @if ($pengajuan->berita_acara_koordinator_signed_date)
                                                    <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh
                                                    Koordinator Bagian Administrasi Barang Milik Negara
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani
                                                    oleh Koordinator Bagian Administrasi Barang Milik Negara
                                                @endif
                                            </span>
                                        </div>
                                        {{-- perencanaan --}}
                                        <div class="d-flex align-items-center mb-1">
                                            {{-- REFACTOR: Dynamic class berdasarkan status perencanaan --}}
                                            <i
                                                class="fas fa-user-check {{ $pengajuan->berita_acara_perencanaan_signed_date ? 'text-primary' : 'text-secondary' }} mr-2"></i>
                                            <span id="perencanaan-signed-status">
                                                {{-- REFACTOR: Check berdasarkan timestamp bukan path --}}
                                                @if ($pengajuan->berita_acara_perencanaan_signed_date)
                                                    <i class="fas fa-check-circle text-success"></i> Ditandatangani oleh
                                                    Bagian Perencanaan
                                                @else
                                                    <i class="fas fa-times-circle text-danger"></i> Belum ditandatangani
                                                    oleh Bagian Perencanaan
                                                @endif
                                            </span>
                                        </div>

                                    </div>
                                    <div class="mt-3 mb-2" id="berita-acara-actions">
                                        {{--                                --}}{{-- REFACTOR: Logic tombol verifikasi berdasarkan timestamp --}}
                                        {{--                                @if ($pengajuan->berita_acara_operator_signed_date && !$pengajuan->berita_acara_pelaksana_signed_date && in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana'])) --}}
                                        {{--                                    <button type="button" class="btn btn-outline-primary" id="verify-berita-acara-button" data-id="{{ $pengajuan->id }}"> --}}
                                        {{--                                        <i class="fas fa-file-signature mr-1"></i> Tanda Tangani Berita Acara --}}
                                        {{--                                    </button> --}}
                                        {{--                                @endif --}}
                                        <div id="verification-buttons-pelaksana" class="mb-4">
                                        </div>
                                        {{-- REFACTOR: Logic tombol download berdasarkan timestamp dan path --}}
                                        @if ($pengajuan->berita_acara_pelaksana_signed_date && $pengajuan->berita_acara_signed_path)
                                            <button type="button" class="btn btn-outline-success"
                                                id="download-berita-acara-signed-button" data-id="{{ $pengajuan->id }}">
                                                <i class="fas fa-download mr-1"></i> Download Berita Acara Tertandatangani
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana', 'Ditolak oleh Koordinator']))
                    <div class="card mb-3 mt-3" id="form-review-pelaksana">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle mr-2"></i>Mata Anggaran</h6>
                        </div>
                        <div class="card-body">
                            <form id="form-review" method="post">
                                @csrf
                                {{-- Untuk case pengajuan revisi tahun anggaran berjalan, sudah ada mata anggarannya tinggal dipilih --}}
                                <div class="form-group" id="kode-pengenal-container"
                                    style="{{ $pengajuan->tipe_pengajuan == 'revisi' ? '' : 'display:none' }}">
                                    <label for="kode_pengenal" class="font-weight-bold">Kode Pengenal:</label>
                                    <select name="kode_pengenal" id="kode_pengenal" class="form-control">
                                        <option value="">-- Pilih Kode Pengenal --</option>
                                    </select>
                                    <div class="invalid-feedback">Kode pengenal harus dipilih untuk pengajuan revisi.</div>
                                    <small class="form-text text-muted">Pilih kode pengenal untuk pengajuan tipe
                                        Revisi</small>
                                </div>

                                {{-- Untuk case pengajuan tahun +1, belum ada mata anggarannya jadi pilih akun untuk membentuk mata anggaran yang baru --}}
                                <div class="form-group" id="akun-dropdown-section"
                                    style="{{ $pengajuan->tipe_pengajuan == 'usulan' ? '' : 'display:none' }}">
                                    <label for="akun_dropdown" class="font-weight-bold">Akun:</label>
                                    <select name="akun_dropdown" id="akun_dropdown" class="form-control">
                                        <option value="">-- Pilih Akun --</option>
                                        <optgroup label="Belanja Modal Tanah">
                                            <option value="531111"
                                                {{ $pengajuan->kode_akun == '531111' ? 'selected' : '' }}>531111</option>
                                            <option value="531112"
                                                {{ $pengajuan->kode_akun == '531112' ? 'selected' : '' }}>531112</option>
                                            <option value="531113"
                                                {{ $pengajuan->kode_akun == '531113' ? 'selected' : '' }}>531113</option>
                                            <option value="531114"
                                                {{ $pengajuan->kode_akun == '531114' ? 'selected' : '' }}>531114</option>
                                            <option value="531115"
                                                {{ $pengajuan->kode_akun == '531115' ? 'selected' : '' }}>531115</option>
                                            <option value="531116"
                                                {{ $pengajuan->kode_akun == '531116' ? 'selected' : '' }}>531116</option>
                                            <option value="531117"
                                                {{ $pengajuan->kode_akun == '531117' ? 'selected' : '' }}>531117</option>
                                        </optgroup>
                                        <optgroup label="Belanja Modal Peralatan dan Mesin">
                                            <option value="532111"
                                                {{ $pengajuan->kode_akun == '532111' ? 'selected' : '' }}>532111</option>
                                            <option value="532112"
                                                {{ $pengajuan->kode_akun == '532112' ? 'selected' : '' }}>532112</option>
                                            <option value="532113"
                                                {{ $pengajuan->kode_akun == '532113' ? 'selected' : '' }}>532113</option>
                                            <option value="532114"
                                                {{ $pengajuan->kode_akun == '532114' ? 'selected' : '' }}>532114</option>
                                            <option value="532115"
                                                {{ $pengajuan->kode_akun == '532115' ? 'selected' : '' }}>532115</option>
                                            <option value="532116"
                                                {{ $pengajuan->kode_akun == '532116' ? 'selected' : '' }}>532116</option>
                                            <option value="532117"
                                                {{ $pengajuan->kode_akun == '532117' ? 'selected' : '' }}>532117</option>
                                            <option value="532118"
                                                {{ $pengajuan->kode_akun == '532118' ? 'selected' : '' }}>532118</option>

                                        </optgroup>
                                        <optgroup label="Belanja Modal Gedung dan Bangunan">
                                            <option value="533111"
                                                {{ $pengajuan->kode_akun == '533111' ? 'selected' : '' }}>533111</option>
                                            <option value="533112"
                                                {{ $pengajuan->kode_akun == '533112' ? 'selected' : '' }}>533112</option>
                                            <option value="533113"
                                                {{ $pengajuan->kode_akun == '533113' ? 'selected' : '' }}>533113</option>
                                            <option value="533114"
                                                {{ $pengajuan->kode_akun == '533114' ? 'selected' : '' }}>533114</option>
                                            <option value="533115"
                                                {{ $pengajuan->kode_akun == '533115' ? 'selected' : '' }}>533115</option>
                                            <option value="533116"
                                                {{ $pengajuan->kode_akun == '533116' ? 'selected' : '' }}>533116</option>
                                            <option value="533117"
                                                {{ $pengajuan->kode_akun == '533117' ? 'selected' : '' }}>533117</option>
                                            <option value="533118"
                                                {{ $pengajuan->kode_akun == '533118' ? 'selected' : '' }}>533118</option>
                                            <option value="533121"
                                                {{ $pengajuan->kode_akun == '533121' ? 'selected' : '' }}>533121</option>
                                        </optgroup>
                                        <optgroup label="Belanja Modal JIJ">
                                            <option value="534111"
                                                {{ $pengajuan->kode_akun == '534111' ? 'selected' : '' }}>534111</option>
                                            <option value="534112"
                                                {{ $pengajuan->kode_akun == '534112' ? 'selected' : '' }}>534112</option>
                                            <option value="534113"
                                                {{ $pengajuan->kode_akun == '534113' ? 'selected' : '' }}>534113</option>
                                            <option value="534114"
                                                {{ $pengajuan->kode_akun == '534114' ? 'selected' : '' }}>534114</option>
                                            <option value="534115"
                                                {{ $pengajuan->kode_akun == '534115' ? 'selected' : '' }}>534115</option>
                                            <option value="534116"
                                                {{ $pengajuan->kode_akun == '534116' ? 'selected' : '' }}>534116</option>
                                            <option value="534117"
                                                {{ $pengajuan->kode_akun == '534117' ? 'selected' : '' }}>534117</option>
                                            <option value="534118"
                                                {{ $pengajuan->kode_akun == '534118' ? 'selected' : '' }}>534118</option>
                                            <option value="534121"
                                                {{ $pengajuan->kode_akun == '534121' ? 'selected' : '' }}>534121</option>
                                            <option value="534122"
                                                {{ $pengajuan->kode_akun == '534122' ? 'selected' : '' }}>534122</option>
                                            <option value="534123"
                                                {{ $pengajuan->kode_akun == '534123' ? 'selected' : '' }}>534123</option>
                                            <option value="534124"
                                                {{ $pengajuan->kode_akun == '534124' ? 'selected' : '' }}>534124</option>
                                            <option value="534125"
                                                {{ $pengajuan->kode_akun == '534125' ? 'selected' : '' }}>534125</option>
                                            <option value="534126"
                                                {{ $pengajuan->kode_akun == '534126' ? 'selected' : '' }}>534126</option>
                                            <option value="534127"
                                                {{ $pengajuan->kode_akun == '534127' ? 'selected' : '' }}>534127</option>
                                            <option value="534128"
                                                {{ $pengajuan->kode_akun == '534128' ? 'selected' : '' }}>534128</option>
                                            <option value="534131"
                                                {{ $pengajuan->kode_akun == '534131' ? 'selected' : '' }}>534131</option>
                                            <option value="534132"
                                                {{ $pengajuan->kode_akun == '534132' ? 'selected' : '' }}>534132</option>
                                            <option value="534133"
                                                {{ $pengajuan->kode_akun == '534133' ? 'selected' : '' }}>534133</option>
                                            <option value="534134"
                                                {{ $pengajuan->kode_akun == '534134' ? 'selected' : '' }}>534134</option>
                                            <option value="534135"
                                                {{ $pengajuan->kode_akun == '534135' ? 'selected' : '' }}>534135</option>
                                            <option value="534136"
                                                {{ $pengajuan->kode_akun == '534136' ? 'selected' : '' }}>534136</option>
                                            <option value="534137"
                                                {{ $pengajuan->kode_akun == '534137' ? 'selected' : '' }}>534137</option>
                                            <option value="534138"
                                                {{ $pengajuan->kode_akun == '534138' ? 'selected' : '' }}>534138</option>
                                            <option value="534141"
                                                {{ $pengajuan->kode_akun == '534141' ? 'selected' : '' }}>534141</option>
                                            <option value="534151"
                                                {{ $pengajuan->kode_akun == '534151' ? 'selected' : '' }}>534151</option>
                                            <option value="534161"
                                                {{ $pengajuan->kode_akun == '534161' ? 'selected' : '' }}>534161</option>
                                        </optgroup>
                                        <optgroup label="Belanja Modal Lainnya">
                                            <option value="536111"
                                                {{ $pengajuan->kode_akun == '536111' ? 'selected' : '' }}>536111</option>
                                            <option value="536112"
                                                {{ $pengajuan->kode_akun == '536112' ? 'selected' : '' }}>536112</option>
                                            <option value="536113"
                                                {{ $pengajuan->kode_akun == '536113' ? 'selected' : '' }}>536113</option>
                                            <option value="536114"
                                                {{ $pengajuan->kode_akun == '536114' ? 'selected' : '' }}>536114</option>
                                            <option value="536115"
                                                {{ $pengajuan->kode_akun == '536115' ? 'selected' : '' }}>536115</option>
                                            <option value="536116"
                                                {{ $pengajuan->kode_akun == '536116' ? 'selected' : '' }}>536116</option>
                                            <option value="536117"
                                                {{ $pengajuan->kode_akun == '536117' ? 'selected' : '' }}>536117</option>
                                            <option value="536118"
                                                {{ $pengajuan->kode_akun == '536118' ? 'selected' : '' }}>536118</option>
                                            <option value="536121"
                                                {{ $pengajuan->kode_akun == '536121' ? 'selected' : '' }}>536121</option>
                                        </optgroup>
                                        <optgroup label="Belanja Ekstrakomptabel">
                                            <option value="521252"
                                                {{ $pengajuan->kode_akun == '521252' ? 'selected' : '' }}>521252</option>
                                            <option value="521253"
                                                {{ $pengajuan->kode_akun == '521253' ? 'selected' : '' }}>521253</option>
                                            <option value="521254"
                                                {{ $pengajuan->kode_akun == '521254' ? 'selected' : '' }}>521254</option>
                                        </optgroup>
                                        <optgroup label="Belanja Persediaan">
                                            <option value="521811"
                                                {{ $pengajuan->kode_akun == '521811' ? 'selected' : '' }}>521811</option>
                                            <option value="521812"
                                                {{ $pengajuan->kode_akun == '521812' ? 'selected' : '' }}>521812</option>
                                            <option value="521813"
                                                {{ $pengajuan->kode_akun == '521813' ? 'selected' : '' }}>521813</option>
                                            <option value="521821"
                                                {{ $pengajuan->kode_akun == '521821' ? 'selected' : '' }}>521821</option>
                                            <option value="521822"
                                                {{ $pengajuan->kode_akun == '521822' ? 'selected' : '' }}>521822</option>
                                            <option value="521831"
                                                {{ $pengajuan->kode_akun == '521831' ? 'selected' : '' }}>521831</option>
                                            <option value="521832"
                                                {{ $pengajuan->kode_akun == '521832' ? 'selected' : '' }}>521832</option>
                                        </optgroup>
                                        <optgroup label="Belanja Persediaan Pemeliharaan">
                                            <option value="523112"
                                                {{ $pengajuan->kode_akun == '523112' ? 'selected' : '' }}>523112</option>
                                            <option value="523123"
                                                {{ $pengajuan->kode_akun == '523123' ? 'selected' : '' }}>523123</option>
                                            <option value="523134"
                                                {{ $pengajuan->kode_akun == '523134' ? 'selected' : '' }}>523134</option>
                                            <option value="523135"
                                                {{ $pengajuan->kode_akun == '523135' ? 'selected' : '' }}>523135</option>
                                            <option value="523136"
                                                {{ $pengajuan->kode_akun == '523136' ? 'selected' : '' }}>523136</option>
                                            <option value="523191"
                                                {{ $pengajuan->kode_akun == '523191' ? 'selected' : '' }}>523191</option>
                                        </optgroup>
                                        <optgroup label="Belanja Pemeliharaan">
                                            <option value=""
                                                {{ $pengajuan->kode_akun == '' ? 'selected' : '' }}> </option>
                                            <option value="523111"
                                                {{ $pengajuan->kode_akun == '523111' ? 'selected' : '' }}>523111</option>
                                            <option value="523119"
                                                {{ $pengajuan->kode_akun == '523119' ? 'selected' : '' }}>523119</option>
                                            <option value="523121"
                                                {{ $pengajuan->kode_akun == '523121' ? 'selected' : '' }}>523121</option>
                                            <option value="523129"
                                                {{ $pengajuan->kode_akun == '523129' ? 'selected' : '' }}>523129</option>
                                            <option value="523132"
                                                {{ $pengajuan->kode_akun == '523132' ? 'selected' : '' }}>523132</option>
                                            <option value="523133"
                                                {{ $pengajuan->kode_akun == '523133' ? 'selected' : '' }}>523133</option>
                                            <option value="523199"
                                                {{ $pengajuan->kode_akun == '523199' ? 'selected' : '' }}>523199</option>
                                        </optgroup>
                                        <optgroup label="Belanja Penambahan Nilai Peralatan dan Mesin">
                                            <option value="532121"
                                                {{ $pengajuan->kode_akun == '532121' ? 'selected' : '' }}>532121</option>
                                        </optgroup>
                                    </select>
                                    <div class="invalid-feedback">Akun harus dipilih untuk pengajuan tahun +1.</div>
                                    <small class="form-text text-muted">Pilih akun untuk pengajuan tipe Usulan</small>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Status Review:</label>
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="status_terima" name="status" value="Terima"
                                            class="custom-control-input" checked>
                                        <label class="custom-control-label" for="status_terima">
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Terima dan
                                                Ajukan ke Koordinator</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="status_tolak" name="status" value="Ditolak"
                                            class="custom-control-input">
                                        <label class="custom-control-label" for="status_tolak">
                                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Tolak
                                                Pengajuan</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="alasan-penolakan-container" class="form-group mt-3" style="display:none;">
                                    <label for="alasan_penolakan" class="font-weight-bold">Alasan Penolakan:</label>
                                    <textarea id="alasan_penolakan" name="alasan_penolakan" class="form-control" rows="4"
                                        placeholder="Berikan alasan penolakan pengajuan ini..."></textarea>
                                    <div class="invalid-feedback">Mohon berikan alasan penolakan.</div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog"
                    aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
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
                                        <div class="pdf-container"
                                            style="height: 600px; border: 1px solid #ddd; position: relative;">
                                            <div class="text-center p-5" id="pdf-loading">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <p class="mt-2">Memuat dokumen...</p>
                                            </div>
                                            <iframe id="pdf-preview" src=""
                                                style="width: 100%; height: 100%; display: none;"></iframe>
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
                                                        <label for="passphrase-input-modal"><i
                                                                class="fas fa-key mr-1"></i> Passphrase:</label>
                                                        <input type="password" id="passphrase-input-modal"
                                                            class="form-control" placeholder="Masukkan passphrase">
                                                        <small class="form-text text-muted">Passphrase diperlukan untuk
                                                            menandatangani dokumen secara elektronik.</small>
                                                    </div>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Pastikan Anda
                                                        telah memeriksa dokumen dengan teliti sebelum menandatanganinya.
                                                    </div>
                                                    <div class="verification-details mt-3">
                                                        <h6 class="font-weight-bold">Detail Dokumen:</h6>
                                                        <div class="row no-gutters">
                                                            <div class="col-5">Nomor Pengajuan</div>
                                                            <div class="col-7"><span class="font-weight-bold"
                                                                    id="detail-nomor-pengajuan"></span></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-5">Bagian Pengusul</div>
                                                            <div class="col-7"><span class="font-weight-bold"
                                                                    id="detail-bagian-pengusul"></span></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-5">Tanggal</div>
                                                            <div class="col-7"><span class="font-weight-bold"
                                                                    id="detail-tanggal"></span></div>
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
                <div class="card-footer text-right">
                    <a href="{{ route('pelaksana.index') }}" class="btn btn-secondary">Kembali</a>

                    @if (in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana', 'Ditolak oleh Koordinator']))
                        <button type="button" class="btn btn-primary" id="simpan-review-button"
                            data-id="{{ $pengajuan->id }}">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    @endif

                    @php
                        $showTorButton = true;
                        if (
                            $pengajuan->jenis_formulir === 'Pengajuan Reguler' &&
                            $pengajuan->tipe_pengajuan === 'revisi'
                        ) {
                            $showTorButton = false;
                        }
                    @endphp

                    @if ($showTorButton)
                        <button type="button" class="btn btn-info" id="download-tor-button"
                            data-id="{{ $pengajuan->id }}">
                            <i class="fas fa-download mr-1"></i>Download TOR
                        </button>
                    @endif
                    <button type="button" class="btn btn-info" id="download-lampiran-button"
                        data-id="{{ $pengajuan->id }}">
                        <i class="fas fa-download mr-1"></i>Download Lampiran
                    </button>

                    @if ($pengajuan->dokumen_pendukung)
                        <button type="button" class="btn btn-info" id="download-dokumen-pendukung-button"
                            data-id="{{ $pengajuan->id }}">
                            <i class="fas fa-file-download mr-1"></i>Download Dokumen Pendukung
                        </button>
                    @endif

                    @if (!empty($pengajuan->dokumen_rekomendasi_bmn))
                        <a href="{{ route('koordinator.download_rekomendasi', $pengajuan->id) }}" target="_blank" class="btn btn-success" title="Download Surat Rekomendasi yang dibuat oleh Koordinator">
                            <i class="fas fa-file-check mr-1"></i> Download Surat Rekomendasi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Pass all data from server to JavaScript
            const pengenalOptions = @json($pengenalOptions ?? []);

            const jenisFormulir = "{{ $pengajuan->jenis_formulir }}";
            const isReguler = jenisFormulir === 'Pengajuan Reguler';

            // --- START: MODIFICATION ---
            const pengajuanData = {
                id: {{ $pengajuan->id }},
                tipe_pengajuan: "{{ $pengajuan->tipe_pengajuan }}",
                jenis_formulir: "{{ $pengajuan->jenis_formulir }}",
                status_pengajuan: "{{ $pengajuan->status_pengajuan }}",
                berita_acara_operator_signed_date: "{{ $pengajuan->berita_acara_operator_signed_date }}",
                berita_acara_pelaksana_signed_date: "{{ $pengajuan->berita_acara_pelaksana_signed_date }}"
            };
            // --- END: MODIFICATION ---


            // ==========================================
            // Utility Functions
            // ==========================================

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

            // Show toast notification helper
            function showToast(icon, title) {
                Toast.fire({
                    icon,
                    title
                });
            }

            function validatePelaksanaForm() {
                const status = $('input[name="status"]:checked').val();
                const alasanPenolakan = $('#alasan_penolakan').val().trim();
                const alasanValid = (status !== 'Ditolak') || (status === 'Ditolak' && alasanPenolakan !== '');

                let dropdownValid = true;
                if (status === 'Terima') {
                    if (tipePengajuan === 'revisi') {
                        dropdownValid = !!$('#kode_pengenal').val();
                    } else if (tipePengajuan === 'usulan') {
                        dropdownValid = !!$('#akun_dropdown').val();
                    }
                }

                let imageValid = true;
                if (status === 'Terima' && tipePengajuan === 'revisi') {
                    const totalItems = $('.item-image-container').length;
                    let itemsWithImages = 0;
                    $('.item-image-container').each(function() {
                        const hasExistingImage = $(this).find('img.img-thumbnail').length > 0;
                        const hasNewImage = $(this).find('.item-image-input')[0]?.files?.length > 0;
                        if (hasExistingImage || hasNewImage) {
                            itemsWithImages++;
                        }
                    });
                    imageValid = (itemsWithImages === totalItems);
                }

                // --- START: MODIFICATION ---
                let beritaAcaraIsRequired = true;
                if (pengajuanData.jenis_formulir === 'Pengajuan Reguler' && pengajuanData.tipe_pengajuan ===
                    'revisi') {
                    beritaAcaraIsRequired = false;
                }

                let beritaAcaraValid = true;
                if (status === 'Terima' && beritaAcaraIsRequired && !pengajuanData
                    .berita_acara_pelaksana_signed_date) {
                    beritaAcaraValid = false;
                }
                // --- END: MODIFICATION ---


                // --- MODIFICATION: Added beritaAcaraValid to condition ---
                if (alasanValid && dropdownValid && imageValid && beritaAcaraValid) {
                    $('#simpan-review-button').prop('disabled', false)
                        .removeClass('btn-secondary').addClass('btn-primary')
                        .html('<i class="fas fa-save mr-1"></i> Simpan');
                    return true;
                } else {
                    $('#simpan-review-button').prop('disabled', true)
                        .removeClass('btn-primary').addClass('btn-secondary')
                        .html('<i class="fas fa-lock mr-1"></i> Simpan');

                    let tooltipMsg = "Untuk menyimpan review, pastikan: ";
                    let missingItems = [];

                    if (status === 'Terima') {
                        // --- START: MODIFICATION ---
                        if (beritaAcaraIsRequired && !beritaAcaraValid) {
                            missingItems.push("Berita Acara telah ditandatangani");
                        }
                        // --- END: MODIFICATION ---

                        if (!dropdownValid) {
                            if (tipePengajuan === 'revisi') {
                                missingItems.push("Kode Pengenal sudah dipilih");
                            } else {
                                missingItems.push("Akun sudah dipilih");
                            }
                        }
                        if (!imageValid && tipePengajuan === 'revisi') {
                            missingItems.push("Semua item memiliki gambar");
                        }
                    }

                    if (status === 'Ditolak' && alasanPenolakan === '') {
                        missingItems.push("Alasan penolakan telah diisi");
                    }

                    tooltipMsg += missingItems.join(", ");

                    $('#simpan-review-button').attr('data-toggle', 'tooltip')
                        .attr('data-placement', 'top')
                        .attr('title', tooltipMsg);

                    $('[data-toggle="tooltip"]').tooltip();

                    return false;
                }
            }

            function updatePelaksanaReviewStatus() {
                const status = $('input[name="status"]:checked').val();
                const alasanPenolakan = $('#alasan_penolakan').val().trim();

                // --- START: MODIFICATION ---
                let beritaAcaraIsRequired = true;
                if (pengajuanData.jenis_formulir === 'Pengajuan Reguler' && pengajuanData.tipe_pengajuan ===
                    'revisi') {
                    beritaAcaraIsRequired = false;
                }

                if (beritaAcaraIsRequired) {
                    let baStatusHtml =
                        '<i class="fas fa-times-circle text-danger"></i> Berita Acara belum ditandatangani oleh Pelaksana';
                    if (pengajuanData.berita_acara_pelaksana_signed_date) {
                        baStatusHtml =
                            '<i class="fas fa-check-circle text-success"></i> Berita Acara telah ditandatangani oleh Pelaksana';
                    } else if (status === 'Ditolak') {
                        baStatusHtml =
                            '<i class="fas fa-minus-circle text-muted"></i> Tanda tangan Berita Acara tidak diperlukan (ditolak)';
                    }
                    $('#berita-acara-review-status').html(baStatusHtml).show();
                } else {
                    $('#berita-acara-review-status').hide();
                }
                // --- END: MODIFICATION ---


                let mataAnggaranStatus =
                    '<i class="fas fa-times-circle text-danger"></i> Mata Anggaran belum dipilih';
                if (status === 'Terima') {
                    if (tipePengajuan === 'revisi') {
                        const kodePengenal = $('#kode_pengenal').val();
                        if (kodePengenal) {
                            mataAnggaranStatus =
                                '<i class="fas fa-check-circle text-success"></i> Kode Pengenal telah dipilih';
                        }
                    } else if (tipePengajuan === 'usulan') {
                        const kodeAkun = $('#akun_dropdown').val();
                        if (kodeAkun) {
                            mataAnggaranStatus =
                                '<i class="fas fa-check-circle text-success"></i> Akun telah dipilih';
                        }
                    }
                } else if (status === 'Ditolak') {
                    mataAnggaranStatus =
                        '<i class="fas fa-minus-circle text-muted"></i> Mata Anggaran tidak diperlukan (ditolak)';
                }
                $('#mata-anggaran-status').html(mataAnggaranStatus);

                if (tipePengajuan === 'revisi') {
                    let gambarStatus = '<i class="fas fa-times-circle text-danger"></i> Gambar item belum lengkap';
                    if (status === 'Terima') {
                        const totalItems = $('.item-image-container').length;
                        let itemsWithImages = 0;
                        $('.item-image-container').each(function() {
                            const hasExistingImage = $(this).find('img.img-thumbnail').length > 0;
                            const hasNewImage = $(this).find('.item-image-input')[0]?.files?.length > 0;
                            if (hasExistingImage || hasNewImage) {
                                itemsWithImages++;
                            }
                        });
                        if (itemsWithImages === totalItems && totalItems > 0) {
                            gambarStatus =
                                '<i class="fas fa-check-circle text-success"></i> Semua item memiliki gambar';
                        } else {
                            gambarStatus =
                                `<i class="fas fa-times-circle text-danger"></i> Gambar item belum lengkap (${itemsWithImages}/${totalItems})`;
                        }
                    } else if (status === 'Ditolak') {
                        gambarStatus =
                            '<i class="fas fa-minus-circle text-muted"></i> Gambar tidak diperlukan (ditolak)';
                    }
                    if ($('#gambar-status').length === 0) {
                        $('#mata-anggaran-status').after('<div class="mb-1" id="gambar-status">' + gambarStatus +
                            '</div>');
                    } else {
                        $('#gambar-status').html(gambarStatus);
                    }
                } else {
                    $('#gambar-status').remove();
                }

                let reviewStatus = '<i class="fas fa-times-circle text-danger"></i> Review belum ditentukan';
                if (status === 'Terima') {
                    reviewStatus =
                        '<i class="fas fa-check-circle text-success"></i> Review: Terima dan Ajukan ke Koordinator';
                } else if (status === 'Ditolak') {
                    if (alasanPenolakan.trim()) {
                        reviewStatus = '<i class="fas fa-check-circle text-success"></i> Review: Tolak Pengajuan';
                    } else {
                        reviewStatus =
                            '<i class="fas fa-times-circle text-danger"></i> Review: Alasan penolakan belum diisi';
                    }
                }
                $('#review-status').html(reviewStatus);

                const isValid = validatePelaksanaForm();
                if (isValid) {
                    const statusText = status === 'Terima' ? 'Terima dan Ajukan ke Koordinator' : 'Tolak Pengajuan';
                    $('#status-info').removeClass('alert-info').addClass('alert-success');
                    $('#status-info i').removeClass('fa-info-circle').addClass('fa-check-circle');
                    $('#status-message').text(`Review siap untuk disimpan (${statusText})`);
                } else {
                    $('#status-info').removeClass('alert-success').addClass('alert-info');
                    $('#status-info i').removeClass('fa-check-circle').addClass('fa-info-circle');
                    $('#status-message').text('Lengkapi semua persyaratan sebelum menyimpan review');
                }
            }

            // Format currency to Rupiah
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

                return 'Rp ' + rupiah + (split[1] ? ',' + split[1] : '');
            }

            // Remove currency format
            function removeComma(value) {
                return value.toString().replace(/[^0-9.]/g, '');
            }

            // ==========================================
            // Status Handling
            // ==========================================

            // Update status info based on current status
            function updateStatusInfo(status) {
                let icon = 'info-circle';
                let alertClass = 'info';
                let message = 'Silakan review pengajuan sebelum melakukan tindakan.';

                if (status === 'Diajukan ke Unit Pelaksana') {
                    icon = 'paper-plane';
                    alertClass = 'primary';
                    message = 'Pengajuan menunggu review dan persetujuan Anda.';
                } else if (status === 'Diajukan ke Koordinator') {
                    icon = 'check-circle';
                    alertClass = 'success';
                    message = 'Pengajuan telah disetujui dan diajukan ke Koordinator.';
                } else if (status.includes('Ditolak')) {
                    icon = 'times-circle';
                    alertClass = 'danger';
                    message = 'Pengajuan ditolak. Silakan review kembali.';
                }

                $('#status-info').removeClass().addClass(`alert alert-${alertClass} mb-3`);
                $('#status-info i').removeClass().addClass(`fas fa-${icon} mr-2 fa-lg`);
                $('#status-message').text(message);
            }

            // Update status info when page loads
            updateStatusInfo($('#status-pengajuan .badge').text());

            // ==========================================
            // Form Initialization and Data Loading
            // ==========================================

            // Get tipe pengajuan for use throughout the script
            const tipePengajuan = $('#tipe-pengajuan').text().toLowerCase().trim();

            function initializeFormByType() {
                console.log('Initializing form for type:', tipePengajuan);

                if (tipePengajuan === 'revisi') {
                    // For Revisi: Show kode pengenal section, hide akun section
                    $('#kode-pengenal-container').show();
                    $('#akun-dropdown-section').hide();

                    // Initialize kode pengenal options
                    initializeKodePengenal();

                } else if (tipePengajuan === 'usulan') {
                    // For Usulan: Hide kode pengenal section, show akun section
                    $('#kode-pengenal-container').hide();
                    $('#akun-dropdown-section').show();
                }
            }

            // Initialize kode pengenal dropdown from already-loaded data
            function initializeKodePengenal() {
                const kodePengenalDropdown = $('#kode_pengenal');
                kodePengenalDropdown.empty().append('<option value="">-- Pilih Kode Pengenal --</option>');

                if (pengenalOptions && pengenalOptions.length > 0) {
                    $.each(pengenalOptions, function(index, item) {
                        const option = `<option value="${item}">${item}</option>`;
                        kodePengenalDropdown.append(option);
                    });

                    kodePengenalDropdown.prop('disabled', false);

                    // Set existing kode pengenal if available
                    const existingKodePengenal = "{{ $pengajuan->kode_pengenal }}";
                    if (existingKodePengenal) {
                        console.log('Setting existing kode pengenal:', existingKodePengenal);
                        kodePengenalDropdown.val(existingKodePengenal);
                    }
                } else {
                    kodePengenalDropdown
                        .append('<option value="" disabled>Tidak ada kode pengenal tersedia</option>')
                        .prop('disabled', true);

                    showToast('warning', 'Tidak ada kode pengenal yang tersedia untuk pengajuan ini');
                }
            }

            // ==========================================
            // Magic Link Implementation untuk Pelaksana
            // ==========================================

            // Function untuk menampilkan magic link verification section
            function showMagicLinkVerificationPelaksana(data) {
                const $verificationButtons = $('#verification-buttons-pelaksana');
                $verificationButtons.empty();

                // Cek apakah perlu verifikasi berita acara untuk pelaksana
                let needsVerification = false;
                if (data.tipe_pengajuan === 'usulan' &&
                    data.berita_acara_operator_signed_date &&
                    !data.berita_acara_pelaksana_signed_date && ['Diajukan ke Unit Pelaksana'].includes(data
                        .status_pengajuan)) {
                    needsVerification = true;
                }

                if (needsVerification) {
                    const magicLinkSection = `
                <div class="magic-link-verification-pelaksana mb-3">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-magic mr-2"></i>Verifikasi Digital Pelaksana</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                <i class="fas fa-info-circle text-info mr-2"></i>
                                Kirim link verifikasi digital ke Eselon III Unit Pelaksana untuk menandatangani Berita Acara.
                            </p>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="alert alert-info mb-2">
                                        <small>
                                            <strong>Dokumen yang akan ditandatangani:</strong><br>
                                            • Berita Acara (Tanda Tangan Pelaksana)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-warning btn-block" id="send-magic-link-pelaksana-btn" data-id="${data.id}">
                                        <i class="fas fa-paper-plane mr-2"></i>Kirim pengajuan verifikasi
                                    </button>
                                </div>
                            </div>

                            <div id="magic-link-status-pelaksana" style="display: none;" class="mt-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span id="magic-link-message-pelaksana"> telah dikirim. Menunggu verifikasi...</span>
                                    <br>
                                    <small id="magic-link-timer-pelaksana" class="font-weight-bold"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    $verificationButtons.append(magicLinkSection);
                }
            }

            // Function untuk countdown timer
            function showMagicLinkStatusPelaksana(expiresAt) {
                const $status = $('#magic-link-status-pelaksana');
                const $message = $('#magic-link-message-pelaksana');
                const $timer = $('#magic-link-timer-pelaksana');

                $status.show();
                const expiryTime = new Date(expiresAt).getTime();

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
                            '<i class="fas fa-times-circle mr-2"></i>Link Verifikasi telah kedaluwarsa');
                        $timer.text('');
                        $('#send-magic-link-pelaksana-btn').show().prop('disabled', false)
                            .html('<i class="fas fa-paper-plane mr-2"></i>Kirim Pengajuan Verifikasi');
                    }
                }, 1000);
            }

            // Initialize form when page loads
            initializeFormByType();

            // Initialize validation
            validatePelaksanaForm();
            updatePelaksanaReviewStatus();

            // ==========================================
            // Event Handlers
            // ==========================================

            // Validation feedback handler
            $('.form-control').on('change', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
                validatePelaksanaForm(); // Tambahkan ini
                updatePelaksanaReviewStatus(); // Tambahkan ini
            });

            // Status radio button handler (show/hide rejection reason)
            $('input[name="status"]').on('change', function() {
                if ($(this).val() === 'Ditolak') {
                    $('#alasan-penolakan-container').slideDown();
                } else {
                    $('#alasan-penolakan-container').slideUp();
                }
                validatePelaksanaForm(); // Tambahkan ini
                updatePelaksanaReviewStatus(); // Tambahkan ini
            });

            $('#kode_pengenal, #akun_dropdown, #alasan_penolakan').on('change input', function() {
                validatePelaksanaForm();
                updatePelaksanaReviewStatus();
            });

            // Custom file input handler
            $('.custom-file-input').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);

                // File size validation
                if (this.files[0] && this.files[0].size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB'
                    });
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih gambar');
                    return;
                }

                // File type validation
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (this.files[0] && !validTypes.includes(this.files[0].type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Format file harus JPG, PNG, atau GIF'
                    });
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih gambar');
                    return;
                }

                // Display image preview
                if (this.files[0]) {
                    const reader = new FileReader();
                    const itemId = $(this).data('item-id');
                    const container = $(this).closest('.item-image-container');

                    reader.onload = function(e) {
                        // Remove previous preview if exists
                        container.find('img.img-thumbnail').remove();
                        container.find('.remove-image').parent().remove();

                        // Add new preview
                        $(`<div class="mb-2 position-relative">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">
                    <button type="button" class="btn btn-sm btn-danger remove-image position-absolute"
                            style="top: 0; right: 0;" data-item-id="${itemId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`).insertBefore(container.find('.custom-file'));

                        // Add remove button event handler
                        container.find('.remove-image').on('click', function() {
                            $(this).parent().remove();
                            container.find(`#image_${itemId}`).val('');
                            container.find('.custom-file-label').html('Pilih gambar');
                        });
                    };

                    reader.readAsDataURL(this.files[0]);
                }

                validatePelaksanaForm();
                updatePelaksanaReviewStatus();
            });

            // ==========================================
            // Form Submission
            // ==========================================
            // Event handler untuk tombol Magic Link Pelaksana
            $(document).on('click', '#send-magic-link-pelaksana-btn', function() {
                const id = $(this).data('id');
                const btn = $(this);

                Swal.fire({
                    title: 'Konfirmasi Pengiriman Pengajuan Verifikasi',
                    text: 'Link verifikasi akan dikirim ke Eselon III Unit Pelaksana melalui WhatsApp.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ffc107'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                            url: `/pelaksana_nonsbsk/${id}/send-magic-link-verification`,
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
                                        showMagicLinkStatusPelaksana(response
                                            .expires_at);
                                        btn.hide();
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
            // Save review button handler
            $('#simpan-review-button').on('click', function() {
                if (!validatePelaksanaForm()) {
                    return;
                }

                const id = $(this).data('id');
                const status = $('input[name="status"]:checked').val();
                const alasanPenolakan = $('#alasan_penolakan').val();

                // Validation
                let isValid = true;

                // Rejection reason validation
                if (status === 'Ditolak' && !alasanPenolakan.trim()) {
                    $('#alasan_penolakan').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#alasan_penolakan').removeClass('is-invalid');
                }

                // Validate form fields based on pengajuan type
                if (status === 'Terima') {
                    if (tipePengajuan === 'revisi') {
                        const kodePengenal = $('#kode_pengenal').val();
                        if (!kodePengenal) {
                            $('#kode_pengenal').addClass('is-invalid');
                            isValid = false;
                        } else {
                            $('#kode_pengenal').removeClass('is-invalid');
                        }
                    } else if (tipePengajuan === 'usulan') {
                        const kodeAkun = $('#akun_dropdown').val();

                        if (!kodeAkun) {
                            $('#akun_dropdown').addClass('is-invalid');
                            isValid = false;
                        } else {
                            $('#akun_dropdown').removeClass('is-invalid');
                        }
                    }
                }

                // If validation fails, show error and stop
                if (!isValid) {
                    showToast('error', 'Mohon lengkapi semua field yang diperlukan');
                    return;
                }

                let confirmMessage = '';
                if (status === 'Ditolak') {
                    confirmMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?.';
                } else {
                    confirmMessage = 'Apakah Anda yakin ingin meneruskan pengajuan ini ke Koordinator?';
                }

                // Confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmMessage,
                    icon: status === 'Ditolak' ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: status === 'Ditolak' ? '#d33' : '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading spinner
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create FormData for sending files
                        const formData = new FormData();
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                        formData.append('status', status);
                        formData.append('alasan_penolakan', alasanPenolakan || '');

                        // Add data based on type
                        if (tipePengajuan === 'revisi') {
                            formData.append('kode_pengenal', $('#kode_pengenal').val());
                        } else if (tipePengajuan === 'usulan') {
                            formData.append('kode_akun', $('#akun_dropdown').val());
                        }

                        // PERBAIKAN: Process images hanya untuk revisi
                        if (status === 'Terima' && tipePengajuan === 'revisi') {
                            console.log('Processing images for revisi...');

                            $('.item-image-input').each(function() {
                                const itemId = $(this).data('item-id');
                                const fileInput = document.getElementById(
                                    `image_${itemId}`);

                                if (fileInput && fileInput.files[0]) {
                                    formData.append(`image[${itemId}]`, fileInput.files[0]);
                                    console.log(`Added image for item ${itemId}:`, fileInput
                                        .files[0].name);
                                }
                            });

                            // Log FormData contents for debugging
                            console.log('FormData contents:');
                            for (let pair of formData.entries()) {
                                console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1]
                                    .name : pair[1]));
                            }
                        }

                        // Submit form data to server
                        $.ajax({
                            url: `{{ route('pelaksana.updateReview', $pengajuan->id) }}`,
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
                                        // Redirect to index page
                                        window.location.href =
                                            "{{ route('pelaksana.index') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.close();
                                console.error('XHR:', xhr);
                                console.error('Status:', status);
                                console.error('Error:', error);

                                let errorMessage =
                                    'Terjadi kesalahan saat memproses permintaan';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
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

            // ==========================================
            // Document Handling
            // ==========================================

            // Verify Berita Acara button handler
            $('#verify-berita-acara-button').on('click', function() {
                // Skip berita acara for reguler revisi
                if (isReguler && tipePengajuan === 'revisi') {
                    console.log('Berita Acara verification not available for Reguler Revisi type');
                    return;
                }

                const id = $(this).data('id');
                const pengajuanId = {{ $pengajuan->id }};

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

                $('#pdf-preview').off('load error');

                const previewUrl =
                    `${window.location.origin}/pelaksana_nonsbsk/${pengajuanId}/preview-berita-acara-operator-signed`;

                // FIXED: Event handler baru tanpa fallback
                $('#pdf-preview').on('load', function() {
                    $('#pdf-loading').hide();
                    $('#pdf-preview').show();
                }).on('error', function() {
                    // FIXED: Hanya tampilkan error, JANGAN panggil URL lain
                    $('#pdf-loading').html(`
                <div class="text-danger text-center">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h5>Gagal Memuat Dokumen</h5>
                    <p>Berita acara yang sudah ditandatangani operator tidak dapat diambil.</p>
                    <p class="small text-muted">Pastikan operator telah menandatangani berita acara terlebih dahulu.</p>
                </div>
            `);
                    $('#pdf-preview').hide();
                    // FIXED: JANGAN set src lagi di sini!
                });

                // Set src hanya satu kali
                $('#pdf-preview').attr('src', previewUrl);

                // Clear previous passphrase
                $('#passphrase-input-modal').val('');
            });


            // Document signing confirmation button handler
            $('#confirm-verification-button').on('click', function() {
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

                // Send verification request
                fetch(`${window.location.origin}/pelaksana_nonsbsk/${id}/verifikasi-berita-acara`, {
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
                                    'Terjadi kesalahan saat verifikasi');
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
                                // Reload page to show updates
                                window.location.href = "{{ route('pelaksana.index') }}";
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

            // PDF loading error handler
            // $(document).on('error', '#pdf-preview', function() {
            //     $('#pdf-loading').html(`
        //         <div class="text-danger">
        //             <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
        //             <p>Gagal memuat dokumen. Silakan coba lagi.</p>
        //             <button class="btn btn-outline-primary btn-sm mt-2" id="retry-load-pdf">
        //                 <i class="fas fa-sync mr-1"></i> Coba Lagi
        //             </button>
        //         </div>
        //     `);
            // });

            // Retry loading PDF
            // $(document).on('click', '#retry-load-pdf', function() {
            //     const id = $('#verify-berita-acara-button').data('id');
            //     const previewUrl = `${window.location.origin}/pengajuanrkbmnbagiannonsbsk/${id}/preview-berita-acara`;
            //
            //     $('#pdf-loading').html(`
        //         <div class="spinner-border text-primary" role="status">
        //             <span class="sr-only">Loading...</span>
        //         </div>
        //         <p class="mt-2">Memuat ulang dokumen...</p>
        //     `);
            //
            //     $('#pdf-preview').attr('src', previewUrl);
            // });

            // Enter key support for passphrase input
            $(document).on('keyup', '#passphrase-input-modal', function(e) {
                if (e.key === 'Enter') {
                    $('#confirm-verification-button').click();
                }
            });

            // Clean up when modal is hidden
            $('#pdfPreviewModal').on('hidden.bs.modal', function() {
                $('#pdf-preview').attr('src', '');
            });

            // Visual feedback when entering passphrase
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

            // ==========================================
            // Download Buttons
            // ==========================================

            // Download Berita Acara button handler
            $(document).on('click', '#download-berita-acara-signed-button', function() {
                const id = $(this).data('id');
                const downloadUrl = `${window.location.origin}/pelaksana_nonsbsk/${id}/download-berita-acara-signed`;

                // Show loading spinner
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyiapkan dokumen Berita Acara tertandatangani',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Open download URL in new tab
                window.open(downloadUrl, '_blank');

                // Close loading spinner after 2 seconds
                setTimeout(() => {
                    Swal.close();
                }, 2000);
            });

            // Download TOR button handler
            $(document).on('click', '#download-tor-button', function() {                // Skip TOR for reguler revisi
                if (isReguler && tipePengajuan === 'revisi') {
                    console.log('TOR download not available for Reguler Revisi type');
                    return;
                }

                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pelaksana_nonsbsk/${id}/download-tor`;

                // Show loading spinner
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyiapkan dokumen TOR',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Open download URL in new tab
                window.open(downloadUrl, '_blank');

                // Close loading spinner after 2 seconds
                setTimeout(() => {
                    Swal.close();
                }, 2000);
            });

            // Download Lampiran button handler
            $(document).on('click', '#download-lampiran-button', function() {
                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pelaksana_nonsbsk/${id}/download-lampiran`;

                // Show loading spinner
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyiapkan dokumen lampiran',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Open download URL in new tab
                window.open(downloadUrl, '_blank');

                // Close loading spinner after 2 seconds
                setTimeout(() => {
                    Swal.close();
                }, 2000);
            });

            $(document).on('click', '#download-dokumen-pendukung-button', function() {
                const id = $(this).data('id');
                const downloadUrl =
                    `${window.location.origin}/pelaksana_nonsbsk/${id}/download-dokumen`;

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

            showMagicLinkVerificationPelaksana(pengajuanData);

        });
    </script>
@endsection
