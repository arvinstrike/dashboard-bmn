<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BmnPemanfaatan;

$output = '';

// Get all records with surat_konfirmasi_nomor
$utilizations = BmnPemanfaatan::whereNotNull('surat_konfirmasi_nomor')->get();

$output .= "Found {$utilizations->count()} records with surat_konfirmasi_nomor\n";
$output .= "==========================================\n\n";

foreach ($utilizations as $util) {
    $output .= "ID: {$util->id} - {$util->nama_mitra_penyewa}\n";
    $output .= "  surat_konfirmasi_nomor: " . ($util->surat_konfirmasi_nomor ?? 'NULL') . "\n";
    $output .= "  surat_konfirmasi_tanggal: " . ($util->surat_konfirmasi_tanggal ? $util->surat_konfirmasi_tanggal->format('Y-m-d') : 'NULL') . "\n";
    $output .= "  surat_konfirmasi_tanggal_berakhir: " . ($util->surat_konfirmasi_tanggal_berakhir ? $util->surat_konfirmasi_tanggal_berakhir->format('Y-m-d') : 'NULL') . "\n";
    $output .= "  surat_konfirmasi_tanggal_konfirmasi_terakhir: " . ($util->surat_konfirmasi_tanggal_konfirmasi_terakhir ? $util->surat_konfirmasi_tanggal_konfirmasi_terakhir->format('Y-m-d') : 'NULL') . "\n";
    $output .= "\n";
}

file_put_contents(__DIR__ . '/all_surat_konfirmasi.txt', $output);
echo $output;
