<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;


class TestController extends Controller
{
    public function test(Request $request)
    {
       

        throw ValidationException::withMessages([
            'locale' => [trans('messages.locale')],
        ]);
      

     
        dd('passed');

    }
}
