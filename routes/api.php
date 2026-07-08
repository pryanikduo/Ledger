<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function() {
    return response()->json(['message' => 'Аутентификация прошла успешно',
        'user' => auth()->user()->email]);
})->middleware('auth.basic');

Route::get('/account/{account}/balance', [AccountController::class, 'balance'])
    ->middleware('auth.basic');

Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware('auth.basic');