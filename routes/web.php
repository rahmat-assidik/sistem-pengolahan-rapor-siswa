<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\RekapnilaiController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\PengampuController;
use App\Http\Controllers\InputNilaiController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\ArsipSiswaController;

use App\Http\Controllers\UbahKataSandiController;

// Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('pages.login');
    })->name('login');

    Route::get('/login', function () {
        return redirect()->route('login');
    });

    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/lupa_sandi', function () {
        return view('pages.lupa_sandi');
    })->name('lupa_sandi');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Shared Routes (Admin & Guru)
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/data_rapor', [RaporController::class, 'showRapor'])->name('data_rapor');
    Route::post('/data_rapor/catatan', [RaporController::class, 'saveCatatan'])->name('data_rapor.catatan');
    Route::get('/ubah_kata_sandi', [UbahKataSandiController::class, 'showUbahKataSandi'])->name('ubah_kata_sandi');
    Route::put('/password/update', [UbahKataSandiController::class, 'updatePassword'])->name('password.update');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        // Master Data Management
        Route::get('/data_siswa', [SiswaController::class, 'showDataSiswa'])->name('data_siswa');
        Route::post('/data_siswa', [SiswaController::class, 'store'])->name('data_siswa.store');
        Route::put('/data_siswa/{id}', [SiswaController::class, 'update'])->name('data_siswa.update');
        Route::delete('/data_siswa/{id}', [SiswaController::class, 'destroy'])->name('data_siswa.destroy');

        Route::get('/data_guru', [GuruController::class, 'showGuru'])->name('data_guru');
        Route::post('/data_guru', [GuruController::class, 'store'])->name('data_guru.store');
        Route::put('/data_guru/{id}', [GuruController::class, 'update'])->name('data_guru.update');
        Route::delete('/data_guru/{id}', [GuruController::class, 'destroy'])->name('data_guru.destroy');

        Route::get('/data_kelas', [KelasController::class, 'showKelas'])->name('data_kelas');
        Route::post('/data_kelas', [KelasController::class, 'store'])->name('data_kelas.store');
        Route::put('/data_kelas/{id}', [KelasController::class, 'update'])->name('data_kelas.update');
        Route::delete('/data_kelas/{id}', [KelasController::class, 'destroy'])->name('data_kelas.destroy');

        Route::get('/data_mapel', [MapelController::class, 'showMapel'])->name('data_mapel');
        Route::post('/data_mapel', [MapelController::class, 'store'])->name('data_mapel.store');
        Route::put('/data_mapel/{id}', [MapelController::class, 'update'])->name('data_mapel.update');
        Route::delete('/data_mapel/{id}', [MapelController::class, 'destroy'])->name('data_mapel.destroy');

        Route::get('/pengampu', [PengampuController::class, 'showPengampu'])->name('pengampu');
        Route::resource('pengampu', PengampuController::class)->except(['index']);
        Route::get('/rekap_nilai', [RekapnilaiController::class, 'showRekapNilai'])->name('rekap_nilai');
        Route::get('/arsip_siswa', [ArsipSiswaController::class, 'showArsipSiswa'])->name('arsip_siswa');
        // Akademik Management
        Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik');
        Route::post('/akademik/tahun-ajaran', [AkademikController::class, 'storeTahunAjaran'])->name('akademik.ta.store');
        Route::put('/akademik/tahun-ajaran/{id}', [AkademikController::class, 'updateTahunAjaran'])->name('akademik.ta.update');
        Route::post('/akademik/semester', [AkademikController::class, 'storeSemester'])->name('akademik.smt.store');
        Route::post('/akademik/set-aktif/{id}', [AkademikController::class, 'setAktif'])->name('akademik.set_aktif');
        Route::post('/akademik/ta/nonaktifkan/{id}', [AkademikController::class, 'nonaktifkanTa'])->name('akademik.ta.nonaktifkan');
        Route::post('/akademik/ta/set-aktif/{id}', [AkademikController::class, 'setAktifTa'])->name('akademik.ta.set_aktif');
    });

    // Guru Only Routes
    Route::middleware('role:guru')->group(function () {
        Route::get('/input_nilai', [InputNilaiController::class, 'showInputNilai'])->name('input_nilai');
        Route::post('/input_nilai', [InputNilaiController::class, 'store'])->name('input_nilai.store');
        Route::post('/komponen_nilai', [InputNilaiController::class, 'storeKomponen'])->name('komponen_nilai.store');
        Route::delete('/komponen_nilai/{id}', [InputNilaiController::class, 'destroyKomponen'])->name('komponen_nilai.destroy');

    });
});