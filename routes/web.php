<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', [\App\Http\Controllers\TestController::class, 'store']);
Route::match(['get', 'post'], '/search', [\App\Http\Controllers\TestController::class, 'searchForm']);
Route::get('/import', [\App\Http\Controllers\TestController::class, 'import']);
Route::get('/showSearch', [\App\Http\Controllers\TestController::class, 'search']);
Route::get('/get/{title}', [\App\Http\Controllers\TestController::class, 'getContent']);
