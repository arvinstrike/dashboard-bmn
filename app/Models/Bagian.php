<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    protected $table = 'bagian';
    
    protected $fillable = [
        'uraianbagian',
        'created_at',
        'updated_at'
    ];
    
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    // Relasi dengan pengajuan
    public function pengajuans()
    {
        return $this->hasMany(BmnPengajuanrkbmnbagian::class, 'id_bagian_pengusul');
    }
}
