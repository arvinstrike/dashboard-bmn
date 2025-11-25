<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BmnPemanfaatan;

$output = '';

// Find record with PIC Penyewa = Nurdin
$utilization = BmnPemanfaatan::where('pic_penyewa', 'like', '%Nurdin%')->first();

if (!$utilization) {
    $output .= "Record dengan PIC Penyewa 'Nurdin' tidak ditemukan!\n";
    $output .= "\nSemua PIC Penyewa:\n";
    $all = BmnPemanfaatan::select('id', 'pic_penyewa', 'nama_mitra_penyewa')->get();
    foreach ($all as $item) {
        $output .= "  ID {$item->id}: {$item->pic_penyewa} - {$item->nama_mitra_penyewa}\n";
    }
} else {
    $output .= "RECORD DITEMUKAN!\n";
    $output .= "==========================================\n";
    $output .= "ID: {$utilization->id}\n";
    $output .= "PIC Penyewa: {$utilization->pic_penyewa}\n";
    $output .= "Nama Mitra: {$utilization->nama_mitra_penyewa}\n";
    $output .= "\n";
    
    $output .= "SURAT KONFIRMASI FIELDS:\n";
    $output .= "==========================================\n";
    
    // All surat konfirmasi fields
    $fields = [
        'surat_konfirmasi_nomor',
        'surat_konfirmasi_tanggal',
        'surat_konfirmasi_tujuan',
        'surat_konfirmasi_tujuan_surat',
        'surat_konfirmasi_peruntukan',
        'surat_konfirmasi_peruntukan_surat',
        'surat_konfirmasi_nomor_perjanjian_lama',
        'surat_konfirmasi_nomor_perjanjian_lama_dpr',
        'surat_konfirmasi_nomor_perjanjian_lama_mitra',
        'surat_konfirmasi_tanggal_berakhir',
        'surat_konfirmasi_tanggal_konfirmasi_terakhir',
        'surat_konfirmasi_kasub_nama',
        'surat_konfirmasi_kasub_nomor',
    ];
    
    foreach ($fields as $field) {
        $value = $utilization->$field;
        
        if (is_null($value)) {
            $output .= "{$field}: NULL\n";
        } else if ($value instanceof \Carbon\Carbon) {
            $output .= "{$field}: {$value->format('Y-m-d')} (Carbon)\n";
        } else {
            $output .= "{$field}: '{$value}'\n";
        }
    }
    
    $output .= "\n";
    $output .= "RAW ATTRIBUTES (from database):\n";
    $output .= "==========================================\n";
    $output .= "surat_konfirmasi_tanggal (raw): " . var_export($utilization->getAttributes()['surat_konfirmasi_tanggal'] ?? null, true) . "\n";
    $output .= "surat_konfirmasi_tanggal_berakhir (raw): " . var_export($utilization->getAttributes()['surat_konfirmasi_tanggal_berakhir'] ?? null, true) . "\n";
    $output .= "surat_konfirmasi_tanggal_konfirmasi_terakhir (raw): " . var_export($utilization->getAttributes()['surat_konfirmasi_tanggal_konfirmasi_terakhir'] ?? null, true) . "\n";
}

file_put_contents(__DIR__ . '/check_nurdin.txt', $output);
echo $output;
