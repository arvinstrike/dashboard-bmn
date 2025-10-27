<?php

namespace App\Http\Controllers\PerencanaanBMN\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportAsetKodeBarang;
use App\Libraries\BearerKey;
use App\Libraries\TarikDataMonsakti;
use App\Models\PerencanaanBMN\Admin\ReferensiBMNRKModel;
use App\Models\PerencanaanBMN\Admin\SBSKBMNRKModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SBSKController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $judul = 'Data SBSK Per Kode Barang';
        $datakodebarang = ReferensiBMNRKModel::all();
        if ($request->ajax()) {
            $data = DB::table('sbskbmnrk')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group" role="group">
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Import" class="btn btn-info btn-sm editkodebarang">Edit</a>';
                    $btn = $btn.'<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deletekodebarang">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('PerencanaanBMN.Admin.sbsk',[
            "judul"=>$judul,
            "datakodebarang" => $datakodebarang
        ]);
    }

    public function store(Request $request)
    {
        $kodebarang = $request->get('kodebarang');
        $sbsk = $request->get('sbsk');
        //cek apakah ada
        $adakode = DB::table('sbskbmnrk')->where('kdbrg','=',$kodebarang)->count();
        if ($adakode == 0){
            $deskripsi = DB::table('t_brg')->where('kd_brg','=',$kodebarang)->value('ur_sskel');
            $kdgol = substr($kodebarang,0,1);
            $kdbid = substr($kodebarang,0,3);
            $kdkel = substr($kodebarang,0,5);
            $kdskel = substr($kodebarang,0,7);

            DB::table('sbskbmnrk')->insert([
                'kdgol' => $kdgol,
                'kdbid' => $kdbid,
                'kdkel' => $kdkel,
                'kdskel' => $kdskel,
                'kdbrg' => $kodebarang,
                'deskripsi' => $deskripsi,
                'jumlahkebutuhan' => $sbsk
            ]);
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }

    public function edit($id)
    {
        $subarea = SBSKBMNRKModel::find($id);
        return response()->json($subarea);
    }

    public function update(Request $request, $id)
    {
        $kodebarang = $request->get('kodebarang');
        $sbsk = $request->get('sbsk');
        //cek apakah ada
        $adakode = DB::table('sbskbmnrk')->where('kdbrg','=',$kodebarang)->count();
        if ($adakode == 0){
            $deskripsi = DB::table('t_brg')->where('kd_brg','=',$kodebarang)->value('ur_sskel');
            $kdgol = substr($kodebarang,0,1);
            $kdbid = substr($kodebarang,0,3);
            $kdkel = substr($kodebarang,0,5);
            $kdskel = substr($kodebarang,0,7);

            DB::table('sbskbmnrk')->updateOrInsert([
                'id' => $id
            ],[
                'kdgol' => $kdgol,
                'kdbid' => $kdbid,
                'kdkel' => $kdkel,
                'kdskel' => $kdskel,
                'kdbrg' => $kodebarang,
                'deskripsi' => $deskripsi,
                'jumlahkebutuhan' => $sbsk
            ]);
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }

    public function destroy($id)
    {
        //cek apakah ada
        $adakode = DB::table('sbskbmnrk')->where('kdbrg','=',$id)->count();
        if ($adakode > 0){
            DB::table('sbskbmnrk')->where('id','=',$id)->delete();
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }
}
