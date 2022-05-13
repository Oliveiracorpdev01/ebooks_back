@component('mail::message')
Olá, {{$user->fullName}},<br>seu cadastro foi efetuado com sucesso em nosso site, {{date('d/m/Y H:i:s')}}.<br>
Confirme seu email para ter acesso completo a nosso portal.<br>

@component('mail::button', ['url' => $url, 'color' => 'violet'])
Confirmar email
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
