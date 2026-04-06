<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BmnPemanfaatan;
use Illuminate\Support\Facades\Log;

class DebugInsertSeeder extends Seeder
{
    public function run()
    {
        try {
            BmnPemanfaatan::create([
                'pic_penyewa' => 'Test PIC',
                'nomor_hp_pic_penyewa' => '08123456789',
                'pic_administrasi_bmn' => 'Test Admin',
                'nomor_pic_administrasi_bmn' => '08129876543',
                'nama_mitra_penyewa' => 'Test Mitra',
                'jenis_mitra' => 'Perusahaan',
                'jenis_usulan' => 'Usulan Baru',
                'peruntukan_sewa' => 'Test Peruntukan',
                'keterangan_uraian' => 'Test Keterangan',
                'is_complete' => false,
            ]);
            $this->command->info('Insert successful');
        } catch (\Exception $e) {
            $this->command->error('Insert failed: ' . $e->getMessage());
            Log::error('Debug Insert Failed: ' . $e->getMessage());
        }
    }
}
