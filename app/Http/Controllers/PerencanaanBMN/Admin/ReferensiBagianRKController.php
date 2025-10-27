<?php

namespace App\Http\Controllers\PerencanaanBMN\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerencanaanBMN\Admin\ReferensiBagianRKModel;
use App\Models\ReferensiUnit\BiroModel;
use App\Models\ReferensiUnit\DeputiModel;
use App\Models\ReferensiUnit\BagianModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReferensiBagianRKController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $judul = 'List Referensi Bagian RK';
        $datadeputi = DeputiModel::all();
        $databiro = BiroModel::all();
        if ($request->ajax()) {
            $data = ReferensiBagianRKModel::all();
            return Datatables::of($data)
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editbagian">Edit</a>';
                    $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deletebagian">Delete</a>';
                    return $btn;
                })
                ->addColumn('iddeputi',function ($row){
                    $iddeputi = $row->iddeputi;
                    $uraiandeputi = DB::table('deputi')->where('id','=',$iddeputi)->value('uraiandeputi');
                    return $uraiandeputi;
                })
                ->addColumn('idbiro',function ($row){
                    $idbiro = $row->idbiro;
                    $uraianbiro = DB::table('biro')->where('id','=',$idbiro)->value('uraianbiro');
                    return $uraianbiro;
                })
                ->addColumn('idbagian',function ($row){
                    $idbiro = $row->idbagian;
                    $uraianbiro = DB::table('bagian')->where('id','=',$idbiro)->value('uraianbagian');
                    return $uraianbiro;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('PerencanaanBMN.Admin.referensibagianrk',[
            "judul"=>$judul,
            "datadeputi" => $datadeputi,
            "databiro" => $databiro
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->get('status') == null){
            $status = "off";
        }else{
            $status = "on";
        }
        $validated = $request->validate([
            'iddeputi' => 'required',
            'idbiro' => 'required',
            'idbagian' => 'required',
            'uraianbagiansakti' => 'required'
        ]);

        ReferensiBagianRKModel::create(
            [
                'iddeputi' => $request->get('iddeputi'),
                'idbiro' => $request->get('idbiro'),
                'idbagian' => $request->get('idbagian'),
                'uraianbagiansakti' => $request->get('uraianbagiansakti'),
                'status' => $status
            ]);

        return response()->json(['status'=>'berhasil']);
    }


    public function edit($id)
    {
        $menu = ReferensiBagianRKModel::find($id);
        return response()->json($menu);
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
        if ($request->get('status') == null){
            $status = "off";
        }else{
            $status = "on";
        }
        $validated = $request->validate([
            'iddeputi' => 'required',
            'idbiro' => 'required',
            'idbagian' => 'required',
            'uraianbagiansakti' => 'required'
        ]);

        ReferensiBagianRKModel::where([
            'id' => $id
        ])->update(
            [
                'iddeputi' => $request->get('iddeputi'),
                'idbiro' => $request->get('idbiro'),
                'idbagian' => $request->get('idbagian'),
                'uraianbagiansakti' => $request->get('uraianbagiansakti'),
                'status' => $status
            ]);

        return response()->json(['status'=>'berhasil']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //TODO
        //cek penggunaan referensi bagian pada tabel monitoring

        $adadata = DB::table('referensibagianrk')->where('id','=',$id)->count();
        if ($adadata != 0){
            ReferensiBagianRKModel::find($id)->delete();
            return response()->json(['status'=>'berhasil']);
        }else{
            return response()->json(['status'=>'gagal']);
        }

    }
}
