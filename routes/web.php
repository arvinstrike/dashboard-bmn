<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BmnDashboardController;
use App\Http\Controllers\BmnStatisticalDashboardController;


Route::get('/', [BmnDashboardController::class, 'index'])->name('bmn.dashboard');
Route::get('/statistical-dashboard', [BmnStatisticalDashboardController::class, 'index'])->name('bmn.statistical_dashboard');
Route::get('/statistical-dashboard/export-excel', [BmnStatisticalDashboardController::class, 'exportExcel'])->name('bmn.statistical_dashboard.export.excel');
Route::get('/statistical-dashboard/export-pdf', [BmnStatisticalDashboardController::class, 'exportPdf'])->name('bmn.statistical_dashboard.export.pdf');
