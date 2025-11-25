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
            // Add date range fields for Nodin Berjenjang
            $table->date('nodin_berjenjang_tanggal_mulai')->nullable()->after('nodin_berjenjang_tanggal');
            $table->date('nodin_berjenjang_tanggal_selesai')->nullable()->after('nodin_berjenjang_tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            // Remove date range fields
            $table->dropColumn(['nodin_berjenjang_tanggal_mulai', 'nodin_berjenjang_tanggal_selesai']);
        });
    }
};
