<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangeEmailController extends Controller
{
  
    public function update(Request $request, $id)
    {
        $arrValidate = array(
            'fullName' => 'string|regex:/\s/|min:6|max:255',
            'username' => 'string|min:3|max:255|unique:users',
            'current_password' => 'string|min:4|max:80',
            'new_password' => 'string|min:4|max:80',
            'phone_number'=>'number|min:6|max:20', 
        );
        $this->validate($request, $arrValidate);

        $user = $request->user();

        $mail = User::User_Email_Equals($request->user()->id, $request['email'], );
        if (count($mail) > 0) {
            return abort(409, 'Email já existe em nossa base de dados!');
        }

        //validando apenas o que esta na regra
        $requestEquals = array();
        foreach ($request->all() as $input => $value) {
            if (array_key_exists($input, $arrValidate)) {
                $requestEquals[$input] = $value;
            }
        }

        if ($request['current_password']) {
            
            if (!$user || !Hash::check($request['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'login' => ['As credenciais fornecidas estão incorretas.'],
                ]);
            }
             $requestEquals['password'] = Hash::make($request['new_password']);
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

            Mail::to($request['email'])->queue(
                new UserVerificationEmail($user, $verifyUrl)
            );
        }

        $user->update($requestEquals);

        return response()->json(
            [],
            200
        );
    }

  
}
