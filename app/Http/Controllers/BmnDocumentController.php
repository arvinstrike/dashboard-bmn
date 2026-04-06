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

        // Define tab mapping for "Lengkapi" buttons
        $docTabMapping = [
            'surat_konfirmasi_perpanjangan_sewa' => 'tab1-tab',
            'nodin_konfirmasi' => 'tab1-tab',
            'nodin_berjenjang' => 'tab2-tab',
            'surat_usulan_kpknl' => 'tab2-tab',
            'surat_pernyataan' => 'tab2-tab',
            'daftar_bmn' => 'tab2-tab',
            'nodin_persetujuan_kpknl' => 'tab3-tab',
            'surat_invoice' => 'tab3-tab',
            'nodin_ttd' => 'tab4-tab',
            'nodin_internal' => 'tab4-tab',
            'surat_penyampaian_perjanjian' => 'tab4-tab',
            'perjanjian' => 'tab4-tab',
        ];

        return view('utilization.documents', compact('utilization', 'documentStatus', 'readyCount', 'docTabMapping'));
    }

    /**
     * Get document status as JSON for AJAX updates
     *
     * Returns the current document status and ready count without full page reload.
     * Used for auto-updating UI after saving document data.
     *
     * @param int $id - BmnPemanfaatan ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentStatus($id)
    {
        // Reload data from database to get fresh status
        $utilization = BmnPemanfaatan::with([
            'perjanjianSewa',
            'nodinBerjenjang',
            'suratKonfirmasi',
            'suratUsulanKpknl',
            'nodinPersetujuanKpknl',
            'daftarBmn',
            'suratPermohonanTtd',
            'nodinInternal',
            'suratPenyampaianPerjanjian'
        ])->findOrFail($id);

        // Check which documents have sufficient data
        $documentStatus = $this->checkDocumentReadiness($utilization);

        // Count ready documents
        $readyCount = count(array_filter($documentStatus, function($status) {
            return $status === 'ready';
        }));

        return response()->json([
            'success' => true,
            'documentStatus' => $documentStatus,
            'readyCount' => $readyCount
        ]);
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
            $filePath = null;

            // Special handling for Excel documents
            if ($type === 'daftar_bmn') {
                \Log::info("Calling createExcelDocument() method");
                $filePath = $this->createExcelDocument($utilization, $type);
            } else {
                // Always use createWordDocument() which has the correct template path mapping
                \Log::info("Calling createWordDocument() method");
                $filePath = $this->createWordDocument($utilization, $type);
            }

            if ($filePath && file_exists($filePath)) {
                return response()->download($filePath)->deleteFileAfterSend(true);
            } else {
                throw new \Exception("Gagal membuat file dokumen.");
            }
            
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
    /**
     * Generate all documents as ZIP
     *
     * @param int $id - BmnPemanfaatan ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAll($id)
    {
        $utilization = BmnPemanfaatan::findOrFail($id);
        $types = [
            'surat_konfirmasi_perpanjangan_sewa',
            'nodin_konfirmasi',
            'nodin_berjenjang',
            'surat_usulan_kpknl',
            'surat_pernyataan',
            'daftar_bmn',
            'nodin_persetujuan_kpknl',
            'surat_invoice',
            'nodin_ttd',
            'nodin_internal',
            'perjanjian'
        ];

        $zipFileName = 'Dokumen_Pemanfaatan_' . str_replace(' ', '_', $utilization->nama_mitra_penyewa) . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp dir exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive;
        $filesToAdd = [];
        $generatedCount = 0;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($types as $type) {
                // Check if document is ready
                $validation = $this->validateDocumentData($utilization, $type);
                
                if ($validation['valid']) {
                    try {
                        $filePath = null;
                        if ($type === 'daftar_bmn') {
                            $filePath = $this->createExcelDocument($utilization, $type);
                        } else {
                            $filePath = $this->createWordDocument($utilization, $type);
                        }
                        
                        if ($filePath && file_exists($filePath)) {
                            $zip->addFile($filePath, basename($filePath));
                            $filesToAdd[] = $filePath;
                            $generatedCount++;
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to generate $type for ZIP: " . $e->getMessage());
                        // Continue to next document
                    }
                }
            }
            $zip->close();

            // Clean up temporary files
            foreach ($filesToAdd as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            if ($generatedCount > 0 && file_exists($zipPath)) {
                // Return URL to download the ZIP
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil membuat ZIP dengan $generatedCount dokumen.",
                    'download_url' => route('bmn.utilization.download_temp', ['filename' => $zipFileName])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada dokumen yang siap untuk digenerate.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file ZIP.'
            ], 500);
        }
    }

    /**
     * Download temporary file
     */
    public function downloadTemp($filename)
    {
        $path = storage_path('app/temp/' . $filename);
        if (file_exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }
        abort(404);
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
    /**
     * Generate Word document with placeholders replaced using TemplateProcessor
     *
     * This is the CORE method for document generation.
     * Uses PHPWord TemplateProcessor to load template and replace ${PLACEHOLDER}
     * with actual data from database.
     *
     * @param BmnPemanfaatan $utilization - Data model
     * @param string $type - Document type
     * @return string - Path to generated file
     */
    private function createWordDocument($utilization, $type)
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
                $templatePath = resource_path("template/template_surat_ttd.docx");
                break;
            case 'nodin_internal':
                $templatePath = resource_path("template/template_nodin_internal.docx");
                break;
            case 'surat_penyampaian_perjanjian':
                $templatePath = resource_path("template/template_penyampaian_perjanjian.docx");
                break;
            case 'perjanjian':
                $templatePath = resource_path("template/template_perjanjian.docx");
                break;
            case 'nodin_persetujuan_kpknl':
                $templatePath = resource_path("template/nodin_usulan_penyampaian_persetujuan_kpknl.docx");
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
            throw new \Exception("Template not found: " . basename($templatePath));
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
            if ($type === 'perjanjian') {
                // Dynamic replacement for Perjanjian
                $vars = $templateProcessor->getVariables();
                \Log::info("Found variables in template: " . implode(', ', $vars));
                
                $allData = $this->getAllAvailableData($utilization);
                
                foreach ($vars as $var) {
                    if (array_key_exists($var, $allData)) {
                        $templateProcessor->setValue($var, $allData[$var]);
                        \Log::info("Replaced \${$var} with: " . substr($allData[$var], 0, 50));
                    } else {
                        \Log::warning("Variable \${$var} found in template but no data available");
                    }
                }

                // Special handling for Perjanjian Sewa Logo - SHAPE REPLACEMENT
                // MOVED TO AFTER SAVE
                // See below...
            } else {
                // Standard logic for other documents
                $placeholders = $this->getPlaceholdersForType($utilization, $type);
                
                \Log::info("Placeholders to replace: " . json_encode(array_keys($placeholders)));

                // Replace all placeholders in the template
                foreach ($placeholders as $placeholder => $value) {
                    // Remove {{ }} from placeholder name for setValue
                    $placeholderName = str_replace(['{{', '}}'], '', $placeholder);

                    // Set the value in template
                    $templateProcessor->setValue($placeholderName, $value);

                    \Log::info("Set template variable {$placeholderName} = {$value}");
                }
            }

            // Generate filename
            $filename = $this->getDocumentFilename($type, $utilization, 'docx');

            // Save to temporary file
            $tempPath = storage_path('app/temp/' . $filename);
            $templateProcessor->saveAs($tempPath);
            
            \Log::info("Document saved to: {$tempPath}");

            // Special handling for Perjanjian Sewa Logo - SHAPE REPLACEMENT
            // We do this AFTER saving because TemplateProcessor overwrites XML changes on saveAs
            if ($type === 'perjanjian' && $utilization->perjanjianSewa && $utilization->perjanjianSewa->logo_penyewa) {
                $logoPath = storage_path('app/public/' . $utilization->perjanjianSewa->logo_penyewa);
                if (file_exists($logoPath)) {
                    $this->replaceShapeWithImage($tempPath, $logoPath);
                } else {
                    \Log::warning('Logo file not found for shape replacement', ['path' => $logoPath]);
                }
            }

            // Check if file was created successfully
            if (!file_exists($tempPath)) {
                throw new \Exception("Failed to create temporary document file");
            }

            // Return the file path
            return $tempPath;

        } catch (\Exception $e) {
            \Log::error("Error generating Word document: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            \Log::error("Template path was: {$templatePath}");
            
            throw $e;
        }
    }

    /**
     * Generate Excel document using PhpSpreadsheet
     *
     * @param BmnPemanfaatan $utilization
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    /**
     * Generate Excel document using PhpSpreadsheet
     *
     * @param BmnPemanfaatan $utilization
     * @param string $type
     * @return string - Path to generated file
     */
    private function createExcelDocument($utilization, $type)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Document number in top-right (dynamic from database)
    $documentNumber = $utilization->daftar_bmn_nomor_surat ?? 'Nomor: (Belum diisi)';
    if ($utilization->daftar_bmn_nomor_surat && !str_starts_with($utilization->daftar_bmn_nomor_surat, 'Nomor:')) {
        $documentNumber = 'Nomor: ' . $utilization->daftar_bmn_nomor_surat;
    }
    $sheet->setCellValue('M1', $documentNumber);
    $sheet->getStyle('M1')->applyFromArray([
        'font' => ['size' => 10],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ],
    ]);

    // Title Line 1 (centered, bold)
    $title1 = "DAFTAR BARANG MILIK NEGARA PADA SEKRETARIAT JENDERAL DPR RI";
    $sheet->mergeCells('A2:O2');
    $sheet->setCellValue('A2', $title1);
    $sheet->getStyle('A2')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Title Line 2 (centered, bold)
    $title2 = "YANG DIUSULKAN SEWA UNTUK BESARAN SEWA";
    $sheet->mergeCells('A3:O3');
    $sheet->setCellValue('A3', $title2);
    $sheet->getStyle('A3')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Add spacing row
    $sheet->getRowDimension(4)->setRowHeight(5);

    // Set Headers (starting from row 5)
    $headerRow = 5;
    $headers = [
        'No', 'Kode Barang', 'NUP', 'Jenis BMN (SIMAK)', 'Luas Keseluruhan (m2)',
        'Nilai Perolehan (Rp)', 'Dicatat di SIMAK', 'Objek Sewa', 'Lokasi',
        'Penyewa', 'Peruntukan', 'Usulan Luas (m2)', 'Usulan Jangka Waktu (tahun)',
        'Usulan Periodesitas', 'Usulan Besaran Sewa (Rp)'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . $headerRow, $header);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Style Header Row (NO BACKGROUND COLOR, just bold black text)
    $lastCol = chr(ord('A') + count($headers) - 1);
    $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'], // Black
            'size' => 11,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    // Set row height for header
    $sheet->getRowDimension($headerRow)->setRowHeight(25);

    // Add Data (starting from row 6)
    $row = $headerRow + 1;
    foreach ($utilization->daftarBmn as $index => $bmn) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValueExplicit('B' . $row, $bmn->kode_barang, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C' . $row, $bmn->nup, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('D' . $row, $bmn->jenis_bmn);
        $sheet->setCellValue('E' . $row, $bmn->luas_keseluruhan);
        $sheet->setCellValue('F' . $row, $bmn->nilai_perolehan);
        $sheet->setCellValue('G' . $row, $bmn->dicatat_di_simak);
        $sheet->setCellValue('H' . $row, $bmn->objek_sewa);
        $sheet->setCellValue('I' . $row, $bmn->lokasi);
        $sheet->setCellValue('J' . $row, $bmn->penyewa);
        $sheet->setCellValue('K' . $row, $bmn->peruntukan);
        $sheet->setCellValue('L' . $row, $bmn->usulan_luas_sewa);
        // Format usulan_jangka_waktu as "integer + tahun" in Excel
        $jangkaWaktu = $bmn->usulan_jangka_waktu ? $bmn->usulan_jangka_waktu . ' tahun' : '';
        $sheet->setCellValue('M' . $row, $jangkaWaktu);
        $sheet->setCellValue('N' . $row, $bmn->usulan_periodesitas);
        $sheet->setCellValue('O' . $row, $bmn->usulan_besaran_sewa);
        
        // Format Currency Columns (Indonesian format: Rp 1.000.000,00)
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Alternating row colors for better readability
        $fillColor = ($index % 2 === 0) ? 'F9FAFB' : 'FFFFFF';
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $fillColor],
            ],
        ]);

        // Set row height
        $sheet->getRowDimension($row)->setRowHeight(20);
        
        $row++;
    }

    // Apply center alignment to ALL data cells (horizontal & vertical)
    $sheet->getStyle('A' . $headerRow . ':' . $lastCol . ($row - 1))->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true, // Wrap text for long content
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => 'D1D5DB'],
            ],
        ],
    ]);

    // Freeze header row (row 5)
    $sheet->freezePane('A6');

    // Auto-fit columns (with minimum width)
    foreach (range('A', $lastCol) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // Set minimum width
        if ($sheet->getColumnDimension($columnID)->getWidth() < 12) {
            $sheet->getColumnDimension($columnID)->setWidth(12);
        }
    }

    // Generate Filename
    $filename = 'Daftar_BMN_' . str_replace(' ', '_', $utilization->nama_mitra_penyewa) . '.xlsx';
    $tempPath = storage_path('app/temp/' . $filename);

    // Ensure temp directory exists
    if (!file_exists(dirname($tempPath))) {
        mkdir(dirname($tempPath), 0755, true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($tempPath);

    return $tempPath;
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
                $placeholders['{{NO_NODIN}}'] = $utilization->nodinBerjenjang->nomor ?? 'N/A';

                // Tanggal Surat (single date)
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->nodinBerjenjang->tanggal);

                // Format date range: (10 September 2025 – 9 September 2026)
                if ($utilization->nodinBerjenjang->tanggal_mulai && $utilization->nodinBerjenjang->tanggal_selesai) {
                    $tanggalMulai = $this->formatDateIndonesia($utilization->nodinBerjenjang->tanggal_mulai);
                    $tanggalSelesai = $this->formatDateIndonesia($utilization->nodinBerjenjang->tanggal_selesai);
                    $placeholders['{{TANGGAL_NODIN}}'] = "({$tanggalMulai} – {$tanggalSelesai})";
                } else {
                    $placeholders['{{TANGGAL_NODIN}}'] = 'N/A';
                }

                // Calculate jangka waktu from date range
                $placeholders['{{JANGKA_WAKTU}}'] = $this->calculateJangkaWaktu(
                    $utilization->nodinBerjenjang->tanggal_mulai,
                    $utilization->nodinBerjenjang->tanggal_selesai
                );

                $placeholders['{{MITRA_PERUNTUKAN}}'] = $utilization->nodinBerjenjang->peruntukan ?? 'N/A';
                $placeholders['{{NOMINAL_SURAT}}'] = 'Rp ' . number_format($utilization->nodinBerjenjang->nominal ?? 0, 0, ',', '.');
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
                $placeholders['NOMOR_SURAT_KPKNL'] = $utilization->suratUsulanKpknl->surat_usulan_nomor ?? 'N/A';
                $placeholders['TANGGAL_SURAT'] = $this->formatDateIndonesia($utilization->suratUsulanKpknl->surat_usulan_tanggal);
                $placeholders['TUJUAN_SURAT'] = $utilization->suratUsulanKpknl->surat_usulan_tujuan ?? 'N/A';
                $placeholders['PERUNTUKAN'] = $utilization->suratUsulanKpknl->surat_usulan_peruntukan ?? 'N/A';
                $placeholders['TANGGAL_BERAKHIR_KPKNL'] = $utilization->suratUsulanKpknl->surat_usulan_tanggal_berakhir ? $this->formatDateIndonesia($utilization->suratUsulanKpknl->surat_usulan_tanggal_berakhir) : 'N/A';
                $placeholders['NAMA_KASUBAG'] = $utilization->suratUsulanKpknl->kasubag_nama ?? 'N/A';
                $placeholders['NOMOR_KASUBAG'] = $utilization->suratUsulanKpknl->kasubag_nomor ?? 'N/A';
                $placeholders['NOMOR_SURAT_SPTJM'] = $utilization->suratUsulanKpknl->sptjm_nomor ?? 'N/A';
                $placeholders['KODE_BARANG'] = $utilization->suratUsulanKpknl->sptjm_kode_barang ?? 'N/A';
                $placeholders['NUP'] = $utilization->suratUsulanKpknl->sptjm_nup ?? 'N/A';
                $placeholders['LUAS_BANGUNAN'] = $utilization->suratUsulanKpknl->sptjm_luasan_sewa ?? 'N/A';
                $placeholders['LOKASI'] = $utilization->suratUsulanKpknl->sptjm_lokasi_sewa ?? 'N/A';
                break;
            
            // 'sptjm' removed as it is merged into surat_usulan_kpknl

            case 'surat_pernyataan':
                $placeholders['{{NO_SURAT}}'] = $utilization->surat_pernyataan_nomor ?? 'N/A';
                $placeholders['{{TANGGAL_SURAT}}'] = $this->formatDateIndonesia($utilization->surat_pernyataan_tanggal);
                $placeholders['{{KODE_BARANG}}'] = $utilization->surat_pernyataan_kode_barang ?? 'N/A';
                break;

            case 'nodin_persetujuan_kpknl':
                $placeholders['{{NOMOR_NODIN}}'] = $utilization->nodinPersetujuanKpknl->nomor_nodin ?? 'N/A';
                $placeholders['{{TANGGAL_NODIN}}'] = $this->formatDateIndonesia($utilization->nodinPersetujuanKpknl->tanggal_nodin);
                $placeholders['{{PERIHAL_NODIN}}'] = $utilization->nodinPersetujuanKpknl->perihal_nodin ?? 'N/A';
                
                // Jangka Waktu (Auto Calculate)
                if ($utilization->nodinPersetujuanKpknl->periode_sewa_mulai && $utilization->nodinPersetujuanKpknl->periode_sewa_selesai) {
                    $start = \Carbon\Carbon::parse($utilization->nodinPersetujuanKpknl->periode_sewa_mulai);
                    $end = \Carbon\Carbon::parse($utilization->nodinPersetujuanKpknl->periode_sewa_selesai);
                    $diffInDays = $start->diffInDays($end);
                    $years = round($diffInDays / 365);
                    $years = $years > 0 ? $years : 1; // Minimum 1 year logic, or just result
                    $placeholders['{{JANGKA_WAKTU}}'] = $years . ' Tahun';
                    
                    $mulai = $this->formatDateIndonesia($utilization->nodinPersetujuanKpknl->periode_sewa_mulai);
                    $selesai = $this->formatDateIndonesia($utilization->nodinPersetujuanKpknl->periode_sewa_selesai);
                    $placeholders['{{PERIODE_SEWA}}'] = "({$mulai} – {$selesai})";
                } else {
                    $placeholders['{{JANGKA_WAKTU}}'] = 'N/A';
                    $placeholders['{{PERIODE_SEWA}}'] = 'N/A';
                }
                
                $placeholders['{{NOMINAL_SEWA}}'] = 'Rp ' . number_format($utilization->nodinPersetujuanKpknl->nominal ?? 0, 0, ',', '.');
                break;

            case 'surat_invoice':
                $placeholders['NOMOR_SURAT_PENYAMPAIAN_INVOICE'] = $utilization->surat_invoice_nomor ?? 'N/A';
                $placeholders['TANGGAL_SURAT_PENYAMPAIAN_INVOICE'] = $this->formatDateIndonesia($utilization->surat_invoice_tanggal);
                
                $nominal = $utilization->surat_invoice_nominal ?? 0;
                $placeholders['ANGKA_NOMINAL_PENYAMPAIAN_INVOICE'] = 'Rp ' . number_format($nominal, 0, ',', '.');
                // Restore missing placeholders
                $placeholders['KASUBAG'] = $utilization->surat_invoice_kasub ?? 'N/A';
                $placeholders['NOMOR_KASUBAG'] = $utilization->surat_invoice_kasub_nomor ?? 'N/A';
                $placeholders['TUJUAN_INVOICE'] = $utilization->surat_invoice_tujuan ?? 'N/A';
                $placeholders['TUJUAN_SURAT'] = $utilization->surat_invoice_tujuan ?? 'N/A';

                // Lama Periode
                $lamaPeriode = $utilization->surat_invoice_lama_periode ? $utilization->surat_invoice_lama_periode . ' tahun' : 'N/A';
                $placeholders['LAMA_PERIODE_INVOICE'] = $lamaPeriode;
                $placeholders['LAMA_PERIODE'] = $lamaPeriode;
                
                // Periode Sewa
                $periodeSewa = $utilization->surat_invoice_periode_sewa ?? 'N/A';
                if ($utilization->surat_invoice_periode_mulai && $utilization->surat_invoice_periode_akhir) {
                    $periodeSewa = '(' . $this->formatDateIndonesia($utilization->surat_invoice_periode_mulai) . ' - ' . $this->formatDateIndonesia($utilization->surat_invoice_periode_akhir) . ')';
                }
                $placeholders['PERIODE_INVOICE'] = $periodeSewa;
                $placeholders['PERIODE'] = $periodeSewa;
                
                $placeholders['ANGKA_NOMINAL_INVOICE'] = 'Rp ' . number_format($nominal, 0, ',', '.');
                
                // Terbilang format: (seribu rupiah)
                $terbilang = '(' . strtolower($this->terbilang($nominal)) . ' rupiah)';
                $placeholders['TERBILANG_NOMINAL_PENYAMPAIAN_INVOICE'] = $terbilang;
                $placeholders['TERBILANG_NOMINAL_INVOICE'] = $terbilang;
                
                $placeholders['NOMOR_BMN'] = $utilization->surat_invoice_nomor_bmn ?? 'N/A';
                $placeholders['TANGGAL_INVOICE'] = $this->formatDateIndonesia($utilization->surat_invoice_tanggal_faktur);
                $placeholders['NAMA_KASUBAG_DENGAN_GELAR'] = $utilization->surat_invoice_nama_kasubag_gelar ?? 'N/A';
                break;

            case 'nodin_ttd':
                $surat = $utilization->suratPermohonanTtd;
                $placeholders['NOMOR_SURAT'] = $surat->nomor_surat ?? 'N/A';
                $placeholders['TANGGAL_SURAT'] = $this->formatDateIndonesia($surat->tanggal_surat);
                $placeholders['TUJUAN_SURAT'] = $surat->tujuan_surat ?? 'N/A';
                $placeholders['TUJUAN_SURAT_BERTEMPAT'] = $surat->tujuan_surat_bertempat ?? 'N/A';
                $placeholders['NAMA_FASILITAS_BMN'] = $surat->nama_fasilitas_bmn ?? 'N/A';
                $placeholders['PERIHAL'] = $surat->perihal ?? 'N/A';
                break;

            case 'nodin_internal':
                $surat = $utilization->nodinInternal;
                $placeholders['NOMOR_SURAT_BERJENJANG_1'] = $surat->nomor_berjenjang_1 ?? 'N/A';
                $placeholders['NOMOR_SURAT_BERJENJANG_2'] = $surat->nomor_berjenjang_2 ?? 'N/A';
                $placeholders['NOMOR_SURAT_BERJENJANG_3'] = $surat->nomor_berjenjang_3 ?? 'N/A';
                $placeholders['PERIHAL_SURAT'] = $surat->perihal ?? 'N/A';
                $placeholders['TANGGAL_SURAT'] = $this->formatDateIndonesia($surat->tanggal_surat);
                $placeholders['NAMA_MITRA'] = $surat->nama_mitra ?? 'N/A';
                $placeholders['OBJEK_BMN'] = $surat->objek_bmn ?? 'N/A';
                $placeholders['SESUAI_PERJANJIAN_INDUK_NOMOR'] = $surat->nomor_perjanjian_induk ?? 'N/A';
                $placeholders['NOMOR_TERBIT_PERSETUJUAN_SEWA_KPKNL'] = $surat->nomor_persetujuan_sewa ?? 'N/A';
                $placeholders['TANGGAL_PERSETUJUAN_SEWA'] = $this->formatDateIndonesia($surat->tanggal_persetujuan_sewa);
                break;

            case 'surat_penyampaian_perjanjian':
                $surat = $utilization->suratPenyampaianPerjanjian;
                $placeholders['NOMOR_SURAT'] = $surat->nomor_surat ?? 'N/A';
                $placeholders['TANGGAL_SURAT'] = $this->formatDateIndonesia($surat->tanggal_surat);
                $placeholders['NAMA_MITRA'] = $surat->nama_mitra ?? 'N/A';
                $placeholders['ALAMAT_MITRA'] = $surat->alamat_mitra ?? 'N/A';
                $placeholders['KOTA_MITRA'] = $surat->kota_mitra ?? 'N/A';
                $placeholders['NAMA_USAHA'] = $surat->nama_usaha ?? 'N/A';
                break;

            case 'perjanjian':
                $perjanjian = $utilization->perjanjianSewa;
                $placeholders['{{NO_PERJANJIAN}}'] = $perjanjian->nomor_surat ?? ($utilization->perjanjian_nomor ?? 'N/A');
                $placeholders['{{TANGGAL_PERJANJIAN}}'] = isset($perjanjian->tanggal_surat) ? $this->formatDateIndonesia($perjanjian->tanggal_surat) : ($utilization->perjanjian_tanggal_penandatanganan ? $this->formatDateIndonesia($utilization->perjanjian_tanggal_penandatanganan) : 'N/A');
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
            'nodin_berjenjang' => $utilization->nodinBerjenjang ? 'ready' : 'missing',
            'surat_usulan_kpknl' => $utilization->suratUsulanKpknl ? 'ready' : 'missing',
            
            // 'sptjm' removed as it is merged into surat_usulan_kpknl
            'surat_pernyataan' => $this->checkDocumentFields($utilization, [
                'surat_pernyataan_nomor', 'surat_pernyataan_tanggal'
            ]),
            'daftar_bmn' => $utilization->daftarBmn()->count() > 0 ? 'ready' : 'missing',

            // Penilaian KPKNL
            'nodin_persetujuan_kpknl' => $utilization->nodinPersetujuanKpknl ? 'ready' : 'missing',
            
            'surat_invoice' => $this->checkDocumentFields($utilization, [
                'surat_invoice_nomor', 'surat_invoice_tanggal', 'surat_invoice_nominal',
                'surat_invoice_kasub_nomor', 'surat_invoice_lama_periode',
                'surat_invoice_nomor_bmn', 'surat_invoice_tanggal_faktur',
                'surat_invoice_nama_kasubag_gelar'
            ]),

            // Perjanjian
            'nodin_ttd' => $utilization->suratPermohonanTtd ? 'ready' : 'missing',
            'nodin_internal' => $utilization->nodinInternal ? 'ready' : 'missing',
            'surat_penyampaian_perjanjian' => $utilization->suratPenyampaianPerjanjian ? 'ready' : 'missing',
            'perjanjian' => ($utilization->perjanjianSewa && $utilization->perjanjianSewa->nomor_surat) ? 'ready' : 'missing',
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
        // Special validation for daftar_bmn
        if ($type === 'daftar_bmn') {
            if ($utilization->daftarBmn()->count() > 0) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Daftar BMN belum diisi']];
            }
        }

        // Special validation for nodin_berjenjang
        if ($type === 'nodin_berjenjang') {
            if ($utilization->nodinBerjenjang) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Nodin Berjenjang belum diisi']];
            }
        }

        // Special validation for surat_usulan_kpknl
        if ($type === 'surat_usulan_kpknl') {
            if ($utilization->suratUsulanKpknl) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Surat Usulan KPKNL belum diisi']];
            }
        }

        // Special validation for nodin_ttd
        if ($type === 'nodin_ttd') {
            if ($utilization->suratPermohonanTtd) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Surat Permohonan TTD belum diisi']];
            }
        }

        // Special validation for nodin_internal
        if ($type === 'nodin_internal') {
            if ($utilization->nodinInternal) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Nodin Internal belum diisi']];
            }
        }

        // Special validation for surat_penyampaian_perjanjian
        if ($type === 'surat_penyampaian_perjanjian') {
            if ($utilization->suratPenyampaianPerjanjian) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Surat Penyampaian Perjanjian belum diisi']];
            }
        }

        // Special validation for perjanjian
        if ($type === 'perjanjian') {
            if ($utilization->perjanjianSewa) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Data Perjanjian Sewa belum diisi']];
            }
        }

        // Special validation for nodin_persetujuan_kpknl
        if ($type === 'nodin_persetujuan_kpknl') {
            if ($utilization->nodinPersetujuanKpknl) {
                return ['valid' => true, 'missing' => []];
            } else {
                return ['valid' => false, 'missing' => ['Nodin Persetujuan KPKNL belum diisi']];
            }
        }

        $requiredFieldsMap = [
            'surat_konfirmasi_perpanjangan_sewa' => ['surat_konfirmasi_nomor', 'surat_konfirmasi_tanggal'],
            'nodin_konfirmasi' => ['nodin_konfirmasi_nomor', 'nodin_konfirmasi_tanggal'],
            // 'nodin_berjenjang' removed as it has special validation
            // 'surat_usulan_kpknl' removed as it has special validation
            // 'sptjm' removed as it is merged into surat_usulan_kpknl
            'surat_pernyataan' => ['surat_pernyataan_nomor', 'surat_pernyataan_tanggal'],
            'surat_invoice' => [
                'surat_invoice_nomor', 'surat_invoice_tanggal', 'surat_invoice_nominal',
                'surat_invoice_kasub_nomor', 'surat_invoice_lama_periode',
                'surat_invoice_nomor_bmn', 'surat_invoice_tanggal_faktur',
                'surat_invoice_nama_kasubag_gelar'
            ],
            'nodin_ttd' => ['surat_permohonan_ttd_nomor', 'surat_permohonan_ttd_tanggal'],
            'nodin_internal' => ['nodin_internal_tanggal', 'nodin_internal_perihal'],
            'surat_penyampaian_perjanjian' => ['surat_penyampaian_perjanjian_nomor', 'surat_penyampaian_perjanjian_tanggal'],
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

    /**
     * Get ALL available data for dynamic replacement
     */
    private function getAllAvailableData($utilization)
    {
        $data = [];
        
        // Common
        $data['TANGGAL'] = $this->formatDateIndonesia(now());
        $data['NAMA_MITRA'] = $utilization->nama_mitra_penyewa ?? 'N/A';
        $data['JENIS_MITRA'] = $utilization->jenis_mitra ?? 'N/A';
        $data['JENIS_USULAN'] = $utilization->jenis_usulan ?? 'N/A';
        
        // Perjanjian Sewa
        if ($utilization->perjanjianSewa) {
            $p = $utilization->perjanjianSewa;
            $data['MITRA_PENYEWA'] = $p->mitra_penyewa ?? 'N/A';
            $data['PERUNTUKAN'] = $p->peruntukan ?? 'N/A';
            $data['GEDUNG'] = $p->objek_gedung ?? 'N/A';
            $data['TANGGAL_LENGKAP'] = $this->formatDateIndonesia($p->tanggal_surat);
            $data['NAMA_PIHAK_PERTAMA'] = $p->pihak_pertama_nama ?? 'N/A';
            $data['PIHAK_PERTAMA_KEDUDUKAN_SEBAGAI'] = $p->pihak_pertama_kedudukan ?? 'N/A';
            $data['PIHAK_PERTAMA_KEPUTUSAN_NOMOR'] = $p->pihak_pertama_keputusan_nomor ?? 'N/A';
            $data['PIHAK_PERTAMA_KEPUTUSAN_TAHUN'] = $p->pihak_pertama_keputusan_tahun ?? 'N/A';
            $data['NAMA_PIHAK_KEDUA'] = $p->pihak_kedua_nama ?? 'N/A';
            $data['PIHAK_KEDUA_KEDUDUKAN_SEBAGAI'] = $p->pihak_kedua_kedudukan ?? 'N/A';
            $data['PIHAK_KEDUA_BERDASARKAN_KEPUTUSAN'] = $p->pihak_kedua_dasar_hukum ?? 'N/A';
            $data['PIHAK_KEDUA_KEPUTUSAN_NOMOR'] = $p->pihak_kedua_keputusan_nomor ?? 'N/A';
            $data['PIHAK_KEDUA_KEPUTUSAN_TANGGAL'] = $this->formatDateIndonesia($p->pihak_kedua_keputusan_tanggal);
            $data['PIHAK_KEDUA_ATAS_NAMA'] = $p->pihak_kedua_atas_nama ?? 'N/A';
            $data['PIHAK_KEDUA_ALAMAT'] = $p->pihak_kedua_alamat ?? 'N/A';
            $data['PIHAK_KEDUA_KEGIATAN_USAHA'] = $p->pihak_kedua_kegiatan_usaha ?? 'N/A';
            $data['LUAS'] = $p->objek_luas ?? 'N/A';
        // Convert m2 to m² (superscript 2)
        $satuan = $p->objek_satuan_luas ?? 'm2';
        $data['SATUAN_LUAS'] = str_replace('2', '²', $satuan);
            $data['LETAK_BANGUNAN'] = $p->objek_letak ?? 'N/A';
            $data['DURASI_SEWA'] = $p->durasi_sewa ?? 'N/A';
            $data['NILAI_SEWA_ANGKA'] = number_format($p->nilai_sewa ?? 0, 0, ',', '.');
        $data['NILAI_SEWA_TERBILANG'] = $this->formatTerbilang($p->nilai_sewa ?? 0);
        $data['NILAI_PENDAPATAN_BUKTI_BAYAR_ANGKA'] = number_format($utilization->nilai_pendapatan_bukti_bayar ?? 0, 0, ',', '.');
        $data['NILAI_PENDAPATAN_BUKTI_BAYAR_TERBILANG'] = $this->formatTerbilang($utilization->nilai_pendapatan_bukti_bayar ?? 0);
        $data['TANGGAL_MULAI'] = $this->formatDateIndonesia($p->periode_mulai);
            $data['TANGGAL_SELESAI'] = $this->formatDateIndonesia($p->periode_selesai);
            $data['ALAMAT_PIHAK_KEDUA'] = $p->pihak_kedua_alamat ?? 'N/A';
        }
        
        return $data;
    }

    /**
     * Format terbilang with proper parentheses
     */
    private function formatTerbilang($nilai)
    {
        if ($nilai == 0) {
            return '(nol rupiah)';
        }

        $terbilang = trim($this->terbilang($nilai));
        return '(' . strtolower($terbilang) . ' rupiah)';
    }

    /**
     * Convert number to Indonesian text (Terbilang)
     */
    private function terbilang($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai/10)." puluh". $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai/100) . " ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai/1000) . " ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai/1000000) . " juta" . $this->terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilang($nilai/1000000000) . " milyar" . $this->terbilang(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->terbilang($nilai/1000000000000) . " trilyun" . $this->terbilang(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    /**
     * Replaces a specific shape (ellipse containing ${LOGO_PENYEWA}) with an image.
     * Preserves the shape's dimensions and position.
     * Operates directly on the saved DOCX file.
     */
    private function replaceShapeWithImage($filePath, $logoPath)
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                \Log::error("Could not open DOCX file for shape replacement: $filePath");
                return;
            }

            $docXml = $zip->getFromName('word/document.xml');
            $relsXml = $zip->getFromName('word/_rels/document.xml.rels');

            if (!$docXml || !$relsXml) {
                \Log::error("Could not read document.xml or its relationships");
                $zip->close();
                return;
            }

            $dom = new \DOMDocument();
            $dom->loadXML($docXml);
            $xpath = new \DOMXPath($dom);
            
            // Register namespaces
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $xpath->registerNamespace('wps', 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape');
            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');

            // Find the shape containing ${LOGO_PENYEWA}
            $query = "//wps:wsp[descendant::w:t[contains(text(), '\${LOGO_PENYEWA}')]]";
            $shapes = $xpath->query($query);

            if ($shapes->length === 0) {
                \Log::warning("No shape found containing \${LOGO_PENYEWA}");
                $zip->close();
                return;
            }

            $shape = $shapes->item(0);
            
            // Extract dimensions
            $ext = $xpath->query(".//a:ext", $shape)->item(0);
            $cx = $ext ? $ext->getAttribute('cx') : '914400'; // Default ~1 inch
            $cy = $ext ? $ext->getAttribute('cy') : '914400';

            \Log::info("Found shape to replace. Dimensions: cx=$cx, cy=$cy");

            // 1. Add Image to Media and Relationships
            $rId = $this->addImageToZip($zip, $relsXml, $logoPath);
            
            // 2. Create the new <pic:pic> element
            $graphicData = $shape->parentNode; // <a:graphicData>
            
            // Update URI to Picture
            $graphicData->setAttribute('uri', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            
            // Remove all children
            while ($graphicData->hasChildNodes()) {
                $graphicData->removeChild($graphicData->firstChild);
            }

            // Namespaces
            $picNs = 'http://schemas.openxmlformats.org/drawingml/2006/picture';
            $aNs = 'http://schemas.openxmlformats.org/drawingml/2006/main';

            // Create <pic:pic> structure
            $pic = $dom->createElementNS($picNs, 'pic:pic');
            
            // <pic:nvPicPr>
            $nvPicPr = $dom->createElementNS($picNs, 'pic:nvPicPr');
            $cNvPr = $dom->createElementNS($picNs, 'pic:cNvPr');
            $cNvPr->setAttribute('id', '0');
            $cNvPr->setAttribute('name', 'Logo');
            $cNvPicPr = $dom->createElementNS($picNs, 'pic:cNvPicPr');
            $nvPicPr->appendChild($cNvPr);
            $nvPicPr->appendChild($cNvPicPr);
            $pic->appendChild($nvPicPr);

            // <pic:blipFill>
            $blipFill = $dom->createElementNS($picNs, 'pic:blipFill');
            $blip = $dom->createElementNS($aNs, 'a:blip');
            $blip->setAttribute('r:embed', $rId);
            $stretch = $dom->createElementNS($aNs, 'a:stretch');
            $fillRect = $dom->createElementNS($aNs, 'a:fillRect');
            $stretch->appendChild($fillRect);
            $blipFill->appendChild($blip);
            $blipFill->appendChild($stretch);
            $pic->appendChild($blipFill);

            // <pic:spPr>
            $spPr = $dom->createElementNS($picNs, 'pic:spPr');
            $xfrm = $dom->createElementNS($aNs, 'a:xfrm');
            $off = $dom->createElementNS($aNs, 'a:off');
            $off->setAttribute('x', '0');
            $off->setAttribute('y', '0');
            $extNode = $dom->createElementNS($aNs, 'a:ext');
            $extNode->setAttribute('cx', $cx);
            $extNode->setAttribute('cy', $cy);
            $xfrm->appendChild($off);
            $xfrm->appendChild($extNode);
            
            $prstGeom = $dom->createElementNS($aNs, 'a:prstGeom');
            $prstGeom->setAttribute('prst', 'rect');
            $avLst = $dom->createElementNS($aNs, 'a:avLst');
            $prstGeom->appendChild($avLst);
            
            $spPr->appendChild($xfrm);
            $spPr->appendChild($prstGeom);
            $pic->appendChild($spPr);

            $graphicData->appendChild($pic);

            // Save back to ZIP
            $zip->addFromString('word/document.xml', $dom->saveXML());
            $zip->close();
            
            \Log::info("Successfully replaced shape with image (rId: $rId)");

        } catch (\Exception $e) {
            \Log::error("Failed to replace shape with image: " . $e->getMessage());
        }
    }

    private function addImageToZip($zip, $relsXml, $imagePath)
    {
        // Parse Relationships
        $dom = new \DOMDocument();
        $dom->loadXML($relsXml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Find max rId
        $maxId = 0;
        foreach ($xpath->query('//r:Relationship') as $node) {
            $id = $node->getAttribute('Id');
            if (preg_match('/^rId(\d+)$/', $id, $matches)) {
                $maxId = max($maxId, (int)$matches[1]);
            }
        }
        $newRId = 'rId' . ($maxId + 1);

        // Add Image File
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $targetName = "media/logo_replaced_{$newRId}.{$ext}";
        $zip->addFromString('word/' . $targetName, file_get_contents($imagePath));

        // Add Relationship
        $rel = $dom->createElement('Relationship');
        $rel->setAttribute('Id', $newRId);
        $rel->setAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image');
        $rel->setAttribute('Target', $targetName);
        
        $dom->documentElement->appendChild($rel);
        $zip->addFromString('word/_rels/document.xml.rels', $dom->saveXML());

        return $newRId;
    }

}
