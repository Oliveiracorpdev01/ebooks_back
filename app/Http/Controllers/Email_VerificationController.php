<?php

namespace App\Http\Controllers;

use App\Mail\UserVerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class Email_VerificationController extends Controller
{
    public function sendEmailVerificationNotification(Request $request)
    {

        $user = $request->user();

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $verifyUrl = getenv('APP_FRONT_URL') . '' . explode('/api', $verifyUrl)[1];

        Mail::to($user['email'])->queue(
            new UserVerificationEmail($user, $verifyUrl)
        );

        return response()->json();

    }
}
