{{-- resources/views/PerencanaanBMN/Batch/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Batch {{ $batch->kode_batch }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }
        h2 {
            font-size: 16px;
            margin: 20px 0 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th {
            width: 200px;
            text-align: left;
            padding: 5px;
            background-color: #f5f5f5;
        }
        .info-table td {
            padding: 5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 5px;
            border: 1px solid #ddd;
        }
        .data-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 10px;
            border-radius: 10px;
            color: white;
        }
        .badge-secondary { background-color: #6c757d; }
        .badge-primary { background-color: #007bff; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL BATCH PENGAJUAN SITANGGUH</h1>
        <p>Kode Batch: {{ $batch->kode_batch }}</p>
    </div>

    <h2>Informasi Batch</h2>
    <table class="info-table">
        <tr>
            <th>Kode Batch</th>
            <td>{{ $batch->kode_batch }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($batch->status == 'draft')
                    <span class="badge badge-secondary">Draft</span>
                @elseif($batch->status == 'dikirim')
                    <span class="badge badge-primary">Dikirim</span>
                @elseif($batch->status == 'diproses')
                    <span class="badge badge-warning">Diproses</span>
                @elseif($batch->status == 'selesai')
                    <span class="badge badge-success">Selesai</span>
                @elseif($batch->status == 'ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ $batch->tanggal_dibuat->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $batch->creator->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Total Nilai Batch</th>
            <td>Rp {{ number_format($batch->total_nilai_batch ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>{{ $batch->keterangan ?? '-' }}</td>
        </tr>
        @if($batch->tanggal_dikirim)
        <tr>
            <th>Tanggal Dikirim</th>
            <td>{{ $batch->tanggal_dikirim->format('d-m-Y H:i') }}</td>
        </tr>
        @endif
        @if($batch->tanggal_diproses)
        <tr>
            <th>Tanggal Diproses</th>
            <td>{{ $batch->tanggal_diproses->format('d-m-Y H:i') }}</td>
        </tr>
        @endif
        @if($batch->tanggal_selesai)
        <tr>
            <th>Tanggal Selesai</th>
            <td>{{ $batch->tanggal_selesai->format('d-m-Y H:i') }}</td>
        </tr>
        @endif
    </table>

    <h2>Daftar Pengajuan dalam Batch</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>ID Pengajuan</th>
                <th>Tanggal</th>
                <th>Bagian Pengusul</th>
                <th>Tipe</th>
                <th>Total Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batch->details->sortBy('urutan') as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->pengajuan->id }}</td>
                    <td>{{ $detail->pengajuan->created_at->format('d-m-Y') }}</td>
                    <td>{{ $detail->pengajuan->bagian_pengusul ?? '-' }}</td>
                    <td>{{ ucfirst($detail->pengajuan->tipe_pengajuan) }}</td>
                    <td>Rp {{ number_format($detail->pengajuan->total_nilai ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($detail->status_pengajuan_di_batch == 'draft')
                            <span class="badge badge-secondary">Draft</span>
                        @elseif($detail->status_pengajuan_di_batch == 'dikirim')
                            <span class="badge badge-primary">Dikirim</span>
                        @elseif($detail->status_pengajuan_di_batch == 'diproses')
                            <span class="badge badge-warning">Diproses</span>
                        @elseif($detail->status_pengajuan_di_batch == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($detail->status_pengajuan_di_batch == 'ditolak')
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada pengajuan dalam batch ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Log Aktivitas</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batch->logs->sortByDesc('created_at') as $log)
                <tr>
                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($log->aktivitas) }}</td>
                    <td>{{ $log->deskripsi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada log aktivitas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen dibuat pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Sistem Perencanaan BMN</p>
    </div>
</body>
</html>
