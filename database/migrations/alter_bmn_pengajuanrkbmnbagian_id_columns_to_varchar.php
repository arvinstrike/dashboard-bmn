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
        Schema::table('bmn_pengajuanrkbmnbagian', function (Blueprint $table) {
            // Ubah tipe data kolom-kolom ID menjadi varchar(11) untuk menyelaraskan dengan tabel bagian
            $table->string('id_bagian_pengusul', 11)->nullable()->change();
            $table->string('id_biro_pengusul', 11)->nullable()->change();
            $table->string('id_bagian_pelaksana', 11)->nullable()->change();
            $table->string('id_biro_pelaksana', 11)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pengajuanrkbmnbagian', function (Blueprint $table) {
            // Kembalikan tipe data kolom-kolom ID ke integer
            $table->integer('id_bagian_pengusul')->nullable()->change();
            $table->integer('id_biro_pengusul')->nullable()->change();
            $table->integer('id_bagian_pelaksana')->nullable()->change();
            $table->integer('id_biro_pelaksana')->nullable()->change();
        });
    }
};