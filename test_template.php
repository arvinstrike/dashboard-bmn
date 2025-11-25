<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = __DIR__ . '/resources/template/surat_konfirmasi_perpanjangan_sewa.docx';
$logFile = __DIR__ . '/storage/logs/template_test.log';

$log = '';
$log .= "Template Test - " . date('Y-m-d H:i:s') . "\n";
$log .= "===========================================\n\n";
$log .= "Template path: {$templatePath}\n";
$log .= "File exists: " . (file_exists($templatePath) ? 'YES' : 'NO') . "\n";
$log .= "File size: " . filesize($templatePath) . " bytes\n\n";

try {
    $log .= "Loading template...\n";
    $templateProcessor = new TemplateProcessor($templatePath);
    $log .= "SUCCESS! TemplateProcessor created.\n\n";
    
    // Get variables in template
    $variables = $templateProcessor->getVariables();
    $log .= "Variables found in template (" . count($variables) . "):\n";
    foreach ($variables as $var) {
        $log .= "  - \${" . $var . "}\n";
    }
    
    $log .= "\n";
    
} catch (Exception $e) {
    $log .= "ERROR: " . $e->getMessage() . "\n";
    $log .= "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

file_put_contents($logFile, $log);
echo "Log written to: {$logFile}\n";
echo $log;
