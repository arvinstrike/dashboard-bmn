<?php

namespace App\Models\PerencanaanBMN\Bagian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KendaraanJabatanModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_pengajuan_kendaraan_jabatan';

    public $timestamps = false;

    protected $guarded = [];
}
