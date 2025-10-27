<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian;

use App\Http\Controllers\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use GuzzleHttp\Client;

// Models
use App\Models\PerencanaanBMN\Admin\PengajuanRKBMNModel;
use App\Models\PerencanaanBMN\Admin\ReferensiBMNRKModel;
use App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel;
use App\Models\PerencanaanBMN\Bagian\KantorModel;
use App\Models\PerencanaanBMN\Bagian\KendaraanJabatanModel;
use App\Models\PerencanaanBMN\Bagian\KendaraanOperasionalModel;
use App\Models\PerencanaanBMN\Bagian\KendaraanFungsionalModel;
use App\Models\PerencanaanBMN\Bagian\RumahNegaraModel;
use App\Models\ReferensiAnggaran\KegiatanModel;
use App\Models\ReferensiAnggaran\ProgramModel;
use App\Models\ReferensiAnggaran\OutputModel;
use App\Models\Sirangga\Admin\BarangModel;
use App\Models\ReferensiUnit\BagianModel;

// Referensi pemilihan barang SBSK
use App\Models\PerencanaanBMN\FilterBarang\Golongan;
use App\Models\PerencanaanBMN\FilterBarang\Bidang;
use App\Models\PerencanaanBMN\FilterBarang\Kelompok;
use App\Models\PerencanaanBMN\FilterBarang\SubKelompok;
use App\Models\PerencanaanBMN\FilterBarang\Barang;

/**
 * =====================================================
 * PENGAJUAN RK BMN BAGIAN CONTROLLER
 * =====================================================
 * Controller untuk mengelola pengajuan RK BMN Bagian
 * dengan dukungan form terpisah untuk setiap jenis pengajuan (R1-R6)
 *
 * Refactored: Modal components → Separate pages
 * Author: System
 * Version: 2.3 (Cleaned up method naming)
 */
class PengajuanRKBMNBagianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // ==========================================
    // CRUD OPERATIONS
    // ==========================================

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $judul = 'Data Pengajuan Kebutuhan BMN';

        if ($request->ajax()) {
            $idBagian = Auth::user()->idbagian;
            $data = PengajuanRKBMNBagianModel::select([
                'id',
                'kode_jenis_pengajuan',
                'status',
                'id_bagian_pelaksana',
                'total_anggaran',
                'created_at',
                'uraian_barang'
            ])->where('id_bagian_pelaksana', $idBagian); // <-- PERBAIKAN: Menggunakan id_bagian_pelaksana

            return Datatables::of($data)
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="'.route('pengajuanrkbmnbagian.review', $row->id).'" class="btn btn-info btn-sm">Review</a>';

                    if (in_array($row->status, ["Draft", "Ditolak oleh Koordinator"])) {
                        $btn .= '<a href="'.route('pengajuanrkbmnbagian.edit', $row->id).'" class="btn btn-warning btn-sm">Edit</a>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('idbagianpelaksana', function ($row){
                    $uraianBagian = DB::table('bagian')->where('id', '=', $row->id_bagian_pelaksana)->value('uraianbagian');
                    return $uraianBagian ?? 'Tidak Ditemukan';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('PerencanaanBMN.Bagian.pengajuanrkbmn.DashboardPengajuanRKBMN', compact('judul'));
    }

    /**
     * Show the form for creating a new resource (separate page).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $user = Auth::user();

            // Query langsung ke database (bukan pakai relasi)
            $uraianBiro = DB::table('biro')->where('id', $user->idbiro)->value('uraianbiro') ?? 'Biro tidak tersedia';
            $uraianBagian = DB::table('bagian')->where('id', $user->idbagian)->value('uraianbagian') ?? 'Bagian tidak tersedia';

            $tahunAnggaran = session('tahunanggaran', date('Y'));

            return view('PerencanaanBMN.Bagian.pengajuanrkbmn.CreateFormRKBMN', compact(
                'uraianBiro',
                'uraianBagian',
                'tahunAnggaran'
            ));

        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return redirect()->route('pengajuanrkbmnbagian.index')
                ->with('error', 'Terjadi kesalahan saat memuat halaman create: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * Handles standardized field names and validation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $jenistabel = $request->get('jenistabel');

            // Initialize SBSK engine dengan proper error handling
            try {
                $sbskEngine = app(\App\Services\SBSKRuleService::class);

                // SBSK validation
                $sbskErrors = $sbskEngine->validateFormData($request->all(), $jenistabel);
                if (!empty($sbskErrors)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validasi SBSK gagal',
                        'errors' => $sbskErrors,
                    ], 422);
                }
            } catch (\Exception $e) {
                Log::warning('SBSK Engine not available: ' . $e->getMessage());
                // Continue without SBSK validation if service is not available
            }

            // Enhanced validation rules dengan standardized fields
            $basicRules = [
                'jenistabel' => 'required|in:R1,R2,R3,R4,R5,R6',
                'tahun_anggaran' => 'required|numeric',
                'harga_barang' => 'required|numeric|min:1',
                'kuantitas' => 'required|numeric|min:1',
                'uraian_barang' => 'required|string|max:1000', // Standardized field name
            ];

            // Dynamic validation rules berdasarkan jenis
            $additionalRules = $this->getValidationRules($jenistabel);
            $rules = array_merge($basicRules, $additionalRules);

            // Process currency fields before validation
            if ($request->has('harga_barang')) {
                $hargaBarang = preg_replace('/[^\d]/', '', $request->input('harga_barang'));
                $request->merge(['harga_barang' => $hargaBarang]);
            }

            if ($request->has('total_anggaran')) {
                $totalAnggaran = preg_replace('/[^\d]/', '', $request->input('total_anggaran'));
                $request->merge(['total_anggaran' => $totalAnggaran]);
            }

            // Set default values untuk R4/R5/R6 if needed
            if (in_array($jenistabel, ['R4', 'R5', 'R6'])) {
                if (!$request->has('tujuan_rencana') && !in_array($jenistabel, ['R4'])) {
                    $request->merge(['tujuan_rencana' => 'Khusus Lainnya']);
                }
                if (!$request->has('atr_nonatr') && !in_array($jenistabel, ['R4'])) {
                    $request->merge(['atr_nonatr' => 'Non ATR']);
                }
            }

            // R4 tidak memerlukan tujuan_rencana dan atr_nonatr
            if ($jenistabel === 'R4') {
                unset($rules['tujuan_rencana']);
                unset($rules['atr_nonatr']);
            }

            $request->validate($rules);

            // Generate pengajuan code
            $count = $this->getPengajuanCount($jenistabel);
            $kodePengajuan = $this->generateKodePengajuan($jenistabel, $count);

            // Prepare pengajuan data dengan standardized fields
            $pengajuanData = $this->preparePengajuanData($request, $kodePengajuan);
            $pengajuan = PengajuanRKBMNBagianModel::create($pengajuanData);

            // Store detail data berdasarkan jenis
            $this->storeDetailData($request, $kodePengajuan, $jenistabel);

            // Handle file upload if present
            if ($request->hasFile('dokumen_pendukung')) {
                $this->handleFileUpload($request, $pengajuan);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan RKBMN berhasil disimpan',
                'data' => $pengajuan
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in store method: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data = PengajuanRKBMNBagianModel::with([
                'bangunanKantor', 'rumahNegara', 'kendaraanJabatan',
                'kendaraanOperasional', 'kendaraanFungsional'
            ])->findOrFail($id);

            // Kondisi untuk bisa mengakses halaman edit
            if (!in_array($data->status, ["Draft", "Ditolak oleh Koordinator"])) {
                return redirect()->route('pengajuanrkbmnbagian.index')
                    ->with('error', 'Pengajuan ini tidak dapat diedit karena statusnya bukan "Draft" atau "Ditolak".');
            }

            $user = Auth::user();
            $uraianBiro = optional($user->biro)->uraianbiro ?? 'Biro tidak tersedia';
            $uraianBagian = optional($user->bagian)->uraianbagian ?? 'Bagian tidak tersedia';

            $jenis = substr($data->kode_jenis_pengajuan, 0, 2);

            return view('PerencanaanBMN.Bagian.pengajuanrkbmn.EditFormRKBMN', compact(
                'data',
                'uraianBiro',
                'uraianBagian',
                'jenis'
            ));

        } catch (\Exception $e) {
            Log::error('Error in PengajuanRKBMNBagianController@edit: ' . $e->getMessage());
            return redirect()->route('pengajuanrkbmnbagian.index')->with('error', 'Gagal memuat halaman edit.');
        }
    }

    /**
     * Display the specified resource for review page.
     * Modified to handle both AJAX and web requests
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        try {
            // Ambil data utama
            $data = PengajuanRKBMNBagianModel::with([
                'biroPengusul',
                'biroPelaksana',
                'bagianPengusul',
                'bagianPelaksana'
            ])->find($id);

            if (!$data) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Data tidak ditemukan'], 404);
                }
                return redirect()->route('pengajuanrkbmnbagian.index')
                    ->with('error', 'Data tidak ditemukan');
            }

            // Ambil jenis form dari kode_jenis_pengajuan
            $jenisForm = $data->kode_jenis_pengajuan;

            // Ambil data detail berdasarkan jenis form menggunakan method yang sudah ada
            $detailData = $this->getDetailData($data, $jenisForm);

            // Ambil data tambahan menggunakan method yang sudah ada
            $additionalData = $this->getAdditionalShowData($data);

            // Ambil informasi barang
            $barangInfo = $this->getBarangInfo($data);

            // Jika request AJAX (existing behavior)
            if ($request->ajax()) {
                return response()->json(array_merge([
                    'success' => true,
                    'data' => $data,
                    'detailData' => $detailData,
                    'jenisForm' => $jenisForm,
                    'barangInfo' => $barangInfo
                ], $additionalData));
            }

            // Jika request web (new behavior untuk review page)
            return view('PerencanaanBMN.Bagian.pengajuanrkbmn.ReviewPageRKBMN', compact(
                'data',
                'detailData',
                'jenisForm',
                'barangInfo'
            ));

        } catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            return redirect()->route('pengajuanrkbmnbagian.index')
                ->with('error', 'Data tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error di show method: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('pengajuanrkbmnbagian.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);
            $jenis = substr($pengajuan->kode_jenis_pengajuan, 0, 2);

            if ($request->has('harga_barang')) {
                $hargaBarangClean = preg_replace('/[^\d]/', '', $request->input('harga_barang'));
                $request->merge(['harga_barang' => $hargaBarangClean]);
            }

            $validationRules = [
                'harga_barang' => 'required|numeric|min:1',
                'kuantitas' => 'required|numeric|min:1',
                'uraian_barang' => 'required|string|max:1000',
            ];

            if (in_array($jenis, ['R4', 'R5', 'R6'])) {
                $validationRules['skema'] = 'required|string|in:beli,sewa';
            }
            $request->validate($validationRules);

            // Update data utama pada tabel pengajuanrkbmnbagian
            $pengajuan->kuantitas = $request->kuantitas;
            $pengajuan->harga_barang = $request->harga_barang;
            $pengajuan->total_anggaran = $request->kuantitas * $request->harga_barang;
            $pengajuan->uraian_barang = $request->uraian_barang;
            $pengajuan->keterangan = $request->keterangan;

            if ($request->has('skema')) {
                $pengajuan->skema = $request->skema;
            }
            if ($request->has('tujuan_rencana')) {
                $pengajuan->tujuan_rencana = $request->tujuan_rencana;
            }
            if ($request->has('atr_nonatr')) {
                $pengajuan->atr_nonatr = $request->atr_nonatr;
            }


            if ($pengajuan->status === 'Ditolak oleh Koordinator') {
                $pengajuan->status = 'Draft';
            }

            if ($request->hasFile('dokumen_pendukung')) {
                if ($pengajuan->dokumen_pendukung && Storage::disk('local')->exists($pengajuan->dokumen_pendukung)) {
                    Storage::disk('local')->delete($pengajuan->dokumen_pendukung);
                }
                $file = $request->file('dokumen_pendukung');
                $namaFile = $pengajuan->kode_jenis_pengajuan . '_dokumenpendukung.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('dokumenpendukung_rkbmn', $namaFile, 'local');
                $pengajuan->dokumen_pendukung = $path;
            }

            $pengajuan->save();

            // Update data detail
            $this->updateDetailData($request, $jenis, $pengajuan->kode_jenis_pengajuan);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan RKBMN berhasil diperbarui.',
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update method: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Delete the supporting document for a submission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteDokumen($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            if ($pengajuan->dokumen_pendukung) {
                if (Storage::disk('local')->exists($pengajuan->dokumen_pendukung)) {
                    Storage::disk('local')->delete($pengajuan->dokumen_pendukung);
                }
                $pengajuan->dokumen_pendukung = null;
                $pengajuan->save();

                return response()->json([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Dokumen berhasil dihapus.'
                ]);
            }
            return response()->json([
                'success' => false,
                'status' => 'info',
                'message' => 'Tidak ada dokumen untuk dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus dokumen.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = PengajuanRKBMNBagianModel::findOrFail($id);
            $kodeJenis = $data->kode_jenis_pengajuan;

            // Tentukan tabel detail berdasarkan jenis pengajuan
            $detailTable = $this->getDetailTableName($kodeJenis);

            // Hapus data dari tabel detail
            if ($detailTable) {
                DB::table($detailTable)->where('kode_jenis_pengajuan', $kodeJenis)->delete();
            }

            // Hapus data dari tabel utama
            $data->delete();

            DB::commit();
            return response()->json(['status' => 'berhasil']);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => 'Data tidak ditemukan.'], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // AJAX & WORKFLOW METHODS
    // ==========================================
    /**
     * Get detail data berdasarkan kode jenis lengkap
     *
     * @param string $kodeJenis - Format: R1-009, R3-001, etc.
     * @param string $jenis - Format: R1, R3, etc.
     * @return object|null
     */
    private function getDetailDataByKode($kodeJenis, $jenis)
    {
        try {
            switch ($jenis) {
                case 'R1':
                    return KantorModel::where('kode_jenis_pengajuan', $kodeJenis)->first();

                case 'R3':
                    return RumahNegaraModel::where('kode_jenis_pengajuan', $kodeJenis)->first();

                case 'R4':
                    return KendaraanJabatanModel::where('kode_jenis_pengajuan', $kodeJenis)->first();

                case 'R5':
                    return KendaraanOperasionalModel::where('kode_jenis_pengajuan', $kodeJenis)->first();

                case 'R6':
                    return KendaraanFungsionalModel::where('kode_jenis_pengajuan', $kodeJenis)->first();

                case 'R2':
                    // R2 tidak diimplementasi
                    return null;

                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error getting detail data for {$kodeJenis}: " . $e->getMessage());
            return null;
        }
    }

        /**
     * Get the edit form component dynamically via AJAX.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditFormComponent($id)
    {
        try {
            $data = PengajuanRKBMNBagianModel::findOrFail($id);
            $jenis = substr($data->kode_jenis_pengajuan, 0, 2);
            $detailData = $this->getDetailData($data, $data->kode_jenis_pengajuan);

            // Mengambil kode-kode SBSK untuk pre-selection dropdown di view
            $barang = \App\Models\PerencanaanBMN\FilterBarang\Barang::where('kd_brg', $data->kode_barang)->first();
            $sbskCodes = [
                'gol'  => optional($barang)->kd_gol,
                'bid'  => optional($barang)->kd_bid,
                'kel'  => optional($barang)->kd_kel,
                'skel' => optional($barang)->kd_skel,
                'brg'  => optional($barang)->kd_brg,
            ];

            $viewName = "PerencanaanBMN.Bagian.pengajuanrkbmn.components.edit.EditComponent{$jenis}";

            if (!view()->exists($viewName)) {
                Log::error("View not found for edit component: {$viewName}");
                return response()->json(['success' => false, 'message' => "Komponen form edit untuk {$jenis} tidak ditemukan."], 404);
            }

            $html = view($viewName, compact('data', 'detailData'))->render();

            // Mengembalikan sbskCodes bersama HTML agar bisa diolah oleh JavaScript di form induk
            return response()->json(['success' => true, 'html' => $html, 'sbskCodes' => $sbskCodes]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Error in getEditFormComponent: Pengajuan not found for ID ' . $id);
            return response()->json(['success' => false, 'message' => 'Data pengajuan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error in getEditFormComponent: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat komponen form edit.'], 500);
        }
    }

    /**
     * Get review component content for a specific submission code.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $kodeJenis - e.g., R1-009, R3-001, etc.
     * @return \Illuminate\Http\Response
     */
    public function getReviewComponent(Request $request, $kodeJenis)
    {
        try {
            Log::info("Loading review component for kode jenis: {$kodeJenis}");

            // Extract jenis (R1, R2, etc.) from the full code (R1-009)
            preg_match('/^(R[1-6])/', $kodeJenis, $matches);
            $jenis = $matches[1] ?? '';

            if (empty($jenis)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format kode jenis tidak valid: ' . $kodeJenis
                ], 400);
            }

            Log::info("Extracted jenis: {$jenis} from kode: {$kodeJenis}");

            // Query detail data using the full code
            $detailData = $this->getDetailDataByKode($kodeJenis, $jenis);

            Log::info("Detail data retrieved:", ['detail_data' => $detailData]);

            // Main data from request (optional, fallback to query if needed)
            $data = $request->input('data', []);
            $data = (object) $data;

            // Determine view name based on the extracted jenis
            $viewName = "PerencanaanBMN.Bagian.pengajuanrkbmn.components.review.ReviewComponent{$jenis}";

            Log::info("Trying to load view: {$viewName}");

            if (!view()->exists($viewName)) {
                Log::error("View not found: {$viewName}");
                return response()->json([
                    'success' => false,
                    'message' => "Component untuk {$jenis} tidak ditemukan di path: {$viewName}"
                ], 404);
            }

            $html = view($viewName, compact('data', 'detailData'))->render();

            Log::info("View rendered successfully for {$jenis}");

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getReviewComponent: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat component: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit pengajuan to next stage (using existing method pattern)
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function submitPengajuan($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::find($id);

            if (!$pengajuan) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            if ($pengajuan->status !== 'Draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan dengan status Draft yang dapat diajukan'
                ], 400);
            }

            // Update status menggunakan pattern yang sama dengan method kirimkepelaksanapengadaan
            $pengajuan->status = 'Diajukan ke Koordinator';
            $pengajuan->tanggal_pengajuan = now();
            $pengajuan->save();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil diajukan ke Koordinator BMN '
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting pengajuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan pengajuan'
            ], 500);
        }
    }

    // ==========================================
    // MAGIC LINK E-SIGNATURE METHODS
    // ==========================================

     /**
     * Send Magic Link Verification via WhatsApp for SBSK
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMagicLinkVerification($id)
    {
        try {
            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            // Validasi status pengajuan - HANYA 2 status yang diizinkan untuk SBSK
            $allowedStatuses = ['Draft', 'Ditolak oleh Koordinator'];
            if (!in_array($pengajuan->status, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Magic Link hanya dapat dikirim untuk pengajuan dengan status: ' .
                        implode(', ', $allowedStatuses)
                ], 400);
            }

            // Ambil data Eselon III dari bagian pengusul dengan validation
            $eselonIII = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->select('nama', 'nip', 'phone')
                ->first();

            // ======================= BLOK TESTING DENGAN DATA STATIS =======================

            // UNCOMMENT BLOK DI BAWAH INI UNTUK TESTING TANPA QUERY DATABASE

            // Skenario 1: Eselon III ditemukan dengan nomor telepon
//             $eselonIII = (object) [
//                 'nama' => 'Testing Nama',
//                 'nip' => '123452341234',
//                 'phone' => '081280974849' // Ganti dengan nomor statis untuk testing
//             ];

            // Skenario 2: Eselon III ditemukan TANPA nomor telepon (untuk menguji kondisi !$eselonIII->phone)
            // $eselonIII = (object) [
            //     'nama' => 'Nama Eselon III Statis Tanpa Phone',
            //     'nip' => '198765432109876543',
            //     'phone' => null // atau '' (string kosong), tergantung bagaimana data disimpan
            // ];

            // Skenario 3: Eselon III TIDAK ditemukan (untuk menguji kondisi !$eselonIII)
            // $eselonIII = null;

            // Pastikan Anda meng-comment kembali kode database di atas jika menggunakan blok testing ini
            // dan hanya uncomment salah satu skenario di atas pada satu waktu.
            // ======================= AKHIR BLOK TESTING =======================

            if (!$eselonIII) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Eselon III dari bagian pengusul tidak ditemukan. Pastikan data pegawai sudah terdaftar dengan benar.'
                ], 404);
            }

            if (empty($eselonIII->nama)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama Eselon III tidak valid. Silakan hubungi administrator.'
                ], 400);
            }

            if (empty($eselonIII->nip)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIP Eselon III tidak valid. Silakan hubungi administrator.'
                ], 400);
            }

            if (empty($eselonIII->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp Eselon III belum terdaftar. Silakan hubungi administrator untuk melengkapi data.'
                ], 400);
            }

            // Buat record magic link verification sesuai struktur tabel yang benar
            $expiresAt = now()->addHours(12);
            $verificationData = [
                'pengajuan_id' => $id,
                'verification_level' => 'sbsk_operator', // Custom naming untuk SBSK
                'documents_to_sign' => json_encode(['berita_acara']), // Hanya berita acara untuk SBSK
                'eselon_iii_nip' => $eselonIII->nip,
                'eselon_iii_name' => $eselonIII->nama,
                'eselon_iii_phone' => $eselonIII->phone,
                'triggered_by_user_id' => Auth::id(),
                'expires_at' => $expiresAt,
                'status' => 'pending'
            ];

            $magicLinkVerification = DB::table('magic_link_verifications')->insertGetId($verificationData);

            // Buat encrypted token
            $tokenData = json_encode([
                'verification_id' => $magicLinkVerification,
                'pengajuan_id' => $id,
                'expires_at' => $expiresAt->timestamp
            ]);

            $compressedData = gzcompress($tokenData);
            $encryptedToken = Crypt::encryptString(base64_encode($compressedData));

            // Update record dengan encrypted token (menggunakan struktur yang benar)
            DB::table('magic_link_verifications')
                ->where('id', $magicLinkVerification)
                ->update([
                    'encrypted_token' => $encryptedToken,
                    'sent_at' => now()
                ]);

            // Buat link verifikasi untuk SBSK
            $verificationLink = url("pengajuanrkbmnbagian/magic-link-sbsk/{$encryptedToken}");

            // Ambil uraian bagian untuk template WhatsApp
            $uraianBagian = DB::table('bagian')
                ->where('id', $pengajuan->id_bagian_pengusul)
                ->value('uraianbagian');

            // Siapkan data untuk template WhatsApp - disesuaikan untuk SBSK
            $nomorPengajuanUntukTemplate = $pengajuan->kode_jenis_pengajuan
                ? "SBSK-" . $id
                : "SBSK-" . $id;
            $jenisDokumenUntukTemplate = "1 dokumen (Berita Acara SBSK)";

            // Kirim WhatsApp menggunakan template magic link verification
            $messageResult = $this->sendMagicLinkWhatsApp(
                $eselonIII->phone,                 // phone
                $eselonIII->nama,                  // namapenanggungjawab
                $nomorPengajuanUntukTemplate,      // no_pengajuan
                $jenisDokumenUntukTemplate,        // jenis_dokumen
                $uraianBagian,                     // bagian_pengusul
                $verificationLink                  // linkvalidasi
            );

            if ($messageResult === "Sukses") {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Magic Link verifikasi berhasil dikirim ke ' . $eselonIII->nama . ' melalui WhatsApp',
                    'verification_id' => $magicLinkVerification,
                    'expires_at' => $expiresAt->format('Y-m-d H:i:s')
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim Magic Link verifikasi via WhatsApp'
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada sendMagicLinkVerification SBSK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Magic Link Validation - Show verification page for SBSK (menggunakan custom verification_level)
     *
     * @param string $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function magicLinkValidation($encrypted_id)
    {
        try {
            $decryptedData = \Illuminate\Support\Facades\Crypt::decryptString($encrypted_id);
            $tokenData = json_decode(gzuncompress(base64_decode($decryptedData)), true);

            if (!$tokenData || !isset($tokenData['verification_id'])) {
                throw new \Exception('Token verifikasi tidak valid.');
            }

            // Siapkan data dasar yang SELALU dibutuhkan oleh view
            $baseDataForView = [
                'encrypted_token' => $encrypted_id,
                'expires_at' => $tokenData['expires_at'] ?? 0,
                'has_dokumen_pendukung' => false, // Nilai default
            ];

            $verification = DB::table('magic_link_verifications')->where('id', $tokenData['verification_id'])->first();
            if (!$verification) {
                throw new \Exception('Data verifikasi tidak ditemukan.');
            }

            // Cek status dan expiry SEBELUM melanjutkan ke data lengkap
            if ($verification->status === 'verified') {
                return view('PerencanaanBMN.Bagian.pengajuanrkbmn.magic-link-sbsk', array_merge($baseDataForView, [
                    'status' => 'already_verified',
                    'message' => 'Verifikasi sudah pernah dilakukan sebelumnya.'
                ]));
            }
            if (now()->timestamp > $tokenData['expires_at']) {
                DB::table('magic_link_verifications')->where('id', $tokenData['verification_id'])->update(['status' => 'expired']);
                return view('PerencanaanBMN.Bagian.pengajuanrkbmn.magic-link-sbsk', array_merge($baseDataForView, [
                    'status' => 'expired',
                    'message' => 'Link verifikasi telah kedaluwarsa.'
                ]));
            }

            // Jika lolos semua cek, siapkan data lengkap untuk halaman verifikasi utama
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel::with([
                'bagianPengusul', 'bagianPelaksana'
            ])->findOrFail($verification->pengajuan_id);

            $jenisForm = $pengajuan->kode_jenis_pengajuan;
            $detailData = $this->getDetailData($pengajuan, $jenisForm);
            $jenisPengajuan = $this->extractJenisFromKode($jenisForm);

            $documentsToShow = ['berita_acara'];
            $has_dokumen_pendukung = !empty($pengajuan->dokumen_pendukung);
            if ($has_dokumen_pendukung) {
                $documentsToShow[] = 'dokumen_pendukung';
            }

            return view('PerencanaanBMN.Bagian.pengajuanrkbmn.magic-link-sbsk', [
                'status' => 'valid',
                'verification' => $verification,
                'pengajuan' => $pengajuan,
                'detailData' => $detailData,
                'jenisPengajuan' => $jenisPengajuan,
                'documentsToSign' => json_decode($verification->documents_to_sign, true),
                'documentsToShow' => $documentsToShow,
                'encrypted_token' => $encrypted_id,
                'expires_at' => $tokenData['expires_at'],
                'bagianPengusul' => $pengajuan->bagianPengusul,
                'has_dokumen_pendukung' => $has_dokumen_pendukung,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error pada magicLinkValidation SBSK: ' . $e->getMessage());
            // Bahkan saat error, kirim data dasar agar view tidak crash
            return view('PerencanaanBMN.Bagian.pengajuanrkbmn.magic-link-sbsk', [
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'encrypted_token' => $encrypted_id ?? null,
                'expires_at' => 0,
                'has_dokumen_pendukung' => false,
            ]);
        }
    }

    /**
     * Process Magic Link E-Sign for SBSK (MODIFIED FOR CO-SIGNING)
     *
     * @param \Illuminate\Http\Request $request
     * @param string $encrypted_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMagicLinkEsign(Request $request, $encrypted_id)
    {
        // Validasi Input Awal
        $request->validate([
            'passphrase' => 'required|string',
            'documents' => 'required|array',
            'documents.*' => 'in:berita_acara'
        ]);

        DB::beginTransaction();
        try {
            // Dekripsi dan Validasi Token
            $decryptedData = Crypt::decryptString($encrypted_id);
            $compressedData = base64_decode($decryptedData);
            $tokenData = gzuncompress($compressedData);
            $data = json_decode($tokenData, true);

            Log::info('Dekripsi token berhasil: ', $data);

            if (!$data || !isset($data['verification_id'])) {
                throw new \Exception('Token verifikasi tidak valid.');
            }

            // Cek Status Verifikasi
            $verification = DB::table('magic_link_verifications')
                ->where('id', $data['verification_id'])
                ->first();

            if (!$verification) {
                throw new \Exception('Data verifikasi tidak ditemukan.');
            }

            // VALIDASI: Pastikan ini adalah verification untuk SBSK
            if (!$this->isSbskVerification($verification->verification_level)) {
                throw new \Exception('Token verifikasi bukan untuk pengajuan SBSK.');
            }

            if ($verification->status !== 'pending') {
                throw new \Exception($verification->status === 'verified'
                    ? 'Verifikasi sudah pernah dilakukan sebelumnya.'
                    : 'Status verifikasi tidak valid: ' . $verification->status);
            }

            // Cek expiry
            if (now()->timestamp > $data['expires_at']) {
                DB::table('magic_link_verifications')
                    ->where('id', $data['verification_id'])
                    ->update(['status' => 'expired']);
                throw new \Exception('Link verifikasi telah kedaluwarsa.');
            }

            // Proses e-sign dokumen
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($verification->pengajuan_id);
            $documentsToSign = json_decode($verification->documents_to_sign, true);
            $requestedDocuments = $request->input('documents');

            $signedDocuments = [];
            $signErrors = [];

            foreach ($requestedDocuments as $documentType) {
                if (!in_array($documentType, $documentsToSign)) {
                    $signErrors[$documentType] = 'Dokumen tidak diizinkan untuk ditandatangani.';
                    continue;
                }

                try {
                    Log::info('Memproses dokumen SBSK: ' . $documentType . ' untuk level: ' . $verification->verification_level);

                    if ($documentType === 'berita_acara') {
                        // **MODIFIKASI UTAMA: Memilih antara sign atau co-sign**
                        if ($verification->verification_level === 'sbsk_operator') {
                            $esignResult = $this->_signBeritaAcaraSBSKMagicLink($pengajuan, $request->passphrase, $verification);
                            // Update pengajuan SBSK (Operator)
                            $pengajuan->berita_acara_sbsk_signed_path = $esignResult['signed_path'];
                            $pengajuan->berita_acara_sbsk_signed_date = now();

                        } elseif ($verification->verification_level === 'sbsk_koordinator') {
                             // Untuk koordinator, kita hanya membuat file co-sign, TIDAK mengubah database.
                            $this->_coSignBeritaAcaraSBSKMagicLink($pengajuan, $request->passphrase, $verification);
                        }
                    }

                    $signedDocuments[] = $documentType;
                    Log::info('Dokumen SBSK berhasil ditandatangani: ' . $documentType);
                } catch (\Exception $e) {
                    Log::error('Error signing ' . $documentType . ' via Magic Link SBSK: ' . $e->getMessage());
                    $signErrors[$documentType] = $e->getMessage();
                }
            }

            // Simpan perubahan pada model Pengajuan (hanya jika ada perubahan dari e-sign)
            // CATATAN: Status pengajuan TIDAK diubah di sini.
            // Status hanya berubah ketika user klik tombol "Ajukan Kembali" di halaman review.
            if ($pengajuan->isDirty()) {
                $pengajuan->save();
                Log::info('Model pengajuan SBSK disimpan dengan perubahan (tanpa mengubah status).', $pengajuan->getChanges());
            }

            // Update Status Verifikasi
            if (!empty($signedDocuments)) {
                $verificationResult = [ 'signed_documents' => $signedDocuments, 'errors' => $signErrors, 'signed_at' => now()->toISOString(), 'signed_by' => $verification->eselon_iii_name, ];
                DB::table('magic_link_verifications')->where('id', $verification->id)->update([ 'status' => 'verified', 'verified_at' => now(), 'verification_result' => json_encode($verificationResult) ]);
                Log::info('Status verification SBSK diupdate ke verified');
            } else {
                // PERBAIKAN: Jangan ubah status ke 'failed', biarkan 'pending' agar user bisa retry
                // Hanya log error attempt untuk tracking
                $attemptResult = [
                    'attempt_at' => now()->toISOString(),
                    'errors' => $signErrors,
                    'attempted_by' => $verification->eselon_iii_name
                ];

                // Update verification_result tanpa mengubah status
                $currentResult = $verification->verification_result ? json_decode($verification->verification_result, true) : [];
                $currentResult['last_attempt'] = $attemptResult;
                $currentResult['attempt_count'] = ($currentResult['attempt_count'] ?? 0) + 1;

                DB::table('magic_link_verifications')->where('id', $verification->id)->update([
                    'verification_result' => json_encode($currentResult)
                ]);

                Log::warning('E-sign attempt failed (likely wrong passphrase), status tetap pending untuk retry. Attempt #' . $currentResult['attempt_count']);
            }

            DB::commit();
            Log::info('Transaction SBSK committed successfully');

            return response()->json([
                'success' => count($signedDocuments) > 0,
                'message' => count($signedDocuments) > 0
                    ? 'Verifikasi berhasil! ' . count($signedDocuments) . ' dokumen telah ditandatangani.'
                    : 'Passphrase salah atau terjadi kesalahan saat menandatangani dokumen. Silakan coba lagi dengan passphrase yang benar.',
                'signed_documents' => $signedDocuments,
                'errors' => $signErrors,
                'can_retry' => true, // Indicator bahwa user bisa retry
                'attempt_count' => ($currentResult['attempt_count'] ?? 0)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada processMagicLinkEsign SBSK: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([ 'success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage() ], 500);
        }
    }

    /**
     * Preview Dokumen via Magic Link for SBSK (menggunakan custom verification_level)
     */
    public function previewDocumentMagicLink($encrypted_id, $document_type)
    {
        try {
            $decryptedData = \Illuminate\Support\Facades\Crypt::decryptString($encrypted_id);
            $tokenData = json_decode(gzuncompress(base64_decode($decryptedData)), true);

            if (!$tokenData || !isset($tokenData['verification_id'])) {
                abort(403, 'Token verifikasi tidak valid.');
            }

            $verification = \Illuminate\Support\Facades\DB::table('magic_link_verifications')
                ->where('id', $tokenData['verification_id'])
                ->first();

            if (!$verification) {
                abort(403, 'Data verifikasi tidak ditemukan.');
            }

            $pengajuan = \App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel::findOrFail($tokenData['pengajuan_id']);

            switch ($document_type) {
                case 'berita_acara':
                    // Cek level verifikasi untuk menentukan dokumen yang ditampilkan
                    if ($verification->verification_level === 'sbsk_operator') {
                        // Operator: Generate PDF baru (preview sebelum TTD pertama)
                        return $this->generateBeritaAcaraSBSKPreview($pengajuan);

                    } elseif ($verification->verification_level === 'sbsk_koordinator') {
                        // Koordinator: Tampilkan file yang sudah ditandatangani operator
                        $signedFilePath = $pengajuan->berita_acara_sbsk_signed_path;

                        if (!$signedFilePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($signedFilePath)) {
                            abort(404, 'File Berita Acara yang ditandatangani operator tidak ditemukan. Pastikan operator sudah menandatangani dokumen terlebih dahulu.');
                        }

                        return response()->file(storage_path('app/public/' . $signedFilePath), [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="berita_acara_operator_signed.pdf"'
                        ]);

                    } else {
                        abort(400, 'Level verifikasi tidak valid.');
                    }

                case 'dokumen_pendukung':
                    $filePath = $pengajuan->dokumen_pendukung;
                    if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
                        abort(404, 'File dokumen pendukung tidak ditemukan.');
                    }
                    return response()->file(storage_path('app/' . $filePath));

                default:
                    abort(404, 'Tipe dokumen tidak dikenali.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error previewDocumentMagicLink SBSK: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan sistem saat menampilkan dokumen.');
        }
    }

        // ==========================================
    // MAGIC LINK HELPER METHODS
    // ==========================================
    private function angkaKeTerbilang($angka)
    {
        // Convert to integer to handle leading zeros from date('d')
        $angka = (int) $angka;

        $nilai = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($angka < 12) {
            return $nilai[$angka];
        } elseif ($angka < 20) {
            return $this->angkaKeTerbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            return $this->angkaKeTerbilang(floor($angka / 10)) . " puluh " . $this->angkaKeTerbilang($angka % 10);
        } elseif ($angka < 200) {
            return "seratus " . $this->angkaKeTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->angkaKeTerbilang(floor($angka / 100)) . " ratus " . $this->angkaKeTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "seribu " . $this->angkaKeTerbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->angkaKeTerbilang(floor($angka / 1000)) . " ribu " . $this->angkaKeTerbilang($angka % 1000);
        }
        return $angka;
    }

        /**
     * Helper untuk menandatangani Berita Acara SBSK via Magic Link (Tanda Tangan Pertama oleh Operator)
     */
    private function _signBeritaAcaraSBSKMagicLink($pengajuan, $passphrase, $verification)
    {
        Log::info('Memulai sign Berita Acara SBSK via Magic Link untuk pengajuan ID: ' . $pengajuan->id);

        // Siapkan data untuk template BeritaAcaraSBSK
        $dataArray = $this->prepareBeritaAcaraSBSKData($pengajuan);

        // Generate PDF
        $pdf = Pdf::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcaraSBSK', $dataArray);
        $pdfBase64 = base64_encode($pdf->output());

        // Siapkan properti tanda tangan untuk OPERATOR (sesuai dengan pengajuan reguler)
        $signatureProperties = [
            'imageBase64' => '',  // Will be filled with QR code
            'tampilan' => 'VISIBLE',
            'page' => 2,
            'originX' => 145.0,
            'originY' => 215.0,
            'width' => 75.0,
            'height' => 75.0,
            'location' => 'Jakarta',
            'reason' => 'Dokumen Berita Acara SBSK Ini Telah Disetujui dengan Tanda Tangan Elektronik (Operator via Magic Link)'
        ];

        // Generate QR Code (sama dengan pengajuan reguler)
        $qrContent = "Berita Acara SBSK ID: " . $pengajuan->id . "\nPenanggung Jawab: " . $verification->eselon_iii_name . "\nTanggal: " . now()->format('d/m/Y H:i');

        try {
            $qrBuilder = Builder::create()
                ->writer(new PngWriter())
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();
            $signatureProperties['imageBase64'] = base64_encode($qrBuilder->getString());
        } catch (\Exception $e) {
            Log::warning('QR Code generation failed, using empty image: ' . $e->getMessage());
            $signatureProperties['imageBase64'] = '';
        }

        // Call e-sign API dengan konfigurasi sama seperti pengajuan reguler
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level);

        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [$signatureProperties],
            'file' => [$pdfBase64]
        ];

        try {
            $response = $client->post($url, [
                'auth' => [$username, $password],
                'json' => $requestData
            ]);

            $json = json_decode($response->getBody()->getContents(), true);

            if (!isset($json['file'][0])) {
                throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen Berita Acara SBSK. Response: " . json_encode($json));
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('E-sign API request failed: ' . $e->getMessage());
            throw new \Exception("Gagal menghubungi API e-sign: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('E-sign API error: ' . $e->getMessage());
            throw new \Exception("Error pada API e-sign: " . $e->getMessage());
        }

        // Simpan file yang sudah ditandatangani
        $signedPdfData = base64_decode($json['file'][0]);
        $dirPath = storage_path('app/public/bmn_rkbmn_sbsk_berita_acara_esign');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        // **MODIFIKASI NAMA FILE**
        $fileName = 'berita_acara_sbsk_' . $pengajuan->id . '_operator_signed.pdf';
        $fullPath = $dirPath . '/' . $fileName;

        if (file_put_contents($fullPath, $signedPdfData) === false) {
            throw new \Exception("Gagal menyimpan file PDF yang sudah ditandatangani ke: " . $fullPath);
        }

        // Verify file was created successfully
        if (!file_exists($fullPath)) {
            throw new \Exception("File PDF tidak berhasil disimpan di: " . $fullPath);
        }

        Log::info('PDF Berita Acara SBSK yang ditandatangani (' . $verification->verification_level . ') berhasil disimpan di: ' . $fullPath);

        // Kembalikan path relatif agar bisa disimpan ke database (sesuai pattern pengajuan reguler)
        return [
            'signed_path' => "bmn_rkbmn_sbsk_berita_acara_esign/{$fileName}"
        ];
    }

        /**
     * [NEW] Helper untuk CO-SIGN Berita Acara SBSK via Magic Link (Tanda Tangan Kedua oleh Koordinator)
     */
    private function _coSignBeritaAcaraSBSKMagicLink($pengajuan, $passphrase, $verification)
    {
        Log::info('Memulai CO-SIGN Berita Acara SBSK via Magic Link untuk pengajuan ID: ' . $pengajuan->id);

        // 1. Ambil PDF yang sudah ditandatangani Operator dari storage
        $operatorSignedPath = $pengajuan->berita_acara_sbsk_signed_path;
        if (!$operatorSignedPath || !Storage::disk('public')->exists($operatorSignedPath)) {
            throw new \Exception('Dokumen Berita Acara yang ditandatangani Operator tidak ditemukan untuk di-co-sign.');
        }
        $pdfBase64 = base64_encode(Storage::disk('public')->get($operatorSignedPath));

        // 2. Siapkan properti tanda tangan untuk KOORDINATOR (posisi berbeda)
        $signatureProperties = [
            'imageBase64' => '',  // Akan diisi QR code
            'tampilan' => 'VISIBLE',
            'page' => 2,
            'originX' => 380.0, // Posisi X berbeda untuk tanda tangan kedua
            'originY' => 215.0, // Posisi Y sama (bersebelahan)
            'width' => 75.0,
            'height' => 75.0,
            'location' => 'Jakarta',
            'reason' => 'Dokumen Berita Acara SBSK Ini Telah Disetujui dengan Tanda Tangan Elektronik (Koordinator via Magic Link)'
        ];

        // 3. Generate QR Code baru untuk Koordinator
        $qrContent = "Berita Acara SBSK ID: " . $pengajuan->id . "\nDisetujui oleh: " . $verification->eselon_iii_name . "\nTanggal: " . now()->format('d/m/Y H:i');
        try {
            $qrBuilder = Builder::create()->writer(new PngWriter())->data($qrContent)->encoding(new Encoding('UTF-8'))->size(150)->margin(5)->build();
            $signatureProperties['imageBase64'] = base64_encode($qrBuilder->getString());
        } catch (\Exception $e) {
            Log::warning('QR Code generation failed, using empty image: ' . $e->getMessage());
            $signatureProperties['imageBase64'] = '';
        }

        // 4. Call e-sign API
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level); // Menggunakan level 'sbsk_koordinator'

        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [$signatureProperties],
            'file' => [$pdfBase64] // Menggunakan PDF yang sudah ada TTD Operator
        ];

        try {
            $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
            $json = json_decode($response->getBody()->getContents(), true);

            if (!isset($json['file'][0])) {
                throw new \Exception("Gagal mendapatkan file dari API e-sign untuk co-sign Berita Acara SBSK. Response: " . json_encode($json));
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('E-sign API request failed for co-sign: ' . $e->getMessage());
            throw new \Exception("Gagal menghubungi API e-sign untuk co-sign: " . $e->getMessage());
        }

        // 5. Simpan file yang sudah di-co-sign
        $signedPdfData = base64_decode($json['file'][0]);
        // Simpan ke direktori yang sama tapi dengan nama file berbeda untuk menandakan "final"
        $dirPath = storage_path('app/public/bmn_rkbmn_sbsk_berita_acara_esign');
        if (!file_exists($dirPath)) { mkdir($dirPath, 0755, true); }

        // **MODIFIKASI NAMA FILE**
        $fileName = 'berita_acara_sbsk_' . $pengajuan->id . '_final_signed.pdf';
        $fullPath = $dirPath . '/' . $fileName;

        if (file_put_contents($fullPath, $signedPdfData) === false) {
            throw new \Exception("Gagal menyimpan file PDF yang sudah di-co-sign ke: " . $fullPath);
        }

        Log::info('PDF Berita Acara SBSK yang di-co-sign (' . $verification->verification_level . ') berhasil disimpan di: ' . $fullPath);

        // Method ini tidak perlu return path karena tidak disimpan ke DB
        return ['status' => 'success'];
    }

    /**
     * Siapkan data untuk template BeritaAcaraSBSK
     */
    private function prepareBeritaAcaraSBSKData($pengajuan)
    {
        try {
            // Ambil data bagian
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();

            // Ambil data Eselon III pengusul
            $pengusulData = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Ambil data Koordinator BMN dari Bagian Administrasi BMN dengan kode bagian 669
            $koordinatorData = DB::table('pegawai')
                ->where('id_satker', 669) // Bagian Administrasi BMN
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Fallback jika tidak ditemukan koordinator di bagian 669
            if (!$koordinatorData) {
                $koordinatorData = DB::table('pegawai')
                    ->where('eselon', 'III')
                    ->whereIn('id_satker', [669, 657]) // Cari di BMN atau Perencanaan
                    ->select('nama', 'nip')
                    ->first();
            }

            // Ambil detail pengajuan berdasarkan jenis
            $jenisForm = $pengajuan->kode_jenis_pengajuan;
            $detailData = $this->getDetailData($pengajuan, $jenisForm);
        } catch (\Exception $e) {
            Log::error('Error in prepareBeritaAcaraSBSKData: ' . $e->getMessage() . ' at line ' . $e->getLine());
            throw $e; // Re-throw untuk ditangkap oleh caller
        }

        // Format nama dengan ucwords
        $formatTitleCase = function($text) {
            return ucwords(strtolower($text ?? ''));
        };

        // Siapkan detail pengajuan untuk template
        $detailPengajuan = [
            'jenis_pengajuan' => $this->getJenisPengajuanLabel($jenisForm),
            'uraian_barang' => $pengajuan->uraian_barang,
            'kuantitas' => $pengajuan->kuantitas,
            'satuan' => 'unit', // Default satuan
            'total_anggaran' => $pengajuan->total_anggaran,
            'skema' => $pengajuan->skema
        ];

        // Add specific details based on jenis pengajuan
        if ($detailData) {
            $jenis = $this->extractJenisFromKode($jenisForm);
            switch ($jenis) {
                case 'R1':
                    $detailPengajuan['klasifikasi_bangunan'] = $detailData->klasifikasi_bangunan ?? null;
                    $detailPengajuan['klasifikasi_pejabat'] = $detailData->klasifikasi_pejabat ?? null;
                    $detailPengajuan['lokasi'] = $detailData->lokasi ?? null;
                    break;
                case 'R3':
                    $detailPengajuan['peruntukan_pejabat'] = $detailData->peruntukan_pejabat ?? null;
                    $detailPengajuan['lokasi'] = $detailData->lokasi ?? null;
                    break;
                case 'R4':
                    $detailPengajuan['klasifikasi_pejabat'] = $detailData->klasifikasi_pejabat ?? null;
                    $detailPengajuan['spesifikasi_kendaraan'] = $detailData->spesifikasi_kendaraan ?? null;
                    break;
                case 'R5':
                    $detailPengajuan['jenis_satker'] = $detailData->jenis_satker ?? null;
                    $detailPengajuan['jenis_kendaraan'] = $detailData->jenis_kendaraan ?? null;
                    break;
                case 'R6':
                    $detailPengajuan['jenis_satker'] = $detailData->jenis_satker ?? null;
                    $detailPengajuan['jenis_kendaraan'] = $detailData->jenis_kendaraan ?? null;
                    $detailPengajuan['tujuan_penggunaan'] = $detailData->tujuan_penggunaan ?? null;
                    break;
            }
        }

        return [
            'tahunAnggaran' => $pengajuan->tahun_anggaran,
            'tanggal' => $this->angkaKeTerbilang(date('d')), // BARIS REVISI: Menggunakan fungsi terbilang
            'bulan' => $this->getNamaBulan(date('m')),
            'tahun' => date('Y'),
            'tahunKata' => $this->angkaKeTerbilang(date('Y')), // INI BAGIAN PENTINGNYA
            'pengusulNama' => optional($pengusulData)->nama,
            'pengusulNip' => optional($pengusulData)->nip,
            'pengusulJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
            'uraianBagianPengusul' => optional($bagianPengusul)->uraianbagian,
            'koordinatorNama' => optional($koordinatorData)->nama,
            'koordinatorNip' => optional($koordinatorData)->nip,
            'koordinatorJabatan' => 'Kepala Bagian Administrasi BMN',
            'detailPengajuan' => $detailPengajuan
        ];
    }

    /**
     * Generate preview Berita Acara SBSK (tanpa tanda tangan)
     */
    private function generateBeritaAcaraSBSKPreview($pengajuan)
    {
        $dataArray = $this->prepareBeritaAcaraSBSKData($pengajuan);
        $pdf = Pdf::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcaraSBSK', $dataArray);
        return $pdf->stream('preview_berita_acara_sbsk.pdf');
    }

    /**
     * Send Magic Link WhatsApp message for SBSK - COMPLETE FIX
     */
    private function sendMagicLinkWhatsApp($phone, $namapenanggungjawab, $no_pengajuan, $jenis_dokumen, $bagian_pengusul, $linkvalidasi)
    {
        try {
            $kepada = $this->formatnomerwhatsapp($phone);
            $token_qontak = "mFETLoTDxuB_1dMcSrxr6UP2YOreTv7fBSvusbUij7U";

            $messageTemplateId = "478e52a2-09fd-4765-a37e-db2b10fd3cec";
            $channelIntegrationId = '81b411ae-b566-4ec5-bb7b-361b9f66131f';

            if (empty($token_qontak)) {
                Log::error('TOKEN_QONTAK tidak ditemukan di environment variable.');
                return "Error";
            }

            $payload = [
                'to_number' => $kepada,
                'to_name' => $namapenanggungjawab,
                'message_template_id' => $messageTemplateId,
                'channel_integration_id' => $channelIntegrationId,
                'language' => ['code' => 'id'],
                'parameters' => [
                    'body' => [
                        ['key' => '1', 'value' => 'nama_penerima', 'value_text' => $namapenanggungjawab],
                        ['key' => '2', 'value' => 'no_pengajuan', 'value_text' => $no_pengajuan],
                        ['key' => '3', 'value' => 'jenis_dokumen', 'value_text' => $jenis_dokumen],
                        ['key' => '4', 'value' => 'bagian_pengusul', 'value_text' => $bagian_pengusul],
                        ['key' => '5', 'value' => 'linkvalidasi', 'value_text' => $linkvalidasi]
                    ]
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $token_qontak",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                Log::error('cURL Error sending WhatsApp: ' . $error);
                return "Error";
            }

            $responseData = json_decode($response, true);

            // ===== INILAH BAGIAN YANG DIPERBAIKI =====
            // Mengecek apakah httpCode ada di rentang 2xx (sukses)
            if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['status']) && $responseData['status'] === 'success') {
                Log::info('WhatsApp message broadcast successfully created.', [
                    'http_code' => $httpCode,
                    'response' => $responseData
                ]);
                return "Sukses";
            } else {
                Log::error('WhatsApp API Error or non-success status:', [
                    'http_code' => $httpCode,
                    'response_body' => $responseData ?? $response
                ]);
                return "Error";
            }
        } catch (\Exception $e) {
            Log::error('Exception in sendMagicLinkWhatsApp: ' . $e->getMessage());
            return "Error";
        }
    }

    /**
     * Get NIK for signing (disesuaikan untuk SBSK dengan custom verification_level)
     */
    private function getNikForSigning($pengajuan, $verificationLevel)
    {
        try {
            // Default NIK jika tidak ditemukan
            $defaultNik = '3201132412920003';

            if ($verificationLevel === 'sbsk_operator') {
                // Ambil NIK dari pegawai eselon III bagian pengusul
                $nik = DB::table('pegawai')
                    ->where('id_satker', $pengajuan->id_bagian_pengusul)
                    ->where('eselon', 'III')
                    ->value('nik');
            } elseif ($verificationLevel === 'sbsk_koordinator') {
                // Ambil NIK dari pegawai eselon III bagian administrasi BMN (669)
                $nik = DB::table('pegawai')
                    ->where('id_satker', 669)
                    ->where('eselon', 'III')
                    ->value('nik');
            }

            // Jika NIK tidak ditemukan, gunakan default
            if (empty($nik)) {
                Log::warning("NIK tidak ditemukan untuk verification level: {$verificationLevel}, menggunakan default NIK");
                $nik = $defaultNik;
            }

            return $nik;
        } catch (\Exception $e) {
            Log::error('Error getting NIK for signing: ' . $e->getMessage());
            return '3201132412920003'; // Default NIK
        }
    }

    private function formatnomerwhatsapp($nomor)
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        if (substr($nomor, 0, 1) === '0') {
            $nomor = '62' . substr($nomor, 1);
        } elseif (substr($nomor, 0, 2) !== '62') {
            $nomor = '62' . $nomor;
        }
        return $nomor;
    }

    /**
     * Get nama bulan dalam bahasa Indonesia
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $namaBulan[$bulan] ?? '';
    }

    /**
     * Extract jenis pengajuan dari kode lengkap (R1-009 -> R1)
     */
    private function extractJenisFromKode($kodeJenis)
    {
        if (!$kodeJenis) return '';
        $match = $kodeJenis ? preg_match('/^(R[1-6])/', $kodeJenis, $matches) : false;
        return $match ? $matches[1] : $kodeJenis;
    }

    /**
     * Get jenis pengajuan label
     */
    private function getJenisPengajuanLabel($jenisForm)
    {
        $jenis = $this->extractJenisFromKode($jenisForm);
        $labels = [
            'R1' => 'Tanah dan/atau Bangunan Perkantoran',
            'R2' => 'Tanah dan/atau Bangunan',
            'R3' => 'Tanah dan/atau Bangunan Rumah Negara',
            'R4' => 'Kendaraan Bermotor Perorangan Dinas Jabatan',
            'R5' => 'Kendaraan Bermotor Perorangan Dinas Operasional',
            'R6' => 'Kendaraan Bermotor Perorangan Dinas Fungsional'
        ];
        return $labels[$jenis] ?? $jenisForm;
    }

    /**
     * Check if verification level is for SBSK (compatible with older PHP versions)
     */
    private function isSbskVerification($verificationLevel)
    {
        return substr($verificationLevel, 0, 5) === 'sbsk_';
    }


    /**
     * Get data barang dalam DBR
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ambildatabarangdalamdbr(Request $request)
    {
        $kodebarang = $request->get('kodebarang');
        $idbagian = Auth::user()->idbagian;

        // Mengambil data barang dalam detildbr
        $dataBarangDalamDbr = DB::table('detildbr as a')
            ->select(DB::raw('count(a.kd_brg) as totalbarangdbr'))
            ->leftJoin('dbrinduk as c','c.iddbr','a.iddbr')
            ->leftJoin('ruangan as d','c.idruangan','=','d.id')
            ->where('a.kd_brg','=',$kodebarang)
            ->where('d.idbagian','=',$idbagian)
            ->get()->toArray();

        // Mengambil data total barang
        $dataBarangTotal = DB::table('barang as a')
            ->select(DB::raw('count(a.kd_brg) as totalbarang'))
            ->where('a.kd_brg','=',$kodebarang)
            ->get()->toArray();

        // Menggabungkan dua array
        $data = array_merge($dataBarangDalamDbr, $dataBarangTotal);

        return response()->json($data);
    }

    // ==========================================
    // DOCUMENT MANAGEMENT
    // ==========================================

    /**
     * Upload dokumen pendukung
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function uploadDokumen(Request $request, $id)
    {
        try {
            // Validasi
            $request->validate([
                'dokumen' => 'required|file|mimes:pdf|max:10240', // 10MB max
            ]);

            // Ambil data pengajuan
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            // Cek status
            if (!in_array($pengajuan->status, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return response()->json([
                    'error' => 'Dokumen hanya bisa diupload untuk pengajuan dengan status tertentu.'
                ], 403);
            }

            // Ambil file
            $file = $request->file('dokumen');

            // Generate nama file
            $kodeJenis = $pengajuan->kode_jenis_pengajuan;
            $namaFile = $kodeJenis . '_dokumenpendukung.' . $file->getClientOriginalExtension();

            // Simpan file
            $path = $file->storeAs('dokumenpendukung_rkbmn', $namaFile, 'local');

            // Update database
            $pengajuan->dokumen_pendukung = $path;
            $pengajuan->save();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload.',
                'filename' => $namaFile,
                'path' => $path
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data pengajuan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengupload dokumen.'], 500);
        }
    }

    /**
     * Download dokumen pendukung pengajuan
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDokumen($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            // Cek apakah ada dokumen
            if (!$pengajuan->dokumen_pendukung) {
                return response()->json([
                    'error' => 'Dokumen pendukung tidak ditemukan untuk pengajuan ini.'
                ], 404);
            }

            // Path lengkap file
            $filePath = storage_path('app/' . $pengajuan->dokumen_pendukung);

            // Cek apakah file exist
            if (!file_exists($filePath)) {
                return response()->json([
                    'error' => 'File dokumen tidak ditemukan di server.'
                ], 404);
            }

            // Download file
            return response()->download($filePath);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data pengajuan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mendownload dokumen.'], 500);
        }
    }

    /**
     * Preview dokumen pendukung pengajuan (untuk iframe preview)
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function previewDokumen($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            if (!$pengajuan->dokumen_pendukung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada dokumen pendukung'
                ], 404);
            }

            $filePath = storage_path('app/' . $pengajuan->dokumen_pendukung);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            // Return file dengan header untuk preview
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dokumen_pendukung_' . $id . '.pdf"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada previewDokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview Berita Acara SBSK (untuk iframe preview di modal)
     * Menampilkan file yang sudah ditandatangani jika ada, atau generate preview baru jika belum
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function previewBeritaAcara($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::with([
                'biroPengusul',
                'bagianPengusul',
                'biroPelaksana',
                'bagianPelaksana',
                'bangunanKantor',
                'rumahNegara',
                'kendaraanJabatan',
                'kendaraanOperasional',
                'kendaraanFungsional'
            ])->findOrFail($id);

            // Jika sudah ada file yang ditandatangani, tampilkan file tersebut
            if (!empty($pengajuan->berita_acara_sbsk_signed_path)) {
                $filePath = storage_path('app/public/' . $pengajuan->berita_acara_sbsk_signed_path);

                if (file_exists($filePath)) {
                    Log::info('Preview berita acara yang sudah ditandatangani: ' . $filePath);
                    return response()->file($filePath, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="berita_acara_signed.pdf"'
                    ]);
                }
            }

            // Jika belum ada file yang ditandatangani, generate preview baru (tanpa tanda tangan)
            Log::info('Generate preview berita acara baru (belum ditandatangani) untuk pengajuan ID: ' . $id);
            return $this->generateBeritaAcaraSBSKPreview($pengajuan);

        } catch (\Exception $e) {
            Log::error('Error pada previewBeritaAcara: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // WORKFLOW METHODS
    // ==========================================

    /**
     * Kirim pengajuan ke pelaksana pengadaan
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function kirimkepelaksanapengadaan($id)
    {
        try {
            $data = PengajuanRKBMNBagianModel::findOrFail($id);

            // Validasi status
            if ($data->status !== 'Draft') {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Pengajuan hanya bisa dikirim dari status Draft.'
                ], 400);
            }

            // Update status
            $data->status = 'Diajukan ke Unit Pelaksana';
            $data->tanggal_pengajuan = now();
            $data->save();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Pengajuan berhasil dikirim ke Unit Pelaksana'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Data pengajuan tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error kirimkepelaksanapengadaan: ' . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'message' => 'Terjadi kesalahan saat memproses permintaan.'
            ], 500);
        }
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Get validation rules with the correct required fields.
     */
    private function getValidationRules(string $jenis): array
    {
        $rules = [];

        switch ($jenis) {
            case 'R1':
                $rules = [
                    'klasifikasi_bangunan' => 'required|string',
                    'klasifikasi_pejabat' => 'required|string',
                    'lokasi' => 'required|string|max:500',
                    'luas_ruang_kerja' => 'required|numeric|min:0',
                    'tujuan_rencana' => 'required|string',
                    'atr_nonatr' => 'nullable|string',
                ];
                break;

            case 'R3':
                $rules = [
                    'peruntukan_pejabat' => 'required|string',
                    'lokasi_rumah' => 'required|string',
                    'tujuan_rencana' => 'required|string',
                    'atr_nonatr' => 'required|string',
                    'luas_tanah' => 'nullable|numeric|min:0',
                    'luas_bangunan' => 'nullable|numeric|min:0',
                ];
                break;

            case 'R4':
                $rules = [
                    'klasifikasi_pejabat_kendaraan' => 'required|string',
                    'spesifikasi_kendaraan' => 'required|string',
                    'skema' => 'required|string|in:beli,sewa',
                ];
                break;

            case 'R5':
                $rules = [
                    'jenis_satker_operasional' => 'required|string',
                    'jenis_kendaraan_operasional' => 'required|string',
                    'skema' => 'required|string|in:beli,sewa',
                ];
                break;

            case 'R6':
                $rules = [
                    'jenis_satker_fungsional' => 'required|string',
                    'jenis_kendaraan_fungsional' => 'required|string',
                    'skema' => 'required|string|in:beli,sewa',
                ];
                break;
        }

        return $rules;
    }

    /**
     * Prepare submission data with standardized field handling.
     */
    private function preparePengajuanData($request, $kodePengajuan)
    {
        $idBiro = $this->getIdBiro($request->input('id_bagian_pelaksana', Auth::user()->idbagian));

        return [
            'kode_jenis_pengajuan' => $kodePengajuan,
            'akun_belanja' => $this->getAkunBelanja($request->jenistabel),
            'akun_neraca' => $this->getAkunNeraca($request->jenistabel),
            'kode_barang' => $request->barang ?? $request->kode_barang,
            'status' => 'Draft',
            'tahun_anggaran' => $request->tahun_anggaran,
            'atr_nonatr' => $request->atr_nonatr ?? 'Non ATR',
            'tujuan_rencana' => $request->tujuan_rencana ?? 'Khusus Lainnya',
            'skema' => $request->skema ?? 'Pengadaan',
            'kuantitas' => $request->kuantitas,
            'harga_barang' => $request->harga_barang,
            'total_anggaran' => $request->harga_barang * $request->kuantitas,
            'uraian_barang' => $request->uraian_barang,
            'keterangan' => $request->keterangan,
            'id_bagian_pelaksana' => $request->input('id_bagian_pelaksana', Auth::user()->idbagian),
            'id_biro_pelaksana' => $idBiro,
            'id_bagian_pengusul' => Auth::user()->idbagian,
            'id_biro_pengusul' => Auth::user()->idbiro,
            'tanggal_pengajuan' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Store detail data with proper field mapping.
     */
    private function storeDetailData($request, $kodePengajuan, $jenis)
    {
        switch ($jenis) {
            case 'R1':
                KantorModel::create([
                    'kode_jenis_pengajuan' => $kodePengajuan,
                    'klasifikasi_bangunan' => $request->klasifikasi_bangunan,
                    'klasifikasi_pejabat' => $request->klasifikasi_pejabat,
                    'lokasi' => $request->lokasi,
                    'luas_ruang_kerja' => $request->luas_ruang_kerja ?? 0,
                    'luas_ruang_tamu' => $request->luas_ruang_tamu ?? 0,
                    'luas_ruang_rapat' => $request->luas_ruang_rapat ?? 0,
                    'luas_ruang_tunggu' => $request->luas_ruang_tunggu ?? 0,
                    'luas_ruang_istirahat' => $request->luas_ruang_istirahat ?? 0,
                    'luas_ruang_sekretaris' => $request->luas_ruang_sekretaris ?? 0,
                    'luas_ruang_simpan' => $request->luas_ruang_simpan ?? 0,
                    'luas_ruang_toilet' => $request->luas_ruang_toilet ?? 0,
                    'luas_ruang_rapat_utama' => $request->luas_ruang_rapat_utama ?? 0,
                ]);
                break;

            case 'R3':
                RumahNegaraModel::create([
                    'kode_jenis_pengajuan' => $kodePengajuan,
                    'peruntukan_pejabat' => $request->peruntukan_pejabat,
                    'jenis_rumah' => $request->tipe_rumah,
                    'lokasi' => $request->lokasi_rumah,
                    'jenis_pengadaan_rumah' => $request->skema,
                    'luas_tanah' => $request->luas_tanah ?? 0,
                    'luas_bangunan' => $request->luas_bangunan ?? 0,

                    // Mapping langsung dari form baru ke kolom DB
                    'jumlah_ruang_duduk' => $request->ruang_duduk ?? 0,
                    'jumlah_ruang_kerja' => $request->ruang_kerja ?? 0,
                    'jumlah_ruang_fungsional' => $request->ruang_fungsional ?? 0,
                    'jumlah_ruang_makan' => $request->ruang_makan ?? 0,
                    'jumlah_ruang_tidur' => $request->ruang_tidur ?? 0,
                    'jumlah_ruang_wc' => $request->kamar_mandi_wc ?? 0,
                    'jumlah_dapur' => $request->dapur ?? 0,
                    'jumlah_gudang' => $request->gudang ?? 0,
                    'jumlah_garasi' => $request->garasi ?? 0,
                    'jumlah_ruang_tidur_pramuwisma' => $request->ruang_tidur_pramuwisma ?? 0,
                    'jumlah_ruang_cuci' => $request->ruang_cuci ?? 0,
                    'jumlah_kamar_mandi_pramuwisma' => $request->kamar_mandi_pramuwisma ?? 0,
                ]);
                break;

            case 'R4':
                KendaraanJabatanModel::create([
                    'kode_jenis_pengajuan' => $kodePengajuan,
                    'klasifikasi_pejabat' => $request->klasifikasi_pejabat_kendaraan,
                    'spesifikasi_kendaraan' => $request->spesifikasi_kendaraan,
                ]);
                break;

            case 'R5':
                KendaraanOperasionalModel::create([
                    'kode_jenis_pengajuan' => $kodePengajuan,
                    'jenis_satker' => $request->jenis_satker_operasional,
                    'jenis_kendaraan' => $request->jenis_kendaraan_operasional,
                ]);
                break;

            case 'R6':
                if (class_exists(KendaraanFungsionalModel::class)) {
                    KendaraanFungsionalModel::create([
                        'kode_jenis_pengajuan' => $kodePengajuan,
                        'jenis_satker' => $request->jenis_satker_fungsional,
                        'jenis_kendaraan' => $request->jenis_kendaraan_fungsional,
                    ]);
                }
                break;

            case 'R2':
                // R2 not implemented yet
                throw new \Exception('R2 belum diimplementasikan');
        }
    }

    /**
     * Get barang information for review
     *
     * @param PengajuanRKBMNBagianModel $data
     * @return array
     */
    private function getBarangInfo($data)
    {
        try {
            // Ambil data dari tabel barang berdasarkan kode_barang
            $barang = DB::table('t_brg')->where('kd_brg', $data->kode_barang)->first();

            if (!$barang) {
                return null;
            }

            // Ambil informasi hierarki barang
            $golongan = DB::table('t_gol')->where('kd_gol', $barang->kd_gol)->first();
            $bidang = DB::table('t_bid')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->first();
            $kelompok = DB::table('t_kel')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->where('kd_kel', $barang->kd_kel)
                ->first();
            $subkelompok = DB::table('t_skel')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->where('kd_kel', $barang->kd_kel)
                ->where('kd_skel', $barang->kd_skel)
                ->first();

            return [
                'golongan' => $golongan ? $golongan->kd_gol . ' - ' . $golongan->ur_gol : '',
                'bidang' => $bidang ? $bidang->kd_bid . ' - ' . $bidang->ur_bid : '',
                'kelompok' => $kelompok ? $kelompok->kd_kel . ' - ' . $kelompok->ur_kel : '',
                'sub_kelompok' => $subkelompok ? $subkelompok->kd_skel . ' - ' . $subkelompok->ur_skel : '',
                'barang' => $barang->kd_brg . ' - ' . $barang->ur_sskel
            ];

        } catch (\Exception $e) {
            Log::error('Error getting barang info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update detail data based on jenis pengajuan
     *
     * @param \Illuminate\Http\Request $request
     * @param string $jenisForm
     * @param string $kodePengajuan
     * @return void
     */
    private function updateDetailData(Request $request, $jenis, $kodePengajuan)
    {
        switch ($jenis) {
            case 'R1':
                \App\Models\PerencanaanBMN\Bagian\KantorModel::where('kode_jenis_pengajuan', $kodePengajuan)
                    ->update($request->only([
                        'klasifikasi_bangunan', 'klasifikasi_pejabat', 'lokasi',
                        'luas_ruang_kerja', 'luas_ruang_tamu', 'luas_ruang_rapat',
                        'luas_ruang_tunggu', 'luas_ruang_istirahat', 'luas_ruang_sekretaris',
                        'luas_ruang_simpan', 'luas_ruang_toilet', 'luas_ruang_rapat_utama'
                    ]));
                break;

            case 'R3':
                \App\Models\PerencanaanBMN\Bagian\RumahNegaraModel::where('kode_jenis_pengajuan', $kodePengajuan)
                    ->update($request->only([
                        // Detail table fields only (NO tujuan_rencana, NO atr_nonatr)
                        'peruntukan_pejabat', 'jenis_rumah', 'lokasi', 'luas_bangunan', 'luas_tanah',
                        'jumlah_ruang_kerja', 'jumlah_ruang_duduk', 'jumlah_ruang_fungsional',
                        'jumlah_ruang_makan', 'jumlah_ruang_tidur', 'jumlah_ruang_wc',
                        'jumlah_dapur', 'jumlah_gudang', 'jumlah_garasi',
                        'jumlah_ruang_tidur_pramuwisma', 'jumlah_ruang_cuci', 'jumlah_kamar_mandi_pramuwisma'
                    ]));
                break;

            case 'R4':
                \App\Models\PerencanaanBMN\Bagian\KendaraanJabatanModel::where('kode_jenis_pengajuan', $kodePengajuan)
                    ->update([
                        'klasifikasi_pejabat' => $request->klasifikasi_pejabat_kendaraan,
                        'spesifikasi_kendaraan' => $request->spesifikasi_kendaraan,
                    ]);
                break;

            case 'R5':
                \App\Models\PerencanaanBMN\Bagian\KendaraanOperasionalModel::where('kode_jenis_pengajuan', $kodePengajuan)
                    ->update([
                        'jenis_satker' => $request->jenis_satker_operasional,
                        'jenis_kendaraan' => $request->jenis_kendaraan_operasional,
                    ]);
                break;

                case 'R6':
                    \App\Models\PerencanaanBMN\Bagian\KendaraanFungsionalModel::where('kode_jenis_pengajuan', $kodePengajuan)
                        ->update($request->only([
                            'jenis_satker_fungsional', 'jenis_kendaraan_fungsional'
                        ]));
                    break;
        }
    }

    /**
     * Get detail data based on jenis form
     *
     * @param PengajuanRKBMNBagianModel $data
     * @param string $jenisForm
     * @return mixed
     */
    private function getDetailData($data, $jenisForm)
    {
        if (strpos($jenisForm, 'R1') === 0) {
            return $data->bangunanKantor;
        } elseif (strpos($jenisForm, 'R3') === 0) {
            return $data->rumahNegara;
        } elseif (strpos($jenisForm, 'R4') === 0) {
            return $data->kendaraanJabatan;
        } elseif (strpos($jenisForm, 'R5') === 0) {
            return $data->kendaraanOperasional;
        }

        return null;
    }

    /**
     * Get additional data for show method
     *
     * @param PengajuanRKBMNBagianModel $data
     * @return array
     */
    private function getAdditionalShowData($data)
    {
        $uraianBarang = DB::table('t_brg')->where('kd_brg', $data->kode_barang)->value('ur_sskel');

        return [
            'uraianBarang' => $uraianBarang,
            'uraianBiroPengusul' => optional($data->biroPengusul)->uraianbiro,
            'uraianBagianPengusul' => optional($data->bagianPengusul)->uraianbagian,
            'uraianBagianPelaksana' => optional($data->bagianPelaksana)->uraianbagian,
            'uraianBiroPelaksana' => optional($data->biroPelaksana)->uraianbiro,
            'tanggalPengajuan' => Carbon::parse($data->tanggal_pengajuan)->translatedFormat('j F Y'),
            'tahunAnggaran' => $data->tahun_anggaran,
            'program' => $data->program,
            'status' => $data->status,
        ];
    }

    /**
     * Handle file upload during store
     *
     * @param \Illuminate\Http\Request $request
     * @param PengajuanRKBMNBagianModel $pengajuan
     * @return void
     */
    private function handleFileUpload($request, $pengajuan)
    {
        $file = $request->file('dokumen_pendukung');
        $kodeJenis = $pengajuan->kode_jenis_pengajuan;
        $namaFile = $kodeJenis . '_dokumenpendukung.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('dokumenpendukung_rkbmn', $namaFile, 'local');

        $pengajuan->dokumen_pendukung = $path;
        $pengajuan->save();
    }

    /**
     * Get detail table name based on kode jenis
     *
     * @param string $kodeJenis
     * @return string|null
     */
    private function getDetailTableName($kodeJenis)
    {
        if (strpos($kodeJenis, 'R1') === 0) {
            return 'bmn_pengajuan_bangunan_perkantoran';
        } elseif (strpos($kodeJenis, 'R2') === 0) {
            return null; // R2 not implemented
        } elseif (strpos($kodeJenis, 'R3') === 0) {
            return 'bmn_pengajuan_rumah_negara';
        } elseif (strpos($kodeJenis, 'R4') === 0) {
            return 'bmn_pengajuan_kendaraan_jabatan';
        } elseif (strpos($kodeJenis, 'R5') === 0) {
            return 'bmn_pengajuan_kendaraan_operasional';
        } elseif (strpos($kodeJenis, 'R6') === 0) {
            return 'bmn_pengajuan_kendaraan_fungsional';
        }

        return null;
    }

    /**
     * Get akun belanja based on jenis
     *
     * @param string $jenis
     * @return string
     */
    private function getAkunBelanja($jenis)
    {
        switch ($jenis) {
            case 'R1':
                return "531111 - Belanja Modal Tanah";
            case 'R3':
                return "533111 - Belanja Modal Gedung dan Bangunan";
            case 'R4':
            case 'R5':
            case 'R6':
                return "532111 - Belanja Modal Peralatan dan Mesin";
            default:
                return "532111 - Belanja Modal Peralatan dan Mesin";
        }
    }

    /**
     * Get akun neraca based on jenis
     *
     * @param string $jenis
     * @return string
     */
    private function getAkunNeraca($jenis)
    {
        switch ($jenis) {
            case 'R1':
                return "131111 - Tanah";
            case 'R3':
                return "133111 - Gedung dan Bangunan";
            case 'R4':
            case 'R5':
            case 'R6':
                return "132111 - Peralatan dan Mesin";
            default:
                return "132111 - Peralatan dan Mesin";
        }
    }

    private function getListProgram()
    {
        $tahunanggaran = session('tahunanggaran', date('Y'));
        return ProgramModel::select('kode as kode_program', 'uraianprogram as nama_program')
            ->distinct('kode')
            ->where('tahunanggaran', $tahunanggaran)
            ->get();
    }

    private function getListOutput()
    {
        $tahunanggaran = session('tahunanggaran', date('Y'));
        return OutputModel::select('kode as kode_output', 'deskripsi as nama_output')
            ->distinct('kode')
            ->where('tahunanggaran', $tahunanggaran)
            ->get();
    }

    private function getDataKegiatan()
    {
        $tahunanggaran = session('tahunanggaran', date('Y'));
        return KegiatanModel::select('kode as kode_kegiatan', 'deskripsi as nama_kegiatan')
            ->where('tahunanggaran', $tahunanggaran)
            ->whereIn('kode', function ($query) {
                $query->select('kodekegiatan')
                    ->from('anggaranbagian')
                    ->where('idbagian', Auth::user()->idbagian);
            })
            ->get();
    }

    /**
     * Get pengajuan count for generating kode
     *
     * @param string $jenis
     * @return int
     */
    private function getPengajuanCount($jenis)
    {
        return PengajuanRKBMNBagianModel::where('kode_jenis_pengajuan', 'like', "{$jenis}-%")->count();
    }

    /**
     * Generate kode pengajuan
     *
     * @param string $jenis
     * @param int $count
     * @return string
     */
    private function generateKodePengajuan($jenis, $count)
    {
        $nextIndex = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return $jenis . '-' . $nextIndex;
    }

    /**
     * Get ID biro from bagian
     *
     * @param int $idBagianPelaksana
     * @return int|null
     */
    private function getIdBiro($idBagianPelaksana)
    {
        return BagianModel::where('id', $idBagianPelaksana)->value('idbiro');
    }

    // ==========================================
    // SBSK RULE ENGINE INTEGRATION METHODS
    // ==========================================

    /**
     * Get the form component with SBSK support.
     */
    public function getFormComponent(Request $request)
    {
        try {
            $jenis = $request->get('jenis');
            $action = $request->get('action');

            // Initialize SBSK Rule Engine
            $sbskEngine = class_exists('\App\Services\SBSKRuleService') ?
                app(\App\Services\SBSKRuleService::class) : null;

            // Handle specific actions
            if ($action && $sbskEngine) {
                return $this->handleFormComponentAction($request, $sbskEngine);
            }

            if (!$jenis || !in_array($jenis, ['R1', 'R2', 'R3', 'R4', 'R5', 'R6'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jenis pengajuan tidak valid'
                ], 400);
            }

            // Get component data using SBSK engine
            $componentData = $this->getComponentData($jenis, $sbskEngine);

            // Render component view
            $viewName = "PerencanaanBMN.Bagian.pengajuanrkbmn.components.create.FormComponent{$jenis}";

            if (!view()->exists($viewName)) {
                throw new \Exception("View {$viewName} tidak ditemukan");
            }

            $html = view($viewName, $componentData)->render();

            return response()->json([
                'status' => 'success',
                'html' => $html,
                'sbsk_config' => $sbskEngine ? $sbskEngine->getFormConfig($jenis) : []
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getFormComponent: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat form component: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle specific form component actions
     */
    private function handleFormComponentAction(Request $request, $sbskEngine)
    {
        $action = $request->get('action');

        try {
            switch ($action) {
                case 'getRoomLimits':
                    $klasifikasi = $request->get('klasifikasi');
                    $roomLimits = $sbskEngine->getRoomLimitsR1($klasifikasi);
                    return response()->json(['success' => true, 'roomLimits' => $roomLimits]);

                case 'getHouseLandLimits':
                    $tipe = $request->get('tipe'); // Defined inside the case
                    $lokasi = $request->get('lokasi');
                    if (!$tipe || !$lokasi) {
                        return response()->json(['success' => false, 'message' => 'Tipe dan Lokasi dibutuhkan.'], 400);
                    }
                    $landLimits = $sbskEngine->getStandarLuasTanahR3($tipe, $lokasi);
                    return response()->json(['success' => true, 'landLimits' => $landLimits]);

                case 'getBuildingLimits':
                    $tipe = $request->get('tipe'); // Defined inside the case
                    if (!$tipe) {
                        return response()->json(['success' => false, 'message' => 'Tipe dibutuhkan.'], 400);
                    }
                    $buildingLimit = $sbskEngine->getStandarLuasBangunanR3($tipe);
                    return response()->json(['success' => true, 'buildingLimit' => $buildingLimit]);

                case 'getRoomRequirements':
                    $tipe = $request->get('tipe'); // Defined inside the case
                    if (!$tipe) {
                        return response()->json(['success' => false, 'message' => 'Tipe dibutuhkan.'], 400);
                    }
                    $kebutuhan = $sbskEngine->getKebutuhanRuangR3($tipe);
                    return response()->json(['success' => true, 'kebutuhan' => $kebutuhan]);

                case 'getVehicleSpecs':
                    $pejabat = $request->get('pejabat');
                    $specs = $sbskEngine->getPejabatToSpesifikasiMappingR4()[$pejabat] ?? [];
                    return response()->json(['success' => true, 'specifications' => $specs]);

                case 'validateFormData':
                    $data = $request->get('data', []);
                    $errors = $sbskEngine->validateFormData($data, $request->get('jenis'));
                    return response()->json(['success' => empty($errors), 'errors' => $errors]);

                default:
                    return response()->json(['success' => false, 'message' => 'Action tidak dikenali'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error in handleFormComponentAction for action '{$action}': " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Terjadi kesalahan pada server saat memproses action: {$action}"
            ], 500);
        }
    }

    /**
     * Get `bidang` with SBSK filtering and consistent response.
     */
    public function getBidang($kd_gol, Request $request)
    {
        try {
            $jenis = $request->get('jenis');

            Log::info("Loading bidang for golongan: {$kd_gol}, jenis: {$jenis}");

            // Use SBSK Rule Engine if available
            if (class_exists('\App\Services\SBSKRuleService')) {
                $sbskEngine = app(\App\Services\SBSKRuleService::class);
                $bidang = $sbskEngine->getBidang($kd_gol, $jenis);

                Log::info("SBSK Engine response:", $bidang);
                return response()->json($bidang);
            }

            // Fallback to direct database query with consistent format
            $bidang = Bidang::where('kd_gol', $kd_gol)
                ->select('kd_bid', 'ur_bid')
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->kd_bid,
                        'text' => $item->kd_bid . ' - ' . trim($item->ur_bid)
                    ];
                });

            Log::info("Direct DB response:", $bidang->toArray());
            return response()->json($bidang->toArray());

        } catch (\Exception $e) {
            Log::error('Error getting bidang: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data bidang',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get `kelompok` with SBSK filtering and consistent response.
     */
    public function getKelompok($kd_gol, $kd_bid, Request $request)
    {
        try {
            $jenis = $request->get('jenis');

            Log::info("Loading kelompok for golongan: {$kd_gol}, bidang: {$kd_bid}, jenis: {$jenis}");

            // Use SBSK Rule Engine if available
            if (class_exists('\App\Services\SBSKRuleService')) {
                $sbskEngine = app(\App\Services\SBSKRuleService::class);
                $kelompok = $sbskEngine->getKelompok($kd_gol, $kd_bid, $jenis);

                Log::info("SBSK Engine response:", $kelompok);
                return response()->json($kelompok);
            }

            // Fallback to direct database query with consistent format
            $kelompok = Kelompok::where('kd_gol', $kd_gol)
                ->where('kd_bid', $kd_bid)
                ->select('kd_kel', 'ur_kel')
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->kd_kel,
                        'text' => $item->kd_kel . ' - ' . trim($item->ur_kel)
                    ];
                });

            Log::info("Direct DB response:", $kelompok->toArray());
            return response()->json($kelompok->toArray());

        } catch (\Exception $e) {
            Log::error('Error getting kelompok: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data kelompok',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get `subkelompok` with SBSK filtering and consistent response.
     */
    public function getSubkelompok($kd_gol, $kd_bid, $kd_kel, Request $request)
    {
        try {
            $jenis = $request->get('jenis');

            Log::info("Loading subkelompok for golongan: {$kd_gol}, bidang: {$kd_bid}, kelompok: {$kd_kel}, jenis: {$jenis}");

            // Use SBSK Rule Engine if available
            if (class_exists('\App\Services\SBSKRuleService')) {
                $sbskEngine = app(\App\Services\SBSKRuleService::class);
                $subkelompok = $sbskEngine->getSubkelompok($kd_gol, $kd_bid, $kd_kel, $jenis);

                Log::info("SBSK Engine response:", $subkelompok);

                // Handle different response formats from SBSK Engine
                if (is_array($subkelompok)) {
                    // If it's already in correct format
                    if (isset($subkelompok['data']) && is_array($subkelompok['data'])) {
                        return response()->json($subkelompok['data']);
                    }
                    // If it's direct array
                    return response()->json($subkelompok);
                }

                // If it's object, convert to array
                if (is_object($subkelompok)) {
                    return response()->json(collect($subkelompok)->toArray());
                }
            }

            // Fallback to direct database query with consistent format
            $subkelompok = SubKelompok::where('kd_gol', $kd_gol)
                ->where('kd_bid', $kd_bid)
                ->where('kd_kel', $kd_kel)
                ->select('kd_skel', 'ur_skel')
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->kd_skel,
                        'text' => $item->kd_skel . ' - ' . trim($item->ur_skel)
                    ];
                });

            Log::info("Direct DB response:", $subkelompok->toArray());
            return response()->json($subkelompok->toArray());

        } catch (\Exception $e) {
            Log::error('Error getting subkelompok: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data subkelompok',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get `barang` with a consistent response format.
     */
    public function getBarang($kd_gol, $kd_bid, $kd_kel, $kd_skel, Request $request)
    {
        try {
            Log::info("Loading barang for golongan: {$kd_gol}, bidang: {$kd_bid}, kelompok: {$kd_kel}, subkelompok: {$kd_skel}");

            // Use SBSK Rule Engine if available
            if (class_exists('\App\Services\SBSKRuleService')) {
                $sbskEngine = app(\App\Services\SBSKRuleService::class);
                $barang = $sbskEngine->getBarang($kd_gol, $kd_bid, $kd_kel, $kd_skel);

                Log::info("SBSK Engine response:", $barang);
                return response()->json($barang);
            }

            // Fallback to direct database query with consistent format
            $barang = Barang::where('kd_gol', $kd_gol)
                ->where('kd_bid', $kd_bid)
                ->where('kd_kel', '>', $kd_kel)
                ->where('kd_skel', $kd_skel)
                ->select('kd_brg', 'ur_sskel')
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->kd_brg,
                        'text' => $item->kd_brg . ' - ' . trim($item->ur_sskel)
                    ];
                });

            Log::info("Direct DB response:", $barang->toArray());
            return response()->json($barang->toArray());

        } catch (\Exception $e) {
            Log::error('Error getting barang: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data barang',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get component data with SBSK integration.
     */
    private function getComponentData($jenis, $sbskEngine)
    {
        // Base data that already exists
        $baseData = [
            'listprogram' => $this->getListProgram(),
            'listoutput' => $this->getListOutput(),
            'datakegiatan' => $this->getDataKegiatan(),
            'golongan' => \App\Models\PerencanaanBMN\FilterBarang\Golongan::all(['kd_gol', 'ur_gol']),
            'jenis' => $jenis
        ];

        // Additional data from the SBSK engine based on type (if available)
        if ($sbskEngine) {
            switch ($jenis) {
                case 'R1':
                    $baseData['klasifikasiBangunan'] = $sbskEngine->getKlasifikasiBangunanR1();
                    $baseData['klasifikasiPejabat'] = $sbskEngine->getKlasifikasiPejabatR1();
                    $baseData['tujuanOptions'] = $sbskEngine->getTujuanRencanaOptions();
                    $baseData['atrOptions'] = $sbskEngine->getAtrNonAtrOptions();
                    break;

                case 'R3':
                    $baseData['peruntukanPejabat'] = $sbskEngine->getPeruntukanPejabatR3();
                    $baseData['lokasiRumah'] = $sbskEngine->getLokasiRumahR3();
                    $baseData['tujuanOptions'] = $sbskEngine->getTujuanRencanaOptions();
                    $baseData['atrOptions'] = $sbskEngine->getAtrNonAtrOptions();
                    break;

                case 'R4':
                    $baseData['klasifikasiPejabatKendaraan'] = $sbskEngine->getKlasifikasiPejabatKendaraanR4();
                    $baseData['spesifikasiKendaraan'] = [];
                    break;

                case 'R5':
                    $baseData['jenisSatkerOperasional'] = $sbskEngine->getJenisSatkerOperasionalR5();
                    $baseData['jenisKendaraanOperasional'] = $sbskEngine->getJenisKendaraanOperasionalR5();
                    $baseData['tujuanOptions'] = $sbskEngine->getTujuanRencanaOptions();
                    $baseData['atrOptions'] = $sbskEngine->getAtrNonAtrOptions();
                    break;

                    case 'R6':
                        $baseData['jenisSatkerFungsional'] = $sbskEngine->getJenisSatkerFungsionalR6();
                        $baseData['jenisKendaraanFungsional'] = $sbskEngine->getJenisKendaraanFungsionalR6();
                        break;
            }
        }

        return $baseData;
    }

    /**
     * Menampilkan preview Berita Acara yang sudah ditandatangani.
     */
    public function previewSignedBA($id)
    {
        try {
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel::findOrFail($id);
            $operatorPath = $pengajuan->berita_acara_sbsk_signed_path;

            if (!$operatorPath) {
                abort(404, 'File Berita Acara tidak ditemukan.');
            }

            $finalPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $operatorPath);

            $pathToUse = \Illuminate\Support\Facades\Storage::disk('public')->exists($finalPath)
                ? $finalPath
                : $operatorPath;

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($pathToUse)) {
                abort(404, 'File Berita Acara tertandatangani tidak ditemukan di storage.');
            }

            // Gunakan Storage::response() untuk menampilkan file (inline)
            return \Illuminate\Support\Facades\Storage::disk('public')->response($pathToUse);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error previewing signed BA: ' . $e->getMessage());
            abort(500, 'Gagal menampilkan dokumen.');
        }
    }

    /**
     * Mengunduh Berita Acara yang sudah ditandatangani.
     */
    public function downloadSignedBA($id)
    {
        try {
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel::findOrFail($id);
            $operatorPath = $pengajuan->berita_acara_sbsk_signed_path;

            if (!$operatorPath) {
                abort(404, 'File Berita Acara tidak ditemukan.');
            }

            // Membuat path untuk file final yang sudah di co-sign
            $finalPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $operatorPath);

            // Memilih path yang akan digunakan: final jika ada, jika tidak, gunakan versi operator
            $pathToUse = \Illuminate\Support\Facades\Storage::disk('public')->exists($finalPath)
                ? $finalPath
                : $operatorPath;

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($pathToUse)) {
                abort(404, 'File Berita Acara tertandatangani tidak ditemukan di storage.');
            }

            // Menentukan nama file download berdasarkan versi file
            $fileName = \Illuminate\Support\Facades\Storage::disk('public')->exists($finalPath)
                ? 'Berita_Acara_Final_Signed_' . $pengajuan->kode_jenis_pengajuan . '.pdf'
                : 'Berita_Acara_Signed_' . $pengajuan->kode_jenis_pengajuan . '.pdf';


            return response()->download(storage_path('app/public/' . $pathToUse), $fileName);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error downloading signed BA: ' . $e->getMessage());
            abort(500, 'Gagal mengunduh dokumen.');
        }
    }
}
