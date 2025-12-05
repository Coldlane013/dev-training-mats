<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Jobs\ProcessUserAuthLog;
use App\Jobs\LogAuthAttempt;
use Illuminate\Support\Facades\Cache;
use App\Models\User; // Assuming User model

class ApiAuthController extends Controller
{
    /**
     * Handle API Token Login.
     */
    public function auth(Request $request): JsonResponse
    {
        // 1. Basic Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');

        // 2. Authenticate Credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Log failed login attempt
            LogAuthAttempt::dispatch(
                email: $email,
                status: 'failed',
                ipAddress: $ipAddress,
                userAgent: $userAgent,
                failureReason: 'invalid_credentials'
            )->onQueue('auth-logs');

            // Increment failed-login metric counter in cache
            try {
                Cache::increment('auth_failed_count');
            } catch (\Throwable $ex) {
                // ignore cache errors
            }

            throw ValidationException::withMessages([
                'email' => ['Invalid Email/Password. Please try again.'],
            ]);
        }

        // 3. Authentication Successful: Create Sanctum Token
        $user = Auth::user();

        // Ensure old tokens are revoked if desired, then create a new one.
        $user->tokens()->delete();

        // Create the token with defined abilities (permissions)
        $plainToken = $user->createToken('auth-token', ['server:update'])->plainTextToken;

        // Log successful login attempt
        LogAuthAttempt::dispatch(
            email: $user->email,
            status: 'success',
            ipAddress: $ipAddress,
            userAgent: $userAgent
        )->onQueue('auth-logs');

        // Dispatch legacy job for backward compatibility
        ProcessUserAuthLog::dispatch()->onQueue('auth-logs');

        return response()->json([
            'token' => $plainToken,
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'user' => $user->only('id', 'name', 'email'),
        ], 200);
    }

    /**
     * Revoke API Token (Logout).
     */
    public function deauth(Request $request): JsonResponse
    {
        // The current token is available via $request->user()->currentAccessToken()
        // Delete the token used for the current request.
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked successfully.'], 200);
    }
}