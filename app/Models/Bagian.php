<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    protected $table = 'bagian';
    
    protected $fillable = [
        'id',
        'iddeputi',
        'idbiro',
        'uraianbagian',
        'status',
        'created_at',
        'updated_at'
    ];
    
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'id' => 'string',
        'iddeputi' => 'string',
        'idbiro' => 'string',
    ];
    
    // Relasi dengan pengajuan
    public function pengajuans()
    {
        return $this->hasMany(BmnPengajuanrkbmnbagian::class, 'id_bagian_pengusul');
    }
}
