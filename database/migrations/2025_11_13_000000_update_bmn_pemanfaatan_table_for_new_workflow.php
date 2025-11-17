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
            // Tahap 1: Informasi Penyewa - New fields based on prompt.md
            $table->string('pic_penyewa')->nullable()->after('pic');
            $table->string('nomor_hp_pic_penyewa')->nullable()->after('pic_penyewa');
            $table->string('pic_administrasi_bmn')->nullable()->after('nomor_hp_pic_penyewa');
            $table->string('nomor_pic_administrasi_bmn')->nullable()->after('pic_administrasi_bmn');
            $table->string('nama_mitra_penyewa')->nullable()->after('nomor_pic_administrasi_bmn');
            $table->enum('jenis_mitra', ['Perusahaan', 'Yayasan', 'Koperasi', 'Perseorangan'])->nullable()->after('nama_mitra_penyewa');
            $table->enum('jenis_usulan', ['Perpanjangan', 'Usulan Baru'])->nullable()->after('jenis_mitra');
            $table->text('peruntukan_sewa')->nullable()->after('jenis_usulan');
            $table->text('keterangan_uraian')->nullable()->after('peruntukan_sewa');

            // Tab 2: Konfirmasi - Nodin Konfirmasi Perpanjangan Sewa
            $table->string('nodin_konfirmasi_nomor')->nullable()->after('keterangan_uraian');
            $table->date('nodin_konfirmasi_tanggal')->nullable()->after('nodin_konfirmasi_nomor');
            $table->string('nodin_konfirmasi_mitra_peruntukan')->nullable()->after('nodin_konfirmasi_tanggal');
            $table->date('nodin_konfirmasi_tanggal_berakhir_sewa')->nullable()->after('nodin_konfirmasi_mitra_peruntukan');

            // Tab 2: Surat Konfirmasi Perpanjangan Sewa
            $table->string('surat_konfirmasi_nomor')->nullable()->after('nodin_konfirmasi_tanggal_berakhir_sewa');
            $table->string('surat_konfirmasi_tujuan')->nullable()->after('surat_konfirmasi_nomor');
            $table->string('surat_konfirmasi_peruntukan')->nullable()->after('surat_konfirmasi_tujuan');
            $table->string('surat_konfirmasi_nomor_perjanjian_lama')->nullable()->after('surat_konfirmasi_peruntukan');
            $table->date('surat_konfirmasi_tanggal_berakhir')->nullable()->after('surat_konfirmasi_nomor_perjanjian_lama');
            $table->string('surat_konfirmasi_kasub_nama_nomor')->nullable()->after('surat_konfirmasi_tanggal_berakhir');
            $table->text('surat_konfirmasi_lampiran')->nullable()->after('surat_konfirmasi_kasub_nama_nomor');

            // Tab 2: Dokumen Pendukung
            $table->text('dokumen_surat_usulan_sewa')->nullable()->after('surat_konfirmasi_lampiran');
            $table->text('dokumen_npwp')->nullable()->after('dokumen_surat_usulan_sewa');
            $table->text('dokumen_ktp_penandatangan')->nullable()->after('dokumen_npwp');
            $table->text('dokumen_nib')->nullable()->after('dokumen_ktp_penandatangan');

            // Tab 3: Usulan Pemanfaatan - Nodin Berjenjang
            $table->string('nodin_berjenjang_mitra')->nullable()->after('dokumen_nib');
            $table->string('nodin_berjenjang_peruntukan')->nullable()->after('nodin_berjenjang_mitra');

            // Tab 3: Surat Usulan Sewa KPKNL
            $table->string('surat_usulan_kpknl_nomor')->nullable()->after('nodin_berjenjang_peruntukan');
            $table->date('surat_usulan_kpknl_tanggal')->nullable()->after('surat_usulan_kpknl_nomor');
            $table->string('surat_usulan_kpknl_hal')->nullable()->after('surat_usulan_kpknl_tanggal');
            $table->string('surat_usulan_kpknl_tujuan')->nullable()->after('surat_usulan_kpknl_hal');
            $table->text('surat_usulan_kpknl_isi')->nullable()->after('surat_usulan_kpknl_tujuan');

            // Tab 3: SPTJM
            $table->string('sptjm_nomor')->nullable()->after('surat_usulan_kpknl_isi');
            $table->date('sptjm_tanggal')->nullable()->after('sptjm_nomor');
            $table->string('sptjm_kode_barang')->nullable()->after('sptjm_tanggal');
            $table->string('sptjm_nup')->nullable()->after('sptjm_kode_barang');
            $table->string('sptjm_luasan_sewa')->nullable()->after('sptjm_nup');
            $table->string('sptjm_lokasi_sewa')->nullable()->after('sptjm_luasan_sewa');

            // Tab 3: Surat Pernyataan
            $table->string('surat_pernyataan_nomor')->nullable()->after('sptjm_lokasi_sewa');
            $table->date('surat_pernyataan_tanggal')->nullable()->after('surat_pernyataan_nomor');
            $table->string('surat_pernyataan_kode_barang')->nullable()->after('surat_pernyataan_tanggal');
            $table->string('surat_pernyataan_nup')->nullable()->after('surat_pernyataan_kode_barang');
            $table->string('surat_pernyataan_luasan_sewa')->nullable()->after('surat_pernyataan_nup');
            $table->string('surat_pernyataan_lokasi_sewa')->nullable()->after('surat_pernyataan_luasan_sewa');

            // Tab 3: Daftar BMN (stored as JSON)
            $table->json('daftar_bmn')->nullable()->after('surat_pernyataan_lokasi_sewa');

            // Tab 3: Dokumen Usulan
            $table->text('dokumen_psp')->nullable()->after('daftar_bmn');
            $table->text('dokumen_kib')->nullable()->after('dokumen_psp');
            $table->text('dokumen_usulan_ttd')->nullable()->after('dokumen_kib');

            // Tab 4: Penilaian KPKNL - Dokumen Penilaian
            $table->text('dokumen_jadwal_penilaian')->nullable()->after('dokumen_usulan_ttd');
            $table->text('dokumen_basl')->nullable()->after('dokumen_jadwal_penilaian');
            $table->text('dokumen_persetujuan_kpknl')->nullable()->after('dokumen_basl');

            // Tab 4: Nodin Penyampaian Surat Persetujuan KPKNL
            $table->string('nodin_persetujuan_kpknl_nomor')->nullable()->after('dokumen_persetujuan_kpknl');
            $table->date('nodin_persetujuan_kpknl_tanggal')->nullable()->after('nodin_persetujuan_kpknl_nomor');
            $table->string('nodin_persetujuan_kpknl_tujuan')->nullable()->after('nodin_persetujuan_kpknl_tanggal');
            $table->string('nodin_persetujuan_kpknl_nomor_persetujuan')->nullable()->after('nodin_persetujuan_kpknl_tujuan');
            $table->date('nodin_persetujuan_kpknl_tanggal_persetujuan')->nullable()->after('nodin_persetujuan_kpknl_nomor_persetujuan');
            $table->string('nodin_persetujuan_kpknl_periode_sewa')->nullable()->after('nodin_persetujuan_kpknl_tanggal_persetujuan');
            $table->decimal('nodin_persetujuan_kpknl_nominal', 18, 2)->nullable()->after('nodin_persetujuan_kpknl_periode_sewa');
            $table->string('nodin_persetujuan_kpknl_mitra')->nullable()->after('nodin_persetujuan_kpknl_nominal');
            $table->string('nodin_persetujuan_kpknl_kasub')->nullable()->after('nodin_persetujuan_kpknl_mitra');

            // Tab 4: Surat Penyampaian Invoice
            $table->string('surat_invoice_nomor')->nullable()->after('nodin_persetujuan_kpknl_kasub');
            $table->date('surat_invoice_tanggal')->nullable()->after('surat_invoice_nomor');
            $table->string('surat_invoice_tujuan')->nullable()->after('surat_invoice_tanggal');
            $table->string('surat_invoice_nomor_persetujuan')->nullable()->after('surat_invoice_tujuan');
            $table->date('surat_invoice_tanggal_persetujuan')->nullable()->after('surat_invoice_nomor_persetujuan');
            $table->string('surat_invoice_periode_sewa')->nullable()->after('surat_invoice_tanggal_persetujuan');
            $table->decimal('surat_invoice_nominal', 18, 2)->nullable()->after('surat_invoice_periode_sewa');
            $table->string('surat_invoice_mitra')->nullable()->after('surat_invoice_nominal');
            $table->string('surat_invoice_kasub')->nullable()->after('surat_invoice_mitra');

            // Tab 4: Kode Billing
            $table->text('dokumen_kode_billing')->nullable()->after('surat_invoice_kasub');

            // Tab 5: Perjanjian - Dokumen Final
            $table->text('dokumen_bukti_bayar')->nullable()->after('dokumen_kode_billing');
            // dokumen_perjanjian already exists

            // Tab 5: Detail Perjanjian
            $table->text('perjanjian_logo_penyewa')->nullable()->after('dokumen_bukti_bayar');
            $table->string('perjanjian_mitra')->nullable()->after('perjanjian_logo_penyewa');
            $table->string('perjanjian_peruntukan')->nullable()->after('perjanjian_mitra');
            $table->string('perjanjian_gedung')->nullable()->after('perjanjian_peruntukan');
            $table->string('perjanjian_hari_tanggal')->nullable()->after('perjanjian_gedung');
            $table->text('perjanjian_detail_pihak_kedua')->nullable()->after('perjanjian_hari_tanggal');

            // Note: perjanjian_nomor, perjanjian_tanggal_penandatanganan, jangka_waktu_nilai,
            // jangka_waktu_satuan, and dokumen_perjanjian already exist from original table

            // Tab 5: Nodin Permohonan Ttd
            $table->string('nodin_ttd_nomor')->nullable()->after('perjanjian_detail_pihak_kedua');
            $table->date('nodin_ttd_tanggal')->nullable()->after('nodin_ttd_nomor');
            $table->string('nodin_ttd_tujuan')->nullable()->after('nodin_ttd_tanggal');
            $table->string('nodin_ttd_mitra')->nullable()->after('nodin_ttd_tujuan');
            $table->string('nodin_ttd_judul_perjanjian')->nullable()->after('nodin_ttd_mitra');

            // Tab 5: Nodin Berjenjang Internal
            $table->string('nodin_internal_nomor')->nullable()->after('nodin_ttd_judul_perjanjian');
            $table->date('nodin_internal_tanggal')->nullable()->after('nodin_internal_nomor');
            $table->string('nodin_internal_mitra')->nullable()->after('nodin_internal_tanggal');
            $table->string('nodin_internal_judul_perjanjian')->nullable()->after('nodin_internal_mitra');
            $table->string('nodin_internal_nomor_perjanjian')->nullable()->after('nodin_internal_judul_perjanjian');
            $table->text('nodin_internal_detail_persetujuan')->nullable()->after('nodin_internal_nomor_perjanjian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bmn_pemanfaatan', function (Blueprint $table) {
            // Drop all new columns
            $table->dropColumn([
                'pic_penyewa', 'nomor_hp_pic_penyewa', 'pic_administrasi_bmn', 'nomor_pic_administrasi_bmn',
                'nama_mitra_penyewa', 'jenis_mitra', 'jenis_usulan', 'peruntukan_sewa', 'keterangan_uraian',
                'nodin_konfirmasi_nomor', 'nodin_konfirmasi_tanggal', 'nodin_konfirmasi_mitra_peruntukan',
                'nodin_konfirmasi_tanggal_berakhir_sewa',
                'surat_konfirmasi_nomor', 'surat_konfirmasi_tujuan', 'surat_konfirmasi_peruntukan',
                'surat_konfirmasi_nomor_perjanjian_lama', 'surat_konfirmasi_tanggal_berakhir',
                'surat_konfirmasi_kasub_nama_nomor', 'surat_konfirmasi_lampiran',
                'dokumen_surat_usulan_sewa', 'dokumen_npwp', 'dokumen_ktp_penandatangan', 'dokumen_nib',
                'nodin_berjenjang_mitra', 'nodin_berjenjang_peruntukan',
                'surat_usulan_kpknl_nomor', 'surat_usulan_kpknl_tanggal', 'surat_usulan_kpknl_hal',
                'surat_usulan_kpknl_tujuan', 'surat_usulan_kpknl_isi',
                'sptjm_nomor', 'sptjm_tanggal', 'sptjm_kode_barang', 'sptjm_nup', 'sptjm_luasan_sewa', 'sptjm_lokasi_sewa',
                'surat_pernyataan_nomor', 'surat_pernyataan_tanggal', 'surat_pernyataan_kode_barang',
                'surat_pernyataan_nup', 'surat_pernyataan_luasan_sewa', 'surat_pernyataan_lokasi_sewa',
                'daftar_bmn', 'dokumen_psp', 'dokumen_kib', 'dokumen_usulan_ttd',
                'dokumen_jadwal_penilaian', 'dokumen_basl', 'dokumen_persetujuan_kpknl',
                'nodin_persetujuan_kpknl_nomor', 'nodin_persetujuan_kpknl_tanggal', 'nodin_persetujuan_kpknl_tujuan',
                'nodin_persetujuan_kpknl_nomor_persetujuan', 'nodin_persetujuan_kpknl_tanggal_persetujuan',
                'nodin_persetujuan_kpknl_periode_sewa', 'nodin_persetujuan_kpknl_nominal',
                'nodin_persetujuan_kpknl_mitra', 'nodin_persetujuan_kpknl_kasub',
                'surat_invoice_nomor', 'surat_invoice_tanggal', 'surat_invoice_tujuan',
                'surat_invoice_nomor_persetujuan', 'surat_invoice_tanggal_persetujuan',
                'surat_invoice_periode_sewa', 'surat_invoice_nominal', 'surat_invoice_mitra', 'surat_invoice_kasub',
                'dokumen_kode_billing', 'dokumen_bukti_bayar',
                'perjanjian_logo_penyewa', 'perjanjian_mitra', 'perjanjian_peruntukan', 'perjanjian_gedung',
                'perjanjian_hari_tanggal', 'perjanjian_detail_pihak_kedua',
                'nodin_ttd_nomor', 'nodin_ttd_tanggal', 'nodin_ttd_tujuan', 'nodin_ttd_mitra', 'nodin_ttd_judul_perjanjian',
                'nodin_internal_nomor', 'nodin_internal_tanggal', 'nodin_internal_mitra',
                'nodin_internal_judul_perjanjian', 'nodin_internal_nomor_perjanjian', 'nodin_internal_detail_persetujuan'
            ]);
        });
    }
};
