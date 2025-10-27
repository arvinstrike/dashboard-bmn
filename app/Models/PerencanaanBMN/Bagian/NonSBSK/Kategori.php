<?php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel bmn_ref_kategori_nonsbsk.
 * Berisi data kategori (jabatan dan ruangan).
 */
class Kategori extends Model
{
    protected $table = 'bmn_ref_kategori_nonsbsk';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'kode_kategori',
        'deskripsi_kategori'
    ];
}
