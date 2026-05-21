<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes — PUBLIC (no auth.jwt middleware)
|--------------------------------------------------------------------------
| These routes handle authentication and are intentionally public.
| reCAPTCHA + credentials act as the authentication factor here.
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->name('auth.')->group(function () {

    // Email + password login
    Route::post('/login',        [AuthController::class, 'login'])->name('login');

    // Google Sign-In (One Tap / popup)
    Route::post('/login/google', [AuthController::class, 'loginWithGoogle'])->name('login.google');

    // Protected auth actions (require valid JWT cookie)
    Route::middleware(['auth.jwt'])->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/me',       [AuthController::class, 'me'])->name('me');
    });

});
