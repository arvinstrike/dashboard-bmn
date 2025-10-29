<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create a new bagian table with correct structure
        Schema::create('bagian_new', function (Blueprint $table) {
            $table->id(); // This creates an auto-incrementing integer primary key
            $table->integer('iddeputi');
            $table->integer('idbiro');
            $table->string('uraianbagian', 200);
            $table->set('status', ['on', 'off'])->default('on');
            $table->timestamps();
        });
        
        // Copy data from old table to new table if old table exists and has data
        if (Schema::hasTable('bagian')) {
            $oldData = DB::table('bagian')->get();
            foreach ($oldData as $row) {
                // Try to convert the id to integer if it was stored as string
                $newId = is_numeric($row->id) ? (int)$row->id : null;
                if ($newId !== null) {
                    DB::table('bagian_new')->insert([
                        'id' => $newId,
                        'iddeputi' => $row->iddeputi,
                        'idbiro' => $row->idbiro,
                        'uraianbagian' => $row->uraianbagian,
                        'status' => $row->status,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            }
        }
        
        // Drop the old table and rename the new one
        Schema::dropIfExists('bagian');
        Schema::rename('bagian_new', 'bagian');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create a temporary table with old structure
        Schema::create('bagian_old', function (Blueprint $table) {
            $table->string('id', 11); // Original VARCHAR(11) structure
            $table->integer('iddeputi');
            $table->integer('idbiro');
            $table->string('uraianbagian', 200);
            $table->set('status', ['on', 'off']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->primary('id');
        });
        
        // Copy data back to old structure
        $newData = DB::table('bagian')->get();
        foreach ($newData as $row) {
            DB::table('bagian_old')->insert([
                'id' => (string)$row->id, // Convert back to string
                'iddeputi' => $row->iddeputi,
                'idbiro' => $row->idbiro,
                'uraianbagian' => $row->uraianbagian,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
        
        // Drop the new table and rename old one back
        Schema::dropIfExists('bagian');
        Schema::rename('bagian_old', 'bagian');
    }
};
