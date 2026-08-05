<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\GoogleController;
use App\Http\Controllers\Api\V1\Cuesheet\CuesheetController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Profile\ProfileAvatarController;
use App\Http\Controllers\Api\V1\Registration\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {

    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::post('avatar', [ProfileAvatarController::class, 'store'])->name('api.v1.profile.avatar.store');
            Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::get('cuesheets', [CuesheetController::class, 'index'])->name('api.v1.cuesheets.index');
            Route::get('registrations', [RegistrationController::class, 'index'])->name('api.v1.registrations.index');
            Route::post('notifications', [NotificationController::class, 'store'])->name('api.v1.notifications.store');
        });

        Route::prefix('google')->group(function (): void {
            Route::get('redirect', [GoogleController::class, 'redirect'])->name('api.v1.auth.google.redirect');
            Route::get('callback', [GoogleController::class, 'callback'])->name('api.v1.auth.google.callback');
        });
    });

});
