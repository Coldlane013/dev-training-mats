<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request to check if user has valid API token.
     * This middleware checks the Authorization header for a Bearer token.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract Bearer token from Authorization header
        $token = $this->extractToken($request);

        if (!$token) {
            // No token provided - redirect to login
            return redirect()->route('login');
        }

        return $next($request);
    }

    /**
     * Extract Bearer token from the Authorization header.
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
