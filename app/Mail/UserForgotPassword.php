<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserForgotPassword extends Mailable implements ShouldQueue
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
        $this->subject('Como redefinir a senha de seu Ebooks?');
        $this->with(['user' => $this->user, 'url' => $this->url]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->markdown('emails.account.ForgotPassword');
    }
}
