<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Auth;

use App\Application\Auth\Services\GoogleAuthService;
use App\Application\Auth\Services\RecaptchaService;
use App\Helpers\ResponseApiHelper;
use App\Models\User;
use App\Presentation\Http\Middleware\CheckJwtCookie;
use App\Presentation\Http\Requests\Auth\GoogleLoginRequest;
use App\Presentation\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * AuthController
 *
 * Handles all authentication flows:
 *   POST /auth/login              — email + password + reCAPTCHA
 *   POST /auth/login/google       — Google ID token + reCAPTCHA
 *   POST /auth/refresh            — silent JWT refresh (auth.jwt required)
 *   POST /auth/logout             — invalidate token, clear cookie (auth.jwt required)
 *   GET  /auth/me                 — current authenticated user (auth.jwt required)
 */
class AuthController
{
    public function __construct(
        private readonly RecaptchaService  $recaptchaService,
        private readonly GoogleAuthService $googleAuthService,
    ) {}

    // ── Public endpoints ──────────────────────────────────────────

    /**
     * Login with email/password.
     * reCAPTCHA v3 is validated before credential check.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // 1. Validate reCAPTCHA v3
            $this->recaptchaService->validate(
                token:  $request->string('recaptcha_token')->toString(),
                action: 'login',
            );

            // 2. Attempt JWT authentication
            $token = JWTAuth::attempt($request->only('email', 'password'));

            if (!$token) {
                return ResponseApiHelper::error('Invalid email or password.', 401);
            }

            return $this->issueTokenResponse($token, 'Login successful.');

        } catch (\DomainException $e) {
            return ResponseApiHelper::error($e);
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    /**
     * Login with Google Sign-In ID token.
     * Validates the Google token, then finds or creates a User record.
     */
    public function loginWithGoogle(GoogleLoginRequest $request): JsonResponse
    {
        try {
            // 1. Validate reCAPTCHA v3
            $this->recaptchaService->validate(
                token:  $request->string('recaptcha_token')->toString(),
                action: 'login_google',
            );

            // 2. Validate Google ID token → returns verified payload
            $payload = $this->googleAuthService->validateIdToken(
                $request->string('id_token')->toString()
            );

            // 3. Find or create user by google_sub (stable across email changes)
            $user = User::firstOrCreate(
                ['google_sub' => $payload['sub']],
                [
                    'name'              => $payload['name'],
                    'email'             => $payload['email'],
                    'avatar'            => $payload['picture'],
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(40)), // unusable password for OAuth users
                    'google_sub'        => $payload['sub'],
                ]
            );

            // Sync email if updated on Google side
            if ($user->email !== $payload['email']) {
                $user->update(['email' => $payload['email']]);
            }

            // 4. Issue JWT
            $token = JWTAuth::fromUser($user);

            return $this->issueTokenResponse($token, 'Google login successful.');

        } catch (\DomainException $e) {
            return ResponseApiHelper::error($e);
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    // ── Protected endpoints (auth.jwt middleware required) ────────

    /**
     * Logout — invalidates JWT and clears the httpOnly cookie.
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Throwable) {
            // Silent — cookie will be cleared regardless of invalidation result
        }

        return ResponseApiHelper::success('Logged out successfully.')
            ->withCookie(Cookie::forget('jwt_token'));
    }

    /**
     * Refresh — issues a new JWT and re-sets the cookie.
     * The old token is invalidated (single-use refresh).
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return $this->issueTokenResponse($newToken, 'Token refreshed.');
        } catch (JWTException $e) {
            return ResponseApiHelper::error('Unable to refresh token. Please login again.', 401);
        }
    }

    /**
     * Me — returns the currently authenticated user.
     */
    public function me(): JsonResponse
    {
        return ResponseApiHelper::success('Authenticated user retrieved.', auth()->user());
    }

    // ── Private Helpers ───────────────────────────────────────────

    /**
     * Build a JSON response that sets the JWT as an httpOnly cookie.
     * The response body does NOT include the raw token — only meta info.
     */
    private function issueTokenResponse(string $token, string $message): JsonResponse
    {
        return ResponseApiHelper::success($message, [
            'token_type' => 'httponly_cookie',
            'expires_in' => (int) config('jwt.ttl', 60) * 60, // seconds
            'user'       => auth()->user(),
        ])->withCookie(CheckJwtCookie::makeJwtCookie($token));
    }
}
