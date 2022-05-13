<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class TestController extends Controller
{
    public function test(Request $request, $avatar)
    {

        //$storagePath = storage_path('public/'.$request->user()->avatar);
        //dd($storagePath);

        $storagePath = storage_path('app/'.$request->user()->avatar);


        return Image::make($storagePath)->response();


       // return Storage::getVisibility($request->user()->avatar);

        //$visibility = Storage::getVisibility('file.jpg');

    }
}
