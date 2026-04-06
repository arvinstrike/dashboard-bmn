<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjanjianSewa extends Model
{
    use HasFactory;

    protected $table = 'perjanjian_sewa';

    protected $fillable = [
        'pemanfaatan_id',
        'logo_penyewa',
        'pihak_pertama_nama',
        'pihak_pertama_kedudukan',
        'pihak_pertama_keputusan_nomor',
        'pihak_pertama_keputusan_tahun',
        'mitra_penyewa',
        'pihak_kedua_nama',
        'pihak_kedua_kedudukan',
        'pihak_kedua_dasar_hukum',
        'pihak_kedua_keputusan_nomor',
        'pihak_kedua_keputusan_tanggal',
        'pihak_kedua_atas_nama',
        'pihak_kedua_alamat',
        'pihak_kedua_kegiatan_usaha',
        'objek_luas',
        'objek_satuan_luas',
        'objek_letak',
        'objek_gedung',
        'peruntukan',
        'nilai_sewa',
        'durasi_sewa',
        'periode_mulai',
        'periode_selesai',
        'nomor_surat',
        'tanggal_surat',
        'dokumen_perjanjian',
        'dokumen_bukti_bayar',
        'dokumen_bukti_tindak_lanjut_siman',
        'nilai_pendapatan_bukti_bayar',
    ];

    protected $casts = [
        'pihak_kedua_keputusan_tanggal' => 'date',
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'tanggal_surat' => 'date',
        'nilai_sewa' => 'decimal:2',
        'nilai_pendapatan_bukti_bayar' => 'decimal:2',
        'objek_luas' => 'double',
    ];

    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }
}
