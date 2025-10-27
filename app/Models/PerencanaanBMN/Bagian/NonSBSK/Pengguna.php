<?php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel bmn_ref_pengguna_non_sbsk.
 * Menyediakan data pengguna yang dapat berupa ruangan atau orang (berdasarkan jabatan).
 */
class Pengguna extends Model
{
    protected $table = 'bmn_ref_pengguna_non_sbsk';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'kode_kategori',
        'kode_pengguna',
        'deskripsi_pengguna'
    ];
}
