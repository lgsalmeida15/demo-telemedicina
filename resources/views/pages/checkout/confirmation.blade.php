<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Confirmado</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=check" />

    <style>
        body {
            background: #f1f5f9;
            font-family: Inter, Arial, sans-serif;
        }

        .confirmation-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            max-width: 650px;
            margin: 40px auto;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .check-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #4ade80;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            animation: pop 0.4s ease-out;
        }

        @keyframes pop {
            0% { transform: scale(0.2); }
            80% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .check-circle i {
            font-size: 45px;
            color: #fff;
        }

        .cta-btn {
            border-radius: 12px;
            padding: 14px 32px;
            font-size: 18px;
        }

        .summary-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px;
            margin-top: 25px;
        }

        .summary-box h6 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 16px;
        }
    </style>

    <script src="https://kit.fontawesome.com/a2d9d6a64f.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="confirmation-card text-center">
    
    <div class="check-circle">
        <span class="material-symbols-outlined"
        style="color: white">
            check
        </span>
    </div>

    <h2 class="font-weight-bold mb-2">Pagamento Iniciado!</h2>
    <p class="text-muted mb-4">
        Seu pedido foi registrado com sucesso. Assim que o pagamento for aprovado, você receberá um e-mail com a confirmação.
    </p>

    <div class="summary-box text-left">
        <h6>Resumo do Pedido</h6>

        <div class="summary-row">
            <span>Beneficiário:</span>
            <strong>{{ $invoice->beneficiary->name }}</strong>
        </div>

        <div class="summary-row">
            <span>Plano Selecionado:</span>
            <strong>{{ $invoice->plan->name }}</strong>
        </div>

        <div class="summary-row">
            <span>Valor:</span>
            <strong>R$ {{ number_format($invoice->plan->value, 2, ',', '.') }}</strong>
        </div>

        <div class="summary-row">
            <span>Forma de Pagamento:</span>
            <strong>{{ ($invoice->payment_type == "CREDIT_CARD")?"CARTÃO DE CRÉDITO":"CARTÃO DE DÉBITO" }}</strong>
        </div>
    </div>

    <a href="{{route('beneficiary.login')}}" class="btn btn-primary cta-btn mt-4">
        Acessar Área do Beneficiário
    </a>

</div>

</body>
</html>
