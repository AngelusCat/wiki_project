<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\TestController::class, 'index']);
Route::post('/articles', [\App\Http\Controllers\TestController::class, 'store']);
Route::get('/search', [\App\Http\Controllers\TestController::class, 'showSearch']);
Route::post('/searchForm', [\App\Http\Controllers\TestController::class, 'searchForm']);
