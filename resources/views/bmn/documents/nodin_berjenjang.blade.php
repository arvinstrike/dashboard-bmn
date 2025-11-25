<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nodin Berjenjang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }
        .to-section {
            margin: 20px 0;
        }
        .details {
            margin: 20px 0;
        }
        .content {
            margin: 30px 0;
            text-align: justify;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .date-place {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">NODIN BERJENJANG</div>
    </div>

    <div class="to-section">
        <p>Kepada Yth:</p>
        <p>Direktur Utama PT {{ $utilization->nama_mitra_penyewa }}</p>
        <p>Di Tempat</p>
    </div>

    <div class="details">
        <table style="width: auto;">
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td>{{ $utilization->nodin_berjenjang_nomor ?? '........................' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ $utilization->nodin_berjenjang_tanggal ? \Carbon\Carbon::parse($utilization->nodin_berjenjang_tanggal)->isoFormat('D MMMM YYYY') : '........................' }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td>Permohonan Persetujuan {{ $utilization->nodin_berjenjang_mitra_peruntukan ?? '........................' }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Dengan hormat,</p>
        
        <p>Bersama ini kami sampaikan bahwa berdasarkan usulan dari {{ $utilization->nama_mitra_penyewa ?? '........................' }} terkait {{ $utilization->nodin_berjenjang_mitra_peruntukan ?? '........................' }}, maka kami mohon persetujuan Bapak/Ibu untuk dapat dilanjutkan proses pemanfaatan Barang Milik Negara tersebut.</p>
        
        <p>Adapun data pendukung yang kami sampaikan antara lain:</p>
        <ul>
            <li>Nama Mitra: {{ $utilization->nama_mitra_penyewa ?? '........................' }}</li>
            <li>Jenis Mitra: {{ $utilization->jenis_mitra ?? '........................' }}</li>
            <li>Jenis Usulan: {{ $utilization->jenis_usulan ?? '........................' }}</li>
        </ul>
    </div>

    <p>Demikian disampaikan, atas perhatian dan persetujuannya kami ucapkan terima kasih.</p>

    <div class="signature">
        <p>Hormat kami,</p>
        <br><br><br>
        <p>Pejabat Pembuat Komitmen</p>
    </div>
</body>
</html>