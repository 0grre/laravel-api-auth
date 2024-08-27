<?php

namespace Ogrre\ApiAuth\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ApiAuthController
{
    use AuthorizesRequests, ValidatesRequests, RefreshDatabase;

    protected $userModel;

    /**
     * ApiAuthController constructor.
     */
    public function __construct()
    {
        $this->userModel = app('ApiAuthUser');
    }

    /**
     * Format and return an error response.
     *
     * @param int $code
     * @param string $error
     * @param string $errorDescription
     * @param int $httpStatusCode
     * @return JsonResponse
     */
    private function errorResponse(int $code, string $error, string $errorDescription, int $httpStatusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'error' => $error,
            'error_description' => $errorDescription,
        ], $httpStatusCode);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8|string|confirmed',
        ]);

        $user = new $this->userModel;
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        $expiresAt = config('sanctum.expiration')
            ? Carbon::now()->addMinutes(config('sanctum.expiration'))->toDateTimeString()
            : null;

        return response()->json([
            'status' => 'success',
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt,
            'access_token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(Request $request): JsonResponse
    {
        $userModelClass = config('auth.providers.users.model');

        $user = $userModelClass()::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse(401, 'USER_NOT_FOUND', 'laravel-api-auth::messages.user_not_found', 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse(401, 'INVALID_PASSWORD', 'laravel-api-auth::messages.invalid_passwords', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $expiresAt = config('sanctum.expiration') ?? Carbon::now()->addMinutes(config('sanctum.expiration'));

        return response()->json([
            'status' => 'success',
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt,
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => __('laravel-api-auth::messages.logout_success')]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => __('laravel-api-auth::messages.reset_link_sent')]);
        } else {
            return response()->json(['message' => __('laravel-api-auth::messages.reset_link_failed')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => __('laravel-api-auth::messages.password_reset_success')]);
        } else {
            return response()->json(['message' => __('laravel-api-auth::messages.password_reset_failed')], 500);
        }
    }
}
