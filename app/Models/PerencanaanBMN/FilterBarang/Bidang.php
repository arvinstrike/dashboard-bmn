<?php

namespace App\Models\PerencanaanBMN\FilterBarang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model {
    protected $table = 't_bid';
    protected $fillable = ['kd_gol', 'kd_bid', 'ur_bid', 'kd_bidbrg'];

    public function golongan(){
         return $this->belongsTo(Golongan::class, 'kd_gol', 'kd_gol');
    }

    public function kelompok(){
         return $this->hasMany(Kelompok::class, ['kd_gol','kd_bid'], ['kd_gol','kd_bid']);
    }
}
