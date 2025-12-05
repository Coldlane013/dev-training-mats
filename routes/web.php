<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Jobs\ProcessUserUpdate;

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');

Route::group(['middleware' => ['web']], function () {
    Route::get('/sanctum/csrf-cookie', function () {
        return response('OK');
    });
});

// Frontend dashboard - accessible after API login
// The frontend will validate the API token via axios client
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Users list page
Route::get('/users', function () {
    return Inertia::render('Users');
})->name('users');


require __DIR__.'/settings.php';