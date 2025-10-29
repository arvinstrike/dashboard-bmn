<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bagian')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert sample bagian data
        DB::table('bagian')->insert([
            [
                'id' => 1,
                'iddeputi' => 1,
                'idbiro' => 101,
                'uraianbagian' => 'Bagian Umum',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'iddeputi' => 1,
                'idbiro' => 102,
                'uraianbagian' => 'Bagian Keuangan',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'iddeputi' => 1,
                'idbiro' => 103,
                'uraianbagian' => 'Bagian Teknis',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'iddeputi' => 1,
                'idbiro' => 104,
                'uraianbagian' => 'Bagian Administrasi',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'iddeputi' => 1,
                'idbiro' => 105,
                'uraianbagian' => 'Bagian Perencanaan',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 20,
                'iddeputi' => 2,
                'idbiro' => 201,
                'uraianbagian' => 'Bagian Pelaksana 1',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 21,
                'iddeputi' => 2,
                'idbiro' => 202,
                'uraianbagian' => 'Bagian Pelaksana 2',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 22,
                'iddeputi' => 2,
                'idbiro' => 203,
                'uraianbagian' => 'Bagian Pelaksana 3',
                'status' => 'on',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}