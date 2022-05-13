<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserAccesEmail;
use App\Mail\UserRegisteredEmail;
use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'terms' => 'required|boolean|in:1',
            'fullName' => 'required|string|min:6|max:100',
            'username' => 'required|string|min:4|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4|max:80',
            'device_name' => 'required',

        ]);

        //$username = explode(' ', $validatedData['fullName'])[0];
        $user = User::create([
            'fullName' => ucwords($validatedData['fullName']),
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
        ]);
        UsersRole::create([
            'user_id' => $user->id,
            'role_id' => 5,
        ]);

        $user->roles = UsersRole::innerjoinUsersPermissions($user->id);

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $urlv = getenv('APP_FRONT_URL') . '' . explode('/api', $verifyUrl)[1];

        Mail::to($validatedData['email'])->queue(
            new UserRegisteredEmail($user, $urlv)
        );

        return response()->json([
            'users' => $user,
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'token_type' => 'bearer',
        ]);

    }

    public function login(Request $request)
    {
        // dd($request);
        $request->validate([
            'email' => 'required|min:6|max:80|email:rfc',
            'password' => 'required|min:4|max:80',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => [trans('messages.credentials')],
            ]);
        }

        $user->roles = UsersRole::innerjoinUsersPermissions($user->id);

        $user->createToken($request->device_name);

        Mail::to($request->email)
            ->queue( //queue para enviar em segundo plano e continuar o processo.
                new UserAccesEmail($user)
            );

        return response()->json([
            'users' => $user,
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'token_type' => 'bearer',
        ]);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked',
        ];
    }
}
