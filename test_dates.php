<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BmnPemanfaatan;

$output = '';

// Get first record with surat_konfirmasi data
$utilization = BmnPemanfaatan::whereNotNull('surat_konfirmasi_nomor')->first();

if (!$utilization) {
    $output .= "No records with surat_konfirmasi found!\n";
    file_put_contents(__DIR__ . '/date_test_result.txt', $output);
    echo $output;
    exit;
}

$output .= "Testing Date Fields for Utilization ID: {$utilization->id}\n";
$output .= "Nama Mitra: {$utilization->nama_mitra_penyewa}\n";
$output .= "==========================================\n\n";

$dateFields = [
    'surat_konfirmasi_tanggal',
    'surat_konfirmasi_tanggal_berakhir',
    'surat_konfirmasi_tanggal_konfirmasi_terakhir'
];

foreach ($dateFields as $field) {
    $value = $utilization->$field;
    
    $output .= "Field: {$field}\n";
    $output .= "  Value is null: " . (is_null($value) ? 'YES' : 'NO') . "\n";
    
    if (!is_null($value)) {
        $output .= "  Type: " . gettype($value) . "\n";
        $output .= "  Class: " . (is_object($value) ? get_class($value) : 'N/A') . "\n";
        
        if ($value instanceof \Carbon\Carbon) {
            $output .= "  Is Carbon: YES\n";
            $output .= "  Formatted (Y-m-d): " . $value->format('Y-m-d') . "\n";
            $output .= "  Formatted (d M Y): " . $value->format('d M Y') . "\n";
        } else if (is_string($value)) {
            $output .= "  Is String: YES\n";
            $output .= "  String value: '{$value}'\n";
        }
    }
    $output .= "\n";
}

// Test the blade expression
$output .= "Testing Blade Expression:\n";
$output .= "==========================================\n";
$tanggal = $utilization->surat_konfirmasi_tanggal;
$result = $tanggal instanceof \Carbon\Carbon ? $tanggal->format('Y-m-d') : ($tanggal ?? '');
$output .= "Expression result: '{$result}'\n";

// Write to file
file_put_contents(__DIR__ . '/date_test_result.txt', $output);
echo $output;
