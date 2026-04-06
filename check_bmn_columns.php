<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$type = DB::select("SHOW COLUMNS FROM bmn_pemanfaatan WHERE Field = 'id'");
echo "Type: " . $type[0]->Type . "\n";
echo "Null: " . $type[0]->Null . "\n";
echo "Key: " . $type[0]->Key . "\n";
echo "Extra: " . $type[0]->Extra . "\n";
