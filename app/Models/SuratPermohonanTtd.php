<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPermohonanTtd extends Model
{
    protected $fillable = [
        'bmn_pemanfaatan_id',
        'nomor_surat',
        'perihal',
        'tanggal_surat',
        'tujuan_surat',
        'tujuan_surat_bertempat',
        'nama_fasilitas_bmn',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    //
}
