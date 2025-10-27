<?php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel bmn_ref_perlengkapan_nonsbsk.
 * Merupakan referensi akhir yang dibentuk dari gabungan kode kategori dan pengguna.
 * Tabel ini digunakan untuk pengecekan barang, misalnya kuantitas melalui kode_barang.
 */
class Perlengkapan extends Model
{
    protected $table = 'bmn_ref_perlengkapan_nonsbsk';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'kode_kategori',
        'kode_pengguna',
        'kode_perlengkapan',
        'deskripsi_perlengkapan',
        'batasan_jumlah',
        'unit',
        'kode_barang'
    ];
}
