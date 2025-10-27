<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian\NonSBSK;

use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilPengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilRevisi;
use App\Models\Realisasi\Admin\LaporanRealisasiAnggaranModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;

class PelaksanaPengajuanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function indexPelaksana()
    {
        $tahunanggaran = session('tahunanggaran');
        $idBagian = Auth::user()->idbagian;

        // Pelaksana bisa lihat semua kecuali Draft
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi', 'bagianPengusul'])
            ->where('id_bagian_pelaksana', $idBagian)
            ->whereNotIn('status_pengajuan', ['Draft'])
            ->orderBy('created_at', 'desc')
            ->get();

        $uniqueStatuses = $pengajuan->pluck('status_pengajuan')->unique()->sort();

        return view('PerencanaanBMN.Bagian.pelaksana_nonsbsk.index', compact(
            'pengajuan',
            'tahunanggaran',
            'uniqueStatuses'
        ));
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Cari data pengajuan dengan eager loading
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
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : 'Pengajuan Pemeliharaan',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $itemTotal,
                        'path_image' => $item->path_image // Tambahkan path_image ke response
                    ];
                }
            }

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
                'detil_revisi' => $detilRevisi ?? [],
                'total_anggaran_pengajuan' => $totalAnggaranPengajuan ?? 0,
                'total_anggaran_revisi' => $totalAnggaranRevisi ?? 0,
                'dokumen_html' => $dokumenHTML ?? '-',
                'alasan_penolakan_pelaksana' => $pengajuan->alasan_penolakan_pelaksana,
                'alasan_penolakan_koordinator' => $pengajuan->alasan_penolakan_koordinator,
                'id_bagian_pengusul' => $pengajuan->id_bagian_pengusul
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada show pengajuan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            // ], 500);
        }
    }

    public function submitToKoordinator($id)
    {
        $pengajuan = Pengajuan::find($id);

        if ($pengajuan) {
            // Update the status to 'Ajukan ke Koordinator'
            $pengajuan->status = 'Ajukan ke Koordinator';
            $pengajuan->save();

            return redirect()->route('pelaksana.index')->with('status', 'Pengajuan berhasil diajukan ke Koordinator');
        }

        return back()->with('error', 'Pengajuan tidak ditemukan');
    }

    public function review($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $tahun_anggaran = session('tahunanggaran');

        // Get bagian and biro information for display
        $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
        $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
        $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
        $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();


        // Get ALL pengenal options for the current pengajuan's bagian
        $pengenalOptions = LaporanRealisasiAnggaranModel::select('pengenal')
            ->where('idbagian', $pengajuan->id_bagian_pengusul)
            ->where('tahunanggaran', $tahun_anggaran)
            ->pluck('pengenal');

        return view('PerencanaanBMN.Bagian.pelaksana_nonsbsk.ReviewPagePelaksana', compact(
            'pengajuan',
            'bagianPengusul',
            'biroPengusul',
            'bagianPelaksana',
            'biroPelaksana',
            'pengenalOptions'
        ));
    }

    public function getPengenalOptions(Request $request)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($request->id);
            $idBagian = $pengajuan->id_bagian_pengusul;
            $tahun_anggaran = session('tahunanggaran');

            $options = LaporanRealisasiAnggaranModel::select('pengenal')
                ->where('idbagian', $idBagian)
                ->where('tahunanggaran', $tahun_anggaran)
                ->pluck('pengenal');

            // log options
            \Log::info('Daftar kode pengenal:', [
                'id_bagian' => $idBagian,
                'tahun_anggaran' => $tahun_anggaran,
                'options' => $options
            ]);

            return response()->json([
                'success' => true,
                'data'    => $options,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getPengenalOptions: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kode pengenal'
            ], 500);
        }
    }

    public function updateReview(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            Log::info('Menerima request updateReview untuk ID: ' . $id);
            Log::info('Request payload:', $request->all());

            // Ambil data pengajuan terlebih dahulu untuk menentukan validasi
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            // Cek apakah gambar diperlukan berdasarkan tipe pengajuan dan tahun anggaran
            $requiresImage = ($pengajuan->tipe_pengajuan == 'revisi');

            $validationRules = [
                'status' => 'required|string|in:Terima,Ditolak',
                'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string',
            ];

            // Validasi image hanya jika status Terima DAN gambar diperlukan
            if ($request->status === 'Terima' && $requiresImage) {
                $validationRules['image.*'] = 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120'; // 5MB
            }

            // Update validasi untuk kode_pengenal atau kode_akun
            if ($request->status === 'Terima') {
                if ($pengajuan->tipe_pengajuan == 'revisi') {
                    $validationRules['kode_pengenal'] = 'required|string|max:50';
                } else {
                    $validationRules['kode_akun'] = 'required|string|max:10';
                }
            }

            $validator = Validator::make($request->all(), $validationRules, [
                'status.required' => 'Status review harus dipilih',
                'status.in' => 'Status harus Terima atau Ditolak',
                'alasan_penolakan.required_if' => 'Alasan penolakan harus diisi jika status Ditolak',
                'image.*.image' => 'File harus berupa gambar',
                'image.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
                'image.*.max' => 'Ukuran gambar maksimal 5MB',
                'kode_pengenal.required' => 'Kode pengenal harus dipilih',
                'kode_akun.required' => 'Akun harus dipilih',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi pengajuan harus dalam status yang valid
            if (!in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana', 'Ditolak oleh Koordinator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak dalam status yang valid untuk direview'
                ], 400);
            }

            // Handle the status update based on the decision
            if ($request->status === 'Ditolak') {
                // Update status dan alasan penolakan
                $pengajuan->status_pengajuan = 'Ditolak Pelaksana';
                $pengajuan->alasan_penolakan_pelaksana = $request->alasan_penolakan;

                // REFACTOR: Hapus file berita acara yang sudah ditandatangani (single path)
                if (!empty($pengajuan->berita_acara_signed_path)) {
                    $fullPath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        Log::info('File berita acara dihapus: ' . $fullPath);
                    }
                }

                // REFACTOR: Reset field berita acara di database (single path + timestamps)
                $pengajuan->berita_acara_signed_path = null;
                $pengajuan->berita_acara_operator_signed_date = null;
                $pengajuan->berita_acara_pelaksana_signed_date = null;
                $pengajuan->berita_acara_koordinator_signed_date = null;

                // Hapus semua file dokumen lainnya (tidak berubah)
                $documentFilesToDelete = [
                    $pengajuan->tor_signed_path,
                    $pengajuan->lampiran_signed_path,
                    $pengajuan->dokumen_pendukung
                ];

                foreach ($documentFilesToDelete as $filePath) {
                    if (!empty($filePath)) {
                        $fullPath = storage_path('app/public/' . $filePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                            Log::info('File dokumen dihapus: ' . $fullPath);
                        }
                    }
                }

                // Reset semua field dokumen di database (tidak berubah)
                $pengajuan->tor_signed_path = null;
                $pengajuan->tor_signed_date = null;
                $pengajuan->lampiran_signed_path = null;
                $pengajuan->lampiran_signed_date = null;
                $pengajuan->dokumen_pendukung = null;

                $message = 'Pengajuan telah ditolak dan dikembalikan ke pengaju. Semua dokumen (TOR, Lampiran, Dokumen Pendukung) dan Berita Acara telah dihapus dan harus dimulai ulang dari operator.';
            } else {
                // Jika status Terima

                // --- START: MODIFICATION ---
                // Cek apakah Berita Acara diperlukan untuk tipe pengajuan ini
                $beritaAcaraIsRequired = true;
                if ($pengajuan->jenis_formulir === 'Pengajuan Reguler' && $pengajuan->tipe_pengajuan === 'revisi') {
                    $beritaAcaraIsRequired = false;
                }

                // Jika Berita Acara diperlukan, cek apakah sudah ditandatangani oleh Pelaksana
                if ($beritaAcaraIsRequired && is_null($pengajuan->berita_acara_pelaksana_signed_date)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan. Berita Acara harus ditandatangani oleh Eselon III Pelaksana terlebih dahulu melalui Magic Link sebelum diajukan ke Koordinator.'
                    ], 422);
                }
                // --- END: MODIFICATION ---

                $pengajuan->status_pengajuan = 'Diajukan ke Koordinator';

                // Update kode_pengenal atau kode_akun berdasarkan tipe pengajuan
                if ($pengajuan->tipe_pengajuan == 'revisi') {
                    $pengajuan->kode_pengenal = $request->kode_pengenal;
                    Log::info('Updated kode_pengenal to: ' . $request->kode_pengenal);
                } else {
                    $pengajuan->kode_akun = $request->kode_akun;
                    Log::info('Updated kode_akun to: ' . $request->kode_akun);
                }

                // Jika sebelumnya ditolak oleh koordinator, hapus alasan penolakan koordinator
