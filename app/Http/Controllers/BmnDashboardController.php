<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BmnDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil data untuk filter
        $bagians = DB::table('bmn_pengajuanrkbmnbagian')->select('id_bagian_pengusul')->distinct()->get();
        $statuses = DB::table('bmn_pengajuanrkbmnbagian')->select('status')->distinct()->get();

        // Query dasar
        $query = DB::table('bmn_pengajuanrkbmnbagian')
            ->select(
                'id',
                'tanggal_pengajuan',
                'id_bagian_pengusul',
                'uraian_barang',
                'total_anggaran',
                'status',
                'tahun_anggaran'
            );

        // Terapkan filter
        if ($request->filled('bagian')) {
            $query->where('id_bagian_pengusul', $request->bagian);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('anggaran_min')) {
            $query->where('total_anggaran', '>=', $request->anggaran_min);
        }

        if ($request->filled('anggaran_max')) {
            $query->where('total_anggaran', '<=', $request->anggaran_max);
        }

        // Ambil data hasil query
        $pengajuans = $query->orderBy('tanggal_pengajuan', 'desc')->paginate(15);

        return view('bmn.dashboard', [
            'pengajuans' => $pengajuans,
            'bagians' => $bagians,
            'statuses' => $statuses,
            'filters' => $request->only(['bagian', 'status', 'anggaran_min', 'anggaran_max'])
        ]);
    }
}
