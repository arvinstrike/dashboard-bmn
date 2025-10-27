<?php
//app/Http/Controllers/PerencanaanBMN/Bagian/NonSBSK/PengajuanController.php
namespace App\Http\Controllers\PerencanaanBMN\Bagian\NonSBSK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilPengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilRevisi;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\Perlengkapan;
use App\Models\ReferensiAnggaran\KegiatanModel;
use App\Models\ReferensiAnggaran\ProgramModel;
use App\Models\ReferensiAnggaran\OutputModel;
use App\Models\Realisasi\Admin\LaporanRealisasiAnggaranModel;
use App\Models\ReferensiUnit\BagianModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Pastikan controller hanya diakses oleh user yang sudah ter-auth.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Tampilkan dashboard pengajuan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tahunanggaran = session('tahunanggaran');
        $idbagian = Auth::user()->idbagian;

        // Filter pengajuan sesuai id_bagian_pengusul
        $pengajuan = Pengajuan::where('id_bagian_pengusul', $idbagian)
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Ambil uraianbagian dari model Bagian
        $deskripsi = BagianModel::where('id', $idbagian)
                                ->value('uraianbagian');

        // Kirim data ke view
        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.index', compact('pengajuan', 'tahunanggaran', 'deskripsi'));
    }

        /**
     * Tampilkan dashboard pengajuan untuk pelaksana.
     *
     * @return \Illuminate\View\View
     */
    public function indexPelaksana()
    {
        // Ambil pengajuan dengan status "Diajukan ke Unit Pelaksana"
        $pengajuan = Pengajuan::where('status_pengajuan', 'Diajukan ke Unit Pelaksana')
                        ->orderBy('created_at', 'desc')
                        ->get();
        $tahunanggaran = session('tahunanggaran');

        return view('PerencanaanBMN.Bagian.pelaksana_nonsbsk.index', compact('pengajuan', 'tahunanggaran'));
    }

    /**
     * Tampilkan dashboard pengajuan untuk koordinator.
     *
     * @return \Illuminate\View\View
     */
    public function indexKoordinator()
    {
        // Ambil pengajuan dengan status "Diajukan ke Koordinator"
        $pengajuan = Pengajuan::where('status_pengajuan', 'Diajukan ke Koordinator')
                        ->orderBy('created_at', 'desc')
                        ->get();
        $tahunanggaran = session('tahunanggaran');

        return view('PerencanaanBMN.Bagian.koordinator_nonsbsk.DashboardKoordinatorNonSBSK', compact('pengajuan', 'tahunanggaran'));
    }


    /**
     * Tampilkan form untuk membuat pengajuan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        $tahunAnggaran = session('tahunanggaran');
        $bagianPengaju = Auth::user()->idbagian;
        $biroPengaju = Auth::user()->idbiro;

        /* --------------------------------------------------------------------
          URAIAN BAGIAN DAN URAIAN BIRO
       -------------------------------------------------------------------- */
        $uraianBagian = DB::table('bagian')
        ->where('id', $bagianPengaju)
        ->value('uraianbagian');
        $uraianBiro = DB::table('biro')
            ->where('id', $biroPengaju)
            ->value('uraianbiro');


        $penggunaOptions = DB::table('bmn_ref_bagian_pengguna_nonsbsk')
            ->where('id_bagian', Auth::user()->idbagian)
            ->get();

        $pelaksanaOptions = BagianModel::whereIn('idbiro',[677,688,728,617,605])->get();

        // --- HANDLE DROPDOWN PENGGUNA ---
        // 1. Ambil data pengguna ruangan dari tabel bmn_ref_bagian_pengguna_nonsbsk
        $bagianPenggunaData = DB::table('bmn_ref_bagian_pengguna_nonsbsk')
            ->where('id_bagian', $bagianPengaju)
            ->get();
        $kodePenggunaList = $bagianPenggunaData->pluck('kode_pengguna')->toArray();

        // 2. Ambil pegawai dari tabel pegawai berdasarkan id_bagian
        $pegawaiData = DB::table('pegawai')
            ->where('id_satker', $bagianPengaju)
            ->pluck('eselon')
            ->toArray();
        // 3. Mapping eselon ke kode pengguna untuk kategori jabatan
        // Jika nilai eselon tidak sesuai, gunakan if untuk melewati.
        $kodePenggunaJabatan = [];
        foreach ($pegawaiData as $eselon) {
            if ($eselon === 'I') {
                $kodePenggunaJabatan[] = 101;
            } elseif ($eselon === 'II') {
                $kodePenggunaJabatan[] = 102;
            } elseif ($eselon === 'III') {
                $kodePenggunaJabatan[] = 103;
            } elseif ($eselon === 'IV') {
                $kodePenggunaJabatan[] = 104;
            } elseif ($eselon === 'JFU') {
                $kodePenggunaJabatan[] = 105;
            }
        }

        $staffCount = count(array_filter($pegawaiData, function($eselon) {
            return in_array($eselon, ['JFU', 'JFT']);
        }));
        \Log::info('Staff count in bagian', [
            'idbagian' => $bagianPengaju,
            'staff_count' => $staffCount
        ]);

        // Gabungkan dan hilangkan duplikat
        $combinedKodePengguna = array_unique(array_merge($kodePenggunaList, $kodePenggunaJabatan));

        // Ambil data pengguna (deskripsi) dari tabel ref pengguna
        $penggunaOptions = DB::table('bmn_ref_pengguna_nonsbsk')
            ->whereIn('kode_pengguna', $combinedKodePengguna)
            ->get();

        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.create', compact(
            'bagianPengaju',
            'biroPengaju',
            'uraianBagian',
            'uraianBiro',
            'penggunaOptions',
            'tahunAnggaran',
            'pelaksanaOptions'
        ));
    }

    public function getOutputByKegiatan(Request $request)
    {
        $kodeKegiatan = $request->input('kodekegiatan');
        $tahunAnggaran = session('tahunanggaran');
        $outputs = OutputModel::where('kodekegiatan', $kodeKegiatan)
            ->where('tahunanggaran', $tahunAnggaran)
            ->get();
        return response()->json($outputs);
    }

    public function getPerlengkapanByPengguna(Request $request)
    {
        $kode_pengguna = $request->input('kode_pengguna');
        if (!$kode_pengguna) {
            return response()->json([], 400);
        }
        $perlengkapan = Perlengkapan::where('kode_pengguna', $kode_pengguna)->get();
        \Log::info('Perlengkapan by Pengguna', [
            'kode_pengguna' => $kode_pengguna,
            'count' => count($perlengkapan)
        ]);
        return response()->json($perlengkapan);
    }


    public function getKuantitasMaksimal(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'kode_pengguna' => 'required|string',
            'kode_perlengkapan' => 'required|string', // Jabatan dari pengguna yang dipilih
        ]);

        // Ambil idbagian dari user yang sedang login
        $idbagian = Auth::user()->idbagian;

        \Log::info('getKuantitasMaksimal input', [
            'kode_pengguna' => $request->kode_pengguna,
            'kode_perlengkapan' => $request->kode_perlengkapan,
            'idbagian' => $idbagian
        ]);

        // Cari data perlengkapan berdasarkan kode pengguna yang dipilih
        $perlengkapan = Perlengkapan::where('kode_pengguna', $request->kode_pengguna)
                              ->where('kode_perlengkapan', $request->kode_perlengkapan)
                              ->first();

        if (!$perlengkapan) {
            \Log::warning('Perlengkapan not found', [
                'kode_pengguna' => $request->kode_pengguna,
                'kode_perlengkapan' => $request->kode_perlengkapan
            ]);
            return response()->json([
                'error' => 'Data perlengkapan tidak ditemukan.'
            ], 404);
        }

        $batasanJumlah = $perlengkapan->batasan_jumlah;
        $totalLimit = $batasanJumlah;

        \Log::info('Perlengkapan data', [
            'kode_barang' => $perlengkapan->kode_barang,
            'batasan_jumlah' => $batasanJumlah
        ]);


        // Jika kode pengguna diawali dengan '1', artinya pejabat
        if (substr($request->kode_pengguna, 0, 1) === '1') {
            // Hitung jumlah pegawai di bagian dengan jabatan yang sama
            $eselonFilter = null;
            $jfuJftFilter = false;

            switch ($request->kode_pengguna) {
                case '101':
                    $eselonFilter = 'I';
                    break;
                case '102':
                    $eselonFilter = 'II';
                    break;
                case '103':
                    $eselonFilter = 'III';
                    break;
                case '104':
                    $eselonFilter = 'IV';
                    break;
                case '105':
                    $jfuJftFilter = true;
                    break;
            }

            $query = DB::table('pegawai')->where('id_satker', $idbagian);

            if ($jfuJftFilter) {
                $query->whereIn('eselon', ['JFT', 'JFU']);
            } elseif ($eselonFilter) {
                $query->where('eselon', $eselonFilter);
            }

            $pegawaiCount = $query->count();

            \Log::info('Pegawai count', [
                'kode_pengguna' => $request->kode_pengguna,
                'filter_type' => $jfuJftFilter ? 'JFU/JFT' : 'Eselon ' . $eselonFilter,
                'count' => $pegawaiCount
            ]);

            // Total limit adalah jumlah pegawai dikali batasan_jumlah dari data perlengkapan
            $totalLimit = $pegawaiCount * $batasanJumlah;

            // Handling untuk kasus staff dengan count 0
            if ($pegawaiCount == 0 && $request->kode_pengguna == '105') {
                \Log::warning('No staff found in bagian', [
                    'idbagian' => $idbagian,
                    'kode_pengguna' => $request->kode_pengguna
                ]);
                // Gunakan minimal 1 untuk menghindari total limit 0 pada kasus staff
                $totalLimit = $batasanJumlah;
            }

        } else {
            // Untuk pengguna dengan kode awalan '2' (ruangan), gunakan batasan_jumlah langsung
            $totalLimit = $batasanJumlah;
        }
        // Query jumlah barang yang sudah ada di tabel detildbr untuk kode_barang tersebut pada bagian ini
        $barangTerdaftar = DB::select("SELECT COUNT(*) as count
        FROM digitall.detildbr AS a
        LEFT JOIN digitall.dbrinduk AS c ON c.iddbr = a.iddbr
        LEFT JOIN digitall.ruangan AS d ON c.idruangan = d.id
        WHERE a.kd_brg = ? AND d.idbagian = ?", [$perlengkapan->kode_barang, $idbagian]);

        $jumlahBarangTerdaftar = $barangTerdaftar[0]->count;


        \Log::info('Existing items', [
            'kode_barang' => $perlengkapan->kode_barang,
            'count' => $jumlahBarangTerdaftar
        ]);

        // Hitung kuantitas maksimal yang diizinkan
        $kuantitasMaksimal = $totalLimit - $jumlahBarangTerdaftar;
        $kuantitasMaksimal = max(0, $kuantitasMaksimal);

        \Log::info('Calculated values', [
            'total_limit' => $totalLimit,
            'jumlah_barang_terdaftar' => $jumlahBarangTerdaftar,
            'kuantitas_maksimal' => $kuantitasMaksimal,
            'dapat_diinput' => $kuantitasMaksimal > 0
        ]);

        return response()->json([
            'kuantitas_maksimal' => $kuantitasMaksimal,
            'total_limit'        => $totalLimit,
            'barang_terdaftar'   => $jumlahBarangTerdaftar,
            'dapat_diinput' => $kuantitasMaksimal > 0
        ]);
    }

    public function getJumlahPegawai(Request $request)
    {
        $request->validate([
            'kode_pengguna' => 'required|string',
        ]);

        $idbagian = Auth::user()->idbagian;
        $jumlahPegawai = 0;
        $index = substr($request->kode_pengguna, 0, 1);

        if($index !== '1'){
            return response()->json(['jumlah_pegawai' => 0]);
        }

        $eselonFilter = null;
        $jfuJftFilter = false;

        switch ($request->kode_pengguna) {
            case '101':
                $eselonFilter = 'I';
                break;
            case '102':
                $eselonFilter = 'II';
                break;
            case '103':
                $eselonFilter = 'III';
                break;
            case '104':
                $eselonFilter = 'IV';
                break;
            case '105':
                $jfuJftFilter = true;
                break;
        }

        $query = DB::table('pegawai')->where('id_satker', $idbagian);

        if ($jfuJftFilter) {
            $query->whereIn('eselon', ['JFT', 'JFU']);
        } elseif ($eselonFilter) {
            $query->where('eselon', $eselonFilter);
        }

        $jumlahPegawai = $query->count();

        return response()->json(['jumlah_pegawai' => $jumlahPegawai]);
    }



    /**
     * Simpan data pengajuan baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $isUsulan = $request->tahun_anggaran == session('tahunanggaran') + 1;

            // Validasi field header pengajuan
            $validatedData = $request->validate([
                'tahun_anggaran'         => 'required|integer',
                'tipe_pengajuan'         => 'required|in:revisi,usulan',
                'keterangan'             => 'required|string',
                'id_bagian_pengusul'     => 'nullable|integer',
                'id_biro_pengusul'       => 'nullable|integer',
                'id_bagian_pelaksana'    => 'required|integer',
                'id_biro_pelaksana'      => 'nullable|integer',
                'status_pengajuan'       => 'nullable|string|max:50',
                'alasan_penolakan_pelaksana'  => 'nullable|string',
                'alasan_penolakan_koordinator'=> 'nullable|string',
                'created_by'             => 'nullable|string',
            ]);

            $validatedData['id_bagian_pengusul'] = Auth::user()->idbagian;
            $validatedData['id_biro_pengusul'] = Auth::user()->idbiro;

            // Use the same approach as in PengajuanRegulerController for consistency
            $validatedData['created_by'] = Auth::user()->username ?? Auth::user()->name ?? Auth::id();

            // Tentukan tipe pengajuan
            if ($validatedData['tahun_anggaran'] == session('tahunanggaran')) {
                $validatedData['tipe_pengajuan'] = 'revisi';
            } else {
                $validatedData['tipe_pengajuan'] = 'usulan';
            }

            // Validasi input barang
            $barangItems = $request->input('barang', []);

            if (empty($barangItems)) {
                return response()->json([
                    'status' => 'gagal',
                    'error' => 'Tidak ada barang yang diajukan.'
                ], 422);
            }

            $totalBarang = 0;

            foreach ($barangItems as $index => $barang) {
                // Validasi perlengkapan
                $perlengkapan = Perlengkapan::where('kode_perlengkapan', $barang['kode_perlengkapan'])->first();
                if (!$perlengkapan) {
                    return response()->json([
                        'status' => 'gagal',
                        'error' => "Data referensi untuk kode_perlengkapan {$barang['kode_perlengkapan']} tidak ditemukan."
                    ], 422);
                }

                // Validasi kuantitas maksimal
                $kuantitasRequest = new Request([
                    'kode_pengguna' => $barang['pengguna'],
                    'kode_perlengkapan' => $barang['kode_perlengkapan']
                ]);
                $response = $this->getKuantitasMaksimal($kuantitasRequest)->getData();

                if (!isset($response->kuantitas_maksimal)) {
                    return response()->json([
                        'status' => 'gagal',
                        'error' => "Gagal mengambil kuantitas maksimal untuk {$barang['kode_perlengkapan']}."
                    ], 500);
                }

                if ($barang['kuantitas'] > $response->kuantitas_maksimal) {
                    return response()->json([
                        'status' => 'gagal',
                        'error' => "Kuantitas melebihi batas yang diizinkan untuk kode_perlengkapan {$barang['kode_perlengkapan']}."
                    ], 422);
                }

                // Hitung total
                $barangItems[$index]['total'] = $barang['kuantitas'] * $barang['harga'];
                $barangItems[$index]['kode_barang'] = $perlengkapan->kode_barang;
                $totalBarang += $barangItems[$index]['total'];
            }

            // Proses simpan dalam transaksi database
            DB::beginTransaction();

            $pengajuan = Pengajuan::create($validatedData);

            // Generate kode_pengajuan based on the pengajuan ID and type
            if ($pengajuan) {
                $tahunAnggaran = session('tahunanggaran');
                $idBagian = Auth::user()->idbagian;

                if ($validatedData['tipe_pengajuan'] == 'revisi') {
                    $kode = "PMB-{$idBagian}-{$tahunAnggaran}-{$pengajuan->id}";
                } else { // usulan
                    $tahunPlusOne = $tahunAnggaran + 1;
                    $kode = "PRC-{$idBagian}-{$tahunPlusOne}-{$pengajuan->id}";
                }

                // Update the pengajuan with the generated code
                $pengajuan->kode_pengajuan = $kode;
                $pengajuan->save();

                if ($validatedData['tipe_pengajuan'] == 'usulan') {
                    foreach ($barangItems as $barang) {
                        DetilPengajuan::create([
                            'pengajuan_id'    => $pengajuan->id,
                            'kode_barang'       => $barang['kode_barang'],
                            'kode_perlengkapan' => $barang['kode_perlengkapan'],
                            'kuantitas'       => $barang['kuantitas'],
                            'harga'           => $barang['harga'],
                            'total'           => $barang['total'],
                        ]);
                    }
                } else {
                    foreach ($barangItems as $barang) {
                        DetilRevisi::create([
                            'pengajuan_id'    => $pengajuan->id,
                            'kode_perlengkapan' => $barang['kode_perlengkapan'],
                            'kode_barang'       => $barang['kode_barang'],
                            'kuantitas'       => $barang['kuantitas'],
                            'harga'           => $barang['harga'],
                            'total'           => $barang['total'],
                        ]);
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status'  => 'gagal',
                    'message' => 'Gagal membuat header pengajuan.'
                ], 500);
            }

            DB::commit();

            return response()->json(['status' => 'berhasil', 'redirect' => route('pengajuan.index')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Terjadi kesalahan pada database.'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'gagal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDBR(Request $request)
    {
        // Mendapatkan idbagian dari pengguna yang sedang login
        $idbagian = Auth::user()->idbagian;

        // Mendapatkan kode_perlengkapan dari request
        $perlengkapan = Perlengkapan::where('kode_perlengkapan', $request->kode_perlengkapan)
                                ->first();

        // Query untuk mendapatkan nilai DBR berdasarkan kode_perlengkapan dan idbagian
        $barangTerdaftar = DB::select("SELECT COUNT(*) as count
            FROM digitall.detildbr AS a
            LEFT JOIN digitall.dbrinduk AS c ON c.iddbr = a.iddbr
            LEFT JOIN digitall.ruangan AS d ON c.idruangan = d.id
            WHERE a.kd_brg = ? AND d.idbagian = ?", [$perlengkapan->kode_barang, $idbagian]);

        $jumlahBarangTerdaftar = $barangTerdaftar[0]->count; // If no rows are returned, default to 0

        // Mengembalikan nilai DBR sebagai respons
        return response()->json([
            'dbr' => $jumlahBarangTerdaftar ?? 0 // Jika tidak ada data, return 0
        ]);
    }




    public function show($id)
    {
        try {
            // Cari data pengajuan
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
                $totalAnggaranPengajuan = 0;
                foreach ($pengajuan->detilPengajuan as $index => $item) {
                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                        ->where('kode_perlengkapan', $item->kode_perlengkapan)
                        ->first();

                    // Hitung total yang benar berdasarkan kuantitas dan harga
                    $itemTotal = $item->kuantitas * $item->harga;
                    $totalAnggaranPengajuan += $itemTotal;

                    $detilPengajuan[] = [
                        'no' => $index + 1,
                        'id' => $item->id,
                        'kode_perlengkapan' => $item->kode_perlengkapan,
                        'kode_barang' => $item->kode_barang,
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $itemTotal // Gunakan total yang benar
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
                        'kode_barang' => $item->kode_barang,
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
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

            // Cek status verifikasi dokumen
            $dokumenterVerified = false;
            $lampiranVerified = false;
            $downloadUrlTor = null;
            $downloadUrlLampiran = null;

            if ($pengajuan->tor_signed_path) {
                $dokumenterVerified = true;
                $downloadUrlTor = url('storage/' . $pengajuan->tor_signed_path);
            }

            if ($pengajuan->lampiran_signed_path) {
                $lampiranVerified = true;
                $downloadUrlLampiran = url('storage/' . $pengajuan->lampiran_signed_path);
            }

            // Pastikan nilai boolean yang diberikan adalah sesuai dengan keberadaan path file
            $beritaAcaraOperatorSigned = !empty($pengajuan->berita_acara_operator_signed_path);
            $beritaAcaraPelaksanaSigned = !empty($pengajuan->berita_acara_pelaksana_signed_path);
            $beritaAcaraKoordinatorSigned = !empty($pengajuan->berita_acara_koordinator_signed_path);

            // Log untuk debugging
//            \Log::info('Status berita acara untuk ID: ' . $id, [
//                'operator_signed' => $beritaAcaraOperatorSigned,
//                'operator_path' => $pengajuan->berita_acara_operator_signed_path,
//                'pelaksana_signed' => $beritaAcaraPelaksanaSigned,
//                'koordinator_signed' => $beritaAcaraKoordinatorSigned
//            ]);

            // Siapkan data untuk response
            $response = [
                'id' => $pengajuan->id,
                'kode_pengajuan' => $pengajuan->kode_pengajuan,
                'tipe_pengajuan' => ucfirst($pengajuan->tipe_pengajuan),
                'tahun_anggaran' => $pengajuan->tahun_anggaran,
                'status_pengajuan' => $pengajuan->status_pengajuan ?: 'Draft',
                'akun' => $pengajuan->kode_akun ?: '-',
                'kode_pengenal' => $pengajuan->kode_pengenal ?: '-',
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
                'tor_signed_path' => $pengajuan->tor_signed_path,
                'lampiran_signed_path' => $pengajuan->lampiran_signed_path,
                'tanggal_verifikasi_tor' => $pengajuan->tanggal_verifikasi_tor ? \Carbon\Carbon::parse($pengajuan->tanggal_verifikasi_tor)->format('d-m-Y H:i') : null,
                'tanggal_verifikasi_lampiran' => $pengajuan->tanggal_verifikasi_lampiran ? \Carbon\Carbon::parse($pengajuan->tanggal_verifikasi_lampiran)->format('d-m-Y H:i') : null,
                'dokumen_verified' => $dokumenterVerified,
                'lampiran_verified' => $lampiranVerified,
                'download_url_tor' => $downloadUrlTor,
                'download_url_lampiran' => $downloadUrlLampiran,
                'dokumen_pendukung' => $pengajuan->dokumen_pendukung
            ];

            // Status tanda tangan berita acara
            $response['berita_acara_operator_signed'] = !empty($pengajuan->berita_acara_operator_signed_path);
            $response['berita_acara_pelaksana_signed'] = !empty($pengajuan->berita_acara_pelaksana_signed_path);
            $response['berita_acara_koordinator_signed'] = !empty($pengajuan->berita_acara_koordinator_signed_path);
            $response['berita_acara_operator_signed_date'] = $pengajuan->berita_acara_operator_signed_date ? \Carbon\Carbon::parse($pengajuan->berita_acara_operator_signed_date)->format('d-m-Y H:i') : null;
            $response['berita_acara_pelaksana_signed_date'] = $pengajuan->berita_acara_pelaksana_signed_date ? \Carbon\Carbon::parse($pengajuan->berita_acara_pelaksana_signed_date)->format('d-m-Y H:i') : null;
            $response['berita_acara_koordinator_signed_date'] = $pengajuan->berita_acara_koordinator_signed_date ? \Carbon\Carbon::parse($pengajuan->berita_acara_koordinator_signed_date)->format('d-m-Y H:i') : null;

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada show pengajuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Upload dokumen pendukung untuk pengajuan
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDokumen(Request $request, $id)
    {
        try {
            // Validasi file
            $request->validate([
                'dokumen' => 'required|file|mimes:pdf|max:5120', // max 5MB, only PDF
            ], [
                'dokumen.required' => 'File dokumen harus dipilih.',
                'dokumen.file' => 'File dokumen tidak valid.',
                'dokumen.mimes' => 'Format file harus PDF.',
                'dokumen.max' => 'Ukuran file maksimal 5MB.',
            ]);

            // Cari pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek status
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Diajukan ke Unit Pelaksana'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen hanya dapat diunggah untuk pengajuan dengan status Draft, Ditolak, atau Diajukan'
                ], 403);
            }


            // Hapus file lama jika ada
            if ($pengajuan->dokumen_pendukung) {
                $oldFilePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Simpan file baru
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $fileName = 'nonsbsk_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bmn_dokumenpendukung_nonsbsk', $fileName, 'public');

                // Update pengajuan
                $pengajuan->dokumen_pendukung = $path;
                $pengajuan->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diunggah',
                    'file_path' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error pada uploadDokumen: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => $e->errors()['dokumen'][0] ?? 'Validasi gagal'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error pada uploadDokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Download dokumen pendukung
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDokumen($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            if (!$pengajuan->dokumen_pendukung) {
                return back()->with('error', 'Tidak ada dokumen pendukung');
            }

            $filePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);

            if (!file_exists($filePath)) {
                return back()->with('error', 'File tidak ditemukan');
            }

            return response()->download($filePath, 'dokumen_pendukung_' . $id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Error pada downloadDokumen: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Check if document exists for the pengajuan
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDokumen($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            $hasDocument = !empty($pengajuan->dokumen_pendukung);
            $fileName = null;
            $uploadTime = null;

            if ($hasDocument) {
                // Ekstrak nama file dari path
                $fileName = basename($pengajuan->dokumen_pendukung);

                // Ambil waktu upload dari updated_at
                if ($pengajuan->updated_at) {
                    $uploadTime = $pengajuan->updated_at->format('d-m-Y H:i');
                }
            }

            return response()->json([
                'success' => true,
                'has_document' => $hasDocument,
                'file_name' => $fileName,
                'upload_time' => $uploadTime
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada checkDokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewDokumen($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            if (!$pengajuan->dokumen_pendukung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada dokumen pendukung'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);

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
            \Log::error('Error pada previewDokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete document attached to the pengajuan
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDokumen($id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::findOrFail($id);

            // Check if document exists
            if (!$pengajuan->dokumen_pendukung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada dokumen untuk dihapus'
                ], 400);
            }

            // Cek status
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Diajukan ke Unit Pelaksana'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen hanya dapat dihapus untuk pengajuan dengan status Draft, Ditolak, atau Diajukan'
                ], 403);
            }

            // Delete file from storage
            $filePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update record
            $pengajuan->dokumen_pendukung = null;
            $pengajuan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada deleteDokumen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim pengajuan ke pelaksana
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function kirimPengajuan($id)
    {
        try {
            \Log::info('Method kirimPengajuan dipanggil dengan ID: ' . $id); // Tambahkan log ini

            $pengajuan = Pengajuan::findOrFail($id);

            // Cek status
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan hanya dapat dikirim saat status Draft atau Ditolak'
                ], 403);
            }

            // Update status
            $pengajuan->status_pengajuan = 'Diajukan ke Unit Pelaksana';
            $pengajuan->created_at = now();
            $pengajuan->save();

            \Log::info('Pengajuan berhasil diupdate, status: ' . $pengajuan->status_pengajuan); // Tambahkan log ini

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim ke Unit Pelaksana'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada kirimPengajuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus pengajuan
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Cek status
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan hanya dapat dihapus saat status Draft atau Ditolak'
                ], 403);
            }

            // Hapus detail
            if ($pengajuan->detilPengajuan && count($pengajuan->detilPengajuan) > 0) {
                foreach ($pengajuan->detilPengajuan as $detail) {
                    $detail->delete();
                }
            }

            if ($pengajuan->detilRevisi && count($pengajuan->detilRevisi) > 0) {
                foreach ($pengajuan->detilRevisi as $revisi) {
                    $revisi->delete();
                }
            }

            // Hapus file dokumen jika ada
            if ($pengajuan->dokumen_pendukung) {
                $filePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Hapus pengajuan
            $pengajuan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * Ambil detail pengajuan untuk datatable
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailPengajuan($id)
    {
        try {
            $detilPengajuan = DetilPengajuan::where('pengajuan_id', $id)->get();

            $results = [];
            $totalAnggaran = 0;

            foreach ($detilPengajuan as $index => $item) {
                $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                    ->where('kode_perlengkapan', $item->kode_perlengkapan)
                    ->first();

                $results[] = [
                    'no' => $index + 1,
                    'id' => $item->id,
                    'kode_perlengkapan' => $item->kode_perlengkapan,
                    'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                    'kuantitas' => $item->kuantitas,
                    'harga' => $item->harga,
                    'total' => $item->total
                ];

                $totalAnggaran += $item->total;
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $totalAnggaran
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada getDetailPengajuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail revisi untuk datatable
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailRevisi($id)
    {
        try {
            $detilRevisi = DetilRevisi::where('pengajuan_id', $id)->get();

            $results = [];
            $totalAnggaran = 0;

            foreach ($detilRevisi as $index => $item) {
                $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                    ->where('kode_perlengkapan', $item->kode_perlengkapan)
                    ->first();

                $results[] = [
                    'no' => $index + 1,
                    'id' => $item->id,
                    'kode_perlengkapan' => $item->kode_perlengkapan,
                    'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : '-',
                    'kuantitas' => $item->kuantitas,
                    'harga' => $item->harga,
                    'total' => $item->total
                ];

                $totalAnggaran += $item->total;
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => $totalAnggaran
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada getDetailRevisi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan form untuk mengedit pengajuan
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Ambil data pengajuan yang akan diedit
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

        // Cek apakah status pengajuan memungkinkan untuk diedit
        if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator'])) {
            return redirect()->route('pengajuan.index')
                ->with('error', 'Pengajuan dengan status ' . $pengajuan->status_pengajuan . ' tidak dapat diedit');
        }

        $tahunAnggaran = session('tahunanggaran');
        $bagianPengaju = Auth::user()->idbagian;
        $biroPengaju = Auth::user()->idbiro;

        /* --------------------------------------------------------------------
            URAIAN BAGIAN DAN URAIAN BIRO
        -------------------------------------------------------------------- */
        $bagianPengajuName = DB::table('bagian')
            ->where('id', $bagianPengaju)
            ->value('uraianbagian');
        $biroPengajuName = DB::table('biro')
            ->where('id', $biroPengaju)
            ->value('uraianbiro');

        // Load pelaksana options
        $pelaksanaOptions = BagianModel::whereIn('idbiro',[677,688,728,617,605])->get();

        // --- HANDLE DROPDOWN PENGGUNA ---
        // 1. Ambil data pengguna ruangan dari tabel bmn_ref_bagian_pengguna_nonsbsk
        $bagianPenggunaData = DB::table('bmn_ref_bagian_pengguna_nonsbsk')
            ->where('id_bagian', $bagianPengaju)
            ->get();
        $kodePenggunaList = $bagianPenggunaData->pluck('kode_pengguna')->toArray();

        // 2. Ambil pegawai dari tabel pegawai berdasarkan id_bagian
        $pegawaiData = DB::table('pegawai')
            ->where('id_satker', $bagianPengaju)
            ->pluck('eselon')
            ->toArray();

        // 3. Mapping eselon ke kode pengguna untuk kategori jabatan
        $kodePenggunaJabatan = [];
        foreach ($pegawaiData as $eselon) {
            if ($eselon === 'I') {
                $kodePenggunaJabatan[] = 101;
            } elseif ($eselon === 'II') {
                $kodePenggunaJabatan[] = 102;
            } elseif ($eselon === 'III') {
                $kodePenggunaJabatan[] = 103;
            } elseif ($eselon === 'IV') {
                $kodePenggunaJabatan[] = 104;
            } elseif ($eselon === 'JFU') {
                $kodePenggunaJabatan[] = 105;
            }
        }

        // Gabungkan dan hilangkan duplikat
        $combinedKodePengguna = array_unique(array_merge($kodePenggunaList, $kodePenggunaJabatan));

        // Ambil data pengguna (deskripsi) dari tabel ref pengguna
        $penggunaOptions = DB::table('bmn_ref_pengguna_nonsbsk')
            ->whereIn('kode_pengguna', $combinedKodePengguna)
            ->get();

        // Prepare detail items for edit form
        $detailItems = [];
        if ($pengajuan->tipe_pengajuan == 'usulan') {
            foreach ($pengajuan->detilPengajuan as $index => $item) {
                $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();

                if ($perlengkapan) {
                    // Ambil data pengguna berdasarkan kode_pengguna dari perlengkapan
                    $pengguna = DB::table('bmn_ref_pengguna_nonsbsk')
                        ->where('kode_pengguna', $perlengkapan->kode_pengguna)
                        ->first();

                    $detailItems[] = [
                        'index' => $index,
                        'kode_perlengkapan' => $item->kode_perlengkapan,
                        'kode_pengguna' => $perlengkapan->kode_pengguna,
                        'deskripsi_pengguna' => $pengguna ? $pengguna->deskripsi_pengguna : '-',
                        'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total,
                        'batasan_jumlah' => $perlengkapan->batasan_jumlah
                    ];
                }
            }
        } else { // Revisi
            foreach ($pengajuan->detilRevisi as $index => $item) {
                $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();

                if ($perlengkapan) {
                    // Ambil data pengguna berdasarkan kode_pengguna dari perlengkapan
                    $pengguna = DB::table('bmn_ref_pengguna_nonsbsk')
                        ->where('kode_pengguna', $perlengkapan->kode_pengguna)
                        ->first();

                    $detailItems[] = [
                        'index' => $index,
                        'kode_perlengkapan' => $item->kode_perlengkapan,
                        'kode_pengguna' => $perlengkapan->kode_pengguna,
                        'deskripsi_pengguna' => $pengguna ? $pengguna->deskripsi_pengguna : '-',
                        'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total,
                        'batasan_jumlah' => $perlengkapan->batasan_jumlah
                    ];
                }
            }
        }

        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.edit', compact(
            'pengajuan',
            'detailItems',
            'bagianPengaju',
            'biroPengaju',
            'bagianPengajuName',
            'biroPengajuName',
            'penggunaOptions',
            'pelaksanaOptions',
            'tahunAnggaran'
        ));
    }

    /**
     * Update data pengajuan yang ada
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Cari pengajuan yang akan diupdate
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek apakah status pengajuan memungkinkan untuk diupdate
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator'])) {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Pengajuan dengan status ' . $pengajuan->status_pengajuan . ' tidak dapat diubah'
                ], 403);
            }

            // Validasi field header pengajuan
            $validatedData = $request->validate([
                'tahun_anggaran'            => 'required|integer',
                'tipe_pengajuan'            => 'required|in:revisi,usulan',
                'id_bagian_pengusul'        => 'nullable|integer',
                'id_biro_pengusul'          => 'nullable|integer',
                'id_bagian_pelaksana'       => 'nullable|integer',
                'id_biro_pelaksana'         => 'nullable|integer',
                'status_pengajuan'          => 'nullable|string|max:50',
                'alasan_penolakan_pelaksana'=> 'nullable|string',
                'alasan_penolakan_koordinator' => 'nullable|string',
                'created_by'                => 'nullable|integer',
            ]);

            // Validasi input barang (detail)
            $barangItems = $request->input('barang', []);
            $totalBarang = 0;

            foreach ($barangItems as $index => $barang) {
                $perlengkapan = Perlengkapan::where('kode_perlengkapan', $barang['kode_perlengkapan'])->first();
                if (!$perlengkapan) {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => "Data referensi untuk kode_perlengkapan {$barang['kode_perlengkapan']} tidak ditemukan."
                    ], 422);
                }

                // Hitung total per baris dan akumulasi
                $barangItems[$index]['total'] = $barang['kuantitas'] * $barang['harga'];
                $totalBarang += $barangItems[$index]['total'];
            }

            // Update header pengajuan
            $pengajuan->update($validatedData);

            // Hapus semua detail pengajuan yang ada sebelumnya dan simpan yang baru
            if ($validatedData['tipe_pengajuan'] == 'usulan') {
                DetilPengajuan::where('pengajuan_id', $id)->delete();

                foreach ($barangItems as $barang) {
                    DetilPengajuan::create([
                        'pengajuan_id'     => $pengajuan->id,
                        'kode_perlengkapan'=> $barang['kode_perlengkapan'],
                        'kuantitas'        => $barang['kuantitas'],
                        'harga'            => $barang['harga'],
                        'total'            => $barang['total'],
                    ]);
                }
            } else { // tipe_pengajuan is 'revisi'
                DetilRevisi::where('pengajuan_id', $id)->delete();

                foreach ($barangItems as $barang) {
                    DetilRevisi::create([
                        'pengajuan_id'     => $pengajuan->id,
                        'kode_perlengkapan'=> $barang['kode_perlengkapan'],
                        'kuantitas'        => $barang['kuantitas'],
                        'harga'            => $barang['harga'],
                        'total'            => $barang['total'],
                    ]);
                }
            }

            DB::commit();

            return response()->json(['status' => 'berhasil', 'message' => 'Data pengajuan berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download dokumen TOR (ditandatangani jika tersedia)
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadTor($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek apakah sudah ada TOR yang ditandatangani
            if ($pengajuan->tor_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->tor_signed_path);

                // Cek apakah file ada
                if (file_exists($filePath)) {
                    // Tentukan nama file
                    $fileName = 'TOR_Pengajuan_RKBMN_NonSBSK_' . $id . '_signed.pdf';

                    // Return file untuk didownload
                    return response()->download($filePath, $fileName);
                }
            }

            // Jika tidak ada file yang ditandatangani, buat baru
            \Log::info('TOR yang ditandatangani tidak ditemukan, membuat TOR baru untuk ID: ' . $id);

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('j F Y');

            // Mencari nama penanggung jawab kegiatan
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian';
            }

            // Mencari informasi bagian dan biro
            $uraianBagianPengusul = DB::table('bagian')
                ->where('id', $pengajuan->id_bagian_pengusul)
                ->value('uraianbagian');

            $uraianBiroPengusul = DB::table('biro')
                ->where('id', $pengajuan->id_biro_pengusul)
                ->value('uraianbiro');

            // Mendapatkan deskripsi mata anggaran
            $deskripsiMataAnggaran = '';
            if ($pengajuan->kode_kegiatan && $pengajuan->kode_output) {
                // Menggabungkan kegiatan dan output dalam format kegiatan.output
                $kodeAnggaran = $pengajuan->kode_kegiatan . '.' . $pengajuan->kode_output;

                // Mencari deskripsi dari tabel output berdasarkan kode yang digabung
                $deskripsiMataAnggaran = DB::table('output')
                    ->where('kode', $kodeAnggaran)
                    ->value('deskripsi');

                // Jika tidak ditemukan, berikan nilai default
                if (empty($deskripsiMataAnggaran)) {
                    $deskripsiMataAnggaran = $kodeAnggaran;
                }
            }

            // Persiapkan data untuk view PDF
            $dataArray = [
                'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
                'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'tahunAnggaranPersetujuan' => date('Y'),
                'tanggalPengajuan' => $tanggalPengajuan,
                'program' => $pengajuan->kode_program ?: '-',
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
                'deskripsiMataAnggaran' => $deskripsiMataAnggaran ?: 'Anggaran Barang Non SBSK',
            ];

            // Buat PDF TOR
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.TorUsulan', $dataArray);

            // Tentukan nama file
            $fileName = 'TOR_Pengajuan_RKBMN_NonSBSK_' . $id . '_' . date('Ymd') . '.pdf';

            // Download file
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Error pada downloadTor: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     /**
     * Download lampiran pengajuan dalam format PDF (ditandatangani jika tersedia)
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadLampiran($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek apakah sudah ada Lampiran yang ditandatangani
            if ($pengajuan->lampiran_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->lampiran_signed_path);

                // Cek apakah file ada
                if (file_exists($filePath)) {
                    // Tentukan nama file
                    $fileName = 'Lampiran_Pengajuan_NonSBSK_' . $id . '_signed.pdf';

                    // Return file untuk didownload
                    return response()->download($filePath, $fileName);
                }
            }

            // Jika tidak ada file yang ditandatangani, buat baru
            \Log::info('Lampiran yang ditandatangani tidak ditemukan, membuat Lampiran baru untuk ID: ' . $id);

            // Ambil data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Tentukan tipe pengajuan untuk memuat data yang benar
            $detailItems = [];
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                foreach ($pengajuan->detilPengajuan as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            } else {
                foreach ($pengajuan->detilRevisi as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            }

            // Ambil data bagian dan biro
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('d F Y');

            // Mencari nama penanggung jawab kegiatan (dari eselon III bagian pelaksana)
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian ' . optional($bagianPelaksana)->uraianbagian;
            }

            // Data untuk view PDF
            $data = [
                'pengajuan' => $pengajuan,
                'detailItems' => $detailItems,
                'uraianBagianPengusul' => optional($bagianPengusul)->uraianbagian,
                'uraianBagianPelaksana' => optional($bagianPelaksana)->uraianbagian,
                'tanggalPengajuan' => $tanggalPengajuan,
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'totalAnggaran' => $this->hitungTotalAnggaran($detailItems),
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana
            ];

            // Generate PDF
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.LampiranUsulan_NonSBSK', $data);

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'landscape');

            // Tentukan nama file
            $fileName = 'Lampiran_Pengajuan_NonSBSK_' . $id . '_' . date('Ymd') . '.pdf';

            // Return PDF untuk download
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Error saat download lampiran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat lampiran: ' . $e->getMessage());
        }
    }

     /**
     * Verifikasi dan tanda tangani dokumen TOR dengan e-sign
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifikasiTor(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'passphrase' => 'required|string',
            ]);

            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('j F Y');

            \Log::info('Memulai verifikasi TOR untuk pengajuan ID: ' . $id);

            // Mencari nama penanggung jawab kegiatan
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian';
                \Log::info('Nama penanggung jawab tidak ditemukan, menggunakan default');
            }

            // Gunakan NIK pengguna yang login atau NIK default jika tidak tersedia
//            $pegawai = DB::table('pegawai')->where('email', auth()->user()->username)->first();
//            $nik = $pegawai ? $pegawai->nik : '1234567890123456'; // NIK default jika tidak ditemukan
            // Gunakan NIK statis untuk testing
            $nik = '3201132412920003';
            \Log::info('Menggunakan NIK: ' . $nik . ' untuk e-sign');

            // Mencari informasi bagian dan biro
            $uraianBagianPengusul = DB::table('bagian')
                ->where('id', $pengajuan->id_bagian_pengusul)
                ->value('uraianbagian');

            $uraianBiroPengusul = DB::table('biro')
                ->where('id', $pengajuan->id_biro_pengusul)
                ->value('uraianbiro');

            // Mendapatkan deskripsi mata anggaran
            $deskripsiMataAnggaran = '';
            if ($pengajuan->kode_kegiatan && $pengajuan->kode_output) {
                // Menggabungkan kegiatan dan output dalam format kegiatan.output
                $kodeAnggaran = $pengajuan->kode_kegiatan . '.' . $pengajuan->kode_output;

                // Mencari deskripsi dari tabel output berdasarkan kode yang digabung
                $deskripsiMataAnggaran = DB::table('output')
                    ->where('kode', $kodeAnggaran)
                    ->value('deskripsi');

                // Jika tidak ditemukan, berikan nilai default
                if (empty($deskripsiMataAnggaran)) {
                    $deskripsiMataAnggaran = $kodeAnggaran;
                }
            }

            // Persiapkan data untuk view PDF
            $dataArray = [
                'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
                'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'tahunAnggaranPersetujuan' => date('Y'),
                'tanggalPengajuan' => $tanggalPengajuan,
                'program' => $pengajuan->kode_program ?: '-',
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
                'deskripsiMataAnggaran' => $deskripsiMataAnggaran ?: 'Anggaran Barang Non SBSK',
            ];

            // Buat PDF TOR sebelum ditandatangani
            \Log::info('Membuat PDF TOR');
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.TorUsulan', $dataArray);

            // Simpan PDF sementara
            $tempPath = storage_path('app/temp/tor_nonsbsk_' . $id . '.pdf');
            $pdf->save($tempPath);
            \Log::info('PDF TOR disimpan di: ' . $tempPath);

            // Buat QR Code untuk tanda tangan
            $qrContent = "TOR Pengajuan Non-SBSK ID: " . $id . "\nPenanggung Jawab: " . $namaPenanggungJawabPelaksana . "\nTanggal: " . $tanggalPengajuan;
            $qrBuilder = Builder::create()
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();

            $qrBase64 = base64_encode($qrBuilder->getString());
            \Log::info('QR Code berhasil dibuat');

            // Baca file PDF sebagai base64
            $pdfContent = file_get_contents($tempPath);
            $pdfBase64 = base64_encode($pdfContent);
            \Log::info('PDF berhasil dikonversi ke base64');

            // Siapkan client HTTP dan URL API
            $client = new \GuzzleHttp\Client([
                'timeout' => 120, // 2 menit
                'connect_timeout' => 30, // 30 detik untuk koneksi
                'verify' => false, // Disable SSL verification (ENABLE FOR PRODUCTION!)
            ]);

            // Gunakan URL dan credentials yang sesuai dengan lingkungan aplikasi
            $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
            $username = config('app.esign_username', 'ApaKabahrul');
            $password = config('app.esign_password', 'ApaKabahrul');

            \Log::info('Mengirim request ke API e-sign: ' . $url);

            // Siapkan data untuk API e-sign
            $requestData = [
                'nik' => $nik,
                'passphrase' => $request->passphrase,
                'signatureProperties' => [
                    [
                        'imageBase64' => $qrBase64,
                        'tampilan' => 'VISIBLE',
                        'page' => 3,
                        'originX' => 410.0,
                        'originY' => 400.0,
                        'width' => 75.0,
                        'height' => 75.0,
                        'location' => 'Jakarta',
                        'reason' => 'Dokumen Ini Telah Disetujui dengan Tanda Tangan Elektronik'
                    ]
                ],
                'file' => [
                    $pdfBase64
                ]
            ];

            // Panggil API e-sign
            $response = $client->post($url, [
                'auth' => [$username, $password],
                'json' => $requestData,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            \Log::info('Respon dari API e-sign status code: ' . $statusCode);

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            if (isset($json['file']) && is_array($json['file']) && !empty($json['file'][0])) {
                // Decode file PDF yang sudah ditandatangani
                $signedPdfData = base64_decode($json['file'][0]);
                \Log::info('PDF yang ditandatangani berhasil di-decode');

                // Pastikan direktori ada
                $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_tor_esign');
                if (!file_exists($dirPath)) {
                    if (!mkdir($dirPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori: ' . $dirPath);
                    }
                    \Log::info('Direktori dibuat: ' . $dirPath);
                }

                // Simpan file yang sudah ditandatangani
                $fileName = 'tor_nonsbsk_' . $id . '_signed.pdf';
                $signedPdfPath = $dirPath . '/' . $fileName;

                $bytesWritten = file_put_contents($signedPdfPath, $signedPdfData);
                if ($bytesWritten === false) {
                    throw new \Exception('Gagal menulis file PDF yang ditandatangani');
                }
                \Log::info('PDF yang ditandatangani disimpan di: ' . $signedPdfPath);

                // Update record pengajuan dengan path file yang sudah ditandatangani
                $pengajuan->tor_signed_path = 'bmn_rkbmn_nonsbsk_tor_esign/' . $fileName;
                $pengajuan->tanggal_verifikasi_tor = now();
                $pengajuan->save();
                \Log::info('Database diupdate dengan path file signed: ' . $pengajuan->tor_signed_path);

                // Hapus file sementara
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                    \Log::info('File temporary dihapus');
                }

                DB::commit();
                \Log::info('Transaksi database berhasil di-commit');

                // Buat URL download
                $downloadUrl = url('storage/' . $pengajuan->tor_signed_path);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen TOR berhasil diverifikasi dan ditandatangani',
                    'download_url' => $downloadUrl
                ]);
            } else {
                \Log::error('Response tidak memiliki file yang valid: ' . json_encode($json));
                throw new \Exception('Gagal mendapatkan file PDF yang sudah ditandatangani dari API e-sign');
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            DB::rollBack();

            \Log::error('Request error: ' . $e->getMessage());

            // Log request details
            $request = $e->getRequest();
            \Log::error('Request: ' . $request->getMethod() . ' ' . $request->getUri());

            // Log response if available
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                \Log::error('Response status: ' . $statusCode);
                \Log::error('Response body: ' . $responseBody);

                $errorMessage = 'Terjadi kesalahan pada API e-sign (Status ' . $statusCode . ')';
                try {
                    // Try to parse error response as JSON
                    $errorData = json_decode($responseBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    }
                } catch (\Exception $jsonEx) {
                    // If JSON parsing fails, use the original error message
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error koneksi ke API e-sign: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Verifikasi TOR Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


     /**
     * Verifikasi dan tanda tangani dokumen Lampiran dengan e-sign
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifikasiLampiran(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'passphrase' => 'required|string',
            ]);

            DB::beginTransaction();

            // Ambil data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            \Log::info('Memulai verifikasi Lampiran untuk pengajuan ID: ' . $id);

            // Tentukan tipe pengajuan untuk memuat data yang benar
            $detailItems = [];
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                foreach ($pengajuan->detilPengajuan as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            } else {
                foreach ($pengajuan->detilRevisi as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            }

            // Ambil data bagian dan biro
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('d F Y');

            // Mencari nama penanggung jawab kegiatan
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian ' . optional($bagianPelaksana)->uraianbagian;
            }

            // Gunakan NIK yang sama dengan verifikasi TOR untuk konsistensi
            $nik = '3201132412920003';
            \Log::info('Menggunakan NIK: ' . $nik . ' untuk e-sign');

            // Data untuk view PDF
            $data = [
                'pengajuan' => $pengajuan,
                'detailItems' => $detailItems,
                'uraianBagianPengusul' => optional($bagianPengusul)->uraianbagian,
                'uraianBagianPelaksana' => optional($bagianPelaksana)->uraianbagian,
                'tanggalPengajuan' => $tanggalPengajuan,
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'totalAnggaran' => $this->hitungTotalAnggaran($detailItems),
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana
            ];

            // Generate PDF (pastikan ukuran kertas dan orientasi ditentukan dengan benar)
            \Log::info('Membuat PDF Lampiran');
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.LampiranUsulan_NonSBSK', $data);
            $pdf->setPaper('A4', 'landscape');

            // Simpan PDF sementara
            $tempPath = storage_path('app/temp/lampiran_nonsbsk_' . $id . '.pdf');
            // Pastikan direktori ada
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            $pdf->save($tempPath);
            \Log::info('PDF Lampiran disimpan di: ' . $tempPath);

            // Buat QR Code untuk tanda tangan
            $qrContent = "Lampiran Non-SBSK ID: " . $id . "\nPenanggung Jawab: " . $namaPenanggungJawabPelaksana . "\nTanggal: " . $tanggalPengajuan;
            $qrBuilder = Builder::create()
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();

            $qrBase64 = base64_encode($qrBuilder->getString());
            \Log::info('QR Code berhasil dibuat');

            // Cek ukuran file
            $fileSize = filesize($tempPath);
            \Log::info('Ukuran file PDF: ' . $fileSize . ' bytes');

            // Baca file PDF sebagai base64
            $pdfContent = file_get_contents($tempPath);
            if ($pdfContent === false) {
                throw new \Exception('Gagal membaca file PDF dari path: ' . $tempPath);
            }
            $pdfBase64 = base64_encode($pdfContent);
            \Log::info('PDF berhasil dikonversi ke base64 dengan panjang: ' . strlen($pdfBase64));

            // Siapkan client HTTP dan URL API (gunakan config yang sama dengan verifikasi TOR)
            $client = new \GuzzleHttp\Client([
                'timeout' => 120, // 2 menit
                'connect_timeout' => 30, // 30 detik untuk koneksi
                'verify' => false, // Disable SSL verification untuk development
            ]);

            // Gunakan URL dan credentials yang sama dengan verifikasi TOR
            $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
            $username = config('app.esign_username', 'ApaKabahrul');
            $password = config('app.esign_password', 'ApaKabahrul');

            \Log::info('Mengirim request ke API e-sign: ' . $url);

            // Sesuaikan posisi tanda tangan untuk dokumen landscape
            $requestData = [
                'nik' => $nik,
                'passphrase' => $request->passphrase,
                'signatureProperties' => [
                    [
                        'imageBase64' => $qrBase64,
                        'tampilan' => 'VISIBLE',
                        'page' => 1, // Halaman pertama
                        'originX' => 700.0, // Sesuaikan posisi X untuk landscape
                        'originY' => 100.0, // Sesuaikan posisi Y untuk landscape
                        'width' => 75.0,
                        'height' => 75.0,
                        'location' => 'Jakarta',
                        'reason' => 'Dokumen Lampiran Ini Telah Disetujui dengan Tanda Tangan Elektronik'
                    ]
                ],
                'file' => [
                    $pdfBase64
                ]
            ];

            // Log request body secara terperinci
            \Log::debug('Request body structure:', [
                'nik_length' => strlen($nik),
                'passphrase_length' => strlen($request->passphrase),
                'signatureProperties' => $requestData['signatureProperties'],
                'file_count' => count($requestData['file']),
                'file_first_20_chars' => substr($requestData['file'][0], 0, 20) . '...'
            ]);

            // Panggil API e-sign dengan error handling terperinci
            try {
                $response = $client->post($url, [
                    'auth' => [$username, $password],
                    'json' => $requestData,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                \Log::info('Respon dari API e-sign status code: ' . $statusCode);

                $body = $response->getBody()->getContents();
                $json = json_decode($body, true);

                \Log::debug('API Response:', ['status' => $statusCode, 'body' => $json]);

                if (!isset($json['file']) || !is_array($json['file']) || empty($json['file'][0])) {
                    throw new \Exception('Format respons API tidak valid: ' . json_encode($json));
                }

                // Decode file PDF yang sudah ditandatangani
                $signedPdfData = base64_decode($json['file'][0]);
                if ($signedPdfData === false) {
                    throw new \Exception('Gagal mendecode data PDF dari respons API');
                }

                \Log::info('PDF yang ditandatangani berhasil di-decode, panjang: ' . strlen($signedPdfData));

                // Pastikan direktori ada
                $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_lampiran_esign');
                if (!file_exists($dirPath)) {
                    if (!mkdir($dirPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori: ' . $dirPath);
                    }
                    \Log::info('Direktori dibuat: ' . $dirPath);
                }

                // Simpan file yang sudah ditandatangani
                $fileName = 'lampiran_nonsbsk_' . $id . '_signed.pdf';
                $signedPdfPath = $dirPath . '/' . $fileName;

                $bytesWritten = file_put_contents($signedPdfPath, $signedPdfData);
                if ($bytesWritten === false) {
                    throw new \Exception('Gagal menulis file PDF yang ditandatangani');
                }
                \Log::info('PDF yang ditandatangani disimpan di: ' . $signedPdfPath . ', ukuran: ' . $bytesWritten . ' bytes');

                // Update record pengajuan dengan path file yang sudah ditandatangani
                $pengajuan->lampiran_signed_path = 'bmn_rkbmn_nonsbsk_lampiran_esign/' . $fileName;
                $pengajuan->tanggal_verifikasi_lampiran = now();
                $pengajuan->save();
                \Log::info('Database diupdate dengan path file signed: ' . $pengajuan->lampiran_signed_path);

                // Hapus file sementara
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                    \Log::info('File temporary dihapus');
                }

                DB::commit();
                \Log::info('Transaksi database berhasil di-commit');

                // Buat URL download
                $downloadUrl = url('storage/' . $pengajuan->lampiran_signed_path);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen Lampiran berhasil diverifikasi dan ditandatangani',
                    'download_url' => $downloadUrl
                ]);

            } catch (\GuzzleHttp\Exception\RequestException $guzzleException) {
                \Log::error('Guzzle request error: ' . $guzzleException->getMessage());

                $request = $guzzleException->getRequest();
                $requestBody = $request->getBody()->getContents();
                \Log::error('Request body: ' . substr($requestBody, 0, 500) . '...');

                if ($guzzleException->hasResponse()) {
                    $response = $guzzleException->getResponse();
                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();

                    \Log::error('Response status: ' . $statusCode);
                    \Log::error('Response body: ' . $responseBody);

                    throw new \Exception('API e-sign error (Status ' . $statusCode . '): ' . $responseBody);
                } else {
                    throw new \Exception('Koneksi ke API e-sign gagal: ' . $guzzleException->getMessage());
                }
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            DB::rollBack();

            \Log::error('Request error: ' . $e->getMessage());

            // Log request details
            $request = $e->getRequest();
            \Log::error('Request: ' . $request->getMethod() . ' ' . $request->getUri());

            // Log response if available
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                \Log::error('Response status: ' . $statusCode);
                \Log::error('Response body: ' . $responseBody);

                $errorMessage = 'Terjadi kesalahan pada API e-sign (Status ' . $statusCode . ')';
                try {
                    // Try to parse error response as JSON
                    $errorData = json_decode($responseBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    }
                } catch (\Exception $jsonEx) {
                    // If JSON parsing fails, use the original error message
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error koneksi ke API e-sign: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Verifikasi Lampiran Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fungsi helper untuk menghitung total anggaran dari detail items
     *
     * @param array $detailItems
     * @return float
     */
    private function hitungTotalAnggaran($detailItems)
    {
        $total = 0;
        foreach ($detailItems as $item) {
            $total += $item['total'];
        }
        return $total;
    }



