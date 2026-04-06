<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'BAG001',
                'iddeputi' => 'DEP001',
                'idbiro' => 'BIR001',
                'uraianbagian' => 'Bagian Umum',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 'BAG002',
                'iddeputi' => 'DEP001',
                'idbiro' => 'BIR001',
                'uraianbagian' => 'Bagian Keuangan',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 'BAG003',
                'iddeputi' => 'DEP002',
                'idbiro' => 'BIR002',
                'uraianbagian' => 'Bagian SDM',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 'BAG004',
                'iddeputi' => 'DEP002',
                'idbiro' => 'BIR002',
                'uraianbagian' => 'Bagian IT',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert or ignore to avoid duplicates if run multiple times
        foreach ($data as $item) {
            DB::table('bagian')->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
        }
    }
}
