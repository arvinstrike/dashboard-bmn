<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKonfirmasiPerpanjanganSewa extends Model
{
    use HasFactory;

    protected $table = 'surat_konfirmasi_perpanjangan_sewa';

    protected $fillable = [
        'pemanfaatan_id',
        'nomor',
        'tanggal',
        'tujuan_surat',
        'peruntukan_surat',
        'nomor_perjanjian_lama_dpr',
        'nomor_perjanjian_lama_mitra',
        'tanggal_berakhir',
        'tanggal_konfirmasi_terakhir',
        'kasub_nama',
        'kasub_nomor',
        'lampiran'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_berakhir' => 'date',
        'tanggal_konfirmasi_terakhir' => 'date',
    ];

    /**
     * Get the pemanfaatan that owns this surat konfirmasi
     */
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
