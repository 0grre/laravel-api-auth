<?php

namespace Ogrre\ApiAuth\Exceptions;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ValidationExceptionResponse extends ValidationException
{
    protected static function mapErrorToCode($error): array
    {
        $map = [
            'validation.password_confirmed' => [
                'code' => 'PASSWORD_NOT_CONFIRMED',
                'status' => 422,
            ],
            'validation.required.password' => [
                'code' => 'PASSWORD_MISSING',
                'status' => 400,
            ],
            'validation.unique.email' => [
                'code' => 'USER_ALREADY_EXISTS',
                'status' => 422,
            ],
            'validation.min.string.password' => [
                'code' => 'INVALID_PASSWORD',
                'status' => 422,
            ],
        ];

        return $map[$error] ?? [
            'code' => 'UNKNOWN_ERROR',
            'status' => 500,
        ];
    }

    /**
     * @param ValidationException $exception
     * @return JsonResponse
     */
    public static function from(ValidationException $exception): JsonResponse
    {
        $errors = $exception->validator->errors()->first();
        $errorDetails = self::mapErrorToCode($errors);

        $response = [
            'status' => 'error',
            'code' => $errorDetails['status'],
            'error' => $errorDetails['code'],
            'error_description' => trans($errors),
        ];

        return response()->json($response, $errorDetails['status']);
    }
}
