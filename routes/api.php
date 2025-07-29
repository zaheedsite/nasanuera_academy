<?php

use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\PdfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/subjects/{role}', [SubjectController::class, 'indexByRole']);
Route::get('/subjects/{id}/videos', [VideoController::class, 'getBySubject']);
Route::get('/videos', [VideoController::class, 'index']);

Route::get('/pdfs', [PdfController::class, 'index']);
Route::get('/pdfs/{id}', [PdfController::class, 'show']);


