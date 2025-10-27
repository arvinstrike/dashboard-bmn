<?php

namespace App\Models\PerencanaanBMN\EksporBatch;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BatchPengajuanSitangguhLog extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'bmn_batch_pengajuan_sitangguh_log';

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
     * Menunjukkan apakah model harus di-timestamp.
     * Tabel ini hanya memiliki created_at, tanpa updated_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi (mass assignable)
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'user_id',
        'aktivitas',
        'deskripsi',
        'status_sebelum',
        'status_sesudah',
        'created_at', // Wajib diisi secara manual karena $timestamps = false
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Dapatkan batch yang log ini miliknya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(BatchPengajuanSitangguh::class, 'batch_id');
    }

    /**
     * Dapatkan user yang melakukan tindakan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
