<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('items', \App\Http\Controllers\Api\ItemController::class)
    ->only(['index', 'show', 'store', 'destroy']);
