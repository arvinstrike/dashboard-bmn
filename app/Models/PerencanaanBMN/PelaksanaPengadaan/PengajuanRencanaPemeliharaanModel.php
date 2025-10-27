<?php

namespace App\Models\PerencanaanBMN\PelaksanaPengadaan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanRencanaPemeliharaanModel extends Model
{
    use HasFactory;

    protected $table = 'rencanapemeliharaanbmnbagian';

    public $timestamps = false;

    protected $guarded = [];
}
