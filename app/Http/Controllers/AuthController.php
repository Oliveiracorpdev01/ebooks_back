<?php

namespace App\Http\Controllers;

use App\Mail\UserRegisteredEmail;
use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'fullName' => 'required| regex:/\s/ | string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'device_name' => 'required',
        ]);
        $username = explode(' ', $validatedData['fullName'])[0];
        $user = User::create([
            'fullName' => $validatedData['fullName'],
            'email' => $validatedData['email'],
            'username' => $username,
            'password' => Hash::make($validatedData['password']),
        ]);
        UsersRole::create([
            'user_id' => $user->id,
            'role_id' => 5,
        ]);

        $user->roles = UsersRole::innerjoinUsersPermissions($user->id);

        Mail::to($validatedData['email'])->send(
            new UserRegisteredEmail($user)
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
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user->roles = UsersRole::innerjoinUsersPermissions($user->id);

        $user->createToken($request->device_name);

        return response()->json([
            'users' => $user,
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'token_type' => 'bearer',
        ]);

    }

    public function profile(Request $request)
    {
        /* $post = '';
        if( Gate::denies('update-post', $post) )
        abort(403, 'Unauthorized'); */

        $device_name = $request->user()->currentAccessToken()->name;
        $request->user()->currentAccessToken()->delete();

        $user = $request->user();

        $user->roles = UsersRole::innerjoinUsersPermissions($user->id);

        if (!Gate::allows('update-post', 'menu_profile')) {
            return abort(403);
        }

        return response()->json([
            'users' => $user,
            'token' => $user->createToken($device_name)->plainTextToken,
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
