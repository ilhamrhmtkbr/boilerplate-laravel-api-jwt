<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Helpers\ResponseApiHelper;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckJwtCookie Middleware
 *
 * Reads JWT from an httpOnly cookie named 'jwt_token'.
 * - On success:   sets auth()->user() and continues the request.
 * - On expired:   attempts a silent refresh; re-issues cookie on success.
 * - On failure:   returns 401 JSON (API) or redirects to login (web).
 *
 * Register in bootstrap/app.php (Laravel 11+):
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['auth.jwt' => CheckJwtCookie::class]);
 *   })
 *
 * Register in app/Http/Kernel.php (Laravel 10):
 *   protected $routeMiddleware = [
 *       'auth.jwt' => CheckJwtCookie::class,
 *   ];
 */
class CheckJwtCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('jwt_token');

        if (empty($token)) {
            return $this->unauthenticated($request, 'No authentication token provided.');
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return $this->unauthenticated($request, 'User not found.');
            }

            auth()->setUser($user);

        } catch (TokenExpiredException) {
            // ── Silent refresh attempt ─────────────────────────────────
            try {
                $newToken = JWTAuth::refresh($token);
                $user     = JWTAuth::setToken($newToken)->authenticate();
                auth()->setUser($user);

                $response = $next($request);
                return $response->withCookie(self::makeJwtCookie($newToken));

            } catch (JWTException) {
                return $this->unauthenticated($request, 'Session expired. Please login again.');
            }

        } catch (TokenInvalidException) {
            return $this->unauthenticated($request, 'Invalid token.');

        } catch (JWTException $e) {
            return $this->unauthenticated($request, 'Authentication error: ' . $e->getMessage());
        }

        return $next($request);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function unauthenticated(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return ResponseApiHelper::error($message, 401);
        }

        // Web route: redirect to login
        return redirect()->route('login')->withErrors(['auth' => $message]);
    }

    public static function makeJwtCookie(string $token): \Symfony\Component\HttpFoundation\Cookie
    {
        $ttl = (int) config('jwt.ttl', 60); // minutes

        return cookie(
            name:     'jwt_token',
            value:    $token,
            minutes:  $ttl,
            path:     '/',
            domain:   null,
            secure:   app()->isProduction(), // true in production only
            httpOnly: true,
            raw:      false,
            sameSite: 'Strict',
        );
    }
}
