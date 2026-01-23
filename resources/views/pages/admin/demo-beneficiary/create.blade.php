@extends('layouts.app', ['activePage' => 'demo-beneficiaries', 'titlePage' => __('Criar Beneficiário Demo')])

@section('content')
<div class="content">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
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
                        <h4 class="card-title">Criar Beneficiário Demo</h4>
                        <p class="card-category">Crie uma conta de demonstração sem passar pelo fluxo de pagamento</p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.demo-beneficiary.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Empresa *</label>
                                        <select name="company_id" class="form-control select2" required>
                                            <option value="">Selecione uma empresa</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Plano *</label>
                                        <select name="plan_id" class="form-control select2" required>
                                            <option value="">Selecione um plano</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }} - R$ {{ number_format($plan->value, 2, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Nome Completo *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">CPF *</label>
                                        <input type="text" name="cpf" class="form-control" value="{{ old('cpf') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Email *</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Telefone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Data de Nascimento *</label>
                                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Gênero *</label>
                                        <select name="gender" class="form-control" required>
                                            <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Grau de Parentesco *</label>
                                        <input type="text" name="relationship" class="form-control" value="{{ old('relationship', 'Titular') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Nome da Mãe</label>
                                        <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Senha (opcional - padrão: data nascimento)</label>
                                        <input type="password" name="password" class="form-control" minlength="6">
                                        <small class="form-text text-muted">Se não informada, será usada a data de nascimento (DDMMAAAA)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group">
                                        <label class="bmd-label-floating">Período Demo (dias) *</label>
                                        <input type="number" name="demo_days" class="form-control" value="{{ old('demo_days', 30) }}" min="1" max="365" required>
                                        <small class="form-text text-muted">Padrão: 30 dias</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="auto_login" value="1">
                                            Criar e fazer login automático
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons">save</i> Salvar e Visualizar
                                    </button>
                                    <a href="{{ route('admin.demo-beneficiary.index') }}" class="btn btn-secondary">
                                        <i class="material-icons">cancel</i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush

