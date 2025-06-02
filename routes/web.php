<?php

use App\Http\Controllers\LoadExcelController;
use Illuminate\Support\Facades\Route;

Route::view('/', view: 'welcome');

Route::middleware('auth.basic')->group(function () {
    Route::get('load-excel', [LoadExcelController::class, 'index']);
    Route::post('load-excel', [LoadExcelController::class, 'store'])->name('load-excel.store');

    Route::get('get-rows', [LoadExcelController::class, 'getRows']);
});

