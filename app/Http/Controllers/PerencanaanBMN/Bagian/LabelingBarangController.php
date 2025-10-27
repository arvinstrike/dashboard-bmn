<?php

namespace App\Http\Controllers\PerencanaanBMN\Bagian;

use App\Http\Controllers\Controller;
use App\Models\PerencanaanBMN\Bagian\LabelingModel;
use App\Models\BmnInventarisasiKkSakti;
use App\Services\LabelingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class LabelingBarangController extends Controller
{
    protected $labelingService;

    public function __construct(LabelingService $labelingService)
    {
        $this->labelingService = $labelingService;
    }

    /**
     * Display dashboard labeling
     */
    public function index()
    {
        try {
            $stats = $this->labelingService->getStatistics();

            return view('PerencanaanBMN.Bagian.DashboardLabeling', [
                'stats' => $stats['success'] ? $stats['stats'] : [],
                'statsByLokasi' => $stats['success'] ? $stats['stats_by_lokasi'] : []
            ]);

        } catch (Exception $e) {
            Log::error('Dashboard Labeling index error: ' . $e->getMessage());
            return view('PerencanaanBMN.Bagian.DashboardLabeling')
                ->with('error', 'Failed to load dashboard data');
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->labelingService->getStatistics();

            return response()->json([
                'status' => 'success',
                'data' => $result['stats'],
                'stats_by_lokasi' => $result['stats_by_lokasi']
            ]);

        } catch (Exception $e) {
            Log::error('Get stats error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get labeling data with filters and pagination
     */
    public function getData(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = LabelingModel::query()
                ->join('bmn_inventarisasi_kk_sakti as kk', 'bmn_labeling.kk_sakti_id', '=', 'kk.id')
                ->select('bmn_labeling.*');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('bmn_labeling.nup', 'like', "%{$search}%")
                      ->orWhere('bmn_labeling.uraian_barang', 'like', "%{$search}%")
                      ->orWhere('bmn_labeling.merek', 'like', "%{$search}%")
                      ->orWhere('bmn_labeling.kode_barang', 'like', "%{$search}%");
                });
            }

            if ($request->filled('kode_barang')) {
                $query->where('bmn_labeling.kode_barang', $request->kode_barang);
            }

            if ($request->filled('area')) {
                $query->where('bmn_labeling.area', $request->area);
            }

            if ($request->filled('gedung')) {
                $query->where('bmn_labeling.gedung', $request->gedung);
            }

            if ($request->filled('ruangan')) {
                $query->where('bmn_labeling.ruangan', $request->ruangan);
            }

            if ($request->filled('status_cetak')) {
                $query->where('bmn_labeling.status_cetak', $request->status_cetak);
            }

            if ($request->filled('status_label')) {
                $query->where('bmn_labeling.status_label', $request->status_label);
            }

            if ($request->filled('bulan_dok')) {
                $query->whereRaw('MONTH(kk.tanggal) = ?', [$request->bulan_dok]);
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'id');
            $sortDir = $request->input('sort_dir', 'desc');
            $query->orderBy('bmn_labeling.' . $sortBy, $sortDir);

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
            Log::error('Get labeling data error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate labeling data from KK SAKTI
     */
    public function generateFromKKSakti(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'batch_id' => 'nullable|integer',
            'only_valid_fixed' => 'nullable|boolean'
        ]);

        try {
            $batchId = $request->input('batch_id');
            $onlyValidFixed = $request->input('only_valid_fixed', true);

            $result = $this->labelingService->generateLabelingFromKKSakti($batchId, $onlyValidFixed);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Generated {$result['processed']} labeling records",
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Generation failed',
                    'data' => $result
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Generate labeling error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark records as cetak
     */
    public function markAsCetak(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:bmn_labeling,id'
        ]);

        try {
            $result = $this->labelingService->markAsCetak($request->ids);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => "{$result['updated']} records marked as cetak",
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Mark as cetak error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark records as label
     */
    public function markAsLabel(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:bmn_labeling,id'
        ]);

        try {
            $result = $this->labelingService->markAsLabel($request->ids);

            if ($result['success']) {
                $message = "{$result['updated']} records marked as label";
                if ($result['skipped'] > 0) {
                    $message .= " ({$result['skipped']} skipped - belum cetak)";
                }

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Mark as label error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Bartender format
     */
    public function exportBartender(Request $request)
    {
        try {
            $filters = [];

            if ($request->filled('status_cetak')) {
                $filters['status_cetak'] = $request->status_cetak;
            }

            if ($request->filled('status_label')) {
                $filters['status_label'] = $request->status_label;
            }

            if ($request->filled('area')) {
                $filters['area'] = $request->area;
            }

            if ($request->filled('gedung')) {
                $filters['gedung'] = $request->gedung;
            }

            if ($request->filled('ruangan')) {
                $filters['ruangan'] = $request->ruangan;
            }

            if ($request->filled('search')) {
                $filters['search'] = $request->search;
            }

            if ($request->filled('bulan_dok')) {
                $filters['bulan_dok'] = $request->bulan_dok;
            }

            $result = $this->labelingService->exportToBartender($filters);

            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 500);
            }

            if ($result['total_records'] === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No data to export'
                ], 404);
            }

            // Create Excel export
            $export = new class($result['data']) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;

                public function __construct($data) {
                    $this->data = $data;
                }

                public function array(): array {
                    return $this->data;
                }
            };

            $fileName = 'Bartender_Export_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download($export, $fileName);

        } catch (Exception $e) {
            Log::error('Export Bartender error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete single record
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $record = LabelingModel::findOrFail($id);
            $record->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Delete labeling error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        try {
            $result = $this->labelingService->bulkDelete($request->ids);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => "{$result['deleted']} records deleted",
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available batch options from KK SAKTI
     */
    public function getBatchOptions(): \Illuminate\Http\JsonResponse
    {
        try {
            $batches = BmnInventarisasiKkSakti::select('upload_batch')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN error_status IN ("valid", "fixed") THEN 1 ELSE 0 END) as valid_count')
                ->groupBy('upload_batch')
                ->orderBy('upload_batch', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $batches
            ]);

        } catch (Exception $e) {
            Log::error('Get batch options error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique area/gedung/ruangan for filter options
     */
    public function getLokasiOptions(): \Illuminate\Http\JsonResponse
    {
        try {
            $areas = LabelingModel::select('area')
                ->distinct()
                ->whereNotNull('area')
                ->orderBy('area')
                ->pluck('area');

            $gedungs = LabelingModel::select('gedung')
                ->distinct()
                ->whereNotNull('gedung')
                ->orderBy('gedung')
                ->pluck('gedung');

            $ruangans = LabelingModel::select('ruangan')
                ->distinct()
                ->whereNotNull('ruangan')
                ->orderBy('ruangan')
                ->pluck('ruangan');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'areas' => $areas,
                    'gedungs' => $gedungs,
                    'ruangans' => $ruangans
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Get lokasi options error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available bulan_dok options (month 1-12 from tanggal)
     */
    public function getBulanDokOptions(): \Illuminate\Http\JsonResponse
    {
        try {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $bulanOptions = DB::table('bmn_labeling')
                ->join('bmn_inventarisasi_kk_sakti as kk', 'bmn_labeling.kk_sakti_id', '=', 'kk.id')
                ->selectRaw('MONTH(kk.tanggal) as bulan_dok')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('kk.tanggal')
                ->groupBy('bulan_dok')
                ->orderBy('bulan_dok', 'asc')
                ->get()
                ->map(function($item) use ($monthNames) {
                    $monthName = $monthNames[$item->bulan_dok] ?? 'Unknown';
                    return [
                        'value' => $item->bulan_dok,
                        'label' => $monthName . ' (' . $item->count . ' records)',
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $bulanOptions
            ]);

        } catch (Exception $e) {
            Log::error('Get bulan dok options error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
