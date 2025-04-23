<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

// Filament Login Custom Route
Route::post('/admin/login', [AuthController::class, 'login']);

// Export Excel Route
Route::get('/export-excel', [ExportController::class, 'exportExcel'])->name('export-excel');
