<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

class ValidateApiToken
{
    /**
     * Handle an incoming request to validate the API token.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract the Bearer token from the Authorization header
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json([
                'message' => 'No API token provided',
                'error' => 'Unauthorized',
            ], 401);
        }

        // Attempt to find the token in the database using Sanctum helper
        // PersonalAccessToken::findToken handles the plain-text token format (id|token)
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'message' => 'Invalid or expired API token',
                'error' => 'Unauthorized',
            ], 401);
        }

        // Check if the token is expired (if you have expiration logic)
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'message' => 'API token has expired',
                'error' => 'Unauthorized',
            ], 401);
        }

        // Attach the authenticated user to the request
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }

    /**
     * Extract the Bearer token from the Authorization header.
     */
    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (!$header) {
            return null;
        }

        // Check if header follows "Bearer {token}" format
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
