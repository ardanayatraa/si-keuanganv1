<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/utang/{utangId}/installments', [App\Http\Controllers\UtangController::class, 'getInstallments']);
