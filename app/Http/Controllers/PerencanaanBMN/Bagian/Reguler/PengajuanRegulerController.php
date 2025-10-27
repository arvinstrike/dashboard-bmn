<?php
//app/Http/Controllers/PerencanaanBMN/Bagian/Reguler.php
namespace App\Http\Controllers\PerencanaanBMN\Bagian\Reguler;

use App\Http\Controllers\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Http\Request;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilPengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilRevisi;

use App\Models\PerencanaanBMN\Bagian\NonSBSK\Perlengkapan;
use App\Models\Realisasi\Admin\LaporanRealisasiAnggaranModel;
use App\Models\ReferensiUnit\BagianModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanRegulerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except(['magicLinkValidation', 'processMagicLinkEsign', 'previewDocumentMagicLink']);
    }

    public function indexOperator()
    {
        $idBagian = Auth::user()->idbagian;
        $tahunanggaran = session('tahunanggaran');

        // Operator bisa lihat semua pengajuan (semua status)
        $pengajuan = Pengajuan::where('jenis_formulir', 'Pengajuan Reguler')
            ->where('id_bagian_pengusul', $idBagian)
            ->orderBy('created_at', 'desc')
            ->get();

        $uniqueStatuses = $pengajuan->pluck('status_pengajuan')->unique()->sort();

        return view('PerencanaanBMN.Bagian.pengajuanrkbmnreguler.DashboardPageReguler', compact('pengajuan', 'tahunanggaran', 'uniqueStatuses'));
    }

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

        $pelaksanaOptions = BagianModel::whereIn('idbiro', [677, 688, 728, 617, 605])->get();

        $barangOptions = DB::table('t_brg')->select('ur_sskel', 'kd_brg')->get();


        return view('PerencanaanBMN.Bagian.pengajuanrkbmnreguler.CreateFormReguler', compact(
            'bagianPengaju',
            'biroPengaju',
            'uraianBagian',
            'uraianBiro',
            'tahunAnggaran',
            'pelaksanaOptions',
            'barangOptions'
        ));
    }

    public function getBarangOptions(Request $request)
    {
        // Ambil data barang dari tabel t_brg
        $barangOptions = DB::table('t_brg')->select('ur_sskel', 'kd_brg')->get();

        // Mengembalikan data barang dalam bentuk JSON
        return response()->json($barangOptions);
    }

    public function store(Request $request)
    {
        try {
            $isUsulan = $request->tahun_anggaran == session('tahunanggaran') + 1;

            // Validasi field header pengajuan
            $validatedData = $request->validate([
                'tahun_anggaran'         => 'required|integer',
                'id_bagian_pelaksana'    => 'required|integer',
                'id_biro_pelaksana'      => 'nullable|integer',
                'status_pengajuan'       => 'nullable|string|max:50',
                'alasan_penolakan_pelaksana'  => 'nullable|string',
                'alasan_penolakan_koordinator' => 'nullable|string',
                'keterangan'       => 'nullable|string',
            ]);

            $validatedData['id_bagian_pengusul'] = Auth::user()->idbagian;
            $validatedData['id_biro_pengusul'] = Auth::user()->idbiro;

            // Use Auth::id() for consistency instead of username
            $validatedData['created_by'] = Auth::user()->username ?? Auth::user()->name ?? Auth::id();

            $validatedData['jenis_formulir'] = 'Pengajuan Reguler';
            $validatedData['status_pengajuan'] = $validatedData['status_pengajuan'] ?? 'Draft';

            // Tentukan tipe pengajuan berdasarkan tahun_anggaran dibandingkan session('tahunanggaran')
            if ($validatedData['tahun_anggaran'] == session('tahunanggaran')) {
                $validatedData['tipe_pengajuan'] = 'revisi';
            } else {
                $validatedData['tipe_pengajuan'] = 'usulan';
            }

            // Validasi input barang (detail) - UPDATED untuk include keterangan_barang
            $barangItems = $request->input('barang', []);
            if (empty($barangItems)) {
                throw ValidationException::withMessages(['barang' => 'Minimal harus ada satu barang yang diajukan.']);
            }

            $validBarangItems = []; // Array untuk menyimpan barang yang valid
            $totalPengajuan = 0; // Mengganti nama variabel agar lebih jelas

            foreach ($barangItems as $index => $barang) {
                // Skip baris kosong atau tidak lengkap
                $kodeBarang = trim($barang['kode_barang'] ?? '');
                $kuantitas = filter_var($barang['kuantitas'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
                $hargaRaw = str_replace('.', '', trim($barang['harga'] ?? '0'));
                $harga = filter_var($hargaRaw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                // Jika baris kosong (tidak ada kode barang), skip
                if (empty($kodeBarang)) {
                    continue;
                }

                // Validasi untuk baris yang ada kode barang
                $errors = [];

                if (!is_numeric($kuantitas) || $kuantitas <= 0) {
                    $errors["barang.{$index}.kuantitas"] = 'Kuantitas harus berupa angka dan lebih dari 0.';
                }

                if (!is_numeric($harga) || $harga <= 0) {
                    $errors["barang.{$index}.harga"] = 'Harga harus berupa angka dan lebih dari 0.';
                }

                if (!empty($errors)) {
                    throw ValidationException::withMessages($errors);
                }

                // Simpan barang yang valid
                $validBarangItem = [
                    'kode_barang' => $kodeBarang,
                    'kuantitas' => (int) $kuantitas,
                    'harga' => (float) $harga,
                    'total' => (int) $kuantitas * (float) $harga,
                    'keterangan_barang' => trim($barang['keterangan_barang'] ?? ''),
                    'kode_perlengkapan' => $barang['kode_perlengkapan'] ?? '-'
                ];

                $validBarangItems[] = $validBarangItem;
                $totalPengajuan += $validBarangItem['total'];
            }

            // Validasi minimal harus ada satu barang yang valid
            if (empty($validBarangItems)) {
                throw ValidationException::withMessages(['barang' => 'Minimal harus ada satu barang yang lengkap (kode barang, kuantitas > 0, dan harga > 0).']);
            }

            // Gunakan validBarangItems untuk proses selanjutnya
            $barangItems = $validBarangItems;

            // Buat header pengajuan
            $pengajuan = Pengajuan::create($validatedData);

            // Generate kode_pengajuan based on the pengajuan ID and type
            if ($pengajuan) {
                $tahunAnggaran = session('tahunanggaran');
                $idBagian = Auth::user()->idbagian;

                if ($validatedData['tipe_pengajuan'] == 'revisi') {
                    $kode = "REG-PMB-{$idBagian}-{$tahunAnggaran}-{$pengajuan->id}";
                } else { // usulan
                    $tahunPlusOne = $tahunAnggaran + 1;
                    $kode = "REG-PRC-{$idBagian}-{$tahunPlusOne}-{$pengajuan->id}";
                }

                // Update the pengajuan with the generated code
                $pengajuan->kode_pengajuan = $kode;
                $pengajuan->save();

                // Simpan detail barang sesuai tipe pengajuan - UPDATED dengan keterangan_barang
                if ($validatedData['tipe_pengajuan'] == 'usulan') {
                    foreach ($barangItems as $barang) {
                        DetilPengajuan::create([
                            'pengajuan_id'       => $pengajuan->id,
                            'kode_perlengkapan'  => $barang['kode_perlengkapan'],
                            'kode_barang'        => $barang['kode_barang'],
                            'keterangan_barang'  => $barang['keterangan_barang'], // KOLOM BARU
                            'kuantitas'          => $barang['kuantitas'],
                            'harga'              => $barang['harga'],
                            'total'              => $barang['total'],
                        ]);
                    }
                } else { // tipe_pengajuan == 'revisi'
                    foreach ($barangItems as $barang) {
                        DetilRevisi::create([
                            'pengajuan_id'       => $pengajuan->id,
                            'kode_perlengkapan'  => $barang['kode_perlengkapan'],
                            'kode_barang'        => $barang['kode_barang'],
                            'keterangan_barang'  => $barang['keterangan_barang'], // KOLOM BARU
                            'kuantitas'          => $barang['kuantitas'],
                            'harga'              => $barang['harga'],
                            'total'              => $barang['total'],
                        ]);
                    }
                }
            } else {
                // Jika $pengajuan gagal dibuat (meskipun create biasanya throw exception jika gagal)
                return response()->json([
                    'status'  => 'gagal',
                    'message' => 'Gagal membuat header pengajuan.'
                ], 500);
            }

            // Respon sukses dengan redirect URL
            return response()->json([
                'status'   => 'berhasil',
                'message'  => 'Data pengajuan berhasil disimpan.',
                'redirect' => route('pengajuan.reguler.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Terjadi kesalahan pada database: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'gagal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Cari data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])
                ->where('jenis_formulir', 'Pengajuan Reguler')
                ->findOrFail($id);

            // Siapkan data terkait
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();

            // Format tanggal pengajuan
            $tanggalPengajuan = $pengajuan->created_at ? $pengajuan->created_at->format('d-m-Y H:i') : '-';

            // Ambil data detil pengajuan jika ada
            $detilPengajuan = [];
            if ($pengajuan->tipe_pengajuan == 'usulan' && count($pengajuan->detilPengajuan) > 0) {
                $totalAnggaranPengajuan = 0;
                foreach ($pengajuan->detilPengajuan as $index => $item) {
                    $barang = DB::table('t_brg')
                        ->where('kd_brg', $item->kode_barang)
                        ->first();

                    $itemTotal = $item->kuantitas * $item->harga;
                    $totalAnggaranPengajuan += $itemTotal;

                    $detilPengajuan[] = [
                        'no' => $index + 1,
                        'id' => $item->id,
                        'kode_barang' => $item->kode_barang,
                        'keterangan_barang' => $item->keterangan_barang ?: '-',
                        'deskripsi' => $barang ? $barang->ur_sskel : 'Pengajuan Pemeliharaan',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $itemTotal
                    ];
                }
            }

            // Ambil data detil revisi jika ada
            $detilRevisi = [];
            if ($pengajuan->tipe_pengajuan == 'revisi' && count($pengajuan->detilRevisi) > 0) {
                foreach ($pengajuan->detilRevisi as $index => $item) {
                    $barang = DB::table('t_brg')
                        ->where('kd_brg', $item->kode_barang)
                        ->first();

                    $detilRevisi[] = [
                        'no' => $index + 1,
                        'id' => $item->id,
                        'kode_barang' => $item->kode_barang,
                        'deskripsi' => $barang ? $barang->ur_sskel : 'Pengajuan Pemeliharaan',
                        'keterangan_barang' => $item->keterangan_barang ?: '-',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
                    ];
                }
            }

            // Hitung total anggaran
            $totalAnggaranPengajuan = array_sum(array_column($detilPengajuan, 'total'));
            $totalAnggaranRevisi = array_sum(array_column($detilRevisi, 'total'));

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
                'alasan_penolakan_perencanaan' => $pengajuan->alasan_penolakan_perencanaan,
                'jenis_formulir' => $pengajuan->jenis_formulir,
                'berita_acara_signed_path' => $pengajuan->berita_acara_signed_path,
                'berita_acara_operator_signed' => !is_null($pengajuan->berita_acara_signed_path) && !is_null($pengajuan->berita_acara_operator_signed_date),
                'berita_acara_pelaksana_signed' => !is_null($pengajuan->berita_acara_signed_path) && !is_null($pengajuan->berita_acara_pelaksana_signed_date),
                'berita_acara_koordinator_signed' => !is_null($pengajuan->berita_acara_signed_path) && !is_null($pengajuan->berita_acara_koordinator_signed_date),
                // Add timestamp fields for debugging/checking
                'berita_acara_operator_signed_date' => $pengajuan->berita_acara_operator_signed_date,
                'berita_acara_pelaksana_signed_date' => $pengajuan->berita_acara_pelaksana_signed_date,
                'berita_acara_koordinator_signed_date' => $pengajuan->berita_acara_koordinator_signed_date,
                'tor_signed_date' => $pengajuan->tor_signed_date,
                'lampiran_signed_date' => $pengajuan->lampiran_signed_date,
                'dokumen_rekomendasi_bmn' => $pengajuan->dokumen_rekomendasi_bmn,
                'tor_signed_path' => $pengajuan->tor_signed_path,
                'lampiran_signed_path' => $pengajuan->lampiran_signed_path,
                'dokumen_pendukung' => $pengajuan->dokumen_pendukung
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada show pengajuan reguler: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function review($id)
    {
        $pengajuan = Pengajuan::where('jenis_formulir', 'Pengajuan Reguler')->findOrFail($id);
        return view('PerencanaanBMN.Bagian.pengajuanrkbmnreguler.ReviewPageReguler', compact('pengajuan'));
    }

    public function previewBeritaAcara($id, $returnData = false)
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
            $formatTitleCase = function ($text) {
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

            // ---> TAMBAHKAN BLOK IF INI <---
            if ($returnData) {
                return response()->json(['dataArray' => $dataArray]);
            }

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
    public function previewTor($id, $returnData = false)
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

            $kodePengajuan = $pengajuan->kode_pengajuan;

            $keterangan = $pengajuan->keterangan ?: '-';


            // Persiapkan data untuk view PDF
            $dataArray = [
                'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
                'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'tahunAnggaranPersetujuan' => date('Y'),
                'tanggalPengajuan' => $tanggalPengajuan,
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
                'kodePengajuan' => $kodePengajuan,
                'keterangan' => $keterangan

            ];

            if ($returnData) {
                return response()->json(['dataArray' => $dataArray]);
            }

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
    public function previewLampiran($id, $returnData = false)
    {
        try {
            // Ambil data pengajuan dengan eager loading
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Tentukan tipe pengajuan untuk memuat data yang benar
            $detailItems = [];
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                foreach ($pengajuan->detilPengajuan as $item) {
                    $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                    $detailItems[] = [
                        'kode_barang' => $item->kode_barang,
                        'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                        'keterangan_barang' => $item->keterangan_barang,
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
                    ];
                }
            } else {
                foreach ($pengajuan->detilRevisi as $item) {
                    $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                    $detailItems[] = [
                        'kode_barang' => $item->kode_barang,
                        'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                        'keterangan_barang' => $item->keterangan_barang,
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
                    ];
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
            if ($returnData) {
                return response()->json(['data' => $data]);
            }

            // Jika request datang dari browser untuk preview, stream PDF
            if (request()->isMethod('get')) {
                $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.LampiranUsulan_NonSBSK', $data);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->stream('preview_lampiran.pdf');
            }

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error('Error pada previewLampiran: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
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

            // Cek status tanda tangan - Changed: check single path
            if (!$pengajuan->berita_acara_signed_path) {
                return back()->with('error', 'Berita Acara belum ditandatangani');
            }

            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path); // Changed: single path

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

            $kodePengajuan = $pengajuan->kode_pengajuan;

            $keterangan = $pengajuan->keterangan ?: '-';


            // Persiapkan data untuk view PDF
            $dataArray = [
                'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
                'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
                'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
                'tahunAnggaranPersetujuan' => date('Y'),
                'tanggalPengajuan' => $tanggalPengajuan,
                'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
                'kodePengajuan' => $kodePengajuan,
                'keterangan' => $keterangan,
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
                    $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                    $detailItems[] = [
                        'kode_barang' => $item->kode_barang,
                        'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
                    ];
                }
            } else {
                foreach ($pengajuan->detilRevisi as $item) {
                    $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                    $detailItems[] = [
                        'kode_barang' => $item->kode_barang,
                        'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total
                    ];
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

    /**
     * Send the application to the executor unit
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function kirimPengajuan($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            // Update status
            $pengajuan->status_pengajuan = 'Diajukan ke Unit Pelaksana';
            $pengajuan->save();

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

            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen hanya dapat diunggah untuk pengajuan dengan status Draft atau Ditolak.'
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
                $fileName = 'reguler_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bmn_dokumenpendukung_reguler', $fileName, 'public');

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

            return response()->download($filePath, 'dokumen_pendukung_reguler_' . $id . '.pdf');
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
                'Content-Disposition' => 'inline; filename="dokumen_pendukung_reguler_' . $id . '.pdf"'
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

            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen hanya dapat dihapus untuk pengajuan dengan status Draft atau Ditolak.'
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

    public function edit($id)
    {
        try {
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])
                ->where('jenis_formulir', 'Pengajuan Reguler')
                ->findOrFail($id);

            // Security check: Allow edit only for certain statuses
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return redirect()->route('pengajuan.reguler.index')->with('error', 'Pengajuan ini tidak dapat diedit karena statusnya sudah ' . $pengajuan->status_pengajuan);
            }

            // Security check: Allow edit only by the creator or specific roles (if applicable)
            if ($pengajuan->id_bagian_pengusul != Auth::user()->idbagian) {
                return redirect()->route('pengajuan.reguler.index')->with('error', 'Anda tidak memiliki hak untuk mengedit pengajuan ini.');
            }

            $tahunAnggaran = session('tahunanggaran'); // Current session TA for comparison
            $bagianPengaju = Auth::user()->idbagian;
            $biroPengaju = Auth::user()->idbiro;

            $uraianBagian = DB::table('bagian')->where('id', $bagianPengaju)->value('uraianbagian');
            $uraianBiro = DB::table('biro')->where('id', $biroPengaju)->value('uraianbiro');
            $pelaksanaOptions = BagianModel::whereIn('idbiro', [677, 688, 728, 617, 605])->get();

            // Prepare details for the view
            $detilItems = [];
            $itemsToProcess = $pengajuan->tipe_pengajuan == 'usulan' ? $pengajuan->detilPengajuan : $pengajuan->detilRevisi;

            foreach ($itemsToProcess as $item) {
                // We need ur_sskel for the pre-selected barang in Select2
                $barangInfo = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                $detilItems[] = [
                    'kode_barang' => $item->kode_barang,
                    'ur_sskel'    => $barangInfo ? $barangInfo->ur_sskel : $item->kode_barang, // Fallback to kode_barang if not found
                    'keterangan_barang' => $item->keterangan_barang ?? '',
                    'kuantitas'   => $item->kuantitas,
                    'harga'       => $item->harga,
                    'total'       => $item->total,
                ];
            }

            return view('PerencanaanBMN.Bagian.pengajuanrkbmnreguler.EditFormReguler', compact(
                'pengajuan',
                'pelaksanaOptions',
                'uraianBagian',
                'uraianBiro',
                'tahunAnggaran', // Session TA
                'detilItems'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pengajuan.reguler.index')->with('error', 'Data pengajuan tidak ditemukan.');
        } catch (\Exception $e) {
            \Log::error('Error in PengajuanRegulerController@edit: ' . $e->getMessage());
            return redirect()->route('pengajuan.reguler.index')->with('error', 'Terjadi kesalahan saat memuat data edit.');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pengajuan = Pengajuan::where('jenis_formulir', 'Pengajuan Reguler')->findOrFail($id);

            // Security check: Allow update only for certain statuses
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return response()->json([
                    'status'  => 'gagal',
                    'message' => 'Pengajuan ini tidak dapat diupdate karena statusnya sudah ' . $pengajuan->status_pengajuan
                ], 403); // Forbidden
            }

            // Security check: Allow update only by the creator
            if ($pengajuan->id_bagian_pengusul != Auth::user()->idbagian) {
                return response()->json([
                    'status'  => 'gagal',
                    'message' => 'Anda tidak memiliki hak untuk mengupdate pengajuan ini.'
                ], 403);
            }

            $validatedData = $request->validate([
                'tahun_anggaran'        => 'required|integer',
                'id_bagian_pelaksana'   => 'required|integer',
                'keterangan'            => 'nullable|string',
            ]);

            $bagianPelaksana = BagianModel::find($validatedData['id_bagian_pelaksana']);
            if (!$bagianPelaksana) {
                throw ValidationException::withMessages(['id_bagian_pelaksana' => 'Bagian Pelaksana tidak valid.']);
            }
            $validatedData['id_biro_pelaksana'] = $bagianPelaksana->idbiro;

            // Determine tipe_pengajuan based on tahun_anggaran
            $originalTipePengajuan = $pengajuan->tipe_pengajuan;
            if ($validatedData['tahun_anggaran'] == session('tahunanggaran')) {
                $newTipePengajuan = 'revisi';
            } else if ($validatedData['tahun_anggaran'] == session('tahunanggaran') + 1) {
                $newTipePengajuan = 'usulan';
            } else {
                throw ValidationException::withMessages(['tahun_anggaran' => 'Tahun anggaran tidak valid untuk pengajuan reguler.']);
            }
            $validatedData['tipe_pengajuan'] = $newTipePengajuan; // Set new tipe pengajuan

            // Update Pengajuan Header
            $pengajuan->fill($validatedData);
            $pengajuan->updated_by = Auth::user()->username; // Track who updated
            $pengajuan->save();

            // Handle Barang Items
            $barangItems = $request->input('barang', []);
            if (empty($barangItems) || !isset($barangItems[0]['kode_barang']) || $barangItems[0]['kode_barang'] == '') {
                throw ValidationException::withMessages(['barang' => 'Minimal harus ada satu barang yang diajukan.']);
            }

            $totalPengajuan = 0;
            $processedBarangItems = [];

            foreach ($barangItems as $index => $barang) {
                if (empty($barang['kode_barang'])) continue;

                $kuantitas = filter_var($barang['kuantitas'], FILTER_SANITIZE_NUMBER_INT);
                $hargaCleaned = str_replace(',', '', $barang['harga']);
                $harga = filter_var($hargaCleaned, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                if (!is_numeric($kuantitas) || $kuantitas < 1) {
                    throw ValidationException::withMessages(["barang.{$index}.kuantitas" => 'Kuantitas barang ke-' . ($index + 1) . ' harus minimal 1.']);
                }
                if (!is_numeric($harga) || $harga < 0) {
                    throw ValidationException::withMessages(["barang.{$index}.harga" => 'Harga barang ke-' . ($index + 1) . ' tidak valid.']);
                }
                $item = [
                    'kode_barang'     => $barang['kode_barang'],
                    'kuantitas'       => (int) $kuantitas,
                    'harga'           => (float) $harga,
                    'total'           => (int)$kuantitas * (float)$harga,
                    'keterangan_barang' => $barang['keterangan_barang'] ?? '', // Include keterangan_barang
                ];
                $processedBarangItems[] = $item;
                $totalPengajuan += $item['total'];
            }

            if (empty($processedBarangItems)) {
                throw ValidationException::withMessages(['barang' => 'Minimal harus ada satu barang yang valid diajukan.']);
            }

            // Delete old details
            if ($originalTipePengajuan == 'usulan') {
                DetilPengajuan::where('pengajuan_id', $pengajuan->id)->delete();
            } else {
                DetilRevisi::where('pengajuan_id', $pengajuan->id)->delete();
            }

            // Add new/updated details to the correct table based on newTipePengajuan
            $detailModel = $newTipePengajuan == 'usulan' ? new DetilPengajuan() : new DetilRevisi();
            foreach ($processedBarangItems as $barang) {
                $detailModel::create([
                    'pengajuan_id'    => $pengajuan->id,
                    'kode_barang'     => $barang['kode_barang'],
                    'kuantitas'       => $barang['kuantitas'],
                    'harga'           => $barang['harga'],
                    'total'           => $barang['total'],
                    'keterangan_barang' => $barang['keterangan_barang'], // Include keterangan_barang
                ]);
            }

            DB::commit();
            return response()->json([
                'status'   => 'berhasil',
                'message'  => 'Data pengajuan berhasil diupdate.',
                'redirect' => route('pengajuan.reguler.index')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Data pengajuan tidak ditemukan.'
            ], 404);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'gagal',
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            \Log::error('QueryException_UpdatePengajuanReguler: ' . $e->getMessage() . ' SQL: ' . $e->getSql() . ' Bindings: ' . implode(", ", $e->getBindings()));
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Terjadi kesalahan pada database saat mengupdate data.'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Exception_UpdatePengajuanReguler: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete pengajuan dan semua file terkait
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Cari pengajuan
            $pengajuan = Pengajuan::where('jenis_formulir', 'Pengajuan Reguler')->findOrFail($id);

            // Validasi authorization - hanya pembuat yang bisa menghapus
            if ($pengajuan->id_bagian_pengusul != Auth::user()->idbagian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk menghapus pengajuan ini.'
                ], 403);
            }

            // Validasi status - hanya draft dan ditolak yang bisa dihapus
            $allowedStatuses = ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'];
            if (!in_array($pengajuan->status_pengajuan, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan dengan status "' . $pengajuan->status_pengajuan . '" tidak dapat dihapus.'
                ], 403);
            }

            \Log::info('Memulai penghapusan pengajuan ID: ' . $id);

            // Array untuk menyimpan file yang akan dihapus
            $filesToDelete = [];

            // Kumpulkan semua file yang perlu dihapus
            if ($pengajuan->dokumen_pendukung) {
                $filesToDelete[] = storage_path('app/public/' . $pengajuan->dokumen_pendukung);
            }

            if ($pengajuan->tor_signed_path) {
                $filesToDelete[] = storage_path('app/public/' . $pengajuan->tor_signed_path);
            }

            if ($pengajuan->lampiran_signed_path) {
                $filesToDelete[] = storage_path('app/public/' . $pengajuan->lampiran_signed_path);
            }

            if ($pengajuan->berita_acara_signed_path) {
                $filesToDelete[] = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            }

            // Hapus detail pengajuan berdasarkan tipe
            if ($pengajuan->tipe_pengajuan == 'usulan') {
                $deletedDetails = DetilPengajuan::where('pengajuan_id', $pengajuan->id)->delete();
                \Log::info('Dihapus ' . $deletedDetails . ' detail pengajuan (usulan)');
            } else {
                $deletedDetails = DetilRevisi::where('pengajuan_id', $pengajuan->id)->delete();
                \Log::info('Dihapus ' . $deletedDetails . ' detail revisi');
            }

            // Hapus pengajuan utama
            $pengajuan->delete();
            \Log::info('Pengajuan ID ' . $id . ' berhasil dihapus dari database');

            // Commit transaksi database dulu
            DB::commit();

            // Hapus file-file setelah database berhasil diupdate
            $deletedFiles = 0;
            foreach ($filesToDelete as $filePath) {
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $deletedFiles++;
                        \Log::info('File dihapus: ' . $filePath);
                    } else {
                        \Log::warning('Gagal menghapus file: ' . $filePath);
                    }
                }
            }

            \Log::info('Penghapusan pengajuan ID ' . $id . ' selesai. Total file dihapus: ' . $deletedFiles);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dihapus beserta semua file terkait.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            \Log::error('Pengajuan tidak ditemukan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada destroy pengajuan: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Send Magic Link Verification via WhatsApp
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMagicLinkVerification($id)
    {
        try {
            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // Validasi bahwa Dokumen Pendukung sudah diunggah
            if (!$pengajuan->dokumen_pendukung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim link. Dokumen Pendukung wajib diunggah terlebih dahulu.'
                ], 400);
            }

            // Validasi status pengajuan
            if (!in_array($pengajuan->status_pengajuan, ['Draft', 'Ditolak Pelaksana', 'Ditolak oleh Koordinator', 'Ditolak oleh Perencanaan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak dalam status yang valid untuk verifikasi'
                ], 400);
            }

            // Tentukan level verifikasi dan dokumen yang perlu ditandatangani
            $verificationLevel = 'operator'; // Default untuk bagian pengusul
            $documentsToSign = [];

            if ($pengajuan->tipe_pengajuan === 'usulan') {
                // Usulan - Operator: Sign Berita Acara + TOR + Lampiran (3 docs)
                $documentsToSign = ['berita_acara', 'tor', 'lampiran'];
            } else {
                // Revisi - Operator: Sign Lampiran only (1 doc)
                $documentsToSign = ['lampiran'];
            }

            // Cari data Eselon III dari bagian pengusul
            $eselonIII = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pengusul)
                ->where('eselon', 'III')
                ->select('nama', 'nip', 'phone')
                ->first();

            // ======================= BLOK TESTING DENGAN DATA STATIS =======================

            // UNCOMMENT BLOK DI BAWAH INI UNTUK TESTING TANPA QUERY DATABASE

            // Skenario 1: Eselon III ditemukan dengan nomor telepon
            // $eselonIII = (object) [
            //     'nama' => 'Nama Eselon III',
            //     'nip' => '123456789012345678',
            //     'phone' => '082283372113' // Ganti dengan nomor statis untuk testing
            // ];

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
                    'message' => 'Data Eselon III tidak ditemukan untuk bagian pengusul'
                ], 404);
            }

            if (!$eselonIII->phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp Eselon III tidak tersedia'
                ], 400);
            }

            // Buat record magic link verification
            $expiresAt = now()->addMinutes(720);
            $verificationData = [
                'pengajuan_id' => $id,
                'verification_level' => $verificationLevel,
                'documents_to_sign' => json_encode($documentsToSign),
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

            // Update record dengan encrypted token
            DB::table('magic_link_verifications')
                ->where('id', $magicLinkVerification)
                ->update([
                    'encrypted_token' => $encryptedToken,
                    'sent_at' => now()
                ]);

            // Buat link verifikasi
            $verificationLink = url('/magic-link-verification/' . $encryptedToken);

            // Siapkan data untuk WhatsApp
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $uraianBagian = $bagianPengusul ? $bagianPengusul->uraianbagian : '-';

            // Format nilai pengajuan
            $totalAnggaran = 0;
            if ($pengajuan->tipe_pengajuan === 'usulan') {
                $totalAnggaran = DB::table('bmn_detil_pengajuan_rkbmnbagian_nonsbsk')
                    ->where('pengajuan_id', $id)
                    ->sum('total');
            } else {
                $totalAnggaran = DB::table('bmn_detil_revisi_rkbmnbagian_nonsbsk')
                    ->where('pengajuan_id', $id)
                    ->sum('total');
            }

            $formattedAnggaran = "Rp" . number_format($totalAnggaran, 0, ',', '.');


            // Ambil kode pengajuan yang sebenarnya, contoh:
            $nomorPengajuanUntukTemplate = $pengajuan->kode_pengajuan ?? "REG-" . $id; // Sesuaikan jika perlu

            // Siapkan deskripsi untuk jenis dokumen, contoh:
            $jenisDokumenUntukTemplate = count($documentsToSign) . " dokumen (" . ucfirst($pengajuan->tipe_pengajuan) . ")";

            // Kirim WhatsApp menggunakan template magic link verification
            $messageResult = $this->sendMagicLinkWhatsApp(
                $eselonIII->phone,                 // Argumen ke-1 -> $phone
                $eselonIII->nama,                  // Argumen ke-2 -> $namapenanggungjawab (untuk {{1}})
                $nomorPengajuanUntukTemplate,      // Argumen ke-3 -> $no_pengajuan (untuk {{2}})
                $jenisDokumenUntukTemplate,        // Argumen ke-4 -> $jenis_dokumen (untuk {{3}})
                $uraianBagian,                     // Argumen ke-5 -> $bagian_pengusul (untuk {{4}})
                $verificationLink                  // Argumen ke-6 -> $linkvalidasi (untuk {{5}}) <-- PENTING!
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
            \Log::error('Error pada sendMagicLinkVerification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    //    /**
    //     * Magic Link Validation - Show verification page
    //     *
    //     * @param string $encrypted_id
    //     * @return \Illuminate\Http\Response
    //     */
    //    public function magicLinkValidation($encrypted_id)
    //    {
    //        try {
    //            // Decrypt dan decode token
    //            $decryptedData = Crypt::decryptString($encrypted_id);
    //            $compressedData = base64_decode($decryptedData);
    //            $tokenData = gzuncompress($compressedData);
    //            $data = json_decode($tokenData, true);
    //
    //            if (!$data || !isset($data['verification_id'])) {
    //                return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                    'status' => 'error',
    //                    'message' => 'Token verifikasi tidak valid',
    //                    'pengajuan' => null
    //                ]);
    //            }
    //
    //            // Cek verification record
    //            $verification = DB::table('magic_link_verifications')
    //                ->where('id', $data['verification_id'])
    //                ->first();
    //
    //            if (!$verification) {
    //                return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                    'status' => 'error',
    //                    'message' => 'Data verifikasi tidak ditemukan',
    //                    'pengajuan' => null
    //                ]);
    //            }
    //
    //            // Cek status verifikasi
    //            if ($verification->status === 'verified') {
    //                return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                    'status' => 'already_verified',
    //                    'message' => 'Verifikasi sudah pernah dilakukan sebelumnya',
    //                    'pengajuan' => null
    //                ]);
    //            }
    //
    //            // Cek expiry
    //            if (now()->timestamp > $data['expires_at']) {
    //                // Update status ke expired
    //                DB::table('magic_link_verifications')
    //                    ->where('id', $data['verification_id'])
    //                    ->update(['status' => 'expired']);
    //
    //                return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                    'status' => 'expired',
    //                    'message' => 'Link verifikasi telah kedaluwarsa',
    //                    'pengajuan' => null
    //                ]);
    //            }
    //
    //            // Ambil data pengajuan
    //            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($verification->pengajuan_id);
    //
    //            // Siapkan data tambahan
    //            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
    //            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
    //
    //            // Decode documents to sign
    //            $documentsToSign = json_decode($verification->documents_to_sign, true);
    //
    //            // Hitung total anggaran
    //            $totalAnggaran = 0;
    //            if ($pengajuan->tipe_pengajuan === 'usulan') {
    //                $totalAnggaran = $pengajuan->detilPengajuan->sum('total');
    //            } else {
    //                $totalAnggaran = $pengajuan->detilRevisi->sum('total');
    //            }
    //
    //            return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                'status' => 'valid',
    //                'verification' => $verification,
    //                'pengajuan' => $pengajuan,
    //                'documentsToSign' => $documentsToSign,
    //                'bagianPengusul' => $bagianPengusul,
    //                'bagianPelaksana' => $bagianPelaksana,
    //                'totalAnggaran' => $totalAnggaran,
    //                'encrypted_token' => $encrypted_id,
    //                'expires_at' => $data['expires_at'],
    //                'verification_level' => 'operator' // <--- TAMBAHKAN BARIS INI
    //            ]);
    //
    //        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
    //            return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                'status' => 'error',
    //                'message' => 'Token verifikasi rusak atau tidak valid',
    //                'pengajuan' => null
    //            ]);
    //        } catch (\Exception $e) {
    //            \Log::error('Error pada magicLinkValidation: ' . $e->getMessage());
    //            return view('PerencanaanBMN.Bagian.magic-link-verification', [
    //                'status' => 'error',
    //                'message' => 'Terjadi kesalahan sistem',
    //                'pengajuan' => null
    //            ]);
    //        }
    //    }
    /**
     * Process Magic Link E-Sign
     *
     * @param \Illuminate\Http\Request $request
     * @param string $encrypted_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMagicLinkEsign(Request $request, $encrypted_id)
    {
        // 1. Validasi Input Awal
        $request->validate([
            'passphrase' => 'required|string',
            'documents' => 'required|array',
            'documents.*' => 'in:berita_acara,tor,lampiran,surat_rekomendasi'
        ]);

        DB::beginTransaction();
        try {
            // 2. Dekripsi dan Validasi Token
            $decryptedData = Crypt::decryptString($encrypted_id);
            $compressedData = base64_decode($decryptedData);
            $tokenData = gzuncompress($compressedData);
            $data = json_decode($tokenData, true);

            \Log::info('Dekripsi token berhasil: ', $data);

            if (!$data || !isset($data['verification_id'])) {
                throw new \Exception('Token verifikasi tidak valid.');
            }

            // 3. Cek Status Verifikasi dengan logging detail
            $verification = DB::table('magic_link_verifications')
                ->where('id', $data['verification_id'])
                ->first();

            \Log::info('Status verification saat ini: ', [
                'verification_id' => $data['verification_id'],
                'found' => $verification ? 'yes' : 'no',
                'status' => $verification ? $verification->status : 'null',
                'created_at' => $verification ? $verification->created_at : 'null',
                'expires_at' => $verification ? $verification->expires_at : 'null'
            ]);

            if (!$verification) {
                throw new \Exception('Data verifikasi tidak ditemukan.');
            }

            // PERBAIKAN: Cek berbagai kemungkinan status yang valid
            $validStatuses = ['pending'];
            if (!in_array($verification->status, $validStatuses)) {
                \Log::error('Status verification tidak valid: ' . $verification->status);

                // Berikan pesan error yang lebih spesifik
                if ($verification->status === 'verified') {
                    throw new \Exception('Verifikasi sudah pernah dilakukan sebelumnya.');
                } elseif ($verification->status === 'expired') {
                    throw new \Exception('Link verifikasi telah kedaluwarsa.');
                } elseif ($verification->status === 'failed') {
                    throw new \Exception('Verifikasi sebelumnya gagal. Silakan minta link baru.');
                } else {
                    throw new \Exception('Status verifikasi tidak valid: ' . $verification->status);
                }
            }

            // 4. Cek expiry dengan logging
            $currentTime = now()->timestamp;
            $expiryTime = $data['expires_at'];

            \Log::info('Checking expiry: ', [
                'current_time' => $currentTime,
                'expiry_time' => $expiryTime,
                'is_expired' => $currentTime > $expiryTime
            ]);

            if ($currentTime > $expiryTime) {
                // Update status ke expired
                DB::table('magic_link_verifications')
                    ->where('id', $data['verification_id'])
                    ->update(['status' => 'expired']);

                throw new \Exception('Link verifikasi telah kedaluwarsa.');
            }

            // 5. Proses Setiap Dokumen dalam Loop
            $pengajuan = Pengajuan::findOrFail($verification->pengajuan_id);
            $documentsToSign = json_decode($verification->documents_to_sign, true);
            $requestedDocuments = $request->input('documents');

            \Log::info('Dokumen processing: ', [
                'documents_to_sign' => $documentsToSign,
                'requested_documents' => $requestedDocuments
            ]);

            $signedDocuments = [];
            $signErrors = [];

            foreach ($requestedDocuments as $documentType) {
                if (!in_array($documentType, $documentsToSign)) {
                    $signErrors[$documentType] = 'Dokumen tidak diizinkan untuk ditandatangani.';
                    \Log::warning('Dokumen tidak diizinkan: ' . $documentType);
                    continue;
                }

                try {
                    \Log::info('Memproses dokumen: ' . $documentType);

                    // Panggil helper untuk menyiapkan data dan e-sign
                    $esignResult = $this->_processSingleDocumentEsign($pengajuan, $documentType, $request->passphrase, $verification);

                    // Update model pengajuan berdasarkan hasil
                    $pengajuan->{$esignResult['path_column']} = $esignResult['signed_path'];
                    $pengajuan->{$esignResult['date_column']} = now();

                    $signedDocuments[] = $documentType;
                    \Log::info('Dokumen berhasil ditandatangani: ' . $documentType);
                } catch (\Exception $e) {
                    \Log::error('Error signing ' . $documentType . ' via Magic Link: ' . $e->getMessage());
                    $signErrors[$documentType] = $e->getMessage();
                }
            }

            // Simpan semua perubahan pada model Pengajuan sekali saja
            if (!empty($signedDocuments)) {
                $pengajuan->save();
                \Log::info('Model pengajuan disimpan dengan dokumen: ', $signedDocuments);

                if ($verification->verification_level === 'perencanaan' && in_array('berita_acara', $signedDocuments)) {
                    $pengajuan->status_pengajuan = 'Disetujui';
                    $pengajuan->save();
                }
            }

            // 6. Update Status Verifikasi di Akhir - HANYA jika ada dokumen yang berhasil ditandatangani
            if (!empty($signedDocuments)) {
                $verificationResult = [
                    'signed_documents' => $signedDocuments,
                    'errors' => $signErrors,
                    'signed_at' => now()->toISOString(),
                    'signed_by' => $verification->eselon_iii_name,
                ];

                $newStatus = 'verified'; // Berhasil karena ada minimal 1 dokumen yang ditandatangani

                DB::table('magic_link_verifications')->where('id', $verification->id)->update([
                    'status' => $newStatus,
                    'verified_at' => now(),
                    'verification_result' => json_encode($verificationResult)
                ]);

                \Log::info('Status verification diupdate ke: ' . $newStatus);
            } else {
                // Jika tidak ada dokumen yang berhasil ditandatangani
                $verificationResult = [
                    'signed_documents' => [],
                    'errors' => $signErrors,
                    'failed_at' => now()->toISOString(),
                    'signed_by' => $verification->eselon_iii_name,
                ];

                DB::table('magic_link_verifications')->where('id', $verification->id)->update([
                    'status' => 'failed',
                    'verified_at' => now(),
                    'verification_result' => json_encode($verificationResult)
                ]);

                \Log::warning('Semua dokumen gagal ditandatangani, status diupdate ke failed');
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            return response()->json([
                'success' => count($signedDocuments) > 0,
                'message' => count($signedDocuments) > 0
                    ? 'Verifikasi berhasil! ' . count($signedDocuments) . ' dokumen telah ditandatangani.'
                    : 'Verifikasi gagal: ' . json_encode($signErrors),
                'signed_documents' => $signedDocuments,
                'errors' => $signErrors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada processMagicLinkEsign (Fixed): ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function _processSingleDocumentEsign($pengajuan, $documentType, $passphrase, $verification)
    {
        // Panggil method lama yang sudah berhasil, tapi dengan beberapa modifikasi
        switch ($documentType) {
            case 'berita_acara':
                $result = $this->_signBeritaAcaraMagicLink($pengajuan, $passphrase, $verification);

                $dateColumn = 'berita_acara_operator_signed_date'; // Default untuk Operator
                if ($verification->verification_level === 'pelaksana') {
                    $dateColumn = 'berita_acara_pelaksana_signed_date'; // Ganti jika Pelaksana
                } else if ($verification->verification_level === 'koordinator') {
                    $dateColumn = 'berita_acara_koordinator_signed_date';
                } else if ($verification->verification_level === 'perencanaan') {
                    $dateColumn = 'berita_acara_perencanaan_signed_date';
                }

                return [
                    'signed_path' => $result['signed_path'],
                    'path_column' => 'berita_acara_signed_path',
                    'date_column' => $dateColumn, // Gunakan variabel dinamis
                ];

            case 'tor':
                $result = $this->_signTorMagicLink($pengajuan, $passphrase, $verification);
                return [
                    'signed_path' => $result['signed_path'],
                    'path_column' => 'tor_signed_path',
                    'date_column' => 'tor_signed_date',
                ];

            case 'lampiran':
                $result = $this->_signLampiranMagicLink($pengajuan, $passphrase, $verification);
                return [
                    'signed_path' => $result['signed_path'],
                    'path_column' => 'lampiran_signed_path',
                    'date_column' => 'lampiran_signed_date',
                ];

            case 'surat_rekomendasi':
                $result = $this->_signRekomendasiMagicLink($pengajuan, $passphrase, $verification);
                return [
                    'signed_path' => $result['signed_path'],
                    'path_column' => 'dokumen_rekomendasi_bmn', // Nama kolom di DB
                    'date_column' => 'rekomendasi_signed_date', // Kita perlu kolom ini
                ];


            default:
                throw new \Exception("Tipe dokumen tidak valid: {$documentType}");
        }
    }
    /**
     * Sign Berita Acara via Magic Link
     */
    private function _signBeritaAcaraMagicLink($pengajuan, $passphrase, $verification)
    {
        \Log::info('Memulai sign Berita Acara via Magic Link untuk pengajuan ID: ' . $pengajuan->id . ' Level: ' . $verification->verification_level);

        // Inisialisasi variabel yang akan diisi oleh blok if/else
        $pdfBase64 = '';
        $signatureProperties = [];

        // ================== AWAL LOGIKA PERCABANGAN ==================
        // Logika dibedakan berdasarkan level verifikasi (Operator atau Pelaksana)

        if ($verification->verification_level === 'pelaksana') {
            // --- LOGIKA UNTUK PELAKSANA: Muat PDF yang sudah ditandatangani Operator ---

            // Validasi bahwa Operator sudah tanda tangan sebelumnya
            if (!$pengajuan->berita_acara_signed_path || !$pengajuan->berita_acara_operator_signed_date) {
                throw new \Exception('Berita Acara harus ditandatangani oleh Operator terlebih dahulu.');
            }

            // Ambil path file yang sudah ada
            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File Berita Acara yang ditandatangani Operator tidak ditemukan.');
            }

            // Baca konten file dan encode ke Base64
            $pdfContent = file_get_contents($filePath);
            $pdfBase64 = base64_encode($pdfContent);

            // Siapkan properti tanda tangan untuk PELAKSANA
            $signatureProperties = [
                'imageBase64' => '...', // Placeholder, akan diisi nanti
                'tampilan'    => 'VISIBLE',
                'page'        => 2,
                'originX'     => 375.0, // Posisi tanda tangan untuk Pelaksana (kanan)
                'originY'     => 215.0,
                'width'       => 75.0,
                'height'      => 75.0,
                'location'    => 'Jakarta',
                'reason'      => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Pelaksana via Magic Link)'
            ];
        } else if ($verification->verification_level === 'koordinator') {
            // --- LOGIKA UNTUK KOORDINATOR: Muat PDF yang sudah ditandatangani ---
            if (!$pengajuan->berita_acara_signed_path || !$pengajuan->berita_acara_pelaksana_signed_date) {
                throw new \Exception('Berita Acara harus ditandatangani oleh Pelaksana terlebih dahulu.');
            }
            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File Berita Acara yang ditandatangani Pelaksana tidak ditemukan.');
            }
            $pdfContent = file_get_contents($filePath);
            $pdfBase64 = base64_encode($pdfContent);

            $signatureProperties = [
                'imageBase64' => '...',
                'tampilan'    => 'VISIBLE',
                'page'        => 2,
                'originX'     => 145.0, // Sesuaikan posisi untuk Koordinator
                'originY'     => 375.0, // Sesuaikan posisi untuk Koordinator
                'width'       => 75.0,
                'height'      => 75.0,
                'location'    => 'Jakarta',
                'reason'      => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Koordinator via Magic Link)'
            ];
        } else if ($verification->verification_level === 'perencanaan') {
            // LOGIKA UNTUK PERENCANAAN: Muat PDF yang sudah ditandatangani Koordinator
            if (!$pengajuan->berita_acara_signed_path || !$pengajuan->berita_acara_koordinator_signed_date) {
                throw new \Exception('Berita Acara harus ditandatangani oleh Koordinator terlebih dahulu.');
            }
            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File Berita Acara yang ditandatangani Koordinator tidak ditemukan.');
            }
            $pdfContent = file_get_contents($filePath);
            $pdfBase64 = base64_encode($pdfContent);

            // Tentukan posisi tanda tangan ke-4 (kanan bawah)
            $signatureProperties = [
                'imageBase64' => '...',
                'tampilan'    => 'VISIBLE',
                'page' => 2,
                'originX'     => 375.0,
                'originY' => 375.0,
                'width'       => 75.0,
                'height' => 75.0,
                'location'    => 'Jakarta',
                'reason'      => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Perencanaan)'
            ];
        } else {
            // --- LOGIKA UNTUK OPERATOR (KODE ASLI): Buat PDF baru dari template ---

            // Mengambil semua data yang diperlukan untuk mengisi template PDF
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first(); //
            $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first(); //
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first(); //
            $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first(); //
            $jumlahPengadaan = ($pengajuan->tipe_pengajuan == 'usulan') ? $pengajuan->detilPengajuan()->sum('kuantitas') : $pengajuan->detilRevisi()->sum('kuantitas'); //
            $tanggal = date('d'); //
            $bulan = $this->getNamaBulan(date('m')); //
            $tahunKata = $this->angkaKeTerbilang(date('Y')); //
            $pengusulData = DB::table('pegawai')->where('id_satker', $pengajuan->id_bagian_pengusul)->where('eselon', 'III')->select('nama', 'nip')->first(); //
            $pelaksanaData = DB::table('pegawai')->where('id_satker', $pengajuan->id_bagian_pelaksana)->where('eselon', 'III')->select('nama', 'nip')->first(); //
            $koordinatorData = DB::table('pegawai')->where('id_satker', 669)->where('eselon', 'III')->select('nama', 'nip')->first(); //
            $perencanaanData = DB::table('pegawai')->where('id_satker', 657)->where('eselon', 'III')->select('nama', 'nip')->first(); //
            $formatTitleCase = function ($text) {
                if (empty($text)) return '';
                $words = explode(' ', strtolower($text));
                foreach ($words as &$word) {
                    $word = ucfirst($word);
                }
                return implode(' ', $words);
            }; //
            $dataArray = [
                'uraianBagianPengusul' => $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'), //
                'uraianBiroPengusul' => $formatTitleCase(optional($biroPengusul)->uraianbiro ?? 'Biro'), //
                'uraianBagianPelaksana' => $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'), //
                'uraianBiroPelaksana' => $formatTitleCase(optional($biroPelaksana)->uraianbiro ?? 'Biro'), //
                'tahunAnggaran' => $pengajuan->tahun_anggaran, //
                'tanggal' => $tanggal, //
                'bulan' => $bulan, //
                'tahunKata' => $tahunKata, //
                'jumlahPengadaan' => $jumlahPengadaan, //
                'jumlahPemeliharaan' => 0, //
                'pengusulNama' => optional($pengusulData)->nama, //
                'pengusulNip' => optional($pengusulData)->nip, //
                'pengusulJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'), //
                'pelaksanaNama' => optional($pelaksanaData)->nama, //
                'pelaksanaNip' => optional($pelaksanaData)->nip, //
                'pelaksanaJabatan' => 'Kepala ' . $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'), //
                'koordinatorNama' => optional($koordinatorData)->nama, //
                'koordinatorNip' => optional($koordinatorData)->nip, //
                'koordinatorJabatan' => 'Kepala Bagian Administrasi BMN', //
                'perencanaanNama' => optional($perencanaanData)->nama, //
                'perencanaanNip' => optional($perencanaanData)->nip, //
                'perencanaanJabatan' => 'Kepala Bagian Perencanaan' //
            ];

            // Membuat PDF dari template
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcara', $dataArray); //
            $pdfBase64 = base64_encode($pdf->output()); //

            // Siapkan properti tanda tangan untuk OPERATOR
            $signatureProperties = [
                'imageBase64' => '...', // Placeholder, akan diisi nanti
                'tampilan'    => 'VISIBLE',
                'page'        => 2,
                'originX'     => 145.0, // Posisi tanda tangan untuk Operator (kiri)
                'originY'     => 215.0,
                'width'       => 75.0,
                'height'      => 75.0,
                'location'    => 'Jakarta',
                'reason'      => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Operator via Magic Link)'
            ];
        }
        // ================== AKHIR LOGIKA PERCABANGAN ==================

        // Generate QR Code (logika ini sama untuk keduanya)
        $qrContent = "Berita Acara RKBMN ID: " . $pengajuan->id . "\nPenanggung Jawab: " . $verification->eselon_iii_name . "\nTanggal: " . date('d M Y');
        $qrBuilder = \Endroid\QrCode\Builder\Builder::create()
            ->data($qrContent)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->size(150)
            ->margin(5)
            ->build();
        $qrBase64 = base64_encode($qrBuilder->getString());

        // Masukkan QR code yang sudah jadi ke dalam properti tanda tangan
        $signatureProperties['imageBase64'] = $qrBase64;

        // Panggil API E-Sign (logika ini sama untuk keduanya)
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        // Idealnya, NIK diambil dari data pegawai Eselon III yang bersangkutan
        //    $nik = '3201132412920003';
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level);

        $requestData = [
            'nik'          => $nik,
            'passphrase'   => $passphrase,
            'signatureProperties' => [$signatureProperties], // Menggunakan properti yang sudah disiapkan
            'file'         => [$pdfBase64] // Menggunakan PDF yang sudah disiapkan
        ];

        $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['file'][0])) {
            throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen berita_acara");
        }

        // Simpan file yang sudah ditandatangani
        $signedPdfData = base64_decode($json['file'][0]);
        $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_berita_acara_esign');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        // Gunakan nama file yang sama untuk menimpa file sebelumnya (jika ada)
        // Ini penting agar hanya ada 1 file berita acara per pengajuan
        $fileName = 'berita_acara_' . $pengajuan->id . '_signed.pdf';
        file_put_contents("{$dirPath}/{$fileName}", $signedPdfData);

        \Log::info('PDF Berita Acara (level: ' . $verification->verification_level . ') berhasil disimpan di: ' . $dirPath . '/' . $fileName);

        // Kembalikan path relatif agar bisa disimpan ke database
        return [
            'signed_path' => "bmn_rkbmn_nonsbsk_berita_acara_esign/{$fileName}"
        ];
    }

    /**
     * Modified version of signTorMagicLink - tanpa DB transaction dan save
     */
    private function _signTorMagicLink($pengajuan, $passphrase, $verification)
    {
        \Log::info('Memulai sign TOR via Magic Link untuk pengajuan ID: ' . $pengajuan->id);

        // Format tanggal dengan Carbon
        $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('j F Y');

        // Mencari nama penanggung jawab kegiatan
        $namaPenanggungJawabPelaksana = DB::table('pegawai')
            ->where('id_satker', $pengajuan->id_bagian_pengusul)
            ->where('eselon', 'III')
            ->value('nama');

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

        $kodePengajuan = $pengajuan->kode_pengajuan;
        $keterangan = $pengajuan->keterangan;

        $dataArray = [
            'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
            'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
            'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
            'tahunAnggaranPersetujuan' => date('Y'),
            'tanggalPengajuan' => $tanggalPengajuan,
            'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
            'kodePengajuan' => $kodePengajuan,
            'keterangan' => $keterangan,
        ];

        // Generate PDF
        $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.TorUsulan', $dataArray);

        // Generate QR Code
        $qrContent = "TOR Pengajuan Non-SBSK ID: " . $pengajuan->id . "\nPenanggung Jawab: " . $namaPenanggungJawabPelaksana . "\nTanggal: " . $tanggalPengajuan;
        $qrBuilder = \Endroid\QrCode\Builder\Builder::create()
            ->data($qrContent)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->size(150)
            ->margin(5)
            ->build();
        $qrBase64 = base64_encode($qrBuilder->getString());

        // Panggil API E-Sign
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        // $nik = '3201132412920003';
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level);

        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [[
                'imageBase64' => $qrBase64,
                'tampilan' => 'VISIBLE',
                'page' => 3,
                'originX' => 415.0,
                'originY' => 640.0,
                'width' => 70.0,
                'height' => 70.0,
                'location' => 'Jakarta',
                'reason' => 'Dokumen TOR Ini Telah Disetujui dengan Tanda Tangan Elektronik (Magic Link)'
            ]],
            'file' => [base64_encode($pdf->output())]
        ];

        $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['file'][0])) {
            throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen tor");
        }

        // Simpan file yang sudah ditandatangani
        $signedPdfData = base64_decode($json['file'][0]);
        $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_tor_esign');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $fileName = 'tor_nonsbsk_' . $pengajuan->id . '_signed_magic.pdf';
        file_put_contents("{$dirPath}/{$fileName}", $signedPdfData);

        \Log::info('PDF TOR yang ditandatangani (refactored) disimpan di: ' . $dirPath . '/' . $fileName);

        return [
            'signed_path' => "bmn_rkbmn_nonsbsk_tor_esign/{$fileName}"
        ];
    }

    /**
     * Modified version of signLampiranMagicLink - tanpa DB transaction dan save
     */
    private function _signLampiranMagicLink($pengajuan, $passphrase, $verification)
    {
        \Log::info('Memulai sign Lampiran via Magic Link untuk pengajuan ID: ' . $pengajuan->id);

        // Ambil data pengajuan dengan eager loading
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($pengajuan->id);

        // Tentukan tipe pengajuan untuk memuat data yang benar
        $detailItems = [];

        if ($pengajuan->tipe_pengajuan == 'usulan') {
            foreach ($pengajuan->detilPengajuan as $item) {
                $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                $detailItems[] = [
                    'kode_barang' => $item->kode_barang,
                    'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                    'keterangan_barang' => $item->keterangan_barang,
                    'kuantitas' => $item->kuantitas,
                    'harga' => $item->harga,
                    'total' => $item->total
                ];
            }
        } else {
            foreach ($pengajuan->detilRevisi as $item) {
                $barang = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();

                $detailItems[] = [
                    'kode_barang' => $item->kode_barang,
                    'deskripsi_perlengkapan' => $barang ? $barang->ur_sskel : "Pengajuan Pemeliharaan",
                    'keterangan_barang' => $item->keterangan_barang,
                    'kuantitas' => $item->kuantitas,
                    'harga' => $item->harga,
                    'total' => $item->total
                ];
            }
        }

        // Ambil data bagian
        $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
        $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();

        // Format tanggal dengan Carbon
        $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('d F Y');

        // Mencari nama penanggung jawab kegiatan
        $namaPenanggungJawabPelaksana = DB::table('pegawai')
            ->where('id_satker', $pengajuan->id_bagian_pengusul)
            ->where('eselon', 'III')
            ->value('nama');

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
        $pdf->setPaper('A4', 'landscape');

        // Generate QR Code
        $qrContent = "Lampiran Non-SBSK ID: " . $pengajuan->id . "\nPenanggung Jawab: " . $namaPenanggungJawabPelaksana . "\nTanggal: " . $tanggalPengajuan;
        $qrBuilder = \Endroid\QrCode\Builder\Builder::create()
            ->data($qrContent)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->size(150)
            ->margin(5)
            ->build();
        $qrBase64 = base64_encode($qrBuilder->getString());

        // Panggil API E-Sign
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        // $nik = '3201132412920003';
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level);


        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [[
                'imageBase64' => $qrBase64,
                'tampilan' => 'VISIBLE',
                'page' => 1,
                'originX' => 620.0,
                'originY' => 400.0,
                'width' => 70.0,
                'height' => 70.0,
                'location' => 'Jakarta',
                'reason' => 'Dokumen Lampiran Ini Telah Disetujui dengan Tanda Tangan Elektronik (Magic Link)'
            ]],
            'file' => [base64_encode($pdf->output())]
        ];

        $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['file'][0])) {
            throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen lampiran");
        }

        // Simpan file yang sudah ditandatangani
        $signedPdfData = base64_decode($json['file'][0]);
        $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_lampiran_esign');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $fileName = 'lampiran_nonsbsk_' . $pengajuan->id . '_signed_magic.pdf';
        file_put_contents("{$dirPath}/{$fileName}", $signedPdfData);

        \Log::info('PDF Lampiran yang ditandatangani (refactored) disimpan di: ' . $dirPath . '/' . $fileName);

        return [
            'signed_path' => "bmn_rkbmn_nonsbsk_lampiran_esign/{$fileName}"
        ];
    }

    private function _signRekomendasiMagicLink($pengajuan, $passphrase, $verification)
    {
        \Log::info('Memulai proses e-sign "Surat Rekomendasi" via Magic Link untuk Pengajuan ID: ' . $pengajuan->id);

        // 1. Validasi dan ambil path file
        if (empty($pengajuan->dokumen_rekomendasi_bmn)) {
            throw new \Exception('Path untuk dokumen rekomendasi tidak ditemukan di database.');
        }
        $relativePath = $pengajuan->dokumen_rekomendasi_bmn;
        $unsignedFilePath = storage_path('app/public/' . $relativePath);

        if (!file_exists($unsignedFilePath)) {
            throw new \Exception('File Surat Rekomendasi fisik tidak ditemukan di storage.');
        }

        // 2. Baca file dan encode
        $pdfBase64 = base64_encode(file_get_contents($unsignedFilePath));

        // 3. Siapkan data untuk e-sign
        $qrContent = "Surat Rekomendasi ID: " . $pengajuan->id . "\nKoordinator: " . $verification->eselon_iii_name . "\nTanggal: " . date('d M Y');
        $qrBuilder = \Endroid\QrCode\Builder\Builder::create()->data($qrContent)->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))->size(150)->margin(5)->build();
        $qrBase64 = base64_encode($qrBuilder->getString());

        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');
        // $nik = '3201132412920003';
        $nik = $this->getNikForSigning($pengajuan, $verification->verification_level);


        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [[
                'imageBase64' => $qrBase64,
                'tampilan' => 'VISIBLE',
                'page' => 1,
                'originX' => 370.0,
                'originY' => 415.0,
                'width' => 75.0,
                'height' => 75.0,
                'location' => 'Jakarta',
                'reason' => 'Surat Rekomendasi disetujui secara elektronik'
            ]],
            'file' => [$pdfBase64]
        ];

        // 4. Panggil API
        $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['file'][0])) {
            throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen Surat Rekomendasi.");
        }

        // 5. Simpan file yang sudah ditandatangani
        $signedPdfData = base64_decode($json['file'][0]);
        Storage::disk('public')->put($relativePath, $signedPdfData);

        \Log::info('PDF Surat Rekomendasi berhasil ditandatangani dan disimpan kembali ke: ' . $relativePath);

        // 6. Kembalikan path
        return ['signed_path' => $relativePath];
    }

    /**
     * Send Magic Link WhatsApp message (disesuaikan untuk template baru)
     */
    private function sendMagicLinkWhatsApp(
        $phone,                 // Nomor telepon penerima
        $namapenanggungjawab,
        $no_pengajuan,
        $jenis_dokumen,         // Untuk {{3}}
        $bagian_pengusul,
        $linkvalidasi
    ) {
        try {
            $kepada = $this->formatnomerwhatsapp($phone);
            $token_qontak = getenv("TOKEN_QONTAK");

            // !! PENTING: Ganti dengan MESSAGE_TEMPLATE_ID dari template baru Anda di Qontak !!
            $messageTemplateId = "478e52a2-09fd-4765-a37e-db2b10fd3cec";
            // Channel Integration ID kemungkinan tetap sama, jika beda, ganti juga.
            $channelIntegrationId = '81b411ae-b566-4ec5-bb7b-361b9f66131f';

            if (empty($token_qontak)) {
                // Anda mungkin ingin log error ini jika menggunakan framework dengan logging
                \Log::error('TOKEN_QONTAK tidak ditemukan di environment variable.');
                return "Error: Token Qontak tidak ada"; // Beri pesan error lebih spesifik
            }

            if ($messageTemplateId === "YOUR_NEW_MESSAGE_TEMPLATE_ID_HERE") {
                \Log::error('Message Template ID baru belum diset.');
                return "Error: Message Template ID baru belum diset";
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false, // Tambahkan ini
                CURLOPT_SSL_VERIFYHOST => false, // Tambahkan ini (gunakan 0 jika versi cURL lebih baru)
                CURLOPT_POSTFIELDS => json_encode([
                    'to_number' => $kepada,
                    'to_name' => $namapenanggungjawab, // Menggunakan nama penanggung jawab
                    'message_template_id' => $messageTemplateId, // ID template baru
                    'channel_integration_id' => $channelIntegrationId,
                    'language' => [
                        'code' => 'id'
                    ],
                    'parameters' => [
                        'body' => [
                            [
                                'key' => '1',
                                'value' => 'nama_penerima', // Diubah! Panjang 13 karakter, sesuai aturan.
                                'value_text' => $namapenanggungjawab
                            ],
                            [
                                'key' => '2', // Sesuai {{2}}
                                'value' => 'no_pengajuan',
                                'value_text' => $no_pengajuan
                            ],
                            [
                                'key' => '3', // Sesuai {{3}}
                                'value' => 'jenis_dokumen',
                                'value_text' => $jenis_dokumen
                            ],
                            [
                                'key' => '4', // Sesuai {{4}}
                                'value' => 'bagian_pengusul',
                                'value_text' => $bagian_pengusul
                            ],
                            [
                                'key' => '5', // Sesuai {{5}}
                                'value' => 'linkvalidasi',
                                'value_text' => $linkvalidasi
                            ]
                        ]
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $token_qontak",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                \Log::error('Curl Error: ' . $err);
                return "Error"; // Atau "Error cURL: " . $err;
            } else {
                $response_data = json_decode($response, true);
                if (isset($response_data['status']) && $response_data['status'] === 'success') {
                    return "Sukses";
                } else {
                    \Log::error('Qontak API Error: ' . $response);
                    return "Error"; // Atau "Error API: " . $response;
                }
            }
        } catch (\Exception $e) {
            // Pastikan \Log::error bisa diakses atau ganti dengan error_log jika tidak menggunakan Laravel/framework sejenis
            // error_log('Error sending WhatsApp: ' . $e->getMessage());
            if (class_exists('\Log')) {
                \Log::error('Error sending WhatsApp: ' . $e->getMessage());
            } else {
                error_log('Error sending WhatsApp: ' . $e->getMessage());
            }
            return "Error";
        }
    }

    /**
     * Menampilkan halaman validasi Magic Link untuk semua level.
     */
    public function magicLinkValidation($encrypted_id)
    {
        try {
            // Decrypt dan decode token
            $decryptedData = \Illuminate\Support\Facades\Crypt::decryptString($encrypted_id);
            $tokenData = json_decode(gzuncompress(base64_decode($decryptedData)), true);

            if (!$tokenData || !isset($tokenData['verification_id'])) {
                throw new \Exception('Token verifikasi tidak valid.');
            }

            // Cek verification record
            $verification = DB::table('magic_link_verifications')->where('id', $tokenData['verification_id'])->first();
            if (!$verification) {
                throw new \Exception('Data verifikasi tidak ditemukan.');
            }

            // Cek status dan expiry
            if ($verification->status === 'verified') {
                return view('PerencanaanBMN.Bagian.magic-link-verification', ['status' => 'already_verified', 'message' => 'Verifikasi sudah pernah dilakukan sebelumnya.']);
            }
            if (now()->timestamp > $tokenData['expires_at']) {
                DB::table('magic_link_verifications')->where('id', $tokenData['verification_id'])->update(['status' => 'expired']);
                return view('PerencanaanBMN.Bagian.magic-link-verification', ['status' => 'expired', 'message' => 'Link verifikasi telah kedaluwarsa.']);
            }

            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($verification->pengajuan_id);

            // Siapkan data untuk view
            $totalAnggaran = ($pengajuan->tipe_pengajuan === 'usulan') ? $pengajuan->detilPengajuan->sum('total') : $pengajuan->detilRevisi->sum('total');

            // Tentukan dokumen apa saja yang perlu DITAMPILKAN di view.
            $documentsToSign = json_decode($verification->documents_to_sign, true);
            $documentsToShow = [];

            // Logika default untuk menampilkan dokumen
            if ($pengajuan->jenis_formulir === 'Pengajuan Reguler' && $pengajuan->tipe_pengajuan === 'revisi') {
                $documentsToShow = ['berita_acara', 'lampiran'];
            } else {
                $documentsToShow = ['berita_acara', 'tor', 'lampiran'];
            }

            // LOGIKA BARU: Cek apakah Surat Rekomendasi ada, jika ya, tambahkan ke daftar.
            if (!empty($pengajuan->dokumen_rekomendasi_bmn)) {
                // Tambahkan 'surat_rekomendasi' ke awal array agar muncul pertama di tab
                array_unshift($documentsToShow, 'surat_rekomendasi');
            }

            // Untuk Operator, yang ditampilkan hanya yang perlu ditandatangani
            if ($verification->verification_level === 'operator') {
                $documentsToShow = $documentsToSign;
            }
            // Definisikan variabel untuk mengecek keberadaan dokumen pendukung
            $hasDokumenPendukung = !empty($pengajuan->dokumen_pendukung);

            return view('PerencanaanBMN.Bagian.magic-link-verification', [
                'status'            => 'valid',
                'verification'      => $verification,
                'pengajuan'         => $pengajuan,
                'documentsToSign'   => $documentsToSign, // Dokumen yang perlu di-sign
                'documentsToShow'   => $documentsToShow, // Dokumen yang perlu di-preview
                'totalAnggaran'     => $totalAnggaran,
                'encrypted_token'   => $encrypted_id,
                'expires_at'        => $tokenData['expires_at'],
                'verification_level' => $verification->verification_level,
                'bagianPengusul'    => DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first(),
                'has_dokumen_pendukung' => $hasDokumenPendukung, // Kirim variabel ke view
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada magicLinkValidation (terpusat): ' . $e->getMessage());
            return view('PerencanaanBMN.Bagian.magic-link-verification', [
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * [BARU & TERPUSAT] Preview Dokumen via Magic Link
     */
    public function previewDocumentMagicLink($encrypted_id, $document_type)
    {
        try {
            $decryptedData = \Illuminate\Support\Facades\Crypt::decryptString($encrypted_id);
            $tokenData = json_decode(gzuncompress(base64_decode($decryptedData)), true);

            if (!$tokenData || !isset($tokenData['verification_id'])) {
                abort(403, 'Token verifikasi tidak valid.');
            }

            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($tokenData['pengajuan_id']);

            $filePath = null;
            switch ($document_type) {
                case 'berita-acara':
                    $filePath = $pengajuan->berita_acara_signed_path;
                    break;
                case 'tor':
                    $filePath = $pengajuan->tor_signed_path;
                    break;
                case 'lampiran':
                    $filePath = $pengajuan->lampiran_signed_path;
                    break;
                case 'surat-rekomendasi':
                    $filePath = $pengajuan->dokumen_rekomendasi_bmn;
                    break;
                case 'dokumen-pendukung':
                    $filePath = $pengajuan->dokumen_pendukung;
                    break;
                default:
                    abort(404, 'Tipe dokumen tidak dikenali.');
            }

            // Pengecekan utama yang menyebabkan 404
            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                // Jika gagal di sini, berarti file tidak ada di storage/app/public atau symlink salah
                abort(404, 'File dokumen tidak ditemukan.');
            }

            // Mengembalikan file untuk ditampilkan di browser
            return response()->file(storage_path('app/public/' . $filePath));
        } catch (\Exception $e) {
            \Log::error('Error previewDocumentMagicLink: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan sistem saat menampilkan dokumen.');
        }
    }

    /**
     * Helper method untuk mendapatkan NIK berdasarkan level verifikasi
     *
     * @param Pengajuan $pengajuan
     * @param string $verificationLevel
     * @return string|null
     */
    private function getNikForSigning($pengajuan, $verificationLevel = 'operator')
    {
        try {
            $idSatker = null;

            // Tentukan id_satker berdasarkan level verifikasi
            switch ($verificationLevel) {
                case 'operator':
                    $idSatker = $pengajuan->id_bagian_pengusul;
                    break;
                case 'pelaksana':
                    $idSatker = $pengajuan->id_bagian_pelaksana;
                    break;
                case 'koordinator':
                    $idSatker = 669; // Bagian Administrasi BMN
                    break;
                case 'perencanaan':
                    $idSatker = 657; // Bagian Perencanaan
                    break;
                default:
                    $idSatker = $pengajuan->id_bagian_pengusul;
            }

            // Query NIK dari tabel pegawai
            $pegawai = DB::table('pegawai')
                ->where('id_satker', $idSatker)
                ->where('eselon', 'III')
                ->select('nik')
                ->first();

            if (!$pegawai || !$pegawai->nik) {
                \Log::warning("NIK tidak ditemukan untuk satker: {$idSatker}, level: {$verificationLevel}");
                throw new \Exception("NIK Eselon III tidak ditemukan untuk {$verificationLevel}");
            }

            \Log::info("NIK ditemukan untuk {$verificationLevel}: " . $pegawai->nik . " (satker: {$idSatker})");
            return $pegawai->nik;
        } catch (\Exception $e) {
            \Log::error('Error getting NIK: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan NIK untuk tanda tangan: ' . $e->getMessage());
        }
    }


    /**
     * Format nomor WhatsApp
     */
    private function formatnomerwhatsapp($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}