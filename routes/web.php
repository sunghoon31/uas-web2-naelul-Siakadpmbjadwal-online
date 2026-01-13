<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\CalonMahasiswaController;
use App\Http\Controllers\KeuanganPMBController;
use App\Http\Controllers\DokumenCalonMahasiswaController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime', [App\Http\Controllers\DashboardController::class, 'getRealtimeStats'])->name('dashboard.realtime');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::patch('/', [ProfileController::class, 'update']); // Keep existing PATCH route
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA - Terpisah berdasarkan Role
    |--------------------------------------------------------------------------
    */
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa)
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            Route::get('export/excel', [MahasiswaController::class, 'export'])->name('export');
            Route::get('/', [MahasiswaController::class, 'index'])->name('index');
            Route::get('{mahasiswa}', [MahasiswaController::class, 'show'])->name('show');
            Route::get('{mahasiswa}/pdf', [MahasiswaController::class, 'generatePDF'])->name('pdf');
        });

        // Routes yang HANYA bisa diakses Admin (CRUD)
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import/excel', [MahasiswaController::class, 'import'])->name('import');
            Route::get('create', [MahasiswaController::class, 'create'])->name('create');
            Route::post('/', [MahasiswaController::class, 'store'])->name('store');
            Route::get('{mahasiswa}/edit', [MahasiswaController::class, 'edit'])->name('edit');
            Route::put('{mahasiswa}', [MahasiswaController::class, 'update'])->name('update');
            Route::delete('{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | DOSEN - Terpisah berdasarkan Role
    |--------------------------------------------------------------------------
    */
    Route::prefix('dosen')->name('dosen.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa)
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            Route::get('export/excel', [DosenController::class, 'export'])->name('export');
            Route::get('/', [DosenController::class, 'index'])->name('index');
            Route::get('{dosen}', [DosenController::class, 'show'])->name('show');
            Route::get('{dosen}/pdf', [DosenController::class, 'generatePDF'])->name('pdf');
        });

        // Routes yang HANYA bisa diakses Admin (CRUD)
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import/excel', [DosenController::class, 'import'])->name('import');
            Route::get('create', [DosenController::class, 'create'])->name('create');
            Route::post('/', [DosenController::class, 'store'])->name('store');
            Route::get('{dosen}/edit', [DosenController::class, 'edit'])->name('edit');
            Route::put('{dosen}', [DosenController::class, 'update'])->name('update');
            Route::delete('{dosen}', [DosenController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AKADEMIK (Halaman Gabungan Fakultas & Prodi) - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->get('/akademik', [FakultasController::class, 'index'])->name('akademik.index');

    /*
    |--------------------------------------------------------------------------
    | FAKULTAS - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('fakultas')->name('fakultas.')->group(function () {
        // Export & Import routes (specific routes HARUS di atas dynamic routes)
        Route::get('export/excel', [FakultasController::class, 'export'])->name('export');
        Route::post('import/excel', [FakultasController::class, 'import'])->name('import');
        
        // CRUD routes
        Route::get('/', [FakultasController::class, 'index'])->name('index');
        Route::post('/', [FakultasController::class, 'store'])->name('store');
        Route::get('{fakultas}/edit', [FakultasController::class, 'edit'])->name('edit');
        Route::put('{fakultas}', [FakultasController::class, 'update'])->name('update');
        Route::delete('{fakultas}', [FakultasController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PRODI - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('prodi')->name('prodi.')->group(function () {
        // Export & Import routes (specific routes di atas dynamic routes)
        Route::get('export/excel', [ProdiController::class, 'export'])->name('export');
        Route::post('import/excel', [ProdiController::class, 'import'])->name('import');
        
        // CRUD routes
        Route::get('/', [ProdiController::class, 'index'])->name('index');
        Route::get('create', [ProdiController::class, 'create'])->name('create');
        Route::post('/', [ProdiController::class, 'store'])->name('store');
        Route::get('{prodi}/edit', [ProdiController::class, 'edit'])->name('edit');
        Route::put('{prodi}', [ProdiController::class, 'update'])->name('update');
        Route::delete('{prodi}', [ProdiController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MATA KULIAH - Terpisah berdasarkan Role
    |--------------------------------------------------------------------------
    */
    Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa)
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            Route::get('export/excel', [MataKuliahController::class, 'export'])->name('export');
            Route::get('/', [MataKuliahController::class, 'index'])->name('index');
        });

        // Routes yang HANYA bisa diakses Admin (CRUD)
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import/excel', [MataKuliahController::class, 'import'])->name('import');
            Route::get('create', [MataKuliahController::class, 'create'])->name('create');
            Route::post('/', [MataKuliahController::class, 'store'])->name('store');
            Route::get('{mataKuliah}/edit', [MataKuliahController::class, 'edit'])->name('edit');
            Route::put('{mataKuliah}', [MataKuliahController::class, 'update'])->name('update');
            Route::delete('{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | RUANGAN - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('ruangan')->name('ruangan.')->group(function () {
        // Export & Import routes (specific routes HARUS di atas dynamic routes)
        Route::get('export/excel', [RuanganController::class, 'export'])->name('export');
        Route::get('export/pdf', [RuanganController::class, 'exportPdf'])->name('export.pdf');
        Route::post('import/excel', [RuanganController::class, 'import'])->name('import');
        Route::get('template/download', [RuanganController::class, 'downloadTemplate'])->name('template');
        
        // CRUD routes dengan AJAX support
        Route::get('/', [RuanganController::class, 'index'])->name('index');
        Route::post('/', [RuanganController::class, 'store'])->name('store');
        Route::get('{ruangan}/edit', [RuanganController::class, 'edit'])->name('edit');
        Route::put('{ruangan}', [RuanganController::class, 'update'])->name('update');
        Route::delete('{ruangan}', [RuanganController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | JADWAL KULIAH - Terpisah berdasarkan Role
    |--------------------------------------------------------------------------
    */
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa) - READ ONLY
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            Route::get('/', [JadwalController::class, 'index'])->name('index');
            Route::get('data', [JadwalController::class, 'getData'])->name('data');
            Route::get('get-data', [JadwalController::class, 'getData'])->name('get-data');
            Route::get('export/pdf', [JadwalController::class, 'exportPdf'])->name('export.pdf');
            Route::get('export/excel', [JadwalController::class, 'exportExcel'])->name('export.excel');
            Route::get('export-pdf', [JadwalController::class, 'exportPdf'])->name('export-pdf');
            Route::get('export-excel', [JadwalController::class, 'exportExcel'])->name('export-excel');
            Route::get('statistik', [JadwalController::class, 'statistik'])->name('statistik');
        });

        // Routes yang HANYA bisa diakses Admin (CRUD & Import)
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import', [JadwalController::class, 'importExcel'])->name('import');
            Route::post('import/excel', [JadwalController::class, 'importExcel'])->name('import-excel');
            Route::get('mata-kuliah/{prodiId}', [JadwalController::class, 'getMataKuliah'])->name('get-mata-kuliah');
            Route::get('ruangan/fakultas/{fakultasId}', [JadwalController::class, 'getRuanganByFakultas'])->name('ruangan.fakultas');
            Route::post('check-bentrok', [JadwalController::class, 'checkBentrok'])->name('check-bentrok');
            Route::post('hitung-jam-selesai', [JadwalController::class, 'hitungJamSelesai'])->name('hitung-jam-selesai');
            Route::post('{id}/pindah', [JadwalController::class, 'pindahJadwal'])->name('pindah');
            Route::post('/', [JadwalController::class, 'store'])->name('store');
            Route::get('{id}/edit', [JadwalController::class, 'edit'])->name('edit');
            Route::put('{id}', [JadwalController::class, 'update'])->name('update');
            Route::delete('{id}', [JadwalController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PMB (Penerimaan Mahasiswa Baru)
    |--------------------------------------------------------------------------
    */
    
    // Dashboard PMB - Admin & Mahasiswa can access
    Route::middleware(['checkRole:admin,mahasiswa'])->get('/pmb/dashboard', [CalonMahasiswaController::class, 'dashboard'])->name('pmb.dashboard');
    Route::middleware(['checkRole:admin,mahasiswa'])->get('/pmb/calon-mahasiswa/dashboard', [CalonMahasiswaController::class, 'dashboard'])->name('pmb.calon-mahasiswa.dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | CALON MAHASISWA (Routes Lama - Tetap Dipertahankan)
    |--------------------------------------------------------------------------
    */
    Route::prefix('pmb/calon-mahasiswa')->name('pmb.calon-mahasiswa.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa) - READ ONLY
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            // ⚠️ PENTING: Export routes HARUS di atas route dengan parameter dinamis
            Route::get('export/pdf', [CalonMahasiswaController::class, 'exportPDF'])->name('export.pdf');
            Route::get('export/excel', [CalonMahasiswaController::class, 'exportExcel'])->name('export.excel');
            
            Route::get('/', [CalonMahasiswaController::class, 'index'])->name('index');
            Route::get('{calonMahasiswa}', [CalonMahasiswaController::class, 'show'])->name('show');
        });

        // Routes yang HANYA bisa diakses Admin (CRUD & Approval)
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import/excel', [CalonMahasiswaController::class, 'importExcel'])->name('import.excel');
            Route::get('create', [CalonMahasiswaController::class, 'create'])->name('create');
            Route::post('/', [CalonMahasiswaController::class, 'store'])->name('store');
            Route::get('{calonMahasiswa}/edit', [CalonMahasiswaController::class, 'edit'])->name('edit');
            Route::put('{calonMahasiswa}', [CalonMahasiswaController::class, 'update'])->name('update');
            Route::delete('{calonMahasiswa}', [CalonMahasiswaController::class, 'destroy'])->name('destroy');
            Route::post('{calonMahasiswa}/approve', [CalonMahasiswaController::class, 'approve'])->name('approve');
            Route::post('{calonMahasiswa}/reject', [CalonMahasiswaController::class, 'reject'])->name('reject');
            Route::post('{calonMahasiswa}/reset-status', [CalonMahasiswaController::class, 'resetStatus'])->name('reset-status');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | CALON MAHASISWA (Routes Baru - Untuk AJAX Modal)
    |--------------------------------------------------------------------------
    */
    Route::prefix('calon-mahasiswa')->name('calon-mahasiswa.')->group(function () {
        // Routes yang bisa diakses SEMUA (Admin & Mahasiswa) - READ ONLY
        Route::middleware(['checkRole:admin,mahasiswa'])->group(function () {
            // ⚠️ PENTING: Export routes HARUS di atas route dengan parameter dinamis
            Route::get('export/pdf', [CalonMahasiswaController::class, 'exportPDF'])->name('export.pdf');
            Route::get('export/excel', [CalonMahasiswaController::class, 'exportExcel'])->name('export.excel');
            
            Route::get('/', [CalonMahasiswaController::class, 'index'])->name('index');
            Route::get('{calonMahasiswa}', [CalonMahasiswaController::class, 'show'])->name('show');
        });

        // Routes yang HANYA bisa diakses Admin
        Route::middleware(['checkRole:admin'])->group(function () {
            Route::post('import/excel', [CalonMahasiswaController::class, 'importExcel'])->name('import.excel');
            Route::post('/', [CalonMahasiswaController::class, 'store'])->name('store');
            Route::put('{calonMahasiswa}', [CalonMahasiswaController::class, 'update'])->name('update');
            Route::delete('{calonMahasiswa}', [CalonMahasiswaController::class, 'destroy'])->name('destroy');
            Route::post('{calonMahasiswa}/approve', [CalonMahasiswaController::class, 'approve'])->name('approve');
            Route::post('{calonMahasiswa}/reject', [CalonMahasiswaController::class, 'reject'])->name('reject');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | DOKUMEN & VERIFIKASI BERKAS (Routes Lama - Tetap Dipertahankan) - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('pmb/dokumen')->name('pmb.dokumen.')->group(function () {
        Route::get('/{calonMahasiswa}', [DokumenCalonMahasiswaController::class, 'index'])->name('index');
        Route::post('/{calonMahasiswa}/upload', [DokumenCalonMahasiswaController::class, 'upload'])->name('upload');
        Route::get('/{calonMahasiswa}/{dokumen}/download', [DokumenCalonMahasiswaController::class, 'download'])->name('download');
        Route::post('/{calonMahasiswa}/{dokumen}/verify', [DokumenCalonMahasiswaController::class, 'verify'])->name('verify');
        Route::post('/{calonMahasiswa}/{dokumen}/reject', [DokumenCalonMahasiswaController::class, 'reject'])->name('reject');
        Route::delete('/{calonMahasiswa}/{dokumen}', [DokumenCalonMahasiswaController::class, 'destroy'])->name('destroy');
        Route::post('/{calonMahasiswa}/kartu-ujian', [DokumenCalonMahasiswaController::class, 'generateKartuUjian'])->name('kartu-ujian');
    });

    /*
    |--------------------------------------------------------------------------
    | DOKUMEN & VERIFIKASI BERKAS (Routes Baru - Untuk AJAX Modal) - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/{calonMahasiswaId}', [DokumenCalonMahasiswaController::class, 'index'])->name('index');
        Route::post('/{calonMahasiswaId}/upload', [DokumenCalonMahasiswaController::class, 'upload'])->name('upload');
        Route::get('/{calonMahasiswaId}/download/{dokumenId}', [DokumenCalonMahasiswaController::class, 'download'])->name('download');
        Route::post('/{calonMahasiswaId}/{dokumenId}/verify', [DokumenCalonMahasiswaController::class, 'verify'])->name('verify');
        Route::post('/{calonMahasiswaId}/{dokumenId}/reject', [DokumenCalonMahasiswaController::class, 'reject'])->name('reject');
        Route::delete('/{calonMahasiswaId}/delete/{dokumenId}', [DokumenCalonMahasiswaController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | KEUANGAN PMB (Routes Lama - Tetap Dipertahankan) - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('pmb/keuangan')->name('pmb.keuangan.')->group(function () {
        Route::get('laporan/view', [KeuanganPMBController::class, 'laporan'])->name('laporan');
        Route::get('laporan/pdf', [KeuanganPMBController::class, 'laporanPDF'])->name('laporan.pdf');
        Route::get('laporan/excel', [KeuanganPMBController::class, 'laporanExcel'])->name('laporan.excel');
        Route::get('bayar/{keuanganPMB}', [KeuanganPMBController::class, 'bayar'])->name('bayar');
        Route::post('bayar/{keuanganPMB}', [KeuanganPMBController::class, 'konfirmasiBayar'])->name('konfirmasi-bayar');
        Route::get('struk/{keuanganPMB}/pdf', [KeuanganPMBController::class, 'strukPDF'])->name('struk.pdf');
        Route::post('bebaskan/{keuanganPMB}', [KeuanganPMBController::class, 'bebaskan'])->name('bebaskan');
        Route::post('batalkan/{keuanganPMB}', [KeuanganPMBController::class, 'batalkan'])->name('batalkan');
        Route::delete('{keuanganPMB}', [KeuanganPMBController::class, 'destroy'])->name('destroy');
        Route::get('{calonMahasiswa}', [KeuanganPMBController::class, 'show'])->name('show');
        Route::post('{calonMahasiswa}/generate', [KeuanganPMBController::class, 'generate'])->name('generate');
        Route::post('{calonMahasiswa}/tambah', [KeuanganPMBController::class, 'tambahBiaya'])->name('tambah');
        Route::get('{calonMahasiswa}/export-pdf', [KeuanganPMBController::class, 'exportPDF'])->name('export-pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | KEUANGAN PMB (Routes Baru - Untuk Link dari Tabel) - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:admin'])->prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('{calonMahasiswaId}', [KeuanganPMBController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | API Helper Routes (untuk AJAX requests) - Admin & Mahasiswa
    |--------------------------------------------------------------------------
    */
    Route::get('api/dosen', function() {
        return response()->json([
            'data' => \App\Models\Dosen::select('id', 'nama', 'nidn')->get()
        ]);
    })->name('api.dosen');

    Route::get('api/ruangan', function() {
        return response()->json([
            'data' => \App\Models\Ruangan::with('fakultas')
                ->select('id', 'nama_ruangan', 'kode_ruangan', 'kapasitas', 'fakultas_id', 'jenis')
                ->get()
        ]);
    })->name('api.ruangan');
    
    Route::get('api/prodi', function() {
        return response()->json([
            'data' => \App\Models\Prodi::with('fakultas')->get()
        ]);
    })->name('api.prodi');
    
    Route::get('api/fakultas', function() {
        return response()->json([
            'data' => \App\Models\Fakultas::all()
        ]);
    })->name('api.fakultas');

});

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze / Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';