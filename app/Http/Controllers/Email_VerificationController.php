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

        $frontendUrl = 'http://cool-app.com/auth/email/verify';

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
        //$urlgene = $frontendUrl . '?verify_url=' . urlencode($verifyUrl);

        Mail::to($user['email'])->send(
            new UserVerificationEmail($user, $verifyUrl)
        );

        return response()->json([
            'message' => 'ok'
        ]);

    }
}
