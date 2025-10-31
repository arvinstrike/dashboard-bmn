<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BmnPengajuanrkbmnbagian;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 19,
                'kode_jenis_pengajuan' => 'R1-004',
                'id_bagian_pengusul' => 669,
                'id_biro_pengusul' => 664,
                'id_bagian_pelaksana' => 678,
                'id_biro_pelaksana' => 677,
                'program' => 'WA | Program Dukungan Manajemen',
                'kegiatan' => '6575',
                'output' => 'EBA',
                'kode_barang' => '2010104004',
                'status' => 'approved',
                'tahun_anggaran' => 2027,
                'tanggal_pengajuan' => '2025-05-07',
                'tanggal_kebmn' => NULL,
                'tanggal_keperencanaan' => NULL,
                'tanggal_final' => NULL,
                'tujuan_rencana' => 'Perluasan',
                'atr_nonatr' => 'Non ATR',
                'skema' => 'Pembelian',
                'harga_barang' => 1000000000,
                'total_anggaran' => 4000000000,
                'uraian_barang' => 'Uraian Spesifikasi Kantor 2',
                'keterangan' => 'Keterangan Kantor 2',
                'dokumen_pendukung' => NULL,
                'alasan_pengusul_bmn' => NULL,
                'alasan_koordinator_bmn' => NULL,
                'alasan_perencanaan' => NULL,
                'akun_belanja' => '531111 - Belanja Modal Tanah',
                'akun_neraca' => '131111 - Tanah',
                'kuantitas' => 4,
                'tor_signed_path' => 'public/bmn_rkbmn_tor_esign/tor_41_signed.pdf',
                'tanggal_verifikasi_tor' => '2025-04-10 21:55:54',
                'lampiran_signed_path' => NULL,
                'tanggal_verifikasi_lampiran' => NULL,
                'created_at' => '2025-03-19 07:41:39',
                'updated_at' => '2025-04-10 07:55:54'
            ],
            [
                'id' => 20,
                'kode_jenis_pengajuan' => 'R3-002',
                'id_bagian_pengusul' => 669,
                'id_biro_pengusul' => 664,
                'id_bagian_pelaksana' => 749,
                'id_biro_pelaksana' => 688,
                'program' => 'WA | Program Dukungan Manajemen',
                'kegiatan' => '5784',
                'output' => 'EBA',
                'kode_barang' => '3020199999',
                'status' => 'rejected',
                'tahun_anggaran' => 2027,
                'tanggal_pengajuan' => NULL,
                'tanggal_kebmn' => NULL,
                'tanggal_keperencanaan' => NULL,
                'tanggal_final' => NULL,
                'tujuan_rencana' => NULL,
                'atr_nonatr' => NULL,
                'skema' => 'Pembelian',
                'harga_barang' => 600000000,
                'total_anggaran' => 1200000000,
                'uraian_barang' => 'Uraian Kendaraan Operasional',
                'keterangan' => 'Keterangan Kendaraan Operasional',
                'dokumen_pendukung' => NULL,
                'alasan_pengusul_bmn' => NULL,
                'alasan_koordinator_bmn' => NULL,
                'alasan_perencanaan' => NULL,
                'akun_belanja' => '532111 - Belanja Modal Peralatan dan Mesin',
                'akun_neraca' => '132111 - Peralatan dan Mesin',
                'kuantitas' => 2,
                'tor_signed_path' => 'public/bmn_rkbmn_tor_esign/tor_40_signed.pdf',
                'tanggal_verifikasi_tor' => '2025-04-10 21:12:46',
                'lampiran_signed_path' => NULL,
                'tanggal_verifikasi_lampiran' => NULL,
                'created_at' => '2025-03-18 04:38:28',
                'updated_at' => '2025-04-10 07:12:46'
            ],
        ];

        DB::table('bmn_pengajuanrkbmnbagian')->insert($data);
    }
}
