<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChangeEmailController;
use App\Http\Controllers\Email_VerificationController;
use App\Http\Controllers\PasswordResets;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
//teste
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', [AuthController::class, 'profile']) /* ->middleware(['verified']) //verificando se o email esta verificado*/;
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/profile', [AuthController::class, 'profile_update']);
    Route::patch('/profile/email', [ChangeEmailController::class, 'update']);

    Route::get('/teste', [TestController::class, 'test']);

    Route::post('/email/verification-notification',
        [Email_VerificationController::class,
            'sendEmailVerificationNotification'])->middleware(['throttle:6,1']);
});

Route::group(['middleware' => ['guest']], function () {
Route::post('/forgot-password', [PasswordResets::class, 'forgot_password']);
Route::post('/reset-password/{token}/{email}', [PasswordResets::class, 'reset_password'])->name('password.reset');
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return '';
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::any('{any}', function () {
    return response()->json([
        'message' => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
