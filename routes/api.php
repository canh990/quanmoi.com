<?php

use App\Http\Controllers\NguoiDung\VideoShortController;
use App\Http\Resources\CurrentUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new CurrentUserResource($request->user());
})->middleware(['web', 'auth']);

use App\Http\Controllers\Api\SearchSuggestionController;

Route::get('/videos', [VideoShortController::class, 'apiGetVideos']);
Route::get('/search/suggest', [SearchSuggestionController::class, 'suggestions']);
