@if($logo)
<div style="text-align:center;margin-bottom:20px;">
    <img 
        src="{{ $logo }}"
        alt="Logo"
        style="width:180px; display:block; margin:0 auto;"
    >
</div>
@endif

<h2>Acesso ao Sistema</h2>

<p>Olá, {{ $name }}.</p>

<p>Seus dados de acesso são:</p>

<p><strong>E-mail:</strong> {{ $email }}</p>
<p><strong>Senha:</strong> {{ $password }}</p>

<p style="margin-top:15px;">
    <a href="{{ $loginUrl }}"
       style="background:#2563eb;color:#fff;padding:10px 15px;
              text-decoration:none;border-radius:5px;">
        Acessar sistema
    </a>
</p>

<p style="margin-top:20px;">
    Recomendamos que você altere sua senha após o primeiro acesso.
</p>

<p>
    Atenciosamente,<br>
    {{ config('mail.from.name') }}
</p>
