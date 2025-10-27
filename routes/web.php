<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BmnDashboardController;


Route::get('/', [BmnDashboardController::class, 'index'])->name('bmn.dashboard');
