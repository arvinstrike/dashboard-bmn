<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel;
use Yajra\DataTables\DataTables;

class KoordinatorSBSKController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman dashboard untuk Koordinator SBSK.
     */
    public function index(Request $request)
    {
        $judul = 'Review Pengajuan RKBMN SBSK';

        if ($request->ajax()) {
            $data = PengajuanRKBMNBagianModel::whereIn('status', ["Diajukan ke Koordinator", "Disetujui oleh Koordinator", "Ditolak oleh Koordinator"])
                ->with('bagianPengusul');

            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('koordinator_sbsk.review', $row->id) . '" class="btn btn-info btn-sm">
                                <i class="fas fa-eye mr-1"></i>Review
                            </a>';
                })
                ->addColumn('bagian_pengusul', function ($row) {
                    return $row->bagianPengusul->uraianbagian ?? 'N/A';
                })
                ->addColumn('status_formatted', function ($row) {
                    // REVISI: Menambahkan logika untuk pewarnaan badge status dinamis
                    $status = $row->status;
                    $badgeClass = 'secondary'; // Warna default

                    if ($status === 'Diajukan ke Koordinator') {
                        $badgeClass = 'warning';
                    } elseif ($status === 'Disetujui oleh Koordinator') {
                        $badgeClass = 'success';
                    } elseif ($status === 'Ditolak oleh Koordinator') {
                        $badgeClass = 'danger';
                    }

                    return '<span class="badge badge-' . $badgeClass . ' badge-sm">' . $status . '</span>';
                })
                ->rawColumns(['action', 'status_formatted'])
                ->make(true);
        }

        // Ambil data pengajuan untuk tampilan non-ajax (client-side filtering)
        $pengajuan = PengajuanRKBMNBagianModel::whereIn('status', ["Diajukan ke Koordinator", "Disetujui oleh Koordinator", "Ditolak oleh Koordinator"])
            ->with('bagianPengusul')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PerencanaanBMN.Bagian.koordinator_sbsk.DashboardKoordinatorSBSK', compact('judul', 'pengajuan'));
    }


    /**
     * Menampilkan halaman review detail. (MODIFIED)
     */
    public function review($id)
    {
        try {
            $data = PengajuanRKBMNBagianModel::with([
                'biroPengusul',
                'bagianPengusul'
            ])->findOrFail($id);

            $detailData = $this->getDetailData($data);
            $barangInfo = $this->getBarangInfo($data->kode_barang);

            // **LOGIKA BARU: Cek apakah file final sudah di co-sign**
            $isCoSigned = false;
            if ($data->berita_acara_sbsk_signed_path) {
                // Ganti nama file operator menjadi nama file final
                $finalBaPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $data->berita_acara_sbsk_signed_path);
                if (Storage::disk('public')->exists($finalBaPath)) {
                    $isCoSigned = true;
                }
            }

            return view('PerencanaanBMN.Bagian.koordinator_sbsk.ReviewPageKoordinatorSBSK', compact(
                'data',
                'detailData',
                'barangInfo',
                'isCoSigned' // Kirim status co-sign ke view
            ));
        } catch (\Exception $e) {
            Log::error('Error in KoordinatorSBSKController@review: ' . $e->getMessage());
            return redirect()->route('koordinator_sbsk.index')->with('error', 'Gagal memuat halaman review.');
        }
    }

    /**
     * Memproses keputusan review (Disetujui/Ditolak). (MODIFIED)
     */
    public function updateReview(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui oleh Koordinator,Ditolak oleh Koordinator',
            'alasan_koordinator_bmn' => 'nullable|required_if:status,Ditolak oleh Koordinator|string|max:2000',
        ]);

        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);
            $newStatus = $request->input('status');

            if ($newStatus === 'Disetujui oleh Koordinator') {
                $finalBaPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $pengajuan->berita_acara_sbsk_signed_path ?? '');
                if (!Storage::disk('public')->exists($finalBaPath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Persetujuan gagal. Dokumen Berita Acara harus di-co-sign oleh Koordinator terlebih dahulu melalui Magic Link.'
                    ], 422);
                }
                // Kosongkan alasan penolakan jika sebelumnya pernah ditolak lalu disetujui
                $pengajuan->alasan_koordinator_bmn = null;
            } elseif ($newStatus === 'Ditolak oleh Koordinator') {
                // LOGIKA BARU UNTUK HAPUS FILE KETIKA DITOLAK
                $filePath = $pengajuan->berita_acara_sbsk_signed_path;

                // 1. Cek apakah path file ada dan hapus dari storage
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }

                // 2. Hapus juga file final jika ada
                $finalBaPath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $filePath ?? '');
                if ($finalBaPath && Storage::disk('public')->exists($finalBaPath)) {
                    Storage::disk('public')->delete($finalBaPath);
                }

                // 3. Kosongkan path di database dan isi alasan penolakan
                $pengajuan->berita_acara_sbsk_signed_path = null;
                $pengajuan->alasan_koordinator_bmn = $request->input('alasan_koordinator_bmn');
            }

            $pengajuan->status = $newStatus;
            $pengajuan->save();

            return response()->json([
                'success' => true,
                'message' => 'Status pengajuan berhasil diperbarui menjadi "' . $newStatus . '".'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in KoordinatorSBSKController@updateReview: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status.'], 500);
        }
    }

    /**
     * Send Magic Link Verification via WhatsApp for Koordinator SBSK
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMagicLink($id)
    {
        DB::beginTransaction();
        try {
            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\PengajuanRKBMNBagianModel::findOrFail($id);

            // Validasi status pengajuan - Hanya status ini yang diizinkan untuk Koordinator
            $allowedStatus = 'Diajukan ke Koordinator';
            if ($pengajuan->status !== $allowedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Magic Link hanya dapat dikirim untuk pengajuan dengan status "' . $allowedStatus . '"'
                ], 400);
            }

            // Ambil data Eselon III dari Bagian Administrasi BMN (kode bagian 669)
             $koordinator = DB::table('pegawai')
                 ->where('id_satker', 669) // ID Satker Bagian Administrasi BMN
                 ->where('eselon', 'III')
                 ->select('nama', 'nip', 'phone')
                 ->first();

//            $koordinator = (object) [
//                'nama' => 'Testing Nama',
//                'nip' => '123452341234',
//                'phone' => '081280974849' // Ganti dengan nomor statis untuk testing
//            ];

            if (!$koordinator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Koordinator BMN (Eselon III) tidak ditemukan. Pastikan data pegawai sudah terdaftar dengan benar.'
                ], 404);
            }

            if (empty($koordinator->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp Koordinator BMN belum terdaftar. Silakan hubungi administrator.'
                ], 400);
            }

            // 1. INSERT DATA AWAL (TANPA TOKEN)
            $expiresAt = now()->addHours(12);
            $verificationData = [
                'pengajuan_id'         => $id,
                'verification_level'   => 'sbsk_koordinator', // Level verifikasi untuk Koordinator
                'documents_to_sign'    => json_encode(['berita_acara']),
                'eselon_iii_nip'       => $koordinator->nip,
                'eselon_iii_name'      => $koordinator->nama,
                'eselon_iii_phone'     => $koordinator->phone,
                'triggered_by_user_id' => Auth::id(),
                'expires_at'           => $expiresAt,
                'status'               => 'pending'
            ];

            $verificationId = DB::table('magic_link_verifications')->insertGetId($verificationData);

            // Buat encrypted token setelah mendapatkan ID
            $tokenData = json_encode([
                'verification_id' => $verificationId,
                'pengajuan_id'    => $id,
                'expires_at'      => $expiresAt->timestamp
            ]);

            $compressedData = gzcompress($tokenData);
            $encryptedToken = Crypt::encryptString(base64_encode($compressedData));

            // 2. UPDATE BARIS DENGAN TOKEN
            // Ini akan mengisi kolom 'encrypted_token' dan 'updated_at' secara otomatis
            DB::table('magic_link_verifications')
                ->where('id', $verificationId)
                ->update([
                    'encrypted_token' => $encryptedToken,
                    'sent_at'         => now()
                ]);

            // Buat link verifikasi untuk Koordinator
            $verificationLink = url("pengajuanrkbmnbagian/magic-link-sbsk/{$encryptedToken}");

            // Ambil uraian bagian pengusul untuk template WhatsApp
            $bagianPengusul = DB::table('bagian')
                ->where('id', $pengajuan->id_bagian_pengusul)
                ->value('uraianbagian');

            // Kirim WhatsApp menggunakan template magic link
            $messageResult = $this->sendMagicLinkWhatsApp(
                $koordinator->phone,                     // phone
                $koordinator->nama,                      // namapenanggungjawab
                "SBSK-" . $id,                           // no_pengajuan
                "1 dokumen (Berita Acara SBSK)",         // jenis_dokumen
                $bagianPengusul,                         // bagian_pengusul
                $verificationLink                        // linkvalidasi
            );

            if ($messageResult === "Sukses") {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Magic Link verifikasi berhasil dikirim ke ' . $koordinator->nama . ' (Koordinator BMN) melalui WhatsApp.',
                    'verification_id' => $verificationId,
                    'expires_at' => $expiresAt->format('Y-m-d H:i:s')
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim Magic Link verifikasi via WhatsApp.'
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in KoordinatorSBSKController@sendMagicLink: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim Magic Link: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Download Berita Acara yang ditandatangani Eselon III Operator.
     */
    public function downloadOperatorBA($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);
            $filePath = $pengajuan->berita_acara_sbsk_signed_path;

            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                abort(404, 'File Berita Acara yang ditandatangani operator tidak ditemukan.');
            }

            return Storage::disk('public')->download($filePath, 'BA_Operator_Signed_' . $pengajuan->kode_jenis_pengajuan . '.pdf');
        } catch (\Exception $e) {
            abort(404, 'Gagal mengunduh file.');
        }
    }


    /**
     * Download Berita Acara final yang sudah di-cosign oleh Koordinator. (MODIFIED)
     */
    public function downloadFinalBA($id)
    {
        try {
            $pengajuan = PengajuanRKBMNBagianModel::findOrFail($id);
            $operatorPath = $pengajuan->berita_acara_sbsk_signed_path;

            if (!$operatorPath) {
                abort(404, 'Path Berita Acara Operator tidak ditemukan.');
            }

            // **LOGIKA BARU: Ganti nama file untuk mendapatkan path final**
            $filePath = str_replace('_operator_signed.pdf', '_final_signed.pdf', $operatorPath);

            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'File Berita Acara final yang telah di co-sign tidak ditemukan.');
            }

            return Storage::disk('public')->download($filePath, 'BA_Final_CoSigned_' . $pengajuan->kode_jenis_pengajuan . '.pdf');
        } catch (\Exception $e) {
            abort(404, 'Gagal mengunduh file.');
        }
    }


    // ================== HELPER METHODS ==================

    private function getDetailData(PengajuanRKBMNBagianModel $data)
    {
        $jenis = substr($data->kode_jenis_pengajuan, 0, 2);
        switch ($jenis) {
            case 'R1':
                return $data->bangunanKantor;
            case 'R3':
                return $data->rumahNegara;
            case 'R4':
                return $data->kendaraanJabatan;
            case 'R5':
                return $data->kendaraanOperasional;
            case 'R6':
                return $data->kendaraanFungsional;
            default:
                return null;
        }
    }

    private function getBarangInfo(?string $kodeBarang)
    {
        if (!$kodeBarang) return null;
        try {
            $barang = DB::table('t_brg')->where('kd_brg', $kodeBarang)->first();
            if (!$barang) return null;

            $golongan = DB::table('t_gol')->where('kd_gol', $barang->kd_gol)->first();
            $bidang = DB::table('t_bid')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->first();
            $kelompok = DB::table('t_kel')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->where('kd_kel', $barang->kd_kel)
                ->first();
            $subkelompok = DB::table('t_skel')
                ->where('kd_gol', $barang->kd_gol)
                ->where('kd_bid', $barang->kd_bid)
                ->where('kd_kel', $barang->kd_kel)
                ->where('kd_skel', $barang->kd_skel)
                ->first();

            return [
                'golongan' => $golongan ? $golongan->kd_gol . ' - ' . $golongan->ur_gol : '',
                'bidang' => $bidang ? $bidang->kd_bid . ' - ' . $bidang->ur_bid : '',
                'kelompok' => $kelompok ? $kelompok->kd_kel . ' - ' . $kelompok->ur_kel : '',
                'sub_kelompok' => $subkelompok ? $subkelompok->kd_skel . ' - ' . $subkelompok->ur_skel : '',
                'barang' => $barang->kd_brg . ' - ' . $barang->ur_sskel
            ];
        } catch (\Exception $e) {
            Log::error("Error getting barang info for {$kodeBarang}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send Magic Link WhatsApp message for SBSK - COMPLETE FIX
     */
    private function sendMagicLinkWhatsApp($phone, $namapenanggungjawab, $no_pengajuan, $jenis_dokumen, $bagian_pengusul, $linkvalidasi)
    {
        try {
            $kepada = $this->formatnomerwhatsapp($phone);
            $token_qontak = "mFETLoTDxuB_1dMcSrxr6UP2YOreTv7fBSvusbUij7U";

            $messageTemplateId = "478e52a2-09fd-4765-a37e-db2b10fd3cec";
            $channelIntegrationId = '81b411ae-b566-4ec5-bb7b-361b9f66131f';

            if (empty($token_qontak)) {
                Log::error('TOKEN_QONTAK tidak ditemukan di environment variable.');
                return "Error";
            }

            $payload = [
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
            ];

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
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $token_qontak",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                Log::error('cURL Error sending WhatsApp: ' . $error);
                return "Error";
            }

            $responseData = json_decode($response, true);

            // ===== INILAH BAGIAN YANG DIPERBAIKI =====
            // Mengecek apakah httpCode ada di rentang 2xx (sukses)
            if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['status']) && $responseData['status'] === 'success') {
                Log::info('WhatsApp message broadcast successfully created.', [
                    'http_code' => $httpCode,
                    'response' => $responseData
                ]);
                return "Sukses";
            } else {
                Log::error('WhatsApp API Error or non-success status:', [
                    'http_code' => $httpCode,
                    'response_body' => $responseData ?? $response
                ]);
                return "Error";
            }
        } catch (\Exception $e) {
            Log::error('Exception in sendMagicLinkWhatsApp: ' . $e->getMessage());
            return "Error";
        }
    }
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
