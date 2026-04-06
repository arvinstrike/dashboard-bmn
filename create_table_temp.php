<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Dropping table...\n";
    DB::statement('DROP TABLE IF EXISTS perjanjian_sewa');
    
    echo "Creating table with INT pemanfaatan_id...\n";
    DB::statement("
        CREATE TABLE perjanjian_sewa (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            pemanfaatan_id INT NOT NULL,
            logo_penyewa VARCHAR(255) NULL,
            pihak_pertama_nama VARCHAR(255) NULL,
            pihak_pertama_kedudukan VARCHAR(255) NULL,
            pihak_pertama_keputusan_nomor VARCHAR(255) NULL,
            pihak_pertama_keputusan_tahun VARCHAR(255) NULL,
            mitra_penyewa VARCHAR(255) NULL,
            pihak_kedua_nama VARCHAR(255) NULL,
            pihak_kedua_kedudukan VARCHAR(255) NULL,
            pihak_kedua_dasar_hukum TEXT NULL,
            pihak_kedua_keputusan_nomor VARCHAR(255) NULL,
            pihak_kedua_keputusan_tanggal DATE NULL,
            pihak_kedua_atas_nama VARCHAR(255) NULL,
            pihak_kedua_alamat TEXT NULL,
            pihak_kedua_kegiatan_usaha VARCHAR(255) NULL,
            objek_luas DOUBLE NULL,
            objek_satuan_luas VARCHAR(255) DEFAULT 'm2',
            objek_letak TEXT NULL,
            objek_gedung VARCHAR(255) NULL,
            peruntukan VARCHAR(255) NULL,
            nilai_sewa DECIMAL(15, 2) NULL,
            durasi_sewa VARCHAR(255) NULL,
            periode_mulai DATE NULL,
            periode_selesai DATE NULL,
            nomor_surat VARCHAR(255) NULL,
            tanggal_surat DATE NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (pemanfaatan_id) REFERENCES bmn_pemanfaatan(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "Table created successfully with FK.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
