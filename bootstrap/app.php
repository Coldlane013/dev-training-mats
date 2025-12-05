<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
// use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Define API middleware group for Sanctum token authentication
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'validate_api_token' => ValidateApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function(QueryException $e, Request $request){
            if($request->is('api/*')){
                return response()->json([
                    'error'     => 'Database Error',
                    'message'   => 'A database operation failed. Please try again.',
                    'details'   => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()->withErrors([
                'database_error' => 'A database operation failed. Please try again.'
            ]);
        });

        $exceptions->report(function(Throwable $e){
            \Log::error('ABd exception occured: '.$e->getMessage());
        });
    })->create();
