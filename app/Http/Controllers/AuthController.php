<?php

namespace App\Http\Controllers;

use App\Mail\UserLoginEmail;
use App\Mail\UserRegisteredEmail;
use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
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
            'fullName' => 'required|string|min:6|max:255',
            'username' => 'required|string|min:4|max:255|unique:users',
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

        Mail::to($request->email)->queue( //queue para enviar em segundo plano e continuar o processo.
            new UserLoginEmail($user->fullName)
        );

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

    public function profile_update(Request $request)
    {

        $arrValidate = array(
            'fullName' => 'string|regex:/\s/|min:6|max:255',
            'email' => 'string|email:rfc,dns|max:255',
            'username' => 'string|min:3|max:255|unique:users',
            'current_password' => 'string|min:4|max:80',
            'new_password' => 'string|min:4|max:80',
            'phone_number' => 'integer|digits_between:6,20',
        );
        $this->validate($request, $arrValidate);

        $user = $request->user();

        //validando apenas o que esta na regra
        $requestEquals = array();
        foreach ($request->all() as $input => $value) {
            if (array_key_exists($input, $arrValidate)) {
                $requestEquals[$input] = $value;
            }
        }

        if ($request['current_password']) {

            if (!$user || !Hash::check($request['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => [trans('messages.current_password')],
                ]);
            }
            if (!$user || Hash::check($request->new_password, $user->password)) {
                throw ValidationException::withMessages([
                    'credentials' => [trans('messages.password_last_equals')],
                ]);
            }
            $requestEquals['password'] = Hash::make($request['new_password']);
        }

        $user->update($requestEquals);

        return response()->json(
            [],
            200
        );

    }

}
