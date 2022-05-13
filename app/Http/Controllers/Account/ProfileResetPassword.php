<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\UserForgotPassword;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileResetPassword extends Controller
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
            new UserForgotPassword($user, $urlv)
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

        if (!$updatePassword || !$request->hasValidSignature()) {
            throw ValidationException::withMessages([
                'signature' => [trans('messages.signature_invalid')],
            ]);
        }

        if (!$user || Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => [trans('messages.password_last_equals')],
            ]);
        }
       
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
        
                $user->password = Hash::make($password);
                $user->save();
                Password::deleteToken($user);
                event(new PasswordReset($user));
            }
        );
        return;

    }

    public function reset_password_test(Request $request, $token, $email)
    {

        $request["token"] = $token;
        $request["email"] = $email;

        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();
        $updatePassword = Password::tokenExists($user, $request['token']);

        if (!$user || !$updatePassword || !$request->hasValidSignature()) {
            throw ValidationException::withMessages([
                'signature' => [trans('messages.signature_invalid')],
            ]);
        }
      
        return;

    }
}
