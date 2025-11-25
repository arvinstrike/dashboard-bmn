<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = __DIR__ . '/resources/template/surat_konfirmasi_perpanjangan_sewa.docx';
$outputPath = __DIR__ . '/test_output_surat_konfirmasi.docx';

echo "Testing document generation...\n\n";

try {
    // Load template
    echo "1. Loading template...\n";
    $templateProcessor = new TemplateProcessor($templatePath);
    echo "   SUCCESS!\n\n";
    
    // Set values (simulating database data)
    echo "2. Setting placeholder values...\n";
    $placeholders = [
        'NOMOR_SURAT' => 'AAA/222/2002',
        'TANGGAL_SURAT' => '23 November 2025',
        'PERUNTUKAN_SURAT' => 'Perpanjangan Sewa Gedung',
        'TUJUAN_SURAT' => 'Bank Mandiri',
        'NOMOR_PERJANJIAN_SEWA_LAMA_DPR' => 'AAA/19/2/2025',
        'NOMOR_PERJANJIAN_SEWA_LAMA_MITRA' => 'MND/2025/001',
        'TANGGAL_BERAKHIR' => '5 Desember 2025',
        'TANGGAL_KONFIRMASI_TERAKHIR' => '30 November 2025',
        'NAMA_KASUB' => 'Maulana',
        'NOMOR_KASUB' => '08139201224'
    ];
    
    foreach ($placeholders as $key => $value) {
        $templateProcessor->setValue($key, $value);
        echo "   - \${$key} = {$value}\n";
    }
    echo "   SUCCESS!\n\n";
    
    // Save document
    echo "3. Saving document to: {$outputPath}\n";
    $templateProcessor->saveAs($outputPath);
    echo "   SUCCESS!\n\n";
    
    // Verify
    if (file_exists($outputPath)) {
        echo "4. Verification:\n";
        echo "   - File exists: YES\n";
        echo "   - File size: " . filesize($outputPath) . " bytes\n";
        echo "\n✅ DOCUMENT GENERATED SUCCESSFULLY!\n";
        echo "\nYou can open the file at: {$outputPath}\n";
    } else {
        echo "❌ ERROR: Output file not created!\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
