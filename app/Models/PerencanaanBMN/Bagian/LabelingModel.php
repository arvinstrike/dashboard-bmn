<?php

namespace App\Models\PerencanaanBMN\Bagian;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BmnInventarisasiKkSakti;

class LabelingModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_labeling';

    protected $fillable = [
        'kk_sakti_id',
        'kode_barang',
        'nup',
        'area',
        'gedung',
        'ruangan',
        'uraian_barang',
        'merek',
        'tahun_perolehan',
        'status_cetak',
        'tanggal_cetak',
        'status_label',
    ];

    protected $casts = [
        'kk_sakti_id' => 'integer',
        'kode_barang' => 'integer',
        'tahun_perolehan' => 'integer',
        'tanggal_cetak' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function kkSakti()
    {
        return $this->belongsTo(BmnInventarisasiKkSakti::class, 'kk_sakti_id');
    }

    // Scopes
    public function scopeBelumCetak($query)
    {
        return $query->where('status_cetak', 'belum_cetak');
    }

    public function scopeSudahCetak($query)
    {
        return $query->where('status_cetak', 'sudah_cetak');
    }

    public function scopeBelumLabel($query)
    {
        return $query->where('status_label', 'belum_label');
    }

    public function scopeSudahLabel($query)
    {
        return $query->where('status_label', 'sudah_label');
    }

    public function scopeByKodeBarang($query, $kodeBarang)
    {
        return $query->where('kode_barang', $kodeBarang);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopeByGedung($query, $gedung)
    {
        return $query->where('gedung', $gedung);
    }

    public function scopeByRuangan($query, $ruangan)
    {
        return $query->where('ruangan', $ruangan);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nup', 'like', "%{$search}%")
              ->orWhere('uraian_barang', 'like', "%{$search}%")
              ->orWhere('merek', 'like', "%{$search}%")
              ->orWhere('kode_barang', 'like', "%{$search}%");
        });
    }

    // Methods
    public function markAsCetak()
    {
        return $this->update([
            'status_cetak' => 'sudah_cetak',
            'tanggal_cetak' => now(),
        ]);
    }

    public function markAsLabel()
    {
        return $this->update([
            'status_label' => 'sudah_label',
        ]);
    }

    public function resetCetak()
    {
        return $this->update([
            'status_cetak' => 'belum_cetak',
            'tanggal_cetak' => null,
        ]);
    }

    public function resetLabel()
    {
        return $this->update([
            'status_label' => 'belum_label',
        ]);
    }

    // Accessors
    public function getLokasiLengkapAttribute()
    {
        $parts = array_filter([$this->area, $this->gedung, $this->ruangan]);
        return implode(' - ', $parts);
    }

    public function getIsCetakAttribute()
    {
        return $this->status_cetak === 'sudah_cetak';
    }

    public function getIsLabelAttribute()
    {
        return $this->status_label === 'sudah_label';
    }

    // Static methods for statistics
    public static function getStats()
    {
        return [
            'total' => self::count(),
            'belum_cetak' => self::belumCetak()->count(),
            'sudah_cetak' => self::sudahCetak()->count(),
            'belum_label' => self::belumLabel()->count(),
            'sudah_label' => self::sudahLabel()->count(),
            'siap_cetak' => self::belumCetak()->count(),
            'siap_label' => self::sudahCetak()->belumLabel()->count(),
        ];
    }

    public static function getStatsByLokasi()
    {
        return self::selectRaw('area, gedung, ruangan,
                               COUNT(*) as total,
                               SUM(CASE WHEN status_cetak = "sudah_cetak" THEN 1 ELSE 0 END) as sudah_cetak,
                               SUM(CASE WHEN status_label = "sudah_label" THEN 1 ELSE 0 END) as sudah_label')
                    ->groupBy('area', 'gedung', 'ruangan')
                    ->orderBy('area')
                    ->orderBy('gedung')
                    ->orderBy('ruangan')
                    ->get();
    }
}
