<?php

namespace App\Models\PerencanaanBMN\FilterBarang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    protected $table = 't_kel';
    protected $fillable = ['kd_gol', 'kd_bid', 'kd_kel', 'ur_kel', 'kd_kelbrg'];
    public $incrementing = false;

    // Relasi: Kelompok milik Bidang
    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'kd_bid', 'kd_bid')
                    ->where('kd_gol', $this->kd_gol);
    }

    // Relasi: Kelompok memiliki banyak Subkelompok
    public function subkelompok()
    {
        return $this->hasMany(Subkelompok::class, 'kd_kel', 'kd_kel')
                    ->where('kd_gol', $this->kd_gol)
                    ->where('kd_bid', $this->kd_bid);
    }
}
