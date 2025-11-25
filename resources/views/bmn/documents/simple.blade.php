<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst(str_replace('_', ' ', $type)) }}</title>
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
        .content {
            margin: 20px 0;
        }
        .details {
            margin: 20px 0;
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
        <div class="title">{{ ucfirst(str_replace('_', ' ', $type)) }}</div>
    </div>

    <div class="content">
        <h3>Detail Pemanfaatan</h3>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Nama Mitra</td>
                <td>{{ $utilization->nama_mitra_penyewa ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Jenis Mitra</td>
                <td>{{ $utilization->jenis_mitra ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Jenis Usulan</td>
                <td>{{ $utilization->jenis_usulan ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>PIC Penyewa</td>
                <td>{{ $utilization->pic_penyewa ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>PIC Administrasi BMN</td>
                <td>{{ $utilization->pic_administrasi_bmn ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Peruntukan Sewa</td>
                <td>{{ $utilization->peruntukan_sewa ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>