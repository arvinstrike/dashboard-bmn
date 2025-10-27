<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Persetujuan Penetapan Status Penggunaan BMN</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2.24cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-size: 11pt;
        }

        /* Header Layout - Format surat resmi */
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }

        .header-text {
            margin-left: 90px;
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
        }

        .header-text-main {
            font-size: 13pt;
            margin-bottom: 5px;
        }

        .header-text-address {
            font-size: 9pt;
            font-weight: normal;
            margin-top: 8px;
            line-height: 1.2;
        }

        /* Document info section */
        .document-header {
            margin: 25px 0;
        }

        .document-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-header td {
            padding: 2px 0;
            vertical-align: top;
        }

        .document-header .label {
            width: 80px;
            font-weight: normal;
        }

        .document-header .colon {
            width: 15px;
        }

        /* Recipient section */
        .recipient {
            margin: 25px 0;
            line-height: 1.3;
        }

        /* Content styling */
        .content {
            text-align: justify;
            margin: 20px 0;
            line-height: 1.4;
        }

        .content p {
            margin: 12px 0;
            text-indent: 30px;
        }

        .content .no-indent {
            text-indent: 0;
        }

        /* Reference paragraph styling */
        .reference-paragraph {
            text-indent: 30px;
            margin: 15px 0;
        }

        /* Signature section */
        .signature {
            margin-top: 40px;
            text-align: right;
            width: 50%;
            margin-left: auto;
        }

        .signature-title {
            margin-bottom: 60px;
        }

        .signature-name {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header/Kop Surat -->
    <div class="header">
        <div class="logo">
            @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
                <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI" style="width: 80px;">
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

    <!-- Document Header Information -->
    <div class="document-header">
        <table>
            <tr>
                <td class="label">Nomor</td>
                <td class="colon">:</td>
                <td>{{ $nomor_surat }}</td>
                <td style="text-align: right;">Jakarta, {{ $tanggal }} {{ $bulan }} {{ $tahun }}</td>
            </tr>
            <tr>
                <td class="label">Sifat</td>
                <td class="colon">:</td>
                <td>{{ $sifat ?? 'Segera' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="colon">:</td>
                <td>{{ $lampiran ?? '1 (satu) berkas' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="colon">:</td>
                <td style="width: 60%;">{{ $hal ?? 'Persetujuan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Setjen DPR RI' }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Recipient -->
    <div class="recipient">
        <strong>{{ $kepada ?? 'Yth. Plt. Deputi Bidang Administrasi' }}</strong><br>
        {{ $alamat_penerima ?? 'di Jl. Jenderal Gatot Subroto' }}<br>
        {{ $kota_penerima ?? 'Jakarta 10270' }}
    </div>

    <!-- Content -->
    <div class="content">
        <div class="reference-paragraph">
            Sehubungan dengan Surat Saudara Nomor {{ $nomor_referensi ?? 'B/8916/KN.02.04/07/2025' }} Tanggal {{ $tanggal_referensi ?? '3 Juli 2025' }} Perihal Permohonan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Setjen DPR RI, dengan ini disampaikan bahwa permohonan Penetapan Status Penggunaan Barang Milik Negara berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} sebagaimana tercantum dalam lampiran surat ini, pada prinsipnya dapat disetujui.
        </div>

        <p>Dalam rangka mewujudkan tertib administrasi pengelolaan Barang Milik Negara, kiranya penatausahaan Barang Milik Negara di lingkungan Sekretariat Jenderal DPR RI dapat dilakukan secara tertib sebagaimana diatur dalam Peraturan Pemerintah Nomor 28 Tahun 2020 tentang Perubahan atas Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah, Peraturan Menteri Keuangan Republik Indonesia Nomor 4/PMK.06/2015 tentang Pendelegasian Kewenangan dan Tanggung Jawab Tertentu dari Pengelola Barang kepada Pengguna Barang, dan Peraturan Menteri Keuangan Nomor 76/PMK.06/2019 tentang Perubahan Kedua atas Peraturan Menteri Keuangan Republik Indonesia Nomor 246/PMK.06/2014 tentang Tata Cara Pelaksanaan Penggunaan Barang Milik Negara.</p>

        <p class="no-indent">Demikian disampaikan, atas perhatiannya kami ucapkan terima kasih.</p>
    </div>

    <!-- Signature -->
    <div class="signature">
        <div class="signature-title">{{ $dari ?? 'Sekretaris Jenderal' }},</div>
        <div class="signature-name">{{ $pejabat_data['nama'] ?? 'Indra Iskandar' }}</div>
    </div>
</body>
</html>
