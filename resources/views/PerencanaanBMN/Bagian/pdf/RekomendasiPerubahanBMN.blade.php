<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi Hasil Penelaahan Akun</title>
    <style>
        /* Menggunakan style yang sama dari BeritaAcara.blade.php untuk konsistensi */
        @page { size: A4 portrait; margin: 2.54cm; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; line-height: 1.5; font-size: 11pt; }
        .header { position: relative; text-align: center; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { position: absolute; left: 0; top: 0; width: 75px; }
        .header-text { margin-left: 75px; text-align: center; font-weight: bold; line-height: 1.3; }
        .header-text-main { font-size: 14pt; }
        .contact-info { text-align: center; font-size: 9pt; margin-top: 2px; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 13pt; margin: 25px 0; }
        .content { text-align: justify; }
        p { margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10pt; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .signatures { margin-top: 40px; width: 100%; text-align: right; } /* Tanda tangan di kanan */
        .signature-col { display: inline-block; width: 300px; text-align: center; }
        .signature-title { font-weight: bold; margin-bottom: 70px; }
        .signature-name { font-weight: bold; text-decoration: underline; }

        /* [PERUBAHAN] Menambahkan class untuk page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            {{-- Pastikan path ke logo benar --}}
            @if(file_exists(public_path('assets/pic/logo_setjen_dpr.png')))
                <img src="{{ public_path('assets/pic/logo_setjen_dpr.png') }}" alt="Logo" style="width: 75px;">
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
        SURAT REKOMENDASI HASIL PENELAAHAN AKUN
    </div>

    <div class="content">
        <p>
            Sehubungan dengan hasil verifikasi dan penelaahan akun atas pengajuan Rencana Kebutuhan Barang Milik Negara (BMN)
            pada <strong>{{ $bagianPengusul->uraianbagian ?? 'Bagian Pengusul' }}</strong>
            berdasarkan kode pengajuan <strong>{{ $pengajuan->kode_pengajuan ?? '...' }}</strong>
            tanggal {{ \Carbon\Carbon::parse($pengajuan->created_at)->translatedFormat('d F Y') }}, bersama ini kami sampaikan hasil penelaahan akun untuk Tahun Anggaran {{ $pengajuan->tahun_anggaran }}.
        </p>
        <p>
            Berdasarkan hasil penelaahan tersebut, kami merekomendasikan agar dilakukan penyesuaian atau koreksi sesuai dengan ketentuan yang berlaku.
        </p>
        <p>
            Demikian surat rekomendasi ini disampaikan untuk dapat ditindaklanjuti sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
        </p>
    </div>

    <div class="signatures">
        <div class="signature-col">
            <div class="signature-title">Kepala Bagian Administrasi BMN</div><br><br>
            <br>
            {{-- Area ini akan menjadi tempat TTD Elektronik. Posisi sudah statis. --}}
            <div class="signature-name">Dedy Bagus Prakasa, S.E., M.Ak.</div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="title">
        HASIL PENELAAHAN AKUN
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-kode">Kode Barang</th>
                <th class="col-deskripsi">Deskripsi Barang</th>
                <th class="col-keterangan-barang">Keterangan Barang</th>
                <th class="col-akun-semula">Akun Semula</th>
                <th class="col-akun-menjadi">Akun Menjadi</th>
                <th class="col-keterangan-rekomendasi">Keterangan Perubahan Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-kode">{{ $item['kode_barang'] ?? '-' }}</td>
                <td class="col-deskripsi">{{ $item['deskripsi'] ?? 'Pengajuan Pemeliharaan' }}</td>
                <td class="col-keterangan-barang">{{ $item['keterangan_barang'] ?? '-' }}</td>
                <td class="col-akun-semula">{{ $item['akun_semula'] ?? '-' }}</td>
                <td class="col-akun-menjadi">{{ $item['akun_menjadi'] ?? '-' }}</td>
                <td class="col-keterangan-rekomendasi">{{ $item['keterangan_rekomendasi'] ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
