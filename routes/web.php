<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/print/barcode', [PrintController::class, 'barcode'])
    ->name('print.barcode');
