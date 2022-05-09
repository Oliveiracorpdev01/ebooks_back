<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserVerificationEmail extends Mailable
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
        return $this
        ->subject('Verificar endereço de e-mail!')
        //->replyTo('adm@oliveiracorp.com.br')
        ->view('email.Verification')->with(['user'=>$this->user, 'url'=>$this->url]);

    }
}
