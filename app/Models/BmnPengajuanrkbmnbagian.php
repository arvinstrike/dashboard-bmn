<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BmnPengajuanrkbmnbagian extends Model
{
    protected $table = 'bmn_pengajuanrkbmnbagian';
    
    protected $fillable = [
        'kode_jenis_pengajuan',
        'id_bagian_pengusul',
        'id_biro_pengusul',
        'id_bagian_pelaksana',
        'id_biro_pelaksana',
        'program',
        'kegiatan',
        'output',
        'kode_barang',
        'status',
        'tahun_anggaran',
        'tanggal_pengajuan',
        'tanggal_kebmn',
        'tanggal_keperencanaan',
        'tanggal_final',
        'tujuan_rencana',
        'atr_nonatr',
        'skema',
        'harga_barang',
        'total_anggaran',
        'uraian_barang',
        'keterangan',
        'dokumen_pendukung',
        'alasan_pengusul_bmn',
        'alasan_koordinator_bmn',
        'alasan_perencanaan',
        'created_at',
        'updated_at',
        'akun_belanja',
        'akun_neraca',
        'kuantitas',
        'tor_signed_path',
        'tanggal_verifikasi_tor',
        'lampiran_signed_path',
        'tanggal_verifikasi_lampiran'
    ];
    
    protected $dates = [
        'tanggal_pengajuan',
        'tanggal_kebmn',
        'tanggal_keperencanaan',
        'tanggal_final',
        'tanggal_verifikasi_tor',
        'tanggal_verifikasi_lampiran',
        'created_at',
        'updated_at'
    ];
}
