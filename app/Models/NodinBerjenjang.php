<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NodinBerjenjang extends Model
{
    use HasFactory;

    protected $table = 'nodin_berjenjang';

    protected $fillable = [
        'pemanfaatan_id',
        'nomor',
        'tanggal',
        'tanggal_mulai',
        'tanggal_selesai',
        'mitra',
        'peruntukan',
        'nominal',
        'kasub_nama',
        'kasub_nomor'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Get the pemanfaatan that owns this nodin berjenjang
     */
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }

    /**
     * Auto-calculate duration (jangka waktu) from date range
     *
     * @return string|null
     */
    public function getJangkaWaktuAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return null;
        }

        $start = Carbon::parse($this->tanggal_mulai);
        $end = Carbon::parse($this->tanggal_selesai);
        $months = $start->diffInMonths($end);
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) {
            return "{$years} tahun {$remainingMonths} bulan";
        } elseif ($years > 0) {
            return "{$years} tahun";
        } else {
            return "{$months} bulan";
        }
    }
}
