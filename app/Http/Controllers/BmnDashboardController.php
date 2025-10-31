<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPengajuanrkbmnbagian;
use App\Models\Bagian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BmnDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari request
        $filters = [
            'jenis_pengajuan' => $request->get('jenis_pengajuan'),
            'bagian' => $request->get('bagian'),
            'status' => $request->get('status'),
            'tahun_anggaran' => $request->get('tahun_anggaran'),
            'anggaran_min' => $request->get('anggaran_min'),
            'anggaran_max' => $request->get('anggaran_max'),
        ];

        // Query dasar dengan filter
        $query = BmnPengajuanrkbmnbagian::query();
        
        // Filter Jenis Pengajuan
        if ($filters['jenis_pengajuan']) {
            $query->where('kode_jenis_pengajuan', 'LIKE', $filters['jenis_pengajuan'] . '%');
        }
        
        // Filter Bagian Pengusul
        if ($filters['bagian']) {
            $selectedBagian = trim($filters['bagian']);
            
            // Gabungkan tabel bagian untuk mencocokkan nama bagian pengusul
            $query->where(function($q) use ($selectedBagian) {
                $q->whereHas('bagianPengusul', function($subQuery) use ($selectedBagian) {
                    $subQuery->whereRaw('BINARY TRIM(uraianbagian) = ?', [trim($selectedBagian)]);
                })
                ->orWhereHas('biroPengusul', function($subQuery) use ($selectedBagian) {
                    $subQuery->whereRaw('BINARY TRIM(uraianbagian) = ?', [trim($selectedBagian)]);
                });
            });
        }
        
        // Filter Status
        if ($filters['status']) {
            $query->where('bmn_pengajuanrkbmnbagian.status', $filters['status']);
        }
        
        // Filter Tahun Anggaran
        if ($filters['tahun_anggaran']) {
            $query->where('tahun_anggaran', $filters['tahun_anggaran']);
        }
        
        // Filter Anggaran Minimum
        if ($filters['anggaran_min']) {
            $query->where('total_anggaran', '>=', $filters['anggaran_min']);
        }
        
        // Filter Anggaran Maximum
        if ($filters['anggaran_max']) {
            $query->where('total_anggaran', '<=', $filters['anggaran_max']);
        }

        // Ambil data untuk statistik
        $stats = $this->getStats($query);
        
        // Ambil data untuk tabel paginated dengan relasi
        $pengajuans = $query->with(['bagianPengusul', 'biroPengusul'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10);

        // Log hasil
        Log::info('Result Count:', ['count' => $pengajuans->count()]);
        if ($pengajuans->count() > 0) {
            Log::info('First Result:', [
                'id' => $pengajuans[0]->id,
                'id_bagian_pengusul' => $pengajuans[0]->id_bagian_pengusul,
                'id_biro_pengusul' => $pengajuans[0]->id_biro_pengusul,
            ]);
        }

        // Ambil data untuk filter options
        $jenisPengajuanOptions = $this->getJenisPengajuanOptions();
        $bagianOptions = Bagian::where('status', 'on')
            ->orderBy('uraianbagian')
            ->get();
        $statusOptions = BmnPengajuanrkbmnbagian::select('status')
            ->distinct()
            ->whereNotNull('status')
            ->get();
        $tahunAnggaranOptions = BmnPengajuanrkbmnbagian::select('tahun_anggaran')
            ->distinct()
            ->whereNotNull('tahun_anggaran')
            ->orderBy('tahun_anggaran', 'desc')
            ->get();

        return view('bmn.dashboard', compact(
            'stats',
            'pengajuans',
            'jenisPengajuanOptions', 
            'bagianOptions',
            'statusOptions',
            'tahunAnggaranOptions',
            'filters'
        ));
    }

    private function getStats($query = null)
    {
        $baseQuery = $query ?: BmnPengajuanrkbmnbagian::query();
        
        return [
            'total_pengajuan' => $baseQuery->clone()->distinct('bmn_pengajuanrkbmnbagian.id')->count(),
            'menunggu_persetujuan' => $baseQuery->clone()->whereNotIn('bmn_pengajuanrkbmnbagian.status', ['approved', 'rejected', 'completed'])->distinct('bmn_pengajuanrkbmnbagian.id')->count(),
            'disetujui' => $baseQuery->clone()->whereIn('bmn_pengajuanrkbmnbagian.status', ['approved', 'completed'])->distinct('bmn_pengajuanrkbmnbagian.id')->count(),
            'ditolak' => $baseQuery->clone()->where('bmn_pengajuanrkbmnbagian.status', 'rejected')->distinct('bmn_pengajuanrkbmnbagian.id')->count(),
            'anggaran_disetujui' => $baseQuery->clone()->whereIn('bmn_pengajuanrkbmnbagian.status', ['approved', 'completed'])->sum('total_anggaran')
        ];
    }

    private function getJenisPengajuanOptions()
    {
        return [
            'R1' => 'Tanah dan/atau bangunan perkantoran',
            'R3' => 'Tanah dan/atau gedung rumah negara',
            'R4' => 'Kendaraan Jabatan',
            'R5' => 'Kendaraan Operasional',
            'R6' => 'Kendaraan Fungsional'
        ];
    }
}