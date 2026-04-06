<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BmnPengajuanRkbmnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dummy untuk bmn_pengajuanrkbmnbagian
        // Menggunakan kode jenis pengajuan yang sesuai dengan getJenisPengajuanOptions() di Controller
        // R1: Tanah dan/atau bangunan perkantoran
        // R3: Tanah dan/atau gedung rumah negara
        // R4: Kendaraan Jabatan
        // R5: Kendaraan Operasional
        // R6: Kendaraan Fungsional
        
        $data = [
            [
                'kode_jenis_pengajuan' => 'R1 (Pengadaan Baru)',
                'id_bagian_pengusul' => 'BAG001',
                'program' => 'Program Dukungan Manajemen',
                'kegiatan' => 'Pengelolaan BMN',
                'output' => 'Layanan Perkantoran',
                'kode_barang' => '3050105001',
                'status' => 'approved',
                'tahun_anggaran' => '2024',
                'harga_barang' => 15000000,
                'total_anggaran' => 75000000, // 5 unit
                'uraian_barang' => 'Laptop High Performance untuk Tim IT',
                'tanggal_pengajuan' => Carbon::now()->subDays(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_jenis_pengajuan' => 'R5 (Pemeliharaan)',
                'id_bagian_pengusul' => 'BAG002',
                'program' => 'Program Sarana Prasarana',
                'kegiatan' => 'Pemeliharaan Gedung',
                'output' => 'Gedung Terawat',
                'kode_barang' => '4010101001',
                'status' => 'pending',
                'tahun_anggaran' => '2024',
                'harga_barang' => 50000000,
                'total_anggaran' => 50000000,
                'uraian_barang' => 'Perbaikan Atap Gedung A',
                'tanggal_pengajuan' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_jenis_pengajuan' => 'R4 (Pengadaan Baru)',
                'id_bagian_pengusul' => 'BAG003',
                'program' => 'Program Layanan Umum',
                'kegiatan' => 'Operasional Kantor',
                'output' => 'Peralatan Kantor',
                'kode_barang' => '3030101002',
                'status' => 'rejected',
                'tahun_anggaran' => '2025',
                'harga_barang' => 2500000,
                'total_anggaran' => 25000000, // 10 unit
                'uraian_barang' => 'Kursi Kerja Ergonomis',
                'tanggal_pengajuan' => Carbon::now()->subDays(20),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_jenis_pengajuan' => 'R1 (Pengadaan Baru)',
                'id_bagian_pengusul' => 'BAG001',
                'program' => 'Program Dukungan Manajemen',
                'kegiatan' => 'Pengelolaan BMN',
                'output' => 'Layanan Perkantoran',
                'kode_barang' => '3050201003',
                'status' => 'approved',
                'tahun_anggaran' => '2024',
                'harga_barang' => 8000000,
                'total_anggaran' => 24000000, // 3 unit
                'uraian_barang' => 'PC All-in-One untuk Front Office',
                'tanggal_pengajuan' => Carbon::now()->subDays(15),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_jenis_pengajuan' => 'R6 (Pemeliharaan)',
                'id_bagian_pengusul' => 'BAG004',
                'program' => 'Program Keamanan',
                'kegiatan' => 'Pemeliharaan Keamanan',
                'output' => 'Sistem Keamanan',
                'kode_barang' => '3060101005',
                'status' => 'in_progress',
                'tahun_anggaran' => '2025',
                'harga_barang' => 12000000,
                'total_anggaran' => 12000000,
                'uraian_barang' => 'Maintenance CCTV Gedung Utama',
                'tanggal_pengajuan' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('bmn_pengajuanrkbmnbagian')->insert($data);
    }
}
