<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BmnPemanfaatanPembayaran extends Model
{
    use HasFactory;

    protected $table = 'bmn_pemanfaatan_pembayaran';

    protected $fillable = [
        'pemanfaatan_id',
        'periode_ke',
        'periode_nama',
        'tanggal_mulai_periode',
        'tanggal_akhir_periode',
        'nomor_invoice',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'jumlah_tagihan',
        'status_pembayaran',
        'jumlah_dibayar',
        'sisa_tagihan',
        'ntpn',
        'tanggal_bayar',
        'dokumen_bukti_bayar',
        'kode_billing',
        'denda',
        'hari_terlambat',
        'catatan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_mulai_periode' => 'date',
        'tanggal_akhir_periode' => 'date',
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'jumlah_tagihan' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'denda' => 'decimal:2'
    ];

    // Relationships
    public function pemanfaatan()
    {
        return $this->belongsTo(BmnPemanfaatan::class, 'pemanfaatan_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status_pembayaran', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status_pembayaran', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status_pembayaran', 'overdue');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year)
                     ->whereMonth('tanggal_bayar', now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        if ($this->status_pembayaran === 'paid') {
            return false;
        }
        return $this->tanggal_jatuh_tempo && Carbon::parse($this->tanggal_jatuh_tempo)->isPast();
    }

    public function getHariTerlambatAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return now()->diffInDays(Carbon::parse($this->tanggal_jatuh_tempo));
    }

    // Methods
    public function hitungDenda()
    {
        if (!$this->is_overdue) {
            return 0;
        }

        $hariTerlambat = $this->hari_terlambat;
        $maxDenda = 10; // Max 10 hari
        $persenDenda = 0.01; // 1% per hari

        if ($hariTerlambat > $maxDenda) {
            $hariTerlambat = $maxDenda;
        }

        $denda = $this->sisa_tagihan * $persenDenda * $hariTerlambat;

        return round($denda, 2);
    }

    public function bayar($jumlah, $ntpn, $tanggalBayar = null, $dokumen = null)
    {
        $tanggalBayar = $tanggalBayar ?? now();

        $this->jumlah_dibayar += $jumlah;
        $this->sisa_tagihan = $this->jumlah_tagihan - $this->jumlah_dibayar;

        if ($this->sisa_tagihan <= 0) {
            $this->status_pembayaran = 'paid';
            $this->sisa_tagihan = 0;
        } else {
            $this->status_pembayaran = 'partial';
        }

        $this->ntpn = $ntpn;
        $this->tanggal_bayar = $tanggalBayar;
        if ($dokumen) {
            $this->dokumen_bukti_bayar = $dokumen;
        }

        // Hitung denda jika terlambat
        if ($this->is_overdue) {
            $this->denda = $this->hitungDenda();
            $this->hari_terlambat = $this->hari_terlambat;
        }

        $this->save();

        // Update total di pemanfaatan
        $this->pemanfaatan->updatePendapatanTerealisasi();

        return $this;
    }

    public function tandaiOverdue()
    {
        if ($this->is_overdue && $this->status_pembayaran === 'pending') {
            $this->status_pembayaran = 'overdue';
            $this->save();
        }
    }
}
