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
        // First, back up the current structure if it has data
        // Then modify the table to have integer ID instead of VARCHAR
        Schema::table('bagian', function (Blueprint $table) {
            $table->integer('id')->change();
        });
        
        // Set the id column as auto-incrementing primary key
        DB::statement('ALTER TABLE bagian MODIFY id INT AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bagian', function (Blueprint $table) {
            $table->string('id', 11)->change();
        });
    }
};