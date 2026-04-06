<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Illuminate\Support\Facades\Schema::getColumnListing('bmn_pemanfaatan');
$targets = [
    'surat_permohonan_ttd_nomor',
    'nodin_internal_nomor_berjenjang_1',
    'surat_penyampaian_perjanjian_nomor'
];

foreach ($targets as $target) {
    echo $target . ": " . (in_array($target, $columns) ? "EXISTS" : "MISSING") . "\n";
}
