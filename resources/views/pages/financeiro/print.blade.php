<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro</title>
    <style>
        :root {
            --green: #2e7d32;
            --red: #c62828;
            --blue: #1565c0;
            --gray: #555;
            --bg: #fff;
            --border: #cfd8dc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Roboto", Arial, Helvetica, sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--gray);
            padding: 28px;
        }

        /* ---------- HEADER ---------- */
        header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
            border-bottom: 3px solid var(--green);
            padding-bottom: 12px;
        }

        header img {
            height: 56px;
        }

        header h1 {
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--green);
            line-height: 1.2;
        }

        header .meta {
            margin-left: auto;
            text-align: right;
            font-size: .9rem;
            line-height: 1.3;
            color: #777;
        }

        /* ---------- TABLE ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }

        thead th {
            background: #f1f8e9;
            color: var(--green);
            font-weight: 600;
            padding: 8px;
            border: 1px solid var(--border);
        }

        tbody td {
            padding: 7px 8px;
            border: 1px solid var(--border);
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        td:last-child,
        th:last-child {
            text-align: right;
        }

        td:nth-child(5) {
            text-align: right;
        }

        td:nth-child(4) {
            text-transform: capitalize;
        }

        /* ---------- FOOTER TOTALS ---------- */
        footer {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            gap: 48px;
            font-size: .95rem;
        }

        .total-box {
            min-width: 150px;
            text-align: center;
            padding: 8px 0;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        .total-box h4 {
            margin-bottom: 2px;
            font-weight: 500;
            font-size: 1rem;
        }

        .total-box.entrada{
            background: color-mix(in srgb, var(--green) 10%, white);
        }
        .total-box.saida{
            background: color-mix(in srgb, var(--red) 10%, white);
        }
        .total-box.saldo{
            background: color-mix(in srgb, var(--blue) 10%, white);
        }

        .in {
            color: var(--green);
        }

        .out {
            color: var(--red);
        }

        .bal {
            color: var(--blue);
        }

        /* ---------- PRINT ---------- */
        @media print {
            @page {
                size: landscape;
                margin: 12mm;
            }

            body {
                padding: 0;
            }

            header {
                margin-bottom: 16px;
            }

            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                gap: 0;
                justify-content: space-around;
            }
        }
    </style>
</head>

<body>
    <header>
        <img src="{{ asset('material') }}/img/logo.png" alt="Logo">
        <div>
            <h1>Relatório de Lançamentos</h1>
            <small>{{ $periodo['inicio'] ?? 'Início' }} &rarr; {{ $periodo['fim'] ?? 'Fim' }}</small><br>
            <small>Centro: {{ $centroSelecionado }}</small>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Descrição</th>
                <th>Centro de Custo</th>
                <th>Tipo</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lancamentos as $l)
                <tr @if($l->tipo == "entrada") class="in" @else class="out" @endif>
                    <td>{{ $l->data_hora_evento->format('d/m/Y H:i') }}</td>
                    <td>{{ $l->descricao }}</td>
                    <td>{{ $l->costCenter->descricao }}</td>
                    <td>{{ ucfirst($l->tipo) }}</td>
                    <td>@if($l->tipo == "entrada") + @else - @endif {{ number_format($l->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        <div class="total-box entrada">
            <h4 class="in">Entradas</h4>
            <strong class="in">R$ {{ number_format($entradas, 2, ',', '.') }}</strong>
        </div>
        <div class="total-box saida">
            <h4 class="out">Saídas</h4>
            <strong class="out">R$ {{ number_format($saidas, 2, ',', '.') }}</strong>
        </div>
        <div class="total-box saldo">
            <h4 class="bal">Saldo</h4>
            <strong class="bal">R$ {{ number_format($saldo, 2, ',', '.') }}</strong>
        </div>
    </footer>

    <script>
        window.print();
    </script>
</body>

</html>
