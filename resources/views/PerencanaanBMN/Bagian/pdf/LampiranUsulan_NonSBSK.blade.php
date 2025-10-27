<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lampiran Pengajuan Non-SBSK</title>
    <style>
        /* Style yang sama seperti sebelumnya */
        @page {
            size: A4 landscape;
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }
        .info-table th {
            background-color: #f2f2f2;
            text-align: left;
            width: 20%;
        }
        .detail-section {
            page-break-before: always; */ /* Dinonaktifkan agar konten mengalir */
            page-break-before: always; */ /* Dinonaktifkan agar konten mengalir */
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .detail-table th, .detail-table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }
        .detail-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .detail-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        .page-header {
            position: running(header);
            text-align: center;
            font-size: 8pt;
            margin-bottom: 5px;
        }
        .page-footer {
            position: running(footer);
            text-align: center;
            font-size: 8pt;
            margin-top: 5px;
        }
        @page {
            @top-center {
                content: element(header);
            }
            @bottom-center {
                content: element(footer);
            }
        }
    </style>
</head>
<body>
    <!-- Header dengan logo -->
    <div class="header">
        <div class="logo">
            @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
                <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI" style="width: 75px;">
            @endif
        </div>
        <div style="margin-left: 85px;">
            <div style="font-weight: bold; font-size: 14pt; text-align: center;">SEKRETARIAT JENDERAL</div>
            <div style="font-weight: bold; font-size: 14pt; text-align: center;">DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA</div>
            <div style="font-size: 8pt; text-align: center;">JL. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270</div>
            <div style="font-size: 8pt; text-align: center;">TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE: www.dpr.go.id</div>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="text-center">
        <div class="title">LAMPIRAN PENGAJUAN BARANG NON-SBSK</div>
        <div class="subtitle">
            RENCANA KEBUTUHAN BARANG MILIK NEGARA (RKBMN)<br>
            TAHUN ANGGARAN {{ $tahunAnggaranPengusulan }}
        </div>
    </div>

    <!-- Informasi Umum Pengajuan -->
        <table class="info-table">
        <tr>
            <th width="25%">Nomor Pengajuan</th>
            <td width="25%">{{ $pengajuan->id }}</td>
            <th width="25%">Tanggal Pengajuan</th>
            <td width="25%">{{ $tanggalPengajuan }}</td>
        </tr>
        <tr>
            <th>Tipe Pengajuan</th>
            <td>{{ ucfirst($pengajuan->tipe_pengajuan) }}</td>
            <th>Tahun Anggaran</th>
            <td>{{ $pengajuan->tahun_anggaran }}</td>
        </tr>
        <tr>
            <th>Bagian Pengusul</th>
            <td colspan="3">{{ $uraianBagianPengusul ?? '-' }}</td>
        </tr>
        <tr>
            <th>Bagian Pelaksana</th>
            <td colspan="3">{{ $uraianBagianPelaksana ?? '-' }}</td>
        </tr>
        @if($pengajuan->tipe_pengajuan == 'revisi')
            <tr>
                <th>Kode Pengenal</th>
                <td colspan="3">{{ $pengajuan->kode_pengenal ?? '-' }}</td>
            </tr>
        @endif
    </table>

    <div style="width: 35%; margin-left: 65%; text-align: center; margin-top: 30px; font-size: 10pt;">
        <p style="margin-bottom: 5px;">Jakarta, {{ $tanggalPengajuan }}</p>
        <p style="margin-top: 0; margin-bottom: 100px;">Penanggung Jawab,</p>
        <p style="margin-bottom: 0; font-weight: bold; text-decoration: underline;">{{ $namaPenanggungJawabPelaksana }}</p>
    </div>

    <!-- Detail Items Pengajuan - Dengan page-break-before -->
    <div class="detail-section">
        <!-- Header untuk halaman kedua -->
        <div class="header" style="margin-top: 0;">
            <div class="logo">
                @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
                    <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI" style="width: 75px;">
                @endif
            </div>
            <div style="margin-left: 85px;">
                <div style="font-weight: bold; font-size: 14pt; text-align: center;">SEKRETARIAT JENDERAL</div>
                <div style="font-weight: bold; font-size: 14pt; text-align: center;">DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA</div>
                <div style="font-size: 8pt; text-align: center;">JL. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270</div>
                <div style="font-size: 8pt; text-align: center;">TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE: www.dpr.go.id</div>
            </div>
        </div>

        <h3>Detail Barang/Perlengkapan yang Diajukan</h3>
        <p><strong>Nomor Pengajuan:</strong> {{ $pengajuan->id }} | <strong>Tahun Anggaran:</strong> {{ $pengajuan->tahun_anggaran }}</p>

        <table class="detail-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Barang</th>
                    <th width="25%">Deskripsi Barang</th>
                    <th width="20%">Keterangan</th>
                    <th width="10%">Kuantitas</th>
                    <th width="15%">Harga Satuan</th>
                    <th width="15%">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detailItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td>{{ $item['deskripsi_perlengkapan'] }}</td>
                        <td class="text-center">{{ $item['keterangan_barang'] }}</td>
                        <td class="text-center">{{ $item['kuantitas'] }}</td>
                        <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data barang/perlengkapan</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">Total Anggaran:</th>
                    <th class="text-right">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Keterangan (jika ada) -->
    @if($pengajuan->keterangan)
        <div style="margin-top: 20px;">
            <h3>Tujuan:</h3>
            <div style="border: 1px solid #ddd; padding: 10px; font-size: 9pt;">
                {{ $pengajuan->keterangan }}
            </div>
        </div>
    @endif

    <!-- Page Number -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
