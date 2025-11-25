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
        // Drop columns that were partially created from failed migration attempts
        $columns = [
            'surat_konfirmasi_nomor_perjanjian_lama_dpr',
            'surat_konfirmasi_tanggal_konfirmasi_terakhir',
            'surat_konfirmasi_kasub_nama',
            'surat_konfirmasi_kasub_nomor'
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('bmn_pemanfaatan', $column)) {
                Schema::table('bmn_pemanfaatan', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore - these were partial/failed migrations
    }
};
