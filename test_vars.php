<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = __DIR__ . '/resources/template/surat_konfirmasi_perpanjangan_sewa.docx';
$logFile = __DIR__ . '/template_variables.txt';

$log = '';
$log .= "Template Variables Test\n";
$log .= "=======================\n\n";

try {
    $templateProcessor = new TemplateProcessor($templatePath);
    $variables = $templateProcessor->getVariables();
    
    $log .= "Found " . count($variables) . " variables:\n\n";
    foreach ($variables as $var) {
        $log .= $var . "\n";
    }
    
} catch (Exception $e) {
    $log .= "ERROR: " . $e->getMessage() . "\n";
}

file_put_contents($logFile, $log);
echo $log;
