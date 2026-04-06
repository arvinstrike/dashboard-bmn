<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarBmn extends Model
{
    protected $table = 'daftar_bmn';

    protected $fillable = [
        'pemanfaatan_id',
        'kode_barang',
        'nup',
        'jenis_bmn',
        'luas_keseluruhan',
        'nilai_perolehan',
        'dicatat_di_simak',
        'objek_sewa',
        'lokasi',
        'penyewa',
        'peruntukan',
        'usulan_luas_sewa',
        'usulan_jangka_waktu',
        'usulan_periodesitas',
        'usulan_besaran_sewa',
    ];

    protected $casts = [
        'luas_keseluruhan' => 'decimal:2',
        'nilai_perolehan' => 'decimal:2',
        'usulan_luas_sewa' => 'decimal:2',
        'usulan_besaran_sewa' => 'decimal:2',
    ];

    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
