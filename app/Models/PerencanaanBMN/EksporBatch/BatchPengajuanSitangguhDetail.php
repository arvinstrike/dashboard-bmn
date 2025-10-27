<?php

namespace App\Models\PerencanaanBMN\EksporBatch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchPengajuanSitangguhDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'bmn_batch_pengajuan_sitangguh_detail';

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
        'batch_id',
        'pengajuan_id',
        'urutan',
        'status_pengajuan_di_batch',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array
     */
    protected $casts = [
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Dapatkan batch yang detail ini miliknya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(BatchPengajuanSitangguh::class, 'batch_id');
    }

    /**
     * Dapatkan pengajuan yang terkait dengan detail ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pengajuan()
    {
        return $this->belongsTo(\App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::class, 'pengajuan_id');
    }
}
