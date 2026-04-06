<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPemanfaatan;
use App\Models\Bagian;
use App\Models\DaftarBmn;
use App\Models\SuratKonfirmasiPerpanjanganSewa;

/**
 * Controller for BMN Utilization CRUD Operations
 *
 * Handles create, read, update, delete operations for BmnPemanfaatan data.
 * Document generation is handled separately by BmnDocumentController.
 *
 * @author Dashboard BMN Development Team
 * @version 2.0 (Refactored - Document generation moved to BmnDocumentController)
 */
class BmnUtilizationController extends Controller
{
    /**
     * Display listing of all utilization data
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get all utilization data (eager loading documents)
        // Ordered by id (since there's no created_at column)
        // Note: pembayaran relationship disabled - table not yet implemented
        $utilizationData = BmnPemanfaatan::withDocuments()
            ->orderBy('id', 'desc')
            ->get();

        if (request()->ajax()) {
            return response()->json(['utilizationData' => $utilizationData]);
        }

        // Get all bagian for dropdown (if needed for related functionality)
        $bagianList = Bagian::where('status', 'on')
            ->orderBy('uraianbagian')
            ->get();

        // Get notifications for expiring leases (e.g., within 30 days)
        $notifications = BmnPemanfaatan::whereNotNull('surat_konfirmasi_tanggal_berakhir')
            ->where('surat_konfirmasi_tanggal_berakhir', '<=', now()->addDays(30))
            ->orderBy('surat_konfirmasi_tanggal_berakhir', 'asc')
            ->get();

        return view('utilization.dashboard', compact('utilizationData', 'bagianList', 'notifications'));
    }

    /**
     * Display single utilization data
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $utilization = BmnPemanfaatan::with('perjanjianSewa')->findOrFail($id);

        // Merge perjanjian_sewa data into main object for backward compatibility
        $data = $utilization->toArray();
        if ($utilization->perjanjianSewa) {
            $perjanjian = $utilization->perjanjianSewa;
            // Map perjanjian_sewa columns back to expected field names
            $data['dokumen_perjanjian'] = $perjanjian->dokumen_perjanjian;
            $data['dokumen_bukti_bayar'] = $perjanjian->dokumen_bukti_bayar;
            $data['dokumen_bukti_tindak_lanjut_siman'] = $perjanjian->dokumen_bukti_tindak_lanjut_siman;
            $data['nilai_pendapatan_bukti_bayar'] = $perjanjian->nilai_pendapatan_bukti_bayar;
            $data['perjanjian_logo_penyewa'] = $perjanjian->logo_penyewa;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Store new utilization data (Tahap 1: Informasi Penyewa)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Tahap 1: Validasi hanya untuk Informasi Penyewa
        $request->validate([
            'pic_penyewa' => 'required|string|max:255',
            'nomor_hp_pic_penyewa' => 'required|string|max:20',
            'pic_administrasi_bmn' => 'required|string|max:255',
            'nomor_pic_administrasi_bmn' => 'required|string|max:20',
            'nama_mitra_penyewa' => 'required|string|max:255',
            'jenis_mitra' => 'required|in:Perusahaan,Yayasan,Koperasi,Perseorangan',
            'jenis_usulan' => 'required|in:Perpanjangan,Usulan Baru',
            'peruntukan_sewa' => 'nullable|string',
            'keterangan_uraian' => 'nullable|string',
        ]);

        $utilization = BmnPemanfaatan::create([
            'pic_penyewa' => $request->pic_penyewa,
            'nomor_hp_pic_penyewa' => $request->nomor_hp_pic_penyewa,
            'pic_administrasi_bmn' => $request->pic_administrasi_bmn,
            'nomor_pic_administrasi_bmn' => $request->nomor_pic_administrasi_bmn,
            'nama_mitra_penyewa' => $request->nama_mitra_penyewa,
            'jenis_mitra' => $request->jenis_mitra,
            'jenis_usulan' => $request->jenis_usulan,
            'peruntukan_sewa' => $request->peruntukan_sewa,
            'keterangan_uraian' => $request->keterangan_uraian,
            'is_complete' => false, // Default to incomplete/draft
        ]);

        return response()->json(['success' => true, 'data' => $utilization]);
    }

    /**
     * Update utilization data (All fields - can be filled gradually)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Tahap 2: Validasi untuk semua field (optional karena bisa diisi bertahap)
        $request->validate([
            // Tahap 1 fields
            'pic_penyewa' => 'nullable|string|max:255',
            'nomor_hp_pic_penyewa' => 'nullable|string|max:20',
            'pic_administrasi_bmn' => 'nullable|string|max:255',
            'nomor_pic_administrasi_bmn' => 'nullable|string|max:20',
            'nama_mitra_penyewa' => 'nullable|string|max:255',
            'jenis_mitra' => 'nullable|in:Perusahaan,Yayasan,Koperasi,Perseorangan',
            'jenis_usulan' => 'nullable|in:Perpanjangan,Usulan Baru',
            'peruntukan_sewa' => 'nullable|string',
            'keterangan_uraian' => 'nullable|string',

            // Tab 2: Konfirmasi - Nodin
            'nodin_konfirmasi_nomor' => 'nullable|string|max:255',
            'nodin_konfirmasi_tanggal' => 'nullable|date',
            'nodin_konfirmasi_mitra_peruntukan' => 'nullable|string|max:255',
            'nodin_konfirmasi_tanggal_berakhir_sewa' => 'nullable|date',

            // Tab 2: Surat Konfirmasi
            'surat_konfirmasi_nomor' => 'nullable|string|max:255',
            'surat_konfirmasi_tujuan' => 'nullable|string|max:255',
            'surat_konfirmasi_peruntukan' => 'nullable|string|max:255',
            'surat_konfirmasi_nomor_perjanjian_lama' => 'nullable|string|max:255',
            'surat_konfirmasi_tanggal_berakhir' => 'nullable|date',
            'surat_konfirmasi_kasub_nama_nomor' => 'nullable|string|max:255',
            'surat_konfirmasi_lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 2: Dokumen Pendukung
            'dokumen_surat_usulan_sewa' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_npwp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_ktp_penandatangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_nib' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 3: Usulan - Nodin Berjenjang
            'nodin_berjenjang_mitra' => 'nullable|string|max:255',
            'nodin_berjenjang_peruntukan' => 'nullable|string|max:255',
            'nodin_berjenjang_nomor' => 'nullable|string|max:100',
            'nodin_berjenjang_tanggal' => 'nullable|date',
            'nodin_berjenjang_nominal' => 'nullable|numeric',

            // Tab 3: Surat Usulan KPKNL
            'surat_usulan_kpknl_nomor' => 'nullable|string|max:255',
            'surat_usulan_kpknl_tanggal' => 'nullable|date',
            'surat_usulan_kpknl_hal' => 'nullable|string|max:255',
            'surat_usulan_kpknl_tujuan' => 'nullable|string|max:255',
            'surat_usulan_kpknl_isi' => 'nullable|string',

            // Tab 3: SPTJM
            'sptjm_nomor' => 'nullable|string|max:255',
            'sptjm_tanggal' => 'nullable|date',
            'sptjm_kode_barang' => 'nullable|string|max:255',
            'sptjm_nup' => 'nullable|string|max:255',
            'sptjm_luasan_sewa' => 'nullable|string|max:255',
            'sptjm_lokasi_sewa' => 'nullable|string|max:255',

            // Tab 3: Surat Pernyataan
            'surat_pernyataan_nomor' => 'nullable|string|max:255',
            'surat_pernyataan_tanggal' => 'nullable|date',
            'surat_pernyataan_kode_barang' => 'nullable|string|max:255',
            'surat_pernyataan_nup' => 'nullable|string|max:255',
            'surat_pernyataan_luasan_sewa' => 'nullable|string|max:255',
            'surat_pernyataan_lokasi_sewa' => 'nullable|string|max:255',

            // Tab 3: Daftar BMN (JSON)
            'daftar_bmn' => 'nullable|json',

            // Tab 3: Dokumen Usulan
            'dokumen_psp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_kib' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_usulan_ttd' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 4: Penilaian KPKNL - Dokumen
            'dokumen_jadwal_penilaian' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_basl' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_persetujuan_kpknl' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 4: Nodin Persetujuan KPKNL
            'nodin_persetujuan_kpknl_nomor' => 'nullable|string|max:255',
            'nodin_persetujuan_kpknl_tanggal' => 'nullable|date',
            'nodin_persetujuan_kpknl_tujuan' => 'nullable|string|max:255',
            'nodin_persetujuan_kpknl_nomor_persetujuan' => 'nullable|string|max:255',
            'nodin_persetujuan_kpknl_tanggal_persetujuan' => 'nullable|date',
            'nodin_persetujuan_kpknl_periode_sewa' => 'nullable|string|max:255',
            'nodin_persetujuan_kpknl_nominal' => 'nullable|numeric',
            'nodin_persetujuan_kpknl_mitra' => 'nullable|string|max:255',
            'nodin_persetujuan_kpknl_kasub' => 'nullable|string|max:255',

            // Tab 4: Surat Invoice
            'surat_invoice_nomor' => 'nullable|string|max:255',
            'surat_invoice_tanggal' => 'nullable|date',
            'surat_invoice_tujuan' => 'nullable|string|max:255',
            'surat_invoice_nomor_persetujuan' => 'nullable|string|max:255',
            'surat_invoice_tanggal_persetujuan' => 'nullable|date',
            'surat_invoice_periode_sewa' => 'nullable|string|max:255',
            'surat_invoice_nominal' => 'nullable|numeric',
            'surat_invoice_mitra' => 'nullable|string|max:255',
            'surat_invoice_kasub' => 'nullable|string|max:255',
            'dokumen_kode_billing' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 5: Perjanjian - Dokumen
            'dokumen_bukti_bayar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_perjanjian' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

            // Tab 5: Detail Perjanjian
            'perjanjian_logo_penyewa' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            'perjanjian_mitra' => 'nullable|string|max:255',
            'perjanjian_peruntukan' => 'nullable|string|max:255',
            'perjanjian_gedung' => 'nullable|string|max:255',
            'perjanjian_hari_tanggal' => 'nullable|string|max:255',
            'perjanjian_detail_pihak_kedua' => 'nullable|string',
            'perjanjian_nomor' => 'nullable|string|max:255',
            'perjanjian_tanggal_penandatanganan' => 'nullable|date',
            'jangka_waktu_nilai' => 'nullable|integer',
            'jangka_waktu_satuan' => 'nullable|string|max:50',

            // Tab 5: Nodin Ttd & Internal
            'nodin_ttd_nomor' => 'nullable|string|max:255',
            'nodin_ttd_tanggal' => 'nullable|date',
            'nodin_ttd_tujuan' => 'nullable|string|max:255',
            'nodin_ttd_mitra' => 'nullable|string|max:255',
            'nodin_ttd_judul_perjanjian' => 'nullable|string|max:255',
            'nodin_internal_nomor' => 'nullable|string|max:255',
            'nodin_internal_tanggal' => 'nullable|date',
            'nodin_internal_mitra' => 'nullable|string|max:255',
            'nodin_internal_judul_perjanjian' => 'nullable|string|max:255',
            'nodin_internal_nomor_perjanjian' => 'nullable|string|max:255',
            'nodin_internal_detail_persetujuan' => 'nullable|string',
        ]);

        $utilization = BmnPemanfaatan::findOrFail($id);

        // Define all file fields
        $fileFields = [
            'surat_konfirmasi_lampiran',
            'dokumen_surat_usulan_sewa',
            'dokumen_npwp',
            'dokumen_ktp_penandatangan',
            'dokumen_nib',
            'dokumen_psp',
            'dokumen_kib',
            'dokumen_usulan_ttd',
            'dokumen_jadwal_penilaian',
            'dokumen_basl',
            'dokumen_persetujuan_kpknl',
            'dokumen_kode_billing',
            'dokumen_bukti_bayar',
            'dokumen_perjanjian',
            'perjanjian_logo_penyewa',
        ];

        // Prepare data for update
        $updateData = $request->except($fileFields);

        // Handle file uploads
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/pemanfaatan', $fileName, 'public');
                $updateData[$field] = $filePath;
            }
        }

        $utilization->update($updateData);

        // Automatically check and update completeness status
        $utilization->refresh();
        $utilization->is_complete = $this->checkCompleteness($utilization);
        $utilization->save();

        return response()->json(['success' => true, 'data' => $utilization]);
    }

    /**
     * Delete utilization data
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);
        $utilization->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle complete status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleComplete(Request $request, $id)
    {
        $request->validate([
            'is_complete' => 'required|boolean',
        ]);

        $utilization = BmnPemanfaatan::findOrFail($id);
        $utilization->update([
            'is_complete' => $request->is_complete
        ]);

        return response()->json([
            'success' => true,
            'data' => $utilization,
            'message' => $request->is_complete ? 'Data ditandai lengkap' : 'Data ditandai belum lengkap'
        ]);
    }

    /**
     * Upload documents only (Lengkapi Data modal)
     *
     * Handles file uploads for supporting documents (no input fields)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocuments(Request $request, $id)
    {
        try {
            \Log::info('Upload Documents called for ID: ' . $id);
            \Log::info('Request data: ', $request->all());

            // Validate all file fields
            $request->validate([
                // Tab 1: Dokumen Konfirmasi
                'surat_konfirmasi_lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_surat_usulan_sewa' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_npwp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_ktp_penandatangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_nib' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

                // Tab 2: Dokumen Usulan
                'dokumen_psp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_kib' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_usulan_ttd' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

                // Tab 3: Dokumen Penilaian
                'dokumen_jadwal_penilaian' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_basl' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_persetujuan_kpknl' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_kode_billing' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',

                // Tab 4: Dokumen Final (files for perjanjian_sewa table)
                'dokumen_bukti_bayar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'dokumen_perjanjian' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'perjanjian_logo_penyewa' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
                'dokumen_bukti_tindak_lanjut_siman' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'nilai_pendapatan_bukti_bayar' => 'nullable|numeric',
                'daftar_bmn_nomor_surat' => 'nullable|string|max:255',
            ]);

            $utilization = BmnPemanfaatan::findOrFail($id);

            // Fields that belong to bmn_pemanfaatan table
            $mainTableFields = [
                'surat_konfirmasi_lampiran',
                'dokumen_surat_usulan_sewa',
                'dokumen_npwp',
                'dokumen_ktp_penandatangan',
                'dokumen_nib',
                'dokumen_psp',
                'dokumen_kib',
                'dokumen_usulan_ttd',
                'dokumen_jadwal_penilaian',
                'dokumen_basl',
                'dokumen_persetujuan_kpknl',
                'dokumen_kode_billing',
                'daftar_bmn_nomor_surat', // This is in main table
            ];

            // Fields that belong to perjanjian_sewa table
            $perjanjianFields = [
                'perjanjian_logo_penyewa',
                'dokumen_perjanjian',
                'dokumen_bukti_bayar',
                'dokumen_bukti_tindak_lanjut_siman',
                'nilai_pendapatan_bukti_bayar'
            ];

            $mainTableData = [];
            $perjanjianData = [];
            $uploadedFiles = [];

            // Process main table files
            foreach ($mainTableFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/pemanfaatan', $fileName, 'public');
                    $mainTableData[$field] = $filePath;
                    $uploadedFiles[] = $field;
                    \Log::info("Uploaded file for main table: $field -> $filePath");
                }
            }

            // Process text field for main table
            if ($request->has('daftar_bmn_nomor_surat') && $request->daftar_bmn_nomor_surat != '') {
                $mainTableData['daftar_bmn_nomor_surat'] = $request->daftar_bmn_nomor_surat;
                \Log::info("Added daftar_bmn_nomor_surat: " . $request->daftar_bmn_nomor_surat);
            }

            // Process perjanjian sewa files and fields
            foreach ($perjanjianFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/pemanfaatan', $fileName, 'public');
                    $perjanjianData[$field] = $filePath;
                    $uploadedFiles[] = $field;
                    \Log::info("Uploaded file for perjanjian: $field -> $filePath");
                } elseif ($field === 'nilai_pendapatan_bukti_bayar' && $request->has($field) && $request->$field != '') {
                    // Handle text input for nilai_pendapatan_bukti_bayar
                    $perjanjianData[$field] = $request->$field;
                    \Log::info("Added nilai_pendapatan_bukti_bayar: " . $request->$field);
                }
            }

            // Update Main Table (BmnPemanfaatan) - only if there's data
            if (!empty($mainTableData)) {
                \Log::info('Updating main table with data: ', $mainTableData);
                $utilization->update($mainTableData);
                \Log::info('Main table updated successfully');
            }

            // Update PerjanjianSewa Table - only if there's data
            if (!empty($perjanjianData)) {
                // Map field names to match database columns
                $mappedData = [];
                if (isset($perjanjianData['perjanjian_logo_penyewa'])) {
                    $mappedData['logo_penyewa'] = $perjanjianData['perjanjian_logo_penyewa'];
                }
                if (isset($perjanjianData['dokumen_perjanjian'])) {
                    $mappedData['dokumen_perjanjian'] = $perjanjianData['dokumen_perjanjian'];
                }
                if (isset($perjanjianData['dokumen_bukti_bayar'])) {
                    $mappedData['dokumen_bukti_bayar'] = $perjanjianData['dokumen_bukti_bayar'];
                }
                if (isset($perjanjianData['dokumen_bukti_tindak_lanjut_siman'])) {
                    $mappedData['dokumen_bukti_tindak_lanjut_siman'] = $perjanjianData['dokumen_bukti_tindak_lanjut_siman'];
                }
                if (isset($perjanjianData['nilai_pendapatan_bukti_bayar'])) {
                    $mappedData['nilai_pendapatan_bukti_bayar'] = $perjanjianData['nilai_pendapatan_bukti_bayar'];
                }

                if (!empty($mappedData)) {
                    \Log::info('Updating perjanjian_sewa table with data: ', $mappedData);
                    $utilization->perjanjianSewa()->updateOrCreate(
                        ['pemanfaatan_id' => $utilization->id],
                        $mappedData
                    );
                    \Log::info('Perjanjian Sewa table updated successfully');
                }
            }

            // Refresh utilization to get latest data including perjanjianSewa
            $utilization->load('perjanjianSewa');
            $utilization->refresh();

            // Check completeness
            $utilization->is_complete = $this->checkCompleteness($utilization);
            $utilization->save();

            \Log::info('Upload completed successfully. Uploaded files count: ' . count($uploadedFiles));

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' dokumen berhasil diunggah',
                'uploaded_files' => $uploadedFiles,
                'data' => $utilization->fresh([
                    'suratKonfirmasi',
                    'nodinBerjenjang',
                    'suratUsulanKpknl',
                    'daftarBmn',
                    'perjanjianSewa',
                    'nodinPersetujuanKpknl',
                    'suratPermohonanTtd',
                    'nodinInternal',
                    'suratPenyampaianPerjanjian'
                ])
            ]);
        } catch (\Exception $e) {
            \Log::error('Upload Documents Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save individual document data via AJAX
     *
     * Handles saving data for specific documents (per-document modal forms)
     *
     * @param Request $request
     * @param int $id Utilization ID
     * @param string $type Document type
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDocumentData(Request $request, $id, $type)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);

        // Validate and save based on document type
        switch ($type) {
            case 'surat-konfirmasi':
                $request->validate([
                    'surat_konfirmasi_nomor' => 'required|string|max:255',
                    'surat_konfirmasi_tanggal' => 'required|date',
                    'surat_konfirmasi_tujuan' => 'nullable|string|max:255',
                    'surat_konfirmasi_tujuan_surat' => 'nullable|string|max:255',
                    'surat_konfirmasi_peruntukan' => 'nullable|string|max:255',
                    'surat_konfirmasi_peruntukan_surat' => 'nullable|string|max:255',
                    'surat_konfirmasi_nomor_perjanjian_lama' => 'nullable|string|max:255',
                    'surat_konfirmasi_nomor_perjanjian_lama_dpr' => 'nullable|string|max:255',
                    'surat_konfirmasi_nomor_perjanjian_lama_mitra' => 'nullable|string|max:255',
                    'surat_konfirmasi_tanggal_berakhir' => 'nullable|date',
                    'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'nullable|date',
                    'surat_konfirmasi_kasub_nama' => 'nullable|string|max:255',
                    'surat_konfirmasi_kasub_nama_nomor' => 'nullable|string|max:255',
                    'surat_konfirmasi_kasub_nomor' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'surat_konfirmasi_nomor' => $request->surat_konfirmasi_nomor,
                    'surat_konfirmasi_tanggal' => $request->surat_konfirmasi_tanggal,
                    'surat_konfirmasi_tujuan' => $request->surat_konfirmasi_tujuan,
                    'surat_konfirmasi_tujuan_surat' => $request->surat_konfirmasi_tujuan_surat,
                    'surat_konfirmasi_peruntukan' => $request->surat_konfirmasi_peruntukan,
                    'surat_konfirmasi_peruntukan_surat' => $request->surat_konfirmasi_peruntukan_surat,
                    'surat_konfirmasi_nomor_perjanjian_lama' => $request->surat_konfirmasi_nomor_perjanjian_lama,
                    'surat_konfirmasi_nomor_perjanjian_lama_dpr' => $request->surat_konfirmasi_nomor_perjanjian_lama_dpr,
                    'surat_konfirmasi_nomor_perjanjian_lama_mitra' => $request->surat_konfirmasi_nomor_perjanjian_lama_mitra,
                    'surat_konfirmasi_tanggal_berakhir' => $request->surat_konfirmasi_tanggal_berakhir,
                    'surat_konfirmasi_tanggal_konfirmasi_terakhir' => $request->surat_konfirmasi_tanggal_konfirmasi_terakhir,
                    'surat_konfirmasi_kasub_nama' => $request->surat_konfirmasi_kasub_nama,
                    'surat_konfirmasi_kasub_nama_nomor' => $request->surat_konfirmasi_kasub_nama_nomor,
                    'surat_konfirmasi_kasub_nomor' => $request->surat_konfirmasi_kasub_nomor,
                ]);
                break;

            case 'surat-konfirmasi-perpanjangan-sewa':
                // Validate with old field names (with prefix from form)
                $validated = $request->validate([
                    'surat_konfirmasi_nomor' => 'required|string|max:255',
                    'surat_konfirmasi_tanggal' => 'required|date',
                    'surat_konfirmasi_tujuan_surat' => 'nullable|string',
                    'surat_konfirmasi_peruntukan_surat' => 'nullable|string',
                    'surat_konfirmasi_nomor_perjanjian_lama_dpr' => 'nullable|string',
                    'surat_konfirmasi_nomor_perjanjian_lama_mitra' => 'nullable|string',
                    'surat_konfirmasi_tanggal_berakhir' => 'nullable|date',
                    'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'nullable|date',
                    'surat_konfirmasi_kasub_nama' => 'nullable|string|max:255',
                    'surat_konfirmasi_kasub_nomor' => 'nullable|string|max:255',
                ]);

                // Map to child table field names (without prefix)
                $childData = [
                    'nomor' => $validated['surat_konfirmasi_nomor'],
                    'tanggal' => $validated['surat_konfirmasi_tanggal'],
                    'tujuan_surat' => $validated['surat_konfirmasi_tujuan_surat'] ?? null,
                    'peruntukan_surat' => $validated['surat_konfirmasi_peruntukan_surat'] ?? null,
                    'nomor_perjanjian_lama_dpr' => $validated['surat_konfirmasi_nomor_perjanjian_lama_dpr'] ?? null,
                    'nomor_perjanjian_lama_mitra' => $validated['surat_konfirmasi_nomor_perjanjian_lama_mitra'] ?? null,
                    'tanggal_berakhir' => $validated['surat_konfirmasi_tanggal_berakhir'] ?? null,
                    'tanggal_konfirmasi_terakhir' => $validated['surat_konfirmasi_tanggal_konfirmasi_terakhir'] ?? null,
                    'kasub_nama' => $validated['surat_konfirmasi_kasub_nama'] ?? null,
                    'kasub_nomor' => $validated['surat_konfirmasi_kasub_nomor'] ?? null,
                ];

                // Save to child table
                $utilization->suratKonfirmasi()->updateOrCreate(
                    ['pemanfaatan_id' => $utilization->id],
                    $childData
                );
                break;

            case 'nodin-konfirmasi':
                $request->validate([
                    'nodin_konfirmasi_nomor' => 'required|string|max:255',
                    'nodin_konfirmasi_tanggal' => 'required|date',
                    'nodin_konfirmasi_mitra_peruntukan' => 'nullable|string|max:255',
                    'nodin_konfirmasi_tanggal_berakhir_sewa' => 'nullable|date',
                ]);

                $utilization->update([
                    'nodin_konfirmasi_nomor' => $request->nodin_konfirmasi_nomor,
                    'nodin_konfirmasi_tanggal' => $request->nodin_konfirmasi_tanggal,
                    'nodin_konfirmasi_mitra_peruntukan' => $request->nodin_konfirmasi_mitra_peruntukan,
                    'nodin_konfirmasi_tanggal_berakhir_sewa' => $request->nodin_konfirmasi_tanggal_berakhir_sewa,
                ]);
                break;

            case 'nodin-berjenjang':
                // Validate with old field names (with prefix from form)
                $validated = $request->validate([
                    'nodin_berjenjang_nomor' => 'required|string|max:100',
                    'nodin_berjenjang_tanggal' => 'required|date',
                    'nodin_berjenjang_tanggal_mulai' => 'required|date',
                    'nodin_berjenjang_tanggal_selesai' => 'required|date',
                    'nodin_berjenjang_mitra' => 'nullable|string|max:255',
                    'nodin_berjenjang_peruntukan' => 'nullable|string|max:255',
                    'nodin_berjenjang_nominal' => 'nullable|numeric',
                    'nodin_berjenjang_kasub_nama' => 'nullable|string|max:255',
                    'nodin_berjenjang_kasub_nomor' => 'nullable|string|max:255',
                ]);

                // Map to child table field names (without prefix)
                $childData = [
                    'nomor' => $validated['nodin_berjenjang_nomor'],
                    'tanggal' => $validated['nodin_berjenjang_tanggal'],
                    'tanggal_mulai' => $validated['nodin_berjenjang_tanggal_mulai'],
                    'tanggal_selesai' => $validated['nodin_berjenjang_tanggal_selesai'],
                    'mitra' => $validated['nodin_berjenjang_mitra'] ?? null,
                    'peruntukan' => $validated['nodin_berjenjang_peruntukan'] ?? null,
                    'nominal' => $validated['nodin_berjenjang_nominal'] ?? null,
                    'kasub_nama' => $validated['nodin_berjenjang_kasub_nama'] ?? null,
                    'kasub_nomor' => $validated['nodin_berjenjang_kasub_nomor'] ?? null,
                ];

                // Save to child table
                $utilization->nodinBerjenjang()->updateOrCreate(
                    ['pemanfaatan_id' => $utilization->id],
                    $childData
                );
                break;

            case 'surat-usulan-kpknl':
                // Validate with old field names (with prefix from form)
                $validated = $request->validate([
                    'surat_usulan_kpknl_nomor' => 'required|string|max:255',
                    'surat_usulan_kpknl_tanggal' => 'required|date',
                    'surat_usulan_kpknl_hal' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_tujuan' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_isi' => 'nullable|string',
                    'surat_usulan_kpknl_peruntukan' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_tanggal_berakhir' => 'nullable|date',
                    'surat_usulan_kpknl_nama_kasubag' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_nomor_kasubag' => 'nullable|string|max:255',
                    'sptjm_nomor' => 'nullable|string|max:255',
                    'sptjm_tanggal' => 'nullable|date',
                    'sptjm_kode_barang' => 'nullable|string|max:255',
                    'sptjm_nup' => 'nullable|string|max:255',
                    'sptjm_luasan_sewa' => 'nullable|string|max:255',
                    'sptjm_lokasi_sewa' => 'nullable|string|max:255',
                ]);

                // Map to child table field names
                $childData = [
                    'surat_usulan_nomor' => $validated['surat_usulan_kpknl_nomor'],
                    'surat_usulan_tanggal' => $validated['surat_usulan_kpknl_tanggal'],
                    'surat_usulan_hal' => $validated['surat_usulan_kpknl_hal'] ?? null,
                    'surat_usulan_tujuan' => $validated['surat_usulan_kpknl_tujuan'] ?? null,
                    'surat_usulan_isi' => $validated['surat_usulan_kpknl_isi'] ?? null,
                    'surat_usulan_peruntukan' => $validated['surat_usulan_kpknl_peruntukan'] ?? null,
                    'surat_usulan_tanggal_berakhir' => $validated['surat_usulan_kpknl_tanggal_berakhir'] ?? null,
                    'kasubag_nama' => $validated['surat_usulan_kpknl_nama_kasubag'] ?? null,
                    'kasubag_nomor' => $validated['surat_usulan_kpknl_nomor_kasubag'] ?? null,
                    'sptjm_nomor' => $validated['sptjm_nomor'] ?? '',
                    'sptjm_tanggal' => $validated['sptjm_tanggal'] ?? null,
                    'sptjm_kode_barang' => $validated['sptjm_kode_barang'] ?? '',
                    'sptjm_nup' => $validated['sptjm_nup'] ?? null,
                    'sptjm_luasan_sewa' => $validated['sptjm_luasan_sewa'] ?? null,
                    'sptjm_lokasi_sewa' => $validated['sptjm_lokasi_sewa'] ?? null,
                ];

                // Save to child table
                $utilization->suratUsulanKpknl()->updateOrCreate(
                    ['pemanfaatan_id' => $utilization->id],
                    $childData
                );
                break;

            case 'sptjm':
                $request->validate([
                    'sptjm_nomor' => 'required|string|max:255',
                    'sptjm_tanggal' => 'required|date',
                    'sptjm_kode_barang' => 'nullable|string|max:255',
                    'sptjm_nup' => 'nullable|string|max:255',
                    'sptjm_luasan_sewa' => 'nullable|string|max:255',
                    'sptjm_lokasi_sewa' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'sptjm_nomor' => $request->sptjm_nomor,
                    'sptjm_tanggal' => $request->sptjm_tanggal,
                    'sptjm_kode_barang' => $request->sptjm_kode_barang,
                    'sptjm_nup' => $request->sptjm_nup,
                    'sptjm_luasan_sewa' => $request->sptjm_luasan_sewa,
                    'sptjm_lokasi_sewa' => $request->sptjm_lokasi_sewa,
                ]);
                break;

            case 'surat-pernyataan':
                $request->validate([
                    'surat_pernyataan_nomor' => 'required|string|max:255',
                    'surat_pernyataan_tanggal' => 'required|date',
                    'surat_pernyataan_kode_barang' => 'nullable|string|max:255',
                    'surat_pernyataan_nup' => 'nullable|string|max:255',
                    'surat_pernyataan_luasan_sewa' => 'nullable|string|max:255',
                    'surat_pernyataan_lokasi_sewa' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'surat_pernyataan_nomor' => $request->surat_pernyataan_nomor,
                    'surat_pernyataan_tanggal' => $request->surat_pernyataan_tanggal,
                    'surat_pernyataan_kode_barang' => $request->surat_pernyataan_kode_barang,
                    'surat_pernyataan_nup' => $request->surat_pernyataan_nup,
                    'surat_pernyataan_luasan_sewa' => $request->surat_pernyataan_luasan_sewa,
                    'surat_pernyataan_lokasi_sewa' => $request->surat_pernyataan_lokasi_sewa,
                ]);
                break;

            case 'daftar-bmn':
                // Validate array of BMN items
                $validated = $request->validate([
                    'daftar_bmn' => 'required|array',
                    'daftar_bmn.*.kode_barang' => 'nullable|string|max:100',
                    'daftar_bmn.*.nup' => 'nullable|string|max:50',
                    'daftar_bmn.*.jenis_bmn' => 'nullable|string|max:255',
                    'daftar_bmn.*.luas_keseluruhan' => 'nullable|numeric',
                    'daftar_bmn.*.nilai_perolehan' => 'nullable|numeric',
                    'daftar_bmn.*.dicatat_di_simak' => 'nullable|string|max:50',
                    'daftar_bmn.*.objek_sewa' => 'nullable|string|max:255',
                    'daftar_bmn.*.lokasi' => 'nullable|string|max:255',
                    'daftar_bmn.*.penyewa' => 'nullable|string|max:255',
                    'daftar_bmn.*.peruntukan' => 'nullable|string|max:255',
                    'daftar_bmn.*.usulan_luas_sewa' => 'nullable|numeric',
                    'daftar_bmn.*.usulan_jangka_waktu' => 'nullable|string|max:100',
                    'daftar_bmn.*.usulan_periodesitas' => 'nullable|string|max:100',
                    'daftar_bmn.*.usulan_besaran_sewa' => 'nullable|numeric',
                ]);

                // Delete existing BMN items for this pemanfaatan
                $utilization->daftarBmn()->delete();

                // Insert new BMN items
                foreach ($validated['daftar_bmn'] as $bmnData) {
                    $utilization->daftarBmn()->create($bmnData);
                }
                break;

            case 'nodin-persetujuan-kpknl':
                $validated = $request->validate([
                    'nodin_persetujuan_kpknl_nomor' => 'required|string|max:255',
                    'nodin_persetujuan_kpknl_tanggal' => 'required|date',
                    'nodin_persetujuan_kpknl_perihal' => 'nullable|string',
                    'nodin_persetujuan_kpknl_periode_mulai' => 'nullable|date',
                    'nodin_persetujuan_kpknl_periode_selesai' => 'nullable|date',
                    'nodin_persetujuan_kpknl_tujuan' => 'nullable|string|max:255',
                    'nodin_persetujuan_kpknl_nominal' => 'nullable|numeric',
                ]);

                $utilization->nodinPersetujuanKpknl()->updateOrCreate(
                    ['pemanfaatan_id' => $utilization->id],
                    [
                        'nomor_nodin' => $validated['nodin_persetujuan_kpknl_nomor'],
                        'tanggal_nodin' => $validated['nodin_persetujuan_kpknl_tanggal'],
                        'perihal_nodin' => $validated['nodin_persetujuan_kpknl_perihal'] ?? null,
                        'periode_sewa_mulai' => $validated['nodin_persetujuan_kpknl_periode_mulai'] ?? null,
                        'periode_sewa_selesai' => $validated['nodin_persetujuan_kpknl_periode_selesai'] ?? null,
                        'tujuan' => $validated['nodin_persetujuan_kpknl_tujuan'] ?? null,
                        'nominal' => $validated['nodin_persetujuan_kpknl_nominal'] ?? null,
                    ]
                );
                break;

            case 'surat-invoice':
                $request->validate([
                    'surat_invoice_nomor' => 'required|string|max:255',
                    'surat_invoice_tanggal' => 'required|date',
                    'surat_invoice_tujuan' => 'nullable|string|max:255',
                    'surat_invoice_nomor_persetujuan' => 'nullable|string|max:255',
                    'surat_invoice_tanggal_persetujuan' => 'nullable|date',
                    'surat_invoice_periode_mulai' => 'nullable|date',
                    'surat_invoice_periode_akhir' => 'nullable|date',
                    'surat_invoice_lama_periode' => 'nullable|string|max:255',
                    'surat_invoice_nominal' => 'nullable|numeric',
                    'surat_invoice_mitra' => 'nullable|string|max:255',
                    'surat_invoice_kasub' => 'nullable|string|max:255',
                    'surat_invoice_kasub_nomor' => 'nullable|string|max:255',
                    'surat_invoice_nomor_bmn' => 'nullable|string|max:255',
                    'surat_invoice_tanggal_faktur' => 'nullable|date',
                    'surat_invoice_nama_kasubag_gelar' => 'nullable|string|max:255',
                ]);

                // Auto-format periode sewa string if dates are provided
                $periodeSewa = $request->surat_invoice_periode_sewa; // Default to existing input if any
                if ($request->surat_invoice_periode_mulai && $request->surat_invoice_periode_akhir) {
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    
                    $start = \Carbon\Carbon::parse($request->surat_invoice_periode_mulai);
                    $end = \Carbon\Carbon::parse($request->surat_invoice_periode_akhir);
                    
                    $startStr = $start->day . ' ' . $months[$start->month] . ' ' . $start->year;
                    $endStr = $end->day . ' ' . $months[$end->month] . ' ' . $end->year;
                    
                    $periodeSewa = "$startStr - $endStr";
                }

                $utilization->update([
                    'surat_invoice_nomor' => $request->surat_invoice_nomor,
                    'surat_invoice_tanggal' => $request->surat_invoice_tanggal,
                    'surat_invoice_tujuan' => $request->surat_invoice_tujuan,
                    'surat_invoice_nomor_persetujuan' => $request->surat_invoice_nomor_persetujuan,
                    'surat_invoice_tanggal_persetujuan' => $request->surat_invoice_tanggal_persetujuan,
                    'surat_invoice_periode_sewa' => $periodeSewa,
                    'surat_invoice_periode_mulai' => $request->surat_invoice_periode_mulai,
                    'surat_invoice_periode_akhir' => $request->surat_invoice_periode_akhir,
                    'surat_invoice_lama_periode' => $request->surat_invoice_lama_periode,
                    'surat_invoice_nominal' => $request->surat_invoice_nominal,
                    'surat_invoice_mitra' => $request->surat_invoice_mitra,
                    'surat_invoice_kasub' => $request->surat_invoice_kasub,
                    'surat_invoice_kasub_nomor' => $request->surat_invoice_kasub_nomor,
                    'surat_invoice_nomor_bmn' => $request->surat_invoice_nomor_bmn,
                    'surat_invoice_tanggal_faktur' => $request->surat_invoice_tanggal_faktur,
                    'surat_invoice_nama_kasubag_gelar' => $request->surat_invoice_nama_kasubag_gelar,
                ]);
                break;

            case 'perjanjian-sewa':
                $request->validate([
                    'perjanjian_logo_penyewa' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                    'perjanjian_mitra' => 'nullable|string|max:255',
                    'perjanjian_peruntukan' => 'nullable|string|max:255',
                    'perjanjian_gedung' => 'nullable|string|max:255',
                    'perjanjian_nomor' => 'required|string|max:255',
                    'perjanjian_tanggal_penandatanganan' => 'required|date',
                    'jangka_waktu_nilai' => 'nullable|integer',
                    'jangka_waktu_satuan' => 'nullable|string|max:50',
                    
                    // New fields for PerjanjianSewa model
                    'pihak_pertama_nama' => 'nullable|string|max:255',
                    'pihak_pertama_kedudukan' => 'nullable|string|max:255',
                    'pihak_pertama_keputusan_nomor' => 'nullable|string|max:255',
                    'pihak_pertama_keputusan_tahun' => 'nullable|string|max:4',
                    'pihak_kedua_nama' => 'nullable|string|max:255',
                    'pihak_kedua_kedudukan' => 'nullable|string|max:255',
                    'pihak_kedua_dasar_hukum' => 'nullable|string',
                    'pihak_kedua_keputusan_nomor' => 'nullable|string|max:255',
                    'pihak_kedua_keputusan_tanggal' => 'nullable|date',
                    'pihak_kedua_atas_nama' => 'nullable|string|max:255',
                    'pihak_kedua_alamat' => 'nullable|string',
                    'pihak_kedua_kegiatan_usaha' => 'nullable|string|max:255',
                    'objek_luas' => 'nullable|numeric',
                    'objek_satuan_luas' => 'nullable|string|max:50',
                    'objek_letak' => 'nullable|string',
                    'nilai_sewa' => 'nullable|numeric',
                    'nilai_pendapatan_bukti_bayar' => 'nullable|numeric',
                    'periode_mulai' => 'nullable|date',
                    'periode_selesai' => 'nullable|date',
                ]);

                // Handle File Upload for Logo
                $logoPath = null;
                if ($request->hasFile('perjanjian_logo_penyewa')) {
                    $file = $request->file('perjanjian_logo_penyewa');
                    $fileName = time() . '_logo_' . $file->getClientOriginalName();
                    $logoPath = $file->storeAs('uploads/logos', $fileName, 'public');
                }

                // Prepare data for PerjanjianSewa model
                $perjanjianData = [
                    'mitra_penyewa' => $request->perjanjian_mitra,
                    'peruntukan' => $request->perjanjian_peruntukan,
                    'objek_gedung' => $request->perjanjian_gedung,
                    'nomor_surat' => $request->perjanjian_nomor,
                    'tanggal_surat' => $request->perjanjian_tanggal_penandatanganan,
                    'durasi_sewa' => ($request->jangka_waktu_nilai ?? '') . ' ' . ($request->jangka_waktu_satuan ?? ''),
                    
                    'pihak_pertama_nama' => $request->pihak_pertama_nama,
                    'pihak_pertama_kedudukan' => $request->pihak_pertama_kedudukan,
                    'pihak_pertama_keputusan_nomor' => $request->pihak_pertama_keputusan_nomor,
                    'pihak_pertama_keputusan_tahun' => $request->pihak_pertama_keputusan_tahun,
                    
                    'pihak_kedua_nama' => $request->pihak_kedua_nama,
                    'pihak_kedua_kedudukan' => $request->pihak_kedua_kedudukan,
                    'pihak_kedua_dasar_hukum' => $request->pihak_kedua_dasar_hukum,
                    'pihak_kedua_keputusan_nomor' => $request->pihak_kedua_keputusan_nomor,
                    'pihak_kedua_keputusan_tanggal' => $request->pihak_kedua_keputusan_tanggal,
                    'pihak_kedua_atas_nama' => $request->pihak_kedua_atas_nama,
                    'pihak_kedua_alamat' => $request->pihak_kedua_alamat,
                    'pihak_kedua_kegiatan_usaha' => $request->pihak_kedua_kegiatan_usaha,
                    
                    'objek_luas' => $request->objek_luas,
                    'objek_satuan_luas' => $request->objek_satuan_luas,
                    'objek_letak' => $request->objek_letak,
                    'objek_letak' => $request->objek_letak,
                    'nilai_sewa' => $request->nilai_sewa,
                    'nilai_pendapatan_bukti_bayar' => $request->nilai_pendapatan_bukti_bayar,
                    'periode_mulai' => $request->periode_mulai,
                    'periode_selesai' => $request->periode_selesai,
                ];

                if ($logoPath) {
                    $perjanjianData['logo_penyewa'] = $logoPath;
                }

                // Update or Create PerjanjianSewa record
                $utilization->perjanjianSewa()->updateOrCreate(
                    ['pemanfaatan_id' => $utilization->id],
                    $perjanjianData
                );


                break;

            case 'nodin-ttd':
                $request->validate([
                    'surat_permohonan_ttd_nomor' => 'required|string|max:255',
                    'surat_permohonan_ttd_tanggal' => 'required|date',
                    'surat_permohonan_ttd_tujuan' => 'nullable|string|max:255',
                    'surat_permohonan_ttd_tujuan_bertempat' => 'nullable|string|max:255',
                    'surat_permohonan_ttd_nama_fasilitas' => 'nullable|string|max:255',
                ]);

                $utilization->suratPermohonanTtd()->updateOrCreate(
                    ['bmn_pemanfaatan_id' => $utilization->id],
                    [
                        'nomor_surat' => $request->surat_permohonan_ttd_nomor,
                        'perihal' => $request->surat_permohonan_ttd_perihal,
                        'tanggal_surat' => $request->surat_permohonan_ttd_tanggal,
                        'tujuan_surat' => $request->surat_permohonan_ttd_tujuan,
                        'tujuan_surat_bertempat' => $request->surat_permohonan_ttd_tujuan_bertempat,
                        'nama_fasilitas_bmn' => $request->surat_permohonan_ttd_nama_fasilitas,
                    ]
                );
                break;

            case 'nodin-internal':
                $request->validate([
                    'nodin_internal_nomor_berjenjang_1' => 'nullable|string|max:255',
                    'nodin_internal_nomor_berjenjang_2' => 'nullable|string|max:255',
                    'nodin_internal_nomor_berjenjang_3' => 'nullable|string|max:255',
                    'nodin_internal_perihal' => 'nullable|string|max:255',
                    'nodin_internal_tanggal' => 'required|date',
                    'nodin_internal_mitra' => 'nullable|string|max:255',
                    'nodin_internal_objek_bmn' => 'nullable|string|max:255',
                    'nodin_internal_nomor_perjanjian_induk' => 'nullable|string|max:255',
                    'nodin_internal_nomor_persetujuan_sewa' => 'nullable|string|max:255',
                    'nodin_internal_tanggal_persetujuan_sewa' => 'nullable|date',
                ]);

                $utilization->nodinInternal()->updateOrCreate(
                    ['bmn_pemanfaatan_id' => $utilization->id],
                    [
                        'nomor_berjenjang_1' => $request->nodin_internal_nomor_berjenjang_1,
                        'nomor_berjenjang_2' => $request->nodin_internal_nomor_berjenjang_2,
                        'nomor_berjenjang_3' => $request->nodin_internal_nomor_berjenjang_3,
                        'perihal' => $request->nodin_internal_perihal,
                        'tanggal_surat' => $request->nodin_internal_tanggal,
                        'nama_mitra' => $request->nodin_internal_mitra,
                        'objek_bmn' => $request->nodin_internal_objek_bmn,
                        'nomor_perjanjian_induk' => $request->nodin_internal_nomor_perjanjian_induk,
                        'nomor_persetujuan_sewa' => $request->nodin_internal_nomor_persetujuan_sewa,
                        'tanggal_persetujuan_sewa' => $request->nodin_internal_tanggal_persetujuan_sewa,
                    ]
                );
                break;

            case 'surat-penyampaian-perjanjian':
                $request->validate([
                    'surat_penyampaian_perjanjian_nomor' => 'required|string|max:255',
                    'surat_penyampaian_perjanjian_tanggal' => 'required|date',
                    'surat_penyampaian_perjanjian_nama_mitra' => 'nullable|string|max:255',
                    'surat_penyampaian_perjanjian_alamat_mitra' => 'nullable|string|max:255',
                    'surat_penyampaian_perjanjian_kota_mitra' => 'nullable|string|max:255',
                    'surat_penyampaian_perjanjian_nama_usaha' => 'nullable|string|max:255',
                ]);

                $utilization->suratPenyampaianPerjanjian()->updateOrCreate(
                    ['bmn_pemanfaatan_id' => $utilization->id],
                    [
                        'nomor_surat' => $request->surat_penyampaian_perjanjian_nomor,
                        'tanggal_surat' => $request->surat_penyampaian_perjanjian_tanggal,
                        'nama_mitra' => $request->surat_penyampaian_perjanjian_nama_mitra,
                        'alamat_mitra' => $request->surat_penyampaian_perjanjian_alamat_mitra,
                        'kota_mitra' => $request->surat_penyampaian_perjanjian_kota_mitra,
                        'nama_usaha' => $request->surat_penyampaian_perjanjian_nama_usaha,
                    ]
                );
                break;



            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid document type'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $utilization->fresh([
                'suratKonfirmasi', 
                'nodinBerjenjang', 
                'suratUsulanKpknl', 
                'daftarBmn',
                'perjanjianSewa',
                'nodinPersetujuanKpknl',
                'suratPermohonanTtd',
                'nodinInternal',
                'suratPenyampaianPerjanjian'
            ])
        ]);
    }

    // ========================================================================
    // ADDITIONAL VIEW METHODS
    // ========================================================================

    /**
     * Show review page
     *
     * @return \Illuminate\View\View
     */
    public function review()
    {
        return view('utilization.review');
    }

