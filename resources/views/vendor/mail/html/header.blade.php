<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://oliveiracorp.com.br/img/about_img_1.png" class="logo" alt="eBooks">
<h1>titulo aqui</h1>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
