@extends('layouts.app', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('content')
<style>
    .card-stats {
        transition: all 0.3s ease-in-out;
        border-radius: 15px;
        cursor: pointer;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .card-icon i {
        font-size: 48px;
        color: #fff;
    }
    .card-category {
        font-weight: 500;
        color: rgba(0, 0, 0, 0.6);
    }
    .card-title {
        font-weight: 600;
    }
    .quick-actions {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }
    .quick-actions a {
        text-decoration: none;
        color: #555;
        transition: all 0.3s ease;
    }
    .quick-actions a:hover {
        transform: translateY(-3px);
        color: #000;
    }
    .quick-action-icon {
        background: #4081F6;
        color: white;
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        margin: 0 auto 8px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }

    .quick-action-icon:hover {
        background: white;
        color: #4081F6;
    }

    .quick-action-icon i {
        font-size: 28px;
    }

    /* Paginação */
    .pagination {
        justify-content: center;
        margin-top: 20px;
    }
</style>

<div class="content">
    <div class="container-fluid">

        <!-- Cards principais -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <a href="{{ route('beneficiary.general.index') }}">
                    <div class="card card-stats text-center">
                        <div class="card-header card-header-primary card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">diversity_3</i>
                            </div>
                            <p class="card-category">Beneficiários Ativos</p>
                            <h3 class="card-title">{{$beneficiaries->count()}}</h3>
                        </div>
                        <div class="card-footer text-center">
                            <i class="material-icons">update</i> Atualizado agora
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <a href="{{ route('convenio.index') }}">
                    <div class="card card-stats text-center">
                        <div class="card-header card-header-primary card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">handshake</i>
                            </div>
                            <p class="card-category">Serviços</p>
                            <h3 class="card-title">{{$convenios->count()}}</h3>
                        </div>
                        <div class="card-footer text-center">
                            <i class="material-icons">update</i> Atualizado agora
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats text-center">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">favorite</i>
                        </div>
                        <p class="card-category">Planos</p>
                        <h3 class="card-title">{{$plans->count()}}</h3>
                    </div>
                    <div class="card-footer text-center">
                        <i class="material-icons">update</i> Atualizado agora
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <a href="{{ route('partner.index') }}">
                    <div class="card card-stats text-center">
                        <div class="card-header card-header-primary card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">check_circle</i>
                            </div>
                            <p class="card-category">Parceiros</p>
                            <h3 class="card-title">{{$partners->count()}}</h3>
                        </div>
                        <div class="card-footer text-center">
                            <i class="material-icons">update</i> Atualizado agora
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Últimas Empresas + Atalhos -->
        <div class="row">
            <!-- Lista de Empresas -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Últimas Empresas Cadastradas</h4>
                        <p class="card-category">Total: {{$companies->count()}}</p>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CNPJ</th>
                                <th>Plano</th>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>{{ $company->name ?? '-' }}</td>
                                    <td>{{ $company->cnpj ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('plan.index', ['company'=>$company->id]) }}" class="btn btn-primary btn-sm">
                                            Ver Planos
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhuma empresa cadastrada</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Paginação -->
                        <div class="pagination-container">
                            {{ $companies->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atalhos Rápidos -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Atalhos Rápidos</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="quick-actions">
                            <a href="{{ route('beneficiary.general.index') }}">
                                <div class="quick-action-icon"><i class="material-icons">person_add</i></div>
                                <span>Beneficiário</span>
                            </a>
                            <a href="{{ route('convenio.index') }}">
                                <div class="quick-action-icon"><i class="material-icons">add_business</i></div>
                                <span>Serviço</span>
                            </a>
                            <a href="{{ route('partner.index') }}">
                                <div class="quick-action-icon"><i class="material-icons">handshake</i></div>
                                <span>Parceiro</span>
                            </a>
                            <a href="{{ route('company.index') }}">
                                <div class="quick-action-icon"><i class="material-icons">business</i></div>
                                <span>Empresas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        md.initDashboardPageCharts();
    });
</script>
@endpush
