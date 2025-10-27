<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Usulan RKBMN</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2.54cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-size: 11pt;
        }
        .header {
            position: relative;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
        }
        .header-text {
            margin-left: 75px;
            text-align: center;
            font-weight: bold;
            line-height: 1.3;
        }
        .header-text-main {
            font-size: 14pt;
        }
        .contact-info {
            text-align: center;
            font-size: 9pt;
            margin-top: 2px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            margin: 15px 0 8px 0;
        }
        .doc-number {
            text-align: center;
            margin-bottom: 15px;
        }
        .content {
            text-align: justify;
        }
        p {
            margin: 6px 0;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signature-row {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-bottom: 20px;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 20px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            line-height: 1.4;
            font-size: 10pt;
        }
        .signature-name {
            font-size: 10pt;
            border-top: 1px solid #000;
            display: inline-block;
            width: 150px;
            padding-top: 3px;
            margin-top: 50px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .pejabat-info {
            margin: 3px 0;
        }
        .pejabat-label {
            display: inline-block;
            width: 60px;
        }
        .pejabat-role {
            margin: 2px 0 8px 0;
            font-style: italic;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
                <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI" style="width: 75px;">
            @endif
        </div>
        <div class="header-text">
            <div class="header-text-main">SEKRETARIAT JENDERAL<br>
            DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA</div>
        </div>
        <div class="contact-info">
            JL. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270<br>
            TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE: www.dpr.go.id
        </div>
    </div>

    <div class="title">
        BERITA ACARA USULAN RENCANA KEBUTUHAN BARANG MILIK NEGARA<br>
        (RKBMN) TAHUN ANGGARAN {{ $tahunAnggaran }} {{ $uraianBagianPengusul }} PADA APLIKASI<br>
        DIGITALL
    </div>

    <div class="doc-number">
        NOMOR: BA/{{ date('m') }}/KN.01.01/{{ date('m') }}/{{ date('Y') }}
    </div>

    <div class="content">
        <p>Pada Hari ini, Tanggal {{ $tanggal }}, Bulan {{ $bulan }}, Tahun {{ $tahunKata }}, bertempat di Jakarta, kami bertanda tangan di bawah ini:</p>

        <div class="pejabat-info">
            I.&nbsp;&nbsp;&nbsp;<span class="pejabat-label">Nama</span>: {{ $pengusulNama ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">NIP</span>: {{ $pengusulNip ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">Jabatan</span>: {{ $pengusulJabatan ?? 'Kepala Bagian '.$uraianBagianPengusul }}
            <div class="pejabat-role">Dalam hal ini bertindak selaku Operator (Pengusul)</div>
        </div>

        <div class="pejabat-info">
            II.&nbsp;&nbsp;<span class="pejabat-label">Nama</span>: {{ $pelaksanaNama ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">NIP</span>: {{ $pelaksanaNip ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">Jabatan</span>: {{ $pelaksanaJabatan ?? 'Kepala Bagian '.$uraianBagianPelaksana }}
            <div class="pejabat-role">Dalam hal ini bertindak selaku Pelaksana</div>
        </div>

        <div class="pejabat-info">
            III.&nbsp;<span class="pejabat-label">Nama</span>: {{ $koordinatorNama ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">NIP</span>: {{ $koordinatorNip ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">Jabatan</span>: {{ $koordinatorJabatan ?? 'Kepala Bagian Administrasi BMN' }}
            <div class="pejabat-role">Dalam hal ini bertindak selaku Koordinator BMN</div>
        </div>

        <div class="pejabat-info">
            IV.&nbsp;<span class="pejabat-label">Nama</span>: {{ $perencanaanNama ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">NIP</span>: {{ $perencanaanNip ?? '.................................' }}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pejabat-label">Jabatan</span>: {{ $perencanaanJabatan ?? 'Kepala Bagian Perencanaan' }}
            <div class="pejabat-role">Dalam hal ini bertindak selaku Perencanaan</div>
        </div>

        <p>Menyatakan bahwa telah melakukan pengajuan Usulan RKBMN-Non SBSK Tahun Anggaran {{ $tahunAnggaran }} dengan cara melakukan penginputan pada Aplikasi DigitAll melalui user Operator BMN unit kerja pengusul BMN dan telah dilakukan proses verifikasi oleh Pelaksana BMN untuk diajukan kepada Koordinator BMN dan Bagian Perencanaan sesuai dengan data terlampir.</p>

        <p>Demikian Berita Acara Usulan Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaran }} ini dibuat sebagai dasar dalam penyusunan rencana kerja anggaran satker Setjen DPR RI Untuk TA {{ $tahunAnggaran }}.</p>

        <p>Apabila dikemudian hari terdapat kekeliruan maka akan dilakukan perbaikan sebagaimana mestinya.</p>
    </div>

    <div class="signatures">
        <!-- Baris pertama: Pengusul dan Pelaksana -->
        <div class="signature-row">
            <div class="signature-col">
                <div class="signature-title">
                    Kepala Bagian Pengusul<br>
                    selaku Operator (Pengusul)
                </div>
                <div class="signature-name">{{ $pengusulNama ?? '........................' }}</div>
            </div>

            <div class="signature-col">
                <div class="signature-title">
                    Kepala Bagian Pelaksana<br>
                    selaku Pelaksana
                </div>
                <div class="signature-name">{{ $pelaksanaNama ?? '........................' }}</div>
            </div>
        </div>

        <!-- Baris kedua: Koordinator BMN dan Perencanaan -->
        <div class="signature-row">
            <div class="signature-col">
                <div class="signature-title">
                    Kepala Bagian Administrasi BMN<br>
                    selaku Koordinator BMN
                </div>
                <div class="signature-name">{{ $koordinatorNama ?? '........................' }}</div>
            </div>

            <div class="signature-col">
                <div class="signature-title">
                    Kepala Bagian Perencanaan<br>
                    selaku Perencanaan
                </div>
                <div class="signature-name">{{ $perencanaanNama ?? '........................' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
