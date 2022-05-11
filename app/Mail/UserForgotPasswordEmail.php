<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserForgotPasswordEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $user;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $name = $this->user->fullName;

        if (preg_match('/\s/', $name)) { //teste se contém espaço
            $upperCase = ucfirst($name);
            $name = explode(' ', $upperCase)[0];
        }
        return $this
            ->subject('Olá ' . $name . '! Como redefinir a senha de seu Ebooks?')
            ->view('email.ForgotPassword')->with(['user' => $this->user, 'url' => $this->url]);
    }
}
