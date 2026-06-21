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
use App\Http\Controllers\RaporController;
use App\Http\Controllers\PengampuController;
use App\Http\Controllers\InputNilaiController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\ArsipRaporController;
use App\Http\Controllers\PembagianKelasController;
use App\Http\Controllers\BobotNilaiController;
use App\Http\Controllers\TandaTanganController;
use App\Http\Controllers\UbahKataSandiController;
use App\Http\Controllers\StatusRaporController;

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
    Route::get('/data_rapor/{nis}/download/{semester_id}', [RaporController::class, 'generateRapor'])->name('data_rapor.download');
    Route::get('/data_rapor/{nis}/preview/{semester_id}', [RaporController::class, 'previewRapor'])->name('data_rapor.preview');
    Route::get('/ubah_kata_sandi', [UbahKataSandiController::class, 'showUbahKataSandi'])->name('ubah_kata_sandi');
    Route::put('/password/update', [UbahKataSandiController::class, 'updatePassword'])->name('password.update');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        // Master Data Management
        Route::get('/data_siswa', [SiswaController::class, 'showDataSiswa'])->name('data_siswa');
        Route::post('/data_siswa', [SiswaController::class, 'store'])->name('data_siswa.store');
        Route::put('/data_siswa/{id}', [SiswaController::class, 'update'])->name('data_siswa.update');
        Route::delete('/data_siswa/{id}', [SiswaController::class, 'destroy'])->name('data_siswa.destroy');

        Route::get('/data_guru', [GuruController::class, 'index'])->name('data_guru');
        Route::post('/data_guru', [GuruController::class, 'store'])->name('data_guru.store');
        Route::put('/data_guru/{id}', [GuruController::class, 'update'])->name('data_guru.update');
        Route::delete('/data_guru/{id}', [GuruController::class, 'destroy'])->name('data_guru.destroy');


        Route::get('/data_kelas', [KelasController::class, 'showKelas'])->name('data_kelas');
        Route::post('/data_kelas', [KelasController::class, 'store'])->name('data_kelas.store');
        Route::put('/data_kelas/{id}', [KelasController::class, 'update'])->name('data_kelas.update');
        Route::delete('/data_kelas/{id}', [KelasController::class, 'destroy'])->name('data_kelas.destroy');

        Route::get('/data_mapel', [MapelController::class, 'index'])->name('data_mapel');
        Route::post('/data_mapel', [MapelController::class, 'store'])->name('data_mapel.store');
        Route::get('/data_mapel/{kode_mapel}/edit', [MapelController::class, 'edit'])->name('data_mapel.edit');
        Route::put('/data_mapel/{kode_mapel}', [MapelController::class, 'update'])->name('data_mapel.update');
        Route::delete('/data_mapel/{kode_mapel}', [MapelController::class, 'destroy'])->name('data_mapel.destroy');

        Route::get('/pengampu', [PengampuController::class, 'showPengampu'])->name('pengampu');
        Route::post('/pengampu/import', [PengampuController::class, 'importFromSemester'])->name('pengampu.import');
        Route::resource('pengampu', PengampuController::class)->except(['index']);
        Route::get('/arsip_rapor', [ArsipRaporController::class, 'showArsipRapor'])->name('arsip_rapor');
        
        Route::get('/pembagian_kelas', [PembagianKelasController::class, 'showPembagianKelas'])->name('pembagian_kelas');
        Route::get('/pembagian_kelas/set_wali_kelas', [PembagianKelasController::class, 'showSetWaliKelas'])->name('set_wali_kelas');
        Route::post('/pembagian_kelas/set_wali_kelas', [PembagianKelasController::class, 'updateWaliKelas'])->name('set_wali_kelas.update');
        Route::get('/pembagian_kelas/{kode_kelas}/kelola', [PembagianKelasController::class, 'manageStudents'])->name('pembagian_kelas.manage');
        Route::post('/pembagian_kelas', [PembagianKelasController::class, 'store'])->name('pembagian_kelas.store');
        Route::put('/pembagian_kelas/{id}', [PembagianKelasController::class, 'update'])->name('pembagian_kelas.update');
        Route::delete('/pembagian_kelas/{id}', [PembagianKelasController::class, 'destroy'])->name('pembagian_kelas.destroy');
        Route::post('/pembagian_kelas/import', [PembagianKelasController::class, 'importFromSemester'])->name('pembagian_kelas.import');
        Route::post('/pembagian_kelas/bulk', [PembagianKelasController::class, 'bulkStore'])->name('pembagian_kelas.bulk');
        Route::post('/pembagian_kelas/move-all', [PembagianKelasController::class, 'moveAll'])->name('pembagian_kelas.move_all');
        
        // Akademik Management
        Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik');
        Route::post('/akademik/tahun-ajaran', [AkademikController::class, 'storeTahunAjaran'])->name('akademik.ta.store');
        Route::put('/akademik/tahun-ajaran/{id}', [AkademikController::class, 'updateTahunAjaran'])->name('akademik.ta.update');
        Route::post('/akademik/semester', [AkademikController::class, 'storeSemester'])->name('akademik.smt.store');
        Route::post('/akademik/set-aktif/{id}', [AkademikController::class, 'setAktif'])->name('akademik.set_aktif');
        Route::post('/akademik/ta/nonaktifkan/{id}', [AkademikController::class, 'nonaktifkanTa'])->name('akademik.ta.nonaktifkan');
        Route::post('/akademik/ta/set-aktif/{id}', [AkademikController::class, 'setAktifTa'])->name('akademik.ta.set_aktif');

        // Global Grade Weights Management
        Route::get('/bobot_nilai', [BobotNilaiController::class, 'index'])->name('settings.bobot');
        Route::put('/bobot_nilai', [BobotNilaiController::class, 'update'])->name('settings.update');
        // Admin Signature Settings
        Route::get('/settings/signature', [TandaTanganController::class, 'showSettings'])->name('admin.signatures.index');
        Route::post('/settings/signature', [TandaTanganController::class, 'updateSignature'])->name('admin.signatures.update');
    });

    // Guru Only Routes
    Route::middleware('role:guru')->group(function () {
        Route::get('/input_nilai', [InputNilaiController::class, 'showInputNilai'])->name('input_nilai');
        Route::post('/input_nilai', [InputNilaiController::class, 'store'])->name('input_nilai.store');
        Route::post('/komponen_nilai', [InputNilaiController::class, 'storeKomponen'])->name('komponen_nilai.store');
        Route::delete('/komponen_nilai/{id}', [InputNilaiController::class, 'destroyKomponen'])->name('komponen_nilai.destroy');
        
        // Guru Signature Upload
        Route::get('/guru/signature', [GuruController::class, 'showSignatureForm'])->name('guru.signature.show');
        Route::post('/guru/signature', [GuruController::class, 'uploadSignature'])->name('guru.signature.upload');

        // Status Rapor (Wali Kelas only, additional check in controller)
        Route::get('/status_rapor', [StatusRaporController::class, 'index'])->name('status_rapor');
        Route::put('/status_rapor/update', [StatusRaporController::class, 'updateStatus'])->name('status_rapor.update');
        Route::put('/status_rapor/bulk-update', [StatusRaporController::class, 'bulkUpdateStatus'])->name('status_rapor.bulk_update');
    });
    // tahun ajaran management
    Route::resource('tahun-ajaran', TahunAjaranController::class);
});
