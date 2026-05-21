<?php

declare(strict_types=1);

use App\Helpers\ResponseApiHelper;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Auth strategy: JWT stored in httpOnly + Secure + SameSite=Strict cookie.
|
| The 'auth.jwt' middleware alias must be registered:
|
|   Laravel 11 (bootstrap/app.php):
|     ->withMiddleware(function (Middleware $middleware) {
|         $middleware->alias([
|             'auth.jwt' => \App\Presentation\Http\Middleware\CheckJwtCookie::class,
|         ]);
|         // Exclude jwt_token cookie from CSRF encryption so the
|         // middleware can read it as a plain string:
|         $middleware->encryptCookiesExcept(['jwt_token']);
|     })
|
|   Laravel 10 (app/Http/Kernel.php):
|     protected $routeMiddleware = [
|         'auth.jwt' => \App\Presentation\Http\Middleware\CheckJwtCookie::class,
|     ];
|     // And in $middlewareGroups['api'], ensure EncryptCookies
|     // excludes 'jwt_token' or use \App\Http\Middleware\EncryptCookies::$except.
|
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public: Auth routes (login, google, refresh, logout, me) ──
    require __DIR__ . '/api/auth.php';

    // ── Protected: All ERP module routes ──────────────────────────
    Route::middleware(['auth.jwt'])->group(function () {

        require __DIR__ . '/api/finance.php';
        require __DIR__ . '/api/hrm.php';
        require __DIR__ . '/api/inventory.php';
        require __DIR__ . '/api/sales.php';
        require __DIR__ . '/api/procurement.php';
        require __DIR__ . '/api/asset.php';
        require __DIR__ . '/api/crm.php';
        require __DIR__ . '/api/project.php';
        require __DIR__ . '/api/accounting.php';

    });

});

// Health check — always public
Route::get('/health', fn () => ResponseApiHelper::success('OK', [
    'timestamp' => now()->toIso8601String(),
    'app'       => config('app.name'),
]))->name('health');
