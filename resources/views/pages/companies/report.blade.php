<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Detalhado de Empresas (Imprimir)</title>

    <!-- Incluir Bootstrap e Material Icons para manter o estilo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* Oculta o menu lateral e cabeçalhos para impressão */
        @media print {
            .no-print {
                display: none !important;
            }
            /* Remove margens e preenchimentos que atrapalham a impressão */
            body, .content, .container-fluid, .row, .card, .card-body, .table-responsive {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            body {
                background-color: #fff !important;
                font-size: 10px;
            }
            .card-header {
                display: none;
            }
        }
        /* Estilos gerais para a tabela de relatório */
        .table-report th, .table-report td {
            white-space: nowrap;
            font-size: 10px;
            padding: 5px 8px !important;
        }
        .table-report thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        /* Estilos do card para visualização na tela */
        .card {
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(156, 39, 176, 0.4);
        }
        .card-header-primary {
            background: linear-gradient(60deg, #08ab10, #14870e);
            color: #fff;
            padding: 15px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    
                    <!-- Cabeçalho (Visível na tela, Oculto na impressão) -->
                    <div class="card-header card-header-primary no-print">
                        <h4 class="card-title">Relatório Detalhado de Empresas</h4>
                        <p class="card-category">Estrutura de dados para impressão.</p>
                    </div>

                    <div class="card-body">
                        
                        <!-- Botão de Impressão e Voltar (Não visível na impressão) -->
                        <div class="no-print" style="width: 100%; text-align: end; margin-bottom: 1.5rem; padding: 10px 0;">
                            <!-- Adapte a rota 'company.index' conforme a sua aplicação -->
                            <a href="{{ route('company.index') }}" class="btn btn-secondary">
                                <i class="material-icons">arrow_back</i> Voltar
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="material-icons">print</i> Imprimir Página
                            </button>
                        </div>
                        
                        <!-- Tabela de Relatório -->
                        <div class="table-responsive">
                            <table class="table table-report table-bordered">
                                <thead class="text-primary">
                                    <tr>
                                        <th>EMPRESA</th>
                                        <th>UF</th>
                                        <th>PARCERIA</th>
                                        <th>DT FATURAMENTO</th>
                                        <th>VENCIMENTO</th>
                                        <th>NF</th>
                                        <th>QT</th>
                                        <th>V.UNIT</th>
                                        <th>VALOR FATURA</th>
                                        <th>PROC</th>
                                        <th>BOLETO</th>
                                        <th>E-MAIL</th>
                                        <th>SEGURO</th>
                                        <th>CLICKLIFE</th>
                                        <th>TEM SAÚDE</th>
                                        <th>ODONTO</th>
                                        <th>TELEPSICOLOGIA</th>
                                        <th>APP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($plans as $plan)

                                        @php

                                            $unitValue = $plan->value;
                                            $beneficiariesTotal = $plan->company->beneficiaries->count();

                                            $total = $unitValue * $beneficiariesTotal; // valor total da fatura

                                        @endphp
                                        
                                        <tr>
                                            <td>{{ $plan->company->name ?? 'N/A' }}</td> {{-- <th>EMPRESA</th> --}}
                                            <td>{{ $plan->company->uf ?? 'N/A' }}</td>  {{-- UF --}}
                                            <td>{{ $plan->company->indications->count() }}</td> {{-- PARCERIA --}}
                                            <td>{{ \Carbon\Carbon::parse($plan->company->billing_date)->format('d/m/Y') ?? 'N/A' }}</td> {{-- DT FATURAMENTO --}}
                                            <td>Todo dia {{ $plan->company->due_day ?? 'N/A' }}</td> {{-- VENCIMENTO --}}
                                            
                                            <td>{{ $plan->nf ?? 'N/A' }}</td> {{-- NF --}}

                                            <td>{{ $plan->company->beneficiaries->count() ?? 0 }}</td> {{-- QT --}}
                                            
                                            <!-- V.UNIT: Acesso corrigido (priorizando o Plano) -->
                                            <td>R$ {{ number_format($plan->value, 2, ',', '.') }}</td> {{-- V. UNIT --}}
                                            
                                            <!-- VALOR FATURA: Corrigido o cálculo fallback -->
                                            <td>R$ {{ number_format($total, 2, ',', '.') }}</td> {{-- VALOR FATURA --}}
                                            
                                            <td>{{ $plan->company->proc ?? '-' }}</td>
                                            <td>{{ $plan->company->boleto ?? '-' }}</td>
                                            <td>{{ $plan->company->email ?? '-' }}</td>
                                            
                                            <td>{{ $plan->has_seguro ?? 'N/A' }}</td>
                                            <td>{{ $plan->has_clicklife ?? 'N' }}</td>
                                            <td>{{ $plan->has_tem_saude ?? 'N' }}</td>
                                            <td>{{ $plan->has_odonto ?? 'N' }}</td>
                                            <td>{{ $plan->has_telepsicologia ?? 'N' }}</td>
                                            <td>{{ $plan->has_app ?? 'N' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="17" class="text-center">Nenhuma empresa encontrada para o relatório.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Fim da Tabela de Relatório -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
