<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmnPemanfaatan extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable Laravel's default timestamps since table doesn't have created_at/updated_at

    protected $table = 'bmn_pemanfaatan';

    protected $fillable = [
        // Old fields (keep for compatibility)
        'pic',
        'mitra',
        'jenis_usaha',
        'lokasi',
        'uraian',
        'biaya_sewa',
        'tanggal_mulai',
        'tanggal_berakhir',
        'total_biaya_sewa',
        'keterangan',
        'is_complete',

        // Tahap 1: Informasi Penyewa
        'pic_penyewa',
        'nomor_hp_pic_penyewa',
        'pic_administrasi_bmn',
        'nomor_pic_administrasi_bmn',
        'nama_mitra_penyewa',
        'jenis_mitra',
        'jenis_usulan',
        'peruntukan_sewa',
        'keterangan_uraian',

        // Tab 2: Konfirmasi - Nodin
        'nodin_konfirmasi_nomor',
        'nodin_konfirmasi_tanggal',
        'nodin_konfirmasi_mitra_peruntukan',
        'nodin_konfirmasi_tanggal_berakhir_sewa',

        // Tab 2: Surat Konfirmasi
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
        'surat_konfirmasi_kasub_nama',
        'surat_konfirmasi_kasub_nama_nomor',
        'surat_konfirmasi_kasub_nomor',
        'surat_konfirmasi_lampiran',

        // Tab 2: Dokumen Pendukung
        'dokumen_surat_usulan_sewa',
        'dokumen_npwp',
        'dokumen_ktp_penandatangan',
        'dokumen_nib',

        // Tab 3: Usulan Pemanfaatan - Nodin Berjenjang
        'nodin_berjenjang_mitra',
        'nodin_berjenjang_peruntukan',
        'nodin_berjenjang_nomor',
        'nodin_berjenjang_tanggal', // Legacy single date
        'nodin_berjenjang_tanggal_mulai', // Date range start
        'nodin_berjenjang_tanggal_selesai', // Date range end
        'nodin_berjenjang_nominal',

        // Tab 3: Surat Usulan KPKNL
        'surat_usulan_kpknl_nomor',
        'surat_usulan_kpknl_tanggal',
        'surat_usulan_kpknl_hal',
        'surat_usulan_kpknl_tujuan',
        'surat_usulan_kpknl_isi',
        'surat_usulan_kpknl_peruntukan',
        'surat_usulan_kpknl_tanggal_berakhir',
        'surat_usulan_kpknl_nama_kasubag',
        'surat_usulan_kpknl_nomor_kasubag',

        // Tab 3: SPTJM
        'sptjm_nomor',
        'sptjm_tanggal',
        'sptjm_kode_barang',
        'sptjm_nup',
        'sptjm_luasan_sewa',
        'sptjm_lokasi_sewa',

        // Tab 3: Surat Pernyataan
        'surat_pernyataan_nomor',
        'surat_pernyataan_tanggal',
        'surat_pernyataan_kode_barang',
        'surat_pernyataan_nup',
        'surat_pernyataan_luasan_sewa',
        'surat_pernyataan_lokasi_sewa',

        // Tab 3: Daftar BMN & Dokumen
        'daftar_bmn',
        'dokumen_psp',
        'dokumen_kib',
        'dokumen_usulan_ttd',

        // Tab 4: Penilaian KPKNL - Dokumen
        'dokumen_jadwal_penilaian',
        'dokumen_basl',
        'dokumen_persetujuan_kpknl',

        // Tab 4: Nodin Persetujuan KPKNL
        'nodin_persetujuan_kpknl_nomor',
        'nodin_persetujuan_kpknl_tanggal',
        'nodin_persetujuan_kpknl_tujuan',
        'nodin_persetujuan_kpknl_nomor_persetujuan',
        'nodin_persetujuan_kpknl_tanggal_persetujuan',
        'nodin_persetujuan_kpknl_periode_sewa',
        'nodin_persetujuan_kpknl_nominal',
        'nodin_persetujuan_kpknl_mitra',
        'nodin_persetujuan_kpknl_kasub',

        // Tab 4: Surat Invoice
        'surat_invoice_nomor',
        'surat_invoice_tanggal',
        'surat_invoice_tujuan',
        'surat_invoice_nomor_persetujuan',
        'surat_invoice_tanggal_persetujuan',
        'surat_invoice_periode_sewa',
        'surat_invoice_nominal',
        'surat_invoice_mitra',
        'surat_invoice_kasub',
        'dokumen_kode_billing',

        // Tab 5: Perjanjian - Dokumen
        'dokumen_bukti_bayar',
        'dokumen_perjanjian',

        // Tab 5: Detail Perjanjian
        'perjanjian_logo_penyewa',
        'perjanjian_mitra',
        'perjanjian_peruntukan',
        'perjanjian_gedung',
        'perjanjian_hari_tanggal',
        'perjanjian_detail_pihak_kedua',
        'perjanjian_nomor',
        'perjanjian_tanggal_penandatanganan',
        'jangka_waktu_nilai',
        'jangka_waktu_satuan',

        // Tab 5: Nodin Ttd & Internal
        'nodin_ttd_nomor',
        'nodin_ttd_tanggal',
        'nodin_ttd_tujuan',
        'nodin_ttd_mitra',
        'nodin_ttd_judul_perjanjian',
        'nodin_internal_nomor',
        'nodin_internal_tanggal',
        'nodin_internal_mitra',
        'nodin_internal_judul_perjanjian',
        'nodin_internal_nomor_perjanjian',
        'nodin_internal_detail_persetujuan',

        // Old fields to keep for backward compatibility
        'konfirmasi_permintaan_data_penyewa_nomor',
        'konfirmasi_permintaan_data_penyewa_tanggal',
        'konfirmasi_permintaan_data_penyewa_dokumen',
        'konfirmasi_penyewa_nomor',
        'konfirmasi_penyewa_tanggal',
        'konfirmasi_penyewa_dokumen',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_nomor',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_dokumen',
        'penilaian',
        'berita_acara_survei_lapangan',
        'persetujuan_pemanfaatan_sewa_kpk_nomor',
        'persetujuan_pemanfaatan_sewa_kpk_tanggal',
        'persetujuan_pemanfaatan_sewa_kpk_dokumen',
        'pembayaran_ntpn',
        'pembayaran_tanggal',
        'pembayaran_dokumen',

        // Revenue Tracking Fields (New - from migration 2025_11_14)
        'status_sewa',
        'total_pendapatan_terealisasi',
        'total_pendapatan_outstanding',
        'periode_pembayaran_ke',
        'total_periode_pembayaran',
        'tanggal_aktivasi',
        'tanggal_penyelesaian',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'cancelled_reason',
        'cancelled_by',
    ];

    protected $casts = [
        // Date fields
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'nodin_konfirmasi_tanggal' => 'date',
        'nodin_konfirmasi_tanggal_berakhir_sewa' => 'date',
        'surat_konfirmasi_tanggal_berakhir' => 'date',
        'surat_konfirmasi_tanggal' => 'date',
        'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'date',
        'surat_usulan_kpknl_tanggal' => 'date',
        'sptjm_tanggal' => 'date',
        'surat_pernyataan_tanggal' => 'date',
        'nodin_persetujuan_kpknl_tanggal' => 'date',
        'nodin_persetujuan_kpknl_tanggal_persetujuan' => 'date',
        'surat_invoice_tanggal' => 'date',
        'surat_invoice_tanggal_persetujuan' => 'date',
        'perjanjian_tanggal_penandatanganan' => 'date',
        'nodin_ttd_tanggal' => 'date',
        'nodin_internal_tanggal' => 'date',
        'nodin_berjenjang_tanggal' => 'date',

        // Old date fields (keep for compatibility)
        'konfirmasi_permintaan_data_penyewa_tanggal' => 'date',
        'konfirmasi_penyewa_tanggal' => 'date',
        'usulan_pemanfaatan_sewa_permohonan_tarif_sewa_tanggal' => 'date',
        'pembayaran_tanggal' => 'date',
        'persetujuan_pemanfaatan_sewa_kpk_tanggal' => 'date',

        // Decimal fields
        'biaya_sewa' => 'decimal:2',
        'total_biaya_sewa' => 'decimal:2',
        'nodin_persetujuan_kpknl_nominal' => 'decimal:2',
        'surat_invoice_nominal' => 'decimal:2',
        'nodin_berjenjang_nominal' => 'decimal:2',
        'total_pendapatan_terealisasi' => 'decimal:2',
        'total_pendapatan_outstanding' => 'decimal:2',

        // JSON fields
        'daftar_bmn' => 'array',

        // Boolean fields
        'is_complete' => 'boolean',

        // Revenue tracking date fields
        'tanggal_aktivasi' => 'date',
        'tanggal_penyelesaian' => 'date',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Additional fillable for new revenue tracking fields
    protected $appends = ['is_aktif_berlangsung', 'persentase_pembayaran'];

    // Relationships
    // NOTE: Relationship disabled - bmn_pemanfaatan_pembayaran table not yet implemented
    // public function pembayaran()
    // {
    //     return $this->hasMany(BmnPemanfaatanPembayaran::class, 'pemanfaatan_id');
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status_sewa', 'active')
                     ->whereDate('tanggal_aktivasi', '<=', now())
                     ->whereDate('tanggal_berakhir', '>=', now())
                     ->where('total_pendapatan_terealisasi', '>', 0);
    }

    public function scopeDraft($query)
    {
        return $query->where('status_sewa', 'draft');
    }

    public function scopeReview($query)
    {
        return $query->where('status_sewa', 'review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_sewa', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_sewa', 'completed');
    }

    // Accessors
    public function getIsAktifBerlangsungAttribute()
    {
        // Pemanfaatan aktif berlangsung jika:
        // nodin_konfirmasi_tanggal_berakhir_sewa belum jatuh tempo (>= hari ini)
        if (!$this->nodin_konfirmasi_tanggal_berakhir_sewa) {
            return false;
        }

        $today = now()->startOfDay();
        $endDate = \Carbon\Carbon::parse($this->nodin_konfirmasi_tanggal_berakhir_sewa)->startOfDay();

        return $endDate >= $today;
    }

    public function getPersentasePembayaranAttribute()
    {
        if ($this->total_biaya_sewa == 0) {
            return 0;
        }
        return round(($this->total_pendapatan_terealisasi / $this->total_biaya_sewa) * 100, 2);
    }

    public function getSisaPembayaranAttribute()
    {
        return $this->total_biaya_sewa - $this->total_pendapatan_terealisasi;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'review' => '<span class="badge bg-info">Review</span>',
            'approved' => '<span class="badge bg-primary">Approved</span>',
            'active' => '<span class="badge bg-success">Active</span>',
            'completed' => '<span class="badge bg-dark">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'expired' => '<span class="badge bg-warning">Expired</span>',
        ];

        return $badges[$this->status_sewa] ?? '<span class="badge bg-secondary">-</span>';
    }

    // Methods
    // NOTE: Method disabled - bmn_pemanfaatan_pembayaran table not yet implemented
    // public function updatePendapatanTerealisasi()
    // {
    //     $this->total_pendapatan_terealisasi = $this->pembayaran()->paid()->sum('jumlah_dibayar');
    //     $this->total_pendapatan_outstanding = $this->pembayaran()
    //         ->whereIn('status_pembayaran', ['pending', 'partial', 'overdue'])
    //         ->sum('sisa_tagihan');
    //     $this->save();
    // }

    public function activate()
    {
        if ($this->status_sewa === 'approved' && $this->total_pendapatan_terealisasi > 0) {
            $this->status_sewa = 'active';
            $this->tanggal_aktivasi = now();
            $this->activated_at = now();
            $this->save();
            return true;
        }
        return false;
    }

    public function complete()
    {
        if ($this->status_sewa === 'active') {
            $this->status_sewa = 'completed';
            $this->tanggal_penyelesaian = now();
            $this->completed_at = now();
            $this->save();
            return true;
        }
        return false;
    }

    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->status_sewa = 'cancelled';
        $this->cancelled_at = now();
        $this->cancelled_reason = $reason;
        $this->cancelled_by = $cancelledBy;
        $this->save();
        return true;
    }

    public function generatePembayaranPeriodik()
    {
        // Generate pembayaran periodik berdasarkan jangka waktu
        if (!$this->tanggal_mulai || !$this->jangka_waktu_nilai || !$this->biaya_sewa) {
            return false;
        }

        $periode = $this->total_periode_pembayaran ?? $this->jangka_waktu_nilai;
        $tanggalMulai = \Carbon\Carbon::parse($this->tanggal_mulai);

        for ($i = 1; $i <= $periode; $i++) {
            $periodeNama = $tanggalMulai->format('F Y');
            $tanggalAkhir = $tanggalMulai->copy()->addMonth(1)->subDay();

            BmnPemanfaatanPembayaran::firstOrCreate(
                [
                    'pemanfaatan_id' => $this->id,
                    'periode_ke' => $i
                ],
                [
                    'periode_nama' => $periodeNama,
                    'tanggal_mulai_periode' => $tanggalMulai->format('Y-m-d'),
                    'tanggal_akhir_periode' => $tanggalAkhir->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => $tanggalMulai->copy()->subDays(7)->format('Y-m-d'),
                    'jumlah_tagihan' => $this->biaya_sewa,
                    'sisa_tagihan' => $this->biaya_sewa,
                    'status_pembayaran' => 'pending'
                ]
            );

            $tanggalMulai->addMonth();
        }

        return true;
    }

    // Static methods untuk dashboard
    public static function getDashboardStats()
    {
        return [
            'total_aktif' => self::active()->count(),
            'total_draft' => self::draft()->count(),
            'total_review' => self::review()->count(),
            'total_approved' => self::approved()->count(),
            'total_completed' => self::completed()->whereYear('completed_at', now()->year)->count(),
            'pendapatan_bulan_ini' => BmnPemanfaatanPembayaran::thisMonth()->paid()->sum('jumlah_dibayar'),
            'pendapatan_tahun_ini' => BmnPemanfaatanPembayaran::thisYear()->paid()->sum('jumlah_dibayar'),
            'total_outstanding' => BmnPemanfaatanPembayaran::whereIn('status_pembayaran', ['pending', 'overdue'])->sum('sisa_tagihan'),
            'invoice_overdue' => BmnPemanfaatanPembayaran::overdue()->count()
        ];
    }
}