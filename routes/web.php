<?php

use Illuminate\Support\Facades\Route;
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
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');
    Route::resources([
        'pengguna'            => PenggunaController::class,
        'kategori-pemasukan'  => KategoriPemasukanController::class,
        'kategori-pengeluaran'=> KategoriPengeluaranController::class,
        'pemasukan'           => PemasukanController::class,
        'pengeluaran'         => PengeluaranController::class,
        'anggaran'            => AnggaranController::class,
        'rekening'            => RekeningController::class,
        'transfer'            => TransferController::class,
        'utang'               => UtangController::class,
        'pembayaran-utang'    => PembayaranUtangController::class,
        'piutang'             => PiutangController::class,
        'pembayaran-piutang'  => PembayaranPiutangController::class,
        'laporan'             => LaporanController::class,
        'admin'               => AdminController::class,
    ]);
Route::post('/laporan/generate', [LaporanController::class, 'generate'])
    ->name('laporan.generate');
    Route::prefix('kategori')->name('kategori.')->group(function(){

    // INDEX
    Route::get('/', function(Request $request){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            return app(KategoriPemasukanController::class)->index();
        }
        return app(KategoriPengeluaranController::class)->index();
    })->name('index');

    // CREATE
    Route::get('create', function(Request $request){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            return app(KategoriPemasukanController::class)->create();
        }
        return app(KategoriPengeluaranController::class)->create();
    })->name('create');

    // STORE
    Route::post('/', function(Request $request){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            return app(KategoriPemasukanController::class)->store($request);
        }
        return app(KategoriPengeluaranController::class)->store($request);
    })->name('store');

    // EDIT
    Route::get('{id}/edit', function(Request $request, $id){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            $m = \App\Models\KategoriPemasukan::findOrFail($id);
            return app(KategoriPemasukanController::class)->edit($m);
        }
        $m = \App\Models\KategoriPengeluaran::findOrFail($id);
        return app(KategoriPengeluaranController::class)->edit($m);
    })->name('edit');

    // UPDATE
    Route::match(['put','patch'], '{id}', function(Request $request, $id){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            $m = \App\Models\KategoriPemasukan::findOrFail($id);
            return app(KategoriPemasukanController::class)->update($request, $m);
        }
        $m = \App\Models\KategoriPengeluaran::findOrFail($id);
        return app(KategoriPengeluaranController::class)->update($request, $m);
    })->name('update');

    // DESTROY
    Route::delete('{id}', function(Request $request, $id){
        $type = $request->query('type','pengeluaran');
        if($type==='pemasukan'){
            $m = \App\Models\KategoriPemasukan::findOrFail($id);
            return app(KategoriPemasukanController::class)->destroy($m);
        }
        $m = \App\Models\KategoriPengeluaran::findOrFail($id);
        return app(KategoriPengeluaranController::class)->destroy($m);
    })->name('destroy');
});
});
