@extends('layouts.app', [
    'activePage' => 'beneficiary_dashboard',
    'titlePage' => __('Editar Perfil')
])

@section('content')

<style>
    /* ===== GLASS UI (MESMO PADRÃO DO INDEX) ===== */

    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        border: 1px solid rgba(255,255,255,0.35);
    }

    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f6f92;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-label {
        font-size: .85rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .form-control-lg {
        border-radius: 12px;
    }
</style>

<div class="content">
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- ==========================
             TÍTULO
        ===========================-->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="glass-card p-4">
                    <h3 class="section-title mb-1">
                        <i class="material-icons">edit</i> Editar meus dados de dependente
                    </h3>
                    <p class="text-muted mb-0">
                        Atualize apenas as informações permitidas abaixo.
                    </p>
                    <h3 class="section-title mb-1">Nome: {{$profile->name}}</h3>
                    <h3 class="section-title mb-1">CPF: {{$profile->cpf}}</h3>
                </div>
            </div>
        </div>

        <!-- ==========================
             FORMULÁRIO
        ===========================-->
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card p-4">

                    <form method="POST" action="{{ route('dependent.area.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            <!-- EMAIL -->
                            <div class="col-md-6 mb-3">
                                <label class="info-label">E-mail</label>
                                <input type="email"
                                       name="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       value="{{ old('email', $profile->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- TELEFONE -->
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Telefone</label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $profile->phone) }}"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- DATA NASCIMENTO -->
                            <div class="col-md-6 mb-3">
                                <label class="info-label">Data de nascimento</label>
                                <input type="date"
                                       name="birth_date"
                                       class="form-control form-control-lg @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date', $profile->birth_date) }}"
                                       required>
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SENHA -->
                            <div class="col-md-6 mb-2">
                                <label class="info-label">Nova senha</label>
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       placeholder="Deixe em branco para não alterar">
                                <small class="text-muted">
                                    A senha só será alterada se este campo for preenchido.
                                </small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- BOTÕES -->
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('dependent.area.index') }}"
                               class="btn btn-outline-secondary btn-lg">
                                <i class="material-icons">arrow_back</i> Voltar
                            </a>

                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="material-icons">save</i> Salvar alterações
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phone = document.getElementById('phone');

        phone.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 11);

            if (value.length >= 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            }
            if (value.length >= 10) {
                value = value.slice(0, 10) + '-' + value.slice(10);
            }

            e.target.value = value;
        });
    });
</script>

@endsection
