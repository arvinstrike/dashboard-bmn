<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPemanfaatan;
use App\Models\Bagian;

class BmnUtilizationController extends Controller
{
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

        return view('bmn.utilization_dashboard', compact('utilizationData', 'bagianList'));
    }
    
    public function show($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);
        return response()->json(['success' => true, 'data' => $utilization]);
    }
    
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
    
    public function destroy($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);
        $utilization->delete();

        return response()->json(['success' => true]);
    }

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
    
    // Additional methods for review, confirmation, and proposals
    public function review()
    {
        return view('bmn.utilization_review');
    }
    
    public function confirmation()
    {
        return view('bmn.utilization_confirmation');
    }
    
    public function proposals()
    {
        return view('bmn.utilization_proposals');
    }
    
    // Helper method to calculate duration in months
    private static function calculateDurationInMonths($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);
        
        return ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0);
    }

    /**
     * Check if all required fields for a utilization record are filled.
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