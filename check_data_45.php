<?php

use App\Models\BmnPemanfaatan;
use App\Models\SuratPenyampaianPerjanjian;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 45;
$utilization = BmnPemanfaatan::find($id);

if (!$utilization) {
    echo "Utilization ID $id not found.\n";
    exit;
}

echo "Utilization ID: " . $utilization->id . "\n";

$surat = SuratPenyampaianPerjanjian::where('bmn_pemanfaatan_id', $id)->first();

if ($surat) {
    echo "Surat Penyampaian Perjanjian FOUND.\n";
    echo "ID: " . $surat->id . "\n";
    echo "Nomor Surat: " . $surat->nomor_surat . "\n";
} else {
    echo "Surat Penyampaian Perjanjian NOT FOUND.\n";
}

$relation = $utilization->suratPenyampaianPerjanjian;
if ($relation) {
    echo "Relationship loaded successfully.\n";
} else {
    echo "Relationship returned NULL.\n";
}
