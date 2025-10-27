<?php

namespace App\Models\PerencanaanBMN\Bagian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KendaraanOperasionalModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_pengajuan_kendaraan_operasional';

    public $timestamps = false;

    protected $guarded = [];
}