//                if ($pengajuan->status_pengajuan === 'Ditolak oleh Koordinator') {
//                    $pengajuan->alasan_penolakan_koordinator = null;
//                }

                // Validasi gambar hanya jika diperlukan
                if ($requiresImage) {
                    // Validasi semua item memiliki gambar
                    $imageItems = $request->file('image') ?? [];

                    // Ambil tipe pengajuan untuk memuat data yang benar
                    $detailItems = $pengajuan->detilRevisi;

                    // Kumpulkan item yang sudah memiliki gambar
                    $itemsWithImages = [];
                    foreach ($detailItems as $item) {
                        if (!empty($item->path_image)) {
                            $itemsWithImages[] = $item->id;
                        }
                    }

                    // Validasi setiap item harus memiliki gambar
                    $allItemIds = $detailItems->pluck('id')->toArray();
                    $itemsWithNewImages = array_keys($imageItems);

                    // Gabungkan item yang sudah ada gambar dengan item yang akan diupload gambar baru
                    $itemsWithImagesTotal = array_unique(array_merge($itemsWithImages, $itemsWithNewImages));

                    $itemsStillMissingImages = array_diff($allItemIds, $itemsWithImagesTotal);

                    Log::info('Validasi gambar:', [
                        'total_items' => count($allItemIds),
                        'items_with_existing_images' => $itemsWithImages,
                        'items_with_new_images' => $itemsWithNewImages,
                        'items_still_missing' => $itemsStillMissingImages
                    ]);

                    if (count($itemsStillMissingImages) > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Setiap item barang harus memiliki gambar untuk pengajuan revisi'
                        ], 422);
                    }

                    // Upload gambar jika ada file yang diupload
                    if ($request->hasFile('image')) {
                        foreach ($request->file('image') as $itemId => $file) {
                            // Validasi format gambar dan ukuran
                            $validator = Validator::make(['image' => $file], [
                                'image' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
                            ]);

                            if ($validator->fails()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Validasi gambar untuk item #' . $itemId . ' gagal: ' . $validator->errors()->first('image')
                                ], 422);
                            }

                            // Proses upload
                            $path = $file->store('pengajuan_item_images', 'public');
                            Log::info('Menyimpan gambar ke: ' . $path);

                            // PERBAIKAN: Update path untuk detil revisi
                            $detailItem = DetilRevisi::where('pengajuan_id', $id)
                                ->where('id', $itemId)
                                ->first();

                            if ($detailItem) {
                                // Hapus gambar lama jika ada
                                if ($detailItem->path_image && Storage::disk('public')->exists($detailItem->path_image)) {
                                    Storage::disk('public')->delete($detailItem->path_image);
                                }

                                $detailItem->path_image = $path;
                                $detailItem->save();

                                Log::info('Updated path_image untuk detil revisi item ID: ' . $itemId . ' dengan path: ' . $path);
                            } else {
                                Log::warning('Detil revisi dengan ID #' . $itemId . ' tidak ditemukan.');
                            }
                        }
                    }
                }

                $message = 'Pengajuan telah disetujui dan diajukan ke Koordinator';
            }

            // Simpan pengajuan
            $pengajuan->save();

            DB::commit();

            // Return JSON response untuk AJAX
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada updateReview: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function verifikasiBeritaAcaraPelaksana(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'passphrase' => 'required|string',
            ]);

            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            \Log::info('Memulai verifikasi Berita Acara (Pelaksana) untuk pengajuan ID: ' . $id);

            // REFACTOR: Verifikasi bahwa operator sudah menandatangani berdasarkan timestamp
            if (!$pengajuan->berita_acara_operator_signed_date) {
                throw new \Exception('Berita acara harus ditandatangani oleh Operator terlebih dahulu');
            }

            // REFACTOR: Verifikasi bahwa pelaksana belum menandatangani berdasarkan timestamp
            if ($pengajuan->berita_acara_pelaksana_signed_date) {
                throw new \Exception('Berita acara sudah ditandatangani oleh Pelaksana');
            }

            // REFACTOR: Baca file berita acara dari single path
            if (!$pengajuan->berita_acara_signed_path) {
                throw new \Exception('File berita acara belum tersedia');
            }

            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File berita acara tidak ditemukan');
            }

            // Baca file PDF sebagai base64
            $pdfContent = file_get_contents($filePath);
            $pdfBase64 = base64_encode($pdfContent);
            \Log::info('PDF berita acara berhasil dikonversi ke base64');

            // API e-sign setup (tidak berubah)
            $client = new \GuzzleHttp\Client([
                'timeout' => 120,
                'connect_timeout' => 30,
                'verify' => false,
            ]);

            $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
            $username = config('app.esign_username', 'ApaKabahrul');
            $password = config('app.esign_password', 'ApaKabahrul');

            \Log::info('Mengirim request ke API e-sign: ' . $url);

            // $nik = '3174062610940001'; // Mas Irfan

            $nik = $this->getNikForSigning($pengajuan, 'pelaksana');
            \Log::info('Menggunakan NIK Pelaksana: ' . $nik . ' untuk e-sign');

            // Buat QR Code untuk tanda tangan
            $qrContent = "Berita Acara RKBMN ID: " . $id . "\nPelaksana: " . Auth::user()->name . "\nTanggal: " . date('d M Y');
            $qrBuilder = Builder::create()
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();

            $qrBase64 = base64_encode($qrBuilder->getString());
            \Log::info('QR Code untuk Pelaksana berhasil dibuat');

            // Siapkan data untuk API e-sign
            $requestData = [
                'nik' => $nik,
                'passphrase' => $request->passphrase,
                'signatureProperties' => [
                    [
                        'imageBase64' => $qrBase64,
                        'tampilan' => 'VISIBLE',
                        'page' => 2,
                        'originX' => 375.0,
                        'originY' => 265.0,
                        'width' => 75.0,
                        'height' => 75.0,
                        'location' => 'Jakarta',
                        'reason' => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Pelaksana)'
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
                \Log::info('PDF yang ditandatangani oleh Pelaksana berhasil di-decode');

                // Pastikan direktori ada
                $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_berita_acara_esign');
                if (!file_exists($dirPath)) {
                    if (!mkdir($dirPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori: ' . $dirPath);
                    }
                    \Log::info('Direktori dibuat: ' . $dirPath);
                }

                // REFACTOR: Simpan file yang sudah ditandatangani dengan nama yang mencerminkan level pelaksana
                $fileName = 'berita_acara_' . $id . '_pelaksana_signed.pdf';
                $signedPdfPath = $dirPath . '/' . $fileName;

                $bytesWritten = file_put_contents($signedPdfPath, $signedPdfData);
                if ($bytesWritten === false) {
                    throw new \Exception('Gagal menulis file PDF yang ditandatangani');
                }
                \Log::info('PDF yang ditandatangani oleh Pelaksana disimpan di: ' . $signedPdfPath);

                // REFACTOR: Update record pengajuan dengan single path dan timestamp pelaksana
                $pengajuan->berita_acara_signed_path = 'bmn_rkbmn_nonsbsk_berita_acara_esign/' . $fileName;
                $pengajuan->berita_acara_pelaksana_signed_date = now();
                $pengajuan->save();
                \Log::info('Database diupdate dengan path file signed: ' . $pengajuan->berita_acara_signed_path);

                DB::commit();
                \Log::info('Transaksi database berhasil di-commit');

                // Buat URL download
                $downloadUrl = url('storage/' . $pengajuan->berita_acara_signed_path);

                return response()->json([
                    'success' => true,
                    'message' => 'Berita Acara berhasil diverifikasi dan ditandatangani oleh Pelaksana',
                    'download_url' => $downloadUrl,
                    'status' => [
                        'operator_signed' => !is_null($pengajuan->berita_acara_operator_signed_date),
                        'pelaksana_signed' => true,
                        'koordinator_signed' => !is_null($pengajuan->berita_acara_koordinator_signed_date)
                    ]
                ]);
            } else {
                \Log::error('Response tidak memiliki file yang valid: ' . json_encode($json));
                throw new \Exception('Gagal mendapatkan file PDF yang sudah ditandatangani dari API e-sign');
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            DB::rollBack();
            \Log::error('Request error: ' . $e->getMessage());

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                \Log::error('Response status: ' . $statusCode);
                \Log::error('Response body: ' . $responseBody);

                $errorMessage = 'Terjadi kesalahan pada API e-sign (Status ' . $statusCode . ')';
                try {
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
            \Log::error('Verifikasi Berita Acara Pelaksana Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Download Berita Acara yang sudah ditandatangani oleh Pelaksana
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadBeritaAcaraPelaksanaSigned($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // REFACTOR: Cek status tanda tangan berdasarkan timestamp dan path
            if (!$pengajuan->berita_acara_pelaksana_signed_date || !$pengajuan->berita_acara_signed_path) {
                return back()->with('error', 'Berita Acara belum ditandatangani oleh Pelaksana');
            }

            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);

            // Cek apakah file ada
            if (!file_exists($filePath)) {
                return back()->with('error', 'File Berita Acara tertandatangani tidak ditemukan');
            }

            // Download file
            $fileName = 'Berita_Acara_Signed_Pelaksana_' . $id . '.pdf';
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            \Log::error('Error pada downloadBeritaAcaraPelaksanaSigned: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Send Magic Link Verification untuk Pelaksana
     */
    public function sendMagicLinkVerification($id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::findOrFail($id);

            // Validasi status
            if (!in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Pelaksana'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak dalam status yang valid untuk verifikasi pelaksana'
                ], 400);
            }

            // Validasi berita acara
            if (!$pengajuan->berita_acara_operator_signed_date || $pengajuan->berita_acara_pelaksana_signed_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita acara belum siap untuk ditandatangani pelaksana'
                ], 400);
            }

            $eselonIII = DB::table('pegawai')
                ->where('id_satker', $pengajuan->id_bagian_pelaksana)
                ->where('eselon', 'III')
                ->select('nama', 'nip', 'phone')
                ->first();

            if (!$eselonIII || !$eselonIII->phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Eselon III Unit Pelaksana tidak ditemukan atau nomor WhatsApp tidak terdaftar'
                ], 404);
            }

            // $eselonIII = (object) [
            //     'nama' => 'Nama Eselon III',
            //     'nip' => '123456789012345678',
            //     'phone' => '123456' // Ganti dengan nomor statis untuk testing
            // ];

            // Create verification record - PERBAIKAN: tanpa timestamps
            $expiresAt = now()->addMinutes(720);
            $verificationData = [
                'pengajuan_id' => $pengajuan->id,
                'verification_level' => 'pelaksana',
                'documents_to_sign' => json_encode(['berita_acara']),
                'eselon_iii_nip' => $eselonIII->nip,
                'eselon_iii_name' => $eselonIII->nama,
                'eselon_iii_phone' => $eselonIII->phone,
                'triggered_by_user_id' => Auth::id(),
                'expires_at' => $expiresAt,
                'status' => 'pending'
            ];

            $magicLinkVerification = DB::table('magic_link_verifications')->insertGetId($verificationData);

            // Generate token
            $tokenData = json_encode([
                'verification_id' => $magicLinkVerification,
                'pengajuan_id' => $pengajuan->id,
                'level' => 'pelaksana',
                'expires_at' => $expiresAt->timestamp
            ]);

            $compressedData = gzcompress($tokenData);
            $encryptedToken = Crypt::encryptString(base64_encode($compressedData));

            // Update record dengan token
            DB::table('magic_link_verifications')
                ->where('id', $magicLinkVerification)
                ->update([
                    'encrypted_token' => $encryptedToken,
                    'sent_at' => now()
                ]);

            $verificationLink = url("/magic-link-validation/{$encryptedToken}");

            // Kirim WhatsApp
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
            $messageResult = $this->sendMagicLinkWhatsApp(
                $eselonIII->phone,
                $eselonIII->nama,
                $pengajuan->kode_pengajuan ?? "PEL-" . $id,
                "1 dokumen (Berita Acara - Pelaksana)",
                $bagianPelaksana ? $bagianPelaksana->uraianbagian : 'Unit Pelaksana',
                $verificationLink
            );

            if ($messageResult === "Sukses") {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Magic Link verifikasi berhasil dikirim ke ' . $eselonIII->nama,
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
            \Log::error('Error sendMagicLinkVerification Pelaksana: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Magic Link WhatsApp (copy dari PengajuanRegulerController)
     */
    /**
     * Send Magic Link WhatsApp message (disesuaikan untuk template baru)
     */
    private function sendMagicLinkWhatsApp(
        $phone,                // Nomor telepon penerima
        $namapenanggungjawab,
        $no_pengajuan,
        $jenis_dokumen,        // Untuk {{3}}
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
     * Magic Link Validation - Show verification page untuk Pelaksana
     *
     * @param string $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function magicLinkValidation($encrypted_id)
    {
        try {
            // Decrypt dan decode token
            $decryptedData = Crypt::decryptString($encrypted_id);
            $compressedData = base64_decode($decryptedData);
            $tokenData = gzuncompress($compressedData);
            $data = json_decode($tokenData, true);

            if (!$data || !isset($data['verification_id'])) {
                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'error',
                    'message' => 'Token verifikasi tidak valid',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Cek verification record
            $verification = DB::table('magic_link_verifications')
                ->where('id', $data['verification_id'])
                ->first();

            if (!$verification) {
                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'error',
                    'message' => 'Data verifikasi tidak ditemukan',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Cek apakah ini untuk pelaksana
            if ($verification->verification_level !== 'pelaksana') {
                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'error',
                    'message' => 'Link verifikasi tidak valid untuk pelaksana',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Cek status verifikasi
            if ($verification->status === 'verified') {
                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'already_verified',
                    'message' => 'Verifikasi sudah pernah dilakukan sebelumnya',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Cek expiry
            if (now()->timestamp > $data['expires_at']) {
                // Update status ke expired
                DB::table('magic_link_verifications')
                    ->where('id', $data['verification_id'])
                    ->update(['status' => 'expired']);

                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'expired',
                    'message' => 'Link verifikasi telah kedaluwarsa',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Ambil data pengajuan
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($verification->pengajuan_id);

            // Validasi bahwa berita acara operator sudah ditandatangani
            if (!$pengajuan->berita_acara_operator_signed_date) {
                return view('PerencanaanBMN.Bagian.magic-link-verification', [
                    'status' => 'error',
                    'message' => 'Berita acara belum ditandatangani oleh operator',
                    'pengajuan' => null,
                    'documentsToSign' => [],
                    'documentsToShow' => [],
                    'totalAnggaran' => 0,
                    'has_dokumen_pendukung' => false
                ]);
            }

            // Siapkan data tambahan
            $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
            $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();

            // Hitung total anggaran
            $totalAnggaran = 0;
            if ($pengajuan->tipe_pengajuan === 'usulan') {
                $totalAnggaran = $pengajuan->detilPengajuan->sum('total');
            } else {
                $totalAnggaran = $pengajuan->detilRevisi->sum('total');
            }

            // Tentukan dokumen yang perlu ditandatangani vs yang ditampilkan
            $documentsToSign = ['berita_acara']; // Pelaksana hanya sign berita acara

            $documentsToShow = [];
            if ($pengajuan->tipe_pengajuan === 'usulan') {
                $documentsToShow = ['berita_acara', 'tor', 'lampiran'];
            } else {
                $documentsToShow = ['berita_acara', 'lampiran'];
            }

            // --- PERUBAHAN DI SINI: Cek keberadaan dokumen pendukung ---
            $hasDokumenPendukung = !empty($pengajuan->dokumen_pendukung);

            // Data untuk view
            $viewData = [
                'status' => 'valid',
                'pengajuan' => $pengajuan,
                'verification' => $verification,
                'bagianPengusul' => $bagianPengusul,
                'bagianPelaksana' => $bagianPelaksana,
                'documentsToSign' => $documentsToSign,
                'documentsToShow' => $documentsToShow,
                'totalAnggaran' => $totalAnggaran,
                'encrypted_token' => $encrypted_id,
                'verification_level' => 'pelaksana',
                'expires_at' => $data['expires_at'],
                'has_dokumen_pendukung' => $hasDokumenPendukung, // <-- Kirim variabel ini ke view
            ];

            return view('PerencanaanBMN.Bagian.magic-link-verification', $viewData);
        } catch (\Exception $e) {
            \Log::error('Error pada magicLinkValidation Pelaksana: ' . $e->getMessage());
            return view('PerencanaanBMN.Bagian.magic-link-verification', [
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memvalidasi token',
                'pengajuan' => null,
                'documentsToSign' => [],
                'documentsToShow' => [],
                'totalAnggaran' => 0,
                'has_dokumen_pendukung' => false
            ]);
        }
    }
    private function getNikForSigning($pengajuan, $verificationLevel = 'pelaksana')
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
                    $idSatker = $pengajuan->id_bagian_pelaksana; // Default untuk pelaksana
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

    public function downloadTor($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            if ($pengajuan->tor_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->tor_signed_path);

                if (file_exists($filePath)) {
                    $fileName = 'TOR_Pengajuan_RKBMN_NonSBSK_' . $id . '_signed.pdf';
                    return response()->download($filePath, $fileName);
                }
            }

            // Fallback if signed TOR is not found or not applicable
            // This part is simplified from PengajuanController as Pelaksana usually deals with signed documents.
            // If you need full TOR generation logic here, copy from PengajuanController.
            return back()->with('error', 'Dokumen TOR belum tersedia atau belum ditandatangani.');

        } catch (\Exception $e) {
            \Log::error('Error pada downloadTor (Pelaksana): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadLampiran($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            if ($pengajuan->lampiran_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->lampiran_signed_path);

                if (file_exists($filePath)) {
                    $fileName = 'Lampiran_Pengajuan_NonSBSK_' . $id . '_signed.pdf';
                    return response()->download($filePath, $fileName);
                }
            }

            // Fallback if signed Lampiran is not found or not applicable
            // If you need full Lampiran generation logic here, copy from PengajuanController.
            return back()->with('error', 'Dokumen Lampiran belum tersedia atau belum ditandatangani.');

        } catch (\Exception $e) {
            \Log::error('Error pada downloadLampiran (Pelaksana): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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
            \Log::error('Error pada downloadDokumen (Pelaksana): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Format nomor WhatsApp
     */
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
}
