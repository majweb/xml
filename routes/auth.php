<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('rejestracja', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('rejestracja', [RegisteredUserController::class, 'store']);

    Route::get('logowanie', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('logowanie', [AuthenticatedSessionController::class, 'store']);

    Route::get('przypomnij-haslo', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('przypomnij-haslo', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-hasla/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-hasla', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('weryfikacja-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('weryfikacja-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('potwierdz-haslo', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('potwierdz-haslo', [ConfirmablePasswordController::class, 'store']);

    Route::post('wyloguj', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
