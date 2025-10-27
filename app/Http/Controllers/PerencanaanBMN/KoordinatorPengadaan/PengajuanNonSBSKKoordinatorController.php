<?php

namespace App\Http\Controllers\PerencanaanBMN\KoordinatorPengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel;
use App\Models\PerencanaanBMN\Bagian\KantorModel;
use App\Models\PerencanaanBMN\Bagian\KendaraanJabatanModel;
use App\Models\PerencanaanBMN\Bagian\KendaraanOperasionalModel;
use App\Models\PerencanaanBMN\Bagian\RumahNegaraModel;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PengajuanNonSBSKKoordinatorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $judul = 'Data Pengajuan Kebutuhan BMN';

        if ($request->ajax()) {
            $idbagian = Auth::user()->idbagian;
            // $data = PengajuanRKBMNBagianModel::where('id_bagian_pelaksana', '=', $idbagian)
            //         ->where('status', '=', 'Diajukan Ke Unit Pelaksana')
            //         ->get();
            $data = PengajuanRKBMNBagianModel::where('status', '=', 'Diajukan Ke Koordinator')
                    ->get();

            // $data = PengajuanRKBMNBagianModel::all();
            // dd($data);


            return Datatables::of($data)
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">';

                    // Tombol Review (dengan data-base-url)
                    $btn .= '<button type="button" class="btn btn-info btn-sm review-pengajuan" data-id="'.$row->id.'" data-base-url="'.url('pengajuanrkbmn').'">Review</button>';
    //
    //                    if ($row->status == "Draft"){
    //                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editpengajuan">Edit</a>';
    //                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deletepengajuan">Delete</a>';
    //                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-info btn-sm kirimkepelaksana">Ajukan</a>';
    //                    }
                    $btn.='</div>';
                    return $btn;
                })
                ->addColumn('id_bagian_pelaksana',function ($row){
                    $idbagian = $row->idbagianpelaksana;
                    $uraianbiro = DB::table('bagian')->where('id','=',$idbagian)->value('uraianbagian');
                    return $uraianbiro;
                })
                ->addColumn('biropelaksana',function ($row){
                    $idbagian = $row->idbiropelaksana;
                    $uraianbiro = DB::table('biro')->where('id','=',$idbagian)->value('uraianbiro');
                    return $uraianbiro;
                })
                ->addColumn('kodebarang',function ($row){
                    $kodebarang = $row->kodebarang;
                    $uraianbarang = DB::table('t_brg')->where('kd_brg','=',$kodebarang)->value('ur_sskel');
                    return $kodebarang." | ".$uraianbarang;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('PerencanaanBMN.KoordinatorPengadaan.pengajuanrkbmnkoordinator',[
            "judul"=>$judul,
        ]);
    }

    public function formatulang($nilai){
        $nilai = str_replace("Rp","",$nilai);
        $nilai = str_replace(".00","",$nilai);
        $nilai = str_replace(",","",$nilai);
        return $nilai;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatestatus(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'approval' => 'required|string|in:setuju,tolak',
            'alasan' => 'nullable|string',
        ]);

        try {
            // Mengambil data berdasarkan ID, jika tidak ditemukan akan dilemparkan ModelNotFoundException
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);

            if ($validated['approval'] === "setuju") {
                // Jika disetujui, ubah status dan atur tanggal serta tahun anggaran persetujuan
                $pengajuan->status = 'Disetujui';
                $pengajuan->tanggal_pengajuan = now();
            } else {
                // Jika ditolak, kembalikan status ke Draft dan simpan alasan penolakan
                $pengajuan->status = 'Ditolak Koordinator BMN';
                $pengajuan->alasan_perencanaan = $validated['alasan'];
            }

            // Eloquent secara otomatis meng-update kolom updated_at
            $pengajuan->save();

            return response()->json(['status' => 'berhasil']);
        } catch (ModelNotFoundException $e) {
            // Tangani kasus data tidak ditemukan
            return response()->json(['status' => 'gagal', 'message' => 'Data pengajuan tidak ditemukan.'], 404); // 404 Not Found

        } catch (\Exception $e) {
            // Tangani error lainnya
            Log::error('Error kirimkekoordinatorpengadaan: ' . $e->getMessage());
            return response()->json(['status' => 'gagal', 'message' => 'Terjadi kesalahan saat memproses permintaan.'], 500); // 500 Internal Server Error
        }
    }



    public function show($id)
    {
        try {
            // Ambil data utama, TANPA eager loading relasi detail
            $data = PengajuanRKBMNBagianModel::with(['biroPengusul', 'biroPelaksana', 'bagianPengusul', 'bagianPelaksana', 'bangunanKantor', 'rumahNegara', 'kendaraanOperasional'])
                ->find($id);

            if (!$data) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }

            // Ambil jenis form dari kode_jenis_pengajuan
            $jenisForm = $data->kode_jenis_pengajuan;

            // Ambil data detail BERDASARKAN jenisForm
            $detailData = null;

            if (strpos($jenisForm, 'R1') === 0) {
                $detailData = $data->bangunanKantor;

            } elseif (strpos($jenisForm, 'R3') === 0) {
                $detailData = $data->rumahNegara;

            } elseif (strpos($jenisForm, 'R4') === 0) {
                $detailData = $data->kendaraanJabatan;
            } elseif (strpos($jenisForm, 'R5') === 0) {
                $detailData = $data->kendaraanOperasional;
            } elseif ($jenisForm == 'R6'){
                $detailData = $data->kendaraanFungsional;
            }
            else {
                // Handle kasus jika kode jenis tidak valid
                throw new \Exception('Kode Jenis Tidak valid.');
            }

            // Ambil data lain yang diperlukan
            $uraianBarang = DB::table('t_brg')->where('kd_brg', $data->kode_barang)->value('ur_sskel');
            $uraianBiroPengusul    = optional($data->biroPengusul)->uraianbiro;
            $uraianBagianPengusul  = optional($data->bagianPengusul)->uraianbagian;
            $uraianBagianPelaksana = optional($data->bagianPelaksana)->uraianbagian;
            $uraianBiroPelaksana   = optional($data->biroPelaksana)->uraianbiro;
            $tanggalPengajuan = Carbon::parse($data->tanggal_pengajuan)->translatedFormat('j F Y');
            $tahunAnggaran = $data->tahunanggaranpengusulan;
            $program = $data->program;
            $status = $data->status;

            // Kirim jenisForm dan detailData ke JavaScript
            return response()->json([
                'success'            => true,
                'data'              => $data,
                'detailData'        => $detailData,
                'uraianBarang'          => $uraianBarang,
                'uraianBiroPengusul'    => $uraianBiroPengusul,
                'uraianBagianPengusul'  => $uraianBagianPengusul,
                'uraianBagianPelaksana' => $uraianBagianPelaksana,
                'uraianBiroPelaksana'   => $uraianBiroPelaksana,
                'tahunAnggaran'         => $tahunAnggaran,
                'program'          => $program,
                'tanggalPengajuan'  => $tanggalPengajuan,
                'status'            => $status,
                'jenisForm'         => $jenisForm,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error di show method: ' . $e->getMessage());
            return response()->json(['status' => 'gagal', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500); //jangan tampilkan error ke user
        }
    }
}
