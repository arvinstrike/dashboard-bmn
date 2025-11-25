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
            $table->string('surat_usulan_kpknl_peruntukan')->nullable()->after('surat_usulan_kpknl_isi');
            $table->date('surat_usulan_kpknl_tanggal_berakhir')->nullable()->after('surat_usulan_kpknl_peruntukan');
            $table->string('surat_usulan_kpknl_nama_kasubag')->nullable()->after('surat_usulan_kpknl_tanggal_berakhir');
            $table->string('surat_usulan_kpknl_nomor_kasubag')->nullable()->after('surat_usulan_kpknl_nama_kasubag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            $table->dropColumn([
                'surat_usulan_kpknl_peruntukan',
                'surat_usulan_kpknl_tanggal_berakhir',
                'surat_usulan_kpknl_nama_kasubag',
                'surat_usulan_kpknl_nomor_kasubag'
            ]);
        });
    }
};
