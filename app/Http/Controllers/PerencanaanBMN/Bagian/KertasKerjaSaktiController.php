<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian;

use App\Http\Controllers\Controller;
use App\Models\BmnInventarisasiKkSakti;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class KertasKerjaSaktiController extends Controller
{
    public function index()
    {
        try {
            $stats = $this->getDashboardStats();
            $batchHistory = $this->fetchBatchHistoryData();

            return view('PerencanaanBMN.Bagian.DashboardKertasKerjaSakti.index', compact('stats', 'batchHistory'));
        } catch (Exception $e) {
            Log::error('Dashboard KK Sakti error: ' . $e->getMessage());
            return view('PerencanaanBMN.Bagian.DashboardKertasKerjaSakti.index')
                ->with('error', 'Failed to load dashboard data');
        }
    }

    public function uploadExcel(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
            'sheet_name' => 'string|nullable'
        ]);

        $file = null;
        $sheetName = null;

        try {
            set_time_limit(600);
            ini_set('memory_limit', '512M');
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $sheetName = $request->input('sheet_name', 'Data Error');
            $batchId = BmnInventarisasiKkSakti::getNextBatchId();

            // Pre-validate Excel structure (no ID required for normal upload)
            $this->validateExcelStructure($file, $sheetName, false);

            // Store batch metadata
            $batchMetadata = [
                'batch_id' => $batchId,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'sheet_name' => $sheetName,
                'started_at' => now(),
                'status' => 'processing'
            ];
            session(['batch_metadata_' . $batchId => $batchMetadata]);

            $result = $this->processExcelFile($file, $sheetName, $batchId);

            DB::commit();

            // Update batch completion
            $batchMetadata['status'] = 'completed';
            $batchMetadata['completed_at'] = now();
            $batchMetadata['results'] = $result;
            session(['batch_metadata_' . $batchId => $batchMetadata]);

            return response()->json([
                'status' => 'success',
                'message' => 'Excel uploaded and processed successfully',
                'data' => array_merge($result, [
                    'batch_id' => $batchId,
                    'filename' => $file->getClientOriginalName(),
                    'processing_time' => now()->diffInSeconds($batchMetadata['started_at']) . 's'
                ])
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Upload Excel KK Sakti error: ' . $e->getMessage(), [
                'file' => $file ? $file->getClientOriginalName() : 'unknown',
                'sheet' => $sheetName ?? 'unknown',
                'user' => auth()->id() ?? 'anonymous'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate Excel file structure and required columns
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $sheetName
     * @param bool $requireId Whether to require ID column (for fix data upload)
     * @throws Exception
     * @return void
     */
    private function validateExcelStructure($file, $sheetName, $requireId = false): void
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());

        $worksheet = $spreadsheet->getSheetByName($sheetName);
        if (!$worksheet) {
            throw new Exception("Sheet '{$sheetName}' not found. Available sheets: " . implode(', ', $spreadsheet->getSheetNames()));
        }

        $headers = $worksheet->rangeToArray('A1:Z1')[0];

        // Clean headers by removing asterisks and trimming whitespace
        $cleanHeaders = array_map(function($header) {
            return trim(str_replace('*', '', $header ?? ''));
        }, $headers);

        // Required columns with flexible matching
        $requiredColumns = [
            'Kode Barang',
            'NUP Awal',
            'NUP Akhir',
            'Uraian Barang'
        ];

        // Add ID requirement only for fix data upload
        if ($requireId) {
            array_unshift($requiredColumns, 'ID');
        }

        foreach ($requiredColumns as $required) {
            $found = false;

            // Check both original and cleaned headers with flexible matching
            foreach ($cleanHeaders as $header) {
                $normalizedHeader = strtolower(str_replace([' ', '_', '-'], '', $header));
                $normalizedRequired = strtolower(str_replace([' ', '_', '-'], '', $required));

                if ($normalizedHeader === $normalizedRequired ||
                    strcasecmp($header, $required) === 0 ||
                    strcasecmp($header, str_replace(' ', '', $required)) === 0) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new Exception("Required column '{$required}' not found in Excel headers. Available headers: " . implode(', ', array_filter($headers)));
            }
        }

        $dataRange = $worksheet->calculateWorksheetDataDimension();
        if ($worksheet->getCell('A2')->getValue() === null) {
            throw new Exception("No data found in Excel file");
        }
    }

    public function getData(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = BmnInventarisasiKkSakti::query();

            // Apply filters
            if ($request->filled('batch_id')) {
                $query->byBatch($request->batch_id);
            }

            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('kode_barang')) {
                $query->byKodeBarang($request->kode_barang);
            }

            if ($request->filled('nup')) {
                $query->where(function($q) use ($request) {
                    $nup = $request->nup;
                    $q->where('nup_awal', 'like', "%{$nup}%")
                      ->orWhere('nup_akhir', 'like', "%{$nup}%");
                });
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->byDateRange($request->date_from, $request->date_to);
            }

            if ($request->filled('jenis_transaksi')) {
                $query->where('jenis_transaksi', 'like', '%' . $request->jenis_transaksi . '%');
            }

            if ($request->filled('min_nilai')) {
                $query->where('nilai_total', '>=', $request->min_nilai);
            }

            if ($request->filled('max_nilai')) {
                $query->where('nilai_total', '<=', $request->max_nilai);
            }

            if ($request->filled('status_pelabelan')) {
                if ($request->status_pelabelan === 'Belum') {
                    $query->where(function($q) {
                        $q->whereNull('status_pelabelan')
                          ->orWhere('status_pelabelan', '')
                          ->orWhere('status_pelabelan', 'like', '%belum%');
                    });
                } elseif ($request->status_pelabelan === 'Sudah') {
                    $query->where('status_pelabelan', 'like', '%sudah%');
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'id');
            $sortDir = $request->input('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            // Pagination
            $perPage = min($request->input('per_page', 50), 200);
            $data = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Get data KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function exportBartender(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = BmnInventarisasiKkSakti::query();

            // Apply filters
            if ($request->filled('batch_id')) {
                $query->byBatch($request->batch_id);
            }

            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('kode_barang')) {
                $query->byKodeBarang($request->kode_barang);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->byDateRange($request->date_from, $request->date_to);
            }

            $data = $query->get();

            // Create Excel data array
            $excelData = [];
            $excelData[] = ['IDBarang', 'Kode Barang', 'NUP', 'Uraian Barang', 'Tahun Perolehan', 'Merek', 'Area', 'Gedung', 'Ruangan'];

            foreach ($data as $item) {
                $bartenderData = $item->toBartenderFormat();
                $excelData[] = array_values($bartenderData);
            }

            $fileName = 'bartender_export_' . date('Y-m-d_His') . '.xlsx';

            return response()->json([
                'status' => 'success',
                'data' => $excelData,
                'filename' => $fileName,
                'total_records' => count($excelData) - 1
            ]);

        } catch (Exception $e) {
            Log::error('Export Bartender KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getStats(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $batchId = $request->input('batch_id');
            $stats = BmnInventarisasiKkSakti::getBatchStats($batchId);

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete all records from specific batch
     * @param int $batchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBatch(int $batchId): \Illuminate\Http\JsonResponse
    {
        try {
            $count = BmnInventarisasiKkSakti::byBatch($batchId)->count();

            if ($count === 0) {
                return response()->json(['status' => 'error', 'message' => 'Batch not found'], 404);
            }

            BmnInventarisasiKkSakti::byBatch($batchId)->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Deleted {$count} records from batch {$batchId}"
            ]);

        } catch (Exception $e) {
            Log::error('Delete batch KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBatchHistory(): \Illuminate\Http\JsonResponse
    {
        try {
            $batches = $this->fetchBatchHistoryData();
            return response()->json(['status' => 'success', 'data' => $batches]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function fetchBatchHistoryData()
    {
        try {
            return DB::table('bmn_inventarisasi_kk_sakti')
                ->select('upload_batch', DB::raw('COUNT(*) as total_records'),
                         DB::raw('MIN(created_at) as uploaded_at'),
                         DB::raw('SUM(nilai_total) as total_nilai'))
                ->groupBy('upload_batch')
                ->orderBy('upload_batch', 'desc')
                ->limit(10)
                ->get();
        } catch (Exception $e) {
            Log::error('Fetch batch history data error: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    // Private helper methods
    /**
     * Process Excel file and save data to database
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $sheetName
     * @param int $batchId
     * @return array
     */
    private function processExcelFile($file, $sheetName, int $batchId): array
    {
        $processed = 0;
        $skipped = 0;
        $updated = 0;
        $duplicates = 0;
        $errors = [];
        $total = 0;
        $duplicateTracker = [];

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());
        $worksheet = $spreadsheet->getSheetByName($sheetName);

        $rows = $worksheet->toArray();
        $headers = array_shift($rows);
        $totalRows = count($rows);

        $columnMapping = [
            0 => 'tanggal',           1 => 'bulan_dok',         2 => 'mak',
            3 => 'nilai_satuan',      4 => 'kode_transaksi',    5 => 'jenis_transaksi',
            6 => 'kode_barang',       7 => 'jumlah',            8 => 'uraian_barang',
            9 => 'nup_awal',          10 => 'nup_akhir',        11 => 'nilai_total',
            12 => 'nilai_spm',        13 => 'nomor_karwas',     14 => 'link_dokumen',
            15 => 'merk_tipe_bmn',    16 => 'nama_pt',          17 => 'nomor_dokumen',
            18 => 'tgl_bast',         19 => 'pekerjaan',        20 => 'lokasi',
            21 => 'bagian',           22 => 'keterangan',       23 => 'spby',
            24 => 'status_pelabelan', 25 => 'tanggal_pelabelan'
        ];

        // Process in chunks for better memory management
        $chunkSize = 100;
        $chunks = array_chunk($rows, $chunkSize);

        foreach ($chunks as $chunkIndex => $chunk) {
            foreach ($chunk as $localIndex => $row) {
                $rowIndex = ($chunkIndex * $chunkSize) + $localIndex + 2; // +2 for header and 1-based indexing
                $total++;

                // Update progress every chunk
                if ($localIndex === 0) {
                    $progress = $totalRows > 0 ? min(90, ($total / $totalRows) * 100) : 0;
                    $this->updateProgress($batchId, $progress, $total, $totalRows);
                }

                if (empty(array_filter($row))) {
                    $skipped++;
                    continue;
                }

                try {
                    $data = $this->mapRowData($row, $columnMapping, $batchId, $rowIndex);

                    if (!$data) {
                        $errors[] = "Row {$rowIndex}: Missing required fields";
                        $skipped++;
                        continue;
                    }

                    $result = $this->saveRecord($data, $duplicateTracker, $rowIndex);

                    switch ($result['action']) {
                        case 'created':
                            $processed++;
                            break;
                        case 'created_variant':
                            $processed++;
                            break;
                        case 'updated':
                            $updated++;
                            break;
                        case 'duplicate':
                            $duplicates++;
                            $skipped++;
                            break;
                    }

                } catch (Exception $e) {
                    $errors[] = "Row {$rowIndex}: " . $e->getMessage();
                    $skipped++;
                }
            }
        }

        // Save error log if any
        if (!empty($errors)) {
            Log::warning("KK Sakti batch {$batchId} processing errors", ['errors' => $errors]);
        }

        return compact('processed', 'skipped', 'updated', 'duplicates', 'total', 'errors');
    }

    /**
     * Map Excel row data to database fields
     * @param array $row
     * @param array $columnMapping
     * @param int $batchId
     * @param int $rowIndex
     * @return array|null
     */
    private function mapRowData(array $row, array $columnMapping, int $batchId, int $rowIndex): ?array
    {
        $data = ['upload_batch' => $batchId];

        foreach ($columnMapping as $colIndex => $fieldName) {
            $value = $row[$colIndex] ?? null;

            switch ($fieldName) {
                case 'tanggal':
                case 'tanggal_pelabelan':
                    $data[$fieldName] = $this->parseExcelDate($value);
                    break;
                case 'nilai_satuan':
                case 'nilai_total':
                case 'nilai_spm':
                    try {
                        $data[$fieldName] = $this->parseNumericValue($value);
                    } catch (Exception $e) {
                        Log::warning("Failed to parse {$fieldName} at row {$rowIndex}", [
                            'original_value' => $value,
                            'field' => $fieldName,
                            'error' => $e->getMessage()
                        ]);
                        $data[$fieldName] = 0; // Set to 0 as fallback
                    }
                    break;
                case 'kode_barang':
                    try {
                        $data[$fieldName] = $this->parseNumericValue($value);
                        if (empty($data[$fieldName])) {
                            throw new Exception("Invalid kode_barang: '{$value}'");
                        }
                    } catch (Exception $e) {
                        Log::warning("Failed to parse kode_barang at row {$rowIndex}", [
                            'original_value' => $value,
                            'error' => $e->getMessage()
                        ]);
                        throw new Exception("Invalid kode_barang: '{$value}'");
                    }
                    break;
                case 'nup_awal':
                case 'nup_akhir':
                    $data[$fieldName] = $this->parseNupValue($value);
                    if (empty($data[$fieldName])) {
                        throw new Exception("Invalid NUP value");
                    }
                    break;
                default:
                    $data[$fieldName] = $this->sanitizeTextValue($value);
            }
        }

        // Validate required fields and detect errors
        if (empty($data['kode_barang']) || empty($data['nup_awal']) || empty($data['nup_akhir'])) {
            return null;
        }

        // Validate data and mark errors if found
        $validation = $this->validateRecordData($data, $rowIndex);

        if (!$validation['isValid']) {
            // Set error status and details
            $data['error_status'] = 'error';
            $data['error_type'] = $validation['errorType'];
            $data['error_messages'] = implode('; ', $validation['errors']);
            $data['error_details'] = [
                'row_index' => $rowIndex,
                'errors' => $validation['errors'],
                'validated_at' => now()->toISOString()
            ];
            $data['needs_review'] = true;
        } else {
            // Set as valid data
            $data['error_status'] = 'valid';
            $data['error_type'] = null;
            $data['error_messages'] = null;
            $data['error_details'] = null;
            $data['needs_review'] = false;
        }

        return $data;
    }

    /**
     * Save or update record in database with enhanced duplicate detection
     * @param array $data
     * @param array $duplicateTracker
     * @param int $rowIndex
     * @return array
     */
    private function saveRecord(array $data, array &$duplicateTracker, int $rowIndex): array
    {
        // Normalize null values to empty string for consistent comparison
        $jenisTransaksi = $data['jenis_transaksi'] ?? '';
        $kodeTransaksi = $data['kode_transaksi'] ?? '';

        // Create comprehensive unique key including transaction details
        $uniqueKey = $data['kode_barang'] . '|' . $data['nup_awal'] . '|' . $data['nup_akhir'] . '|' .
                     $jenisTransaksi . '|' . $kodeTransaksi;


        // Check in-batch duplicates (exact same record)
        if (isset($duplicateTracker[$uniqueKey])) {
            return ['action' => 'duplicate', 'reason' => 'in_batch_duplicate'];
        }
        $duplicateTracker[$uniqueKey] = $rowIndex;

        // Step 1: Check for exact match in database
        $exactMatchQuery = BmnInventarisasiKkSakti::where('kode_barang', $data['kode_barang'])
            ->where('nup_awal', $data['nup_awal'])
            ->where('nup_akhir', $data['nup_akhir']);

        // Handle jenis_transaksi comparison
        if (empty($jenisTransaksi)) {
            $exactMatchQuery->where(function($q) {
                $q->whereNull('jenis_transaksi')->orWhere('jenis_transaksi', '');
            });
        } else {
            $exactMatchQuery->where('jenis_transaksi', $jenisTransaksi);
        }

        // Handle kode_transaksi comparison
        if (empty($kodeTransaksi)) {
            $exactMatchQuery->where(function($q) {
                $q->whereNull('kode_transaksi')->orWhere('kode_transaksi', '');
            });
        } else {
            $exactMatchQuery->where('kode_transaksi', $kodeTransaksi);
        }

        $existingRecord = $exactMatchQuery->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            return ['action' => 'updated', 'id' => $existingRecord->id, 'reason' => 'exact_match'];
        }

        // Step 2: Check if same asset exists (regardless of transaction details)
        $sameAssetExists = BmnInventarisasiKkSakti::where('kode_barang', $data['kode_barang'])
            ->where('nup_awal', $data['nup_awal'])
            ->where('nup_akhir', $data['nup_akhir'])
            ->exists();

        try {
            // Create new record
            $record = BmnInventarisasiKkSakti::create($data);

            if ($sameAssetExists) {
                return ['action' => 'created_variant', 'id' => $record->id, 'reason' => 'different_transaction'];
            } else {
                return ['action' => 'created', 'id' => $record->id, 'reason' => 'new_record'];
            }

        } catch (\Exception $e) {
            // Handle database constraint errors
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                Log::warning("Database constraint violation for row {$rowIndex}", [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);

                // Try to find the conflicting record and update it instead
                $conflictingRecord = BmnInventarisasiKkSakti::where('kode_barang', $data['kode_barang'])
                    ->where('nup_awal', $data['nup_awal'])
                    ->where('nup_akhir', $data['nup_akhir'])
                    ->first();

                if ($conflictingRecord) {
                    $conflictingRecord->update($data);
                    return ['action' => 'updated', 'id' => $conflictingRecord->id, 'reason' => 'constraint_conflict_resolved'];
                }
            }

            Log::error("Failed to save record for row {$rowIndex}", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw $e;
        }
    }

    /**
     * Update processing progress in session
     * @param int $batchId
     * @param float $progress
     * @param int $current
     * @param int $total
     * @return void
     */
    private function updateProgress($batchId, $progress, $current, $total)
    {
        session(['excel_progress_' . $batchId => [
            'status' => 'processing',
            'progress' => is_numeric($progress) ? round((float)$progress, 2) : 0,
            'current_row' => $current,
            'total_rows' => $total,
            'updated_at' => now()
        ]]);
    }

    /**
     * Get upload progress for specific batch
     * @param int $batchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUploadProgress(int $batchId): \Illuminate\Http\JsonResponse
    {
        $progress = session('excel_progress_' . $batchId, ['status' => 'not_found', 'progress' => 0]);

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function bulkDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'batch_ids' => 'required|array',
            'batch_ids.*' => 'integer'
        ]);

        try {
            $totalDeleted = 0;

            foreach ($request->batch_ids as $batchId) {
                $count = BmnInventarisasiKkSakti::byBatch($batchId)->count();
                if ($count > 0) {
                    BmnInventarisasiKkSakti::byBatch($batchId)->delete();
                    $totalDeleted += $count;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Deleted {$totalDeleted} records from " . count($request->batch_ids) . " batches"
            ]);

        } catch (Exception $e) {
            Log::error('Bulk delete KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Parse Excel date value to Carbon instance
     * @param mixed $value
     * @return \Carbon\Carbon|null
     */
    private function parseExcelDate($value): ?\Carbon\Carbon
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            // Excel numeric date
            return \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d'));
        }

        // Try to parse string date
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
        } catch (Exception $e) {
            try {
                return \Carbon\Carbon::parse($value);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    /**
     * Parse formatted number with various formats to integer
     * Supports formats like: 5.475.000, 5,475,000, 5.475,00, -5.475.000 etc.
     * @param mixed $value
     * @return int
     */
    private function parseFormattedNumber($value): int
    {
        if (empty($value)) return 0;

        // Convert to string for processing
        $stringValue = (string) $value;
        $stringValue = trim($stringValue);

        if (empty($stringValue)) return 0;

        // Handle negative numbers
        $isNegative = false;
        if (strpos($stringValue, '-') !== false) {
            $isNegative = true;
            $stringValue = str_replace('-', '', $stringValue);
        }

        // Remove all non-numeric characters except dots and commas
        $cleanValue = preg_replace('/[^0-9.,]/', '', $stringValue);

        if (empty($cleanValue)) return 0;

        // Determine format and parse accordingly
        $result = 0;

        // Count dots and commas to determine format
        $dotCount = substr_count($cleanValue, '.');
        $commaCount = substr_count($cleanValue, ',');

        if ($dotCount == 0 && $commaCount == 0) {
            // Pure number: "5475000"
            $result = (int) $cleanValue;
        } elseif ($dotCount > 0 && $commaCount == 0) {
            // Dots only: could be "5.475.000" (thousands) or "5475.00" (decimal)
            $lastDotPos = strrpos($cleanValue, '.');
            $afterLastDot = substr($cleanValue, $lastDotPos + 1);

            if (strlen($afterLastDot) <= 2 && $dotCount == 1) {
                // Decimal format: "5475.00"
                $result = (int) (is_numeric($cleanValue) ? round((float) $cleanValue) : 0);
            } else {
                // Thousands separator: "5.475.000"
                $result = (int) str_replace('.', '', $cleanValue);
            }
        } elseif ($commaCount > 0 && $dotCount == 0) {
            // Commas only: could be "5,475,000" (thousands) or "5475,00" (decimal)
            $lastCommaPos = strrpos($cleanValue, ',');
            $afterLastComma = substr($cleanValue, $lastCommaPos + 1);

            if (strlen($afterLastComma) <= 2 && $commaCount == 1) {
                // Decimal format: "5475,00"
                $decimalValue = str_replace(',', '.', $cleanValue);
                $result = (int) (is_numeric($decimalValue) ? round((float) $decimalValue) : 0);
            } else {
                // Thousands separator: "5,475,000"
                $result = (int) str_replace(',', '', $cleanValue);
            }
        } elseif ($dotCount > 0 && $commaCount > 0) {
            // Mixed format: "5.475,00" or "5,475.00"
            $lastDotPos = strrpos($cleanValue, '.');
            $lastCommaPos = strrpos($cleanValue, ',');

            if ($lastCommaPos > $lastDotPos) {
                // Format: "5.475,00" (European)
                $integerPart = str_replace('.', '', substr($cleanValue, 0, $lastCommaPos));
                $decimalPart = substr($cleanValue, $lastCommaPos + 1);
                $finalValue = $integerPart . '.' . $decimalPart;
                $result = (int) (is_numeric($finalValue) ? round((float) $finalValue) : 0);
            } else {
                // Format: "5,475.00" (US)
                $integerPart = str_replace(',', '', substr($cleanValue, 0, $lastDotPos));
                $decimalPart = substr($cleanValue, $lastDotPos + 1);
                $finalValue = $integerPart . '.' . $decimalPart;
                $result = (int) (is_numeric($finalValue) ? round((float) $finalValue) : 0);
            }
        }

        return $isNegative ? -$result : $result;
    }



    /**
     * Parse numeric value to integer (backward compatibility wrapper)
     * @param mixed $value
     * @return int
     */
    private function parseNumericValue($value): int
    {
        // Use the enhanced formatting method
        return $this->parseFormattedNumber($value);
    }

    /**
     * Parse NUP value to string
     * @param mixed $value
     * @return string
     */
    private function parseNupValue($value): string
    {
        if (empty($value)) return '';
        return (string) $value;
    }

    /**
     * Sanitize text value and normalize whitespace
     * @param mixed $value
     * @return string|null
     */
    private function sanitizeTextValue($value): ?string
    {
        if (empty($value)) return null;
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    /**
     * Get dashboard statistics and metrics
     * @return array
     */
    private function getDashboardStats(): array
    {
        $stats = [
            'total_records' => BmnInventarisasiKkSakti::count(),
            'total_batches' => BmnInventarisasiKkSakti::distinct('upload_batch')->count(),
            'total_nilai' => BmnInventarisasiKkSakti::sum('nilai_total'),
            'latest_batch' => BmnInventarisasiKkSakti::max('upload_batch'),
            'date_range' => [
                'earliest' => BmnInventarisasiKkSakti::min('created_at'),
                'latest' => BmnInventarisasiKkSakti::max('created_at')
            ]
        ];

        // Enhanced statistics
        $stats['avg_nilai_per_record'] = ($stats['total_records'] > 0 && is_numeric($stats['total_nilai'])) ?
            round($stats['total_nilai'] / $stats['total_records']) : 0;

        $stats['records_by_status'] = [
            'labeled' => BmnInventarisasiKkSakti::where('status_pelabelan', 'like', '%sudah%')->count(),
            'unlabeled' => BmnInventarisasiKkSakti::where(function($q) {
                $q->whereNull('status_pelabelan')
                  ->orWhere('status_pelabelan', '')
                  ->orWhere('status_pelabelan', 'like', '%belum%');
            })->count()
        ];

        $stats['top_kode_barang'] = BmnInventarisasiKkSakti::select('kode_barang')
            ->selectRaw('COUNT(*) as total, SUM(nilai_total) as nilai_total')
            ->groupBy('kode_barang')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $stats['monthly_uploads'] = BmnInventarisasiKkSakti::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->selectRaw('COUNT(*) as records, COUNT(DISTINCT upload_batch) as batches')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return $stats;
    }

    /**
     * Validate record data against mandatory field requirements
     * @param array $data
     * @param int $rowIndex
     * @return array ['isValid' => bool, 'errors' => array, 'errorType' => string]
     */
    private function validateRecordData(array $data, int $rowIndex): array
    {
        $errors = [];
        $errorType = null;

        // Define mandatory fields
        $mandatoryFields = [
            'mak' => 'MAK',
            'nilai_satuan' => 'Nilai Satuan',
            'kode_transaksi' => 'Kode Transaksi',
            'jenis_transaksi' => 'Jenis Transaksi',
            'kode_barang' => 'Kode Barang',
            'jumlah' => 'Jumlah',
            'nup_awal' => 'NUP Awal',
            'nup_akhir' => 'NUP Akhir',
            'nilai_total' => 'Nilai Total',
            'nama_pt' => 'Nama PT',
            'nomor_dokumen' => 'Nomor Dokumen',
            'bagian' => 'Bagian'
        ];

        // Check mandatory fields
        foreach ($mandatoryFields as $field => $label) {
            $value = $data[$field] ?? null;

            if (empty($value) || (is_string($value) && trim($value) === '')) {
                $errors[] = "Field {$label} wajib diisi";
                $errorType = 'MISSING_MANDATORY';
            }
        }

        // Validate NUP format (must be numeric only)
        if (!empty($data['nup_awal']) && !preg_match('/^\d+$/', (string)$data['nup_awal'])) {
            $errors[] = "NUP Awal harus berupa angka";
            $errorType = 'INVALID_NUP_FORMAT';
        }

        if (!empty($data['nup_akhir']) && !preg_match('/^\d+$/', (string)$data['nup_akhir'])) {
            $errors[] = "NUP Akhir harus berupa angka";
            $errorType = 'INVALID_NUP_FORMAT';
        }

        // Validate date format if provided
        if (!empty($data['tanggal']) && $data['tanggal'] === null) {
            $errors[] = "Format tanggal tidak valid";
            $errorType = 'INVALID_DATE_FORMAT';
        }

        if (!empty($data['tanggal_pelabelan']) && $data['tanggal_pelabelan'] === null) {
            $errors[] = "Format tanggal pelabelan tidak valid";
            $errorType = 'INVALID_DATE_FORMAT';
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'errorType' => $errorType
        ];
    }

    /**
     * Get error data with filtering and pagination
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getErrorData(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = BmnInventarisasiKkSakti::withErrors();

            // Apply filters
            if ($request->filled('batch_id')) {
                $query->byBatch($request->batch_id);
            }

            if ($request->filled('error_type')) {
                $query->byErrorType($request->error_type);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('kode_barang', 'like', "%{$search}%")
                      ->orWhere('nup_awal', 'like', "%{$search}%")
                      ->orWhere('nup_akhir', 'like', "%{$search}%")
                      ->orWhere('error_messages', 'like', "%{$search}%");
                });
            }

            if ($request->filled('needs_review')) {
                if ($request->needs_review === 'true') {
                    $query->needsReview();
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDir = $request->input('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            // Pagination
            $perPage = min($request->input('per_page', 50), 200);
            $data = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Get error data KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get error statistics
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getErrorStats(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $batchId = $request->input('batch_id');
            $stats = BmnInventarisasiKkSakti::getErrorStats($batchId);

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export error data to Excel format
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function exportErrorsToExcel(Request $request)
    {
        try {
            $query = BmnInventarisasiKkSakti::withErrors();

            // Apply filters
            if ($request->filled('batch_id')) {
                $query->byBatch($request->batch_id);
            }

            if ($request->filled('error_type')) {
                $query->byErrorType($request->error_type);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('kode_barang', 'like', "%{$search}%")
                      ->orWhere('nup_awal', 'like', "%{$search}%")
                      ->orWhere('nup_akhir', 'like', "%{$search}%")
                      ->orWhere('error_messages', 'like', "%{$search}%");
                });
            }

            $errorData = $query->orderBy('created_at', 'desc')->get();

            if ($errorData->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data error untuk di-export'
                ], 404);
            }

            // Create export class untuk Excel dengan multiple sheets
            $export = new class($errorData) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {

                private $errorData;

                public function __construct($errorData) {
                    $this->errorData = $errorData;
                }

                public function sheets(): array
                {
                    return [
                        'PETUNJUK' => new class implements \Maatwebsite\Excel\Concerns\FromArray,
                                                        \Maatwebsite\Excel\Concerns\WithStyles {
                            public function array(): array
                            {
                                return [
                                    ['PETUNJUK PERBAIKAN DATA ERROR KK SAKTI'],
                                    [''],
                                    ['LANGKAH-LANGKAH:'],
                                    ['1. Pindah ke sheet "Data Error" untuk melihat data yang bermasalah'],
                                    ['2. Perbaiki data di kolom yang berwarna KUNING (mandatory fields)'],
                                    ['3. JANGAN mengubah kolom ID (abu-abu) - ini untuk mapping data'],
                                    ['4. Kolom MERAH hanya sebagai informasi error, tidak perlu diubah'],
                                    ['5. Simpan file Excel setelah perbaikan selesai'],
                                    ['6. Upload kembali file ini melalui dashboard'],
                                    [''],
                                    ['KETERANGAN WARNA:'],
                                    ['🔸 ABU-ABU: ID Record (Jangan diubah!)'],
                                    ['🔸 KUNING: Field Mandatory (Wajib diisi)'],
                                    ['🔸 MERAH: Informasi Error (Hanya referensi)'],
                                    ['🔸 PUTIH: Field Optional (Boleh kosong)'],
                                    [''],
                                    ['FIELD MANDATORY (WAJIB DIISI):'],
                                    ['✓ MAK'],
                                    ['✓ Nilai Satuan'],
                                    ['✓ Kode Transaksi'],
                                    ['✓ Jenis Transaksi'],
                                    ['✓ Kode Barang'],
                                    ['✓ Jumlah'],
                                    ['✓ NUP Awal (hanya angka)'],
                                    ['✓ NUP Akhir (hanya angka)'],
                                    ['✓ Nilai Total'],
                                    ['✓ Nama PT'],
                                    ['✓ Nomor Dokumen'],
                                    ['✓ Bagian'],
                                    [''],
                                    ['CATATAN PENTING:'],
                                    ['• NUP harus berupa angka saja (tidak boleh ada huruf atau simbol)'],
                                    ['• Semua field mandatory harus diisi (tidak boleh kosong)'],
                                    ['• Format tanggal: dd/mm/yyyy (contoh: 15/03/2025)'],
                                    ['• Jangan menambah atau mengurangi baris data'],
                                    ['• Jangan mengubah nama kolom/header']
                                ];
                            }

                            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                            {
                                // Title styling
                                $sheet->getStyle('A1')->applyFromArray([
                                    'font' => [
                                        'bold' => true,
                                        'size' => 16,
                                        'color' => ['rgb' => 'FFFFFF']
                                    ],
                                    'fill' => [
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => ['rgb' => '4472C4']
                                    ],
                                    'alignment' => ['horizontal' => 'center']
                                ]);

                                // Section headers
                                $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
                                $sheet->getStyle('A11')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
                                $sheet->getStyle('A17')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
                                $sheet->getStyle('A30')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

                                // Auto-size columns
                                $sheet->getColumnDimension('A')->setAutoSize(true);

                                return [];
                            }
                        },
                        'Data Error' => new class($this->errorData) implements \Maatwebsite\Excel\Concerns\FromArray,
                                                      \Maatwebsite\Excel\Concerns\WithHeadings,
                                                      \Maatwebsite\Excel\Concerns\WithStyles,
                                                      \Maatwebsite\Excel\Concerns\WithColumnWidths {

                            private $errorData;

                            public function __construct($errorData) {
                                $this->errorData = $errorData;
                            }

                            public function array(): array
                            {
                                $data = [];

                                $formatDate = function($dateValue) {
                                    if (empty($dateValue)) return '';
                                    if ($dateValue instanceof \Carbon\Carbon) return $dateValue->format('d/m/Y');
                                    if (!is_string($dateValue)) return (string) $dateValue;

                                    // Try direct parsing first (for ISO dates, d/m/Y, etc.)
                                    try {
                                        return \Carbon\Carbon::parse($dateValue)->format('d/m/Y');
                                    } catch (\Exception $e) {
                                        // Handle Indonesian date format using regex
                                        $indonesianMonths = [
                                            'januari' => '01', 'februari' => '02', 'maret' => '03',
                                            'april' => '04', 'mei' => '05', 'juni' => '06',
                                            'juli' => '07', 'agustus' => '08', 'september' => '09',
                                            'oktober' => '10', 'november' => '11', 'desember' => '12'
                                        ];

                                        // Pattern: "17 Januari 2025" or "17-Januari-2025"
                                        if (preg_match('/(\d{1,2})\s*[\-\s]\s*(\w+)\s*[\-\s]\s*(\d{4})/i', $dateValue, $matches)) {
                                            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                            $monthName = strtolower(trim($matches[2]));
                                            $year = $matches[3];

                                            if (isset($indonesianMonths[$monthName])) {
                                                $month = $indonesianMonths[$monthName];
                                                try {
                                                    return \Carbon\Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day)->format('d/m/Y');
                                                } catch (\Exception $e2) {
                                                    // Invalid date combination
                                                }
                                            }
                                        }

                                        // Pattern: "2025-01-17" or "17-01-2025"
                                        if (preg_match('/(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})/', $dateValue, $matches)) {
                                            try {
                                                return \Carbon\Carbon::createFromFormat('Y-m-d', $matches[1] . '-' . $matches[2] . '-' . $matches[3])->format('d/m/Y');
                                            } catch (\Exception $e2) {}
                                        }

                                        if (preg_match('/(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{4})/', $dateValue, $matches)) {
                                            try {
                                                return \Carbon\Carbon::createFromFormat('d/m/Y', $matches[1] . '/' . $matches[2] . '/' . $matches[3])->format('d/m/Y');
                                            } catch (\Exception $e2) {}
                                        }

                                        // If all parsing fails, return empty string instead of original
                                        return '';
                                    }
                                };

                                foreach ($this->errorData as $item) {
                                    $data[] = [
                                        $item->id,
                                        $formatDate($item->tanggal),
                                        $item->bulan_dok ?? '',
                                        $item->mak ?? '',
                                        $item->nilai_satuan ?? 0,
                                        $item->kode_transaksi ?? '',
                                        $item->jenis_transaksi ?? '',
                                        $item->kode_barang ?? '',
                                        $item->jumlah ?? 0,
                                        $item->uraian_barang ?? '',
                                        $item->nup_awal ?? '',
                                        $item->nup_akhir ?? '',
                                        $item->nilai_total ?? 0,
                                        $item->nilai_spm ?? 0,
                                        $item->nomor_karwas ?? '',
                                        $item->link_dokumen ?? '',
                                        $item->merk_tipe_bmn ?? '',
                                        $item->nama_pt ?? '',
                                        $item->nomor_dokumen ?? '',
                                        $formatDate($item->tgl_bast),
                                        $item->pekerjaan ?? '',
                                        $item->lokasi ?? '',
                                        $item->bagian ?? '',
                                        $item->keterangan ?? '',
                                        $item->spby ?? '',
                                        $item->status_pelabelan ?? '',
                                        $formatDate($item->tanggal_pelabelan),
                                        // Error Information columns
                                        $item->error_type ?? '',
                                        str_replace(';', "\n", $item->error_messages ?? ''),
                                        $item->needs_review ? 'Ya' : 'Tidak'
                                    ];
                                }

                                return $data;
                            }

                            public function headings(): array
                            {
                                return [
                                    'ID',
                                    'Tanggal',
                                    'Bulan Dok',
                                    'MAK *',
                                    'Nilai Satuan *',
                                    'Kode Transaksi *',
                                    'Jenis Transaksi *',
                                    'Kode Barang *',
                                    'Jumlah *',
                                    'Uraian Barang',
                                    'NUP Awal *',
                                    'NUP Akhir *',
                                    'Nilai Total *',
                                    'Nilai SPM',
                                    'Nomor Karwas',
                                    'Link Dokumen',
                                    'Merk Tipe BMN',
                                    'Nama PT *',
                                    'Nomor Dokumen *',
                                    'Tgl BAST',
                                    'Pekerjaan',
                                    'Lokasi',
                                    'Bagian *',
                                    'Keterangan',
                                    'SPBY',
                                    'Status Pelabelan',
                                    'Tanggal Pelabelan',
                                    // Error information (read-only)
                                    'Tipe Error',
                                    'Pesan Error',
                                    'Perlu Review'
                                ];
                            }

                            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                            {
                                // Header styling
                                $sheet->getStyle('A1:AD1')->applyFromArray([
                                    'font' => [
                                        'bold' => true,
                                        'color' => ['rgb' => 'FFFFFF'],
                                    ],
                                    'fill' => [
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => ['rgb' => '4472C4']
                                    ],
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                            'color' => ['rgb' => '000000'],
                                        ],
                                    ],
                                ]);

                                // Mandatory fields (yellow background)
                                $mandatoryColumns = ['D', 'E', 'F', 'G', 'H', 'I', 'K', 'L', 'M', 'R', 'S', 'W'];
                                foreach ($mandatoryColumns as $col) {
                                    $sheet->getStyle($col.'1')->applyFromArray([
                                        'fill' => [
                                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                            'color' => ['rgb' => 'FFE699'] // Light yellow
                                        ]
                                    ]);
                                }

                                // Error information columns (red background)
                                $sheet->getStyle('AB1:AD1')->applyFromArray([
                                    'fill' => [
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => ['rgb' => 'FF6B6B'] // Light red
                                    ]
                                ]);

                                // ID column (gray background - don't edit)
                                $sheet->getStyle('A:A')->applyFromArray([
                                    'fill' => [
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => ['rgb' => 'D6DCE5'] // Light gray
                                    ]
                                ]);

                                // Auto-size columns
                                foreach (range('A', 'AD') as $col) {
                                    $sheet->getColumnDimension($col)->setAutoSize(true);
                                }

                                // Wrap text for error messages
                                $sheet->getStyle('AC:AC')->getAlignment()->setWrapText(true);

                                return [];
                            }

                            public function columnWidths(): array
                            {
                                return [
                                    'A' => 10, // ID
                                    'B' => 12, // Tanggal
                                    'J' => 30, // Uraian Barang
                                    'AC' => 50, // Error Messages
                                    'V' => 15, // Lokasi
                                    'W' => 15, // Bagian
                                ];
                            }
                        }
                    ];
                }
            };

            $fileName = 'KK_SAKTI_Data_Error_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download($export, $fileName);

        } catch (Exception $e) {
            Log::error('Export Error Data KK Sakti error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload fixed data to replace error records
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFixData(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
            'sheet_name' => 'string|nullable'
        ]);

        $file = null;
        $sheetName = null;

        try {
            set_time_limit(600);
            ini_set('memory_limit', '512M');
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $sheetName = $request->input('sheet_name', 'Data Error');

            // Pre-validate Excel structure (ID required for fix data upload)
            $this->validateExcelStructure($file, $sheetName, true);

            $result = $this->processFixDataFile($file, $sheetName);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Fix data uploaded and processed successfully',
                'data' => array_merge($result, [
                    'filename' => $file->getClientOriginalName()
                ])
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Upload Fix Data KK Sakti error: ' . $e->getMessage(), [
                'file' => $file ? $file->getClientOriginalName() : 'unknown',
                'sheet' => $sheetName ?? 'unknown',
                'user' => auth()->id() ?? 'anonymous'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Fix data upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process fix data file and update error records
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $sheetName
     * @return array
     */
    private function processFixDataFile($file, $sheetName): array
    {
        $processed = 0;
        $matched = 0;
        $notFound = 0;
        $errors = [];

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());
        $worksheet = $spreadsheet->getSheetByName($sheetName);

        $rows = $worksheet->toArray();
        $headers = array_shift($rows);

        // Clean headers by removing asterisks and trimming whitespace
        $cleanHeaders = array_map(function($header) {
            return trim(str_replace('*', '', $header ?? ''));
        }, $headers);

        // Find ID column index
        $idColumnIndex = false;
        foreach ($cleanHeaders as $index => $header) {
            if (strcasecmp($header, 'ID') === 0) {
                $idColumnIndex = $index;
                break;
            }
        }

        if ($idColumnIndex === false) {
            throw new Exception("ID column not found in Excel headers");
        }

        // Create dynamic column mapping based on actual headers
        $columnMapping = $this->createColumnMappingFromHeaders($cleanHeaders);

        foreach ($rows as $rowIndex => $row) {
            $actualRowIndex = $rowIndex + 2; // +2 for header and 1-based indexing

            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $recordId = $row[$idColumnIndex] ?? null;

                if (empty($recordId)) {
                    $errors[] = "Row {$actualRowIndex}: ID not found";
                    continue;
                }

                // Find existing error record
                $existingRecord = BmnInventarisasiKkSakti::find($recordId);

                if (!$existingRecord) {
                    $notFound++;
                    $errors[] = "Row {$actualRowIndex}: Record with ID {$recordId} not found";
                    continue;
                }

                if (!$existingRecord->hasErrors()) {
                    $errors[] = "Row {$actualRowIndex}: Record ID {$recordId} is not marked as error";
                    continue;
                }

                // Map fix data
                $fixData = $this->mapRowData($row, $columnMapping, $existingRecord->upload_batch, $actualRowIndex);

                if (!$fixData) {
                    $errors[] = "Row {$actualRowIndex}: Invalid fix data";
                    continue;
                }

                // Validate fix data
                $validation = $this->validateRecordData($fixData, $actualRowIndex);

                if (!$validation['isValid']) {
                    $errors[] = "Row {$actualRowIndex}: Fix data still has errors - " . implode(', ', $validation['errors']);
                    continue;
                }

                // Update record with fix data and mark as fixed
                $fixData['error_status'] = 'fixed';
                $fixData['error_type'] = null;
                $fixData['error_messages'] = null;
                $fixData['error_details'] = null;
                $fixData['needs_review'] = false;

                $existingRecord->update($fixData);

                $matched++;
                $processed++;

            } catch (Exception $e) {
                $errors[] = "Row {$actualRowIndex}: " . $e->getMessage();
            }
        }

        return compact('processed', 'matched', 'notFound', 'errors');
    }

    /**
     * Create column mapping from Excel headers
     * @param array $cleanHeaders
     * @return array
     */
    private function createColumnMappingFromHeaders(array $cleanHeaders): array
    {
        $mapping = [];

        // Define header to database field mapping
        $headerMapping = [
            'Tanggal' => 'tanggal',
            'Bulan Dokumen' => 'bulan_dok',
            'Bulan Dok' => 'bulan_dok',
            'MAK' => 'mak',
            'Nilai Satuan' => 'nilai_satuan',
            'Kode Transaksi' => 'kode_transaksi',
            'Jenis Transaksi' => 'jenis_transaksi',
            'Kode Barang' => 'kode_barang',
            'Jumlah' => 'jumlah',
            'Uraian Barang' => 'uraian_barang',
            'NUP Awal' => 'nup_awal',
            'NUPAwal' => 'nup_awal',
            'NUP Akhir' => 'nup_akhir',
            'NUPAkhir' => 'nup_akhir',
            'Nilai Total' => 'nilai_total',
            'Nilai SPM' => 'nilai_spm',
            'Nomor Karwas' => 'nomor_karwas',
            'Link Dokumen' => 'link_dokumen',
            'Merek/Tipe BMN' => 'merk_tipe_bmn',
            'Merk/Tipe BMN' => 'merk_tipe_bmn',
            'Nama PT' => 'nama_pt',
            'Nomor Dokumen' => 'nomor_dokumen',
            'Tanggal BAST' => 'tgl_bast',
            'Tgl BAST' => 'tgl_bast',
            'Pekerjaan' => 'pekerjaan',
            'Lokasi' => 'lokasi',
            'Bagian' => 'bagian',
            'Keterangan' => 'keterangan',
            'SPBY' => 'spby',
            'Status Pelabelan' => 'status_pelabelan',
            'Tanggal Pelabelan' => 'tanggal_pelabelan'
        ];

        // Create mapping based on actual headers
        foreach ($cleanHeaders as $index => $header) {
            if (isset($headerMapping[$header])) {
                $mapping[$index] = $headerMapping[$header];
            }
        }

        return $mapping;
    }
}
