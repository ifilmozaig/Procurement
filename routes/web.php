<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RealisasiDownloadController;
use App\Http\Controllers\RealisasiTerealisasiDownloadController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/admin/login');
})->name('admin.logout');

Route::get('/admin/realisasi/download', [RealisasiDownloadController::class, 'download'])
    ->middleware(['web', 'auth'])
    ->name('realisasi.download');

Route::get('/realisasi/download/terealisasi', [RealisasiTerealisasiDownloadController::class, 'download'])
    ->middleware(['web', 'auth'])
    ->name('realisasi.download.terealisasi');