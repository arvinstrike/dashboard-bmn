<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Tanggung Jawab Mutlak - Lampiran Data Non-PSP</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-size: 11pt;
        }

        /* Header Layout - Kop Surat */
        .header {
            position: relative;
            text-align: center;
            border-bottom: 0.7px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            position: absolute;
            left: 5px;
            top: 0;
            width: 100px;
        }

        .header-text {
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
            padding: 0 130px;
        }

        .header-text-main {
            margin-top: 5px;
            font-size: 16pt;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        .header-text-address {
            font-size: 12pt;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0 10px 0;
            text-decoration: underline;
        }

        .document-number {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        /* Identity Section */
        .identity-section {
            margin: 15px 0;
            line-height: 1.5;
        }

        .identity-section p {
            margin: 3px 0;
        }

        .identity-table {
            margin-left: 0;
            margin-top: 10px;
        }

        .identity-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .identity-table .label {
            width: 80px;
        }

        .identity-table .colon {
            width: 15px;
        }

        /* Statement Text */
        .statement-text {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.4;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 7.5pt;
        }

        .data-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            line-height: 1.2;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
            line-height: 1.2;
        }

        .data-table .col-no {
            width: 3%;
            text-align: center;
        }

        .data-table .col-kode {
            width: 9%;
            text-align: center;
        }

        .data-table .col-nup {
            width: 5%;
            text-align: center;
        }

        .data-table .col-nama {
            width: 22%;
        }

        .data-table .col-qty {
            width: 6%;
            text-align: center;
        }

        .data-table .col-tanggal {
            width: 9%;
            text-align: center;
        }

        .data-table .col-nilai {
            width: 18%;
            text-align: right;
            padding-right: 4px;
        }

        .data-table .col-jumlah {
            width: 23%;
            text-align: right;
            padding-right: 4px;
        }

        /* Total Row */
        .total-row {
            font-weight: bold;
        }

        /* Closing Statement */
        .closing-statement {
            text-align: justify;
            margin: 20px 0;
            line-height: 1.4;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            text-align: right;
        }

        .signature-location {
            margin-bottom: 5px;
        }

        .signature-title {
            margin-bottom: 5px;
        }

        .materai-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 15px 20px;
            margin: 20px 0;
            text-align: center;
            font-size: 9pt;
        }

        .signature-name {
            margin-top: 10px;
            text-decoration: underline;
            font-weight: bold;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Part Info (for multi-part documents) */
        .part-info {
            text-align: center;
            font-size: 9pt;
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header/Kop Surat -->
    <div class="header">
        <div class="logo">
            @if(file_exists(public_path('assets/pic/logo_setjen_dpr_color.png')))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/pic/logo_setjen_dpr_color.png'))) }}" alt="Logo Setjen DPR RI" style="width: 100px;">
            @endif
        </div>
        <div class="header-text">
            <div class="header-text-main">
                SEKRETARIAT JENDERAL<br>
                DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA
            </div>
            <div class="header-text-address">
                JLN. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270<br>
                TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE : www.dpr.go.id
            </div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK
    </div>
    <div class="document-number">
        Nomor : {{ $nomor_surat }}{{ $part_suffix ?? '' }}
    </div>

    <!-- Identity Section -->
    <div class="identity-section">
        <p>Yang bertanda tangan di bawah ini:</p>

        <table class="identity-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $pejabat_data['nama'] ?? 'Indra Iskandar' }}</td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td class="colon">:</td>
                <td>{{ $pejabat_data['nip'] ?? '196611141997031001' }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td>{{ $pejabat_data['jabatan'] ?? 'Sekretaris Jenderal' }}</td>
            </tr>
        </table>
    </div>

    <!-- Statement Text -->
    <div class="statement-text">
        Dengan ini menyatakan bahwa Barang Milik Negara dengan nilai perolehan di bawah Rp100.000.000,00
        (seratus juta rupiah) per unit/satuan dengan perincian data:
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no" rowspan="2">NO</th>
                <th class="col-kode" rowspan="2">KODE<br>BARANG</th>
                <th colspan="2">NUP</th>
                <th class="col-nama" rowspan="2">NAMA BARANG</th>
                <th class="col-qty" rowspan="2">KUANTITAS</th>
                <th class="col-tanggal" rowspan="2">TANGGAL<br>PEROLEHAN</th>
                <th colspan="2">NILAI PEROLEHAN</th>
            </tr>
            <tr>
                <th class="col-nup">AWAL</th>
                <th class="col-nup">AKHIR</th>
                <th class="col-nilai">PERTAMA</th>
                <th class="col-jumlah">JUMLAH<br>NILAI</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="col-no">{{ $start_number + $index }}</td>
                <td class="col-kode">{{ $row->kode_barang }}</td>
                <td class="col-nup">{{ $row->nup_awal }}</td>
                <td class="col-nup">{{ $row->nup_akhir }}</td>
                <td class="col-nama">{{ $row->nama_barang }}</td>
                <td class="col-qty">{!! $row->formatted_kuantitas !!}</td>
                <td class="col-tanggal">{!! $row->formatted_tanggal !!}</td>
                <td class="col-nilai">{!! $row->formatted_nilai_satuan !!}</td>
                <td class="col-jumlah">{!! $row->formatted_jumlah_nilai !!}</td>
            </tr>
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="5" style="text-align: center;">Jumlah</td>
                <td class="col-qty">{!! $formatted_total_kuantitas !!}</td>
                <td></td>
                <td class="col-nilai">{!! $formatted_total_nilai !!}</td>
                <td class="col-jumlah">{!! $formatted_total_jumlah !!}</td>
            </tr>
        </tbody>
    </table>

    @if($part_number && $total_parts > 1)
    <div class="part-info">
        Bagian {{ $part_number }} dari {{ $total_parts }} | Data: {{ $start_number }} - {{ $end_number }}
    </div>
    @endif

    <!-- Closing Statement -->
    <div class="closing-statement">
        adalah Barang Milik Negara yang dikuasai dan digunakan untuk penyelenggaraan tugas dan fungsi
        Kementerian/Lembaga.
    </div>

    <div class="closing-statement">
        Demikian pernyataan ini kami buat dengan sebenar-benarnya dalam rangka permohonan penetapan status
        penggunaan Barang Milik Negara.
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-location">
            Jakarta, {{ $tanggal }} {{ $bulan }} {{ $tahun }}
        </div>
        <div class="signature-title">
            Yang menyatakan,
        </div>

        <div class="materai-box">
            Materai<br>Rp10.000
        </div>

        <div class="signature-name">
            {{ $pejabat_data['nama'] ?? 'Indra Iskandar' }}
        </div>
    </div>
</body>
</html>
