<?php

use App\Http\Controllers\TestController;

use App\Http\Controllers\Account\ProfileChangeMailController;
use App\Http\Controllers\Account\ProfileResetPassword;
use App\Http\Controllers\Account\ProfileImageController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Email\VerificationEmailController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\Auth\AuthController;


//teste
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'profile']) /* ->middleware(['verified']) //verificando se o email esta verificado*/;
    Route::patch('/profile', [ProfileController::class, 'profile_update']);
    Route::patch('/profile/email', [ProfileChangeMailController::class, 'update']);
    Route::post('/profile/image', [ProfileImageController::class, 'updateAccountImage'])->name('account.me');

    Route::get('/teste', [TestController::class, 'test']);

    Route::post('/email/verification-notification',
        [VerificationEmailController::class,
            'sendEmailVerificationNotification'])->middleware(['throttle:6,1']);
});

Route::group(['middleware' => ['guest']], function () {
Route::post('/forgot-password', [ProfileResetPassword::class, 'forgot_password']);
Route::post('/reset-password/{token}/{email}', [ProfileResetPassword::class, 'reset_password'])->name('password.reset');
Route::get('/reset-password/{token}/{email}', [ProfileResetPassword::class, 'reset_password_test'])->middleware(['signed']);

});

Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return '';
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::any('{any}', function () {
    return response()->json([
        'message' => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
