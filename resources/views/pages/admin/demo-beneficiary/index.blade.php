@extends('layouts.app', ['activePage' => 'demo-beneficiaries', 'titlePage' => __('Beneficiários Demo')])

@section('content')
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Beneficiários Demo</h4>
                            <p class="card-category">Gerenciar contas de demonstração</p>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <a href="{{ route('admin.demo-beneficiary.create') }}" class="btn btn-primary">
                                        <i class="material-icons">add_box</i> Criar Novo Demo
                                    </a>
                                </div>
                                
                                @if($demoBeneficiaries->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Nenhum Beneficiário Demo Cadastrado
                                    </div>
                                @else
                                    <table class="table" id="demo-beneficiaries-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>CPF</th>
                                                <th>Status</th>
                                                <th>Dias Restantes</th>
                                                <th>Expira em</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($demoBeneficiaries as $beneficiary)
                                                <tr>
                                                    <td>{{ $beneficiary->id }}</td>
                                                    <td>
                                                        {{ $beneficiary->name }}
                                                        <span class="badge badge-warning">DEMO</span>
                                                    </td>
                                                    <td>{{ $beneficiary->email }}</td>
                                                    <td>{{ $beneficiary->cpf }}</td>
                                                    <td>
                                                        @if($beneficiary->isDemoExpired())
                                                            <span class="badge badge-danger">EXPIRADO</span>
                                                        @else
                                                            <span class="badge badge-success">ATIVO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($beneficiary->demo_expires_at)
                                                            @php
                                                                $daysRemaining = now()->diffInDays($beneficiary->demo_expires_at, false);
                                                            @endphp
                                                            @if($daysRemaining > 0)
                                                                <span class="badge badge-info">{{ $daysRemaining }} dias</span>
                                                            @else
                                                                <span class="badge badge-danger">Expirado</span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($beneficiary->demo_expires_at)
                                                            {{ $beneficiary->demo_expires_at->format('d/m/Y') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $beneficiary->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $beneficiary->id }}">
                                                                <a class="dropdown-item" href="{{ route('admin.demo-beneficiary.show', $beneficiary->id) }}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{ route('admin.demo-beneficiary.login-as', $beneficiary->id) }}">Acessar Portal</a>
                                                                <button class="dropdown-item" type="button" data-toggle="modal" data-target="#extendModal{{ $beneficiary->id }}">Estender Demo</button>
                                                                <a class="dropdown-item" href="{{ route('admin.demo-beneficiary.convert', $beneficiary->id) }}">Converter para Real</a>
                                                                <div class="dropdown-divider"></div>
                                                                <button class="dropdown-item text-danger" type="button" data-toggle="modal" data-target="#deleteModal{{ $beneficiary->id }}">Remover</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal Estender -->
                                                <div class="modal fade" id="extendModal{{ $beneficiary->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('admin.demo-beneficiary.extend', $beneficiary->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Estender Período Demo</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Quantos dias deseja adicionar?</label>
                                                                        <input type="number" name="days" class="form-control" min="1" max="365" value="30" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary">Estender</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Deletar -->
                                                <div class="modal fade" id="deleteModal{{ $beneficiary->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('admin.demo-beneficiary.destroy', $beneficiary->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Confirmar Remoção</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja remover o beneficiário demo <strong>{{ $beneficiary->name }}</strong>?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-danger">Remover</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    
                                    <div class="d-flex justify-content-center">
                                        {{ $demoBeneficiaries->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#demo-beneficiaries-table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "desc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: true
            });
        });
    </script>
@endpush

