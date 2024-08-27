<?php

namespace Ogrre\ApiAuth\Exceptions;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ValidationExceptionResponse extends ValidationException
{
    protected static function mapErrorToCode(string $attribute, string $rule): array
    {
        $key = "{$attribute}.{$rule}";

        $map = [
            'email.unique' => [
                'code' => 'USER_ALREADY_EXISTS',
                'status' => 422,
            ],
            'password.confirmed' => [
                'code' => 'PASSWORD_NOT_CONFIRMED',
                'status' => 422,
            ],
            'password.required' => [
                'code' => 'PASSWORD_MISSING',
                'status' => 400,
            ],
            'password.min' => [
                'code' => 'INVALID_PASSWORD',
                'status' => 422,
            ],
        ];

        return $map[$key] ?? [
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
        $errors = $exception->validator->errors()->messages();

        $firstError = array_key_first($errors);
        $firstErrorMessage = $errors[$firstError][0];

        $rule = self::extractRuleFromMessage($firstErrorMessage);

        $errorDetails = self::mapErrorToCode($firstError, $rule);

        $response = [
            'status' => 'error',
            'code' => $errorDetails['status'],
            'error' => $errorDetails['code'],
            'error_description' => $firstErrorMessage,
        ];

        return response()->json($response, $errorDetails['status']);
    }

    /**
     * @param string $message
     * @return string
     */
    protected static function extractRuleFromMessage(string $message): string
    {
        return match (true) {
            str_contains($message, 'has already been taken') => 'unique',
            str_contains($message, 'confirmation does not match') => 'confirmed',
            str_contains($message, 'is required') => 'required',
            str_contains($message, 'must be at least') => 'min',
            default => 'unknown',
        };
    }
}
