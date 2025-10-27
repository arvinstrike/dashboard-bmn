{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/review/ReviewComponentR3.blade.php --}}
{{-- Tampilan Review untuk R3 - Rumah Negara (REFACTORED) --}}

@php
    // Definisikan daftar ruangan sesuai dengan kolom di database dan label yang ingin ditampilkan
    $ruanganList = [
        'jumlah_ruang_tamu' => 'Ruang Tamu',
        'jumlah_ruang_kerja' => 'Ruang Kerja',
        'jumlah_ruang_duduk' => 'Ruang Duduk',
        'jumlah_ruang_fungsional' => 'Ruang Fungsional',
        'jumlah_ruang_makan' => 'Ruang Makan',
        'jumlah_ruang_tidur' => 'Ruang Tidur',
        'jumlah_ruang_wc' => 'Kamar Mandi/WC',
        'jumlah_dapur' => 'Dapur',
        'jumlah_gudang' => 'Gudang',
        'jumlah_garasi' => 'Garasi',
        'jumlah_ruang_tidur_pramuwisma' => 'Ruang Tidur Pramuwisma',
        'jumlah_ruang_cuci' => 'Ruang Cuci',
        'jumlah_kamar_mandi_pramuwisma' => 'Kamar Mandi Pramuwisma'
    ];
@endphp

{{-- Informasi Dasar Rumah Negara --}}
<div class="row">
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Peruntukan Pejabat:</label>
            {{-- Menggunakan 'peruntukan_pejabat' dari tabel detail --}}
            <span class="page-value">{{ $detailData->peruntukan_pejabat ?? 'Tidak ada data' }}</span>
        </div>
        <div class="page-row">
            <label class="page-label">Tipe Rumah:</label>
            {{-- Menggunakan 'jenis_rumah' sesuai kolom DB --}}
            <span class="page-value">{{ $detailData->jenis_rumah ?? 'Tidak ada data' }}</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Lokasi Rumah:</label>
            {{-- Menggunakan 'lokasi' sesuai kolom DB --}}
            <span class="page-value">{{ $detailData->lokasi ?? 'Tidak ada data' }}</span>
        </div>
        <div class="page-row">
            <label class="page-label">Jenis Pengadaan:</label>
             {{-- Menggunakan 'jenis_pengadaan_rumah' sesuai kolom DB --}}
            <span class="page-value">{{ $detailData->jenis_pengadaan_rumah ?? 'Tidak ada data' }}</span>
        </div>
    </div>
</div>

<hr>

{{-- Luas Tanah dan Bangunan --}}
<div class="row">
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Luas Bangunan Diajukan:</label>
            {{-- Menggunakan 'luas_bangunan' sesuai kolom DB --}}
            <span class="page-value font-weight-bold">{{ $detailData->luas_bangunan ?? 0 }} m²</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="page-row">
            <label class="page-label">Luas Tanah Diajukan:</label>
             {{-- Menggunakan 'luas_tanah' sesuai kolom DB --}}
            <span class="page-value font-weight-bold">{{ $detailData->luas_tanah ?? 0 }} m²</span>
        </div>
    </div>
</div>

{{-- Detail Kebutuhan Ruang --}}
<div class="mt-3">
    <h6 class="font-weight-bold">Detail Ruangan yang Diajukan</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Jenis Ruang</th>
                    <th class="text-center">Jumlah Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ruanganList as $kolom => $label)
                    @if(isset($detailData->$kolom) && $detailData->$kolom > 0)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-center">{{ $detailData->$kolom }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
