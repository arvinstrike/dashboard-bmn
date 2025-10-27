<?php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'bmn_pengajuanrkbmnbagian_nonsbsk';

    /**
     * Primary key dari tabel
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Tipe data primary key
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Kolom yang dapat diisi (mass assignable)
     *
     * @var array
     */
    protected $fillable = [
        'tahun_anggaran',
        'tipe_pengajuan',
        'jenis_formulir',
        'kode_pengenal',
        'kode_akun',         // Sesuai dengan struktur DB
        'skema',
        'keterangan',
        'id_bagian_pengusul',
        'id_biro_pengusul',
        'id_bagian_pelaksana',
        'id_biro_pelaksana',
        'status_pengajuan',
        'alasan_penolakan_pelaksana',
        'alasan_penolakan_koordinator',
        'created_by',
        'batch_id',
        'dokumen_pendukung',
    ];

    /**
     * Kolom yang harus di-cast.
     *
     * @var array
     */
    protected $casts = [
        'tahun_anggaran' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Satu pengajuan dapat memiliki banyak detail usulan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detilPengajuan()
    {
        return $this->hasMany(DetilPengajuan::class, 'pengajuan_id');
    }

    /**
     * Relasi: Satu pengajuan dapat memiliki banyak detail revisi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detilRevisi()
    {
        return $this->hasMany(DetilRevisi::class, 'pengajuan_id');
    }

    /**
     * Relasi: Pengajuan milik batch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(\App\Models\PerencanaanBMN\EksporBatch\BatchPengajuanSitangguh::class, 'batch_id');
    }

    /**
     * Accessor untuk menghitung total nilai
     *
     * @return float
     */
    public function getTotalNilaiAttribute()
    {
        // Hitung total berdasarkan tipe pengajuan
        if ($this->tipe_pengajuan == 'revisi') {
            return $this->detilRevisi()->sum('total');
        } else {
            return $this->detilPengajuan()->sum('total');
        }
    }

    public function bagianPengusul()
    {
        return $this->belongsTo('App\Models\ReferensiUnit\BagianModel', 'id_bagian_pengusul', 'id');
    }

    public function bagianPelaksana()
    {
        return $this->belongsTo('App\Models\ReferensiUnit\BagianModel', 'id_bagian_pelaksana', 'id');
    }
}
