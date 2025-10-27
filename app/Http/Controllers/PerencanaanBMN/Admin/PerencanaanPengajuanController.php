<?php

namespace App\Http\Controllers\PerencanaanBMN\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan;
use App\Models\ReferensiUnit\BagianModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;

class PerencanaanPengajuanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function indexPerencanaan()
    {
        $tahunanggaran = session('tahunanggaran');
        $idbagian = Auth::user()->idbagian;

        // Ambil uraianbagian dari model Bagian
        $deskripsi = BagianModel::where('id', $idbagian)
            ->value('uraianbagian');

        // Perencanaan hanya bisa lihat yang diajukan ke perencanaan, disetujui, dan ditolak perencanaan
        $pengajuan = Pengajuan::whereIn('status_pengajuan', [
            'Diajukan ke Unit Perencanaan dengan Rekomendasi',
            'Diajukan ke Unit Perencanaan',
            'Disetujui',
            'Ditolak oleh Perencanaan'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        // PENAMBAHAN: Pisahkan pengajuan berdasarkan status magic link dan e-sign
        $pengajuanMenungguEsignKepala = $pengajuan->filter(function ($p) {
            // Cek apakah ada magic link yang masih aktif dan belum ditandatangani
            $magicLink = DB::table('magic_link_verifications')
                ->where('pengajuan_id', $p->id)
                ->where('expires_at', '>', now())
                ->whereNull('verified_at')
                ->first();

            return $magicLink !== null;
        });

        $pengajuanBelumSign = $pengajuan->filter(function ($p) {
            // Filter berdasarkan status yang diizinkan untuk tabel 2
            $allowedStatuses = [
                'Diajukan ke Unit Perencanaan dengan Rekomendasi',
                'Diajukan ke Unit Perencanaan',
                'Ditolak oleh Perencanaan'
            ];

            $hasValidStatus = in_array($p->status_pengajuan, $allowedStatuses);

            // Cek apakah TIDAK ada magic link aktif (kebalikan dari tabel 1)
            $hasPendingMagicLink = DB::table('magic_link_verifications')
                ->where('pengajuan_id', $p->id)
                ->where('expires_at', '>', now())
                ->whereNull('verified_at')
                ->exists();

            return $hasValidStatus && !$hasPendingMagicLink;
        });

        $pengajuanSudahSign = $pengajuan->filter(function ($p) {
            return !is_null($p->berita_acara_perencanaan_signed_date);
        });

        // Unique statuses untuk masing-masing tabel
        $uniqueStatusesMenungguEsign = $pengajuanMenungguEsignKepala->pluck('status_pengajuan')->unique()->sort();
        $uniqueStatusesBelumSign = $pengajuanBelumSign->pluck('status_pengajuan')->unique()->sort();
        $uniqueStatusesSudahSign = $pengajuanSudahSign->pluck('status_pengajuan')->unique()->sort();

        return view('PerencanaanBMN.Admin.DashboardPagePerencanaan', compact(
            'pengajuan',
            'tahunanggaran',
            'deskripsi',
            'pengajuanMenungguEsignKepala',
            'pengajuanBelumSign',
            'pengajuanSudahSign',
            'uniqueStatusesMenungguEsign',
            'uniqueStatusesBelumSign',
            'uniqueStatusesSudahSign'
        ));
    }

    public function review($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        return view('PerencanaanBMN.Admin.review.ReviewPagePerencanaan', compact('pengajuan'));
    }

    public function updateReview(Request $request, $id)
    {
        // PERUBAHAN: Validasi input yang lebih detail
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Terima,Ditolak',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string',
        ], [
            'alasan_penolakan.required_if' => 'Alasan penolakan harus diisi jika status Ditolak.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            if (!in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Perencanaan', 'Diajukan ke Unit Perencanaan dengan Rekomendasi'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak dalam status yang valid untuk direview oleh Perencanaan.'
                ], 400);
            }

            if ($request->status === 'Ditolak') {
                // --- PROSES PENOLAKAN ---
                $pengajuan->status_pengajuan = 'Ditolak oleh Perencanaan';
                $pengajuan->alasan_penolakan_perencanaan = $request->input('alasan_penolakan');

                // PERUBAHAN: Hapus file berita acara yang sudah ditandatangani dari storage
                if (!empty($pengajuan->berita_acara_signed_path)) {
                    // Gunakan Storage facade untuk keamanan dan konsistensi
                    Storage::disk('public')->delete($pengajuan->berita_acara_signed_path);
                    Log::info('File berita acara dihapus dari storage: ' . $pengajuan->berita_acara_signed_path);
                }

                // PERUBAHAN: Reset semua field berita acara di database
                $pengajuan->berita_acara_signed_path = null;
                $pengajuan->berita_acara_operator_signed_date = null;
                $pengajuan->berita_acara_pelaksana_signed_date = null;
                $pengajuan->berita_acara_koordinator_signed_date = null;
                $pengajuan->berita_acara_perencanaan_signed_date = null; // Reset ttd perencanaan juga

                // PERUBAHAN: Hapus file dokumen lainnya (TOR, Lampiran, Pendukung)
                $documentPaths = [
                    $pengajuan->tor_signed_path,
                    $pengajuan->lampiran_signed_path,
                    $pengajuan->dokumen_pendukung,
                ];
                foreach ($documentPaths as $path) {
                    if (!empty($path)) {
                        Storage::disk('public')->delete($path);
                        Log::info('File dokumen dihapus dari storage: ' . $path);
                    }
                }

                // PERUBAHAN: Reset semua field dokumen di database
                $pengajuan->tor_signed_path = null;
                $pengajuan->tor_signed_date = null;
                $pengajuan->lampiran_signed_path = null;
                $pengajuan->lampiran_signed_date = null;
                $pengajuan->dokumen_pendukung = null;

                $message = 'Pengajuan telah ditolak. Semua dokumen dan Berita Acara telah direset.';

            } else { // status === 'Terima'
                // --- PROSES PENERIMAAN ---
                // Anda mungkin perlu menambahkan validasi di sini jika persetujuan
                // memerlukan tanda tangan dari Perencanaan terlebih dahulu
                // if (!$pengajuan->berita_acara_perencanaan_signed_date) {
                //     return response()->json(['success' => false, 'message' => 'Anda harus menandatangani Berita Acara terlebih dahulu'], 400);
                // }

                $pengajuan->status_pengajuan = 'Disetujui'; // Atau status selanjutnya yang sesuai
                // $pengajuan->alasan_penolakan_perencanaan = null;
                $message = 'Review akun telah berhasil disimpan dan pengajuan disetujui.';
            }

            $pengajuan->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada PerencanaanReviewController@updateReview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server saat memproses review.'
            ], 500);
        }
    }

    /**
     * Mengirim Magic Link untuk verifikasi Berita Acara oleh Perencanaan.
     */
    public function sendBeritaAcaraMagicLink(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            if (!in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Perencanaan', 'Diajukan ke Unit Perencanaan dengan Rekomendasi'])) {
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak dalam status yang valid untuk verifikasi.'], 400);
            }
            if (!$pengajuan->berita_acara_koordinator_signed_date) {
                return response()->json(['success' => false, 'message' => 'Berita Acara harus ditandatangani oleh Koordinator terlebih dahulu.'], 400);
            }

            $eselonIII = DB::table('pegawai')->where('id_satker', 657)->where('eselon', 'III')->select('nama', 'nip', 'phone')->first();
            if (!$eselonIII || !$eselonIII->phone) {
                throw new \Exception('Data Eselon III Perencanaan (satker 657) tidak ditemukan atau nomor WhatsApp tidak terdaftar.');
            }
//            $eselonIII = (object) [
//                'nama' => 'Nama Eselon III',
//                'nip' => '123456789012345678',
//                'phone' => '123456' // Ganti dengan nomor statis untuk testing
//            ];

            $expiresAt = now()->addMinutes(720);
            $verificationData = [
                'pengajuan_id' => $id,
                // PASTIKAN NILAI INI TEPAT 'perencanaan'
                'verification_level' => 'perencanaan',
                'documents_to_sign' => json_encode(['berita_acara']),
                'eselon_iii_nip' => $eselonIII->nip,
                'eselon_iii_name' => $eselonIII->nama,
                'eselon_iii_phone' => $eselonIII->phone,
                'triggered_by_user_id' => Auth::id(),
                'expires_at' => $expiresAt,
                'status' => 'pending'
            ];
            $magicLinkVerification = DB::table('magic_link_verifications')->insertGetId($verificationData);

            $tokenData = json_encode(['verification_id' => $magicLinkVerification, 'pengajuan_id' => $id, 'expires_at' => $expiresAt->timestamp]);
            $encryptedToken = \Illuminate\Support\Facades\Crypt::encryptString(base64_encode(gzcompress($tokenData)));
            DB::table('magic_link_verifications')->where('id', $magicLinkVerification)->update(['encrypted_token' => $encryptedToken, 'sent_at' => now()]);

            $verificationLink = url('/magic-link-validation/' . $encryptedToken);
            $messageResult = $this->sendMagicLinkWhatsApp(
                $eselonIII->phone,
                $eselonIII->nama,
                $pengajuan->kode_pengajuan ?? $pengajuan->id,
                "Berita Acara Pengajuan",
                "Unit Perencanaan",
                $verificationLink
            );

            if ($messageResult !== "Sukses") {
                throw new \Exception('Gagal mengirim Magic Link via WhatsApp.');
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Magic Link verifikasi Berita Acara berhasil dikirim ke ' . $eselonIII->nama]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error Send BA Magic Link Perencanaan: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk mengirim WhatsApp
     */
    private function sendMagicLinkWhatsApp($phone, $namapenanggungjawab, $no_pengajuan, $jenis_dokumen, $bagian_pengusul, $linkvalidasi)
    {
        try {
            $kepada = $this->formatnomerwhatsapp($phone);
            $token_qontak = getenv("TOKEN_QONTAK");

            // Pastikan ID Template dan Channel Integration sudah benar
            $messageTemplateId = "478e52a2-09fd-4765-a37e-db2b10fd3cec";
            $channelIntegrationId = '81b411ae-b566-4ec5-bb7b-361b9f66131f';

            if (empty($token_qontak)) {
                Log::error('TOKEN_QONTAK tidak ditemukan di environment variable.');
                return "Error: Token Qontak tidak ada";
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
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $token_qontak",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error('Qontak Curl Error: ' . $err);
                return "Error";
            } else {
                $response_data = json_decode($response, true);
                if (isset($response_data['status']) && $response_data['status'] === 'success') {
                    return "Sukses";
                } else {
                    Log::error('Qontak API Error: ' . $response);
                    return "Error";
                }
            }
        } catch (\Exception $e) {
            Log::error('Error pada sendMagicLinkWhatsApp: ' . $e->getMessage());
            return "Error";
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

    /**
     * Download Berita Acara yang sudah ditandatangani final.
     */
    public function downloadBeritaAcaraSigned($id)
    {
        try {
            $pengajuan = Pengajuan::findOrFail($id);

            // Cek apakah Perencanaan sudah tanda tangan
            if (!$pengajuan->berita_acara_perencanaan_signed_date) {
                return back()->with('error', 'Berita Acara belum ditandatangani oleh Unit Perencanaan.');
            }

            $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
            if (!file_exists($filePath)) {
                return back()->with('error', 'File Berita Acara tertandatangani tidak ditemukan.');
            }

            $fileName = 'Berita_Acara_Final_' . $id . '.pdf';
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            Log::error('Error pada downloadBeritaAcaraSigned Perencanaan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
     * Verifikasi E-Sign langsung dari dashboard (bukan magic link)
     * Hanya untuk kepala bagian perencanaan (eselon III, satker 657)
     * Unit Perencanaan hanya menandatangani BERITA ACARA saja
     */
    public function verifikasiEsign(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'passphrase' => 'required|string',
            'documents' => 'required|array',
            'documents.*' => 'in:berita_acara' // Hanya berita acara yang boleh ditandatangani oleh perencanaan
        ]);

        DB::beginTransaction();
        try {
            // 1. Validasi user adalah kepala bagian perencanaan
            // UNTUK TESTING: comment baris query di bawah, uncomment baris testing
            $isKepalaBagianPerencanaan = DB::table('pegawai')
                ->where('nama', Auth::user()->nama)
                ->where('eselon', 'III')
                ->where('id_satker', 657)
                ->exists();

            // UNTUK TESTING - UNCOMMENT BARIS DI BAWAH:
            // $isKepalaBagianPerencanaan = true;

            if (!$isKepalaBagianPerencanaan) {
                throw new \Exception('Akses ditolak. Hanya kepala bagian perencanaan yang dapat melakukan verifikasi.');
            }

            // 2. Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            // 3. Validasi status pengajuan
            if (!in_array($pengajuan->status_pengajuan, ['Diajukan ke Unit Perencanaan', 'Diajukan ke Unit Perencanaan dengan Rekomendasi'])) {
                throw new \Exception('Pengajuan tidak dalam status yang valid untuk verifikasi perencanaan.');
            }

            // 4. Validasi bahwa koordinator sudah menandatangani berita acara
            if (!$pengajuan->berita_acara_koordinator_signed_date) {
                throw new \Exception('Berita Acara harus ditandatangani oleh Koordinator terlebih dahulu.');
            }

            // 5. Buat object verification sementara untuk kompatibilitas dengan method magic link
            $verification = (object)[
                'pengajuan_id' => $id,
                'verification_level' => 'perencanaan',
                'documents_to_sign' => json_encode(['berita_acara']), // Hanya berita acara
                'eselon_iii_name' => Auth::user()->nama,
                'eselon_iii_nip' => $this->getNipKepalaBagianPerencanaan(),
                'status' => 'pending'
            ];

            // 6. Proses setiap dokumen yang diminta (validasi hanya berita acara)
            $requestedDocuments = $request->input('documents');

            // Validasi tambahan: pastikan hanya berita acara
            $invalidDocuments = array_diff($requestedDocuments, ['berita_acara']);
            if (!empty($invalidDocuments)) {
                throw new \Exception('Unit Perencanaan hanya dapat menandatangani berita acara. Dokumen tidak valid: ' . implode(', ', $invalidDocuments));
            }

            $signedDocuments = [];
            $signErrors = [];

            foreach ($requestedDocuments as $documentType) {
                try {
                    \Log::info('Memproses dokumen verifikasi langsung: ' . $documentType);

                    // Panggil helper untuk menyiapkan data dan e-sign (reuse dari magic link)
                    $esignResult = $this->_processSingleDocumentEsignPerencanaan($pengajuan, $documentType, $request->passphrase, $verification);

                    // Update model pengajuan berdasarkan hasil
                    $pengajuan->{$esignResult['path_column']} = $esignResult['signed_path'];
                    $pengajuan->{$esignResult['date_column']} = now();

                    $signedDocuments[] = $documentType;
                    \Log::info('Dokumen berhasil ditandatangani via verifikasi langsung: ' . $documentType);
                } catch (\Exception $e) {
                    \Log::error('Error signing ' . $documentType . ' via verifikasi langsung: ' . $e->getMessage());
                    $signErrors[$documentType] = $e->getMessage();
                }
            }

            // 7. Simpan perubahan jika ada dokumen yang berhasil ditandatangani
            if (!empty($signedDocuments)) {
                $pengajuan->save();

                // Update status pengajuan setelah berita acara ditandatangani oleh perencanaan
                if (in_array('berita_acara', $signedDocuments)) {
                    $pengajuan->status_pengajuan = 'Disetujui';
                    $pengajuan->save();

                    \Log::info('Status pengajuan diupdate ke: Disetujui');
                }

                // PERBAIKAN: Update magic link verification yang aktif untuk pengajuan ini
                $activeMagicLinks = DB::table('magic_link_verifications')
                    ->where('pengajuan_id', $id)
                    ->where('verification_level', 'perencanaan')
                    ->whereIn('status', ['pending', 'sent'])
                    ->get();

                foreach ($activeMagicLinks as $magicLink) {
                    DB::table('magic_link_verifications')
                        ->where('id', $magicLink->id)
                        ->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'verification_result' => json_encode([
                                'signed_documents' => $signedDocuments,
                                'signed_via' => 'dashboard_verification',
                                'signed_by' => Auth::user()->nama,
                                'signed_at' => now()->toISOString()
                            ])
                        ]);

                    \Log::info('Magic link verification diupdate ke verified: ' . $magicLink->id);
                }
            }

            DB::commit();

            return response()->json([
                'success' => !empty($signedDocuments),
                'message' => !empty($signedDocuments)
                    ? 'Verifikasi berhasil! Berita acara telah ditandatangani Unit Perencanaan.'
                    : 'Verifikasi gagal: ' . json_encode($signErrors),
                'signed_documents' => $signedDocuments,
                'errors' => $signErrors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error pada verifikasiEsign: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk mendapatkan NIP kepala bagian perencanaan
     */
    private function getNipKepalaBagianPerencanaan()
    {
        // UNTUK TESTING: comment baris query di bawah, uncomment baris testing
        $nip = DB::table('pegawai')
            ->where('nama', Auth::user()->nama)
            ->where('eselon', 'III')
            ->where('id_satker', 657)
            ->value('nip');

        // UNTUK TESTING - UNCOMMENT BARIS DI BAWAH:
        // $nip = '123456789012345678';

        return $nip ?: '000000000000000000'; // fallback NIP
    }

    /**
     * Helper method untuk mendapatkan NIK untuk signing (khusus perencanaan)
     */
    private function getNikForSigningPerencanaan($pengajuan, $verificationLevel)
    {
        // UNTUK TESTING: comment baris query di bawah, uncomment baris testing
        $nik = DB::table('pegawai')
            ->where('nama', Auth::user()->nama)
            ->where('eselon', 'III')
            ->where('id_satker', 657)
            ->value('nik');

        // UNTUK TESTING - UNCOMMENT BARIS DI BAWAH:
        // $nik = '3201132412920003';

        return $nik ?: '3201132412920003'; // fallback NIK untuk testing
    }

    /**
     * Preview Berita Acara untuk perencanaan
     */
    public function previewBeritaAcara($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            // PRIORITAS: Ambil file signed jika ada
            if ($pengajuan->berita_acara_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);

                if (file_exists($filePath)) {
                    return response()->file($filePath);
                }
            }

            // FALLBACK: Generate PDF baru jika file signed tidak ada
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.BeritaAcara', $this->getBeritaAcaraData($pengajuan));
            return $pdf->stream('preview_berita_acara.pdf');
        } catch (\Exception $e) {
            \Log::error('Error preview berita acara: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat preview berita acara');
        }
    }

    /**
     * Preview TOR untuk perencanaan
     */
    public function previewTor($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            // PRIORITAS: Ambil file signed jika ada
            if ($pengajuan->tor_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->tor_signed_path);

                if (file_exists($filePath)) {
                    return response()->file($filePath);
                }
            }

            // FALLBACK: Generate PDF baru jika file signed tidak ada
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.TorUsulan', $this->getTorData($pengajuan));
            return $pdf->stream('preview_tor.pdf');
        } catch (\Exception $e) {
            \Log::error('Error preview TOR: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat preview TOR');
        }
    }

    /**
     * Preview Lampiran untuk perencanaan
     */
    public function previewLampiran($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            // PRIORITAS: Ambil file signed jika ada
            if ($pengajuan->lampiran_signed_path) {
                $filePath = storage_path('app/public/' . $pengajuan->lampiran_signed_path);

                if (file_exists($filePath)) {
                    return response()->file($filePath);
                }
            }

            // FALLBACK: Generate PDF baru jika file signed tidak ada
            $pdf = \PDF::loadView('PerencanaanBMN.Bagian.pdf.LampiranUsulan_NonSBSK', $this->getLampiranData($pengajuan));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('preview_lampiran.pdf');
        } catch (\Exception $e) {
            \Log::error('Error preview lampiran: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat preview lampiran');
        }
    }

    /**
     * Preview Dokumen Pendukung untuk perencanaan
     */
    public function previewDokumenPendukung($id)
    {
        try {
            // Ambil data pengajuan
            $pengajuan = \App\Models\PerencanaanBMN\Bagian\NonSBSK\Pengajuan::findOrFail($id);

            // Check apakah ada dokumen pendukung
            if (!$pengajuan->dokumen_pendukung) {
                abort(404, 'Tidak ada dokumen pendukung untuk pengajuan ini');
            }

            // Path file dokumen pendukung
            $filePath = storage_path('app/public/' . $pengajuan->dokumen_pendukung);

            // Check apakah file exists
            if (!file_exists($filePath)) {
                abort(404, 'File dokumen pendukung tidak ditemukan');
            }

            // Return file untuk ditampilkan di browser
            return response()->file($filePath);
        } catch (\Exception $e) {
            \Log::error('Error preview dokumen pendukung: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat preview dokumen pendukung');
        }
    }

    /**
     * Helper method untuk mendapatkan data berita acara
     */
    private function getBeritaAcaraData($pengajuan)
    {
        // Reuse logic dari method yang sudah ada di PengajuanRegulerController
        $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
        $biroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->first();
        $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
        $biroPelaksana = DB::table('biro')->where('id', $pengajuan->id_biro_pelaksana)->first();

        $jumlahPengadaan = ($pengajuan->tipe_pengajuan == 'usulan') ?
            $pengajuan->detilPengajuan()->sum('kuantitas') : $pengajuan->detilRevisi()->sum('kuantitas');

        $tanggal = date('d');
        $bulan = date('F'); // Simplified
        $tahunKata = date('Y'); // Simplified

        $pengusulData = DB::table('pegawai')->where('id_satker', $pengajuan->id_bagian_pengusul)->where('eselon', 'III')->select('nama', 'nip')->first();
        $pelaksanaData = DB::table('pegawai')->where('id_satker', $pengajuan->id_bagian_pelaksana)->where('eselon', 'III')->select('nama', 'nip')->first();
        $koordinatorData = DB::table('pegawai')->where('id_satker', 669)->where('eselon', 'III')->select('nama', 'nip')->first();
        $perencanaanData = DB::table('pegawai')->where('id_satker', 657)->where('eselon', 'III')->select('nama', 'nip')->first();

        $formatTitleCase = function ($text) {
            if (empty($text)) return '';
            return ucwords(strtolower($text));
        };

        return [
            'uraianBagianPengusul' => $formatTitleCase(optional($bagianPengusul)->uraianbagian ?? 'Bagian'),
            'uraianBiroPengusul' => $formatTitleCase(optional($biroPengusul)->uraianbiro ?? 'Biro'),
            'uraianBagianPelaksana' => $formatTitleCase(optional($bagianPelaksana)->uraianbagian ?? 'Bagian'),
            'uraianBiroPelaksana' => $formatTitleCase(optional($biroPelaksana)->uraianbiro ?? 'Biro'),
            'tahunAnggaran' => $pengajuan->tahun_anggaran,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahunKata' => $tahunKata,
            'jumlahPengadaan' => $jumlahPengadaan,
            'jumlahPemeliharaan' => 0,
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
    }

    /**
     * Helper method untuk mendapatkan data TOR
     */
    private function getTorData($pengajuan)
    {
        $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('j F Y');
        $namaPenanggungJawabPelaksana = DB::table('pegawai')
            ->where('id_satker', $pengajuan->id_bagian_pengusul)
            ->where('eselon', 'III')
            ->value('nama') ?: 'Kepala Bagian';

        $uraianBagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->value('uraianbagian');
        $uraianBiroPengusul = DB::table('biro')->where('id', $pengajuan->id_biro_pengusul)->value('uraianbiro');

        return [
            'uraianBagianPengusul' => ucwords(strtolower($uraianBagianPengusul)),
            'uraianBiroPengusul' => ucwords(strtolower($uraianBiroPengusul)),
            'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
            'tahunAnggaranPersetujuan' => date('Y'),
            'tanggalPengajuan' => $tanggalPengajuan,
            'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana,
            'kodePengajuan' => $pengajuan->kode_pengajuan,
            'keterangan' => $pengajuan->keterangan,
        ];
    }

    /**
     * Helper method untuk mendapatkan data lampiran
     */
    private function getLampiranData($pengajuan)
    {
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
                    'kuantitas' => $item->kuantitas,
                    'harga' => $item->harga,
                    'total' => $item->total
                ];
            }
        }

        $bagianPengusul = DB::table('bagian')->where('id', $pengajuan->id_bagian_pengusul)->first();
        $bagianPelaksana = DB::table('bagian')->where('id', $pengajuan->id_bagian_pelaksana)->first();
        $tanggalPengajuan = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $namaPenanggungJawabPelaksana = DB::table('pegawai')
            ->where('id_satker', $pengajuan->id_bagian_pengusul)
            ->where('eselon', 'III')
            ->value('nama') ?: 'Kepala Bagian ' . optional($bagianPelaksana)->uraianbagian;

        return [
            'pengajuan' => $pengajuan,
            'detailItems' => $detailItems,
            'uraianBagianPengusul' => optional($bagianPengusul)->uraianbagian,
            'uraianBagianPelaksana' => optional($bagianPelaksana)->uraianbagian,
            'tanggalPengajuan' => $tanggalPengajuan,
            'tahunAnggaranPengusulan' => $pengajuan->tahun_anggaran,
            'totalAnggaran' => collect($detailItems)->sum('total'),
            'namaPenanggungJawabPelaksana' => $namaPenanggungJawabPelaksana
        ];
    }

    /**
     * Helper method untuk memproses single document e-sign khusus perencanaan
     * Perencanaan hanya menandatangani berita acara
     */
    private function _processSingleDocumentEsignPerencanaan($pengajuan, $documentType, $passphrase, $verification)
    {
        // Perencanaan hanya menandatangani berita acara
        if ($documentType !== 'berita_acara') {
            throw new \Exception("Unit Perencanaan hanya dapat menandatangani berita acara. Dokumen {$documentType} tidak dapat ditandatangani.");
        }

        $result = $this->_signBeritaAcaraPerencanaan($pengajuan, $passphrase, $verification);
        return [
            'signed_path' => $result['signed_path'],
            'path_column' => 'berita_acara_signed_path',
            'date_column' => 'berita_acara_perencanaan_signed_date',
        ];
    }

    /**
     * Sign Berita Acara untuk perencanaan (reuse dari magic link)
     */
    private function _signBeritaAcaraPerencanaan($pengajuan, $passphrase, $verification)
    {
        \Log::info('Memulai sign Berita Acara untuk perencanaan (verifikasi langsung) - pengajuan ID: ' . $pengajuan->id);

        // Muat PDF yang sudah ditandatangani Koordinator
        if (!$pengajuan->berita_acara_signed_path || !$pengajuan->berita_acara_koordinator_signed_date) {
            throw new \Exception('Berita Acara harus ditandatangani oleh Koordinator terlebih dahulu.');
        }

        $filePath = storage_path('app/public/' . $pengajuan->berita_acara_signed_path);
        if (!file_exists($filePath)) {
            throw new \Exception('File Berita Acara yang ditandatangani Koordinator tidak ditemukan.');
        }

        $pdfContent = file_get_contents($filePath);
        $pdfBase64 = base64_encode($pdfContent);

        // Posisi tanda tangan ke-4 (kanan bawah) untuk perencanaan
        $signatureProperties = [
            'imageBase64' => '', // akan diisi nanti
            'tampilan' => 'VISIBLE',
            'page' => 2,
            'originX' => 375.0,
            'originY' => 375.0,
            'width' => 75.0,
            'height' => 75.0,
            'location' => 'Jakarta',
            'reason' => 'Dokumen Berita Acara Ini Telah Disetujui dengan Tanda Tangan Elektronik (Unit Perencanaan)'
        ];

        // Generate QR Code
        $qrContent = "Berita Acara RKBMN ID: " . $pengajuan->id . "\nPenanggung Jawab: " . $verification->eselon_iii_name . "\nTanggal: " . date('d M Y');
        $qrBuilder = \Endroid\QrCode\Builder\Builder::create()
            ->data($qrContent)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->size(150)
            ->margin(5)
            ->build();
        $qrBase64 = base64_encode($qrBuilder->getString());

        $signatureProperties['imageBase64'] = $qrBase64;

        // Panggil API E-Sign
        $client = new \GuzzleHttp\Client(['timeout' => 120, 'connect_timeout' => 30, 'verify' => false]);
        $url = config('app.esign_api_url', 'https://bsre-prod.dpr.go.id/api/v2/sign/pdf');
        $username = config('app.esign_username', 'ApaKabahrul');
        $password = config('app.esign_password', 'ApaKabahrul');

        // Gunakan NIK dari verification object atau method helper
        $nik = $this->getNikForSigningPerencanaan($pengajuan, $verification->verification_level);

        $requestData = [
            'nik' => $nik,
            'passphrase' => $passphrase,
            'signatureProperties' => [$signatureProperties],
            'file' => [$pdfBase64]
        ];

        $response = $client->post($url, ['auth' => [$username, $password], 'json' => $requestData]);
        $json = json_decode($response->getBody()->getContents(), true);

        if (!isset($json['file'][0])) {
            throw new \Exception("Gagal mendapatkan file dari API e-sign untuk dokumen berita_acara");
        }

        // Simpan file yang sudah ditandatangani (timpa file sebelumnya)
        $signedPdfData = base64_decode($json['file'][0]);
        $dirPath = storage_path('app/public/bmn_rkbmn_nonsbsk_berita_acara_esign');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $fileName = 'berita_acara_' . $pengajuan->id . '_signed.pdf';
        file_put_contents("{$dirPath}/{$fileName}", $signedPdfData);

        \Log::info('PDF Berita Acara (Unit Perencanaan) berhasil disimpan di: ' . $dirPath . '/' . $fileName);

        return [
            'signed_path' => "bmn_rkbmn_nonsbsk_berita_acara_esign/{$fileName}"
        ];
    }
}
