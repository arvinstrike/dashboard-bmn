<?php

namespace App\Http\Controllers\Rkbmn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BmnPengajuanrkbmnbagian;
use App\Models\Bagian;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * RKBMN Statistical Dashboard Controller
 *
 * Handles statistical analysis and data visualization for RKBMN
 * Includes export functionality to Excel and PDF
 */
class StatisticalController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari request
        $filters = [
            'jenis_pengajuan' => $request->get('jenis_pengajuan'),
            'bagian' => $request->get('bagian'),
            'status' => $request->get('status'),
            'tahun_anggaran' => $request->get('tahun_anggaran'),
            'atr_non_atr' => $request->get('atr_non_atr'),
            'skema' => $request->get('skema'),
        ];

        // Query dasar dengan filter
        $query = BmnPengajuanrkbmnbagian::query();

        if ($filters['jenis_pengajuan']) {
            $query->where('kode_jenis_pengajuan', 'LIKE', $filters['jenis_pengajuan'] . '%');
        }

        if ($filters['bagian']) {
            $query->where('id_bagian_pengusul', $filters['bagian']);
        }

        if ($filters['status']) {
            $query->where('bmn_pengajuanrkbmnbagian.status', $filters['status']);
        }

        if ($filters['tahun_anggaran']) {
            $query->where('tahun_anggaran', $filters['tahun_anggaran']);
        }

        if ($filters['atr_non_atr']) {
            $query->where('atr_nonatr', $filters['atr_non_atr']);
        }

        if ($filters['skema']) {
            $query->where('skema', $filters['skema']);
        }

        // Ambil data untuk berbagai statistik berdasarkan filter
        $stats = $this->getStatisticalData($query);

        // Ambil data untuk filter options
        $jenisPengajuanOptions = $this->getJenisPengajuanOptions($query);
        $bagianOptions = Bagian::where('status', 'on')
            ->orderBy('uraianbagian')
            ->get();
        $statusOptions = BmnPengajuanrkbmnbagian::select('status')->distinct()->get();
        $tahunAnggaranOptions = BmnPengajuanrkbmnbagian::select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'asc')->get();
        $atrOptions = BmnPengajuanrkbmnbagian::select('atr_nonatr')->whereNotNull('atr_nonatr')->distinct()->get();
        $skemaOptions = BmnPengajuanrkbmnbagian::select('skema')->whereNotNull('skema')->distinct()->get();

        $statusStatsByYear = $stats['statusStatsByYear'];
        unset($stats['statusStatsByYear']);

        $bagianStatsByYear = $stats['bagianStatsByYear'];
        unset($stats['bagianStatsByYear']);

        $anggaranBagianByYear = $stats['anggaranBagianByYear'];
        unset($stats['anggaranBagianByYear']);

        return view('rkbmn.statistical', compact(
            'stats',
            'jenisPengajuanOptions',
            'bagianOptions',
            'statusOptions',
            'tahunAnggaranOptions',
            'atrOptions',
            'skemaOptions',
            'filters',
            'statusStatsByYear',
            'bagianStatsByYear',
            'anggaranBagianByYear'
        ));
    }

    private function getStatisticalData($query = null)
    {
        $baseQuery = $query ?: BmnPengajuanrkbmnbagian::query();

        $totalPengajuan = $baseQuery->clone()->count();
        $menungguPersetujuan = $baseQuery->clone()->whereNotIn('status', ['approved', 'rejected', 'completed'])->count();
        $pengajuanDisetujui = $baseQuery->clone()->whereIn('status', ['approved', 'completed'])->count();
        $pengajuanDitolak = $baseQuery->clone()->where('status', 'rejected')->count();
        $anggaranDisetujui = $baseQuery->clone()->whereIn('status', ['approved', 'completed'])->sum('total_anggaran');

        // Statistik berdasarkan status
        $statusQuery = $baseQuery->clone();
        $statusStats = $statusQuery->select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Statistik status per tahun
        $statusByYearQuery = $baseQuery->clone();
        $statusStatsByYear = $statusByYearQuery
            ->select('tahun_anggaran', 'status', \DB::raw('COUNT(*) as count'))
            ->groupBy('tahun_anggaran', 'status')
            ->get()
            ->groupBy('tahun_anggaran')
            ->map(function ($item) {
                return $item->pluck('count', 'status');
            })
            ->toArray();

        // Statistik bagian per tahun
        $bagianByYearQuery = $baseQuery->clone();
        $bagianStatsByYear = $bagianByYearQuery
            ->join('bagian', 'bmn_pengajuanrkbmnbagian.id_bagian_pengusul', '=', 'bagian.id')
            ->select('tahun_anggaran', 'bagian.uraianbagian as nama_bagian', \DB::raw('COUNT(*) as count'))
            ->groupBy('tahun_anggaran', 'bagian.id', 'bagian.uraianbagian')
            ->get()
            ->groupBy('tahun_anggaran')
            ->map(function ($item) {
                return $item->pluck('count', 'nama_bagian');
            })
            ->toArray();

        // Statistik berdasarkan tahun anggaran
        $tahunQuery = $baseQuery->clone();
        $tahunStats = $tahunQuery->select(
            'tahun_anggaran',
            \DB::raw('COUNT(*) as count'),
            \DB::raw('SUM(total_anggaran) as total_anggaran')
        )
            ->groupBy('tahun_anggaran')
            ->orderBy('tahun_anggaran', 'asc')
            ->get();

        // Statistik berdasarkan bagian pengusul
        $bagianQuery = $baseQuery->clone();
        $bagianStats = $bagianQuery->join('bagian', 'bmn_pengajuanrkbmnbagian.id_bagian_pengusul', '=', 'bagian.id')
            ->select(
                'bagian.uraianbagian as nama_bagian',
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_anggaran) as total_anggaran')
            )
            ->groupBy('bagian.id', 'bagian.uraianbagian')
            ->orderBy('count', 'desc')
            ->get();

        // Statistik berdasarkan jenis pengajuan
        $jenisQuery = $baseQuery->clone();
        $jenisStats = $jenisQuery->select(
                \DB::raw('SUBSTRING(kode_jenis_pengajuan, 1, 2) as jenis_pengajuan'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_anggaran) as total_anggaran')
            )
            ->groupBy(\DB::raw('SUBSTRING(kode_jenis_pengajuan, 1, 2)'))
            ->get();

        // Statistik ATR vs Non-ATR
        $atrQuery = $baseQuery->clone();
        $atrStats = $atrQuery->select(
                'atr_nonatr',
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_anggaran) as total_anggaran')
            )
            ->whereNotNull('atr_nonatr')
            ->groupBy('atr_nonatr')
            ->get();

        // Statistik Anggaran per Bagian per Tahun
        $anggaranBagianByYearQuery = $baseQuery->clone();
        $anggaranBagianByYear = $anggaranBagianByYearQuery
            ->join('bagian', 'bmn_pengajuanrkbmnbagian.id_bagian_pengusul', '=', 'bagian.id')
            ->select('tahun_anggaran', 'bagian.uraianbagian as nama_bagian', \DB::raw('SUM(total_anggaran) as total_anggaran'))
            ->groupBy('tahun_anggaran', 'bagian.id', 'bagian.uraianbagian')
            ->get()
            ->groupBy('tahun_anggaran')
            ->map(function ($item) {
                return $item->pluck('total_anggaran', 'nama_bagian');
            })
            ->toArray();

        // Statistik berdasarkan skema
        $skemaQuery = $baseQuery->clone();
        $skemaStats = $skemaQuery->select(
                'skema',
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_anggaran) as total_anggaran')
            )
            ->whereNotNull('skema')
            ->groupBy('skema')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'total_pengajuan' => $totalPengajuan,
            'menunggu_persetujuan' => $menungguPersetujuan,
            'approved' => $pengajuanDisetujui,
            'rejected' => $pengajuanDitolak,
            'anggaran_disetujui' => $anggaranDisetujui,
            'status_stats' => $statusStats,
            'statusStatsByYear' => $statusStatsByYear,
            'bagianStatsByYear' => $bagianStatsByYear,
            'tahun_stats' => $tahunStats,
            'bagian_stats' => $bagianStats,
            'jenis_stats' => $jenisStats,
            'atr_stats' => $atrStats,
            'anggaranBagianByYear' => $anggaranBagianByYear,
            'skema_stats' => $skemaStats
        ];
    }

    private function getJenisPengajuanOptions($query = null)
    {
        return [
            'R1' => 'Tanah dan/atau bangunan perkantoran',
            'R3' => 'Tanah dan/atau gedung rumah negara',
            'R4' => 'Kendaraan Jabatan',
            'R5' => 'Kendaraan Operasional',
            'R6' => 'Kendaraan Fungsional'
        ];
    }

    public function exportExcel(Request $request)
    {
        // Apply filters
        $query = BmnPengajuanrkbmnbagian::query();

        if ($request->get('jenis_pengajuan')) {
            $query->where('kode_jenis_pengajuan', 'LIKE', $request->get('jenis_pengajuan') . '%');
        }
        if ($request->get('bagian')) {
            $query->where('id_bagian_pengusul', $request->get('bagian'));
        }
        if ($request->get('status')) {
            $query->where('bmn_pengajuanrkbmnbagian.status', $request->get('status'));
        }
        if ($request->get('tahun_anggaran')) {
            $query->where('tahun_anggaran', $request->get('tahun_anggaran'));
        }
        if ($request->get('atr_non_atr')) {
            $query->where('atr_nonatr', $request->get('atr_non_atr'));
        }
        if ($request->get('skema')) {
            $query->where('skema', $request->get('skema'));
        }

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Kode Jenis Pengajuan');
        $sheet->setCellValue('C1', 'ID Bagian Pengusul');
        $sheet->setCellValue('D1', 'ID Biro Pengusul');
        $sheet->setCellValue('E1', 'ID Bagian Pelaksana');
        $sheet->setCellValue('F1', 'ID Biro Pelaksana');
        $sheet->setCellValue('G1', 'Program');
        $sheet->setCellValue('H1', 'Kegiatan');
        $sheet->setCellValue('I1', 'Output');
        $sheet->setCellValue('J1', 'Kode Barang');
        $sheet->setCellValue('K1', 'Status');
        $sheet->setCellValue('L1', 'Tahun Anggaran');
        $sheet->setCellValue('M1', 'Tanggal Pengajuan');
        $sheet->setCellValue('N1', 'Tanggal Kebmn');
        $sheet->setCellValue('O1', 'Tanggal Keperencanaan');
        $sheet->setCellValue('P1', 'Tanggal Final');
        $sheet->setCellValue('Q1', 'Tujuan Rencana');
        $sheet->setCellValue('R1', 'ATR/Non-ATR');
        $sheet->setCellValue('S1', 'Skema');
        $sheet->setCellValue('T1', 'Harga Barang');
        $sheet->setCellValue('U1', 'Total Anggaran');
        $sheet->setCellValue('V1', 'Uraian Barang');
        $sheet->setCellValue('W1', 'Kuantitas');

        // Set data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->kode_jenis_pengajuan);
            $sheet->setCellValue('C' . $row, $item->id_bagian_pengusul);
            $sheet->setCellValue('D' . $row, $item->id_biro_pengusul);
            $sheet->setCellValue('E' . $row, $item->id_bagian_pelaksana);
            $sheet->setCellValue('F' . $row, $item->id_biro_pelaksana);
            $sheet->setCellValue('G' . $row, $item->program);
            $sheet->setCellValue('H' . $row, $item->kegiatan);
            $sheet->setCellValue('I' . $row, $item->output);
            $sheet->setCellValue('J' . $row, $item->kode_barang);
            $sheet->setCellValue('K' . $row, $item->status);
            $sheet->setCellValue('L' . $row, $item->tahun_anggaran);
            $sheet->setCellValue('M' . $row, $item->tanggal_pengajuan);
            $sheet->setCellValue('N' . $row, $item->tanggal_kebmn);
            $sheet->setCellValue('O' . $row, $item->tanggal_keperencanaan);
            $sheet->setCellValue('P' . $row, $item->tanggal_final);
            $sheet->setCellValue('Q' . $row, $item->tujuan_rencana);
            $sheet->setCellValue('R' . $row, $item->atr_nonatr);
            $sheet->setCellValue('S' . $row, $item->skema);
            $sheet->setCellValue('T' . $row, $item->harga_barang);
            $sheet->setCellValue('U' . $row, $item->total_anggaran);
            $sheet->setCellValue('V' . $row, $item->uraian_barang);
            $sheet->setCellValue('W' . $row, $item->kuantitas);
            $row++;
        }

        // Styling for header
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6FA']
            ]
        ];

        $sheet->getStyle('A1:W1')->applyFromArray($headerStyle);

        // Auto size columns
        foreach (range('A', 'W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'statistical_dashboard_pengajuan_bmn_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportPdf(Request $request)
    {
        // Apply filters
        $query = BmnPengajuanrkbmnbagian::query();

        if ($request->get('jenis_pengajuan')) {
            $query->where('kode_jenis_pengajuan', 'LIKE', $request->get('jenis_pengajuan') . '%');
        }
        if ($request->get('bagian')) {
            $query->where('id_bagian_pengusul', $request->get('bagian'));
        }
        if ($request->get('status')) {
            $query->where('bmn_pengajuanrkbmnbagian.status', $request->get('status'));
        }
        if ($request->get('tahun_anggaran')) {
            $query->where('tahun_anggaran', $request->get('tahun_anggaran'));
        }
        if ($request->get('atr_non_atr')) {
            $query->where('atr_nonatr', $request->get('atr_non_atr'));
        }
        if ($request->get('skema')) {
            $query->where('skema', $request->get('skema'));
        }

        $data = $query->get();

        // Generate PDF
        $pdf = Pdf::loadView('rkbmn.partials.pdf_export', ['data' => $data]);
        return $pdf->download('statistical_dashboard_pengajuan_bmn_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
