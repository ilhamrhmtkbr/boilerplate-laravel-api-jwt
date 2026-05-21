<?php

declare(strict_types=1);

namespace App\Application\Auth\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GoogleAuthService — validates Google Sign-In ID tokens.
 *
 * Uses Google's tokeninfo endpoint (no SDK required).
 * For high-traffic production, replace with google/apiclient for offline verification.
 *
 * Add to config/services.php:
 *   'google' => [
 *       'client_id' => env('GOOGLE_CLIENT_ID'),
 *   ],
 *
 * .env:
 *   GOOGLE_CLIENT_ID=your_google_oauth_client_id.apps.googleusercontent.com
 */
class GoogleAuthService
{
    private const TOKEN_INFO_URL = 'https://oauth2.googleapis.com/tokeninfo';

    /**
     * Validate a Google ID token and return the verified payload.
     *
     * @return array{sub: string, email: string, name: string, picture: string, email_verified: bool}
     * @throws \DomainException if token is invalid, expired, or audience mismatches
     */
    public function validateIdToken(string $idToken): array
    {
        if (empty($idToken)) {
            throw new \DomainException('Google ID token is required.', 422);
        }

        $response = Http::timeout(5)->get(self::TOKEN_INFO_URL, ['id_token' => $idToken]);

        if (!$response->successful()) {
            // Google returns 400 for invalid tokens
            if ($response->status() === 400) {
                Log::warning('GoogleAuth: invalid ID token.', ['status' => $response->status()]);
                throw new \DomainException('Invalid Google ID token.', 422);
            }
            Log::warning('GoogleAuth: tokeninfo request failed.', ['status' => $response->status()]);
            throw new \DomainException('Google authentication service unavailable.', 503);
        }

        $payload = $response->json();

        // ── Validate audience (aud) matches our app's Client ID ───────
        $clientId = config('services.google.client_id');
        if (!empty($clientId) && ($payload['aud'] ?? '') !== $clientId) {
            Log::warning('GoogleAuth: audience mismatch.', [
                'expected' => $clientId,
                'received' => $payload['aud'] ?? 'none',
            ]);
            throw new \DomainException('Google token audience mismatch.', 422);
        }

        // ── Validate token expiry ──────────────────────────────────────
        if (!isset($payload['exp']) || (int) $payload['exp'] < time()) {
            throw new \DomainException('Google ID token has expired.', 422);
        }

        // ── Require verified email ─────────────────────────────────────
        if (($payload['email_verified'] ?? 'false') !== 'true') {
            throw new \DomainException('Google account email address is not verified.', 422);
        }

        // ── Required fields ────────────────────────────────────────────
        if (empty($payload['sub']) || empty($payload['email'])) {
            throw new \DomainException('Google token payload is missing required fields.', 422);
        }

        return [
            'sub'            => $payload['sub'],              // unique Google user ID
            'email'          => $payload['email'],
            'name'           => $payload['name'] ?? '',
            'picture'        => $payload['picture'] ?? '',
            'email_verified' => true,
        ];
    }
}
