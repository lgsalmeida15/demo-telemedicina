@if($logo)
<div style="text-align:center;margin-bottom:20px;">
    <img 
        src="{{ $logo }}"
        alt="Logo"
        style="width:180px; display:block; margin:0 auto;"
    >
</div>
@endif

<h2>Cancelamento de Plano</h2>

<p>Olá, {{ $name }}.</p>

<p>
    Informamos que o cancelamento do seu plano foi feito com <strong>sucesso</strong>.
</p>

<p>
    Em caso de dúvidas, entre em contato com a equipe da BoxFarma.
</p>

<p>
    Atenciosamente,<br>
    {{ config('mail.from.name') }}
</p>
