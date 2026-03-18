<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {

    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');
        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('api.v1.auth.logout');

        Route::prefix('google')->group(function (): void {
            Route::get('redirect', [GoogleController::class, 'redirect'])->name('api.v1.auth.google.redirect');
            Route::get('callback', [GoogleController::class, 'callback'])->name('api.v1.auth.google.callback');
        });

        Route::get('mobile-callback', function (Request $request): Response {
            $path = $request->filled('error')
                ? 'auth/callback?error=1&message='.urlencode($request->query('message', 'Google login failed'))
                : 'auth/callback?token='.urlencode($request->query('token', ''));

            // Use Android intent:// URL — Chrome Custom Tabs explicitly supports this format
            // and will fire the Android Intent to open the app, unlike nativephp:// scheme
            // which Chrome blocks when navigated via JavaScript.
            $intentUrl = 'intent://'.$path.'#Intent;scheme=nativephp;package=com.quiz.myapp;end';

            return response('<html><head><meta http-equiv="refresh" content="0;url='.htmlspecialchars($intentUrl, ENT_QUOTES).'"></head><body></body></html>')
                ->header('Content-Type', 'text/html');
        })->name('api.v1.auth.mobile-callback');
    });

});
