<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Simple check (replace with proper auth logic in a real app)
        if (auth()->user() && auth()->user()->role !== $role) {
            return redirect('home')->with('error', 'Unauthorized action.');
        }

        return $next($request);
    }
}
