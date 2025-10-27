<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian\NonSBSK;

use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilPengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilRevisi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Auth;

class MonitoringController extends Controller
{
    /**
     * Display monitoring dashboard with all pengajuan
     */
    public function index()
    {
        // Get all pengajuan with related data using eager loading
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $statistics = [
            'total' => $pengajuan->count(),
            'pending' => $pengajuan->whereIn('status_pengajuan', [
                'Diajukan ke Pelaksana',
                'Diajukan ke Koordinator',
                'Diajukan ke Unit Perencanaan'
            ])->count(),
            'approved' => $pengajuan->whereIn('status_pengajuan', [
                'Disetujui',
                'Disetujui Koordinator BMN'
            ])->count(),
            'rejected' => $pengajuan->filter(function($item) {
                return strpos($item->status_pengajuan, 'Ditolak') !== false;
            })->count()
        ];

        // Add calculated fields for each pengajuan
        foreach ($pengajuan as $item) {
            // Get bagian names via direct query (since relationships might not be defined)
            $bagianPengusul = DB::table('bagian')->where('id', $item->id_bagian_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $item->id_bagian_pelaksana)->first();
            $biroPengusul = DB::table('biro')->where('id', $item->id_biro_pengusul)->first();
            $biroPelaksana = DB::table('biro')->where('id', $item->id_biro_pelaksana ?? 0)->first();

            $item->bagian_pengusul_nama = $bagianPengusul ? $bagianPengusul->uraianbagian : '-';
            $item->bagian_pelaksana_nama = $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-';
            $item->biro_pengusul_nama = $biroPengusul ? $biroPengusul->uraianbiro : '-';
            $item->biro_pelaksana_nama = $biroPelaksana ? $biroPelaksana->uraianbiro : '-';

            // Calculate total budget
            $totalAnggaran = 0;
            if ($item->tipe_pengajuan === 'usulan') {
                foreach ($item->detilPengajuan as $detail) {
                    $totalAnggaran += $detail->kuantitas * $detail->harga;
                }
            } else {
                foreach ($item->detilRevisi as $detail) {
                    $totalAnggaran += $detail->kuantitas * $detail->harga;
                }
            }
            $item->total_anggaran = $totalAnggaran;

            // Add status badge class
            $item->status_badge_class = $this->getStatusBadgeClass($item->status_pengajuan);

            // Add progress percentage
            $item->progress_percentage = $this->getProgressPercentage($item->status_pengajuan);
        }

        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.Monitoring.index', compact('pengajuan', 'statistics'));
    }

