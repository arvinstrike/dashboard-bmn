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
        // Add surat_konfirmasi_tanggal if it doesn't exist
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_tanggal')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->date('surat_konfirmasi_tanggal')->nullable()->after('surat_konfirmasi_nomor');
            });
        }

        // Add surat_konfirmasi_peruntukan_surat if it doesn't exist (different from existing surat_konfirmasi_peruntukan)
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_peruntukan_surat')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_peruntukan_surat')->nullable()->after('surat_konfirmasi_peruntukan');
            });
        }

        // Add surat_konfirmasi_tujuan_surat if it doesn't exist (different from existing surat_konfirmasi_tujuan)
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_tujuan_surat')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_tujuan_surat')->nullable()->after('surat_konfirmasi_tujuan');
            });
        }

        // Add surat_konfirmasi_nomor_perjanjian_lama_dpr if it doesn't exist
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_nomor_perjanjian_lama_dpr')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_nomor_perjanjian_lama_dpr')->nullable()->after('surat_konfirmasi_nomor_perjanjian_lama');
            });
        }

        // Add surat_konfirmasi_nomor_perjanjian_lama_mitra if it doesn't exist
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_nomor_perjanjian_lama_mitra')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_nomor_perjanjian_lama_mitra')->nullable()->after('surat_konfirmasi_nomor_perjanjian_lama_dpr');
            });
        }

        // Add surat_konfirmasi_tanggal_konfirmasi_terakhir if it doesn't exist
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_tanggal_konfirmasi_terakhir')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->date('surat_konfirmasi_tanggal_konfirmasi_terakhir')->nullable()->after('surat_konfirmasi_tanggal_berakhir');
            });
        }

        // Add surat_konfirmasi_kasub_nama if it doesn't exist (separating the old combined field)
        if (!Schema::hasColumn('bmn_pemanfaatan', 'surat_konfirmasi_kasub_nama')) {
            Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
                $table->text('surat_konfirmasi_kasub_nama')->nullable()->after('surat_konfirmasi_kasub_nama_nomor');
            });
        }

        // Add surat_konfirmasi_kasub_nomor if it doesn't exist (separating the old combined field)
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
            $table->dropColumn([
                'surat_konfirmasi_tanggal',
                'surat_konfirmasi_peruntukan_surat',
                'surat_konfirmasi_tujuan_surat',
                'surat_konfirmasi_nomor_perjanjian_lama_dpr',
                'surat_konfirmasi_nomor_perjanjian_lama_mitra',
                'surat_konfirmasi_tanggal_konfirmasi_terakhir',
                'surat_konfirmasi_kasub_nama',
                'surat_konfirmasi_kasub_nomor'
            ]);
        });
    }
};
