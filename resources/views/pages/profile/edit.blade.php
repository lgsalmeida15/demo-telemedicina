@extends('layouts.app', ['activePage' => 'profile', 'titlePage' => __('Perfil do Usuário')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{ route('profile.update') }}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('put')

            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Editar Perfil') }}</h4>
                <p class="card-category">{{ __('Informações do Usuário') }}</p>
              </div>
              <div class="card-body">
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Nome') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" 
                             name="name" id="input-name" type="text" 
                             placeholder="Nome" 
                             value="{{ old('name', auth()->user()->name) }}" required />
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">
                          {{ $errors->first('name') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('E-mail') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                             name="email" id="input-email" type="email" 
                             placeholder="E-mail" 
                             value="{{ old('email', auth()->user()->email) }}" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">
                          {{ $errors->first('email') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block">Salvar</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Trocar senha --}}
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{ route('profile.password') }}" class="form-horizontal">
            @csrf
            @method('put')

            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Alterar Senha</h4>
                <p class="card-category">Senha</p>
              </div>

              <div class="card-body">
                @if (session('status_password'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status_password') }}</span>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-current-password">Senha Atual</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}" 
                             type="password" name="old_password" id="input-current-password" 
                             placeholder="Senha Atual" required />
                      @if ($errors->has('old_password'))
                        <span id="old-password-error" class="error text-danger">
                          {{ $errors->first('old_password') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password">Nova Senha</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                             name="password" id="input-password" type="password" 
                             placeholder="Nova Senha" required />
                      @if ($errors->has('password'))
                        <span id="password-error" class="error text-danger">
                          {{ $errors->first('password') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">Confirmar Nova Senha</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" 
                             name="password_confirmation" 
                             id="input-password-confirmation" 
                             type="password" 
                             placeholder="Confirmar Nova Senha" required />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block">Alterar Senha</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
