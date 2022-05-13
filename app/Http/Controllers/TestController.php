<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {

        return $request->image->storeAs('public', $imageName);
        //$visibility = Storage::getVisibility('file.jpg');

    }
}
