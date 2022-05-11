<?php

namespace App\Http\Controllers;

use App\Mail\UserForgotPasswordEmail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
//
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordResets extends Controller
{
    public function forgot_password(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Password::createToken($user);

        $verifyUrl = URL::signedRoute(
            'password.reset',

            [
                'token' => $token,
                'email' => $request->email,
            ]
        );
        $urlv = getenv('APP_FRONT_URL') . '' . explode('/api', $verifyUrl)[1];

        Mail::to($request->email)->queue( //queue para enviar em segundo plano e continuar o processo.
            new UserForgotPasswordEmail($user, $urlv)
        );

        return '';

    }

    public function reset_password(Request $request, $token, $email)
    {

        $request["token"] = $token;
        $request["email"] = $email;

        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:4|max:80|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $updatePassword = Password::tokenExists($user, $request['token']);

        if (!$updatePassword && !$request->hasValidSignature()) {
            abort(401);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
                Password::deleteToken($user);
                event(new PasswordReset($user));
            }
        );
        return;

    }
}
