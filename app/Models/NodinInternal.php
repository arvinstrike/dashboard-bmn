<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodinInternal extends Model
{
    protected $fillable = [
        'bmn_pemanfaatan_id',
        'nomor_berjenjang_1',
        'nomor_berjenjang_2',
        'nomor_berjenjang_3',
        'perihal',
        'tanggal_surat',
        'nama_mitra',
        'objek_bmn',
        'nomor_perjanjian_induk',
        'nomor_persetujuan_sewa',
        'tanggal_persetujuan_sewa',
        'detail_persetujuan',
        'judul_perjanjian',
        'nomor_perjanjian',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_persetujuan_sewa' => 'date',
    ];
}
