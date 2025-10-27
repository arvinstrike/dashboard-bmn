<?php

namespace App\Models\PerencanaanBMN\FilterBarang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 't_brg';
    protected $fillable = ['kd_gol', 'kd_bid', 'kd_kel', 'kd_skel', 'kd_sskel', 'ur_sskel', 'kd_brg'];
    public $incrementing = false;

    // Relasi: Barang milik Subkelompok
    public function subkelompok()
    {
        return $this->belongsTo(Subkelompok::class, 'kd_skel', 'kd_skel')
                    ->where('kd_gol', $this->kd_gol)
                    ->where('kd_bid', $this->kd_bid)
                    ->where('kd_kel', $this->kd_kel);
    }
}
