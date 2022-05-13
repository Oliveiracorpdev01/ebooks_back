@component('mail::message')
Olá, {{$user->fullName}},<br>
Confirme seu email para ter acesso completo a nosso portal. Clique no botão para confirmar.<br>

@component('mail::button', ['url' => $url, 'color' => 'success'])
Confirmar email
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
