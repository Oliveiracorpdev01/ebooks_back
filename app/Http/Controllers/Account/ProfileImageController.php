<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    public function updateAccountImage(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $file = $request->file('avatar');
        // dd($file->getClientOriginalExtension());

        $user = $request->user();

        if ($request->user()->avatar) {

            Storage::disk('local')->delete($user->avatar);
            $user->avatar = $file->store('images/users/' . $user->id, 'local'); //caso não exista
            $user->update(); //salva no banco o caminho

        } else {
            $user->avatar = $file->store('images/users/' . $user->id, 'local'); //caso não exista
            $user->update(); //salva no banco o caminho
        }

        return response()->json(
            [],
            200
        );
    }
}
