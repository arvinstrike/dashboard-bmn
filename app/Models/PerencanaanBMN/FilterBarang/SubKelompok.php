<?php

namespace App\Models\PerencanaanBMN\FilterBarang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkelompok extends Model
{
    protected $table = 't_skel';
    protected $fillable = ['kd_gol', 'kd_bid', 'kd_kel', 'kd_skel', 'ur_skel', 'kd_skelbrg'];
    public $incrementing = false;

    // Relasi: Subkelompok milik Kelompok
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kd_skel', 'kd_skel')
                    ->where('kd_gol', $this->kd_gol)
                    ->where('kd_bid', $this->kd_bid)
                    ->where('kd_kel', $this->kd_kel);
    }

    // Relasi: Subkelompok memiliki banyak Barang
    public function barang()
    {
        return $this->hasMany(Barang::class, 'kd_skel', 'kd_skel')
                    ->where('kd_gol', $this->kd_gol)
                    ->where('kd_bid', $this->kd_bid)
                    ->where('kd_kel', $this->kd_kel);
    }
}
