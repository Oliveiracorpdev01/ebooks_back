<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Image;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // $file = $request->file('avatar');

        $user = $request->user();

        // Storage::disk('local')->delete('images/users/' . $user->id . '/' . $user->avatar);
        //$file_name = $file->store('images/teste/', 'local'); //caso não exista

        $classifiedImg = $request->file('avatar');
        $filename = $classifiedImg->getClientOriginalExtension();

        // Intervention
        $image = Image::make($classifiedImg)->encode('webp', 90)->save(public_path('uploads/' . $filename . '.webp'));

        dd($image);

        return $user->avatar;
    }
}
