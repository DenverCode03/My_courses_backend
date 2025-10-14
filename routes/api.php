<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user;
// })->middleware(AuthenticateWithToken::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
