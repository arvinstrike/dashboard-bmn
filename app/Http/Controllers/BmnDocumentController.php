<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmnPemanfaatan;

/**
 * Controller for BMN Document Generation
 *
 * Handles all document generation logic separately from BmnUtilizationController
 * for better separation of concerns and maintainability.
 *
 * @author Dashboard BMN Development Team
 * @version 1.0
 */
class BmnDocumentController extends Controller
{
    /**
     * Show documents generation page
     *
     * Displays page with list of documents that can be generated
     * and their readiness status based on available data.
     *
     * @param int $id - BmnPemanfaatan ID
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);

        // Check which documents have sufficient data
        $documentStatus = $this->checkDocumentReadiness($utilization);

        // Count ready documents
        $readyCount = count(array_filter($documentStatus, function($status) {
            return $status === 'ready';
        }));

        return view('utilization.documents', compact('utilization', 'documentStatus', 'readyCount'));
    }

    /**
     * Generate single document (DOCX)
     *
     * Main entry point for document generation.
     * Validates required fields, loads template, and generates DOCX file.
     *
     * @param int $id - BmnPemanfaatan ID
     * @param string $type - Document type (e.g., 'nodin_berjenjang')
     * @return \Illuminate\Http\Response - Download response or JSON error
     */
    public function generate($id, $type)
    {
        \Log::info("=== DOCUMENT GENERATION REQUEST ===");
        \Log::info("Utilization ID: {$id}");
        \Log::info("Document Type: {$type}");
        
        $utilization = BmnPemanfaatan::findOrFail($id);

        // Validate that required fields exist for this document type
        $validation = $this->validateDocumentData($utilization, $type);
        
        \Log::info("Validation result: " . ($validation['valid'] ? 'VALID' : 'INVALID'));
        if (!$validation['valid']) {
            \Log::warning("Missing fields: " . implode(', ', $validation['missing']));
        }

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap: ' . implode(', ', $validation['missing'])
            ], 400);
        }

        try {
            // Always use generateWordDocument() which has the correct template path mapping
            // The method will handle template loading and fallback to simple document if needed
            \Log::info("Calling generateWordDocument() method");
            return $this->generateWordDocument($utilization, $type);
            
        } catch (\Exception $e) {
            \Log::error("Error in generate() method: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate all documents as ZIP (FUTURE FEATURE)
     *
     * @param int $id - BmnPemanfaatan ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAll($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);

        // TODO: Implement ZIP generation with all 12 documents

        return response()->json([
            'success' => true,
            'message' => 'Generating all documents...'
        ]);
    }

    // ========================================================================
    // PRIVATE HELPER METHODS
    // ========================================================================

    /**
     * Generate Word document with placeholders replaced using TemplateProcessor
     *
     * This is the CORE method for document generation.
     * Uses PHPWord TemplateProcessor to load template and replace ${PLACEHOLDER}
     * with actual data from database.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $type - Document type
     * @return \Illuminate\Http\Response - Download response
     */
    private function generateWordDocument($utilization, $type)
    {
        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Determine the template file based on document type
        // Special handling for template file names that don't match the type exactly
        switch ($type) {
            case 'surat_konfirmasi':
                $templatePath = resource_path("template/surat_konfirmasi_perpanjangan_sewa.docx");
                break;
            case 'surat_konfirmasi_perpanjangan_sewa':
                $templatePath = resource_path("template/surat_konfirmasi_perpanjangan_sewa.docx");
                break;
            case 'nodin_berjenjang':
                $templatePath = resource_path("template/nodin_berjenjang.docx");
                break;
            case 'nodin_konfirmasi':
                $templatePath = resource_path("template/nodin_konfirmasi.docx");
                break;
            case 'surat_usulan_kpknl':
                $templatePath = base_path("resources/template/surat_usulan_perpanjangan_kpknl.docx");
                break;
            // 'sptjm' removed as it is merged into surat_usulan_kpknl
            case 'surat_pernyataan':
                $templatePath = resource_path("template/surat_pernyataan.docx");
                break;
            case 'surat_invoice':
                $templatePath = resource_path("template/surat_invoice.docx");
                break;
            case 'nodin_ttd':
                $templatePath = resource_path("template/nodin_ttd.docx");
                break;
            case 'nodin_internal':
                $templatePath = resource_path("template/nodin_internal.docx");
                break;
            case 'perjanjian':
                $templatePath = resource_path("template/perjanjian_sewa.docx");
                break;
            case 'nodin_persetujuan_kpknl':
                $templatePath = resource_path("template/nodin_persetujuan_kpknl.docx");
                break;
            default:
                // Try with underscore first (nodin_berjenjang.docx)
                $templatePath = resource_path("template/{$type}.docx");
                // If not found, try with dash (nodin-berjenjang.docx)
                if (!file_exists($templatePath)) {
                    $typeWithDash = str_replace('_', '-', $type);
                    $templatePath = resource_path("template/{$typeWithDash}.docx");
                }
                break;
        }

        if (!file_exists($templatePath)) {
            // If no template exists, create a simple document
            \Log::warning("Template not found for type: {$type}");
            \Log::warning("Checked path: {$templatePath}");
            return response("Template not found. Checked path: " . $templatePath, 404);
            // return $this->createSimpleWordDocument($utilization, $type);
        }

        try {
            \Log::info("Loading template from: {$templatePath}");
            \Log::info("Template file exists: " . (file_exists($templatePath) ? 'YES' : 'NO'));
            \Log::info("Template file size: " . filesize($templatePath) . " bytes");
            
            // Use PHPWord TemplateProcessor - the CORRECT way
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            \Log::info("TemplateProcessor created successfully");

            // Get placeholders and their values based on document type
            $placeholders = $this->getPlaceholdersForType($utilization, $type);
            
            \Log::info("Placeholders to replace: " . json_encode(array_keys($placeholders)));

            // Replace all placeholders in the template
            // TemplateProcessor uses ${VARIABLE} format
            foreach ($placeholders as $placeholder => $value) {
                // Remove {{ }} from placeholder name for setValue
                $placeholderName = str_replace(['{{', '}}'], '', $placeholder);

                // Set the value in template
                $templateProcessor->setValue($placeholderName, $value);

                \Log::info("Set template variable {$placeholderName} = {$value}");
            }

            // Generate filename
            $filename = $this->getDocumentFilename($type, $utilization, 'docx');

            // Save to temporary file
            $tempPath = storage_path('app/temp/' . $filename);
            $templateProcessor->saveAs($tempPath);
            
            \Log::info("Document saved to: {$tempPath}");

            // Check if file was created successfully
            if (!file_exists($tempPath)) {
                throw new \Exception("Failed to create temporary document file");
            }

            // Return the file for download
            return response()->download($tempPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error("Error generating Word document: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            \Log::error("Template path was: {$templatePath}");
            
            // For debugging: Return the error directly to the user
            return response("Error generating document: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine(), 500);
            
            // If template processing fails, create a simple document
            // return $this->createSimpleWordDocument($utilization, $type);
        }
    }

    /**
     * Get placeholders mapping for specific document type
     *
     * Maps placeholder names (e.g., 'NO_NODIN') to actual values from database.
     * Each document type has different placeholders.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $type - Document type
     * @return array - Associative array ['{{PLACEHOLDER}}' => 'value']
     */
    private function getPlaceholdersForType($utilization, $type)
    {
        $placeholders = [];

        // Common placeholders for all document types
        $placeholders['{{TANGGAL}}'] = $this->formatDateIndonesia(now());
        $placeholders['{{NAMA_MITRA}}'] = $utilization->nama_mitra_penyewa ?? 'N/A';
        $placeholders['{{JENIS_MITRA}}'] = $utilization->jenis_mitra ?? 'N/A';
        $placeholders['{{JENIS_USULAN}}'] = $utilization->jenis_usulan ?? 'N/A';

        switch ($type) {
            case 'nodin_berjenjang':
                $placeholders['{{NO_NODIN}}'] = $utilization->nodin_berjenjang_nomor ?? 'N/A';

                // Tanggal Surat (single date)
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->nodin_berjenjang_tanggal);

                // Format date range: (10 September 2025 – 9 September 2026)
                if ($utilization->nodin_berjenjang_tanggal_mulai && $utilization->nodin_berjenjang_tanggal_selesai) {
                    $tanggalMulai = $this->formatDateIndonesia($utilization->nodin_berjenjang_tanggal_mulai);
                    $tanggalSelesai = $this->formatDateIndonesia($utilization->nodin_berjenjang_tanggal_selesai);
                    $placeholders['{{TANGGAL_NODIN}}'] = "({$tanggalMulai} – {$tanggalSelesai})";
                } else {
                    $placeholders['{{TANGGAL_NODIN}}'] = 'N/A';
                }

                // Calculate jangka waktu from date range
                $placeholders['{{JANGKA_WAKTU}}'] = $this->calculateJangkaWaktu(
                    $utilization->nodin_berjenjang_tanggal_mulai,
                    $utilization->nodin_berjenjang_tanggal_selesai
                );

                $placeholders['{{MITRA_PERUNTUKAN}}'] = $utilization->nodin_berjenjang_peruntukan ?? 'N/A';
                $placeholders['{{NOMINAL_SURAT}}'] = 'Rp ' . number_format($utilization->nodin_berjenjang_nominal ?? 0, 0, ',', '.');
                break;

            case 'surat_konfirmasi_perpanjangan_sewa':
                $placeholders['{{NOMOR_SURAT}}'] = $utilization->surat_konfirmasi_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal);
                // Use general peruntukan_sewa field instead of surat_konfirmasi_peruntukan_surat
                $placeholders['{{PERUNTUKAN_SURAT}}'] = $utilization->peruntukan_sewa ?? ($utilization->surat_konfirmasi_peruntukan_surat ?? 'N/A');
                // Use surat_konfirmasi_tujuan_surat with fallback to nama_mitra_penyewa
                $placeholders['{{TUJUAN_SURAT}}'] = $utilization->surat_konfirmasi_tujuan_surat ?? ($utilization->nama_mitra_penyewa ?? 'N/A');
                $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_DPR}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_dpr ?? 'N/A';
                $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_MITRA}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_mitra ?? ($utilization->surat_konfirmasi_nomor_perjanjian_lama ?? 'N/A');
                $placeholders['{{TANGGAL_BERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_berakhir);
                $placeholders['{{TANGGAL_KONFIRMASI_TERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir);
                $placeholders['{{NAMA_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nama ?? 'N/A';
                $placeholders['{{NOMOR_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nomor ?? 'N/A';
                break;

            case 'surat_konfirmasi':
                $placeholders['{{NOMOR_SURAT}}'] = $utilization->surat_konfirmasi_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal);
                // Use general peruntukan_sewa field instead of surat_konfirmasi_peruntukan_surat
                $placeholders['{{PERUNTUKAN_SURAT}}'] = $utilization->peruntukan_sewa ?? ($utilization->surat_konfirmasi_peruntukan_surat ?? 'N/A');
                // Use surat_konfirmasi_tujuan_surat with fallback to nama_mitra_penyewa
                $placeholders['{{TUJUAN_SURAT}}'] = $utilization->surat_konfirmasi_tujuan_surat ?? ($utilization->nama_mitra_penyewa ?? 'N/A');
                $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_DPR}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_dpr ?? 'N/A';
                $placeholders['{{NOMOR_PERJANJIAN_SEWA_LAMA_MITRA}}'] = $utilization->surat_konfirmasi_nomor_perjanjian_lama_mitra ?? ($utilization->surat_konfirmasi_nomor_perjanjian_lama ?? 'N/A');
                $placeholders['{{TANGGAL_BERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_berakhir);
                $placeholders['{{TANGGAL_KONFIRMASI_TERAKHIR}}'] = $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir);
                $placeholders['{{NAMA_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nama ?? 'N/A';
                $placeholders['{{NOMOR_KASUB}}'] = $utilization->surat_konfirmasi_kasub_nomor ?? 'N/A';
                break;

            case 'nodin_konfirmasi':
                $placeholders['{{NO_NODIN}}'] = $utilization->nodin_konfirmasi_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_NODIN}}'] = $this->formatDateIndonesia($utilization->nodin_konfirmasi_tanggal);
                $placeholders['{{MITRA_PERUNTUKAN}}'] = $utilization->nodin_konfirmasi_mitra_peruntukan ?? 'N/A';
                break;

            case 'surat_usulan_kpknl':
                $placeholders['NOMOR_SURAT_KPKNL'] = $utilization->surat_usulan_kpknl_nomor ?? 'N/A';
                $placeholders['TANGGAL_SURAT'] = $this->formatDateIndonesia($utilization->surat_usulan_kpknl_tanggal);
                $placeholders['TUJUAN_SURAT'] = $utilization->surat_usulan_kpknl_tujuan ?? 'N/A';
                $placeholders['PERUNTUKAN'] = $utilization->surat_usulan_kpknl_peruntukan ?? 'N/A';
                $placeholders['TANGGAL_BERAKHIR_KPKNL'] = $utilization->surat_usulan_kpknl_tanggal_berakhir ? $this->formatDateIndonesia($utilization->surat_usulan_kpknl_tanggal_berakhir) : 'N/A';
                $placeholders['NAMA_KASUBAG'] = $utilization->surat_usulan_kpknl_nama_kasubag ?? 'N/A';
                $placeholders['NOMOR_KASUBAG'] = $utilization->surat_usulan_kpknl_nomor_kasubag ?? 'N/A';
                $placeholders['NOMOR_SURAT_SPTJM'] = $utilization->sptjm_nomor ?? 'N/A';
                $placeholders['KODE_BARANG'] = $utilization->sptjm_kode_barang ?? 'N/A';
                $placeholders['NUP'] = $utilization->sptjm_nup ?? 'N/A';
                $placeholders['LUAS_BANGUNAN'] = $utilization->sptjm_luasan_sewa ?? 'N/A';
                $placeholders['LOKASI'] = $utilization->sptjm_lokasi_sewa ?? 'N/A';
                break;
            
            // 'sptjm' removed as it is merged into surat_usulan_kpknl

            case 'surat_pernyataan':
                $placeholders['{{NO_SURAT}}'] = $utilization->surat_pernyataan_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_pernyataan_tanggal);
                $placeholders['{{KODE_BARANG}}'] = $utilization->surat_pernyataan_kode_barang ?? 'N/A';
                break;

            case 'surat_invoice':
                $placeholders['{{NO_SURAT}}'] = $utilization->surat_invoice_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_invoice_tanggal);
                $placeholders['{{NOMINAL_SURAT}}'] = 'Rp ' . number_format($utilization->surat_invoice_nominal ?? 0, 0, ',', '.');
                break;

            case 'nodin_ttd':
                $placeholders['{{NO_NODIN}}'] = $utilization->nodin_ttd_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_NODIN}}'] = $this->formatDateIndonesia($utilization->nodin_ttd_tanggal);
                $placeholders['{{JUDUL_PERJANJIAN}}'] = $utilization->nodin_ttd_judul_perjanjian ?? 'N/A';
                break;

            case 'nodin_internal':
                $placeholders['{{NO_NODIN}}'] = $utilization->nodin_internal_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_NODIN}}'] = $this->formatDateIndonesia($utilization->nodin_internal_tanggal);
                $placeholders['{{JUDUL_PERJANJIAN}}'] = $utilization->nodin_internal_judul_perjanjian ?? 'N/A';
                break;

            case 'perjanjian':
                $placeholders['{{NO_PERJANJIAN}}'] = $utilization->perjanjian_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_PERJANJIAN}}'] = $this->formatDateIndonesia($utilization->perjanjian_tanggal_penandatanganan);
                $placeholders['{{JANGKA_WAKTU_NILAI}}'] = $utilization->jangka_waktu_nilai ?? 'N/A';
                $placeholders['{{JANGKA_WAKTU_SATUAN}}'] = $utilization->jangka_waktu_satuan ?? 'N/A';
                break;
        }

        return $placeholders;
    }

    /**
     * Create a simple Word document when template doesn't exist (FALLBACK)
     *
     * Creates plain text document with basic information.
     * This is a fallback when template file is missing.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $type - Document type
     * @return \Illuminate\Http\Response - Download response
     */
    private function createSimpleWordDocument($utilization, $type)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Add document title based on type
        $title = $this->getDocumentTitle($type);
        $section->addTitle($title, 1);

        // Add common information
        $section->addText("Nama Mitra: " . ($utilization->nama_mitra_penyewa ?? 'N/A'));
        $section->addText("Jenis Mitra: " . ($utilization->jenis_mitra ?? 'N/A'));
        $section->addText("Jenis Usulan: " . ($utilization->jenis_usulan ?? 'N/A'));
        $section->addText("Tanggal: " . $this->formatDateIndonesia(now()));

        // Add document-specific information
        switch ($type) {
            case 'nodin_berjenjang':
                $section->addText("Nomor Nodin Berjenjang: " . ($utilization->nodin_berjenjang_nomor ?? 'N/A'));
                $section->addText("Tanggal Nodin Berjenjang: " . $this->formatDateIndonesia($utilization->nodin_berjenjang_tanggal));
                $section->addText("Mitra Peruntukan: " . ($utilization->nodin_berjenjang_peruntukan ?? 'N/A'));
                $section->addText("Nominal: Rp " . number_format($utilization->nodin_berjenjang_nominal ?? 0, 0, ',', '.'));
                break;

            case 'surat_konfirmasi':
                $section->addText("Nomor Surat Konfirmasi: " . ($utilization->surat_konfirmasi_nomor ?? 'N/A'));
                $section->addText("Tanggal Surat Konfirmasi: " . $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal));
                $section->addText("Peruntukan Surat: " . ($utilization->surat_konfirmasi_peruntukan_surat ?? 'N/A'));
                $section->addText("Tujuan Surat: " . ($utilization->surat_konfirmasi_tujuan_surat ?? 'N/A'));
                $section->addText("Nomor Perjanjian Sewa Lama DPR: " . ($utilization->surat_konfirmasi_nomor_perjanjian_lama_dpr ?? 'N/A'));
                $section->addText("Nomor Perjanjian Sewa Lama Mitra: " . ($utilization->surat_konfirmasi_nomor_perjanjian_lama_mitra ?? 'N/A'));
                $section->addText("Tanggal Berakhir: " . $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_berakhir));
                $section->addText("Tanggal Konfirmasi Terakhir: " . $this->formatDateIndonesia($utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir));
                $section->addText("Nama Kasub: " . ($utilization->surat_konfirmasi_kasub_nama ?? 'N/A'));
                $section->addText("Nomor Kasub: " . ($utilization->surat_konfirmasi_kasub_nomor ?? 'N/A'));
                break;

            case 'nodin_konfirmasi':
                $section->addText("Nomor Nodin Konfirmasi: " . ($utilization->nodin_konfirmasi_nomor ?? 'N/A'));
                $section->addText("Tanggal Nodin Konfirmasi: " . $this->formatDateIndonesia($utilization->nodin_konfirmasi_tanggal));
                $section->addText("Mitra Peruntukan: " . ($utilization->nodin_konfirmasi_mitra_peruntukan ?? 'N/A'));
                break;

            case 'surat_usulan_kpknl':
                $section->addText("Nomor Surat Usulan KPKNL: " . ($utilization->surat_usulan_kpknl_nomor ?? 'N/A'));
                $section->addText("Tanggal Surat Usulan KPKNL: " . $this->formatDateIndonesia($utilization->surat_usulan_kpknl_tanggal));
                $section->addText("Tujuan Surat: " . ($utilization->surat_usulan_kpknl_tujuan ?? 'N/A'));
                break;
        }

        // Generate filename
        $filename = $this->getDocumentFilename($type, $utilization, 'docx');

        // Save to temporary file
        $tempPath = storage_path('app/temp/' . $filename);
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        // Return the file for download
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    /**
     * Format date to Indonesian format
     *
     * Converts date to format: "DD Month YYYY" in Indonesian
     * Example: "20 November 2025"
     *
     * @param mixed $date - Date string or Carbon instance
     * @return string - Formatted date or 'N/A' if null
     */
    private function formatDateIndonesia($date)
    {
        if (!$date) return 'N/A';

        $date = \Carbon\Carbon::parse($date);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    /**
     * Calculate jangka waktu (duration) from two dates
     *
     * Calculates the difference between start and end date and returns
     * formatted string like "12 bulan" or "1 tahun 6 bulan"
     *
     * @param mixed $startDate - Start date
     * @param mixed $endDate - End date
     * @return string - Formatted duration or 'N/A' if dates not provided
     */
    private function calculateJangkaWaktu($startDate, $endDate)
    {
        if (!$startDate || !$endDate) return 'N/A';

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        $diffInMonths = $start->diffInMonths($end);
        $years = floor($diffInMonths / 12);
        $months = $diffInMonths % 12;

        if ($years > 0 && $months > 0) {
            return "{$years} tahun {$months} bulan";
        } elseif ($years > 0) {
            return "{$years} tahun";
        } else {
            return "{$months} bulan";
        }
    }

    /**
     * Get document title based on type
     *
     * Returns human-readable title for each document type.
     *
     * @param string $type - Document type
     * @return string - Document title
     */
    private function getDocumentTitle($type)
    {
        $titles = [
            'nodin_berjenjang' => 'Nodin Berjenjang',
            'surat_konfirmasi_perpanjangan_sewa' => 'Surat Konfirmasi Perpanjangan Sewa',
            'surat_konfirmasi' => 'Surat Konfirmasi Perpanjangan Sewa',
            'nodin_konfirmasi' => 'Nodin Konfirmasi Perpanjangan Sewa',
            'surat_usulan_kpknl' => 'Surat Usulan Sewa KPKNL',
            'sptjm' => 'SPTJM (Surat Pernyataan Tanggung Jawab Mutlak)',
            'surat_pernyataan' => 'Surat Pernyataan',
            'surat_invoice' => 'Surat Invoice',
            'nodin_ttd' => 'Nodin TTD (Permohonan TTD Perjanjian)',
            'nodin_internal' => 'Nodin Internal (Berjenjang Internal)',
            'perjanjian' => 'Perjanjian Sewa',
        ];

        return $titles[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Check which documents are ready to be generated
     *
     * Checks if all required fields are filled for each document type.
     * Returns status: 'ready' or 'missing'
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @return array - Status for each document type
     */
    private function checkDocumentReadiness($utilization)
    {
        return [
            'surat_konfirmasi_perpanjangan_sewa' => $this->checkDocumentFields($utilization, [
                'surat_konfirmasi_nomor', 'surat_konfirmasi_tanggal'
                // Note: Other fields are optional/have fallbacks
            ]),
            'nodin_konfirmasi' => $this->checkDocumentFields($utilization, [
                'nodin_konfirmasi_nomor', 'nodin_konfirmasi_tanggal'
            ]),

            // Usulan
            'nodin_berjenjang' => $this->checkDocumentFields($utilization, [
                'nodin_berjenjang_nomor', 'nodin_berjenjang_tanggal_mulai', 'nodin_berjenjang_tanggal_selesai', 'nodin_berjenjang_peruntukan'
            ]),
            'surat_usulan_kpknl' => $this->checkDocumentFields($utilization, [
                'surat_usulan_kpknl_nomor', 'surat_usulan_kpknl_tanggal',
                'sptjm_nomor', 'sptjm_tanggal', 'sptjm_kode_barang'
            ]),
            // 'sptjm' removed as it is merged into surat_usulan_kpknl
            'surat_pernyataan' => $this->checkDocumentFields($utilization, [
                'surat_pernyataan_nomor', 'surat_pernyataan_tanggal'
            ]),
            'daftar_bmn' => $this->checkDocumentFields($utilization, [
                'daftar_bmn'
            ]),

            // Penilaian KPKNL
            'nodin_persetujuan_kpknl' => $this->checkDocumentFields($utilization, [
                'nodin_persetujuan_kpknl_nomor', 'nodin_persetujuan_kpknl_tanggal'
            ]),
            'surat_invoice' => $this->checkDocumentFields($utilization, [
                'surat_invoice_nomor', 'surat_invoice_tanggal', 'surat_invoice_nominal'
            ]),

            // Perjanjian
            'nodin_ttd' => $this->checkDocumentFields($utilization, [
                'nodin_ttd_nomor', 'nodin_ttd_tanggal'
            ]),
            'nodin_internal' => $this->checkDocumentFields($utilization, [
                'nodin_internal_nomor', 'nodin_internal_tanggal'
            ]),
            'perjanjian' => $this->checkDocumentFields($utilization, [
                'perjanjian_nomor', 'perjanjian_tanggal_penandatanganan'
            ]),
        ];
    }

    /**
     * Check if required fields exist for document
     *
     * Helper method to check if all required fields have values.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param array $fields - Required field names
     * @return string - 'ready' if all filled, 'missing' if any empty
     */
    private function checkDocumentFields($utilization, $fields)
    {
        foreach ($fields as $field) {
            if (empty($utilization->{$field})) {
                return 'missing'; // Missing required data
            }
        }
        return 'ready'; // All fields present
    }

    /**
     * Validate document data before generation
     *
     * Validates that all required fields for a document type are filled.
     * Returns validation result with list of missing fields.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $type - Document type
     * @return array - ['valid' => bool, 'missing' => array]
     */
    private function validateDocumentData($utilization, $type)
    {
        $requiredFieldsMap = [
            'surat_konfirmasi_perpanjangan_sewa' => ['surat_konfirmasi_nomor', 'surat_konfirmasi_tanggal'],
            'nodin_konfirmasi' => ['nodin_konfirmasi_nomor', 'nodin_konfirmasi_tanggal'],
            'nodin_berjenjang' => ['nodin_berjenjang_nomor', 'nodin_berjenjang_tanggal_mulai', 'nodin_berjenjang_tanggal_selesai', 'nodin_berjenjang_peruntukan'],
            'surat_usulan_kpknl' => ['surat_usulan_kpknl_nomor', 'surat_usulan_kpknl_tanggal', 'sptjm_nomor', 'sptjm_tanggal', 'sptjm_kode_barang'],
            // 'sptjm' removed as it is merged into surat_usulan_kpknl
            'surat_pernyataan' => ['surat_pernyataan_nomor', 'surat_pernyataan_tanggal'],
            'daftar_bmn' => ['daftar_bmn'],
            'nodin_persetujuan_kpknl' => ['nodin_persetujuan_kpknl_nomor', 'nodin_persetujuan_kpknl_tanggal'],
            'surat_invoice' => ['surat_invoice_nomor', 'surat_invoice_tanggal', 'surat_invoice_nominal'],
            'nodin_ttd' => ['nodin_ttd_nomor', 'nodin_ttd_tanggal'],
            'nodin_internal' => ['nodin_internal_nomor', 'nodin_internal_tanggal'],
            'perjanjian' => ['perjanjian_nomor', 'perjanjian_tanggal_penandatanganan'],
        ];

        if (!isset($requiredFieldsMap[$type])) {
            return ['valid' => false, 'missing' => ['Unknown document type']];
        }

        $missing = [];
        foreach ($requiredFieldsMap[$type] as $field) {
            if (empty($utilization->{$field})) {
                $missing[] = $field;
            }
        }

        return [
            'valid' => empty($missing),
            'missing' => $missing
        ];
    }

    /**
     * Get filename for document download
     *
     * Generates filename based on document type and mitra name.
     * Format: {DocumentType}_{MitraName}.{extension}
     *
     * @param string $type - Document type
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $extension - File extension (default: 'pdf')
     * @return string - Generated filename
     */
    private function getDocumentFilename($type, $utilization, $extension = 'pdf')
    {
        $typeNames = [
            'surat_konfirmasi_perpanjangan_sewa' => 'Surat_Konfirmasi_Perpanjangan_Sewa',
            'surat_konfirmasi' => 'Surat_Konfirmasi_Perpanjangan_Sewa',
            'nodin_konfirmasi' => 'Nodin_Konfirmasi',
            'nodin_berjenjang' => 'Nodin_Berjenjang',
            'surat_usulan_kpknl' => 'Surat_Usulan_KPKNL',
            'sptjm' => 'SPTJM',
            'surat_pernyataan' => 'Surat_Pernyataan',
            'daftar_bmn' => 'Daftar_BMN',
            'nodin_persetujuan_kpknl' => 'Nodin_Persetujuan_KPKNL',
            'surat_invoice' => 'Surat_Invoice',
            'nodin_ttd' => 'Nodin_TTD',
            'nodin_internal' => 'Nodin_Internal',
            'perjanjian' => 'Perjanjian_Sewa',
        ];

        $docName = $typeNames[$type] ?? $type;
        $mitraName = str_replace(' ', '_', $utilization->nama_mitra_penyewa);

        return "{$docName}_{$mitraName}.{$extension}";
    }
}
