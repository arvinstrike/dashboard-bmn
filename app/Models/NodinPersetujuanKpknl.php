<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NodinPersetujuanKpknl extends Model
{
    use HasFactory;

    protected $table = 'nodin_persetujuan_kpknl';

    protected $fillable = [
        'pemanfaatan_id',
        'nomor_nodin',
        'tanggal_nodin',
        'perihal_nodin',
        'jangka_waktu',
        'periode_sewa_mulai',
        'periode_sewa_selesai',
        'tujuan',
        'nominal',
        'mitra',
        'kasub',
    ];

    protected $casts = [
        'tanggal_nodin' => 'date',
        'periode_sewa_mulai' => 'date',
        'periode_sewa_selesai' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
