<?php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel bmn_ref_bagian_pengguna_nonsbsk.
 * Digunakan untuk mengecek id bagian guna memfilter pengguna berdasarkan id bagian.
 */
class BagianPengguna extends Model
{
    protected $table = 'bmn_ref_bagian_pengguna_nonsbsk';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id_bagian',
        'uraian_bagian',
        'kode_pengguna'
    ];
}
