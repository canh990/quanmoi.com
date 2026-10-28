<?php

use App\Http\Controllers\Api\SearchSuggestionController;
use App\Http\Controllers\NguoiDung\NapTienController;
use App\Http\Controllers\NguoiDung\VideoShortController;
use App\Http\Resources\CurrentUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new CurrentUserResource($request->user());
})->middleware(['web', 'auth']);

Route::get('/videos', [VideoShortController::class, 'apiGetVideos']);
Route::get('/search/suggest', [SearchSuggestionController::class, 'suggestions']);

// SePay Webhook & IPN endpoints
Route::post('/sepay/webhook', [NapTienController::class, 'webhook'])->name('api.sepay.webhook');
Route::post('/sepay/ipn', [NapTienController::class, 'ipn'])->name('api.sepay.ipn');
Route::get('/tai-khoan/nap-tien/{code}/status', [NapTienController::class, 'status'])->name('api.sepay.status');
Route::get('/tai-khoan/nap-tien/status/{code}', [NapTienController::class, 'status']);
