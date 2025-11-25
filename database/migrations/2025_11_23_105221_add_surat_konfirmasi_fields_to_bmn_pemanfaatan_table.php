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
            // First, convert some existing large VARCHAR fields to TEXT to free up row space
            $table->text('surat_konfirmasi_tujuan')->nullable()->change();
            $table->text('surat_konfirmasi_peruntukan')->nullable()->change();
            $table->text('surat_konfirmasi_nomor_perjanjian_lama')->nullable()->change();
            $table->text('surat_konfirmasi_kasub_nama_nomor')->nullable()->change();
        });

        // Add new fields only if they don't exist
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_nomor_perjanjian_lama_dpr')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_nomor_perjanjian_lama_dpr')->nullable()->after('surat_konfirmasi_nomor_perjanjian_lama');
            });
        }

        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_tanggal_konfirmasi_terakhir')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->date('surat_konfirmasi_tanggal_konfirmasi_terakhir')->nullable()->after('surat_konfirmasi_tanggal_berakhir');
            });
        }

        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_kasub_nama')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_kasub_nama')->nullable()->after('surat_konfirmasi_kasub_nama_nomor');
            });
        }

        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_kasub_nomor')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_kasub_nomor')->nullable()->after('surat_konfirmasi_kasub_nama');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            // Drop the new fields
            $table->dropColumn([
                'surat_konfirmasi_nomor_perjanjian_lama_dpr',
                'surat_konfirmasi_tanggal_konfirmasi_terakhir',
                'surat_konfirmasi_kasub_nama',
                'surat_konfirmasi_kasub_nomor'
            ]);

            // Revert TEXT back to VARCHAR (optional - may keep as TEXT)
            // Note: Reverting to original VARCHAR sizes may cause row size issues again
            // So we'll keep them as TEXT in rollback
        });
    }
};
