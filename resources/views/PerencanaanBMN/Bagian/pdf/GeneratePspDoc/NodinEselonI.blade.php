<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Dinas Eselon I - Usulan PSP</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.15;
            font-size: 11pt;
        }

        /* Header Layout - Menggunakan struktur dari template sebelumnya */
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            line-height: 1.3;
        }

        .header-text-main {
            font-size: 14pt;
            margin-bottom: 5px;
        }

        .header-text-address {
            font-size: 9pt;
            font-weight: normal;
            margin-top: 5px;
        }

        .header-text-contact {
            font-size: 8pt;
            font-weight: normal;
            margin-top: 2px;
        }

        .sub-header {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0 15px 0;
            text-decoration: underline;
        }

        .nota-dinas-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
        }

        .document-info {
            margin: 20px 0;
        }

        .document-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .document-info .label {
            width: 80px;
        }

        .document-info .colon {
            width: 15px;
        }

        .content {
            text-align: justify;
            margin: 20px 0;
        }

        .content p {
            margin: 8px 0;
        }

        .content ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .content ol li {
            margin: 8px 0;
            text-align: justify;
        }

        .signature {
            margin-top: 30px;
            text-align: left;
            width: 50%;
            margin-left: auto;
        }

        .signature-name {
            margin-top: 60px;
            text-decoration: underline;
        }

        /* Style khusus untuk paragraf pembuka yang panjang */
        .opening-paragraph {
            margin: 15px 0;
            text-align: justify;
            text-indent: 30px;
        }
    </style>
</head>
<body>
    <!-- Header/Kop Surat - Struktur sama dengan template sebelumnya -->
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
                Jl. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270
            </div>
            <div class="header-text-contact">
                TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE: www.dpr.go.id
            </div>
        </div>
    </div>

    <div class="sub-header">
        {{ strtoupper($deputi_nama ?? 'DEPUTI BIDANG ADMINISTRASI') }}
    </div>

    <div class="nota-dinas-title">
        NOTA DINAS<br>
        Nomor: {{ $nomor_surat }}
    </div>

    <div class="document-info">
        <table>
            <tr>
                <td class="label">Kepada</td>
                <td class="colon">:</td>
                <td>{{ $kepada ?? 'Sekretaris Jenderal DPR RI' }}</td>
            </tr>
            <tr>
                <td class="label">Dari</td>
                <td class="colon">:</td>
                <td>{{ $dari ?? 'Plt. Deputi Bidang Administrasi' }}</td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="colon">:</td>
                <td>{{ $hal ?? 'Permohonan Tanda Tangan' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="colon">:</td>
                <td>{{ $tanggal }} {{ $bulan }} {{ $tahun }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <div class="opening-paragraph">
            Berdasarkan Peraturan Pemerintah Nomor: 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah sebagaimana telah diubah dengan Peraturan Pemerintah Nomor 28 Tahun 2020 tentang Perubahan atas Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah dan Peraturan Menteri Keuangan Republik Indonesia Nomor 4/PMK.06/2015 tentang Pendelegasian Kewenangan dan Tanggung Jawab Tertentu dari Pengelola Barang kepada Pengguna Barang, bersama ini kami sampaikan dengan hormat:
        </div>

        <ol>
            <li>Surat Permohonan Penetapan Status Penggunaan BMN oleh Pengguna Barang pada Setjen DPR RI berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} dengan nilai perolehan {{ $kategori_nilai ?? 'sampai dengan Rp100.000.000,-' }} yang ditujukan kepada Sekretaris Jenderal DPR RI;</li>

            <li>Surat Permohonan Penetapan Status Penggunaan BMN oleh Pengguna Barang pada Setjen DPR RI berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} dengan nilai perolehan {{ $kategori_nilai ?? 'sampai dengan Rp100.000.000,-' }} yang ditujukan kepada Kepala Kantor Pelayanan Kekayaan Negara dan Lelang (KPKNL) Jakarta I.</li>

            <li>Surat Pernyataan Tanggung Jawab Mutlak dalam rangka Permohonan Penetapan Status Penggunaan BMN oleh Pengguna Barang pada Setjen DPR RI berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} dengan nilai perolehan {{ $kategori_nilai ?? 'sampai dengan Rp100.000.000,-' }} yang ditujukan kepada Sekretaris Jenderal DPR RI.</li>

            <li>Surat Pernyataan Kebenaran Arsip Digital dalam rangka Permohonan Penetapan Status Penggunaan BMN oleh Pengguna Barang pada Setjen DPR RI berupa {{ $jenis_bmn ?? 'Peralatan dan Mesin' }} dengan nilai perolehan {{ $kategori_nilai ?? 'sampai dengan Rp100.000.000,-' }} yang ditujukan kepada Sekretaris Jenderal DPR RI.</li>
        </ol>

        <p>Apabila tidak ada koreksi, mohon perkenan Bapak untuk dapat memberikan tanda tangan pada surat sebagaimana terlampir</p>

        <p>Atas perhatian dan perkenan Bapak, kami ucapkan terima kasih.</p>
    </div>

    <div class="signature">
        <p>{{ $dari ?? 'Plt. Deputi Bidang Administrasi' }},</p>
        <div class="signature-name">{{ $pejabat_data['nama'] ?? 'Rudi Rochmansyah, S.H., M.H.' }}</div>
    </div>
</body>
</html>
