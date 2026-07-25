<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\KasirController;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Manager
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

    // Bahan Baku
    Route::resource('bahan-baku', \App\Http\Controllers\BahanBakuController::class)->only([
        'index', 'store', 'update', 'destroy'
    ])->names([
        'index'   => 'bahan-baku.index',
        'store'   => 'bahan-baku.store',
        'update'  => 'bahan-baku.update',
        'destroy' => 'bahan-baku.destroy',
    ]);

    // Menu
    Route::resource('menu', \App\Http\Controllers\MenuController::class)->only([
        'index', 'store', 'update', 'destroy'
    ])->names([
        'index'   => 'menu.index',
        'store'   => 'menu.store',
        'update'  => 'menu.update',
        'destroy' => 'menu.destroy',
    ]);

    // Resep
    Route::post('/resep/{menuId}', [\App\Http\Controllers\ResepController::class, 'simpan'])->name('resep.simpan');

    // Pengguna
    Route::get('/pengguna', [\App\Http\Controllers\PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [\App\Http\Controllers\PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{user}', [\App\Http\Controllers\PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{user}', [\App\Http\Controllers\PenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Batch
    Route::get('/batch', [\App\Http\Controllers\BatchController::class, 'index'])->name('batch.index');
    Route::post('/batch', [\App\Http\Controllers\BatchController::class, 'store'])->name('batch.store');
    Route::delete('/batch/{batch}', [\App\Http\Controllers\BatchController::class, 'destroy'])->name('batch.destroy');

    // Food Wastage
    Route::get('/food-wastage', [\App\Http\Controllers\FoodWastageController::class, 'index'])->name('food-wastage.index');
    Route::post('/food-wastage', [\App\Http\Controllers\FoodWastageController::class, 'store'])->name('food-wastage.store');
    
    // Notifikasi
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/baca', [\App\Http\Controllers\NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::get('/notifikasi/baca-semua', [\App\Http\Controllers\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::get('/notifikasi/{id}/hapus', [\App\Http\Controllers\NotifikasiController::class, 'hapus'])->name('notifikasi.hapus');
    Route::get('/notifikasi/hapus-semua', [\App\Http\Controllers\NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus-semua');

    // Riwayat (data mentah/log)
    Route::get('/riwayat', [ManagerController::class, 'riwayatIndex'])->name('riwayat.index');

    // Laporan (analitik untuk pengambilan keputusan)
    Route::get('/laporan', [ManagerController::class, 'laporanAnalitik'])->name('laporan.index');

    // QR
    Route::get('/qrcode', [\App\Http\Controllers\MenuPublikController::class, 'qrcode'])->name('qrcode');
});

// Kasir
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

    // Transaksi
    Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/buat', [\App\Http\Controllers\TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}/struk', [\App\Http\Controllers\TransaksiController::class, 'struk'])->name('transaksi.struk');
    Route::post('/transaksi/{id}/batal', [\App\Http\Controllers\TransaksiController::class, 'batal'])->name('transaksi.batal');

    // Menu
    Route::get('/menu', [KasirController::class, 'menuIndex'])->name('menu.index');
});

// Halaman publik (tanpa login)
Route::get('/menu-publik', [\App\Http\Controllers\MenuPublikController::class, 'index'])->name('menu.publik');

// Notifikasi fetch (polling)
Route::middleware('auth')->get('/notifikasi/fetch', [\App\Http\Controllers\NotifikasiController::class, 'fetch']);