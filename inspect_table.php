<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $result = DB::select("SHOW CREATE TABLE bmn_pemanfaatan");
    // The result is an array of objects. The key for the create statement depends on the driver but usually 'Create Table'.
    foreach ($result as $row) {
        print_r($row);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
