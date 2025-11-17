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
        Schema::create('bmn_pemanfaatan_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->integer('pemanfaatan_id')->comment('FK ke bmn_pemanfaatan');

            // Detail Periode
            $table->integer('periode_ke')->comment('Periode pembayaran ke berapa (1, 2, 3, dst)');
            $table->string('periode_nama')->comment('Nama periode (Januari 2025, Q1 2025, dst)');
            $table->date('tanggal_mulai_periode')->comment('Tanggal mulai periode ini');
            $table->date('tanggal_akhir_periode')->comment('Tanggal akhir periode ini');

            // Invoice & Billing
            $table->string('nomor_invoice')->nullable();
            $table->date('tanggal_invoice')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('jumlah_tagihan', 18, 2)->comment('Nominal tagihan periode ini');

            // Pembayaran
            $table->enum('status_pembayaran', [
                'pending',      // Belum dibayar
                'partial',      // Dibayar sebagian
                'paid',         // Lunas
                'overdue',      // Terlambat
                'cancelled'     // Dibatalkan
            ])->default('pending');

            $table->decimal('jumlah_dibayar', 18, 2)->default(0);
            $table->decimal('sisa_tagihan', 18, 2)->default(0);

            $table->string('ntpn')->nullable()->comment('Nomor Transaksi Penerimaan Negara');
            $table->date('tanggal_bayar')->nullable();
            $table->text('dokumen_bukti_bayar')->nullable();
            $table->text('kode_billing')->nullable();

            // Denda (jika terlambat)
            $table->decimal('denda', 18, 2)->default(0);
            $table->integer('hari_terlambat')->default(0);

            // Notes
            $table->text('catatan')->nullable();

            // Audit
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('pemanfaatan_id');
            $table->index('status_pembayaran');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmn_pemanfaatan_pembayaran');
    }
};