// Implementasi method downloadBeritaAcara di PengajuanController.php

     /**
     * Verifikasi dan tanda tangani Berita Acara dengan e-sign (level Operator)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifikasiBeritaAcara(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'passphrase' => 'required|string',
            ]);

            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            \Log::info('Memulai verifikasi Berita Acara untuk pengajuan ID: ' . $id);

            // Cek apakah pengajuan dalam status yang diizinkan untuk verifikasi
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator'])) {
                throw new \Exception('Pengajuan tidak dalam status yang valid untuk verifikasi berita acara');
            }

            // Jika pengajuan ditolak koordinator, reset path berita acara yang lama
            if ($pengajuan->status_pengajuan === 'Ditolak oleh Koordinator') {
                // Reset semua path berita acara untuk memulai ulang proses tanda tangan
                $pengajuan->berita_acara_operator_signed_path = null;
                $pengajuan->berita_acara_operator_signed_date = null;
                $pengajuan->berita_acara_pelaksana_signed_path = null;
                $pengajuan->berita_acara_pelaksana_signed_date = null;
                $pengajuan->berita_acara_koordinator_signed_path = null;
                $pengajuan->berita_acara_koordinator_signed_date = null;

                // Reset alasan penolakan koordinator
                $pengajuan->alasan_penolakan_koordinator = null;
            }
            // Siapkan data bagian dan biro
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();

            // Tambahkan data bagian dan biro pelaksana
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();

            // Ambil jumlah pengajuan berdasarkan item untuk pengadaan
            $jumlahPengadaan = 0;
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                $jumlahPengadaan = $pengajuan->detilPengajuan()->sum('kuantitas');
            } else {
                $jumlahPengadaan = $pengajuan->detilRevisi()->sum('kuantitas');
            }

            // Format tanggal Indonesia
            $tanggal = date('d');
            $bulan = $this->getNamaBulan(date('m'));
            $tahunKata = $this->angkaKeTerbilang(date('Y'));

            // Cari data pegawai Pengusul (Eselon III dari bagian pengusul)
            $pengusulData = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Cari data pegawai Pelaksana (Eselon III dari bagian pelaksana)
            $pelaksanaData = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pelaksana)
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Cari data Koordinator BMN dari Bagian Administrasi BMN dengan kode bagian 669
            $koordinatorData = DB::table('pegawai')
                ->where('id_satker', 669) // Bagian Administrasi BMN
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Cari data Perencanaan dari Bagian Perencanaan dengan kode bagian 657
            $perencanaanData = DB::table('pegawai')
                ->where('id_satker', 657) // Bagian Perencanaan
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Fallback jika tidak ditemukan koordinator di bagian 669
            if (!$koordinatorData) {
                $koordinatorData = DB::table('pegawai')
                    ->where('nama', 'LIKE', '%Administrasi BMN%')
                    ->where('eselon', 'III')
                    ->select('nama', 'nip')
                    ->first();

                if (!$koordinatorData) {
                    // Fallback kedua jika masih tidak ditemukan
                    $koordinatorData = DB::table('pegawai')
                        ->where('eselon', 'III')
                        ->where(function ($query) {
                            $query->where('nama', 'LIKE', '%BMN%')
                                ->orWhere('nip', 'LIKE', '%BMN%');
                        })
                        ->select('nama', 'nip')
                        ->first();
                }
            }

            // Helper function untuk mengubah format teks menjadi Title Case
            $formatTitleCase = function($text) {
                if (empty($text)) return '';
                // Membagi teks berdasarkan spasi
                $words = explode(' ', strtolower($text));
                // Ubah huruf pertama setiap kata menjadi kapital
                foreach ($words as &$word) {
                    $word = ucfirst($word);
                }
                // Gabungkan kembali
                return implode(' ', $words);
            };

            // Siapkan data untuk template
            $dataArray = [
                'uraianBagianPengusul' => $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
                'uraianBiroPengusul' => $formatTitleCase(optional($biroPengusul)->uraianbiro ?? 'Biro'),
                'uraianBagianPelaksana' => $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'),
                'uraianBiroPelaksana' => $formatTitleCase(optional($biroPelaksana)->uraianbiro ?? 'Biro'),
                'tahunAnggaran' => $pengajuan->tahun_anggaran,
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'tahunKata' => $tahunKata,
                'jumlahPengadaan' => $jumlahPengadaan,
                'jumlahPemeliharaan' => 0, // Asumsi tidak ada data pemeliharaan
                'pengusulNama' => optional($pengusulData)->nama,
                'pengusulNip' => optional($pengusulData)->nip,
                'pengusulJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
                'pelaksanaNama' => optional($pelaksanaData)->nama,
                'pelaksanaNip' => optional($pelaksanaData)->nip,
                'pelaksanaJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'),
                'koordinatorNama' => optional($koordinatorData)->nama,
                'koordinatorNip' => optional($koordinatorData)->nip,
                'koordinatorJabatan' => 'Kepala Bagian Administrasi BMN',
                'perencanaanNama' => optional($perencanaanData)->nama,
                'perencanaanNip' => optional($perencanaanData)->nip,
                'perencanaanJabatan' => 'Kepala Bagian Perencanaan'
            ];

            // Buat PDF Berita Acara
            \Log::info('Membuat PDF Berita Acara');
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcara', $dataArray);

            // Simpan PDF sementara
            $tempPath = storage_path('app/temp/berita_acara_' . $id . '.pdf');
            $pdf->save($tempPath);
            \Log::info('PDF Berita Acara disimpan di: ' . $tempPath);

            // Buat QR Code untuk tanda tangan
            $qrContent = "Berita Acara RKBMN ID: " . $id . "\nPengusul: " . $dataArray['pengusulNama'] . "\nTanggal: " . $tanggal . " " . $bulan . " " . date('Y');
            $qrBuilder = Builder::create()
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();

            $qrBase64 = base64_encode($qrBuilder->getString());
            \Log::info('QR Code berhasil dibuat');

            // Baca file PDF sebagai base64
            $pdfContent = file_get_contents($tempPath);
            $pdfBase64 = base64_encode($pdfContent);
            \Log::info('PDF berhasil dikonversi ke base64');

            // Siapkan client HTTP dan URL API
            $client = new \GuzzleHttp\Client([
                'timeout' => 120, // 2 menit
                'connect_timeout' => 30, // 30 detik untuk koneksi
                'verify' => false, // Disable SSL verification (ENABLE FOR PRODUCTION!)
            ]);

            // Gunakan URL dan credentials yang sesuai dengan lingkungan aplikasi
            $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
            $username = config('app.esign_username', 'ApaKabahrul');
            $password = config('app.esign_password', 'ApaKabahrul');

            \Log::info('Mengirim request ke API e-sign: ' . $url);

            // Gunakan NIK statis untuk testing
            $nik = '3201132412920003';
            \Log::info('Menggunakan NIK: ' . $nik . ' untuk e-sign');

            // Siapkan data untuk API e-sign - posisikan di bagian tanda tangan Operator
            $requestData = [
                'nik' => $nik,
                'passphrase' => $request->passphrase,
                'signatureProperties' => [
                    [
                        'imageBase64' => $qrBase64,
                        'tampilan' => 'VISIBLE',
                        'page' => 2, // Halaman tanda tangan (bagian bawah)
                        'originX' => 145.0, // Posisi X untuk tanda tangan operator (sesuaikan dengan template)
                        'originY' => 255.0, // Posisi Y untuk tanda tangan operator (sesuaikan dengan template)
                        'width' => 75.0,
                        'height' => 75.0,
                        'location' => 'Jakarta',
                        'reason' => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Operator)'
                    ]
                ],
                'file' => [
                    $pdfBase64
                ]
            ];

            // Panggil API e-sign
            $response = $client->post($url, [
                'auth' => [$username, $password],
                'json' => $requestData,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            \Log::info('Respon dari API e-sign status code: ' . $statusCode);

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            if (isset($json['file']) && is_array($json['file']) && !empty($json['file'][0])) {
                // Decode file PDF yang sudah ditandatangani
                $signedPdfData = base64_decode($json['file'][0]);
                \Log::info('PDF yang ditandatangani berhasil di-decode');

                // Pastikan direktori ada
                $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_berita_acara_esign');
                if (!file_exists($dirPath)) {
                    if (!mkdir($dirPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori: ' . $dirPath);
                    }
                    \Log::info('Direktori dibuat: ' . $dirPath);
                }

                // Simpan file yang sudah ditandatangani
                $fileName = 'berita_acara_' . $id . '_operator_signed.pdf';
                $signedPdfPath = $dirPath . '/' . $fileName;

                $bytesWritten = file_put_contents($signedPdfPath, $signedPdfData);
                if ($bytesWritten === false) {
                    throw new \Exception('Gagal menulis file PDF yang ditandatangani');
                }
                \Log::info('PDF yang ditandatangani disimpan di: ' . $signedPdfPath);

                // Update record pengajuan dengan path file yang sudah ditandatangani
                $pengajuan->berita_acara_operator_signed_path = 'bmn_rkbmn_nonsbsk_berita_acara_esign/' . $fileName;
                $pengajuan->berita_acara_operator_signed_date = now();
                $pengajuan->save();
                \Log::info('Database diupdate dengan path file signed: ' . $pengajuan->berita_acara_operator_signed_path);

                // Hapus file sementara
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                    \Log::info('File temporary dihapus');
                }

                DB::commit();
                \Log::info('Transaksi database berhasil di-commit');

                // Buat URL download
                $downloadUrl = url('storage/' . $pengajuan->berita_acara_operator_signed_path);

                return response()->json([
                    'success' => true,
                    'message' => 'Berita Acara berhasil diverifikasi dan ditandatangani oleh Operator',
                    'download_url' => $downloadUrl,
                    'status' => [
                        'operator_signed' => true,
                        'pelaksana_signed' => false,
                        'koordinator_signed' => false
                    ]
                ]);
            } else {
                \Log::error('Response tidak memiliki file yang valid: ' . json_encode($json));
                throw new \Exception('Gagal mendapatkan file PDF yang sudah ditandatangani dari API e-sign');
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            DB::rollBack();

            \Log::error('Request error: ' . $e->getMessage());

            // Log request details
            $request = $e->getRequest();
            \Log::error('Request: ' . $request->getMethod() . ' ' . $request->getUri());

            // Log response if available
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                \Log::error('Response status: ' . $statusCode);
                \Log::error('Response body: ' . $responseBody);

                $errorMessage = 'Terjadi kesalahan pada API e-sign (Status ' . $statusCode . ')';
                try {
                    // Try to parse error response as JSON
                    $errorData = json_decode($responseBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    }
                } catch (\Exception $jsonEx) {
                    // If JSON parsing fails, use the original error message
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error koneksi ke API e-sign: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Verifikasi Berita Acara Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Berita Acara yang sudah ditandatangani
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadBeritaAcaraSigned($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek status tanda tangan
            if (!$pengajuan->berita_acara_operator_signed_path) {
                return back()->with('error', 'Berita Acara belum ditandatangani oleh Operator');
            }

            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_operator_signed_path);

            // Cek apakah file ada
            if (!file_exists($filePath)) {
                return back()->with('error', 'File Berita Acara tertandatangani tidak ditemukan');
            }

            // Download file
            $fileName = 'Berita_Acara_Signed_' . $id . '.pdf';
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            \Log::error('Error pada downloadBeritaAcaraSigned: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     /**
     * Generate and preview Berita Acara before signing
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function previewBeritaAcara($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Siapkan data bagian dan biro
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();

            // Tambahkan data bagian dan biro pelaksana
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();

            // Ambil jumlah pengajuan berdasarkan item untuk pengadaan
            $jumlahPengadaan = 0;
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                $jumlahPengadaan = $pengajuan->detilPengajuan()->sum('kuantitas');
            } else {
                $jumlahPengadaan = $pengajuan->detilRevisi()->sum('kuantitas');
            }

            // Format tanggal Indonesia
            $tanggal = date('d');
            $bulan = $this->getNamaBulan(date('m'));
            $tahunKata = $this->angkaKeTerbilang(date('Y'));

            // Cari data pegawai Pengusul (Eselon III dari bagian pengusul)
            $pengusulData = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Cari data pegawai Pelaksana (Eselon III dari bagian pelaksana)
            $pelaksanaData = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pelaksana)
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Cari data Koordinator BMN dari Bagian Administrasi BMN dengan kode bagian 669
            $koordinatorData = DB::table('pegawai')
                ->where('id_satker', 669) // Bagian Administrasi BMN
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            $perencanaanData = DB::table('pegawai')
                ->where('id_satker', 657) // Bagian Perencanaan
                ->where('eselon', 'III')
                ->select('nama', 'nip')
                ->first();

            // Fallback jika tidak ditemukan koordinator di bagian 669
            if (!$koordinatorData) {
                $koordinatorData = DB::table('pegawai')
                    ->where('nama', 'LIKE', '%Administrasi BMN%')
                    ->where('eselon', 'III')
                    ->select('nama', 'nip')
                    ->first();

                if (!$koordinatorData) {
                    // Fallback kedua jika masih tidak ditemukan
                    $koordinatorData = DB::table('pegawai')
                        ->where('eselon', 'III')
                        ->where(function ($query) {
                            $query->where('nama', 'LIKE', '%BMN%')
                                ->orWhere('nip', 'LIKE', '%BMN%');
                        })
                        ->select('nama', 'nip')
                        ->first();
                }
            }

            // Helper function untuk mengubah format teks menjadi Title Case
            $formatTitleCase = function($text) {
                if (empty($text)) return '';
                // Membagi teks berdasarkan spasi
                $words = explode(' ', strtolower($text));
                // Ubah huruf pertama setiap kata menjadi kapital
                foreach ($words as &$word) {
                    $word = ucfirst($word);
                }
                // Gabungkan kembali
                return implode(' ', $words);
            };

            // Siapkan data untuk template
            $dataArray = [
                'uraianBagianPengusul' => $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
                'uraianBiroPengusul' => $formatTitleCase(optional($biroPengusul)->uraianbiro ?? 'Biro'),
                'uraianBagianPelaksana' => $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'),
                'uraianBiroPelaksana' => $formatTitleCase(optional($biroPelaksana)->uraianbiro ?? 'Biro'),
                'tahunAnggaran' => $pengajuan->tahun_anggaran,
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'tahunKata' => $tahunKata,
                'jumlahPengadaan' => $jumlahPengadaan,
                'jumlahPemeliharaan' => 0, // Asumsi tidak ada data pemeliharaan
                'pengusulNama' => optional($pengusulData)->nama,
                'pengusulNip' => optional($pengusulData)->nip,
                'pengusulJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
                'pelaksanaNama' => optional($pelaksanaData)->nama,
                'pelaksanaNip' => optional($pelaksanaData)->nip,
                'pelaksanaJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'),
                'koordinatorNama' => optional($koordinatorData)->nama,
                'koordinatorNip' => optional($koordinatorData)->nip,
                'koordinatorJabatan' => 'Kepala Bagian Administrasi BMN',
                'perencanaanNama' => optional($perencanaanData)->nama,
                'perencanaanNip' => optional($perencanaanData)->nip,
                'perencanaanJabatan' => 'Kepala Bagian Perencanaan',
            ];

            // Buat PDF Berita Acara
            \Log::info('Membuat preview PDF Berita Acara');
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcara', $dataArray);

            // Kembalikan PDF sebagai response
            return $pdf->stream('preview_berita_acara.pdf');

        } catch (\Exception $e) {
            \Log::error('Error pada previewBeritaAcara: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     /**
     * Generate and preview TOR before signing
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function previewTor($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('j F Y');

            // Mencari nama penanggung jawab kegiatan
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian';
            }

            // Mencari informasi bagian dan biro
            $uraianBagianPengusul = DB::table('bagian')
                ->where('id', $pengajuan->id_bagian_pengusul)
                ->value('uraianbagian');

            $uraianBiroPengusul = DB::table('biro')
                ->where('id', $pengajuan->id_biro_pengusul)
                ->value('uraianbiro');

            // Mendapatkan deskripsi mata anggaran
            $deskripsiMataAnggaran = '';
            if ($pengajuan->kode_kegiatan && $pengajuan->kode_output) {
                // Menggabungkan kegiatan dan output dalam format kegiatan.output
                $kodeAnggaran = $pengajuan->kode_kegiatan . '.' . $pengajuan->kode_output;

                // Mencari deskripsi dari tabel output berdasarkan kode yang digabung
                $deskripsiMataAnggaran = DB::table('output')
                    ->where('kode', $kodeAnggaran)
                    ->value('deskripsi');

                // Jika tidak ditemukan, berikan nilai default
                if (empty($deskripsiMataAnggaran)) {
                    $deskripsiMataAnggaran = $kodeAnggaran;
                }
            }

            // Persiapkan data untuk view PDF
            $dataArray = [
                'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
                'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'tahunAnggaranPersetujuan' => date('Y'),
                'tanggalPengajuan' => $tanggalPengajuan,
                'program' => $pengajuan->kode_program ?: '-',
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
                'deskripsiMataAnggaran' => $deskripsiMataAnggaran ?: 'Anggaran Barang Non SBSK',
            ];

            // Buat PDF TOR
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.TorUsulan', $dataArray);

            // Stream PDF untuk preview
            return $pdf->stream('preview_tor.pdf');

        } catch (\Exception $e) {
            \Log::error('Error pada previewTor: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     /**
     * Generate and preview Lampiran before signing
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function previewLampiran($id)
    {
        try {
            // Ambil data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Tentukan tipe pengajuan untuk memuat data yang benar
            $detailItems = [];
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                foreach ($pengajuan->detilPengajuan as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            } else {
                foreach ($pengajuan->detilRevisi as $item) {
                    $perlengkapan = Perlengkapan::where('kode_perlengkapan', $item->kode_perlengkapan)->first();
                    if ($perlengkapan) {
                        $detailItems[] = [
                            'kode_perlengkapan' => $item->kode_perlengkapan,
                            'deskripsi_perlengkapan' => $perlengkapan->deskripsi_perlengkapan,
                            'kuantitas' => $item->kuantitas,
                            'harga' => $item->harga,
                            'total' => $item->total,
                            'kode_barang' => $perlengkapan->kode_barang
                        ];
                    }
                }
            }

            // Ambil data bagian dan biro
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();

            // Format tanggal dengan Carbon
            $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('d F Y');

            // Mencari nama penanggung jawab kegiatan (dari eselon III bagian pelaksana)
            $namaPenanggungJawabPelaksana = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->value('nama');

            // Jika tidak ditemukan, berikan nilai default
            if (empty($namaPenanggungJawabPelaksana)) {
                $namaPenanggungJawabPelaksana = 'Kepala Bagian ' . optional($bagianPelaksana)->uraianbagian;
            }

            // Data untuk view PDF
            $data = [
                'pengajuan' => $pengajuan,
                'detailItems' => $detailItems,
                'uraianBagianPengusul' => optional($bagianPengusul)->uraianbagian,
                'uraianBagianPelaksana' => optional($bagianPelaksana)->uraianbagian,
                'tanggalPengajuan' => $tanggalPengajuan,
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'totalAnggaran' => $this->hitungTotalAnggaran($detailItems),
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana
            ];

            // Generate PDF
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.LampiranUsulan_NonSBSK', $data);

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'landscape');

            // Stream PDF untuk preview
            return $pdf->stream('preview_lampiran.pdf');

        } catch (\Exception $e) {
            \Log::error('Error pada previewLampiran: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk mendapatkan nama bulan dalam bahasa Indonesia
     *
     * @param string $bulanAngka Bulan dalam format angka (01-12)
     * @return string Nama bulan dalam bahasa Indonesia
     */
    private function getNamaBulan($bulanAngka)
    {
        $namaBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        return $namaBulan[$bulanAngka] ?? '';
    }

    /**
     * Helper method untuk mengubah angka menjadi kata terbilang
     *
     * @param int $angka Angka yang akan diubah
     * @return string Angka dalam bentuk terbilang
     */
    private function angkaKeTerbilang($angka)
    {
        $nilai = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($angka < 12) {
            return $nilai[$angka];
        } elseif ($angka < 20) {
            return $nilai[$angka - 10] . " belas";
        } elseif ($angka < 100) {
            return $nilai[floor($angka / 10)] . " puluh " . $nilai[$angka % 10];
        } elseif ($angka < 200) {
            return "seratus " . $this->angkaKeTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $nilai[floor($angka / 100)] . " ratus " . $this->angkaKeTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "seribu " . $this->angkaKeTerbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->angkaKeTerbilang(floor($angka / 1000)) . " ribu " . $this->angkaKeTerbilang($angka % 1000);
        }

        return $angka;
    }
    // Add to PengajuanController.php
    public function review($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        return view('PerencanaanBMN.Bagian.pengajuanrkbmnbagiannonsbsk.ReviewPageOperator', compact('pengajuan'));
    }
}

