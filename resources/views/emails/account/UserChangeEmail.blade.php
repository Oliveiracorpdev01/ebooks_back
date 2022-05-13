@component('mail::message')
Olá, {{$user->fullName}},<br>
O endereço de e-mail associado à sua conta foi recentemente {{date('d/m/Y H:i:s')}} alterado para {{$user->email}}.<br>
Se você não fez essa alteração e acredita se tratar de um erro, acesse nossa central de ajuda, onde você poderá entrar em contato com nossa equipe de suporte.<br>
Se você é um cliente de eBooks.com e acredita que essa alteração é um erro,consulte este artigo de suporte. Caso contrario confirme o email clicando no botão.<br>

@component('mail::button', ['url' => $url, 'color' => 'violet'])
Confirmar email
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
