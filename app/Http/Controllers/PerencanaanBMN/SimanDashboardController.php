<?php
namespace App\Http\Controllers\PerencanaanBMN;

use App\Http\Controllers\Controller;
use App\Libraries\TarikDataSiman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Exports\ExportsBMN\MultiSheetPspExport;
use Barryvdh\Snappy\Facades\SnappyPdf;

class SimanDashboardController extends Controller
{
    private $simanService;

    public function __construct()
    {
        try {
            $this->simanService = new TarikDataSiman();
        } catch (Exception $e) {
            Log::error('Failed to initialize TarikDataSiman: ' . $e->getMessage());
            $this->simanService = null;
        }
    }

    /**
     * Display dashboard page with updated asset types (removed RKBMN)
     */
    public function index()
    {
        // v2.0 - Complete 14 asset types
        $assetTypes = [
            'alat_besar' => 'Alat Besar',
            'alat_persenjataan' => 'Alat Persenjataan',
            'angkutan_bermotor' => 'Angkutan Bermotor',
            'tak_berwujud' => 'Tak Berwujud',
            'tetap_lainnya' => 'Tetap Lainnya',
            'bangunan_air' => 'Bangunan Air',
            'gedung_bangunan' => 'Gedung & Bangunan',
            'instalasi_jaringan' => 'Instalasi & Jaringan',
            'jalan_jembatan' => 'Jalan & Jembatan',
            'kdp' => 'KDP (Konstruksi Dalam Pengerjaan)',
            'tanah' => 'Tanah',
            'rumah' => 'Rumah Negara',
            'non_tik' => 'Peralatan & Mesin Non TIK',
            'khusus_tik' => 'Peralatan & Mesin Khusus TIK'
        ];

        return view('PerencanaanBMN.Bagian.SimanDashboardPage', compact('assetTypes'));
    }

