{{--resources/views/PerencanaanBMN/Bagian/pdf/TorUsulan.blade.php--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen PDF - Term of Reference & Pengajuan RKBMN</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2.54cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            font-size: 10pt;
            text-align: justify;
        }
        .center {
            text-align: center;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 200px;
        }
        .info-table td:nth-child(2) {
            width: 20px;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        ol {
            padding-left: 20px;
        }
        ol.alpha {
            list-style-type: lower-alpha;
        }
        .signature {
            text-align: right;
            margin-top: 40px;
        }
        .signature-content {
            display: inline-block;
            text-align: center;
        }
        p { text-indent: 0.3cm; }
        /* Style untuk tabel detail (dengan border) */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid black;
            padding: 8px;
        }
        /* Halaman landscape untuk Pengajuan RKBMN */
        .landscape {
            page-break-before: always;
            page: landscape;
        }
        hr {
            margin: 40px 0;
            border: none;
            border-top: 1px solid #000;
        }
        /* Page break utilities */
        .page-break {
            page-break-after: always;
        }
        .page-break-before {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div style="position: relative; border-bottom: 1px solid black; padding-bottom: 15px; margin-bottom: 20px; width: 100%;">
        <!-- Logo container with fixed positioning -->
        <div style="position: absolute; left: 0; top: 0;">
            <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo Setjen DPR RI" style="height: 90px; width: auto;">
        </div>

        <!-- Text container with proper centering and single line titles -->
        <div style="text-align: center; margin-left: 120px; margin-right: 10px; padding-top: 10px;">
            <div style="font-weight: bold; font-size: 1.4em; font-family: Arial, sans-serif; line-height: 1.5; margin-bottom: 5px;">SEKRETARIAT JENDERAL</div>
            <div style="font-weight: bold; font-size: 1.4em; font-family: Arial, sans-serif; line-height: 1.5; margin-bottom: 8px; white-space: nowrap;">DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA</div>
            <div style="font-size: 0.8em; font-family: Arial, sans-serif; line-height: 1.4;">JL. JENDERAL GATOT SUBROTO JAKARTA KODE POS 10270</div>
            <div style="font-size: 0.8em; font-family: Arial, sans-serif; line-height: 1.4;">TELP (021) 5715 349 FAX (021) 5715 423 / 5715 925, WEBSITE: www.dpr.go.id</div>
        </div>
    </div>

    <!-- Halaman Term of Reference (Potrait) -->
    <div class="center">
        <strong><i>TERM OF REFERENCE</i></strong>
        <p>
            <strong>
                RENCANA PENGADAAN NON SBSK DAN PEMELIHARAAN BAGIAN<br>
                {{ strtoupper($uraianBagianPengusul) }} PADA RENCANA KEBUTUHAN BARANG MILIK NEGARA<br>
                (RKBMN) TAHUN ANGGARAN {{ $tahunAnggaranPengusulan }}
            </strong>
        </p>
    </div>

    <table class="info-table">
        <tr>
            <td>Kementerian Negara/</td>
            <td>:</td>
            <td>DEWAN PERWAKILAN RAKYAT RI</td>
        </tr>
        <tr>
            <td>Lembaga</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Unit Eselon I</td>
            <td>:</td>
            <td>SEKRETARIAT JENDERAL DPR RI</td>
        </tr>
        <tr>
            <td>Hasil</td>
            <td>:</td>
            <td>Terwujudnya Tata Kelola Barang Milik Negara</td>
        </tr>
        <tr>
            <td>Unit Eselon II/Satker</td>
            <td>:</td>
            <td>{{ $uraianBiroPengusul }}</td>
        </tr>
        <tr>
            <td>Kegiatan</td>
            <td>:</td>
            <td>
                Rencana Pengadaan Non SBSK dan Pemeliharaan {{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }}
            </td>
        </tr>
        <tr>
            <td>Indikator Kinerja Kegiatan</td>
            <td>:</td>
            <td>Indeks Pengelolaan Aset</td>
        </tr>
        <tr>
            <td>Satuan/Ukuran dan Jenis Keluaran</td>
            <td>:</td>
            <td>1 (satu) Laporan</td>
        </tr>
        <tr>
            <td>Volume</td>
            <td>:</td>
            <td>1 (satu) Kali Kegiatan</td>
        </tr>
        <tr>
            <td>No. Pengajuan</td>
            <td>:</td>
            <td>{{ $kodePengajuan }}</td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">A. Latar Belakang</div>
        <p>
            Pengelolaan Barang Milik Negara (BMN) merupakan bagian integral dari pengelolaan keuangan negara yang harus dilakukan secara tertib, efisien, transparan, dan akuntabel. Berdasarkan ketentuan Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah dan perubahannya dalam PP Nomor 28 Tahun 2020, setiap instansi pemerintah wajib merencanakan kebutuhan BMN sebagai bagian dari siklus pengelolaan BMN, guna menjamin ketersediaan sarana dan prasarana dalam mendukung pelaksanaan tugas dan fungsi organisasi.
        </p>
        <p class="page-break-before">
            Lebih lanjut, dalam <strong> Menteri Keuangan Nomor 181/PMK.06/2016</strong> tentang Penatausahaan BMN ditegaskan bahwa kebutuhan BMN yang tidak termasuk dalam SBSK dapat direncanakan dan dilaksanakan selama sesuai dengan peraturan perundang-undangan dan mendukung tugas pokok serta fungsi unit kerja.
        </p>
        <p>
            Dengan mengacu pada kebijakan tersebut, penyusunan <em>Term of Reference</em> (ToR) ini dimaksudkan untuk memberikan penjelasan teknis dan justifikasi terhadap rencana pengadaan BMN Non SBSK dan Pemeliharaan yang diajukan oleh masing-masing unit kerja. ToR ini menjadi dasar dalam proses pengusulan, penilaian, dan pelaksanaan pengadaan BMN Non SBSK dan Pemeliharaan secara tertib administrasi dan sesuai ketentuan yang berlaku.
        </p>
    </div>

    <div class="section">
        <div class="section-title">B. Dasar Hukum</div>
        <ol>
            <li>
                Peraturan Peraturan Pemerintah Nomor 27 Tahun 2014 tentang Pengelolaan Barang Milik Negara/Daerah sebagaimana telah diubah dengan Peraturan Pemerintah Nomor 28 Tahun 2020 tentang Pengelolaan Barang Milik Negara/ Daerah;            </li>
            <li>
                Peraturan Menteri Keuangan Nomor 181 tahun 2016 tentang Penatausahaan Barang Milik Negara;
            </li>
            <li>
                Peraturan Menteri Keuangan Nomor 153 Tahun 2021 tentang Perencanaan Kebutuhan Barang Milik Negara;
            </li>
            <li>
                Peraturan Menteri Keuangan Nomor 138 Tahun 2024 tentang Standar Barang Standar Kebutuhan;
            </li>
            <li>
                Peraturan Menteri Keuangan Nomor 139 Tahun 2024 tentang Pengadaan Barang Milik Negara;
            </li>
            <li>
                Peraturan Sekretaris Jenderal Dewan Perwakilan Rakyat Republik Indonesia Nomor 6 Tahun 2024 Tentang Perubahan Keempat Atas Peraturan Sekretaris Jenderal Dewan Perwakilan Rakyat Republik Indonesia Nomor 6 Tahun 2021 tentang Organisasi dan Tata Kerja Sekretariat Jenderal Dewan Perwakilan Rakyat Republik Indonesia;
            </li>
            <li>
                Dewan Perwakilan Rakyat Republik Indonesia Nomor 2124/SEKJEN/2024 tentang Penetapan Pengguna Aplikasi DigitAll Modul Aset di Sekretariat Jenderal Dewan Perwakilan Rakyat Republik Indonesia;
            </li>
            <li>
                Surat Keputusan Sekretaris Jenderal Dewan Perwakilan Rakyat Republik Indonesia nomor 1239/SEKJEN/2025 tentang Penetapan Standar kebutuhan alat kantor berupa peralatan dan mesin Sekretariat Jenderal Dewan Perwakilan Rakyat Republik Indonesia.
            </li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">C. Tujuan</div>
        <p>
            Adapun tujuan dari kegiatan "Rencana Pengadaan Non SBSK dan Pemeliharaaan{{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }}" adalah:
        </p>
        <ol class="alpha">
            <li>Terciptanya perencanaan yang baik dalam pengelolaan Barang Milik Negara</li>
            <li>Terakomodasinya kebutuhan barang milik negara sebagai penunjang tugas dan fungsi {{ $uraianBagianPengusul }};</li>
            <li>Dasar dalam pengajuan RKBMN TA {{ $tahunAnggaranPengusulan }}</li>
            <li>{{ $keterangan }}</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">D. Ruang Lingkup Kegiatan</div>
        <p>
            Kegiatan Rencana Pengadaan Non SBSK dan Pemeliharaan {{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }} dilaksanakan oleh:
        </p>
        <ol>
            <li>{{ $uraianBagianPengusul }} selaku Operator BMN</li>
            <li>{{ $uraianBagianPengusul }} selaku Pengusul BMN</li>
            <li>Administrasi BMN selaku Koordinator BMN</li>
            <li>Bagian Perencanaan selaku pengelola rencana anggaran BMN</li>
        </ol>
    </div>

    <div class="section">
        <div class="section-title">E. <i>Output</i> Kegiatan</div>
        <p>
            Kegiatan "Rencana Pengadaan {{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }}" diharapkan menghasilkan ketepatan pada perencanaan kebutuhan BMN pada Dewan Perwakilan Rakyat Republik Indonesia.
        </p>
    </div>

    <!-- Page break untuk section F -->
    <div class="section">
        <div class="section">
            <div class="section-title">F. Jadwal Kegiatan</div>
            <p>
                Kegiatan "Rencana Pengadaan Non SBSK dan Pemeliharaan {{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }}" akan dilaksanakan sebelum Penyusunan RKA K/L Tahun Anggaran {{ $tahunAnggaranPersetujuan }}.
            </p>
        </div>

        <div class="section">
            <div class="section-title">G. Penutup</div>
            <p>
                Demikian <i>Term of Reference</i> ini dibuat untuk menjadi pedoman dalam kegiatan Rencana Pengadaan {{ $uraianBagianPengusul }} pada Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }} serta menjadi dasar dalam penyusunan Rencana Kebutuhan Barang Milik Negara (RKBMN) Tahun Anggaran {{ $tahunAnggaranPengusulan }} Dewan Perwakilan Rakyat Republik Indonesia.
            </p>
        </div>

        <div class="signature">
            <div class="signature-content">
                <strong>Jakarta, {{ $tanggalPengajuan }}</strong>
                <br>
                <strong>Penanggung Jawab Kegiatan</strong>
                <br><br><br><br>
                <!-- E-sign akan ditambahkan di atas nama -->
                <br><br>
                {{ $namaPenanggungJawabPelaksana ?? '-' }}
            </div>
        </div>
    </div>
</body>
</html>
