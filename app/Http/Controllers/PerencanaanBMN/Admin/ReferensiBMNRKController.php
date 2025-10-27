<?php

namespace App\Http\Controllers\PerencanaanBMN\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportAsetKodeBarang;
use App\Libraries\BearerKey;
use App\Libraries\TarikDataMonsakti;
use App\Models\PerencanaanBMN\Admin\ReferensiBMNRKModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReferensiBMNRKController extends Controller
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
        $judul = 'Data Kode Barang Wajib RK BMN';
        $datakodebarang = DB::table('t_brg')->get();
        if ($request->ajax()) {
            $data = DB::table('referensibmnrk')->get();
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
        return view('PerencanaanBMN.Admin.referensibmnrk',[
            "judul"=>$judul,
            "datakodebarang" => $datakodebarang
        ]);
    }

    public function store(Request $request)
    {
        $kodebarang = $request->get('kodebarang');
        $kewenangan = $request->get('kewenangan');
        //cek apakah ada
        $adakode = DB::table('referensibmnrk')->where('kdbrg','=',$kodebarang)->count();
        if ($adakode == 0){
            $deskripsi = DB::table('t_brg')->where('kd_brg','=',$kodebarang)->value('ur_sskel');
            $kdgol = substr($kodebarang,0,1);
            $kdbid = substr($kodebarang,0,3);
            $kdkel = substr($kodebarang,0,5);
            $kdskel = substr($kodebarang,0,7);

            DB::table('referensibmnrk')->insert([
                'kdgol' => $kdgol,
                'kdbid' => $kdbid,
                'kdkel' => $kdkel,
                'kdskel' => $kdskel,
                'kdbrg' => $kodebarang,
                'deskripsi' => $deskripsi,
                'kewenangan'=> $kewenangan
            ]);
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }

    public function edit($id)
    {
        $subarea = ReferensiBMNRKModel::find($id);
        return response()->json($subarea);
    }

    public function update(Request $request, $id)
    {
        $kodebarang = $request->get('kodebarang');
        $kewenangan = $request->get('kewenangan');
        //cek apakah ada
        $adakode = DB::table('referensibmnrk')->where('kdbrg','=',$kodebarang)->count();
        if ($adakode == 0){
            $deskripsi = DB::table('t_brg')->where('kd_brg','=',$kodebarang)->value('ur_sskel');
            $kdgol = substr($kodebarang,0,1);
            $kdbid = substr($kodebarang,0,3);
            $kdkel = substr($kodebarang,0,5);
            $kdskel = substr($kodebarang,0,7);

            DB::table('referensibmnrk')->updateOrInsert([
                'id' => $id
            ],[
                'kdgol' => $kdgol,
                'kdbid' => $kdbid,
                'kdkel' => $kdkel,
                'kdskel' => $kdskel,
                'kdbrg' => $kodebarang,
                'deskripsi' => $deskripsi,
                'kewenangan'=> $kewenangan
            ]);
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }

    public function destroy($id)
    {
        //cek apakah ada
        $adakode = DB::table('referensibmnrk')->where('id','=',$id)->count();
        if ($adakode > 0){
            DB::table('referensibmnrk')->where('id','=',$id)->delete();
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }
    }
}
