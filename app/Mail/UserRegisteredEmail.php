<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredEmail extends Mailable implements ShouldQueue
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
            $name = ucwords(strtok($this->user->fullName, " "));
        }

        return $this
            ->subject('' . $name . ', vamos confirmar seu e-mail de cadastro?')
            ->view('email.Registered')->with(['user' => $this->user, 'url' => $this->url]);
    }
}
