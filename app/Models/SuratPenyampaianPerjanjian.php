<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPenyampaianPerjanjian extends Model
{
    protected $fillable = [
        'bmn_pemanfaatan_id',
        'nomor_surat',
        'tanggal_surat',
        'nama_mitra',
        'alamat_mitra',
        'kota_mitra',
        'nama_usaha',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];
}
