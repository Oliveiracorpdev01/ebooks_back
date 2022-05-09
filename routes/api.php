<?php

use App\Http\Controllers\AbilitiesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Email_VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', [AuthController::class, 'profile']) /* ->middleware(['verified']) //verificando se o email esta verificado*/;
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/teste', [AbilitiesController::class, 'index']);

    Route::post('/email/verification-notification',
        [Email_VerificationController::class,
            'sendEmailVerificationNotification'])->middleware(['throttle:6,1']);
});

//ativando rota de verificaçãod e email

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return 'email verificado';
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('{any}', function () {
    return response()->json([
        'message' => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
