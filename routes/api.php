<?php

use App\Http\Controllers\AbilitiesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResets;
use App\Http\Controllers\Email_VerificationController;
use App\Http\Controllers\ChangeEmailController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
//teste
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', [AuthController::class, 'profile']) /* ->middleware(['verified']) //verificando se o email esta verificado*/;
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/profile', [AuthController::class, 'profile_update']);
    Route::patch('/profile/email', [ChangeEmailController::class, 'update']);

    

    Route::post('/teste', [AbilitiesController::class, 'index']);

    Route::post('/email/verification-notification',
        [Email_VerificationController::class,
            'sendEmailVerificationNotification'])->middleware(['throttle:6,1']);
});

Route::post('/forgot-password', [PasswordResets::class, 'forgot_password'])->middleware('guest');

Route::post('/reset-password/{token}/{email}',[PasswordResets::class, 'reset_password'])->middleware(['guest'])->name('password.reset');


//apenas para gerar um link de email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return '';
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');




Route::any('{any}', function () {
    return response()->json([
        'message' => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
