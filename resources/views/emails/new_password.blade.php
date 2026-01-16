@if($logo)
<div style="text-align:center;margin-bottom:20px;">
    <img 
        src="{{ $logo }}"
        alt="Logo"
        style="width:180px; display:block; margin:0 auto;"
    >
</div>
@endif

<h2>Recuperação de Acesso</h2>

<p>Olá, {{ $name }}.</p>

<p>
    Sua nova senha de acesso como <strong>{{ $type }}</strong> é:
</p>

<p style="font-size:18px;font-weight:bold;">
    {{ $password }}
</p>

<p>
    Recomendamos que você faça login e altere sua senha imediatamente.
</p>

<p>
    Atenciosamente,<br>
    {{ config('mail.from.name') }}
</p>
