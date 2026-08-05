<?php

use App\Http\Controllers\NguoiDung\VideoShortController;
use App\Http\Resources\CurrentUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new CurrentUserResource($request->user());
})->middleware(['web', 'auth']);

Route::get('/videos', [VideoShortController::class, 'apiGetVideos']);
