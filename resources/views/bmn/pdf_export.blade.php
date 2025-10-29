<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistical Dashboard Pengajuan BMN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
        }
        .date {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Statistical Dashboard Pengajuan BMN</div>
        <div class="date">Ditampilkan pada: {{ date('d M Y H:i:s') }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Jenis</th>
                <th>Bagian Pengusul</th>
                <th>Program</th>
                <th>Kegiatan</th>
                <th>Status</th>
                <th>Tahun Anggaran</th>
                <th>Skema</th>
                <th>ATR/Non-ATR</th>
                <th>Total Anggaran</th>
                <th>Kuantitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->kode_jenis_pengajuan }}</td>
                <td>{{ $item->id_bagian_pengusul }}</td>
                <td>{{ $item->program }}</td>
                <td>{{ $item->kegiatan }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->tahun_anggaran }}</td>
                <td>{{ $item->skema }}</td>
                <td>{{ $item->atr_nonatr }}</td>
                <td>Rp {{ number_format($item->total_anggaran, 0, ',', '.') }}</td>
                <td>{{ $item->kuantitas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px;">
        <p>Jumlah Total Pengajuan: {{ $data->count() }}</p>
        <p>Total Anggaran: Rp {{ number_format($data->sum('total_anggaran'), 0, ',', '.') }}</p>
    </div>
</body>
</html>