<?php

use App\Http\Controllers\Api\AuthController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login", [AuthController::class, 'login']);
Route::post("/signup", [AuthController::class, 'signup']);
Route::get("/account/verify/{id}/{hash}", [AuthController::class, 'verify'])
    // ->middleware('signed')
    ->name('account.verify');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
