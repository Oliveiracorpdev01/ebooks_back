<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Http\Request;


class AbilitiesController extends Controller
{
    public function index(Request $request)
    {


        $notifiable = $request->user();

        /* $permissions = auth()->user();

        if (!Gate::allows('update-post', 'editar_livro')) {
            return abort(403);
        }  */

        //return $permissions;


        $frontendUrl = 'http://cool-app.com/auth/email/verify';

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
        //$urlgene = $frontendUrl . '?verify_url=' . urlencode($verifyUrl);
        dd($verifyUrl);
    }
}
