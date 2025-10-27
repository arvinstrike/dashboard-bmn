<?php

namespace App\Http\Controllers\PerencanaanBMN\BatchSitangguh;

use App\Http\Controllers\Controller;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\EksporBatch\BatchPengajuanSitangguh;
use App\Models\PerencanaanBMN\EksporBatch\BatchPengajuanSitangguhDetail;
use App\Models\PerencanaanBMN\EksporBatch\BatchPengajuanSitangguhLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDF;

class BatchSitangguhController extends Controller
{
    /**
     * Tampilkan daftar pengajuan yang tersedia dan batch yang ada.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Dapatkan pengajuan yang tersedia untuk dibatch (dari cache jika ada)
        $pengajuan = Cache::remember('available_pengajuan', 60*5, function () {
            return Pengajuan::where(function($query) {
                    $query->where('status_pengajuan', 'Disetujui Koordinator BMN')
                        ->orWhere('status_pengajuan', 'Disetujui Unit Perencanaan');
                })
                ->whereNull('batch_id')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        // Dapatkan semua batch untuk monitoring
        $batches = Cache::remember('recent_batches', 60*5, function () {
            return BatchPengajuanSitangguh::orderBy('created_at', 'desc')
                ->with('details') // Eager load batch details
                ->paginate(10);
        });

        return view('PerencanaanBMN.batch.index', compact('pengajuan', 'batches'));
    }

    /**
     * Tampilkan dashboard monitoring batch.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Statistik jumlah batch berdasarkan status
        $totalBatch = BatchPengajuanSitangguh::count();
        $totalDraft = BatchPengajuanSitangguh::where('status', 'draft')->count();
        $totalDikirim = BatchPengajuanSitangguh::where('status', 'dikirim')->count();
        $totalDiproses = BatchPengajuanSitangguh::where('status', 'diproses')->count();
        $totalSelesai = BatchPengajuanSitangguh::where('status', 'selesai')->count();
        $totalDitolak = BatchPengajuanSitangguh::where('status', 'ditolak')->count();

        // Dapatkan 5 batch terbaru
        $latestBatches = BatchPengajuanSitangguh::with('details')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Dapatkan 5 batch dengan nilai terbesar
        $highestValueBatches = BatchPengajuanSitangguh::orderBy('total_nilai_batch', 'desc')
            ->take(5)
            ->get();

        return view('PerencanaanBMN.batch.dashboard', compact(
            'totalBatch', 'totalDraft', 'totalDikirim', 'totalDiproses',
            'totalSelesai', 'totalDitolak', 'latestBatches', 'highestValueBatches'
        ));
    }

    /**
     * Simpan batch baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'keterangan' => 'nullable|string',
            'pengajuan_ids' => 'required|array',
            'pengajuan_ids.*' => 'exists:bmn_pengajuanrkbmnbagian_nonsbsk,id',
        ]);

        // Mulai database transaction
        DB::beginTransaction();

        try {
            // Generate kode batch unik
            $kode_batch = 'BAT-' . date('Ymd') . '-' . str_pad(BatchPengajuanSitangguh::count() + 1, 4, '0', STR_PAD_LEFT);

            // Hitung total nilai menggunakan accessor dari model Pengajuan
            $totalNilai = 0;
            $pengajuanItems = Pengajuan::whereIn('id', $request->pengajuan_ids)->get();
            foreach ($pengajuanItems as $item) {
                $totalNilai += $item->total_nilai;
            }

            // Buat batch baru
            $batch = BatchPengajuanSitangguh::create([
                'kode_batch' => $kode_batch,
                'tanggal_dibuat' => Carbon::now(),
                'created_by' => Auth::id(),
                'status' => 'draft',
                'keterangan' => $request->keterangan,
                'total_nilai_batch' => $totalNilai,
            ]);

            // Tambahkan item pengajuan ke batch
            foreach ($request->pengajuan_ids as $index => $pengajuanId) {
                // Buat detail batch
                BatchPengajuanSitangguhDetail::create([
                    'batch_id' => $batch->id,
                    'pengajuan_id' => $pengajuanId,
                    'urutan' => $index + 1,
                    'status_pengajuan_di_batch' => 'draft',
                ]);

                // Update batch_id pada pengajuan
                $pengajuan = Pengajuan::find($pengajuanId);
                $pengajuan->batch_id = $batch->id;
                $pengajuan->save();
            }

            // Catat log pembuatan batch
            BatchPengajuanSitangguhLog::create([
                'batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'pembuatan',
                'deskripsi' => 'Batch baru dibuat dengan ' . count($request->pengajuan_ids) . ' pengajuan',
                'created_at' => Carbon::now(), // Set waktu pembuatan log
            ]);

            // Hapus cache
            $this->clearBatchCache();

            DB::commit();

            return redirect()->route('batch.show', $batch->id)
                ->with('success', 'Batch berhasil dibuat dengan kode ' . $kode_batch);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal membuat batch: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan detail batch tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $batch = BatchPengajuanSitangguh::findOrFail($id);

        // Eager load relationships untuk performa lebih baik
        $batch->load(['details.pengajuan', 'logs.user', 'creator']);

        return view('PerencanaanBMN.batch.show', compact('batch'));
    }

    /**
     * Tampilkan form untuk mengedit batch.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $batch = BatchPengajuanSitangguh::findOrFail($id);

        // Hanya batch dengan status draft yang dapat diedit
        if ($batch->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya batch dengan status draft yang dapat diedit.');
        }

        // Dapatkan pengajuan yang sudah ada di batch ini
        $selectedPengajuanIds = $batch->details->pluck('pengajuan_id')->toArray();

        // Dapatkan pengajuan yang tersedia (tidak dalam batch lain atau dalam batch ini)
        $availablePengajuan = Pengajuan::where(function($query) {
                $query->where('status_pengajuan', 'Disetujui Koordinator BMN')
                    ->orWhere('status_pengajuan', 'Disetujui Unit Perencanaan');
            })
            ->where(function($query) use ($id) {
                $query->whereNull('batch_id')
                    ->orWhere('batch_id', $id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PerencanaanBMN.batch.edit', compact('batch', 'selectedPengajuanIds', 'availablePengajuan'));
    }

    /**
     * Update batch yang ada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'keterangan' => 'nullable|string',
            'pengajuan_ids' => 'required|array',
            'pengajuan_ids.*' => 'exists:bmn_pengajuanrkbmnbagian_nonsbsk,id',
        ]);

        $batch = BatchPengajuanSitangguh::findOrFail($id);

        // Hanya batch dengan status draft yang dapat diupdate
        if ($batch->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya batch dengan status draft yang dapat diupdate.');
        }

        DB::beginTransaction();
        try {
            // Update info batch
            $batch->keterangan = $request->keterangan;

            // Hitung ulang total nilai menggunakan accessor
            $totalNilai = 0;
            $pengajuanItems = Pengajuan::whereIn('id', $request->pengajuan_ids)->get();
            foreach ($pengajuanItems as $item) {
                $totalNilai += $item->total_nilai;
            }

            $batch->total_nilai_batch = $totalNilai;
            $batch->save();

            // Dapatkan ID pengajuan yang saat ini ada di batch
            $currentPengajuanIds = $batch->details->pluck('pengajuan_id')->toArray();

            // Hapus pengajuan yang dikeluarkan dari batch
            $removedPengajuanIds = array_diff($currentPengajuanIds, $request->pengajuan_ids);
            if (!empty($removedPengajuanIds)) {
                BatchPengajuanSitangguhDetail::where('batch_id', $batch->id)
                    ->whereIn('pengajuan_id', $removedPengajuanIds)
                    ->delete();

                // Reset batch_id pada pengajuan yang dikeluarkan
                Pengajuan::whereIn('id', $removedPengajuanIds)
                    ->update(['batch_id' => null]);
            }

            // Tambahkan pengajuan baru ke batch
            $newPengajuanIds = array_diff($request->pengajuan_ids, $currentPengajuanIds);
            $urutan = $batch->details->max('urutan') ?? 0;

            foreach ($newPengajuanIds as $pengajuanId) {
                $urutan++;
                BatchPengajuanSitangguhDetail::create([
                    'batch_id' => $batch->id,
                    'pengajuan_id' => $pengajuanId,
                    'urutan' => $urutan,
                    'status_pengajuan_di_batch' => 'draft',
                ]);

                // Update batch_id pada pengajuan baru
                $pengajuan = Pengajuan::find($pengajuanId);
                $pengajuan->batch_id = $batch->id;
                $pengajuan->save();
            }

            // Catat log update
            BatchPengajuanSitangguhLog::create([
                'batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'update',
                'deskripsi' => 'Batch diupdate dengan ' . count($request->pengajuan_ids) . ' pengajuan',
                'created_at' => Carbon::now(),
            ]);

            // Hapus cache
            $this->clearBatchCache();

            DB::commit();
            return redirect()->route('batch.show', $batch->id)
                ->with('success', 'Batch berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengupdate batch: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Kirim batch ke Sitangguh.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendToSitangguh($id)
    {
        $batch = BatchPengajuanSitangguh::findOrFail($id);

        // Hanya batch dengan status draft yang dapat dikirim
        if ($batch->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya batch dengan status draft yang dapat dikirim ke Sitangguh.');
        }

        DB::beginTransaction();

        try {
            // Update status batch
            $batch->status = 'dikirim';
            $batch->tanggal_dikirim = Carbon::now();
            $batch->save();

            // Update status detail
            $batch->details()->update(['status_pengajuan_di_batch' => 'dikirim']);

            // Catat log pengiriman
            BatchPengajuanSitangguhLog::create([
                'batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'pengiriman',
                'deskripsi' => 'Batch dikirim ke Sitangguh',
                'status_sebelum' => 'draft',
                'status_sesudah' => 'dikirim',
                'created_at' => Carbon::now(),
            ]);

            // Hapus cache
            $this->clearBatchCache();

            DB::commit();

            return redirect()->route('batch.show', $batch->id)
                ->with('success', 'Batch berhasil dikirim ke Sitangguh.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal mengirim batch ke Sitangguh: ' . $e->getMessage());
        }
    }

    /**
     * Tolak batch dari Sitangguh.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectFromSitangguh($id, Request $request)
    {
        $batch = BatchPengajuanSitangguh::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update status batch
            $batch->status = 'ditolak';
            $batch->save();

            // Reset status pengajuan
            foreach($batch->details as $detail) {
                $pengajuan = $detail->pengajuan;
                $pengajuan->status_pengajuan = 'Ditolak Sitangguh';
                $pengajuan->batch_id = null;
                $pengajuan->save();

                // Update status detail
                $detail->status_pengajuan_di_batch = 'ditolak';
                $detail->save();
            }

            // Catat log penolakan
            BatchPengajuanSitangguhLog::create([
                'batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'penolakan',
                'deskripsi' => 'Batch ditolak Sitangguh: ' . $request->alasan_penolakan,
                'status_sebelum' => 'dikirim',
                'status_sesudah' => 'ditolak',
                'created_at' => Carbon::now(),
            ]);

            // Hapus cache
            $this->clearBatchCache();

            DB::commit();
            return redirect()->route('batch.show', $batch->id)
                ->with('success', 'Batch berhasil diproses sebagai ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    /**
     * Terima batch dari Sitangguh.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptFromSitangguh($id)
    {
        $batch = BatchPengajuanSitangguh::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update status batch
            $batch->status = 'selesai';
            $batch->tanggal_selesai = Carbon::now();
            $batch->save();

            // Update status detail
            $batch->details()->update(['status_pengajuan_di_batch' => 'selesai']);

            // Catat log penerimaan
            BatchPengajuanSitangguhLog::create([
                'batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'penerimaan',
                'deskripsi' => 'Batch diterima dan diproses Sitangguh',
                'status_sebelum' => 'dikirim',
                'status_sesudah' => 'selesai',
                'created_at' => Carbon::now(),
            ]);

            // Hapus cache
            $this->clearBatchCache();

            DB::commit();
            return redirect()->route('batch.show', $batch->id)
                ->with('success', 'Batch berhasil diproses dan diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memproses penyelesaian: ' . $e->getMessage());
        }
    }

    /**
     * Export detail batch ke PDF.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id)
    {
        $batch = BatchPengajuanSitangguh::with(['details.pengajuan', 'logs.user', 'creator'])
            ->findOrFail($id);

        $pdf = PDF::loadView('PerencanaanBMN.batch.pdf', compact('batch'));
        return $pdf->download('batch-'.$batch->kode_batch.'.pdf');
    }

    /**
     * Hapus cache terkait batch.
     *
     * @return void
     */
    private function clearBatchCache()
    {
        Cache::forget('available_pengajuan');
        Cache::forget('recent_batches');
    }
}
