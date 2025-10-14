<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;


Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('places', PlaceController::class)->only(['index', 'show']);

// [BARU] Rute untuk mendapatkan tempat berdasarkan kategori
Route::get('/places/categories/{categoryId}', [PlaceController::class, 'getByCategory']);

// path get media
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*');
