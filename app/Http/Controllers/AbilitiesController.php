<?php

namespace App\Http\Controllers;

use App\Models\PasswordResets;
use App\Models\User;

//teste

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AbilitiesController extends Controller
{
    public function forgot_password(Request $request)
    {

        //$user = $request->user();

        /* $permissions = auth()->user();

        if (!Gate::allows('update-post', 'editar_livro')) {
        return abort(403);
        }  */
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Password::createToken($user);

        dd(Password::tokenExists($user, $token));

        
        PasswordResets::Delete_PasswordReset();

        dd(url('reset', $token));

    }
}
