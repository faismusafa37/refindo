<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityImportController;

Route::redirect('/', '/admin');

// Filament Login Custom Route
Route::post('/admin/login', [AuthController::class, 'login']);

// Export Excel Route
Route::get('/export-excel', [ExportController::class, 'exportExcel'])->name('export-excel');




// Menampilkan form
Route::get('/activities/import', [ActivityImportController::class, 'showForm'])->name('activities.import.form');

// Memproses upload Excel
Route::post('/activities/import', [ActivityImportController::class, 'import'])->name('activities.import.process');


Route::get('/admin/activities/generate-template', [ActivityImportController::class, 'generateTemplate'])->name('activities.generate.template');


