<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use App\Mail\UserChangeEmail;

class ChangeEmailController extends Controller
{

    public function update(Request $request)
    {
        $arrValidate = array(
            'email' => 'required|min:6|max:80|email:rfc',
            'password' => 'string|min:4|max:80',
        );
        $this->validate($request, $arrValidate);

        $user = $request->user();

        $mail = User::User_Email_Equals($request->user()->id, $request['email'], );
        if (count($mail) > 0) {
            throw ValidationException::withMessages([
                'email' => ['Email já existe em nossa base de dados!'],
            ]);
        }

        //validando apenas o que esta na regra
        $requestEquals = array();
        $requestEquals['email'] = $request['email'];

        if (!$user || !Hash::check($request['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if ($request['email'] && $request['email'] != $user->email) {
            $requestEquals['email_verified_at'] = null;

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            $urlv = getenv('APP_FRONT_URL') . '' . explode('/api', $verifyUrl)[1];
            Mail::to($request['email'])
            ->cc([$user->email])
            ->queue(
                new UserChangeEmail($user, $urlv)
            );
        }

        $user->update($requestEquals);

        return response()->json(
            [],
            200
        );
    }

}
