<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan RKBMN - Lampiran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header img {
            height: 50px;
            width: auto;
        }
        .title {
            font-weight: bold;
            font-style: italic;
            font-size: 12pt;
            margin: 8px 0;
        }
        .subtitle {
            font-weight: bold;
            font-size: 10pt;
            margin: 4px 0 12px 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #444;
            padding: 4px;
            font-size: 8pt;
        }
        .detail-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left;
            padding: 3px;
            border: 1px solid #444;
            font-size: 9pt;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }
        .center {
            text-align: center;
        }
        .signature {
            text-align: right;
            margin-top: 20px;
            margin-right: 30px;
            page-break-inside: avoid;
        }
        .signature-content {
            display: inline-block;
            text-align: center;
        }
        .page-number {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 8pt;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 7pt;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
            <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI">
        @endif
        <div class="title">LAMPIRAN USULAN PENGADAAN
            @php
                $jenisPengajuan = '';
                if(isset($data->kode_jenis_pengajuan)) {
                    if(substr($data->kode_jenis_pengajuan, 0, 2) == 'R1') {
                        $jenisPengajuan = 'TANAH DAN/ATAU BANGUNAN PERKANTORAN';
                    } elseif(substr($data->kode_jenis_pengajuan, 0, 2) == 'R2') {
                        $jenisPengajuan = 'BANGUNAN PENDIDIKAN';
                    } elseif(substr($data->kode_jenis_pengajuan, 0, 2) == 'R3') {
                        $jenisPengajuan = 'RUMAH NEGARA';
                    } elseif(substr($data->kode_jenis_pengajuan, 0, 2) == 'R4') {
                        $jenisPengajuan = 'KENDARAAN JABATAN';
                    } elseif(substr($data->kode_jenis_pengajuan, 0, 2) == 'R5') {
                        $jenisPengajuan = 'KENDARAAN OPERASIONAL';
                    }
                }
                echo $jenisPengajuan;
            @endphp
        </div>
        <div class="subtitle">
            RENCANA PENGADAAN BAGIAN {{ $uraianBagianPelaksana ?? '' }} PADA RENCANA KEBUTUHAN BARANG MILIK<br>
            NEGARA (RKBMN) TAHUN ANGGARAN {{ $tahunAnggaranPengusulan ?? date('Y') }}
        </div>
    </div>

    @php
        $pengajuanType = isset($data->kode_jenis_pengajuan) ? substr($data->kode_jenis_pengajuan, 0, 2) : '';
    @endphp

    @if($pengajuanType == 'R1' || $pengajuanType == 'R2' || $pengajuanType == 'R3')
        <!-- Tabel untuk R1, R2, R3 -->
        <table class="detail-table">
            <tr>
                <th width="15%">Tujuan</th>
                <td width="35%">{{ $data->tujuan_rencana ?? '-' }}</td>
                <th width="15%">ATR/NON ATR</th>
                <td width="35%">{{ $data->atr_nonatr ?? '-' }}</td>
            </tr>
            <tr>
                <th>Skema</th>
                <td>{{ $data->skema ?? '-' }}</td>
                <th>Kode Mata Anggaran</th>
                <td>{{ $data->kegiatan ?? '-' }}.{{ $data->output ?? '-' }}</td>
            </tr>
            <tr>
                <th>Akun Belanja</th>
                <td>{{ $data->akun_belanja ?? '-' }}</td>
                <th>Akun Neraca</th>
                <td>{{ $data->akun_neraca ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Kantor</th>
                <td>
                    @if($pengajuanType == 'R1' && isset($data->bangunanKantor))
                        {{ $data->bangunanKantor->klasifikasi_bangunan ?? '-' }}
                    @elseif($pengajuanType == 'R3' && isset($data->rumahNegara))
                        {{ $data->rumahNegara->klasifikasi_pejabat ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <th>Kode Barang</th>
                <td>{{ $data->kode_barang ?? '-' }}</td>
            </tr>
            <tr>
                <th>Uraian Barang</th>
                <td>{{ $uraianBarang ?? '-' }}</td>
                <th>Lokasi</th>
                <td>
                    @if($pengajuanType == 'R1' && isset($data->bangunanKantor))
                        {{ $data->bangunanKantor->lokasi ?? '-' }}
                    @elseif($pengajuanType == 'R3' && isset($data->rumahNegara))
                        {{ $data->rumahNegara->lokasi ?? '-' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Luas</th>
                <td>
                    @if($pengajuanType == 'R1' && isset($data->bangunanKantor))
                        {{ $data->bangunanKantor->luas_ruang_kerja + $data->bangunanKantor->luas_ruang_tamu + $data->bangunanKantor->luas_ruang_rapat + $data->bangunanKantor->luas_ruang_tunggu + $data->bangunanKantor->luas_ruang_istirahat + $data->bangunanKantor->luas_ruang_sekretaris + $data->bangunanKantor->luas_ruang_simpan + $data->bangunanKantor->luas_ruang_toilet + $data->bangunanKantor->luas_ruang_rapat_utama }} m²
                    @elseif($pengajuanType == 'R3' && isset($data->rumahNegara))
                        {{ $data->rumahNegara->luas_tanah ?? '-' }} m²
                    @else
                        -
                    @endif
                </td>
                <th>Keterangan</th>
                <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
        </table>

        @if($pengajuanType == 'R1' && isset($data->bangunanKantor))
            <div class="section-header">Detail Ruangan</div>
            <table class="detail-table" style="margin-bottom: 0;">
                <tr>
                    <th width="16%">Ruang Kerja</th>
                    <td width="12%">{{ $data->bangunanKantor->luas_ruang_kerja ?? '-' }} m²</td>
                    <th width="16%">Ruang Tamu</th>
                    <td width="12%">{{ $data->bangunanKantor->luas_ruang_tamu ?? '-' }} m²</td>
                    <th width="16%">Ruang Rapat</th>
                    <td width="12%">{{ $data->bangunanKantor->luas_ruang_rapat ?? '-' }} m²</td>
                </tr>
                <tr>
                    <th>Ruang Tunggu</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_tunggu ?? '-' }} m²</td>
                    <th>Ruang Istirahat</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_istirahat ?? '-' }} m²</td>
                    <th>Ruang Sekretaris</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_sekretaris ?? '-' }} m²</td>
                </tr>
                <tr>
                    <th>Ruang Simpan</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_simpan ?? '-' }} m²</td>
                    <th>Ruang Toilet</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_toilet ?? '-' }} m²</td>
                    <th>Ruang Rapat Utama</th>
                    <td>{{ $data->bangunanKantor->luas_ruang_rapat_utama ?? '-' }} m²</td>
                </tr>
            </table>
        @elseif($pengajuanType == 'R3' && isset($data->rumahNegara))
            <div class="section-header">Detail Ruangan Rumah Negara</div>
            <table class="detail-table">
                <tr>
                    <th width="20%">Ruang Kerja</th>
                    <td width="13%">{{ $data->rumahNegara->jumlah_ruang_kerja ?? '-' }} ruang</td>
                    <th width="20%">Ruang Duduk</th>
                    <td width="13%">{{ $data->rumahNegara->jumlah_ruang_duduk ?? '-' }} ruang</td>
                    <th width="20%">Ruang Fungsional</th>
                    <td width="14%">{{ $data->rumahNegara->jumlah_ruang_fungsional ?? '-' }} ruang</td>
                </tr>
                <tr>
                    <th>Ruang Makan</th>
                    <td>{{ $data->rumahNegara->jumlah_ruang_makan ?? '-' }} ruang</td>
                    <th>Ruang Tidur</th>
                    <td>{{ $data->rumahNegara->jumlah_ruang_tidur ?? '-' }} ruang</td>
                    <th>Kamar Mandi/WC</th>
                    <td>{{ $data->rumahNegara->jumlah_ruang_wc ?? '-' }} ruang</td>
                </tr>
                <tr>
                    <th>Dapur</th>
                    <td>{{ $data->rumahNegara->jumlah_dapur ?? '-' }} ruang</td>
                    <th>Gudang</th>
                    <td>{{ $data->rumahNegara->jumlah_gudang ?? '-' }} ruang</td>
                    <th>Garasi</th>
                    <td>{{ $data->rumahNegara->jumlah_garasi ?? '-' }} ruang</td>
                </tr>
                <tr>
                    <th>Ruang Tidur Pramuwisma</th>
                    <td>{{ $data->rumahNegara->jumlah_ruang_tidur_pramuwisma ?? '-' }} ruang</td>
                    <th>Ruang Cuci</th>
                    <td>{{ $data->rumahNegara->jumlah_ruang_cuci ?? '-' }} ruang</td>
                    <th>Kamar Mandi Pramuwisma</th>
                    <td>{{ $data->rumahNegara->jumlah_kamar_mandi_pramuwisma ?? '-' }} ruang</td>
                </tr>
            </table>
        @endif

    @elseif($pengajuanType == 'R4')
        <!-- Tabel untuk R4 -->
        <table class="detail-table">
            <tr>
                <th width="15%">Skema</th>
                <td width="35%">{{ $data->skema ?? '-' }}</td>
                <th width="15%">Mata Anggaran</th>
                <td width="35%">{{ $data->kegiatan ?? '-' }}.{{ $data->output ?? '-' }}</td>
            </tr>
            <tr>
                <th>Akun Belanja</th>
                <td>{{ $data->akun_belanja ?? '-' }}</td>
                <th>Akun Neraca</th>
                <td>{{ $data->akun_neraca ?? '-' }}</td>
            </tr>
            <tr>
                <th>Pejabat Pemakai</th>
                <td>
                    @if(isset($data->kendaraanJabatan))
                        {{ $data->kendaraanJabatan->pejabat_pemakai ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <th>Kualifikasi</th>
                <td>
                    @if(isset($data->kendaraanJabatan))
                        {{ $data->kendaraanJabatan->spesifikasi ?? '-' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Kode Barang</th>
                <td>{{ $data->kode_barang ?? '-' }}</td>
                <th>Uraian Barang</th>
                <td>{{ $uraianBarang ?? '-' }}</td>
            </tr>
            <tr>
                <th>Usulan/Jumlah Unit</th>
                <td>{{ $data->kuantitas ?? '-' }} unit</td>
                <th>Keterangan</th>
                <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
        </table>

    @elseif($pengajuanType == 'R5')
        <!-- Tabel untuk R5 -->
        <table class="detail-table">
            <tr>
                <th width="15%">Skema</th>
                <td width="35%">{{ $data->skema ?? '-' }}</td>
                <th width="15%">Mata Anggaran</th>
                <td width="35%">{{ $data->kegiatan ?? '-' }}.{{ $data->output ?? '-' }}</td>
            </tr>
            <tr>
                <th>Akun Belanja</th>
                <td>{{ $data->akun_belanja ?? '-' }}</td>
                <th>Akun Neraca</th>
                <td>{{ $data->akun_neraca ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Satker</th>
                <td>
                    @if(isset($data->kendaraanOperasional))
                        {{ $data->kendaraanOperasional->jenis_satker ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <th>Jenis Kendaraan</th>
                <td>
                    @if(isset($data->kendaraanOperasional))
                        {{ $data->kendaraanOperasional->jenis_kendaraan ?? '-' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Kode Barang</th>
                <td>{{ $data->kode_barang ?? '-' }}</td>
                <th>Uraian Barang</th>
                <td>{{ $uraianBarang ?? '-' }}</td>
            </tr>
            <tr>
                <th>Usulan/Jumlah Unit</th>
                <td>{{ $data->kuantitas ?? '-' }} unit</td>
                <th>Keterangan</th>
                <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
            @if(isset($data->kendaraanOperasional) && $data->kendaraanOperasional->jenis_kendaraan && strpos($data->kendaraanOperasional->jenis_kendaraan, 'Dialihfungs') !== false)
                <tr>
                    <th>Tipe Kendaraan Alih Fungsi</th>
                    <td colspan="3">{{ $data->kendaraanOperasional->tipe_kendaraan_jabatan_alih_fungsi ?? '-' }}</td>
                </tr>
            @endif
        </table>
    @else
        <!-- Tabel default jika tidak ada jenis pengajuan yang sesuai -->
        <table class="detail-table">
            <tr>
                <th width="15%">Skema</th>
                <td width="35%">{{ $data->skema ?? '-' }}</td>
                <th width="15%">Kode Mata Anggaran</th>
                <td width="35%">{{ $data->kegiatan ?? '-' }}.{{ $data->output ?? '-' }}</td>
            </tr>
            <tr>
                <th>Akun Belanja</th>
                <td>{{ $data->akun_belanja ?? '-' }}</td>
                <th>Akun Neraca</th>
                <td>{{ $data->akun_neraca ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kode Barang</th>
                <td>{{ $data->kode_barang ?? '-' }}</td>
                <th>Uraian Barang</th>
                <td>{{ $uraianBarang ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jumlah</th>
                <td>{{ $data->kuantitas ?? '-' }} unit</td>
                <th>Keterangan</th>
                <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
        </table>
    @endif

    <div class="signature">
        <table style="margin-left: auto; border: none; width: auto;">
            <tr>
                <td style="text-align: right; border: none; padding: 0;"><strong>Jakarta, {{ $tanggalPengajuan ?? date('j F Y') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: right; border: none; padding: 0;"><strong>Penanggung Jawab Kegiatan</strong></td>
            </tr>
            <tr>
                <td style="height: 100px; border: none;">
                    <!-- Area untuk E-Sign QR Code, space ini akan diisi dengan QR Code dan tanda tangan elektronik -->
                </td>
            </tr>
            <tr>
                <td style="text-align: right; border: none; padding: 0;"><strong>{{ $namaPenanggungJawabPelaksana ?? '.....................................' }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="page-number">
        Halaman 1 dari 1
    </div>

    <div class="footer">
        Dokumen ini merupakan lampiran pengajuan Rencana Kebutuhan Barang Milik Negara (RKBMN)
    </div>
</body>
</html>
