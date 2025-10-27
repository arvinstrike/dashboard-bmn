<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian\NonSBSK;

use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilPengajuan;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\DetilRevisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Auth;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Http; // Add this at the top for Guzzle HTTP client
use PDF;

class KoordinatorPengajuanController extends Controller
{
    public function indexKoordinator()
    {
        $tahunanggaran = session('tahunanggaran');

        // Koordinator hanya bisa melihat yang diajukan ke koordinator ke atas
        // Tidak termasuk: Draft, Diajukan ke Unit Pelaksana, Ditolak Pelaksana
        $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi', 'bagianPengusul', 'bagianPelaksana'])
            ->whereIn('status_pengajuan', [
                'Diajukan ke Koordinator',
                'Diajukan ke Unit Perencanaan dengan Rekomendasi',
                'Diajukan ke Unit Perencanaan',
                'Ditolak oleh Koordinator',
                'Disetujui',
                'Ditolak oleh Perencanaan'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $uniqueStatuses = $pengajuan->pluck('status_pengajuan')->unique()->sort();

        return view('PerencanaanBMN.Bagian.koordinator_nonsbsk.DashboardKoordinatorNonSBSK', compact('pengajuan', 'tahunanggaran', 'uniqueStatuses'));
    }

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
                        'deskripsi' => $perlengkapan ? $perlengkapan->deskripsi_perlengkapan : 'Pengajuan Pemeliharaan',
                        'kuantitas' => $item->kuantitas,
                        'harga' => $item->harga,
                        'total' => $item->total,
                        'path_image' => $item->path_image // Tambahkan path_image ke response
                    ];
                }
            }

            // Cek apakah ada dokumen pendukung
            $dokumenHTML = '-';
            if ($pengajuan->dokumen_pendukung) {
                $downloadURL = route('pengajuan.downloadDokumen', ['id' => $id]);
                $dokumenHTML = '<a href="' . $downloadURL . '" target="_blank">Download Dokumen</a>';
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
                'dokumen_html' => $dokumenHTML,
                'alasan_penolakan_pelaksana' => $pengajuan->alasan_penolakan_pelaksana,
                'alasan_penolakan_koordinator' => $pengajuan->alasan_penolakan_koordinator
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada show pengajuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi dan tanda tangani Berita Acara dengan e-sign (level Koordinator)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifikasiBeritaAcaraKoordinator(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'passphrase' => 'required|string',
            ]);

            DB::beginTransaction();

            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            \Log::info('Memulai verifikasi Berita Acara (Koordinator) untuk pengajuan ID: ' . $id);

            // PERUBAHAN: Verifikasi bahwa operator dan pelaksana sudah menandatangani berita acara
            // Cek berdasarkan timestamp, bukan path
            if (!$pengajuan->berita_acara_operator_signed_date) {
                throw new \Exception('Berita acara harus ditandatangani oleh Operator terlebih dahulu');
            }

            if (!$pengajuan->berita_acara_pelaksana_signed_date) {
                throw new \Exception('Berita acara harus ditandatangani oleh Pelaksana terlebih dahulu');
            }

            // Verifikasi bahwa koordinator belum menandatangani berita acara
            if ($pengajuan->berita_acara_koordinator_signed_date) {
                throw new \Exception('Berita acara sudah ditandatangani oleh Koordinator');
            }

            // PERUBAHAN: Baca file berita acara dari path tunggal
            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                throw new \Exception('File berita acara tidak ditemukan');
            }

            // Baca file PDF sebagai base64
            $pdfContent = file_get_contents($filePath);
            $pdfBase64 = base64_encode($pdfContent);
            \Log::info('PDF berita acara berhasil dikonversi ke base64');

            // Siapkan client HTTP dan URL API
            $client = new \GuzzleHttp\Client([
                'timeout' => 120,
                'connect_timeout' => 30,
                'verify' => false,
            ]);

            $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
            $username = config('app.esign_username', 'ApaKabahrul');
            $password = config('app.esign_password', 'ApaKabahrul');

            \Log::info('Mengirim request ke API e-sign: ' . $url);

                    //    $nik = '3201132412920003';
            // $nik = '3174062610940001'; // Mas Irfan

            $nik = $this->getNikForSigning($pengajuan, 'koordinator');
            \Log::info('Menggunakan NIK Koordinator: ' . $nik . ' untuk e-sign');

            // Buat QR Code untuk tanda tangan
            $qrContent = "Berita Acara RKBMN ID: " . $id . "\nKoordinator: " . Auth::user()->name . "\nTanggal: " . date('d M Y');
            $qrBuilder = Builder::create()
                ->data($qrContent)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(5)
                ->build();

            $qrBase64 = base64_encode($qrBuilder->getString());
            \Log::info('QR Code untuk Koordinator berhasil dibuat');

            // Siapkan data untuk API e-sign - posisikan di bagian tanda tangan Koordinator
            $requestData = [
                'nik' => $nik,
                'passphrase' => $request->passphrase,
                'signatureProperties' => [
                    [
                        'imageBase64' => $qrBase64,
                        'tampilan' => 'VISIBLE',
                        'page' => 2,
                        'originX' => 145.0,
                        'originY' => 425.0,
                        'width' => 75.0,
                        'height' => 75.0,
                        'location' => 'Jakarta',
                        'reason' => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Koordinator)'
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
                \Log::info('PDF yang ditandatangani oleh Koordinator berhasil di-decode');

                // Pastikan direktori ada
                $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_berita_acara_esign');
                if (!file_exists($dirPath)) {
                    if (!mkdir($dirPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori: ' . $dirPath);
                    }
                    \Log::info('Direktori dibuat: ' . $dirPath);
                }

                // PERUBAHAN: Simpan file dengan nama yang sama, update path tunggal
                $fileName = 'berita_acara_' . $id . '_final_signed.pdf';
                $signedPdfPath = $dirPath . '/' . $fileName;

                $bytesWritten = file_put_contents($signedPdfPath, $signedPdfData);
                if ($bytesWritten === false) {
                    throw new \Exception('Gagal menulis file PDF yang ditandatangani');
                }
                \Log::info('PDF yang ditandatangani oleh Koordinator disimpan di: ' . $signedPdfPath);

                // PERUBAHAN: Update path tunggal dan timestamp koordinator
                $pengajuan->berita_acara_signed_path = 'bmn_rkbmn_nonsbsk_berita_acara_esign/' . $fileName;
                $pengajuan->berita_acara_koordinator_signed_date = now();

                $pengajuan->save();
                \Log::info('Database diupdate dengan path file signed: ' . $pengajuan->berita_acara_signed_path);

                DB::commit();
                \Log::info('Transaksi database berhasil di-commit');

                // Buat URL download
                $downloadUrl = url('storage/' . $pengajuan->berita_acara_signed_path);

                return response()->json([
                    'success' => true,
                    'message' => 'Berita Acara berhasil diverifikasi dan ditandatangani oleh Koordinator',
                    'download_url' => $downloadUrl,
                    'status' => [
                        'operator_signed' => true,
                        'pelaksana_signed' => true,
                        'koordinator_signed' => true,
                        'pengajuan_disetujui' => false
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
            \Log::error('Verifikasi Berita Acara Koordinator Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function review($id)
    {
        $pengajuan = Pengajuan::find($id);
        return view('PerencanaanBMN.Bagian.koordinator_nonsbsk.ReviewPageKoordinatorNonSBSK', compact('pengajuan'));
    }

    // Ganti keseluruhan fungsi updateReview dengan yang ini
// Ganti KESELURUHAN fungsi updateReview di KoordinatorPengajuanController.php dengan versi ini

public function updateReview(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Terima,Ditolak',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $pengajuan = Pengajuan::findOrFail($id);
        $status = $request->status;
        $message = '';

        if ($status === 'Terima') {
            // Logika untuk persetujuan (tidak berubah)
            if (empty($pengajuan->berita_acara_koordinator_signed_date)) {
                throw new \Exception('Gagal menyimpan: Berita Acara harus diverifikasi (e-sign) terlebih dahulu.');
            }

            $hasSignedRecommendation = !empty($pengajuan->dokumen_rekomendasi_bmn) && !empty($pengajuan->rekomendasi_signed_date);

            if ($hasSignedRecommendation) {
                $pengajuan->status_pengajuan = 'Diajukan ke Unit Perencanaan dengan Rekomendasi';
                $message = 'Pengajuan telah disetujui dengan rekomendasi dan diajukan ke Unit Perencanaan.';
            } else {
                if (!empty($pengajuan->dokumen_rekomendasi_bmn) && empty($pengajuan->rekomendasi_signed_date)) {
                    throw new \Exception('Gagal menyimpan: Surat Rekomendasi sudah dibuat namun belum diverifikasi (e-sign).');
                }
                $pengajuan->status_pengajuan = 'Diajukan ke Unit Perencanaan';
                $message = 'Pengajuan telah disetujui dan diajukan ke Unit Perencanaan.';
            }

//            $pengajuan->alasan_penolakan_koordinator = null;

        } elseif ($status === 'Ditolak') {
            // [LOGIKA RESET DIMULAI DI SINI]

            // 1. Kumpulkan semua path file yang mungkin ada untuk dihapus dari storage
            $filesToDelete = [
                $pengajuan->berita_acara_signed_path,
                $pengajuan->tor_signed_path,
                $pengajuan->lampiran_signed_path,
                $pengajuan->dokumen_rekomendasi_bmn,
            ];

            // 2. Hapus file-file fisik dari storage
            foreach ($filesToDelete as $filePath) {
                if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                    Log::info('File dihapus karena pengajuan ditolak: ' . $filePath);
                }
            }

            // 3. Set status baru dan alasan penolakan
            $pengajuan->status_pengajuan = 'Ditolak oleh Koordinator';
            $pengajuan->alasan_penolakan_koordinator = $request->alasan_penolakan;
            $message = 'Pengajuan telah ditolak dan semua status TTD telah direset.';

            // 4. Reset semua field path dan tanggal di database menjadi null
            $pengajuan->berita_acara_signed_path = null;
            $pengajuan->berita_acara_operator_signed_date = null;
            $pengajuan->berita_acara_pelaksana_signed_date = null;
            $pengajuan->berita_acara_koordinator_signed_date = null;
            $pengajuan->berita_acara_perencanaan_signed_date = null;

            $pengajuan->tor_signed_path = null;
            $pengajuan->tor_signed_date = null;

            $pengajuan->lampiran_signed_path = null;
            $pengajuan->lampiran_signed_date = null;

            $pengajuan->dokumen_rekomendasi_bmn = null;
            $pengajuan->rekomendasi_signed_date = null;
        }

        $pengajuan->save();
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $message,
            'status_pengajuan' => $pengajuan->status_pengajuan
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error pada updateReview Koordinator: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// TAMBAHKAN FUNGSI BARU INI DI DALAM FILE KoordinatorPengajuanController.php

/**
 * Mengunduh dokumen surat rekomendasi yang sudah dibuat.
 *
 * @param int $id
 * @return \Illuminate\Http\Response
 */
    public function downloadRekomendasi($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            // Validasi: pastikan path dokumen ada di database
            if (empty($pengajuan->dokumen_rekomendasi_bmn)) {
                return back()->with('error', 'Dokumen rekomendasi tidak ditemukan pada pengajuan ini.');
            }

            $filePath = $pengajuan->dokumen_rekomendasi_bmn;

            // Validasi: pastikan file benar-benar ada di storage
            if (!Storage::disk('public')->exists($filePath)) {
                Log::warning('File rekomendasi tidak ditemukan di storage: ' . $filePath . ' untuk pengajuan ID: ' . $id);
                return back()->with('error', 'File fisik untuk dokumen rekomendasi tidak ditemukan. Harap hubungi administrator.');
            }

            // Siapkan nama file untuk di-download oleh pengguna
            $fileName = 'Surat_Rekomendasi_' . ($pengajuan->kode_pengajuan ?? $pengajuan->id) . '.pdf';

            // Return file untuk di-download menggunakan Storage facade
            return Storage::disk('public')->download($filePath, $fileName);

        } catch (\Exception $e) {
            Log::error('Error pada KoordinatorPengajuanController@downloadRekomendasi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencoba mengunduh dokumen.');
        }
    }

public function previewBeritaAcaraPelaksanaSigned($id)
{
    try {
        // Ambil data pengajuan
        $pengajuan = Pengajuan::findOrFail($id);

            // PERUBAHAN: Cek berdasarkan path tunggal
            if ($pengajuan->berita_acara_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);

                // Cek apakah file ada
                if (file_exists($filePath)) {
                    // Stream PDF
                    return response()->file($filePath);
                } else {
                    abort(404, 'File berita acara yang ditandatangani tidak ditemukan');
                }
            } else {
                // Fallback ke preview biasa jika belum ada file
                return redirect()->route('pengajuan.previewBeritaAcara', $id);
            }
        } catch (\Exception $e) {
            \Log::error('Error pada previewBeritaAcaraPelaksanaSigned: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Download Berita Acara yang sudah ditandatangani (final)
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadBeritaAcaraKoordinatorSigned($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = Pengajuan::findOrFail($id);

            // PERUBAHAN: Cek status tanda tangan berdasarkan timestamp
            if (!$pengajuan->berita_acara_koordinator_signed_date) {
                return back()->with('error', 'Berita Acara belum ditandatangani oleh Koordinator');
            }

            // PERUBAHAN: Gunakan path tunggal
            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);

            // Cek apakah file ada
            if (!file_exists($filePath)) {
                return back()->with('error', 'File Berita Acara tertandatangani tidak ditemukan');
            }

            // Download file
            $fileName = 'Berita_Acara_Final_' . $id . '.pdf';
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            \Log::error('Error pada downloadBeritaAcaraKoordinatorSigned: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Mengirim Magic Link Verifikasi via WhatsApp ke Koordinator
     */
    /**
     * Mengirim Magic Link Verifikasi via WhatsApp ke Koordinator
     */
    public function sendMagicLinkVerificationKoordinator(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrFail($id);

            // 1. Validasi Status Pengajuan (tetap sama)
            if ($pengajuan->status_pengajuan !== 'Diajukan ke Koordinator') {
                throw new \Exception('Pengajuan tidak dalam status yang valid untuk verifikasi.');
            }
            if (!$pengajuan->berita_acara_operator_signed_date || !$pengajuan->berita_acara_pelaksana_signed_date) {
                throw new \Exception('Berita Acara harus ditandatangani oleh Operator dan Pelaksana terlebih dahulu.');
            }

            // 2. Cari data Koordinator BMN (Eselon III, satker 669)
            $eselonIII = DB::table('pegawai')->where('id_satker', 669)->where('eselon', 'III')->select('nama', 'nip', 'phone')->first();
            if (!$eselonIII || !$eselonIII->phone) {
                throw new \Exception('Data Eselon III Koordinator BMN tidak ditemukan atau nomor WhatsApp tidak terdaftar.');
            }

            // $eselonIII = (object) [
            //     'nama' => 'Nama Eselon III',
            //     'nip' => '123456789012345678',
            //     'phone' => '082283372113' // Ganti dengan nomor statis untuk testing
            // ];

            // ================== PERUBAHAN DIMULAI DI SINI ==================

            // 3. Ambil daftar dokumen yang akan ditandatangani dari request
            // Default ke 'berita_acara' jika tidak ada data yang dikirim
            $documentsToSign = $request->input('documents_to_sign', ['berita_acara']);

            // Buat deskripsi dinamis untuk pesan WhatsApp
            $jenis_dokumen = "Berita Acara";
            if (in_array('surat_rekomendasi', $documentsToSign) && count($documentsToSign) > 1) {
                $jenis_dokumen = "Berita Acara dan Surat Rekomendasi";
            }

            // 4. Buat record magic link dengan menyertakan daftar dokumen
            $expiresAt = now()->addMinutes(720);
            $verificationData = [
                'pengajuan_id'       => $id,
                'verification_level' => 'koordinator',
                'documents_to_sign'  => json_encode($documentsToSign), // Simpan sebagai JSON
                'eselon_iii_nip'     => $eselonIII->nip,
                'eselon_iii_name'    => $eselonIII->nama,
                'eselon_iii_phone'   => $eselonIII->phone,
                'triggered_by_user_id' => Auth::id(),
                'expires_at'         => $expiresAt,
                'status'             => 'pending'
            ];
            $magicLinkVerification = DB::table('magic_link_verifications')->insertGetId($verificationData);

            // 5. Buat token terenkripsi (tetap sama)
            $tokenData = json_encode(['verification_id' => $magicLinkVerification, 'pengajuan_id' => $id, 'expires_at' => $expiresAt->timestamp]);
            $encryptedToken = \Illuminate\Support\Facades\Crypt::encryptString(base64_encode(gzcompress($tokenData)));

            DB::table('magic_link_verifications')->where('id', $magicLinkVerification)->update(['encrypted_token' => $encryptedToken, 'sent_at' => now()]);

            // 6. Kirim WhatsApp dengan deskripsi dokumen yang dinamis
            $verificationLink = url('/magic-link-validation/' . $encryptedToken);
            $messageResult = $this->sendMagicLinkWhatsApp(
                $eselonIII->phone,
                $eselonIII->nama,
                $pengajuan->kode_pengajuan ?? "KOOR-" . $id,
                $jenis_dokumen, // Gunakan deskripsi dinamis
                "Koordinator BMN",
                $verificationLink
            );

            // ================== AKHIR PERUBAHAN ==================

            if ($messageResult === "Sukses") {
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Magic Link verifikasi berhasil dikirim ke ' . $eselonIII->nama]);
            } else {
                throw new \Exception('Gagal mengirim Magic Link verifikasi via WhatsApp.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada sendMagicLinkVerificationKoordinator: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk mengirim WhatsApp (copy dari controller lain)
     */
    private function sendMagicLinkWhatsApp($phone, $namapenanggungjawab, $no_pengajuan, $jenis_dokumen, $bagian_pengusul, $linkvalidasi)
    {
        try {
            $kepada = $this->formatnomerwhatsapp($phone);
            $token_qontak = getenv("TOKEN_QONTAK");
            $messageTemplateId = "478e52a2-09fd-4765-a37e-db2b10fd3cec";
            $channelIntegrationId = '81b411ae-b566-4ec5-bb7b-361b9f66131f';

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
                CURLOPT_POSTFIELDS => json_encode([
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
                ]),
                CURLOPT_HTTPHEADER => ["Authorization: Bearer $token_qontak", "Content-Type: application/json"],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                \Log::error('Curl Error: ' . $err);
                return "Error";
            } else {
                $response_data = json_decode($response, true);
                return (isset($response_data['status']) && $response_data['status'] === 'success') ? "Sukses" : "Error";
            }
        } catch (\Exception $e) {
            \Log::error('Error sending WhatsApp: ' . $e->getMessage());
            return "Error";
        }
    }

    private function getNikForSigning($pengajuan, $verificationLevel = 'koordinator')
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
                    $idSatker = 669; // Default untuk koordinator BMN
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
     * Helper format nomor WhatsApp
     */
    private function formatnomerwhatsapp($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    // Dalam KoordinatorPengajuanController.php

    /**
     * Mengambil daftar item dari sebuah pengajuan REVISI untuk ditampilkan di modal rekomendasi.
     * Akun semula untuk semua item diambil dari kode_akun utama pengajuan.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Mengambil daftar item dari sebuah pengajuan (Usulan maupun Revisi)
     * untuk ditampilkan di modal rekomendasi.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
 * Mengambil daftar item dari sebuah pengajuan (Usulan maupun Revisi)
 * untuk ditampilkan di modal rekomendasi.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
    public function getPengajuanItemsForRecommendation($id)
    {
        try {
            // Eager load kedua relasi untuk efisiensi
            $pengajuan = Pengajuan::with(['detilPengajuan', 'detilRevisi'])->findOrFail($id);

            $itemsCollection = null;

            if ($pengajuan->tipe_pengajuan === 'usulan') {
                $itemsCollection = $pengajuan->detilPengajuan;
            } else {
                $itemsCollection = $pengajuan->detilRevisi;
            }

            if ($itemsCollection->isEmpty()) {
                return response()->json(['success' => true, 'items' => []]);
            }

            $akunSemula = $pengajuan->kode_akun;
            $isReguler = ($pengajuan->jenis_formulir === 'Pengajuan Reguler');

            $items = $itemsCollection->map(function ($item) use ($isReguler, $akunSemula) {

                $kodeDisplay = ''; // Variabel untuk kode barang/perlengkapan
                $deskripsiDisplay = 'Pengajuan Pemeliharaan'; // Default jika tidak ditemukan di referensi
                $keteranganBarang = $item->keterangan_barang ?: '-';

                if ($isReguler) {
                    $kodeDisplay = $item->kode_barang; // Ambil kode barang
                    $barangData = DB::table('t_brg')->where('kd_brg', $item->kode_barang)->first();
                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                    if ($barangData && !empty($barangData->ur_sskel)) {
                        $deskripsiDisplay = $barangData->ur_sskel;
                    }
                    // Jika tidak ditemukan atau kosong, tetap gunakan default "Pengajuan Pemeliharaan"
                } else {
                    $kodeDisplay = $item->kode_perlengkapan; // Ambil kode perlengkapan
                    $perlengkapan = DB::table('bmn_ref_perlengkapan_nonsbsk')
                        ->where('kode_perlengkapan', $item->kode_perlengkapan)
                        ->first();
                    // Jika ditemukan di referensi dan tidak kosong, gunakan deskripsi dari referensi
                    if ($perlengkapan && !empty($perlengkapan->deskripsi_perlengkapan)) {
                        $deskripsiDisplay = $perlengkapan->deskripsi_perlengkapan;
                    }
                    // Jika tidak ditemukan atau kosong, tetap gunakan default "Pengajuan Pemeliharaan"
                }

                return [
                    'id' => $item->id,
                    'kode_barang' => $kodeDisplay,
                    'deskripsi' => $deskripsiDisplay,
                    'keterangan' => $keteranganBarang, // Ubah key dari keterangan_barang ke keterangan untuk match dengan JS
                    'akun_semula' => $akunSemula
                ];
            });

            return response()->json([
                'success' => true,
                'items' => $items
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getPengajuanItemsForRecommendation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data item.'
            ], 500);
        }
    }

    public function generateRecommendationLetter(Request $request, $id)
    {
        // 1. Validasi input dari request - perbaiki validasi
        $validatedData = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.kode_barang' => 'required|string',
            'items.*.deskripsi' => 'nullable|string',
            'items.*.keterangan_barang' => 'nullable|string',
            'items.*.akun_semula' => 'required|string',
            'items.*.akun_menjadi' => 'required|string',
            'items.*.keterangan_rekomendasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pengajuan = Pengajuan::findOrFail($id);

            // 2. PERBAIKAN: Hapus file rekomendasi lama dengan lebih thorough
            if ($pengajuan->dokumen_rekomendasi_bmn) {
                \Log::info('Mencoba menghapus file lama: ' . $pengajuan->dokumen_rekomendasi_bmn);

                // Cek apakah file ada dan hapus
                if (Storage::disk('public')->exists($pengajuan->dokumen_rekomendasi_bmn)) {
                    $deleted = Storage::disk('public')->delete($pengajuan->dokumen_rekomendasi_bmn);
                    \Log::info('File lama dihapus: ' . ($deleted ? 'BERHASIL' : 'GAGAL'));
                } else {
                    \Log::warning('File lama tidak ditemukan di storage: ' . $pengajuan->dokumen_rekomendasi_bmn);
                }

                // Hapus juga file dengan pattern nama yang mungkin berbeda
                $dirPath = 'bmn_rkbmn_nonsbsk_rekomendasi';
                $files = Storage::disk('public')->files($dirPath);
                foreach ($files as $file) {
                    if (strpos($file, 'rekomendasi_bmn_' . $id . '_') !== false) {
                        Storage::disk('public')->delete($file);
                        \Log::info('Menghapus file dengan pattern: ' . $file);
                    }
                }
            }

            // 3. Siapkan data untuk template PDF
            $processedItems = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'kode_barang' => $item['kode_barang'],
                    'deskripsi' => $item['deskripsi'] ?? 'Pengajuan Pemeliharaan',
                    'keterangan_barang' => $item['keterangan_barang'] ?? '-',
                    'akun_semula' => $item['akun_semula'],
                    'akun_menjadi' => $item['akun_menjadi'],
                    'keterangan_rekomendasi' => $item['keterangan_rekomendasi'] ?? '-'
                ];
            }, $validatedData['items']);

            $dataForPdf = [
                'pengajuan' => $pengajuan,
                'bagianPengusul' => DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first(),
                'items' => $processedItems
            ];

            // 4. Render Blade view ke dalam variabel PDF
            $pdf = PDF::loadView('PerencanaanBMN.Bagian.pdf.RekomendasiPerubahanBMN', $dataForPdf);

            // 5. PERBAIKAN: Gunakan nama file yang konsisten (tanpa timestamp)
            $fileName = 'rekomendasi_bmn_' . $id . '.pdf';
            $filePath = 'bmn_rkbmn_nonsbsk_rekomendasi/' . $fileName;

            // Pastikan direktori ada
            $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_rekomendasi');
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0755, true);
                \Log::info('Direktori dibuat: ' . $dirPath);
            }

            // Simpan file PDF
            $saved = Storage::disk('public')->put($filePath, $pdf->output());
            \Log::info('File baru disimpan: ' . ($saved ? 'BERHASIL' : 'GAGAL') . ' di path: ' . $filePath);

            // 6. Update kolom di database dengan path file
            $pengajuan->dokumen_rekomendasi_bmn = $filePath;
            $pengajuan->save();

            DB::commit();

            // 7. Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Dokumen rekomendasi berhasil dibuat/diperbarui.',
                'file_path' => $filePath,
                'preview_url' => route('koordinator.preview_rekomendasi', $id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat PDF Rekomendasi: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat dokumen rekomendasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewRekomendasi($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek apakah path dokumen ada dan file-nya benar-benar ada di storage
            if (!$pengajuan->dokumen_rekomendasi_bmn || !Storage::disk('public')->exists($pengajuan->dokumen_rekomendasi_bmn)) {
                abort(404, 'Dokumen rekomendasi tidak ditemukan.');
            }

            // Ambil path lengkap ke file
            $filePath = Storage::disk('public')->path($pengajuan->dokumen_rekomendasi_bmn);

            // Kembalikan file sebagai response untuk ditampilkan inline di browser
            return response()->file($filePath);

        } catch (\Exception $e) {
            \Log::error('Error pada previewRekomendasi: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat menampilkan dokumen.');
        }
    }
}
