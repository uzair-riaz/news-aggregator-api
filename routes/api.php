<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('/articles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{article}', [ArticleController::class, 'show']);
});

Route::prefix('/preferences')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserPreferenceController::class, 'index']);
    Route::post('/', [UserPreferenceController::class, 'save']);
});

Route::prefix('/user')->middleware('auth:sanctum')->group(function () {
    Route::get('/personalized-feed', [UserPreferenceController::class, 'personalizedNewsFeed']);
});
