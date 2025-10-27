<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penetapan Status Penggunaan BMN</title>
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

        /* Date and location */
        .date-location {
            text-align: right;
            margin: 20px 0;
            font-weight: normal;
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
                <td>{{ $lampiran ?? '1 (satu) Berkas' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="colon">:</td>
                <td style="width: 60%;">{{ $hal ?? 'Permohonan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Setjen DPR RI' }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Recipient -->
    <div class="recipient">
        <strong>{{ $kepada ?? 'Yth. Sekretaris Jenderal DPR RI' }}</strong><br>
        {{ $alamat_penerima ?? 'di Jl. Jenderal Gatot Subroto' }}<br>
        {{ $kota_penerima ?? 'Jakarta 10270' }}
    </div>

    <!-- Content -->
    <div class="content">
        <p>Menindaklanjuti Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah sebagaimana telah diubah dengan Peraturan Pemerintah Nomor 28 Tahun 2020 tentang Perubahan atas Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah dan Peraturan Menteri Keuangan Republik Indonesia Nomor 4/PMK.06/2015 tentang Pendelegasian Kewenangan dan Tanggung Jawab Tertentu dari Pengelola Barang kepada Pengguna Barang, bersama ini kami sampaikan dengan hormat usulan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Sekretariat Jenderal DPR RI berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} dengan nilai perolehan {{ $kategori_nilai ?? 'sampai dengan Rp100.000.000,00 (Seratus Juta Rupiah)' }} per unit/satuan sebagaimana daftar terlampir.</p>

        <p>Berkenaan dengan hal tersebut, mohon perkenan Bapak untuk dapat memberikan persetujuan penetapan status penggunaan terhadap Barang Milik Negara dimaksud.</p>

        <p class="no-indent">Atas perhatian dan perkenan Bapak, kami ucapkan terima kasih.</p>
    </div>

    <!-- Signature -->
    <div class="signature">
        <div class="signature-title">{{ $dari ?? 'Plt. Deputi Bidang Administrasi' }},</div>
        <div class="signature-name">{{ $pejabat_data['nama'] ?? 'Rudi Rochmansyah' }}</div>
    </div>
</body>
</html>
