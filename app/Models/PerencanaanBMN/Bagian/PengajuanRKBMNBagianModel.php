<?php

namespace App\Models\PerencanaanBMN\Bagian;

use App\Models\ReferensiUnit\BiroModel;
use App\Models\ReferensiUnit\BagianModel;
use App\Models\PerencanaanBMN\Bagian\KantorModel;
use App\Models\PerencanaanBMN\Bagian\RumahNegaraModel;
use App\Models\PerencanaanBMN\Bagian\KenjadaraanJabatanModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanRKBMNBagianModel extends Model
{
    use HasFactory;

    protected $table = 'bmn_pengajuanrkbmnbagian';

    public $timestamps = false;

    protected $guarded = [];

    public function biroPengusul(){
        return $this->belongsTo(BiroModel::class, 'id_biro_pengusul', 'id');
        // belongsTo(ModelTujuan, foreignKey, ownerKey)
    }

    public function biroPelaksana(){
        return $this->belongsTo(BiroModel::class, 'id_biro_pelaksana', 'id');
        // belongsTo(ModelTujuan, foreignKey, ownerKey)
    }

    public function bagianPengusul(){
        return $this->belongsTo(BagianModel::class, 'id_bagian_pengusul', 'id');

    }

     public function bagianPelaksana(){
        return $this->belongsTo(BagianModel::class, 'id_bagian_pelaksana', 'id');
    }

    public function bangunanKantor()
    {
        return $this->hasOne(KantorModel::class, 'kode_jenis_pengajuan', 'kode_jenis_pengajuan');
    }

    public function rumahNegara()
    {
        return $this->hasOne(RumahNegaraModel::class, 'kode_jenis_pengajuan', 'kode_jenis_pengajuan');
    }

    public function kendaraanJabatan() {
        return $this->hasOne(KendaraanJabatanModel::class, 'kode_jenis_pengajuan', 'kode_jenis_pengajuan');
    }

    public function kendaraanOperasional() {
        return $this->hasOne(KendaraanOperasionalModel::class, 'kode_jenis_pengajuan', 'kode_jenis_pengajuan');
    }

    public function kendaraanFungsional() {
        return $this->hasOne(KendaraanFungsionalModel::class, 'kode_jenis_pengajuan', 'kode_jenis_pengajuan');
    }
}
