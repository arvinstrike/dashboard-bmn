<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmnPemanfaatan extends Model
{
    use HasFactory;

    protected $table = 'bmn_pemanfaatan';

    protected $fillable = [
        'pic_penyewa',
        'nomor_hp_pic_penyewa',
        'pic_administrasi_bmn',
        'nomor_pic_administrasi_bmn',
        'nama_mitra_penyewa',
        'jenis_mitra',
        'jenis_usulan',
        'peruntukan_sewa',
        'keterangan_uraian',
        
        // Nodin Konfirmasi fields
        'nodin_konfirmasi_nomor',
        'nodin_konfirmasi_tanggal',
        'nodin_konfirmasi_mitra_peruntukan',
        'nodin_konfirmasi_tanggal_berakhir_sewa',
        
        // Surat Konfirmasi fields (Legacy/Direct columns)
        'surat_konfirmasi_nomor',
        'surat_konfirmasi_tanggal',
        'surat_konfirmasi_tujuan',
        'surat_konfirmasi_tujuan_surat',
        'surat_konfirmasi_peruntukan',
        'surat_konfirmasi_peruntukan_surat',
        'surat_konfirmasi_nomor_perjanjian_lama',
        'surat_konfirmasi_nomor_perjanjian_lama_dpr',
        'surat_konfirmasi_nomor_perjanjian_lama_mitra',
        'surat_konfirmasi_tanggal_berakhir',
        'surat_konfirmasi_tanggal_konfirmasi_terakhir',
        'surat_konfirmasi_kasub_nama_nomor',
        'surat_konfirmasi_kasub_nama',
        'surat_konfirmasi_kasub_nomor',
        'surat_konfirmasi_lampiran',
        
        // Documents
        'dokumen_surat_usulan_sewa',
        'dokumen_npwp',
        'dokumen_ktp_penandatangan',
        'dokumen_nib',
        
        // Surat Pernyataan
        'surat_pernyataan_nomor',
        'surat_pernyataan_tanggal',
        'surat_pernyataan_kode_barang',
        'surat_pernyataan_nup',
        'surat_pernyataan_luasan_sewa',
        'surat_pernyataan_lokasi_sewa',
        
        // More Documents
        'dokumen_psp',
        'dokumen_kib',
        'dokumen_usulan_ttd',
        'dokumen_jadwal_penilaian',
        'dokumen_basl',
        'dokumen_persetujuan_kpknl',
        
        // Surat Invoice
        'surat_invoice_nomor',
        'surat_invoice_nomor_bmn',
        'surat_invoice_tanggal',
        'surat_invoice_tanggal_faktur',
        'surat_invoice_tujuan',
        'surat_invoice_nomor_persetujuan',
        'surat_invoice_tanggal_persetujuan',
        'surat_invoice_periode_sewa',
        'surat_invoice_periode_mulai',
        'surat_invoice_periode_akhir',
        'surat_invoice_lama_periode',
        'surat_invoice_nominal',
        'surat_invoice_mitra',
        'surat_invoice_kasub',
        'surat_invoice_nama_kasubag_gelar',
        'surat_invoice_kasub_nomor',
        
        'dokumen_kode_billing',
        'daftar_bmn_nomor_surat',
        
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_dokumen',
        
        'is_complete',
        'status_sewa',
        
        // Financial tracking
        'total_pendapatan_terealisasi',
        'total_pendapatan_outstanding',
        'periode_pembayaran_ke',
        'total_periode_pembayaran',
        
        // Dates
        'tanggal_aktivasi',
        'tanggal_penyelesaian',
        
        // Extension logic
        'dapat_diperpanjang',
        'batas_perpanjangan',
        'kali_perpanjangan',
        
        'catatan_pembayaran',
        'catatan_status',
        
        'approved_at',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancelled_reason',
    ];

    protected $casts = [
        'nodin_konfirmasi_tanggal' => 'date',
        'nodin_konfirmasi_tanggal_berakhir_sewa' => 'date',
        'surat_konfirmasi_tanggal' => 'date',
        'surat_konfirmasi_tanggal_berakhir' => 'date',
        'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'date',
        'surat_pernyataan_tanggal' => 'date',
        'surat_invoice_tanggal' => 'date',
        'surat_invoice_tanggal_faktur' => 'date',
        'surat_invoice_tanggal_persetujuan' => 'date',
        'surat_invoice_periode_mulai' => 'date',
        'surat_invoice_periode_akhir' => 'date',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal' => 'date',
        'tanggal_aktivasi' => 'date',
        'tanggal_penyelesaian' => 'date',
        'batas_perpanjangan' => 'date',
        'approved_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_complete' => 'boolean',
        'dapat_diperpanjang' => 'boolean',
        'surat_invoice_nominal' => 'decimal:2',
        'total_pendapatan_terealisasi' => 'decimal:2',
        'total_pendapatan_outstanding' => 'decimal:2',
    ];

    // Relationships

    public function daftarBmn()
    {
        return $this->hasMany(DaftarBmn::class, 'pemanfaatan_id');
    }

    public function perjanjianSewa()
    {
        return $this->hasOne(PerjanjianSewa::class, 'pemanfaatan_id');
    }

    public function suratKonfirmasi()
    {
        return $this->hasOne(SuratKonfirmasiPerpanjanganSewa::class, 'pemanfaatan_id');
    }

    public function nodinBerjenjang()
    {
        return $this->hasOne(NodinBerjenjang::class, 'pemanfaatan_id');
    }

    public function nodinInternal()
    {
        return $this->hasOne(NodinInternal::class, 'bmn_pemanfaatan_id');
    }

    public function suratPermohonanTtd()
    {
        return $this->hasOne(SuratPermohonanTtd::class, 'bmn_pemanfaatan_id');
    }

    public function suratPenyampaianPerjanjian()
    {
        return $this->hasOne(SuratPenyampaianPerjanjian::class, 'bmn_pemanfaatan_id');
    }

    public function suratUsulanKpknlSptjm()
    {
        return $this->hasOne(SuratUsulanKpknlSptjm::class, 'pemanfaatan_id');
    }

    public function suratUsulanKpknl()
    {
        return $this->hasOne(SuratUsulanKpknlSptjm::class, 'pemanfaatan_id');
    }

    public function nodinPersetujuanKpknl()
    {
        return $this->hasOne(NodinPersetujuanKpknl::class, 'pemanfaatan_id');
    }
    
    // Helper Scopes
    
    public function scopeWithDocuments($query)
    {
        return $query->with([
            'perjanjianSewa',
            'suratKonfirmasi',
            'nodinBerjenjang',
            'nodinInternal',
            'suratPermohonanTtd',
            'suratPenyampaianPerjanjian',
            'suratUsulanKpknlSptjm',
            'nodinPersetujuanKpknl'
        ]);
    }
}
