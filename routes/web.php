<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenyerahanController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Semua halaman lain wajib login. Middleware 'role:...' membatasi per role;
// admin adalah superuser dan selalu lolos.
Route::middleware('auth')->group(function () {
    // Dashboard: semua role (isinya menyesuaikan role)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pasien: semua role bisa melihat; dokter+kasir (dan admin) bisa menambah;
    // edit/hapus hanya admin.
    Route::get('/pasien', [PasienController::class, 'index'])
        ->middleware('role:dokter,apoteker,kasir')->name('pasien.index');
    Route::post('/pasien', [PasienController::class, 'store'])
        ->middleware('role:dokter,kasir')->name('pasien.store');
    Route::middleware('role:admin')->group(function () {
        Route::get('/pasien/{pasien}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
        Route::put('/pasien/{pasien}', [PasienController::class, 'update'])->name('pasien.update');
        Route::delete('/pasien/{pasien}', [PasienController::class, 'destroy'])->name('pasien.destroy');
    });

    // Master dokter: dokter bisa melihat; kelola hanya admin.
    Route::get('/dokter', [DokterController::class, 'index'])
        ->middleware('role:dokter')->name('dokter.index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
        Route::get('/dokter/{dokter}/edit', [DokterController::class, 'edit'])->name('dokter.edit');
        Route::put('/dokter/{dokter}', [DokterController::class, 'update'])->name('dokter.update');
        Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    });

    // Obat: apoteker (dan admin) kelola penuh; dokter hanya melihat.
    Route::get('/obat', [ObatController::class, 'index'])
        ->middleware('role:apoteker,dokter')->name('obat.index');
    Route::middleware('role:apoteker')->group(function () {
        Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
        Route::get('/obat/{obat}/edit', [ObatController::class, 'edit'])->name('obat.edit');
        Route::put('/obat/{obat}', [ObatController::class, 'update'])->name('obat.update');
        Route::delete('/obat/{obat}', [ObatController::class, 'destroy'])->name('obat.destroy');
    });

    // Rekam medis: semua role melihat daftar + detail (halaman resep);
    // dokter (miliknya sendiri, dicek di controller) dan admin yang mengelola.
    Route::get('/rekam-medis', [RekamMedisController::class, 'index'])
        ->middleware('role:dokter,apoteker,kasir')->name('rekam-medis.index');
    Route::get('/rekam-medis/{rekamMedis}/resep', [ResepController::class, 'index'])
        ->middleware('role:dokter,apoteker,kasir')->name('resep.index');
    Route::middleware('role:dokter')->group(function () {
        Route::post('/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');
        Route::get('/rekam-medis/{rekamMedis}/edit', [RekamMedisController::class, 'edit'])->name('rekam-medis.edit');
        Route::put('/rekam-medis/{rekamMedis}', [RekamMedisController::class, 'update'])->name('rekam-medis.update');
        Route::delete('/rekam-medis/{rekamMedis}', [RekamMedisController::class, 'destroy'])->name('rekam-medis.destroy');

        // Baris resep: dokter meresepkan / menghapus (aturan status di controller)
        Route::post('/rekam-medis/{rekamMedis}/resep', [ResepController::class, 'store'])->name('resep.store');
        Route::delete('/rekam-medis/{rekamMedis}/resep/{resep}', [ResepController::class, 'destroy'])->name('resep.destroy');
    });

    // Penyerahan obat (antrean resep 'diresepkan'): apoteker (dan admin).
    Route::middleware('role:apoteker')->group(function () {
        Route::get('/penyerahan', [PenyerahanController::class, 'index'])->name('penyerahan.index');
        Route::post('/penyerahan/{resep}', [PenyerahanController::class, 'serahkan'])->name('penyerahan.serahkan');
    });

    // Pembayaran: kasir (dan admin).
    Route::middleware('role:kasir')->group(function () {
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::patch('/pembayaran/{pembayaran}/status', [PembayaranController::class, 'updateStatus'])->name('pembayaran.status');
    });

    // Laporan per role
    Route::get('/laporan/pasien', [LaporanController::class, 'pasien'])
        ->middleware('role:dokter')->name('laporan.pasien');
    Route::get('/laporan/obat', [LaporanController::class, 'obat'])
        ->middleware('role:apoteker')->name('laporan.obat');
    Route::get('/laporan/pembayaran', [LaporanController::class, 'pembayaran'])
        ->middleware('role:kasir')->name('laporan.pembayaran');

    // Manajemen pengguna: hanya admin.
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
