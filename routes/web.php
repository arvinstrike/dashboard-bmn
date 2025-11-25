<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BmnDashboardController;
use App\Http\Controllers\BmnStatisticalDashboardController;
use App\Http\Controllers\BmnUtilizationController;
use App\Http\Controllers\BmnDocumentController;


Route::get('/', [BmnDashboardController::class, 'index'])->name('bmn.dashboard');
Route::get('/statistical-dashboard', [BmnStatisticalDashboardController::class, 'index'])->name('bmn.statistical_dashboard');
Route::get('/statistical-dashboard/export-excel', [BmnStatisticalDashboardController::class, 'exportExcel'])->name('bmn.statistical_dashboard.export.excel');
Route::get('/statistical-dashboard/export-pdf', [BmnStatisticalDashboardController::class, 'exportPdf'])->name('bmn.statistical_dashboard.export.pdf');

// BMN Utilization Routes
Route::get('/utilization-dashboard', [BmnUtilizationController::class, 'index'])->name('bmn.utilization_dashboard');
Route::post('/utilization-dashboard', [BmnUtilizationController::class, 'store'])->name('bmn.utilization.store');
Route::post('/utilization-dashboard/{id}/toggle-complete', [BmnUtilizationController::class, 'toggleComplete'])->name('bmn.utilization.toggle_complete');
Route::get('/utilization-dashboard/{id}', [BmnUtilizationController::class, 'show'])->name('bmn.utilization.show');
Route::put('/utilization-dashboard/{id}', [BmnUtilizationController::class, 'update'])->name('bmn.utilization.update');
Route::delete('/utilization-dashboard/{id}', [BmnUtilizationController::class, 'destroy'])->name('bmn.utilization.delete');

// Upload documents (Lengkapi Data - files only)
Route::post('/utilization-dashboard/{id}/upload-documents', [BmnUtilizationController::class, 'uploadDocuments'])->name('bmn.utilization.upload_documents');

// Save individual document data (per-document modal forms)
Route::post('/utilization-dashboard/{id}/document/{type}/save', [BmnUtilizationController::class, 'saveDocumentData'])->name('bmn.utilization.document.save');

// Utilization Documents Routes
Route::get('/utilization-dashboard/{id}/documents', [BmnDocumentController::class, 'index'])->name('bmn.utilization.documents');
Route::post('/utilization-dashboard/{id}/documents/generate/{type}', [BmnDocumentController::class, 'generate'])->name('bmn.utilization.generate');
Route::post('/utilization-dashboard/{id}/documents/generate-all', [BmnDocumentController::class, 'generateAll'])->name('bmn.utilization.generateAll');

// Utilization Review and Confirmation Routes
Route::get('/utilization-dashboard/review', [BmnUtilizationController::class, 'review'])->name('bmn.utilization.review');
Route::get('/utilization-dashboard/review/{id}', [BmnUtilizationController::class, 'review'])->name('bmn.utilization.review.detail');
Route::get('/utilization-dashboard/confirmation', [BmnUtilizationController::class, 'confirmation'])->name('bmn.utilization.confirmation');
Route::get('/utilization-dashboard/proposals', [BmnUtilizationController::class, 'proposals'])->name('bmn.utilization.proposals');

// Terbilang Route
Route::get('/utilization-dashboard/terbilang', [BmnUtilizationController::class, 'getTerbilang'])->name('bmn.utilization.terbilang');

