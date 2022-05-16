<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
}
