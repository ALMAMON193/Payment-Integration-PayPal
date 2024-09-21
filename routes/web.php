<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/process-transaction', [PayPalController::class, 'processTransaction'])->name('processTransaction');
Route::get('/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
Route::get('/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