    /**
     * Show review page for specific pengajuan
     */
    public function review($id)
    {
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);
        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.Monitoring.review', compact('pengajuan'));
    }

    /**
     * Show detailed view of pengajuan (for AJAX or separate page)
     */
    public function show($id)
    {
        try {
            // Cari data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Siapkan data terkait
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();

            // Format tanggal pengajuan
            $tanggalPengajuan = $pengajuan->created_at ? $pengajuan->created_at->format('d-m-Y H:i') : '-';

            // Ambil data detil pengajuan jika ada
            $detilPengajuan = [];
            if (count($pengajuan->detilPengajuan) > 0) {
                foreach ($pengajuan->detilPengajuan as $index => $item) {
                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                        ->where('kode_perlengkapan', $item->kode_perlengkapan)
                        ->first();

                    $itemTotal = $item->kuantitas * $item->harga;

                    $detilPengajuan[] = [
                        'no' => $index + 1,
                        'id' => $item->id,
                        'kode_perlengkapan' => $item->kode_perlengkapan,
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $itemTotal,
                        'path_image' => $item->path_image
                    ];
                }
            }

            // Ambil data detil revisi jika ada
            $detilRevisi = [];
            if (count($pengajuan->detilRevisi) > 0) {
                foreach ($pengajuan->detilRevisi as $index => $item) {
                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                        ->where('kode_perlengkapan', $item->kode_perlengkapan)
                        ->first();

                    $detilRevisi[] = [
                        'no' => $index + 1,
                        'id' => $item->id,
                        'kode_perlengkapan' => $item->kode_perlengkapan,
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total,
                        'path_image' => $item->path_image
                    ];
                }
            }

            // Hitung total anggaran
            $totalAnggaranPengajuan = 0;
            foreach ($detilPengajuan as $item) {
                $totalAnggaranPengajuan += $item['total'];
            }

            $totalAnggaranRevisi = 0;
            foreach ($detilRevisi as $item) {
                $totalAnggaranRevisi += $item['total'];
            }

            // Siapkan data untuk response
            $response = [
                'id' => $pengajuan->id,
                'tipe_pengajuan' => ucfirst($pengajuan->tipe_pengajuan),
                'tahun_anggaran' => $pengajuan->tahun_anggaran,
                'status_pengajuan' => $pengajuan->status_pengajuan ?: 'Draft',
                'kode_pengenal' => $pengajuan->kode_pengenal ?: '-',
                'kode_akun' => $pengajuan->kode_akun ?: '-',
                'tanggal_pengajuan' => $tanggalPengajuan,
                'bagian_pengusul' => $bagianPengusul ? $bagianPengusul->uraianbagian : '-',
                'biro_pengusul' => $biroPengusul ? $biroPengusul->uraianbiro : '-',
                'bagian_pelaksana' => $bagianPelaksana ? $bagianPelaksana->uraianbagian : '-',
                'biro_pelaksana' => $biroPelaksana ? $biroPelaksana->uraianbiro : '-',
                'keterangan' => $pengajuan->keterangan ?: '-',
                'detil_pengajuan' => $detilPengajuan,
                'detil_revisi' => $detilRevisi,
                'total_anggaran_pengajuan' => $totalAnggaranPengajuan,
                'total_anggaran_revisi' => $totalAnggaranRevisi,
                'alasan_penolakan_pelaksana' => $pengajuan->alasan_penolakan_pelaksana,
                'alasan_penolakan_koordinator' => $pengajuan->alasan_penolakan_koordinator,
                'alasan_penolakan_perencanaan' => $pengajuan->alasan_penolakan_perencanaan
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada show pengajuan monitoring: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status pengajuan (optional - for advanced control)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            Log::info('Monitoring - Update status pengajuan ID: ' . $id);
            Log::info('Request payload:', $request->all());

            // Validasi input
            $validator = Validator::make($request->all(), [
                'status' => 'required|string',
                'catatan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Update status
            $pengajuan->status_pengajuan = $request->status;

            // Add notes if provided
            if ($request->filled('catatan')) {
                $pengajuan->keterangan = $pengajuan->keterangan . "\n\n[Monitoring - " . date('d/m/Y H:i') . "]: " . $request->catatan;
            }

            $pengajuan->save();

            DB::commit();

            Log::info('Status pengajuan berhasil diupdate via monitoring: ' . $pengajuan->status_pengajuan);

            return response()->json([
                'success' => true,
                'message' => 'Status pengajuan berhasil diupdate',
                'status_pengajuan' => $pengajuan->status_pengajuan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada updateStatus monitoring: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status badge class based on status
     */
    private function getStatusBadgeClass($status)
    {
        $statusClasses = [
            'Draft' => 'secondary',
            'Diajukan ke Pelaksana' => 'warning',
            'Diajukan ke Koordinator' => 'info',
            'Diajukan ke Unit Perencanaan' => 'primary',
            'Disetujui' => 'success',
            'Disetujui Koordinator BMN' => 'success',
        ];

        // Handle rejection statuses
        if (strpos($status, 'Ditolak') !== false) {
            return 'danger';
        }

        return $statusClasses[$status] ?? 'secondary';
    }

    /**
     * Get progress percentage based on status
     */
    private function getProgressPercentage($status)
    {
        $progressMap = [
            'Draft' => 10,
            'Diajukan ke Pelaksana' => 30,
            'Diajukan ke Koordinator' => 60,
            'Diajukan ke Unit Perencanaan' => 80,
            'Disetujui' => 100,
            'Disetujui Koordinator BMN' => 100,
        ];

        // Handle rejection statuses
        if (strpos($status, 'Ditolak') !== false) {
            return 0;
        }

        return $progressMap[$status] ?? 0;
    }
}
