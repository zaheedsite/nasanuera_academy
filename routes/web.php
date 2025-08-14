<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Blade\SubjectCrudController;
use App\Http\Controllers\Blade\VideoCrudController;
use App\Http\Controllers\Blade\DashboardController;
use App\Http\Controllers\Blade\PdfCrudController;

// Redirect root berdasarkan login
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Semua route berikut hanya bisa diakses jika sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectCrudController::class, 'index'])->name('index');
        Route::get('/role/{role}', [SubjectCrudController::class, 'indexByRole'])->name('byRole');
        Route::get('/create', [SubjectCrudController::class, 'create'])->name('create');
        Route::post('/', [SubjectCrudController::class, 'store'])->name('store');
        Route::get('/{subject}/edit', [SubjectCrudController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [SubjectCrudController::class, 'update'])->name('update');
        Route::delete('/{subject}', [SubjectCrudController::class, 'destroy'])->name('destroy');
        Route::get('/{subject}', [SubjectCrudController::class, 'show'])->name('show');
    });

    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [VideoCrudController::class, 'index'])->name('index');
        Route::get('/create', [VideoCrudController::class, 'create'])->name('create');
        Route::post('/', [VideoCrudController::class, 'store'])->name('store');
        Route::get('/{video}/edit', [VideoCrudController::class, 'edit'])->name('edit');
        Route::put('/{video}', [VideoCrudController::class, 'update'])->name('update');
        Route::delete('/{video}', [VideoCrudController::class, 'destroy'])->name('destroy');
        Route::get('/{video}', [VideoCrudController::class, 'show'])->name('show');
        Route::post('/signed-url', [VideoCrudController::class, 'getSignedUrl'])
            ->name('signed-url');
        Route::post('/verify-upload', [VideoCrudController::class, 'verifyUpload'])
            ->name('verify-upload');
        Route::get('/debug-s3', [VideoCrudController::class, 'debugS3Config'])
            ->name('debug-s3');
        Route::post('/make-public', [VideoCrudController::class, 'makeVideosPublic'])
            ->name('make-public');
        Route::post('/make-file-public', [VideoCrudController::class, 'makeFilePublic'])
            ->name('make-file-public');
    });

    Route::prefix('pdfs')->name('pdfs.')->group(function () {
        Route::get('/', [PdfCrudController::class, 'index'])->name('index');
        Route::get('/create', [PdfCrudController::class, 'create'])->name('create');
        Route::post('/', [PdfCrudController::class, 'store'])->name('store');
        Route::get('/{pdf}/edit', [PdfCrudController::class, 'edit'])->name('edit');
        Route::put('/{pdf}', [PdfCrudController::class, 'update'])->name('update');
        Route::delete('/{pdf}', [PdfCrudController::class, 'destroy'])->name('destroy');
        Route::get('/{pdf}', [PdfCrudController::class, 'show'])->name('show');
    });
});

// Route login, register, dll dari Laravel Breeze / Jetstream
require __DIR__ . '/auth.php';
