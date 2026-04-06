<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $results = DB::select("SHOW CREATE TABLE bmn_pemanfaatan");
    foreach ($results as $row) {
        echo "Table: " . $row->Table . "\n";
        echo "Create Table: " . $row->{'Create Table'} . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
