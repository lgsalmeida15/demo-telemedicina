@extends('layouts.app', ['activePage' => 'financeiro', 'titlePage' => __('Lançamentos')])

@push('css')
    <style>
        /* ---- Ajustes visuais rápidos ---- */
        .card-stats .card-header-icon {
            height: 80px;
            width: 80px;
            border-radius: 50%;
            margin: -40px auto 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header-success {
            background: linear-gradient(60deg, #66bb6a, #43a047) !important;
        }

        .card-header-danger {
            background: linear-gradient(60deg, #ef5350, #e53935) !important;
        }

        .card-header-info {
            background: linear-gradient(60deg, #26c6da, #00acc1) !important;
        }

        .card-filter .form-group {
            margin-top: 0;
        }

        .table-hover tbody tr:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        /* deixa o selectpicker da mesma altura do input */
        .bootstrap-select>.dropdown-toggle {
            height: 36px;
            /* mesma altura do input */
            padding-top: 6px;
            /* ajuste vertical */
            padding-bottom: 6px;
            line-height: 22px;
            /* centraliza o texto */
        }

        /* opcional: faz a seta ficar mais próxima da borda inferior, como no input-date */
        .bootstrap-select .dropdown-toggle::after {
            margin-top: -2px;
        }
    </style>
    <style>
        /* ---------- DATATABLES NOVO LAYOUT ---------- */
        /* cabeçalho leve e texto verde */
        .table-fin th {
            background: #fafafa;
            color: #2e7d32;
            font-weight: 600;
            border: 0
        }

        .table-fin td {
            vertical-align: middle;
            border-top: 1px solid #f0f0f0
        }

        .table-fin tbody tr:hover {
            background: #f6f9fc
        }

        /* wrapper: remove espaçamentos loucos */
        .dataTables_wrapper .row {
            margin: 0
        }

        /* length + search em linha */
        .dataTables_length label,
        .dataTables_filter label {
            font-weight: 500;
            margin-bottom: 0;
            display: flex;
            align-items: center
        }

        .dataTables_length select {
            width: auto;
            margin: 0 .25rem;
        }

        .dataTables_filter input {
            width: 200px;
            margin-left: .25rem;
        }

        /* paginação minimalista */
        .dataTables_paginate .paginate_button {
            border: 0;
            border-radius: .25rem !important;
            background: #e0e0e0 !important;
            color: #424242 !important;
            padding: .25rem .75rem;
            margin: 0 .15rem;
            font-size: .875rem
        }

        .dataTables_paginate .paginate_button.current {
            background: #2e7d32 !important;
            color: #fff !important;
            font-weight: 600
        }

        .page-item.active .page-link{
           background: unset;
        }

        .page-item.active {
            background: var(--primary-border) !important;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- Filtro -->
            <div class="card card-filter mb-4 shadow-sm">
                <div class="card-body">
                    <form id="filtroForm" method="GET" action="{{ route('financial.index') }}" class="row align-items-end">
                        {{-- Data Início --}}
                        <div class="col-md-3 d-flex flex-column mb-2">
                            <label for="filtroDataInicio" class="small font-weight-bold mb-0">Data Início</label>
                            <input type="date" id="filtroDataInicio" name="data_inicio"
                                value="{{ request('data_inicio') ?? date('Y-m-d') }}" class="form-control">
                        </div>

                        {{-- Data Fim --}}
                        <div class="col-md-3 d-flex flex-column mb-2">
                            <label for="filtroDataFim" class="small font-weight-bold mb-0">Data Fim</label>
                            <input type="date" id="filtroDataFim" name="data_fim"
                                value="{{ request('data_fim') ?? date('Y-m-d') }}" class="form-control">
                        </div>

                        {{-- Centro de Custo --}}
                        <div class="col-md-4 d-flex flex-column mb-2">
                            <label for="filtroCentroCusto" class="small font-weight-bold mb-0">Centro de Custo</label>
                            <select id="filtroCentroCusto" name="centro_custo_id" class="form-control">
                                <option value="">Todos</option>
                                @foreach ($centrosDeCusto as $centro)
                                    <option value="{{ $centro->id }}"
                                        {{ (string) $centro->id === ($filtro['centro_custo_id'] ?? '') ? 'selected' : '' }}>
                                        {{ $centro->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botão Filtrar --}}
                        <div class="col-md-2 text-right">
                            <button id="btnFiltrar" type="submit" class="btn btn-success btn-block">
                                <i class="material-icons mr-1" style="font-size:18px;">filter_list</i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Totais -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card card-stats shadow-sm">
                        <div class="card-header card-header-icon card-header-success">
                            <i class="material-icons">south_east</i>
                        </div>
                        <div class="card-body text-center">
                            <p class="card-category mb-0">Total Entradas</p>
                            <h4 class="card-title text-success" id="totalEntradas">R$ 0,00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-stats shadow-sm">
                        <div class="card-header card-header-icon card-header-danger">
                            <i class="material-icons">north_west</i>
                        </div>
                        <div class="card-body text-center">
                            <p class="card-category mb-0">Total Saídas</p>
                            <h4 class="card-title text-danger" id="totalSaidas">R$ 0,00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card card-stats shadow-sm">
                        <div class="card-header card-header-icon card-header-info">
                            <i class="material-icons">payments</i>
                        </div>
                        <div class="card-body text-center">
                            <p class="card-category mb-0">Saldo</p>
                            <h4 class="card-title" id="saldoLiquido">R$ 0,00</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão adicionar -->
            <div class="row mb-2">
                <div class="col-12 text-right">
                    @php
                        $query = http_build_query([
                            'data_inicio'     => request('data_inicio') ?? date('Y-m-d'),
                            'data_fim'        => request('data_fim') ?? date('Y-m-d'),
                            'centro_custo_id' => request('centro_custo_id'),
                        ]);
                    @endphp

                    <a href="{{ route('financial.print').'?'.$query }}"
                        target="_blank"
                        class="btn btn-primary btn-round">
                        <i class="material-icons">print</i> Imprimir Lançamentos
                    </a>
                    <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#addFinanceModal">
                        <i class="material-icons">add</i> Novo Lançamento
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="card shadow-sm">
                <div class="card-header card-header-primary">
                    <h4 class="card-title mb-0">Movimentações Financeiras</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="financeiro-table" class="table table-hover align-middle mb-0">
                            <thead class="text-primary">
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Descrição</th>
                                    <th>Centro de Custo</th>
                                    <th>Tipo</th>
                                    <th class="text-right">Valor (R$)</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lancamentos as $lancamento)
                                    <tr data-id="{{ $lancamento->id }}">
                                        <td>{{ $lancamento->data_hora_evento->format('d/m/Y H:i') }}</td>
                                        <td>{{ $lancamento->descricao }}</td>
                                        <td>{{ $lancamento->costCenter->descricao ?? "Sem Descrição" }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $lancamento->tipo === 'entrada' ? 'success' : 'danger' }}">
                                                {{ ucfirst($lancamento->tipo) }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($lancamento->valor, 2, ',', '.') }}</td>
                                        <td class="td-actions text-center">
                                            <button type="button" rel="tooltip" class="btn btn-info btn-sm btnEdit"
                                                data-toggle="modal" data-target="#editFinanceModal">
                                                <i class="material-icons">edit</i>
                                            </button>
                                            <button type="button" rel="tooltip" class="btn btn-danger btn-sm btnDelete">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modais (Adicionar e Editar) -->
            @include('pages.financeiro.modals')

        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            /* --- DataTable --- */
            const tabela = $('#financeiro-table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                /* coloca length (esquerda) + search (direita) na mesma linha */
                dom: "<'row align-items-center mb-3'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                    "<'table-responsive't>" +
                    "<'row align-items-center mt-3'<'col-sm-6'i><'col-sm-6'p>>",

                /* remove listras padrão e usa hover custom */
                stripeClasses: []
            });

            /* --- Filtro --- */
            // $('#btnFiltrar').on('click', function() {
            //     tabela.draw();
            // });

            // Função personalizada de filtragem
            // $.fn.dataTable.ext.search.push(function(settings, data, index) {
            //     const inicio = $('#filtroDataInicio').val();
            //     const fim = $('#filtroDataFim').val();
            //     const centro = $('#filtroCentroCusto').val();
            //     const dataLinha = data[0];
            //     const centroLinha = tabela.row(index).data()[2];

            //     if (inicio) {
            //         const dataInicio = new Date(inicio);
            //         const [diaI, mesI, anoI] = dataLinha.split(' ')[0].split('/');
            //         const dataComp = new Date(`${anoI}-${mesI}-${diaI}`);
            //         if (dataComp < dataInicio) return false;
            //     }
            //     if (fim) {
            //         const dataFim = new Date(fim);
            //         const [diaF, mesF, anoF] = dataLinha.split(' ')[0].split('/');
            //         const dataComp = new Date(`${anoF}-${mesF}-${diaF}`);
            //         if (dataComp > dataFim) return false;
            //     }
            //     if (centro && centroLinha.indexOf(centro) === -1) return false;
            //     return true;
            // });

            /* --- Totais --- */
            function atualizarTotais() {
                let entradas = 0,
                    saidas = 0;
                tabela.rows({
                    filter: 'applied'
                }).every(function() {
                    const row = $(this.node());
                    const tipo = row.find('span.badge').text().trim().toLowerCase();
                    const valor = parseFloat(row.find('td:eq(4)').text().replace('.', '').replace(',',
                        '.'));
                    if (tipo === 'entrada') entradas += valor;
                    else saidas += valor;
                });
                $('#totalEntradas').text('R$ ' + entradas.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2
                }));
                $('#totalSaidas').text('R$ ' + saidas.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2
                }));
                const saldo = entradas - saidas;
                $('#saldoLiquido').text('R$ ' + saldo.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    }))
                    .toggleClass('text-success', saldo >= 0)
                    .toggleClass('text-danger', saldo < 0);
            }
            tabela.on('draw', atualizarTotais);
            atualizarTotais();

            /* --- Editar: preencher modal --- */
            $('#financeiro-table').on('click', '.btnEdit', function() {
                const row = $(this).closest('tr');
                const id = row.data('id');
                const dataHora = row.find('td:eq(0)').text();
                const descricao = row.find('td:eq(1)').text();
                const centro = row.find('td:eq(2)').text();
                const tipo = row.find('span.badge').text().trim().toLowerCase();
                const valor = row.find('td:eq(4)').text().replace('.', '').replace(',', '.');

                $('#editTipoGroup input[value="' + tipo + '"]')
                    .prop('checked', true) // marca o radio
                    .parent().addClass('active') // pinta o botão
                    .siblings().removeClass('active');
                const parts = dataHora.split(' ');
                const dataParts = parts[0].split('/');
                const hora = parts[1];
                $('#editDataHora').val(`${dataParts[2]}-${dataParts[1]}-${dataParts[0]}T${hora}`);
                $('#editDescricao').val(descricao);
                const idCentro = $('#editCentroCusto option')
                    .filter(function() {
                        return $(this).text() === centro;
                    })
                    .val();
                if (idCentro) {
                    $('#editCentroCusto').val(idCentro).trigger('change'); // Select2 atualiza
                }


                $('#editTipo').val(tipo);
                $('#editValor').val(valor);

                $('#formEditFinance').attr('action', ('{{ route('financial.destroy', '#') }}').replace('#',
                    id));
            });

            /* --- Delete --- */
            $('#financeiro-table').on('click', '.btnDelete', function() {
                const id = $(this).closest('tr').data('id');
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Este lançamento será removido de forma permanente!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53935',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, apagar!'
                }).then((result) => {
                    // console.log(result);
                    if (result.value) {
                        $.ajax({
                                url: ('{{ route('financial.destroy', '#') }}').replace('#', id),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                }
                            }).done(function() {
                                Swal.fire('Excluído!', 'Lançamento removido com sucesso.',
                                        'success')
                                    .then(() => location.reload());
                            })
                            .fail(function(jqXHR) {
                                Swal.fire('Erro', 'Não foi possível excluir o lançamento.',
                                    'error');
                                console.error(jqXHR.responseText);
                            });
                    }
                });
            });
        });
    </script>
@endpush
