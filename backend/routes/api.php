<?php
//routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/auth/github/redirect', [AuthController::class, 'redirectToGithub']);
Route::get('/auth/github/callback', [AuthController::class,'handleGithubCallback']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);
});