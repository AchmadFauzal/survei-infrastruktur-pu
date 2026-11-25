<?php

use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\InfrastructureManagementController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\BigReportController;
use App\Http\Controllers\Admin\SurveyLogController;
use App\Http\Controllers\Surveyor\SurveyorDashboardController;
use App\Models\WilayahBoundary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page Peta Leaflet (Publik & Petugas)
Route::get('/', [MapController::class, 'index'])->name('landing');

// Pengarah otomatis jika mengakses /dashboard
Route::middleware(['auth'])->get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('surveyor.dashboard');
})->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 2. Fitur Akuisisi Survei, Tambah Titik Manual, Import CSV/JSON & Foto (Surveyor & Admin)
Route::middleware(['auth'])->group(function () {
    Route::post('/infrastructure/store-point', [MapController::class, 'storePoint'])->name('infrastructure.store_point');
    Route::post('/infrastructure/import', [MapController::class, 'importData'])->name('infrastructure.import');

    Route::post('/survey/{infrastructure}/update', [SurveyController::class, 'update'])->name('survey.update');
    Route::delete('/survey/photo/{photo}', [SurveyController::class, 'destroyPhoto'])->name('survey.photo.delete');
    Route::get('/survey/photo/{photo}/download', [SurveyController::class, 'downloadPhoto'])->name('survey.photo.download');
    Route::get('/survey/{infrastructure}/download-zip', [SurveyController::class, 'downloadAllPhotosZip'])->name('survey.photos.download_zip');
});

// 3. Area Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen User
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Manajemen Data Infrastruktur & Penugasan Surveyor
    Route::get('/infrastructures', [InfrastructureManagementController::class, 'index'])->name('infrastructures.index');
    Route::post('/infrastructures', [InfrastructureManagementController::class, 'store'])->name('infrastructures.store');
    Route::put('/infrastructures/{infrastructure}', [InfrastructureManagementController::class, 'update'])->name('infrastructures.update');
    Route::delete('/infrastructures/{infrastructure}', [InfrastructureManagementController::class, 'destroy'])->name('infrastructures.destroy');
    Route::post('/infrastructures/bulk-delete', [InfrastructureManagementController::class, 'bulkDelete'])->name('infrastructures.bulk_delete');
    Route::post('/infrastructures/bulk-assign', [InfrastructureManagementController::class, 'bulkAssign'])->name('infrastructures.bulk_assign');

    // Halaman Lihat & Kelola List Penugasan Surveyor
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::put('/assignments/{infrastructure}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{infrastructure}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post('/assignments/bulk-unassign', [AssignmentController::class, 'bulkUnassign'])->name('assignments.bulk_unassign');

    // Status Pelaporan ke BIG
    Route::get('/big', [BigReportController::class, 'index'])->name('big.index');
    Route::patch('/big/{infrastructure}/toggle', [BigReportController::class, 'toggle'])->name('big.toggle');
    Route::post('/big/bulk-update', [BigReportController::class, 'bulkUpdate'])->name('big.bulk');

    // Log Aktivitas Survei
    Route::get('/logs', [SurveyLogController::class, 'index'])->name('logs.index');
});

// 4. Area Khusus Surveyor
Route::middleware(['auth', 'role:surveyor'])->prefix('surveyor')->name('surveyor.')->group(function () {
    Route::get('/dashboard', [SurveyorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/assignments', [SurveyorDashboardController::class, 'assignments'])->name('assignments');
    Route::post('/assignments/submit-batch', [SurveyorDashboardController::class, 'submitBatchCompletion'])->name('assignments.submit_batch');
});

// 5. API GeoJSON Batas Wilayah
Route::get('/api/wilayah/geojson', function (Request $request) {
    $level = $request->get('level', 2); // 1 = Provinsi, 2 = Kabupaten/Kota
    $boundaries = WilayahBoundary::where('level', $level)->get();

    $features = $boundaries->map(function ($w) {
        $kodeProv = explode('.', $w->kode)[0];
        return [
            'type' => 'Feature',
            'properties' => [
                'kode'      => $w->kode,
                'kode_prov' => $kodeProv,
                'nama'      => $w->nama,
                'level'     => $w->level,
            ],
            'geometry' => $w->geojson,
        ];
    });

    return response()->json([
        'type'     => 'FeatureCollection',
        'features' => $features,
    ]);
})->name('api.wilayah.geojson');

require __DIR__.'/auth.php';