    /**
     * Show confirmation page
     *
     * @return \Illuminate\View\View
     */
    public function confirmation()
    {
        return view('utilization.confirmation');
    }

    /**
     * Show proposals page
     *
     * @return \Illuminate\View\View
     */
    public function proposals()
    {
        return view('utilization.proposals');
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Calculate duration in months
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    private static function calculateDurationInMonths($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);

        return ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0);
    }

    /**
     * Check if all required fields for a utilization record are filled
     *
     * Determines if a utilization record has all critical fields filled
     * to be considered "complete" for processing.
     *
     * @param BmnPemanfaatan $utilization
     * @return bool
     */
    public function checkCompleteness($utilization)
    {
        // Basic fields
        if (empty($utilization->nama_mitra_penyewa) || 
            empty($utilization->jenis_mitra) || 
            empty($utilization->peruntukan_sewa)) {
            return false;
        }

        // Perjanjian Sewa fields (if applicable)
        if ($utilization->perjanjianSewa) {
            if (empty($utilization->perjanjianSewa->nomor_surat) || 
                empty($utilization->perjanjianSewa->tanggal_surat) ||
                empty($utilization->perjanjianSewa->nilai_sewa)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get auto-populate kasub data for AJAX requests
     * Used for auto-filling kasub fields from previous documents in the workflow
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAutoPopulateKasub(Request $request, $id)
    {
        $utilization = BmnPemanfaatan::with(['suratKonfirmasi', 'nodinBerjenjang'])->findOrFail($id);
        $documentType = $request->query('document_type');

        $kasub = null;

        switch ($documentType) {
            case 'nodin_berjenjang':
                // Get kasub from Surat Konfirmasi
                if ($utilization->suratKonfirmasi) {
                    $kasub = [
                        'nama' => $utilization->suratKonfirmasi->kasub_nama,
                        'nomor' => $utilization->suratKonfirmasi->kasub_nomor,
                    ];
                }
                break;

            case 'surat_usulan_kpknl':
                // Get kasubag from Nodin Berjenjang (priority) or Surat Konfirmasi (fallback)
                if ($utilization->nodinBerjenjang && $utilization->nodinBerjenjang->kasub_nama) {
                    $kasub = [
                        'nama' => $utilization->nodinBerjenjang->kasub_nama,
                        'nomor' => $utilization->nodinBerjenjang->kasub_nomor,
                    ];
                } elseif ($utilization->suratKonfirmasi) {
                    $kasub = [
                        'nama' => $utilization->suratKonfirmasi->kasub_nama,
                        'nomor' => $utilization->suratKonfirmasi->kasub_nomor,
                    ];
                }
                break;
        }

        return response()->json($kasub);
    }
}
