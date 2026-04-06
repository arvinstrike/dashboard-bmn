<?php

use Illuminate\Support\Facades\DB;

try {
    DB::select('SELECT created_at, updated_at FROM bmn_pemanfaatan LIMIT 1');
    echo "Columns exist\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
