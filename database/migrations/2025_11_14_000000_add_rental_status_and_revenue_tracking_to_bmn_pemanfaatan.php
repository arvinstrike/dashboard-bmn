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
            // Status Sewa
            $table->enum('status_sewa', [
                'draft',        // Baru dibuat, belum lengkap
                'review',       // Dalam review/proses persetujuan
                'approved',     // Disetujui, menunggu pembayaran
                'active',       // Sewa sedang berlangsung
                'completed',    // Sewa selesai
                'cancelled',    // Dibatalkan
                'expired'       // Kadaluarsa (tidak diperpanjang)
            ])->default('draft')->after('is_complete');

            // Revenue Tracking - Detail pembayaran per periode
            $table->decimal('total_pendapatan_terealisasi', 18, 2)->default(0)->after('status_sewa')
                ->comment('Total pendapatan yang sudah dibayar');

            $table->decimal('total_pendapatan_outstanding', 18, 2)->default(0)->after('total_pendapatan_terealisasi')
                ->comment('Total pendapatan yang belum dibayar (invoice terbit)');

            $table->integer('periode_pembayaran_ke')->default(0)->after('total_pendapatan_outstanding')
                ->comment('Periode pembayaran ke berapa saat ini');

            $table->integer('total_periode_pembayaran')->nullable()->after('periode_pembayaran_ke')
                ->comment('Total periode pembayaran yang harus dilakukan');

            // Tracking tanggal penting untuk status aktif
            $table->date('tanggal_aktivasi')->nullable()->after('total_periode_pembayaran')
                ->comment('Tanggal sewa mulai aktif/berlangsung');

            $table->date('tanggal_penyelesaian')->nullable()->after('tanggal_aktivasi')
                ->comment('Tanggal sewa selesai (actual)');

            // Informasi perpanjangan
            $table->boolean('dapat_diperpanjang')->default(true)->after('tanggal_penyelesaian');
            $table->date('batas_perpanjangan')->nullable()->after('dapat_diperpanjang')
                ->comment('Batas waktu untuk mengajukan perpanjangan');

            $table->integer('kali_perpanjangan')->default(0)->after('batas_perpanjangan')
                ->comment('Sudah diperpanjang berapa kali');

            // Notes untuk tracking
            $table->text('catatan_pembayaran')->nullable()->after('kali_perpanjangan');
            $table->text('catatan_status')->nullable()->after('catatan_pembayaran');

            // Timestamps untuk audit trail
            $table->timestamp('approved_at')->nullable()->after('catatan_status');
            $table->timestamp('activated_at')->nullable()->after('approved_at');
            $table->timestamp('completed_at')->nullable()->after('activated_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');

            $table->string('cancelled_by')->nullable()->after('cancelled_at');
            $table->text('cancelled_reason')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            $table->dropColumn([
                'status_sewa',
                'total_pendapatan_terealisasi',
                'total_pendapatan_outstanding',
                'periode_pembayaran_ke',
                'total_periode_pembayaran',
                'tanggal_aktivasi',
                'tanggal_penyelesaian',
                'dapat_diperpanjang',
                'batas_perpanjangan',
                'kali_perpanjangan',
                'catatan_pembayaran',
                'catatan_status',
                'approved_at',
                'activated_at',
                'completed_at',
                'cancelled_at',
                'cancelled_by',
                'cancelled_reason'
            ]);
        });
    }
};
