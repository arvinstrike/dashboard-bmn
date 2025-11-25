<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPemanfaatan;
use App\Models\Bagian;

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
        // Get all utilization data (eager loading)
        // Ordered by id (since there's no created_at column)
        // Note: pembayaran relationship disabled - table not yet implemented
        $utilizationData = BmnPemanfaatan::orderBy('id', 'desc')
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
        $utilization = BmnPemanfaatan::findOrFail($id);
        return response()->json(['success' => true, 'data' => $utilization]);
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

            // Tab 4: Dokumen Final
            'dokumen_bukti_bayar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dokumen_perjanjian' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'perjanjian_logo_penyewa' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        $utilization = BmnPemanfaatan::findOrFail($id);

        // File fields array
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

        $updateData = [];
        $uploadedFiles = [];

        // Handle file uploads
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . $field . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/pemanfaatan', $fileName, 'public');
                $updateData[$field] = $filePath;
                $uploadedFiles[] = $field;
            }
        }

        if (!empty($updateData)) {
            $utilization->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' dokumen berhasil diunggah',
            'uploaded_files' => $uploadedFiles,
            'data' => $utilization->fresh()
        ]);
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
                $request->validate([
                    'surat_konfirmasi_nomor' => 'required|string|max:255',
                    'surat_konfirmasi_tanggal' => 'required|date',
                    'surat_konfirmasi_tujuan_surat' => 'nullable|string|max:255',
                    'surat_konfirmasi_peruntukan_surat' => 'nullable|string|max:255',
                    'surat_konfirmasi_nomor_perjanjian_lama_dpr' => 'nullable|string|max:255',
                    'surat_konfirmasi_nomor_perjanjian_lama_mitra' => 'nullable|string|max:255',
                    'surat_konfirmasi_tanggal_berakhir' => 'nullable|date',
                    'surat_konfirmasi_tanggal_konfirmasi_terakhir' => 'nullable|date',
                    'surat_konfirmasi_kasub_nama' => 'nullable|string|max:255',
                    'surat_konfirmasi_kasub_nomor' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'surat_konfirmasi_nomor' => $request->surat_konfirmasi_nomor,
                    'surat_konfirmasi_tanggal' => $request->surat_konfirmasi_tanggal,
                    'surat_konfirmasi_tujuan_surat' => $request->surat_konfirmasi_tujuan_surat,
                    'surat_konfirmasi_peruntukan_surat' => $request->surat_konfirmasi_peruntukan_surat,
                    'surat_konfirmasi_nomor_perjanjian_lama_dpr' => $request->surat_konfirmasi_nomor_perjanjian_lama_dpr,
                    'surat_konfirmasi_nomor_perjanjian_lama_mitra' => $request->surat_konfirmasi_nomor_perjanjian_lama_mitra,
                    'surat_konfirmasi_tanggal_berakhir' => $request->surat_konfirmasi_tanggal_berakhir,
                    'surat_konfirmasi_tanggal_konfirmasi_terakhir' => $request->surat_konfirmasi_tanggal_konfirmasi_terakhir,
                    'surat_konfirmasi_kasub_nama' => $request->surat_konfirmasi_kasub_nama,
                    'surat_konfirmasi_kasub_nomor' => $request->surat_konfirmasi_kasub_nomor,
                ]);
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
                $request->validate([
                    'nodin_berjenjang_nomor' => 'required|string|max:100',
                    'nodin_berjenjang_tanggal' => 'required|date',
                    'nodin_berjenjang_tanggal_mulai' => 'required|date',
                    'nodin_berjenjang_tanggal_selesai' => 'required|date',
                    'nodin_berjenjang_mitra' => 'nullable|string|max:255',
                    'nodin_berjenjang_peruntukan' => 'nullable|string|max:255',
                    'nodin_berjenjang_nominal' => 'nullable|numeric',
                ]);

                $utilization->update([
                    'nodin_berjenjang_nomor' => $request->nodin_berjenjang_nomor,
                    'nodin_berjenjang_tanggal' => $request->nodin_berjenjang_tanggal,
                    'nodin_berjenjang_tanggal_mulai' => $request->nodin_berjenjang_tanggal_mulai,
                    'nodin_berjenjang_tanggal_selesai' => $request->nodin_berjenjang_tanggal_selesai,
                    'nodin_berjenjang_mitra' => $request->nodin_berjenjang_mitra,
                    'nodin_berjenjang_peruntukan' => $request->nodin_berjenjang_peruntukan,
                    'nodin_berjenjang_nominal' => $request->nodin_berjenjang_nominal,
                ]);
                break;

            case 'surat-usulan-kpknl':
                $request->validate([
                    'surat_usulan_kpknl_nomor' => 'required|string|max:255',
                    'surat_usulan_kpknl_tanggal' => 'required|date',
                    'surat_usulan_kpknl_peruntukan' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_tanggal_berakhir' => 'nullable|date',
                    'surat_usulan_kpknl_nama_kasubag' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_nomor_kasubag' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_hal' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_tujuan' => 'nullable|string|max:255',
                    'surat_usulan_kpknl_isi' => 'nullable|string',
                    // SPTJM Fields
                    'sptjm_nomor' => 'nullable|string|max:255',
                    'sptjm_tanggal' => 'nullable|date',
                    'sptjm_kode_barang' => 'nullable|string|max:255',
                    'sptjm_nup' => 'nullable|string|max:255',
                    'sptjm_luasan_sewa' => 'nullable|string|max:255',
                    'sptjm_lokasi_sewa' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'surat_usulan_kpknl_nomor' => $request->surat_usulan_kpknl_nomor,
                    'surat_usulan_kpknl_tanggal' => $request->surat_usulan_kpknl_tanggal,
                    'surat_usulan_kpknl_peruntukan' => $request->surat_usulan_kpknl_peruntukan,
                    'surat_usulan_kpknl_tanggal_berakhir' => $request->surat_usulan_kpknl_tanggal_berakhir,
                    'surat_usulan_kpknl_nama_kasubag' => $request->surat_usulan_kpknl_nama_kasubag,
                    'surat_usulan_kpknl_nomor_kasubag' => $request->surat_usulan_kpknl_nomor_kasubag,
                    'surat_usulan_kpknl_hal' => $request->surat_usulan_kpknl_hal,
                    'surat_usulan_kpknl_tujuan' => $request->surat_usulan_kpknl_tujuan,
                    'surat_usulan_kpknl_isi' => $request->surat_usulan_kpknl_isi,
                    // SPTJM Fields
                    'sptjm_nomor' => $request->sptjm_nomor,
                    'sptjm_tanggal' => $request->sptjm_tanggal,
                    'sptjm_kode_barang' => $request->sptjm_kode_barang,
                    'sptjm_nup' => $request->sptjm_nup,
                    'sptjm_luasan_sewa' => $request->sptjm_luasan_sewa,
                    'sptjm_lokasi_sewa' => $request->sptjm_lokasi_sewa,
                ]);
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
                $request->validate([
                    'daftar_bmn' => 'required|json',
                ]);

                $utilization->update([
                    'daftar_bmn' => $request->daftar_bmn,
                ]);
                break;

            case 'nodin-persetujuan-kpknl':
                $request->validate([
                    'nodin_persetujuan_kpknl_nomor' => 'required|string|max:255',
                    'nodin_persetujuan_kpknl_tanggal' => 'required|date',
                    'nodin_persetujuan_kpknl_tujuan' => 'nullable|string|max:255',
                    'nodin_persetujuan_kpknl_nomor_persetujuan' => 'nullable|string|max:255',
                    'nodin_persetujuan_kpknl_tanggal_persetujuan' => 'nullable|date',
                    'nodin_persetujuan_kpknl_periode_sewa' => 'nullable|string|max:255',
                    'nodin_persetujuan_kpknl_nominal' => 'nullable|numeric',
                    'nodin_persetujuan_kpknl_mitra' => 'nullable|string|max:255',
                    'nodin_persetujuan_kpknl_kasub' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'nodin_persetujuan_kpknl_nomor' => $request->nodin_persetujuan_kpknl_nomor,
                    'nodin_persetujuan_kpknl_tanggal' => $request->nodin_persetujuan_kpknl_tanggal,
                    'nodin_persetujuan_kpknl_tujuan' => $request->nodin_persetujuan_kpknl_tujuan,
                    'nodin_persetujuan_kpknl_nomor_persetujuan' => $request->nodin_persetujuan_kpknl_nomor_persetujuan,
                    'nodin_persetujuan_kpknl_tanggal_persetujuan' => $request->nodin_persetujuan_kpknl_tanggal_persetujuan,
                    'nodin_persetujuan_kpknl_periode_sewa' => $request->nodin_persetujuan_kpknl_periode_sewa,
                    'nodin_persetujuan_kpknl_nominal' => $request->nodin_persetujuan_kpknl_nominal,
                    'nodin_persetujuan_kpknl_mitra' => $request->nodin_persetujuan_kpknl_mitra,
                    'nodin_persetujuan_kpknl_kasub' => $request->nodin_persetujuan_kpknl_kasub,
                ]);
                break;

            case 'surat-invoice':
                $request->validate([
                    'surat_invoice_nomor' => 'required|string|max:255',
                    'surat_invoice_tanggal' => 'required|date',
                    'surat_invoice_tujuan' => 'nullable|string|max:255',
                    'surat_invoice_nomor_persetujuan' => 'nullable|string|max:255',
                    'surat_invoice_tanggal_persetujuan' => 'nullable|date',
                    'surat_invoice_periode_sewa' => 'nullable|string|max:255',
                    'surat_invoice_nominal' => 'nullable|numeric',
                    'surat_invoice_mitra' => 'nullable|string|max:255',
                    'surat_invoice_kasub' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'surat_invoice_nomor' => $request->surat_invoice_nomor,
                    'surat_invoice_tanggal' => $request->surat_invoice_tanggal,
                    'surat_invoice_tujuan' => $request->surat_invoice_tujuan,
                    'surat_invoice_nomor_persetujuan' => $request->surat_invoice_nomor_persetujuan,
                    'surat_invoice_tanggal_persetujuan' => $request->surat_invoice_tanggal_persetujuan,
                    'surat_invoice_periode_sewa' => $request->surat_invoice_periode_sewa,
                    'surat_invoice_nominal' => $request->surat_invoice_nominal,
                    'surat_invoice_mitra' => $request->surat_invoice_mitra,
                    'surat_invoice_kasub' => $request->surat_invoice_kasub,
                ]);
                break;

            case 'perjanjian-sewa':
                $request->validate([
                    'perjanjian_mitra' => 'nullable|string|max:255',
                    'perjanjian_peruntukan' => 'nullable|string|max:255',
                    'perjanjian_gedung' => 'nullable|string|max:255',
                    'perjanjian_hari_tanggal' => 'nullable|string|max:255',
                    'perjanjian_detail_pihak_kedua' => 'nullable|string',
                    'perjanjian_nomor' => 'required|string|max:255',
                    'perjanjian_tanggal_penandatanganan' => 'required|date',
                    'jangka_waktu_nilai' => 'nullable|integer',
                    'jangka_waktu_satuan' => 'nullable|string|max:50',
                ]);

                $utilization->update([
                    'perjanjian_mitra' => $request->perjanjian_mitra,
                    'perjanjian_peruntukan' => $request->perjanjian_peruntukan,
                    'perjanjian_gedung' => $request->perjanjian_gedung,
                    'perjanjian_hari_tanggal' => $request->perjanjian_hari_tanggal,
                    'perjanjian_detail_pihak_kedua' => $request->perjanjian_detail_pihak_kedua,
                    'perjanjian_nomor' => $request->perjanjian_nomor,
                    'perjanjian_tanggal_penandatanganan' => $request->perjanjian_tanggal_penandatanganan,
                    'jangka_waktu_nilai' => $request->jangka_waktu_nilai,
                    'jangka_waktu_satuan' => $request->jangka_waktu_satuan,
                ]);
                break;

            case 'nodin-ttd':
                $request->validate([
                    'nodin_ttd_nomor' => 'required|string|max:255',
                    'nodin_ttd_tanggal' => 'required|date',
                    'nodin_ttd_tujuan' => 'nullable|string|max:255',
                    'nodin_ttd_mitra' => 'nullable|string|max:255',
                    'nodin_ttd_judul_perjanjian' => 'nullable|string|max:255',
                ]);

                $utilization->update([
                    'nodin_ttd_nomor' => $request->nodin_ttd_nomor,
                    'nodin_ttd_tanggal' => $request->nodin_ttd_tanggal,
                    'nodin_ttd_tujuan' => $request->nodin_ttd_tujuan,
                    'nodin_ttd_mitra' => $request->nodin_ttd_mitra,
                    'nodin_ttd_judul_perjanjian' => $request->nodin_ttd_judul_perjanjian,
                ]);
                break;

            case 'nodin-internal':
                $request->validate([
                    'nodin_internal_nomor' => 'required|string|max:255',
                    'nodin_internal_tanggal' => 'required|date',
                    'nodin_internal_mitra' => 'nullable|string|max:255',
                    'nodin_internal_judul_perjanjian' => 'nullable|string|max:255',
                    'nodin_internal_nomor_perjanjian' => 'nullable|string|max:255',
                    'nodin_internal_detail_persetujuan' => 'nullable|string',
                ]);

                $utilization->update([
                    'nodin_internal_nomor' => $request->nodin_internal_nomor,
                    'nodin_internal_tanggal' => $request->nodin_internal_tanggal,
                    'nodin_internal_mitra' => $request->nodin_internal_mitra,
                    'nodin_internal_judul_perjanjian' => $request->nodin_internal_judul_perjanjian,
                    'nodin_internal_nomor_perjanjian' => $request->nodin_internal_nomor_perjanjian,
                    'nodin_internal_detail_persetujuan' => $request->nodin_internal_detail_persetujuan,
                ]);
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
            'data' => $utilization->fresh()
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
    private function checkCompleteness(BmnPemanfaatan $utilization)
    {
        // Critical fields that MUST be filled for a record to be considered complete
        $requiredFields = [
            // Tahap 1: Informasi Penyewa (MUST)
            'pic_penyewa',
            'nomor_hp_pic_penyewa',
            'pic_administrasi_bmn',
            'nomor_pic_administrasi_bmn',
            'nama_mitra_penyewa',
            'jenis_mitra',
            'jenis_usulan',

            // Tab 2: Konfirmasi (Key fields)
            'nodin_konfirmasi_nomor',
            'surat_konfirmasi_nomor',

            // Tab 3: Usulan (Key fields)
            'surat_usulan_kpknl_nomor',

            // Tab 4: Penilaian KPKNL (Key fields)
            'nodin_persetujuan_kpknl_nomor',

            // Tab 5: Perjanjian (Key fields)
            'perjanjian_nomor',
            'perjanjian_tanggal_penandatanganan',
            'dokumen_perjanjian',
        ];

        foreach ($requiredFields as $field) {
            if (empty($utilization->{$field})) {
                return false; // If any field is empty, it's not complete
            }
        }

        return true; // All critical fields are filled
    }
}
