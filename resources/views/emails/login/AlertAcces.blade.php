@component('mail::message')
Olá {{$user->fullName}}, seu login acaba de ser efetuado em nosso site, {{date('d/m/Y H:i:s')}}.
Se você não fez login e acredita se tratar de um erro, acesse seu perfil e altere sua senha para mais segurança, ou  você poderá entrar em contato com nossa equipe de suporte.<br>

Obrigado,<br>
{{ config('app.name') }}
@endcomponent