<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MahasiswaController as ApiMahasiswaController;
use App\Http\Controllers\Api\DosenController as ApiDosenController;
use App\Http\Controllers\Api\FakultasController as ApiFakultasController;
use App\Http\Controllers\Api\ProdiController as ApiProdiController;
use App\Http\Controllers\Api\JadwalController as ApiJadwalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route API menggunakan prefix /api
| Contoh: http://localhost:8000/api/v1/mahasiswa
|--------------------------------------------------------------------------
*/

// ================= PUBLIC ROUTES =================
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

// ================= PROTECTED ROUTES =================
Route::middleware('auth:sanctum')->group(function () {

    // ===== AUTH =====
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    // ===== MASTER DATA API (Version 1) =====
    Route::prefix('v1')->name('api.v1.')->group(function () {
        
        // Mahasiswa API
        Route::apiResource('mahasiswa', ApiMahasiswaController::class)
            ->names([
                'index' => 'mahasiswa.index',
                'store' => 'mahasiswa.store',
                'show' => 'mahasiswa.show',
                'update' => 'mahasiswa.update',
                'destroy' => 'mahasiswa.destroy',
            ]);

        // Dosen API
        Route::apiResource('dosen', ApiDosenController::class)
            ->names([
                'index' => 'dosen.index',
                'store' => 'dosen.store',
                'show' => 'dosen.show',
                'update' => 'dosen.update',
                'destroy' => 'dosen.destroy',
            ]);

        // Fakultas API
        Route::apiResource('fakultas', ApiFakultasController::class)
            ->names([
                'index' => 'fakultas.index',
                'store' => 'fakultas.store',
                'show' => 'fakultas.show',
                'update' => 'fakultas.update',
                'destroy' => 'fakultas.destroy',
            ]);

        // Prodi API
        Route::apiResource('prodi', ApiProdiController::class)
            ->names([
                'index' => 'prodi.index',
                'store' => 'prodi.store',
                'show' => 'prodi.show',
                'update' => 'prodi.update',
                'destroy' => 'prodi.destroy',
            ]);

        // Jadwal API
        Route::apiResource('jadwal', ApiJadwalController::class)
            ->names([
                'index' => 'jadwal.index',
                'store' => 'jadwal.store',
                'show' => 'jadwal.show',
                'update' => 'jadwal.update',
                'destroy' => 'jadwal.destroy',
            ]);

        // Jadwal - Additional endpoints
        Route::post('/jadwal/{jadwal}/pindah', [ApiJadwalController::class, 'pindahJadwal'])
            ->name('jadwal.pindah');
        Route::post('/jadwal/ruangan-kosong', [ApiJadwalController::class, 'cekRuanganKosong'])
            ->name('jadwal.ruangan-kosong');
        Route::post('/jadwal/check-bentrok', [ApiJadwalController::class, 'checkBentrok'])
            ->name('jadwal.check-bentrok');
    });
});