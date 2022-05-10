<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
    use HasFactory;

    protected $table = 'password_resets';

    protected $fillable = ['email', 'token', 'created_at'];

    public static function Delete_PasswordReset($email)
    {
        PasswordResets::table('password_resets')->where('email','gezerramo@gmail.com')->delete();
        return ;
    }


}
