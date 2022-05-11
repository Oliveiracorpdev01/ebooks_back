<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserChangeEmail extends Mailable implements ShouldQueue
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
            ->subject('E-mail de usuário alterado!')
            //->replyTo('adm@oliveiracorp.com.br')
            ->view('email.ChangeEmail')->with(['user'=>$this->user, 'url'=>$this->url]);
    
        }
    }
    
