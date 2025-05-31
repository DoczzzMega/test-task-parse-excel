<?php

use App\Http\Controllers\LoadExcelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.basic')->group(function () {
    Route::get('load-excel', [LoadExcelController::class, 'index']);
    Route::post('load-excel', [LoadExcelController::class, 'store'])->name('load-excel.store');
});

