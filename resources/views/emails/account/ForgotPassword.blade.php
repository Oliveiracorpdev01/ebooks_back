@component('mail::message')
Prezado(a) {{$user->fullName}}.<br>
Recentemente as {{date('d/m/Y H:i:s')}}, você solicitou a redefinição de senha para seu perfil na eBooks. Para concluir o processo, clique no link abaixo.<br>

@component('mail::button', ['url' => $url, 'color' => 'success'])
Alterar senha
@endcomponent

Caso não tenha feito esta alteração ou acredita que um usuário não autorizado tenha acessado sua conta, acesse eBooks para redefinir sua senha imediatamente. Em seguida, inicie sessão na página da conta de seu perfil eBooks para revisar e atualizar seus ajustes de segurança.<br>
Obrigado,<br>
{{ config('app.name') }}
@endcomponent