<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserAccesEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->subject('Acabou de fazer o login em sua conta eBooks!');
        $this->with(['user' => $this->user]);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this            
            ->markdown('emails.login.AlertAcces');
    }
}
