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
        Schema::create('bmn_pengajuanrkbmnbagian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_jenis_pengajuan', 20)->nullable();
            $table->integer('id_bagian_pengusul')->nullable();
            $table->integer('id_biro_pengusul')->nullable();
            $table->integer('id_bagian_pelaksana')->nullable();
            $table->integer('id_biro_pelaksana')->nullable();
            $table->string('program', 255)->nullable();
            $table->string('kegiatan', 255)->nullable();
            $table->string('output', 255)->nullable();
            $table->string('kode_barang', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->integer('tahun_anggaran')->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_kebmn')->nullable();
            $table->date('tanggal_keperencanaan')->nullable();
            $table->date('tanggal_final')->nullable();
            $table->string('tujuan_rencana', 255)->nullable();
            $table->string('atr_nonatr', 50)->nullable();
            $table->string('skema', 50)->nullable();
            $table->double('harga_barang')->nullable();
            $table->double('total_anggaran')->nullable();
            $table->text('uraian_barang')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('dokumen_pendukung')->nullable();
            $table->text('alasan_pengusul_bmn')->nullable();
            $table->text('alasan_koordinator_bmn')->nullable();
            $table->text('alasan_perencanaan')->nullable();
            $table->string('akun_belanja', 255)->nullable();
            $table->string('akun_neraca', 255)->nullable();
            $table->integer('kuantitas')->nullable();
            $table->string('tor_signed_path', 255)->nullable();
            $table->timestamp('tanggal_verifikasi_tor')->nullable();
            $table->string('lampiran_signed_path', 255)->nullable();
            $table->timestamp('tanggal_verifikasi_lampiran')->nullable();
            $table->timestamps();

            // Foreign key constraints
            // Note: Assuming 'bagian' table with 'id' column exists.
            // The SQL file shows these constraints, so we add them here.
            // $table->foreign('id_bagian_pengusul', 'fk_pengajuan_bagian_pengusul')->references('id')->on('bagian');
            // $table->foreign('id_bagian_pelaksana', 'fk_pengajuan_bagian_pelaksana')->references('id')->on('bagian')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmn_pengajuanrkbmnbagian');
    }
};