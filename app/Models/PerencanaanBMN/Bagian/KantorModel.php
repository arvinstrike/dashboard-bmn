<?php

namespace App\Models\PerencanaanBMN\Bagian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KantorModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_pengajuan_bangunan_perkantoran';

    public $timestamps = false;

    protected $guarded = [];
}
