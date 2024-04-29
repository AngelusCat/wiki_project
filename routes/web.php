<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', [\App\Http\Controllers\WikiParserController::class, 'store']);
Route::match(['get', 'post'], '/search', [\App\Http\Controllers\WikiParserController::class, 'searchForm']);
Route::get('/importHTMLCode', [\App\Http\Controllers\WikiParserController::class, 'getImportHTMLCode']);
Route::get('/searchHTMLCode', [\App\Http\Controllers\WikiParserController::class, 'getSearchHTMLCode']);
Route::get('/get/{title}', [\App\Http\Controllers\WikiParserController::class, 'getContent']);
