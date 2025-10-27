<?php

namespace App\Models\PerencanaanBMN\FilterBarang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golongan extends Model {
    protected $table = 't_gol';
    protected $primaryKey = 'kd_gol';
    public $incrementing = false; // jika tidak auto-increment sesuai kebutuhan
    protected $fillable = ['kd_gol', 'ur_gol'];

    public function bidang(){
         return $this->hasMany(Bidang::class, 'kd_gol', 'kd_gol');
    }
}
