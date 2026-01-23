@extends('layouts.app', ['activePage' => 'demo-beneficiaries', 'titlePage' => __('Detalhes do Beneficiário Demo')])

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
                    <div class="card-header card-header-warning">
                        <h4 class="card-title">
                            Beneficiário Demo: {{ $beneficiary->name }}
                            <span class="badge badge-warning">DEMO</span>
                            @if($beneficiary->isDemoExpired())
                                <span class="badge badge-danger">EXPIRADO</span>
                            @else
                                <span class="badge badge-success">ATIVO</span>
                            @endif
                        </h4>
                        <p class="card-category">Informações completas da conta de demonstração</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome Completo</h5>
                                <p>{{ $beneficiary->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CPF</h5>
                                <p>{{ $beneficiary->cpf }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Email</h5>
                                <p>{{ $beneficiary->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Telefone</h5>
                                <p>{{ $beneficiary->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Nascimento</h5>
                                <p>{{ $beneficiary->birth_date ? \Carbon\Carbon::parse($beneficiary->birth_date)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Gênero</h5>
                                <p>{{ $beneficiary->gender == 'M' ? 'Masculino' : 'Feminino' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Empresa</h5>
                                <p>{{ $beneficiary->company->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Plano</h5>
                                <p>
                                    @if($beneficiary->plans->first())
                                        {{ $beneficiary->plans->first()->plan->name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Expiração do Demo</h5>
                                <p>
                                    @if($beneficiary->demo_expires_at)
                                        {{ $beneficiary->demo_expires_at->format('d/m/Y H:i') }}
                                        @php
                                            $daysRemaining = now()->diffInDays($beneficiary->demo_expires_at, false);
                                        @endphp
                                        @if($daysRemaining > 0)
                                            <span class="badge badge-info ml-2">{{ $daysRemaining }} dias restantes</span>
                                        @else
                                            <span class="badge badge-danger ml-2">Expirado</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Criação</h5>
                                <p>{{ $beneficiary->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold mb-3">Ações</h5>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.demo-beneficiary.login-as', $beneficiary->id) }}" class="btn btn-primary">
                                        <i class="material-icons">login</i> Acessar Portal
                                    </a>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#extendModal">
                                        <i class="material-icons">schedule</i> Estender Demo
                                    </button>
                                    <a href="{{ route('admin.demo-beneficiary.convert', $beneficiary->id) }}" class="btn btn-warning">
                                        <i class="material-icons">payment</i> Converter para Real
                                    </a>
                                    <a href="{{ route('admin.demo-beneficiary.index') }}" class="btn btn-secondary">
                                        <i class="material-icons">arrow_back</i> Voltar
                                    </a>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                        <i class="material-icons">delete</i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Estender -->
<div class="modal fade" id="extendModal" tabindex="-1" role="dialog">
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
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
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
@endsection

