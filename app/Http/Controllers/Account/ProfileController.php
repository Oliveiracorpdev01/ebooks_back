<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
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
                    'new_password' => [trans('messages.password_last_equals')],
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
