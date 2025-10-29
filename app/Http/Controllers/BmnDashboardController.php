<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPengajuanrkbmnbagian;
use App\Models\Bagian;

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
        
        if ($filters['jenis_pengajuan']) {
            $query->where('kode_jenis_pengajuan', 'LIKE', $filters['jenis_pengajuan'] . '%');
        }
        
        if ($filters['bagian']) {
            $query->where('id_bagian_pengusul', $filters['bagian']);
        }
        
        if ($filters['status']) {
            $query->where('bmn_pengajuanrkbmnbagian.status', $filters['status']);
        }
        
        if ($filters['tahun_anggaran']) {
            $query->where('tahun_anggaran', $filters['tahun_anggaran']);
        }
        
        if ($filters['anggaran_min']) {
            $query->where('total_anggaran', '>=', $filters['anggaran_min']);
        }
        
        if ($filters['anggaran_max']) {
            $query->where('total_anggaran', '<=', $filters['anggaran_max']);
        }

        // Ambil data untuk statistik
        $stats = $this->getStats($query);
        
        // Ambil data untuk tabel paginated
        $pengajuans = $query->join('bagian', 'bmn_pengajuanrkbmnbagian.id_bagian_pengusul', '=', 'bagian.id')
            ->select('bmn_pengajuanrkbmnbagian.*', 'bagian.uraianbagian as nama_bagian_pengusul')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10);

        // Ambil data untuk filter options
        $jenisPengajuanOptions = $this->getJenisPengajuanOptions();
        $bagianOptions = Bagian::all();
        $statusOptions = BmnPengajuanrkbmnbagian::select('status')->distinct()->get();
        $tahunAnggaranOptions = BmnPengajuanrkbmnbagian::select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'desc')->get();

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
            'total_pengajuan' => $baseQuery->clone()->count(),
            'pending' => $baseQuery->clone()->where('status', 'pending')->count(),
            'approved' => $baseQuery->clone()->where('status', 'approved')->count(),
            'rejected' => $baseQuery->clone()->where('status', 'rejected')->count(),
            'anggaran_disetujui' => $baseQuery->clone()->whereIn('status', ['approved', 'completed'])->sum('total_anggaran')
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