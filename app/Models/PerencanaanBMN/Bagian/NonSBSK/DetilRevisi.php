<?php

// File: app/Models/PerencanaanBMN/Bagian/NonSBSK/DetilRevisi.php

namespace App\Models\PerencanaanBMN\Bagian\NonSBSK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetilRevisi extends Model
{
    use HasFactory;

    protected $table = 'bmn_detil_revisi_rkbmnbagian_nonsbsk';

    protected $primaryKey = 'id';

    protected $fillable = [
        'pengajuan_id',
        'kode_perlengkapan',
        'kode_barang',
        'keterangan_barang',
        'kuantitas',
        'harga',
        'total',
        'path_image'
    ];

    // Cast untuk memastikan tipe data yang benar
    protected $casts = [
        'pengajuan_id' => 'integer',
        'kuantitas' => 'integer',
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Validasi rules yang bisa digunakan di controller
    public static function validationRules()
    {
        return [
            'pengajuan_id' => 'required|integer|exists:bmn_pengajuan_rkbmnbagian_nonsbsk,id',
            'kode_perlengkapan' => 'nullable|string|max:50',
            'kode_barang' => 'required|string|max:50',
            'keterangan_barang' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'path_image' => 'nullable|string|max:255',
        ];
    }

    // Relasi ke header pengajuan
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    // Accessor untuk format harga dalam rupiah
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Accessor untuk format total dalam rupiah
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    // Accessor untuk harga tanpa format (untuk input)
    public function getRawHargaAttribute()
    {
        return number_format($this->harga, 0, '', '.');
    }

    // Accessor untuk total tanpa format (untuk input)
    public function getRawTotalAttribute()
    {
        return number_format($this->total, 0, '', '.');
    }

    // Mutator untuk harga (membersihkan format sebelum simpan)
    public function setHargaAttribute($value)
    {
        // Hapus titik pemisah ribuan dan karakter non-numeric
        $cleanValue = preg_replace('/[^0-9]/', '', $value);
        $this->attributes['harga'] = (float) $cleanValue;
    }

    // Mutator untuk total (auto calculate atau clean format)
    public function setTotalAttribute($value)
    {
        if (is_string($value)) {
            // Jika berupa string, bersihkan format
            $cleanValue = preg_replace('/[^0-9]/', '', $value);
            $this->attributes['total'] = (float) $cleanValue;
        } else {
            $this->attributes['total'] = (float) $value;
        }
    }

    // Method untuk kalkulasi otomatis total
    public function calculateTotal()
    {
        $this->total = $this->kuantitas * $this->harga;
        return $this->total;
    }

    // Scope untuk filter berdasarkan pengajuan
    public function scopeByPengajuan($query, $pengajuanId)
    {
        return $query->where('pengajuan_id', $pengajuanId);
    }

    // Scope untuk filter berdasarkan kode barang
    public function scopeByKodeBarang($query, $kodeBarang)
    {
        return $query->where('kode_barang', $kodeBarang);
    }

    // Method untuk mendapatkan total nilai per pengajuan
    public static function getTotalByPengajuan($pengajuanId)
    {
        return self::where('pengajuan_id', $pengajuanId)->sum('total');
    }
}
