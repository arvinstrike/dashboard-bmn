<?php

namespace App\Models\PerencanaanBMN\EksporBatch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BatchPengajuanSitangguh extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'bmn_batch_pengajuan_sitangguh';

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
     * Atribut yang dapat diisi (mass assignable)
     *
     * @var array
     */
    protected $fillable = [
        'kode_batch',
        'tanggal_dibuat',
        'created_by',
        'status',
        'keterangan',
        'tanggal_dikirim',
        'tanggal_diproses',
        'tanggal_selesai',
        'total_nilai_batch',
        'meta_info',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_dibuat' => 'date',
        'tanggal_dikirim' => 'datetime',
        'tanggal_diproses' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'total_nilai_batch' => 'decimal:2',
        'meta_info' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Dapatkan pembuat batch ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Dapatkan detail untuk batch ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(BatchPengajuanSitangguhDetail::class, 'batch_id');
    }

    /**
     * Dapatkan log untuk batch ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(BatchPengajuanSitangguhLog::class, 'batch_id');
    }

    /**
     * Dapatkan semua pengajuan yang terkait dengan batch ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pengajuan()
    {
        return $this->belongsToMany(\App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::class,
            'bmn_batch_pengajuan_sitangguh_detail',
            'batch_id',
            'pengajuan_id')
            ->withPivot('urutan', 'status_pengajuan_di_batch')
            ->orderBy('urutan');
    }
}
