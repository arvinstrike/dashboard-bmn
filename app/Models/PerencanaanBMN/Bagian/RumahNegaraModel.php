<?php

namespace App\Models\PerencanaanBMN\Bagian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahNegaraModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_pengajuan_rumah_negara';

    public $timestamps = false;

    protected $guarded = [];
}
