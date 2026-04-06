<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratUsulanKpknlSptjm extends Model
{
    use HasFactory;

    protected $table = 'surat_usulan_kpknl_sptjm';

    protected $fillable = [
        'pemanfaatan_id',
        'surat_usulan_nomor',
        'surat_usulan_tanggal',
        'surat_usulan_hal',
        'surat_usulan_tujuan',
        'surat_usulan_isi',
        'surat_usulan_peruntukan',
        'surat_usulan_tanggal_berakhir',
        'kasubag_nama',
        'kasubag_nomor',
        'sptjm_nomor',
        'sptjm_tanggal',
        'sptjm_kode_barang',
        'sptjm_nup',
        'sptjm_luasan_sewa',
        'sptjm_lokasi_sewa'
    ];

    protected $casts = [
        'surat_usulan_tanggal' => 'date',
        'surat_usulan_tanggal_berakhir' => 'date',
        'sptjm_tanggal' => 'date',
    ];

    /**
     * Get the pemanfaatan that owns this surat usulan kpknl & sptjm
     */
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }

    /**
     * Check if this record has valid SPTJM data
     *
     * @return bool
     */
    public function hasSptjmData()
    {
        return !empty($this->sptjm_nomor) && !empty($this->sptjm_kode_barang);
    }
}
