<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\KategoriPemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UtangController;
use App\Http\Controllers\PembayaranUtangController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PembayaranPiutangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\LaporanBackupController;
use Illuminate\Support\Facades\Auth;

// Route::get('/google/auth', function () {
//     $client = new \Google_Client();
//     $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
//     $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
//     $client->setRedirectUri(route('google.callback'));
//     $client->setAccessType('offline');
//     $client->setPrompt('consent');
//     $client->setScopes(['https://www.googleapis.com/auth/drive.file']);

//     return redirect($client->createAuthUrl());
// })->name('google.auth');

// Route::get('/google/callback', function () {
//     $client = new \Google_Client();
//     $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
//     $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
//     $client->setRedirectUri(route('google.callback'));

//     $code = request('code');
//     $token = $client->fetchAccessTokenWithAuthCode($code);

//     return response()->json([
//         'access_token' => $token['access_token'] ?? null,
//         'refresh_token' => $token['refresh_token'] ?? '(kosong - mungkin pernah diambil sebelumnya)',
//         'expires_in' => $token['expires_in'] ?? null,
//     ]);
// })->name('google.callback');


 Route::get('/google/auth',      [GoogleAuthController::class, 'redirectToGoogle'])->name('google.auth');
    Route::get('/google/callback',  [GoogleAuthController::class, 'handleCallback'])->name('google.callback');
    Route::post('/google/disconnect',[GoogleAuthController::class, 'disconnect'])->name('google.disconnect');


// Landing Page
Route::get('/', function () {
    return view('landing-page');
});

// Auth Custom (override Fortify)
Route::get('login', function (Request $request) {
    // Jika sudah login sebagai admin → dashboard admin
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    // Jika sudah login sebagai user biasa (web) → dashboard user
    if (Auth::guard('web')->check()) {
        return redirect()->route('dashboard');
    }

    // Belum login sama sekali → tampilkan form login
    return view('auth.login');
})->name('login');
Route::post('/login',  [CustomAuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout');

// ✅ Akses publik untuk register
Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');


Route::prefix('auth/admin')
     ->middleware('auth:admin')
     ->name('admin.')
     ->group(function(){
         // Dashboard
         Route::get('dashboard', [AdminController::class,'dashboard'])
              ->name('dashboard');

                   Route::patch('/pengguna/{pengguna}/toggle-status', [PenggunaController::class, 'toggleStatus'])
        ->name('pengguna.toggleStatus');
         // Resource CRUD untuk Pengguna
         Route::resource('pengguna', PenggunaController::class)
              ->names([
                'index'   => 'pengguna.index',
                'create'  => 'pengguna.create',
                'store'   => 'pengguna.store',
                'edit'    => 'pengguna.edit',
                'update'  => 'pengguna.update',
                'destroy' => 'pengguna.destroy',
              ]);
     });


Route::middleware(['auth'])->group(function () {
    Route::post('/laporan/backup', [LaporanBackupController::class, 'backup'])->name('laporan.backup');
    Route::post('/laporan/restore', [LaporanBackupController::class, 'restore'])->name('laporan.restore');
});
// 🔐 Area yang butuh login
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
 Route::get('/backup', [GoogleDriveController::class, 'backup'])->name('drive.backup');
    Route::get('/restore', [GoogleDriveController::class, 'restore'])->name('drive.restore');
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🔐 Protected: Pengguna (selain create/store)
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::match(['put', 'patch'], '/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');

    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    // Toggle status via AJAX


     Route::prefix('utang')->name('utang.')->group(function () {
        Route::resource('pembayaran', PembayaranUtangController::class);
    });

    Route::prefix('piutang')->name('piutang.')->group(function () {
        // resource routes untuk pembayaran piutang:
        Route::resource('pembayaran', PembayaranPiutangController::class);
    });

    Route::get('/profile', [PenggunaController::class,'editProfile'])
         ->name('pengguna.profile.edit');
    Route::put('/profile', [PenggunaController::class,'updateProfile'])
         ->name('pengguna.profile.update');
    // Resource routes
    Route::resources([
        'kategori-pemasukan'   => KategoriPemasukanController::class,
        'kategori-pengeluaran' => KategoriPengeluaranController::class,
        'pemasukan'            => PemasukanController::class,
        'pengeluaran'          => PengeluaranController::class,
        'anggaran'             => AnggaranController::class,
        'rekening'             => RekeningController::class,
        'transfer'             => TransferController::class,
        'utang'                => UtangController::class,
        'piutang'              => PiutangController::class,
        'laporan'              => LaporanController::class,
        'admin'                => AdminController::class,
    ]);



    Route::post('/laporan/generate', [LaporanController::class, 'generate'])->name('laporan.generate');
 Route::get('/print/{laporan}', [LaporanController::class, 'print'])->name('laporan.print');
    // 🔀 Dynamic kategori route
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', function (Request $request) {
            $type = $request->query('type', 'pengeluaran');
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->index()
                : app(KategoriPengeluaranController::class)->index();
        })->name('index');

        Route::get('create', function (Request $request) {
            $type = $request->query('type', 'pengeluaran');
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->create()
                : app(KategoriPengeluaranController::class)->create();
        })->name('create');

        Route::post('/', function (Request $request) {
            $type = $request->query('type', 'pengeluaran');
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->store($request)
                : app(KategoriPengeluaranController::class)->store($request);
        })->name('store');

        Route::get('{id}/edit', function (Request $request, $id) {
            $type = $request->query('type', 'pengeluaran');
            $model = $type === 'pemasukan'
                ? \App\Models\KategoriPemasukan::findOrFail($id)
                : \App\Models\KategoriPengeluaran::findOrFail($id);
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->edit($model)
                : app(KategoriPengeluaranController::class)->edit($model);
        })->name('edit');

        Route::match(['put', 'patch'], '{id}', function (Request $request, $id) {
            $type = $request->query('type', 'pengeluaran');
            $model = $type === 'pemasukan'
                ? \App\Models\KategoriPemasukan::findOrFail($id)
                : \App\Models\KategoriPengeluaran::findOrFail($id);
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->update($request, $model)
                : app(KategoriPengeluaranController::class)->update($request, $model);
        })->name('update');

        Route::delete('{id}', function (Request $request, $id) {
            $type = $request->query('type', 'pengeluaran');
            $model = $type === 'pemasukan'
                ? \App\Models\KategoriPemasukan::findOrFail($id)
                : \App\Models\KategoriPengeluaran::findOrFail($id);
            return $type === 'pemasukan'
                ? app(KategoriPemasukanController::class)->destroy($model)
                : app(KategoriPengeluaranController::class)->destroy($model);
        })->name('destroy');
    });
});