    /**
     * Get dashboard statistics for all asset types
     */
    public function getDashboardStats()
    {
        try {
            if (!$this->simanService) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'SIMAN service not available'
                ], 500);
            }

            $stats = $this->simanService->getDashboardStats();

            // Get latest sync status - parse JSON details
            $latestSync = DB::table('siman_sync_logs')
                ->orderBy('start_time', 'desc')
                ->first();

            $syncInfo = [
                'status' => $latestSync->status ?? 'never',
                'start_time' => $latestSync->start_time ?? null,
                'end_time' => $latestSync->end_time ?? null,
            ];

            // Parse details JSON if exists
            if ($latestSync && $latestSync->details) {
                $details = json_decode($latestSync->details, true);
                $syncInfo['details'] = $details;
                $syncInfo['asset_type'] = $details['asset_type'] ?? null;
                $syncInfo['operation'] = $details['operation'] ?? null;
                $syncInfo['records_processed'] = $details['records_processed'] ?? null;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'asset_stats' => $stats,
                    'latest_sync' => $syncInfo
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sync history logs
     */
    public function getSyncHistory(Request $request)
    {
        try {
            $limit = min($request->get('limit', 20), 100);

            $logs = DB::table('siman_sync_logs')
                ->orderBy('start_time', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($log) {
                    // Parse JSON details for each log
                    $details = $log->details ? json_decode($log->details, true) : [];

                    return [
                        'id' => $log->id,
                        'status' => $log->status,
                        'start_time' => $log->start_time,
                        'end_time' => $log->end_time,
                        'asset_type' => $details['asset_type'] ?? null,
                        'operation' => $details['operation'] ?? null,
                        'records_processed' => $details['records_processed'] ?? null,
                        'processing_time_seconds' => $details['processing_time_seconds'] ?? null,
                        'details' => $details
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $logs
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get sync history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset metadata for dynamic filtering
     */
    public function getAssetMetadata($assetType)
    {
        try {
            // v2.0 - Complete 14 asset types
            $validTypes = ['alat_besar', 'alat_persenjataan', 'angkutan_bermotor', 'tak_berwujud', 'tetap_lainnya', 'bangunan_air', 'gedung_bangunan', 'instalasi_jaringan', 'jalan_jembatan', 'kdp', 'tanah', 'rumah', 'non_tik', 'khusus_tik'];

            if (!in_array($assetType, $validTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid asset type'
                ], 400);
            }

            // Define asset-specific filter options
            $metadata = $this->getAssetSpecificFilters($assetType);

            return response()->json([
                'status' => 'success',
                'data' => $metadata
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get asset metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset-specific filter options
     */
    private function getAssetSpecificFilters($assetType)
    {
        $filters = [];

        switch ($assetType) {
            case 'alat_angkutan':
                $filters = [
                    'no_polisi' => ['label' => 'No. Polisi', 'type' => 'text'],
                    'no_mesin' => ['label' => 'No. Mesin', 'type' => 'text'],
                    'tahun_buat' => ['label' => 'Tahun Pembuatan', 'type' => 'number'],
                    'bahan_bakar' => ['label' => 'Bahan Bakar', 'type' => 'select']
                ];
                break;

            case 'gedung_bangunan':
                $filters = [
                    'kd_kabkota' => ['label' => 'Kab/Kota', 'type' => 'select'],
                    'luas_bangunan' => ['label' => 'Luas Bangunan', 'type' => 'range'],
                    'jumlah_lantai' => ['label' => 'Jumlah Lantai', 'type' => 'number'],
                    'jenis_sertifikat' => ['label' => 'Jenis Sertifikat', 'type' => 'select']
                ];
                break;

            case 'tanah':
                $filters = [
                    'luas_tanah_seluruhnya' => ['label' => 'Luas Tanah', 'type' => 'range'],
                    'jenis_sertifikat' => ['label' => 'Jenis Sertifikat', 'type' => 'select'],
                    'status_sengketa' => ['label' => 'Status Sengketa', 'type' => 'select'],
                    'kecamatan' => ['label' => 'Kecamatan', 'type' => 'text']
                ];
                break;

            case 'pm_tik':
                $filters = [
                    'jns_processor' => ['label' => 'Jenis Processor', 'type' => 'select'],
                    'processor' => ['label' => 'Processor', 'type' => 'range'],
                    'memori' => ['label' => 'Memori (GB)', 'type' => 'range'],
                    'hardisk' => ['label' => 'Hardisk (GB)', 'type' => 'range']
                ];
                break;

            default:
                $filters = [
                    'nilai_perolehan' => ['label' => 'Nilai Perolehan', 'type' => 'range'],
                    'tahun_perolehan' => ['label' => 'Tahun Perolehan', 'type' => 'number']
                ];
        }

        return [
            'asset_type' => $assetType,
            'specific_filters' => $filters,
            'common_filters' => [
                'search' => ['label' => 'Pencarian Global', 'type' => 'text'],
                'kode_kl' => ['label' => 'Kode KL', 'type' => 'text'],
                'nama_satker' => ['label' => 'Satker', 'type' => 'text'],
                'kondisi' => ['label' => 'Kondisi', 'type' => 'select', 'options' => ['B' => 'Baik', 'RR' => 'Rusak Ringan', 'RB' => 'Rusak Berat']]
            ]
        ];
    }

    // Keep existing methods unchanged
    public function getData($assetType, Request $request)
    {
        try {
            Log::info("SIMAN getData called", [
                'asset_type' => $assetType,
                'params' => $request->all()
            ]);

            if (!$this->simanService) {
                Log::error("SIMAN service not available");
                return response()->json([
                    'status' => 'error',
                    'message' => 'SIMAN service not available',
                    'data' => []
                ], 500);
            }

            // v2.0 - Complete 14 asset types
            $validTypes = ['alat_besar', 'alat_persenjataan', 'angkutan_bermotor', 'tak_berwujud', 'tetap_lainnya', 'bangunan_air', 'gedung_bangunan', 'instalasi_jaringan', 'jalan_jembatan', 'kdp', 'tanah', 'rumah', 'non_tik', 'khusus_tik'];

            if (!in_array($assetType, $validTypes)) {
                Log::error("Invalid asset type", ['asset_type' => $assetType]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid asset type: ' . $assetType,
                    'data' => []
                ], 400);
            }

            $page = max(1, (int)$request->get('page', 1));
            $perPage = min(max((int)$request->get('per_page', 100), 10), 500);

            // Mengambil filter dari request
            $filters = $request->only(['kode_barang', 'kondisi', 'search', 'nup']);
            if ($request->has('tanggal_perolehan') && is_numeric($request->get('tanggal_perolehan'))) {
                $filters['tanggal_perolehan'] = $request->get('tanggal_perolehan');
            }

            // Mengambil parameter sorting dengan aman
            $sortBy = $request->get('sortBy', 'id');
            $sortDir = $request->get('sortDir', 'desc');

            $this->simanService->setSilentMode(true);

            // Mengirim semua parameter ke service
            $result = $this->simanService->getDataWithLazyDecryption($assetType, $perPage, $filters, $sortBy, $sortDir, $page);

            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'pagination' => $result['pagination'],
            ]);

        } catch (Exception $e) {
            Log::error('Error in SIMAN getData', [
                'asset_type' => $assetType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data: ' . $e->getMessage(),
                'data' => [],
                'debug_info' => [
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ]
            ], 500);
        }
    }

    public function exportData($assetType, $format, Request $request)
    {
        try {
            // Mengambil filter dari request, sama seperti di fungsi getData
            $filters = $request->only(['kode_barang', 'kondisi', 'search']);
             if ($request->has('tanggal_perolehan') && is_numeric($request->get('tanggal_perolehan'))) {
                $filters['tanggal_perolehan'] = $request->get('tanggal_perolehan');
            }

            // Mengambil SEMUA data yang cocok (limit diatur sangat tinggi)
            $result = $this->simanService->getDataWithLazyDecryption($assetType, 10000, $filters, 'id', 'asc', 1);
            $dataToExport = collect($result['data']);

            $exportObject = new class($dataToExport) implements FromCollection, WithHeadings, WithMapping {
                private $collection;

                public function __construct($collection)
                {
                    $this->collection = $collection;
                }

                public function collection()
                {
                    return $this->collection;
                }

                public function headings(): array
                {
                    return [
                        'Kode Barang',
                        'Nama Barang',
                        'NUP',
                        'Kondisi',
                        'Kuantitas',
                        'Merk',
                        'Tahun Perolehan',
                        'Nilai Perolehan',
                        'Nilai Buku',
                        'Status Penggunaan',
                    ];
                }

                public function map($row): array
                {
                    return [
                        $row['kode_barang'] ?? '-',
                        $row['nama_barang'] ?? '-',
                        $row['nup'] ?? '-',
                        $row['kondisi'] ?? '-',
                        $row['kuantitas'] ?? '-',
                        $row['merk'] ?? '-',
                        isset($row['tanggal_perolehan']) ? date('Y', strtotime($row['tanggal_perolehan'])) : '-',
                        $row['nilai_perolehan'] ?? 0,
                        $row['nilai_buku'] ?? 0,
                        $row['status_penggunaan'] ?? '-',
                    ];
                }
            };

            $fileName = "SIMAN_{$assetType}_" . date('Y-m-d') . ($format === 'csv' ? '.csv' : '.xlsx');
            return Excel::download($exportObject, $fileName);

        } catch (Exception $e) {
            Log::error('Error exporting SIMAN data: ' . $e->getMessage());
            return back()->withErrors('Gagal mengekspor data. Silakan coba lagi.');
        }
    }

    private function getDataFallback($assetType, $page, $perPage, $filters)
    {
        try {
            Log::info("SIMAN using fallback method");

            $assetMappings = [
                'alat_angkutan' => 'SIMAN_ASET_ALAT_ANGKUTAN',
                'alat_berat' => 'SIMAN_ASET_ALAT_BERAT',
                'gedung_bangunan' => 'SIMAN_ASET_GEDUNG_BANGUNAN',
                'pm_tik' => 'SIMAN_ASET_PM_TIK',
                'pm_non_tik' => 'SIMAN_ASET_PM_NON_TIK',
                'rumah_negara' => 'SIMAN_ASET_RUMAH_NEGARA',
                'tak_berwujud' => 'SIMAN_ASET_TAK_BERWUJUD',
                'tanah' => 'SIMAN_ASET_TANAH',
                'tetap_lainnya' => 'SIMAN_ASET_TETAP_LAINNYA'
            ];

            $tableName = $assetMappings[$assetType] ?? null;
            if (!$tableName) {
                throw new Exception('Unknown asset type: ' . $assetType);
            }

            $reflection = new \ReflectionClass($this->simanService);
            $pdoProperty = $reflection->getProperty('pdo');
            $pdoProperty->setAccessible(true);
            $pdo = $pdoProperty->getValue($this->simanService);

            if (!$pdo) {
                throw new Exception('PDO connection not available');
            }

            // Kode Baru (Benar)
            $stmt = $pdo->prepare("SHOW TABLES LIKE '$tableName'");
            $stmt->execute();
            $tableExists = $stmt->fetch() !== false;

            if (!$tableExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Table not found. Please fetch data from API first.',
                    'data' => [],
                    'table_info' => ['exists' => false, 'table_name' => $tableName]
                ]);
            }

            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM `$tableName` ORDER BY id DESC LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            $records = $stmt->fetchAll();

            Log::info("SIMAN fallback success", [
                'table' => $tableName,
                'records' => count($records)
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $records,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($records),
                    'last_page' => 1,
                    'has_more' => false
                ],
                'table_info' => [
                    'exists' => true,
                    'table_name' => $tableName,
                    'method_used' => 'fallback_direct_query'
                ]
            ]);

        } catch (Exception $e) {
            Log::error("SIMAN fallback failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Fallback method failed: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * [REMOVED] decryptData() method - no longer needed in v2.0 (plain data from API)
     */

    public function fetchFromAPI($assetType, Request $request)
    {
        try {
            if (!$this->simanService) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'SIMAN service not available',
                    'error_code' => 'SERVICE_UNAVAILABLE'
                ], 500);
            }

            // v2.0 - Complete 14 asset types
            $validTypes = ['alat_besar', 'alat_persenjataan', 'angkutan_bermotor', 'tak_berwujud', 'tetap_lainnya', 'bangunan_air', 'gedung_bangunan', 'instalasi_jaringan', 'jalan_jembatan', 'kdp', 'tanah', 'rumah', 'non_tik', 'khusus_tik'];

            if (!in_array($assetType, $validTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid asset type: ' . $assetType,
                    'error_code' => 'INVALID_ASSET_TYPE'
                ], 400);
            }

            Log::info("Starting v2.0 SIMAN API fetch", [
                'asset_type' => $assetType,
                'user_ip' => $request->ip()
            ]);

            set_time_limit(0);
            $this->simanService->setSilentMode(true);
            ob_start();

            try {
                $options = [
                    'lazy_decrypt' => $request->get('lazy_decrypt', true),
                    'batch_size' => min($request->get('batch_size', 1000), 2000)
                ];

                $result = $this->simanService->fetchAndSaveToSingleTable($assetType, $options);
                ob_end_clean();

                if ($result['status'] === 'success') {
                    $result['data']['asset_type'] = $assetType;
                    $result['data']['timestamp'] = now()->toISOString();

                    Log::info("SIMAN API fetch completed successfully", [
                        'asset_type' => $assetType,
                        'records' => $result['data']['inserted_records'] ?? 0,
                        'processing_time' => $result['data']['processing_time_seconds'] ?? 0
                    ]);
                }

                return response()->json($result);

            } catch (Exception $e) {
                ob_end_clean();
                throw $e;
            }

        } catch (Exception $e) {
            Log::error('Error fetching from SIMAN API', [
                'asset_type' => $assetType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch from API: ' . $e->getMessage(),
                'error_code' => 'API_FETCH_ERROR',
                'asset_type' => $assetType
            ], 500);
        }
    }

    /**
     * Run PSP validation with filters (without saving to database)
     */
    public function runPspValidation(Request $request)
    {
        try {
            // Perubahan dimulai: Mengambil parameter filter baru
            $filterNilai = $request->get('filter_nilai', '<100'); // '<100' atau '>=100'
            $tahunDari = $request->get('tahun_dari', 2010);
            $tahunSampai = $request->get('tahun_sampai', date('Y'));
            $page = max(1, (int)$request->get('page', 1));
            $perPage = min(250, max(50, (int)$request->get('per_page', 100)));

            $nup = $request->get('nup');
            $kodeBarang = $request->get('kode_barang');

            // Membuat kondisi SQL dinamis untuk filter opsional
            $nupCondition = !empty($nup) ? 'AND nup = ?' : '';
            $kodeBarangCondition = !empty($kodeBarang) ? 'AND kode_barang LIKE ?' : '';
            // Perubahan selesai

            // Build nilai condition
            $nilaiCondition = $filterNilai === '<100'
                ? 'nilai_perolehan_pertama < 100000000'
                : 'nilai_perolehan_pertama >= 100000000';

            // Base query with CTE for grouping - MENGGUNAKAN POSITIONAL PLACEHOLDER (?)
            $baseQuery = "
                WITH all_assets AS (
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_angkutan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_berat
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_gedung_bangunan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_non_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_rumah_negara
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tak_berwujud
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tanah
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tetap_lainnya
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}
                ),
                grouped_assets AS (
                    SELECT *,
                        (nup - ROW_NUMBER() OVER (
                            PARTITION BY kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                            ORDER BY nup
                        )) AS group_id
                    FROM all_assets
                ),
                final_grouped AS (
                    SELECT
                        kode_barang,
                        MIN(nup) AS nup_awal,
                        MAX(nup) AS nup_akhir,
                        nama_barang,
                        merk,
                        COUNT(*) AS kuantitas,
                        tanggal_perolehan,
                        nilai_perolehan_pertama,
                        (nilai_perolehan_pertama * COUNT(*)) AS jumlah_nilai,
                        kondisi AS keterangan
                    FROM grouped_assets
                    GROUP BY group_id, kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                    ORDER BY kode_barang, nup_awal
                )
            ";

            // Perubahan dimulai: Membuat array parameter yang dinamis
            $params = [];
            for ($i = 0; $i < 9; $i++) {
                $params[] = $tahunDari;
                $params[] = $tahunSampai;
                if (!empty($nup)) {
                    $params[] = $nup;
                }
                if (!empty($kodeBarang)) {
                    $params[] = $kodeBarang . '%';
                }
            }
            // Perubahan selesai

            // Count total records for pagination
            $countQuery = $baseQuery . " SELECT COUNT(*) as total FROM final_grouped";
            $totalRecords = DB::select($countQuery, $params)[0]->total;

            // Calculate pagination
            $offset = ($page - 1) * $perPage;
            $lastPage = ceil($totalRecords / $perPage);

            // Get paginated data
            $dataQuery = $baseQuery . " SELECT * FROM final_grouped LIMIT ? OFFSET ?";

            // Tambahkan limit dan offset ke array parameter untuk query data
            $dataParams = $params;
            $dataParams[] = $perPage;
            $dataParams[] = $offset;

            $results = DB::select($dataQuery, $dataParams);

            // Perubahan dimulai: Menyimpan filter baru ke session
            session([
                'psp_filter' => [
                    'filter_nilai' => $filterNilai,
                    'tahun_dari' => $tahunDari,
                    'tahun_sampai' => $tahunSampai,
                    'nup' => $nup,
                    'kode_barang' => $kodeBarang
                ]
            ]);
            // Perubahan selesai

            return response()->json([
                'status' => 'success',
                'data' => $results,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $totalRecords,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $totalRecords)
                ],
                // Perubahan dimulai: Mengembalikan nilai filter ke frontend
                'filters' => [
                    'nilai' => $filterNilai,
                    'tahun_dari' => $tahunDari,
                    'tahun_sampai' => $tahunSampai,
                    'nup' => $nup,
                    'kode_barang' => $kodeBarang
                ]
                // Perubahan selesai
            ]);

        } catch (Exception $e) {
            Log::error('Error running PSP validation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat validasi data: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Download PSP Excel with filters (without database) - Multi-Sheet Support
     */
    public function downloadPspAttachment(Request $request)
    {
        try {
            // Get filter parameters from request
            $filterNilai = $request->get('filter_nilai', '<100');
            $tahunDari = $request->get('tahun_dari', 2010);
            $tahunSampai = $request->get('tahun_sampai', date('Y'));
            $nup = $request->get('nup');
            $kodeBarang = $request->get('kode_barang');

            Log::info('PSP Download Request', [
                'filter_nilai' => $filterNilai,
                'tahun_dari' => $tahunDari,
                'tahun_sampai' => $tahunSampai,
                'nup' => $nup,
                'kode_barang' => $kodeBarang
            ]);

            // Build dynamic SQL conditions
            $nupCondition = !empty($nup) ? 'AND nup = ?' : '';
            $kodeBarangCondition = !empty($kodeBarang) ? 'AND kode_barang LIKE ?' : '';

            // Build nilai condition
            $nilaiCondition = $filterNilai === '<100'
                ? 'nilai_perolehan_pertama < 100000000'
                : 'nilai_perolehan_pertama >= 100000000';

            // Build complete query without LIMIT
            $query = "
                WITH all_assets AS (
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_angkutan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_berat
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_gedung_bangunan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_non_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_rumah_negara
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tak_berwujud
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tanah
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tetap_lainnya
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}
                ),
                grouped_assets AS (
                    SELECT *,
                        (nup - ROW_NUMBER() OVER (
                            PARTITION BY kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                            ORDER BY nup
                        )) AS group_id
                    FROM all_assets
                )
                SELECT
                    kode_barang,
                    MIN(nup) AS nup_awal,
                    MAX(nup) AS nup_akhir,
                    nama_barang,
                    merk,
                    COUNT(*) AS kuantitas,
                    tanggal_perolehan,
                    nilai_perolehan_pertama,
                    (nilai_perolehan_pertama * COUNT(*)) AS jumlah_nilai,
                    kondisi AS keterangan
                FROM grouped_assets
                GROUP BY group_id, kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                ORDER BY kode_barang, nup_awal
            ";

            // Build dynamic parameters array
            $params = [];
            for ($i = 0; $i < 9; $i++) {
                $params[] = $tahunDari;
                $params[] = $tahunSampai;
                if (!empty($nup)) {
                    $params[] = $nup;
                }
                if (!empty($kodeBarang)) {
                    $params[] = $kodeBarang . '%';
                }
            }

            // Execute query to get ALL data
            $data = DB::select($query, $params);

            // Check if data exists
            if (empty($data)) {
                return back()->with('warning', 'Tidak ada data yang sesuai dengan filter yang dipilih.');
            }

            // Convert to collection
            $dataCollection = collect($data);

            // Log total records
            Log::info('PSP Download Data', [
                'total_records' => count($data),
                'total_nilai' => $dataCollection->sum('jumlah_nilai'),
                'sheets_required' => ceil(count($data) / 10000)
            ]);

            // Create Multi-Sheet Excel export
            $exportObject = new MultiSheetPspExport($dataCollection);

            // Generate filename with filter info
            $filterInfo = $filterNilai === '<100' ? 'Kurang100jt' : 'Lebih100jt';
            $totalSheets = ceil(count($data) / 10000);
            $sheetInfo = $totalSheets > 1 ? "_{$totalSheets}sheets" : '';
            $filename = 'Lampiran_PSP_KPKNL_' . $filterInfo . '_' . $tahunDari . '-' . $tahunSampai . $sheetInfo . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download($exportObject, $filename);

        } catch (Exception $e) {
            Log::error('Error downloading PSP attachment: ' . $e->getMessage());
            return back()->withErrors('Gagal mengunduh file. Silakan coba lagi.');
        }
    }
    /**
     * Show form for generating PSP documents
     */
    public function generatePspDocumentForm()
    {
        try {
            $documentTypes = [
                'nodin_eselon_iii' => 'Nodin permohonan usulan PSP (Kepala Bagian | Eselon III)',
                'nodin_eselon_ii' => 'Nodin permohonan usulan PSP (Kepala Biro | Eselon II)',
                'nodin_eselon_i' => 'Nodin permohonan usulan PSP (Deputi | Eselon I)',
                'nodin_penetapan' => 'Nodin permohonan penetapan status penggunaan BMN (Deputi | Eselon I)',
                'surat_kpknl' => 'Surat permohonan PSP ke KPKNL (Sekjen Eselon I)'
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'document_types' => $documentTypes,
                    'current_date' => now()->format('Y-m-d'),
                    'current_month' => now()->format('m'),
                    'current_year' => now()->format('Y')
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error getting PSP document form data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load document form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview PSP document
     */
    public function previewPspDocument(Request $request)
    {
        try {
            $request->validate([
                'document_type' => 'required|string',
                'nomor_surat' => 'required|string|max:100',
                'jenis_bmn' => 'nullable|string|max:100',
                'kategori_nilai' => 'nullable|string|max:100',
                'nomor_referensi' => 'nullable|string|max:100',
                'tanggal_referensi' => 'nullable|string|max:50'
            ]);

            $documentType = $request->get('document_type');
            $nomorSurat = $request->get('nomor_surat');

            // Additional data for template
            $additionalData = [
                'jenis_bmn' => $request->get('jenis_bmn', 'Peralatan dan Mesin'),
                'kategori_nilai' => $request->get('kategori_nilai', 'sampai dengan Rp100.000.000,-'),
                'nomor_referensi' => $request->get('nomor_referensi'),
                'tanggal_referensi' => $request->get('tanggal_referensi')
            ];

            // Generate document data
            $documentData = $this->generateDocumentData($documentType, $nomorSurat, $additionalData);

            // Get template path
            $templatePath = $this->getTemplatePath($documentType);

            // Render view
            $html = view($templatePath, $documentData)->render();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'html' => $html,
                    'document_type' => $documentType,
                    'nomor_surat' => $nomorSurat,
                    'additional_data' => $additionalData
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error previewing PSP document: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to preview document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PSP document as PDF
     */
    public function downloadPspDocument(Request $request)
    {
        try {
            $request->validate([
                'document_type' => 'required|string',
                'nomor_surat' => 'required|string|max:100',
                'jenis_bmn' => 'nullable|string|max:100',
                'kategori_nilai' => 'nullable|string|max:100',
                'nomor_referensi' => 'nullable|string|max:100',
                'tanggal_referensi' => 'nullable|string|max:50'
            ]);

            $documentType = $request->get('document_type');
            $nomorSurat = $request->get('nomor_surat');

            // Additional data for template
            $additionalData = [
                'jenis_bmn' => $request->get('jenis_bmn', 'Peralatan dan Mesin'),
                'kategori_nilai' => $request->get('kategori_nilai', 'sampai dengan Rp100.000.000,-'),
                'nomor_referensi' => $request->get('nomor_referensi'),
                'tanggal_referensi' => $request->get('tanggal_referensi')
            ];

            // Generate document data
            $documentData = $this->generateDocumentData($documentType, $nomorSurat, $additionalData);

            // Get template path
            $templatePath = $this->getTemplatePath($documentType);

            // Generate PDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView($templatePath, $documentData);
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = $this->generateFilename($documentType, $nomorSurat);

            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('Error downloading PSP document: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate document data for templates
     */
    private function generateDocumentData($documentType, $nomorSurat, $additionalData = [])
    {
        $currentDate = now();
        $pejabatData = $this->getOfficialData($documentType);

        $baseData = [
            'nomor_surat' => $nomorSurat,
            'tanggal' => $currentDate->format('d'),
            'bulan' => $this->getIndonesianMonth($currentDate->format('n')),
            'tahun' => $currentDate->format('Y'),
            'tahun_kata' => $this->numberToWords($currentDate->format('Y')),
            'document_type' => $documentType,
            'pejabat_data' => $pejabatData
        ];

        // Add document type specific data
        switch ($documentType) {
            case 'nodin_eselon_iii':
                $baseData = array_merge($baseData, [
                    'bagian_nama' => $pejabatData['bagian_nama'] ?? 'BAGIAN ADMINISTRASI BARANG MILIK NEGARA',
                    'kepada' => 'Kepala Biro Keuangan',
                    'dari' => $pejabatData['jabatan'] ?? 'Kepala Bagian Administrasi BMN',
                    'hal' => 'Permohonan Tanda Tangan',
                    'jenis_bmn' => $additionalData['jenis_bmn'] ?? 'Peralatan dan Mesin',
                    'kategori_nilai' => $additionalData['kategori_nilai'] ?? 'sampai dengan Rp100.000.000,-'
                ]);
                break;

            case 'nodin_eselon_ii':
                $baseData = array_merge($baseData, [
                    'biro_nama' => $pejabatData['biro_nama'] ?? 'BIRO KEUANGAN',
                    'kepada' => 'Plt. Deputi Bidang Administrasi',
                    'dari' => $pejabatData['jabatan'] ?? 'Kepala Biro Keuangan',
                    'hal' => 'Permohonan Tanda Tangan',
                    'jenis_bmn' => $additionalData['jenis_bmn'] ?? 'Peralatan dan Mesin',
                    'kategori_nilai' => $additionalData['kategori_nilai'] ?? 'sampai dengan Rp100.000.000,-'
                ]);
                break;

            case 'nodin_eselon_i':
                $baseData = array_merge($baseData, [
                    'deputi_nama' => $pejabatData['deputi_nama'] ?? 'DEPUTI BIDANG ADMINISTRASI',
                    'kepada' => 'Sekretaris Jenderal DPR RI',
                    'dari' => $pejabatData['jabatan'] ?? 'Plt. Deputi Bidang Administrasi',
                    'hal' => 'Permohonan Tanda Tangan',
                    'jenis_bmn' => $additionalData['jenis_bmn'] ?? 'Peralatan dan Mesin',
                    'kategori_nilai' => $additionalData['kategori_nilai'] ?? 'sampai dengan Rp100.000.000,-'
                ]);
                break;

            case 'nodin_penetapan':
                $kategoriNilaiPenetapan = $additionalData['kategori_nilai'] === 'di atas Rp100.000.000,-'
                    ? 'di atas Rp100.000.000,00 (Seratus Juta Rupiah)'
                    : 'sampai dengan Rp100.000.000,00 (Seratus Juta Rupiah)';

                $baseData = array_merge($baseData, [
                    'deputi_nama' => $pejabatData['deputi_nama'] ?? 'DEPUTI BIDANG ADMINISTRASI',
                    'kepada' => 'Yth. Sekretaris Jenderal DPR RI',
                    'dari' => $pejabatData['jabatan'] ?? 'Plt. Deputi Bidang Administrasi',
                    'alamat_penerima' => 'di Jl. Jenderal Gatot Subroto',
                    'kota_penerima' => 'Jakarta 10270',
                    'sifat' => 'Segera',
                    'lampiran' => '1 (satu) Berkas',
                    'hal' => 'Permohonan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Setjen DPR RI',
                    'jenis_bmn' => $additionalData['jenis_bmn'] ?? 'Peralatan dan Mesin',
                    'kategori_nilai' => $kategoriNilaiPenetapan
                ]);
                break;

            case 'surat_kpknl':
                $baseData = array_merge($baseData, [
                    'kepada' => 'Yth. Plt. Deputi Bidang Administrasi',
                    'dari' => $pejabatData['jabatan'] ?? 'Sekretaris Jenderal',
                    'alamat_penerima' => 'di Jl. Jenderal Gatot Subroto',
                    'kota_penerima' => 'Jakarta 10270',
                    'sifat' => 'Segera',
                    'lampiran' => '1 (satu) berkas',
                    'hal' => 'Persetujuan Penetapan Status Penggunaan Barang Milik Negara oleh Pengguna Barang pada Setjen DPR RI',
                    'jenis_bmn' => $additionalData['jenis_bmn'] ?? 'Peralatan dan Mesin',
                    'kategori_nilai' => $additionalData['kategori_nilai'] ?? 'sampai dengan Rp100.000.000,-',
                    'nomor_referensi' => $additionalData['nomor_referensi'] ?? 'B/8916/KN.02.04/07/2025',
                    'tanggal_referensi' => $additionalData['tanggal_referensi'] ?? '3 Juli 2025'
                ]);
                break;
        }

        return array_merge($baseData, $additionalData);
    }

    /**
     * Get template path based on document type
     */
    private function getTemplatePath($documentType)
    {
        $templates = [
            'nodin_eselon_iii' => 'PerencanaanBMN.Bagian.pdf.GeneratePspDoc.NodinEselonIII',
            'nodin_eselon_ii' => 'PerencanaanBMN.Bagian.pdf.GeneratePspDoc.NodinEselonII',
            'nodin_eselon_i' => 'PerencanaanBMN.Bagian.pdf.GeneratePspDoc.NodinEselonI',
            'nodin_penetapan' => 'PerencanaanBMN.Bagian.pdf.GeneratePspDoc.NodinPenetapan',
            'surat_kpknl' => 'PerencanaanBMN.Bagian.pdf.GeneratePspDoc.SuratKPKNL'
        ];

        return $templates[$documentType] ?? $templates['nodin_eselon_iii'];
    }

    /**
     * Generate filename based on document type
     */
    private function generateFilename($documentType, $nomorSurat)
    {
        $date = now()->format('Y-m-d');
        $nomorClean = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $nomorSurat);

        $typeNames = [
            'nodin_eselon_iii' => 'Nodin_Eselon_III',
            'nodin_eselon_ii' => 'Nodin_Eselon_II',
            'nodin_eselon_i' => 'Nodin_Eselon_I',
            'nodin_penetapan' => 'Nodin_Penetapan',
            'surat_kpknl' => 'Surat_KPKNL'
        ];

        $typeName = $typeNames[$documentType] ?? 'Dokumen_PSP';

        return "{$typeName}_{$nomorClean}_{$date}.pdf";
    }

    /**
     * Get Indonesian month name
     */
    private function getIndonesianMonth($monthNumber)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $months[$monthNumber] ?? 'Januari';
    }

    /**
     * Convert number to Indonesian words (basic implementation)
     */
    private function numberToWords($number)
    {
        $words = [
            2024 => 'dua ribu dua puluh empat',
            2025 => 'dua ribu dua puluh lima',
            2026 => 'dua ribu dua puluh enam',
            2027 => 'dua ribu dua puluh tujuh',
            2028 => 'dua ribu dua puluh delapan'
        ];

        return $words[$number] ?? 'dua ribu dua puluh lima';
    }

    /**
     * Get official data based on document type
     */
    private function getOfficialData($documentType)
    {
        try {
            switch ($documentType) {
                case 'nodin_eselon_iii':
                    $koordinatorData = DB::table('pegawai')
                        ->where('id_satker', 669)
                        ->where('eselon', 'III')
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($koordinatorData && !empty($koordinatorData->nama)) {
                        return [
                            'nama' => $koordinatorData->nama,
                            'nip' => $koordinatorData->nip ?? '',
                            'jabatan' => $koordinatorData->jabatan ?? 'Kepala Bagian Administrasi BMN',
                            'bagian_nama' => 'BAGIAN ADMINISTRASI BARANG MILIK NEGARA'
                        ];
                    }

                    $backupData = DB::table('pegawai')
                        ->where('id_satker', 669)
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($backupData && !empty($backupData->nama)) {
                        return [
                            'nama' => $backupData->nama,
                            'nip' => $backupData->nip ?? '',
                            'jabatan' => $backupData->jabatan ?? 'Kepala Bagian Administrasi BMN',
                            'bagian_nama' => 'BAGIAN ADMINISTRASI BARANG MILIK NEGARA'
                        ];
                    }
                    break;

                case 'nodin_eselon_ii':
                    $biroData = DB::table('pegawai')
                        ->where('id_satker', 664)
                        ->where('eselon', 'II')
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($biroData && !empty($biroData->nama)) {
                        return [
                            'nama' => $biroData->nama,
                            'nip' => $biroData->nip ?? '',
                            'jabatan' => $biroData->jabatan ?? 'Kepala Biro Keuangan',
                            'biro_nama' => 'BIRO KEUANGAN'
                        ];
                    }

                    $backupBiroData = DB::table('pegawai')
                        ->where('id_satker', 664)
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($backupBiroData && !empty($backupBiroData->nama)) {
                        return [
                            'nama' => $backupBiroData->nama,
                            'nip' => $backupBiroData->nip ?? '',
                            'jabatan' => $backupBiroData->jabatan ?? 'Kepala Biro Keuangan',
                            'biro_nama' => 'BIRO KEUANGAN'
                        ];
                    }
                    break;

                case 'nodin_eselon_i':
                case 'nodin_penetapan':
                    $deputiData = DB::table('pegawai')
                        ->where('id_satker', 628)
                        ->where('eselon', 'I')
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($deputiData && !empty($deputiData->nama)) {
                        return [
                            'nama' => $deputiData->nama,
                            'nip' => $deputiData->nip ?? '',
                            'jabatan' => $deputiData->jabatan ?? 'Plt. Deputi Bidang Administrasi',
                            'deputi_nama' => 'DEPUTI BIDANG ADMINISTRASI'
                        ];
                    }

                    $backupDeputiData = DB::table('pegawai')
                        ->where('id_satker', 628)
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($backupDeputiData && !empty($backupDeputiData->nama)) {
                        return [
                            'nama' => $backupDeputiData->nama,
                            'nip' => $backupDeputiData->nip ?? '',
                            'jabatan' => $backupDeputiData->jabatan ?? 'Plt. Deputi Bidang Administrasi',
                            'deputi_nama' => 'DEPUTI BIDANG ADMINISTRASI'
                        ];
                    }
                    break;

                case 'surat_kpknl':
                    $sekjenData = DB::table('pegawai')
                        ->where('id_satker', 500)
                        ->where('eselon', 'I')
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($sekjenData && !empty($sekjenData->nama)) {
                        return [
                            'nama' => $sekjenData->nama,
                            'nip' => $sekjenData->nip ?? '',
                            'jabatan' => $sekjenData->jabatan ?? 'Sekretaris Jenderal DPR RI'
                        ];
                    }

                    $backupSekjenData = DB::table('pegawai')
                        ->where('jabatan', 'LIKE', '%Sekretaris Jenderal%')
                        ->select('nama', 'nip', 'jabatan')
                        ->first();

                    if ($backupSekjenData && !empty($backupSekjenData->nama)) {
                        return [
                            'nama' => $backupSekjenData->nama,
                            'nip' => $backupSekjenData->nip ?? '',
                            'jabatan' => $backupSekjenData->jabatan ?? 'Sekretaris Jenderal DPR RI'
                        ];
                    }
                    break;
            }

        } catch (Exception $e) {
            Log::error('Error getting official data: ' . $e->getMessage());
        }

        // Fallback data if database query fails
        switch ($documentType) {
            case 'nodin_eselon_iii':
                return [
                    'nama' => 'Dedy Bagus Prakasa',
                    'nip' => '197001011990031001',
                    'jabatan' => 'Kepala Bagian Administrasi BMN',
                    'bagian_nama' => 'BAGIAN ADMINISTRASI BARANG MILIK NEGARA'
                ];

            case 'nodin_eselon_ii':
                return [
                    'nama' => 'Rahmad Budiaji',
                    'nip' => '197001011990031002',
                    'jabatan' => 'Kepala Biro Keuangan',
                    'biro_nama' => 'BIRO KEUANGAN'
                ];

            case 'nodin_eselon_i':
            case 'nodin_penetapan':
                return [
                    'nama' => 'Rudi Rochmansyah, S.H., M.H.',
                    'nip' => '197001011990031003',
                    'jabatan' => 'Plt. Deputi Bidang Administrasi',
                    'deputi_nama' => 'DEPUTI BIDANG ADMINISTRASI'
                ];

            case 'surat_kpknl':
                return [
                    'nama' => 'Indra Iskandar',
                    'nip' => '197001011990031004',
                    'jabatan' => 'Sekretaris Jenderal DPR RI'
                ];

            default:
                return [
                    'nama' => 'Nama Pejabat',
                    'nip' => 'NIP Pejabat',
                    'jabatan' => 'Jabatan Pejabat'
                ];
        }
    }

    /**
     * Preview SPTJM Lampiran Info - Shows how many PDFs will be generated
     */
    public function previewSptjmInfo(Request $request)
    {
        try {
            // Get filter parameters (same as PSP validation)
            $filterNilai = $request->get('filter_nilai', '<100');
            $tahunDari = $request->get('tahun_dari', 2010);
            $tahunSampai = $request->get('tahun_sampai', date('Y'));
            $nup = $request->get('nup');
            $kodeBarang = $request->get('kode_barang');

            // Build query to count total records
            $nupCondition = !empty($nup) ? 'AND nup = ?' : '';
            $kodeBarangCondition = !empty($kodeBarang) ? 'AND kode_barang LIKE ?' : '';

            $nilaiCondition = $filterNilai === '<100'
                ? 'nilai_perolehan_pertama < 100000000'
                : 'nilai_perolehan_pertama >= 100000000';

            $countQuery = "
                WITH all_assets AS (
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_angkutan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_berat
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_gedung_bangunan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_non_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_rumah_negara
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tak_berwujud
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tanah
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tetap_lainnya
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}
                ),
                grouped_assets AS (
                    SELECT *,
                        (nup - ROW_NUMBER() OVER (
                            PARTITION BY kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                            ORDER BY nup
                        )) AS group_id
                    FROM all_assets
                )
                SELECT COUNT(*) as total FROM (
                    SELECT kode_barang
                    FROM grouped_assets
                    GROUP BY group_id, kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                ) as final_count
            ";

            // Build parameters
            $params = [];
            for ($i = 0; $i < 9; $i++) {
                $params[] = $tahunDari;
                $params[] = $tahunSampai;
                if (!empty($nup)) {
                    $params[] = $nup;
                }
                if (!empty($kodeBarang)) {
                    $params[] = $kodeBarang . '%';
                }
            }

            $totalRecords = DB::select($countQuery, $params)[0]->total;
            $totalPdfs = ceil($totalRecords / 10000);
            $estimatedSizeMB = ($totalRecords * 0.005); // Rough estimate: 5KB per record

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_records' => $totalRecords,
                    'total_pdfs' => $totalPdfs,
                    'records_per_pdf' => 10000,
                    'estimated_size_mb' => round($estimatedSizeMB, 2),
                    'filters' => [
                        'filter_nilai' => $filterNilai,
                        'tahun_dari' => $tahunDari,
                        'tahun_sampai' => $tahunSampai,
                        'nup' => $nup,
                        'kode_barang' => $kodeBarang
                    ]
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error previewing SPTJM info: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to preview SPTJM info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }

    /**
     * Generate SPTJM Lampiran - Multiple PDFs in ZIP (Optimized)
     */
    public function generateSptjmLampiran(Request $request)
    {
        // === STAGE 0: EARLY VALIDATION ===
        try {
            $request->validate([
                'nomor_surat' => 'required|string|max:100',
                'tahun_dari' => 'required|integer|min:1900|max:2099',
                'tahun_sampai' => 'required|integer|min:1900|max:2099'
            ]);
        } catch (\Exception $e) {
            Log::error('SPTJM Validation Failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi input gagal: ' . $e->getMessage()
            ], 400);
        }

        // === STAGE 1: INITIALIZATION & LIMITS ===
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Remove memory limit restrictions - use server default
        set_time_limit(0); // Unlimited time
        // Let server use its configured memory_limit (no override)

        // Shutdown handler for fatal errors
        register_shutdown_function(function() use ($startTime) {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
                Log::error('SPTJM Generation Fatal Error', [
                    'error' => $error,
                    'error_type' => $error['type'],
                    'error_message' => $error['message'],
                    'error_file' => $error['file'],
                    'error_line' => $error['line'],
                    'execution_time' => round(microtime(true) - $startTime, 2) . 's',
                    'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
                ]);
            }
        });

        Log::info('SPTJM Generation Started', [
            'server_memory_limit' => ini_get('memory_limit'),
            'server_time_limit' => ini_get('max_execution_time'),
            'initial_memory' => $this->formatBytes($startMemory),
            'available_memory' => $this->formatBytes(memory_get_usage(false)),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'note' => 'Using server default limits (no override)'
        ]);

        try {
            // === STAGE 2: PARAMETER EXTRACTION ===
            $nomorSurat = $request->get('nomor_surat');
            $filterNilai = $request->get('filter_nilai', '<100');
            $tahunDari = $request->get('tahun_dari', 2010);
            $tahunSampai = $request->get('tahun_sampai', date('Y'));
            $nup = $request->get('nup');
            $kodeBarang = $request->get('kode_barang');

            Log::info('SPTJM Stage 2: Parameters validated', [
                'nomor_surat' => $nomorSurat,
                'filter_nilai' => $filterNilai,
                'tahun_dari' => $tahunDari,
                'tahun_sampai' => $tahunSampai,
                'nup' => $nup,
                'kode_barang' => $kodeBarang,
                'current_memory' => $this->formatBytes(memory_get_usage(true))
            ]);

            // Build query (same as PSP validation)
            $nupCondition = !empty($nup) ? 'AND nup = ?' : '';
            $kodeBarangCondition = !empty($kodeBarang) ? 'AND kode_barang LIKE ?' : '';

            $nilaiCondition = $filterNilai === '<100'
                ? 'nilai_perolehan_pertama < 100000000'
                : 'nilai_perolehan_pertama >= 100000000';

            $query = "
                WITH all_assets AS (
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_angkutan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_alat_berat
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_gedung_bangunan
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_non_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_pm_tik
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_rumah_negara
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tak_berwujud
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tanah
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}

                    UNION ALL
                    SELECT kode_barang, nup, nama_barang, merk,
                           DATE(tanggal_perolehan) as tanggal_perolehan,
                           nilai_perolehan_pertama, kondisi
                    FROM siman_aset_tetap_lainnya
                    WHERE {$nilaiCondition}
                      AND (no_psp IS NULL OR no_psp = '')
                      AND YEAR(tanggal_perolehan) BETWEEN ? AND ?
                      {$nupCondition} {$kodeBarangCondition}
                ),
                grouped_assets AS (
                    SELECT *,
                        (nup - ROW_NUMBER() OVER (
                            PARTITION BY kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                            ORDER BY nup
                        )) AS group_id
                    FROM all_assets
                )
                SELECT
                    kode_barang,
                    MIN(nup) AS nup_awal,
                    MAX(nup) AS nup_akhir,
                    nama_barang,
                    merk,
                    COUNT(*) AS kuantitas,
                    tanggal_perolehan,
                    nilai_perolehan_pertama,
                    (nilai_perolehan_pertama * COUNT(*)) AS jumlah_nilai,
                    kondisi AS keterangan
                FROM grouped_assets
                GROUP BY group_id, kode_barang, nama_barang, merk, tanggal_perolehan, nilai_perolehan_pertama, kondisi
                ORDER BY kode_barang, nup_awal
            ";

            // Build parameters
            $params = [];
            for ($i = 0; $i < 9; $i++) {
                $params[] = $tahunDari;
                $params[] = $tahunSampai;
                if (!empty($nup)) {
                    $params[] = $nup;
                }
                if (!empty($kodeBarang)) {
                    $params[] = $kodeBarang . '%';
                }
            }

            Log::info('SPTJM Stage 2.5: Query built successfully', [
                'param_count' => count($params),
                'has_nup_filter' => !empty($nup),
                'has_kode_barang_filter' => !empty($kodeBarang),
                'current_memory' => $this->formatBytes(memory_get_usage(true))
            ]);

            // === STAGE 3: LOAD DATA WITH PAGINATION (Memory Optimized) ===
            Log::info('SPTJM Stage 3: Starting paginated data loading', [
                'chunk_size' => 2000,
                'current_memory' => $this->formatBytes(memory_get_usage(true))
            ]);

            $allData = [];
            $loadChunkSize = 2000; // Load in smaller chunks
            $offset = 0;
            $iteration = 0;

            do {
                $chunkQuery = $query . " LIMIT ? OFFSET ?";

                try {
                    $chunkParams = $params;
                    $chunkParams[] = $loadChunkSize;
                    $chunkParams[] = $offset;

                    $chunk = DB::select($chunkQuery, $chunkParams);

                    if (empty($chunk)) break;

                    $allData = array_merge($allData, $chunk);
                    $offset += $loadChunkSize;
                    $iteration++;

                    // Log progress every 2 iterations (every 4000 records)
                    if ($iteration % 2 === 0) {
                        gc_collect_cycles();
                        Log::info("SPTJM Data Loading Progress", [
                            'iteration' => $iteration,
                            'records_loaded' => count($allData),
                            'memory_used' => $this->formatBytes(memory_get_usage(true)),
                            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true))
                        ]);
                    }

                    unset($chunk);
                } catch (\Exception $e) {
                    Log::error('SPTJM Stage 3: Database query failed', [
                        'error' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'iteration' => $iteration,
                        'offset' => $offset,
                        'records_so_far' => count($allData),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e; // Re-throw to be caught by outer catch
                }
            } while (true);

            $memoryAfterLoad = memory_get_usage(true);
            Log::info('SPTJM Stage 3 Complete: Data loading finished', [
                'total_records' => count($allData),
                'total_iterations' => $iteration,
                'memory_used' => $this->formatBytes($memoryAfterLoad),
                'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
                'load_time' => round(microtime(true) - $startTime, 2) . 's'
            ]);

            if (empty($allData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang sesuai dengan filter'
                ], 400);
            }

            // === STAGE 4: PREPARE PDF GENERATION ===
            $totalRecords = count($allData);
            $pdfChunkSize = 10000;
            $dataChunks = array_chunk($allData, $pdfChunkSize);
            $totalParts = count($dataChunks);

            // Free memory from allData since we now have chunks
            unset($allData);
            gc_collect_cycles();

            Log::info('SPTJM Stage 4: PDF preparation complete', [
                'total_pdfs' => $totalParts,
                'records_per_pdf' => $pdfChunkSize,
                'memory_after_chunking' => $this->formatBytes(memory_get_usage(true))
            ]);

            // Get pejabat data (Sekjen)
            $pejabatData = $this->getOfficialData('surat_kpknl');

            // Current date for document
            $currentDate = now();
            $tanggal = $currentDate->format('d');
            $bulan = $this->getIndonesianMonth($currentDate->format('n'));
            $tahun = $currentDate->format('Y');

            // Clean nomor surat for filename
            $nomorClean = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $nomorSurat);

            // === STAGE 5: SINGLE PDF CASE ===
            if ($totalParts === 1) {
                Log::info('SPTJM Stage 5: Generating single PDF', [
                    'total_records' => $totalRecords
                ]);

                // Pre-format all data to avoid processing in Blade (OPTIMIZATION)
                $chunk = $dataChunks[0];
                foreach ($chunk as $row) {
                    // Format currency values (save 10k+ number_format calls in view)
                    $row->formatted_nilai_satuan = 'Rp' . number_format($row->nilai_perolehan_pertama, 2, ',', '.');
                    $row->formatted_jumlah_nilai = 'Rp' . number_format($row->jumlah_nilai, 2, ',', '.');

                    // Format dates (save 10k+ date/strtotime calls in view)
                    $row->formatted_tanggal = date('d-m-Y', strtotime($row->tanggal_perolehan));

                    // Format quantities
                    $row->formatted_kuantitas = number_format($row->kuantitas);
                }

                // Pre-format totals
                $total_kuantitas = array_sum(array_column($chunk, 'kuantitas'));
                $total_nilai_pertama = array_sum(array_column($chunk, 'nilai_perolehan_pertama'));
                $total_jumlah_nilai = array_sum(array_column($chunk, 'jumlah_nilai'));

                $formatted_total_kuantitas = number_format($total_kuantitas);
                $formatted_total_nilai = 'Rp' . number_format($total_nilai_pertama, 2, ',', '.');
                $formatted_total_jumlah = 'Rp' . number_format($total_jumlah_nilai, 2, ',', '.');

                $documentData = [
                    'nomor_surat' => $nomorSurat,
                    'pejabat_data' => $pejabatData,
                    'tanggal' => $tanggal,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'data' => $chunk,
                    'start_number' => 1,
                    'end_number' => $totalRecords,
                    'total_kuantitas' => $total_kuantitas,
                    'total_nilai_pertama' => $total_nilai_pertama,
                    'total_jumlah_nilai' => $total_jumlah_nilai,
                    'formatted_total_kuantitas' => $formatted_total_kuantitas,
                    'formatted_total_nilai' => $formatted_total_nilai,
                    'formatted_total_jumlah' => $formatted_total_jumlah,
                    'part_number' => null,
                    'total_parts' => 1,
                    'part_suffix' => ''
                ];

                // Use wkhtmltopdf via Snappy (10-20x faster than DomPDF)
                $pdf = SnappyPdf::loadView('PerencanaanBMN.Bagian.pdf.GeneratePspDoc.SptjmLampiran', $documentData);
                $pdf->setPaper('A4')
                    ->setOrientation('portrait')
                    ->setOption('margin-top', 20)
                    ->setOption('margin-bottom', 20)
                    ->setOption('margin-left', 15)
                    ->setOption('margin-right', 15)
                    ->setOption('enable-local-file-access', true)
                    ->setOption('enable-javascript', false)
                    ->setOption('no-stop-slow-scripts', true)
                    ->setOption('dpi', 96);

                $filename = "SPTJM_Lampiran_{$nomorClean}_" . date('Y-m-d') . ".pdf";

                $executionTime = round(microtime(true) - $startTime, 2);
                Log::info('SPTJM Generation Complete (Single PDF)', [
                    'total_records' => $totalRecords,
                    'execution_time' => $executionTime . 's',
                    'peak_memory' => $this->formatBytes(memory_get_peak_usage(true))
                ]);

                return $pdf->download($filename);
            }

            // === STAGE 6: MULTIPLE PDFs CASE ===
            Log::info('SPTJM Stage 6: Generating multiple PDFs', [
                'total_parts' => $totalParts,
                'total_records' => $totalRecords
            ]);

            $zip = new \ZipArchive();
            $zipFilename = "SPTJM_Lampiran_{$nomorClean}_" . date('Y-m-d') . ".zip";
            $zipPath = storage_path('app/temp/' . $zipFilename);

            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new Exception('Could not create ZIP file');
            }

            // Generate each PDF and add to ZIP
            $startNumber = 1;
            foreach ($dataChunks as $index => $chunk) {
                $partNumber = $index + 1;
                $endNumber = $startNumber + count($chunk) - 1;

                $memoryBeforePdf = memory_get_usage(true);

                // Pre-format all data to avoid processing in Blade (OPTIMIZATION)
                foreach ($chunk as $row) {
                    // Format currency values (save 10k+ number_format calls in view)
                    $row->formatted_nilai_satuan = 'Rp' . number_format($row->nilai_perolehan_pertama, 2, ',', '.');
                    $row->formatted_jumlah_nilai = 'Rp' . number_format($row->jumlah_nilai, 2, ',', '.');

                    // Format dates (save 10k+ date/strtotime calls in view)
                    $row->formatted_tanggal = date('d-m-Y', strtotime($row->tanggal_perolehan));

                    // Format quantities
                    $row->formatted_kuantitas = number_format($row->kuantitas);
                }

                // Pre-format totals
                $total_kuantitas = array_sum(array_column($chunk, 'kuantitas'));
                $total_nilai_pertama = array_sum(array_column($chunk, 'nilai_perolehan_pertama'));
                $total_jumlah_nilai = array_sum(array_column($chunk, 'jumlah_nilai'));

                $formatted_total_kuantitas = number_format($total_kuantitas);
                $formatted_total_nilai = 'Rp' . number_format($total_nilai_pertama, 2, ',', '.');
                $formatted_total_jumlah = 'Rp' . number_format($total_jumlah_nilai, 2, ',', '.');

                $documentData = [
                    'nomor_surat' => $nomorSurat,
                    'pejabat_data' => $pejabatData,
                    'tanggal' => $tanggal,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'data' => $chunk,
                    'start_number' => $startNumber,
                    'end_number' => $endNumber,
                    'total_kuantitas' => $total_kuantitas,
                    'total_nilai_pertama' => $total_nilai_pertama,
                    'total_jumlah_nilai' => $total_jumlah_nilai,
                    'formatted_total_kuantitas' => $formatted_total_kuantitas,
                    'formatted_total_nilai' => $formatted_total_nilai,
                    'formatted_total_jumlah' => $formatted_total_jumlah,
                    'part_number' => $partNumber,
                    'total_parts' => $totalParts,
                    'part_suffix' => " (Bagian {$partNumber} dari {$totalParts})"
                ];

                // Use wkhtmltopdf via Snappy (10-20x faster than DomPDF)
                $pdf = SnappyPdf::loadView('PerencanaanBMN.Bagian.pdf.GeneratePspDoc.SptjmLampiran', $documentData);
                $pdf->setPaper('A4')
                    ->setOrientation('portrait')
                    ->setOption('margin-top', 20)
                    ->setOption('margin-bottom', 20)
                    ->setOption('margin-left', 15)
                    ->setOption('margin-right', 15)
                    ->setOption('enable-local-file-access', true)
                    ->setOption('enable-javascript', false)
                    ->setOption('no-stop-slow-scripts', true)
                    ->setOption('dpi', 96);

                $pdfFilename = "SPTJM_Lampiran_{$nomorClean}_Part{$partNumber}of{$totalParts}_" . date('Y-m-d') . ".pdf";
                $pdfContent = $pdf->output();

                $zip->addFromString($pdfFilename, $pdfContent);

                // === AGGRESSIVE MEMORY CLEANUP ===
                unset($pdf, $pdfContent, $documentData);
                gc_collect_cycles();

                $memoryAfterPdf = memory_get_usage(true);
                Log::info("SPTJM PDF Part {$partNumber}/{$totalParts} completed", [
                    'records_processed' => count($chunk),
                    'memory_before' => $this->formatBytes($memoryBeforePdf),
                    'memory_after' => $this->formatBytes($memoryAfterPdf),
                    'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
                    'elapsed_time' => round(microtime(true) - $startTime, 2) . 's'
                ]);

                $startNumber = $endNumber + 1;
            }

            $zip->close();

            $executionTime = round(microtime(true) - $startTime, 2);
            $peakMemory = memory_get_peak_usage(true);

            Log::info('SPTJM Generation Complete (Multiple PDFs)', [
                'total_records' => $totalRecords,
                'total_pdfs' => $totalParts,
                'zip_size' => $this->formatBytes(filesize($zipPath)),
                'execution_time' => $executionTime . 's',
                'peak_memory' => $this->formatBytes($peakMemory)
            ]);

            // Download ZIP and delete after sending
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors - return 400
            Log::warning('SPTJM Validation Error', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);

        } catch (\Illuminate\Database\QueryException $e) {
            // Database-specific errors
            Log::error('SPTJM Database Error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'execution_time' => round(microtime(true) - $startTime, 2) . 's',
                'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
                'hint' => 'Coba kurangi range tahun atau tambahkan filter spesifik'
            ], 500);

        } catch (\Exception $e) {
            // General exceptions
            Log::error('SPTJM Generation Error', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'execution_time' => isset($startTime) ? round(microtime(true) - $startTime, 2) . 's' : 'N/A',
                'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB',
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error generating SPTJM: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'hint' => 'Silakan cek log server untuk detail lengkap'
            ], 500);
        }
    }
}
