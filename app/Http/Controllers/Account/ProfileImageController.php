<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Image;

class ProfileImageController extends Controller
{
    public function updateAccountImage(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:4048',
        ]);

        $file = $request->file('avatar');

        $user = $request->user();
        $filename = substr(md5(date("YmdHis")), 1, 35);
        if ($request->user()->avatar) {
            Storage::disk('local')->delete('images/users/' . $user->id . '/' . $user->avatar);
        }
        $storagePath = storage_path('app/images/users/' . $user->id);
        if (!is_dir($storagePath)) {
            File::makeDirectory(storage_path('app/images/users/' . $user->id));
        }

        Image::make($file)->encode('webp', 90)->save(storage_path('app/images/users/' . $user->id . '/' . $filename . '.webp'));
        $user->avatar = $filename . '.webp';
        $user->update(); //salva no banco o caminho

        return $user->avatar;
    }

    public function indexAccountImage(Request $request, $id, $avatar)
    {

        $request['user_id'] = $id;
        $request['avatar'] = $avatar;

        $request->validate([
            'user_id' => 'required|integer|min:1',
            'avatar' => 'required|string|min:6|max:200',
        ]);

        $storagePath = storage_path('app/images/users/' . $id . '/' . $avatar);

        if (!file_exists($storagePath)) {
            throw ValidationException::withMessages([
                'avatar' => [trans('messages.image_exist')],
            ]);
        }

        return Image::make($storagePath)->response();
    }
}
