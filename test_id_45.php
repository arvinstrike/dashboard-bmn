<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BmnPemanfaatan;

$output = '';

// Get record ID 45 (Bank Mandiri)
$utilization = BmnPemanfaatan::find(45);

if (!$utilization) {
    $output .= "Record ID 45 not found!\n";
    file_put_contents(__DIR__ . '/test_id_45.txt', $output);
    echo $output;
    exit;
}

$output .= "Testing Record ID 45: {$utilization->nama_mitra_penyewa}\n";
$output .= "==========================================\n\n";

$dateFields = [
    'surat_konfirmasi_tanggal',
    'surat_konfirmasi_tanggal_berakhir',
    'surat_konfirmasi_tanggal_konfirmasi_terakhir'
];

foreach ($dateFields as $field) {
    $value = $utilization->$field;
    
    $output .= "Field: {$field}\n";
    
    if (is_null($value)) {
        $output .= "  Value: NULL\n";
    } else {
        $output .= "  Type: " . gettype($value) . "\n";
        $output .= "  Class: " . get_class($value) . "\n";
        $output .= "  Is Carbon: " . ($value instanceof \Carbon\Carbon ? 'YES' : 'NO') . "\n";
        
        if ($value instanceof \Carbon\Carbon) {
            $output .= "  Formatted (Y-m-d): " . $value->format('Y-m-d') . "\n";
        }
        
        // Test blade expression
        $bladeResult = $value instanceof \Carbon\Carbon ? $value->format('Y-m-d') : ($value ?? '');
        $output .= "  Blade expression result: '{$bladeResult}'\n";
    }
    $output .= "\n";
}

file_put_contents(__DIR__ . '/test_id_45.txt', $output);
echo $output;
