<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResponseApiHelper
{
    public static function success(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return self::send($message, $code, $data);
    }

    public static function created(string $message, mixed $data = null): JsonResponse
    {
        return self::send($message, Response::HTTP_CREATED, $data);
    }

    public static function notFound(string $message = 'Data not found'): JsonResponse
    {
        return self::send($message, Response::HTTP_NOT_FOUND);
    }

    public static function unprocessable(string $message): JsonResponse
    {
        return self::send($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function error(string|Throwable $item, int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        if ($item instanceof ModelNotFoundException) {
            return self::send('Data not found', Response::HTTP_NOT_FOUND);
        }

        if ($item instanceof ValidationException) {
            return self::send(
                $item->validator->errors()->first() ?? 'Validation failed',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($item instanceof AuthorizationException) {
            return self::send('You are not authorized to perform this action', Response::HTTP_FORBIDDEN);
        }

        if ($item instanceof Throwable) {
            $message      = $item->getMessage() ?: 'An unexpected error occurred';
            $resolvedCode = (int) $item->getCode();
            if ($resolvedCode < 100 || $resolvedCode > 599) {
                $resolvedCode = $code;
            }

            if ($resolvedCode >= 500) {
                Log::channel('slack')->error("🚨 ERROR {$resolvedCode}: {$message}", [
                    'exception' => get_class($item),
                    'file'      => $item->getFile(),
                    'line'      => $item->getLine(),
                    'trace'     => $item->getTraceAsString(),
                ]);
            }

            return self::send($message, $resolvedCode);
        }

        return self::send($item, $code);
    }

    private static function send(string $message, int $code, mixed $data = null): JsonResponse
    {
        $body = [
            'success' => $code >= 200 && $code < 400,
            'message' => $message,
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        return response()->json($body, $code);
    }
}
