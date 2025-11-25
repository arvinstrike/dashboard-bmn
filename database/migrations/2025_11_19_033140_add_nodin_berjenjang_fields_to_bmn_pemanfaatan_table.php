<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            // Add fields for Nodin Berjenjang document template
            // Added at the end of table to avoid row size limit
            $table->string('nodin_berjenjang_nomor', 100)->nullable();
            $table->date('nodin_berjenjang_tanggal')->nullable(); // Legacy field - kept for backward compatibility
            $table->date('nodin_berjenjang_tanggal_mulai')->nullable(); // Start date for date range
            $table->date('nodin_berjenjang_tanggal_selesai')->nullable(); // End date for date range
            $table->decimal('nodin_berjenjang_nominal', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            $table->dropColumn([
                'nodin_berjenjang_nomor',
                'nodin_berjenjang_tanggal',
                'nodin_berjenjang_tanggal_mulai',
                'nodin_berjenjang_tanggal_selesai',
                'nodin_berjenjang_nominal'
            ]);
        });
    }
};
