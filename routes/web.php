<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', [\App\Http\Controllers\WikiParserController::class, 'import']);
Route::match(['get', 'post'], '/search', [\App\Http\Controllers\WikiParserController::class, 'search']);
Route::get('/importHTMLCode', [\App\Http\Controllers\WikiParserController::class, 'getImportHTMLCode']);
Route::get('/searchHTMLCode', [\App\Http\Controllers\WikiParserController::class, 'getSearchHTMLCode']);
Route::get('/getArticleContent/{title}', [\App\Http\Controllers\WikiParserController::class, 'getArticleContent']);
