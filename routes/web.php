<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityImportController;
use App\Http\Controllers\UserController;
// use Auth;

Route::redirect('/', '/admin');

// Filament Login Custom Route
Route::post('/admin/login', [AuthController::class, 'login']);

// Export Excel Route
Route::get('/export-excel', [ExportController::class, 'exportExcel'])->name('export-excel');

Route::post('/users', [UserController::class, 'store'])->name('users.store');


// Menampilkan form
Route::get('/activities/import', [ActivityImportController::class, 'showForm'])->name('activities.import.form');

// Memproses upload Excel
Route::post('/activities/import', [ActivityImportController::class, 'import'])->name('activities.import.process');


Route::get('/admin/activities/generate-template', [ActivityImportController::class, 'generateTemplate'])->name('activities.generate.template');

Route::get('/test', function () {
    if (Auth::check()) {
        return Auth::user()->hasRole('DLH');
    }
    return 'gak login';
});
