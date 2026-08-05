<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/videos', [\App\Http\Controllers\NguoiDung\VideoShortController::class, 'apiGetVideos']);
