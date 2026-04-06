<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("DROP TABLE IF EXISTS perjanjian_sewa");
    
    $sql = "CREATE TABLE perjanjian_sewa (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, pemanfaatan_id BIGINT UNSIGNED NOT NULL, logo_penyewa VARCHAR(255) NULL, pihak_pertama_nama VARCHAR(255) NULL, pihak_pertama_kedudukan VARCHAR(255) NULL, pihak_pertama_keputusan_nomor VARCHAR(255) NULL, pihak_pertama_keputusan_tahun VARCHAR(255) NULL, mitra_penyewa VARCHAR(255) NULL, pihak_kedua_nama VARCHAR(255) NULL, pihak_kedua_kedudukan VARCHAR(255) NULL, pihak_kedua_dasar_hukum TEXT NULL, pihak_kedua_keputusan_nomor VARCHAR(255) NULL, pihak_kedua_keputusan_tanggal DATE NULL, pihak_kedua_atas_nama VARCHAR(255) NULL, pihak_kedua_alamat TEXT NULL, pihak_kedua_kegiatan_usaha VARCHAR(255) NULL, objek_luas DOUBLE NULL, objek_satuan_luas VARCHAR(255) DEFAULT 'm2', objek_letak TEXT NULL, objek_gedung VARCHAR(255) NULL, peruntukan VARCHAR(255) NULL, nilai_sewa DECIMAL(15, 2) NULL, durasi_sewa VARCHAR(255) NULL, periode_mulai DATE NULL, periode_selesai DATE NULL, nomor_surat VARCHAR(255) NULL, tanggal_surat DATE NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL) ENGINE=InnoDB";
    
    DB::statement($sql);
    
    echo "Table created successfully!\n";
    
    // Add foreign key
    DB::statement("ALTER TABLE perjanjian_sewa ADD CONSTRAINT fk_perjanjian_pemanfaatan FOREIGN KEY (pemanfaatan_id) REFERENCES bmn_pemanfaatan(id) ON DELETE CASCADE");
    
    echo "Foreign key added successfully!\n";
    
    // Mark migration as run
    DB::table('migrations')->insert([
        'migration' => '2025_11_26_081525_recreate_perjanjian_sewa_table',
        'batch' => DB::table('migrations')->max('batch') + 1
    ]);
    
    echo "Migration marked as complete!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Full trace: " . $e->getTraceAsString() . "\n";
}
