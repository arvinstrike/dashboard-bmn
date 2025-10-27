<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PerencanaanBMN\FilterBarang\Golongan;
use App\Models\PerencanaanBMN\FilterBarang\Bidang;
use App\Models\PerencanaanBMN\FilterBarang\Kelompok;
use App\Models\PerencanaanBMN\FilterBarang\SubKelompok;
use App\Models\PerencanaanBMN\FilterBarang\Barang;

class DropdownController extends Controller
{
    public function getBidang($kd_gol)
    {
         $bidang = Bidang::where('kd_gol', $kd_gol)->get();
         return response()->json($bidang);
    }

    public function getKelompok($kd_gol, $kd_bid)
    {
         $kelompok = Kelompok::where('kd_gol', $kd_gol)
                     ->where('kd_bid', $kd_bid)
                     ->get();
         return response()->json($kelompok);
    }

    public function getSubkelompok($kd_gol, $kd_bid, $kd_kel)
    {
         $subkelompok = Subkelompok::where('kd_gol', $kd_gol)
                         ->where('kd_bid', $kd_bid)
                         ->where('kd_kel', $kd_kel)
                         ->get();
         return response()->json($subkelompok);
    }

    public function getBarang($kd_gol, $kd_bid, $kd_kel, $kd_skel)
    {
         $barang = Barang::where('kd_gol', $kd_gol)
                  ->where('kd_bid', $kd_bid)
                  ->where('kd_kel', $kd_kel)
                  ->where('kd_skel', $kd_skel)
                  ->get();
         return response()->json($barang);
    }
}

