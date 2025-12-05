<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\Api\Admin\MetricsController;

Route::prefix('v1')->group(function(){
    // Auth
    Route::post('/auth', [ApiAuthController::class, 'auth'])
        ->name('auth');

    Route::middleware(['auth:sanctum', 'validate_api_token'])->group(function () {
        // Logout by revoking the current token
        Route::post('/deauth', [ApiAuthController::class, 'deauth'])
            ->name('deauth');

        // Resources
        Route::resource('users', UserController::class);
        Route::resource('banks', BankController::class);

        // Admin metrics for minimal dashboard
        Route::get('/admin/metrics', [MetricsController::class, 'index'])->name('admin.metrics');
    });
});