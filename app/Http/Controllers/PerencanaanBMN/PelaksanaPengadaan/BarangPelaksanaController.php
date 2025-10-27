<?php

namespace App\Http\Controllers\PerencanaanBMN\PelaksanaPengadaan;

use App\Exports\ExportDataBarang;
use App\Http\Controllers\Controller;
use App\Jobs\UpdateRegisterAset;
use App\Jobs\UpdateSisaMasaManfaat;
use App\Models\PerencanaanBMN\PelaksanaPengadaan\PengajuanRencanaPemeliharaanModel;
use App\Models\Sirangga\Admin\BarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class BarangPelaksanaController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function barang(){
        $judul = "Data Barang";
        $barang = DB::table('barang')->count();
        $databagianrk = DB::table('referensibagianrk as a')
            ->select(['a.idbagian as idbagian','a.idbiro as idbiro','b.uraianbagian as uraianbagian'])
            ->leftJoin('bagian as b','a.idbagian','=','b.id')
            ->where('a.status','=','on')
            ->get(['idbagian','uraianbagian']);
        $tahunanggaran = session('tahunanggaran');
        $budgetYears = [
            $tahunanggaran,
            $tahunanggaran + 1,
            $tahunanggaran + 2
        ];

        return view('PerencanaanBMN.PelaksanaPengadaan.barang',[
            "judul"=>$judul,
            "totalbarang" => $barang,
            "databagianrk" => $databagianrk,
            "datatahunanggaran" => $budgetYears
        ]);
    }

    public function getdatabarang(){
        $data = BarangModel::with('kodebarangrelation')
            ->select('barang.*');

        return (new \Yajra\DataTables\DataTables)->eloquent($data)
            ->addColumn('ur_sskel', function (BarangModel $barang) {
                return $barang->kodebarangrelation->ur_sskel;
            })
            ->addColumn('foto', function ($row) {
                $fotoPaths = explode(',', $row->foto);
                $gambar = '';
                if (count($fotoPaths) > 0 && !empty($fotoPaths[0])) {
                    foreach ($fotoPaths as $foto) {
                        $gambar .= '
                    <div class="input-group">
                        <div class="col-sm-12">
                            <div class="input-group mb-3">
                                <div class="user-panel">
                                    <div class="image">
                                        <img src="'.asset('storage')."/".$foto.'" class="img-circle elevation-2" alt="Foto BMN">
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm delete-photo" data-idbarang="'.$row->id.'" data-path="'.$foto.'" style="position: absolute; top: 5px; right: 25px; padding: 0; border: none; background: transparent;">
                                    <i class="fas fa-times" style="color: red;"></i>
                                </button>
                                <button class="btn btn-info btn-sm download-photo" data-idbarang="'.$row->id.'" data-path="'.$foto.'" style="position: absolute; top: 5px; right: 5px; padding: 0; border: none; background: transparent;">
                                    <i class="fas fa-download" style="color: green;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                ';
                    }
                }else {
                    // Tidak ada foto, tampilkan tombol upload saja
                    $gambar .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm uploadfoto">Upload Foto</a>';
                }

                // Menambahkan tombol upload foto di luar loop (tetap ditambahkan jika tidak ada foto)
                if (count($fotoPaths) > 0 && !empty($fotoPaths[0])) {
                    $gambar .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm uploadfoto">Upload Foto</a>';
                }

                return $gambar;
            })
            ->editColumn('statusdbr', function ($row) {
                if ($row->statusdbr == 2){
                    $kd_lokasi = $row->kd_lokasi;
                    $kd_brg = $row->kd_brg;
                    $no_aset = $row->no_aset;
                    $iddbr = DB::table('detildbr')
                        ->where('kd_lokasi','=',$kd_lokasi)
                        ->where('kd_brg','=',$kd_brg)
                        ->where('no_aset','=',$no_aset)
                        ->value('iddbr');
                    return "IDDBR ".$iddbr;
                }else{
                    return "Belum DBR";
                }
            })
            ->addColumn('action', function($row){
                if ($row->kondisi != 3){
                    $btn = '<div class="btn-group" role="group">
                            <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm ajukanpemeliharaan">Ajukan Pemeliharaan</a>';
                }else{
                    $btn = "";
                }

                return $btn;
            })
            ->rawColumns(['action','foto'])
            ->toJson();

    }

    public function dapatkandatabarang($id)
    {
        $menu = BarangModel::with('kodebarangrelation')->find($id);
        return response()->json($menu);
    }

    public function ajukanrencanapemeliharaan(Request $request){
        $validatedUmum = $request->validate([
            'jenistabel' => 'required',
            'idbagianpelaksana' => 'required',
            'tahunpemeliharaan' => 'required',
            'daftarhasilpemeliharaan' => 'required',
            'keterangan' => 'required'
        ]);


        $jenistabel = $request->get('jenistabel');
        if ($jenistabel == "R5"){
            $validatedkhusus = $request->validate([
                'luas' => 'required',
                'luastapak' => 'required',
                'luaspemanfaatan' => 'required',
                'luasriil' => 'required',
                'luaspemeliharaansatkerlain' => 'required'
            ]);

            $luas = $validatedkhusus['luas'];
            $luastapak = $validatedkhusus['luastapak'];
            $luaspemanfaatan = $validatedkhusus['luaspemanfaatan'];
            $luasriil = $validatedkhusus['luasriil'];
            $luaspemeliharaansatkerlain = $validatedkhusus['luaspemeliharaansatkerlain'];

            $datainsertkhusus = array(
                'luas' => $luas,
                'luastapak' => $luastapak,
                'luaspemanfaatan' => $luaspemanfaatan,
                'luasriil' => $luasriil,
                'luaspemeliharaansatkerlain' => $luaspemeliharaansatkerlain
            );

        }else{
            $datainsertkhusus = [];
        }

        if ($request->file('file')){
            $file = $request->file('file')->store(
                'dokumenpendukungrkbagian','public');
        }else{
            $file = null;
        }

        $jenistabel = $validatedUmum['jenistabel'];
        $idbagianpelaksana = $validatedUmum['idbagianpelaksana'];
        $idbiropelaksana = DB::table('bagian')->where('id','=',$validatedUmum['idbagianpelaksana'])->value('idbiro');
        $idbagianpengusul = Auth::user()->idbagian;
        $idbiropengusul = DB::table('bagian')->where('id','=',$idbagianpengusul)->value('idbiro');
        $tanggalpengajuan = now();
        $keterangan = $validatedUmum['keterangan'];
        $hasilpemeliharaan = $validatedUmum['daftarhasilpemeliharaan'];
        $status = "Draft";
        $dokumenpendukung = $file;
        $tahunanggaranpengusulan = $validatedUmum['tahunpemeliharaan'];

        $datainsertumum = array(
            'jenistabel' => $jenistabel,
            'idbagianpengusul' => $idbagianpengusul,
            'idbiropengusul' => $idbiropengusul,
            'idbagianpelaksana' => $idbagianpelaksana,
            'idbiropelaksana' => $idbiropelaksana,
            'tanggalpengajuan' => $tanggalpengajuan,
            'keterangan' => $keterangan,
            'hasilpemeliharaan' => $hasilpemeliharaan,
            'status' => $status,
            'dokumenpendukung' => $dokumenpendukung,
            'tahunanggaranpengusulan' => $tahunanggaranpengusulan
        );

        $datainsert = array_merge($datainsertkhusus, $datainsertumum);

        $pengajuan = PengajuanRencanaPemeliharaanModel::create($datainsert);

        if ($pengajuan) {
            return response()->json(['status' => 'berhasil']);
        } else {
            return response()->json(['status' => 'gagal'], 500);
        }

    }

    public function downloadfotobarang(Request $request)
    {
        $path = $request->input('path');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
    }

    public function hapusfotobarang(Request $request)
    {
        $idbarang = $request->input('idbarang');
        $path = $request->input('path');

        $barang = BarangModel::findOrFail($idbarang);

        // Pisahkan string path foto menjadi array
        $fotoPaths = explode(',', $barang->foto);

        // Cari dan hapus path yang dipilih dari array
        if (($key = array_search($path, $fotoPaths)) !== false) {
            unset($fotoPaths[$key]);

            // Hapus file dari storage
            Storage::disk('public')->delete($path);

            // Update field foto di database
            $barang->foto = implode(',', $fotoPaths);
            $barang->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function aksiuploadfoto(Request $request)
    {
        $request->validate([
            'fotobmn.*' => 'required|mimes:jpg,webp,jpeg,png|max:2048',
        ]);

        $idbarang = $request->input('idbarang');
        $barang = BarangModel::findOrFail($idbarang);

        // Ambil foto yang sudah ada di database
        $existingPhotos = !empty($barang->foto) ? explode(',', $barang->foto) : [];

        $newPhotoPaths = []; // Array untuk menyimpan path foto baru

        if ($request->hasFile('fotobmn')) {
            foreach ($request->file('fotobmn') as $index => $file) {
                // Membuat nama file yang unik
                $filename = uniqid('foto') . '_IDBarang' . $idbarang . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('fotobarang', $filename, 'public');

                $newPhotoPaths[] = $path; // Tambahkan path ke array foto baru
            }
        }

        // Gabungkan foto baru dengan foto yang sudah ada
        $allPhotoPaths = array_merge($existingPhotos, $newPhotoPaths);

        // Simpan array path sebagai string yang dipisahkan koma atau sebagai JSON
        $barang->foto = implode(',', $allPhotoPaths); // atau json_encode($allPhotoPaths);
        $barang->save();

        // Berikan respons JSON untuk AJAX
        return response()->json(['success' => true, 'message' => 'Foto berhasil diunggah!']);
    }


}
