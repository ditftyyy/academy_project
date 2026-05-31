<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Endpoint publik untuk login
Route::post('/login', [AuthController::class, 'login']);

// Endpoint yang membutuhkan autentikasi (token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    // Tambahkan endpoint lain yang diperlukan Flutter di sini (misal: daftar course, materi, dll)
});