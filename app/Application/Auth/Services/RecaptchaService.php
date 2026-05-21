<?php

declare(strict_types=1);

namespace App\Application\Auth\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * RecaptchaService — validates Google reCAPTCHA v3 tokens.
 *
 * Add to config/services.php:
 *   'recaptcha' => [
 *       'secret_key' => env('RECAPTCHA_SECRET_KEY'),
 *       'min_score'  => env('RECAPTCHA_MIN_SCORE', 0.5),
 *   ],
 *
 * .env:
 *   RECAPTCHA_SECRET_KEY=your_secret_key_here
 *   RECAPTCHA_MIN_SCORE=0.5
 */
class RecaptchaService
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Validate a reCAPTCHA v3 token.
     *
     * @param string $token   Token from frontend (grecaptcha.execute())
     * @param string $action  Expected action name (e.g. 'login', 'register')
     *
     * @throws \DomainException when validation fails, score too low, or service unavailable
     */
    public function validate(string $token, string $action = 'login'): void
    {
        // Skip validation in local/testing environments when token is absent
        if ((app()->isLocal() || app()->runningUnitTests()) && empty($token)) {
            Log::debug('reCAPTCHA skipped (local/test environment).');
            return;
        }

        if (empty($token)) {
            throw new \DomainException('reCAPTCHA token is required.', 422);
        }

        $response = Http::asForm()
            ->timeout(5)
            ->post(self::VERIFY_URL, [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

        if (!$response->successful()) {
            Log::warning('reCAPTCHA HTTP request failed.', ['status' => $response->status()]);
            throw new \DomainException('reCAPTCHA verification service is temporarily unavailable.', 503);
        }

        $body = $response->json();

        // Check success flag
        if (!($body['success'] ?? false)) {
            $errors = implode(', ', $body['error-codes'] ?? ['unknown-error']);
            Log::warning('reCAPTCHA: verification failed.', ['errors' => $errors, 'action' => $action]);
            throw new \DomainException('reCAPTCHA verification failed: ' . $errors, 422);
        }

        // Check action matches (prevents token reuse across actions)
        if (($body['action'] ?? '') !== $action) {
            Log::warning('reCAPTCHA: action mismatch.', [
                'expected' => $action,
                'received' => $body['action'] ?? 'none',
            ]);
            throw new \DomainException('reCAPTCHA action mismatch.', 422);
        }

        // Check score threshold (v3 only)
        $minScore = (float) config('services.recaptcha.min_score', 0.5);
        $score    = (float) ($body['score'] ?? 0.0);

        if ($score < $minScore) {
            Log::warning('reCAPTCHA: score too low.', [
                'score'     => $score,
                'threshold' => $minScore,
                'ip'        => request()->ip(),
            ]);
            throw new \DomainException('Suspicious activity detected. Please try again.', 422);
        }
    }
}
