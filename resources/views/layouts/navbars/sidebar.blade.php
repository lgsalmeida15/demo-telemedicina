@php
    $isBeneficiary = Auth::guard('beneficiary')->check();
    $isDependent = Auth::guard('dependent')->check();
    $isAdmin = Auth::guard('web')->check(); // ou Auth::check() se o guard padrão for web
@endphp

<div class="sidebar" data-color="purple" data-background-color="white">
  <div class="logo">
    <a href="{{ $isBeneficiary ? route('beneficiary.area.index') : route('admin.home') }}" class="simple-text logo-normal">
      <img src="{{ asset('material/img/logo.png') }}" alt="Logo" width="200px" style="border-radius: 10px;">
    </a>
  </div>

  <div class="sidebar-wrapper">
    <ul class="nav">

      {{-- ======================================
          SE FOR BENEFICIÁRIO LOGADO
      ======================================= --}}
      @if($isBeneficiary)
        <li class="nav-item{{ $activePage == 'beneficiary_dashboard' ? ' active' : '' }}">
          <a class="nav-link" href="{{ route('beneficiary.area.index') }}">
            <i class="material-icons">home</i>
            <p>{{ __('Início') }}</p>
          </a>
        </li>

        {{-- ✅ Telemedicina sempre visível para demonstração --}}
        <li class="nav-item{{ $activePage == 'telemedicine' ? ' active' : '' }}">
          <a class="nav-link loading-link" href="{{ route('beneficiary.area.telemedicine') }}">
            <i class="material-icons">local_hospital</i>
            <p>{{ __('Telemedicina') }}</p>
          </a>
        </li>

        <li class="nav-item{{ $activePage == 'dependents' ? ' active' : '' }}">
          <a class="nav-link" href="{{ route('beneficiary.area.dependent') }}">
            <i class="material-icons">accessibility</i>
            <p>{{ __('Dependentes') }}</p>
          </a>
        </li>

        <li class="nav-item{{ $activePage == 'schedules' ? ' active' : '' }}">
          <a class="nav-link loading-link" href="{{ route('beneficiary.area.schedule') }}">
            <i class="material-icons">calendar_month</i>
            <p>{{ __('Agendamentos') }}</p>
          </a>
        </li>
        

        <li class="nav-item" style="margin-bottom: 8rem">
          <a class="nav-link" href="{{ route('beneficiary.logout') }}"
             onclick="event.preventDefault();document.getElementById('beneficiary-logout-form').submit();">
            <i class="material-icons">logout</i>
            <p>{{ __('Sair') }}</p>
          </a>
          <form id="beneficiary-logout-form" action="{{ route('beneficiary.logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>

      {{-- ======================================
          SE FOR DEPENDENTE LOGADO
      ======================================= --}}
      @elseif ($isDependent)
        <li class="nav-item{{ $activePage == 'dependent_dashboard' ? ' active' : '' }}">
          <a class="nav-link" href="{{ route('dependent.area.index') }}">
            <i class="material-icons">home</i>
            <p>{{ __('Início') }}</p>
          </a>
        </li>

        <li class="nav-item{{ $activePage == 'telemedicine' ? ' active' : '' }}">
            <a class="nav-link loading-link" href="{{ route('dependent.area.telemedicine') }}">
              <i class="material-icons">local_hospital</i>
              <p>{{ __('Telemedicina') }}</p>
            </a>
          </li>

        <li class="nav-item{{ $activePage == 'schedules' ? ' active' : '' }}">
          <a class="nav-link loading-link" href="{{ route('dependent.area.schedules') }}">
            <i class="material-icons">calendar_month</i>
            <p>{{ __('Agendamentos') }}</p>
          </a>
        </li>
        <li class="nav-item" style="margin-bottom: 8rem">
          <a class="nav-link" href="{{ route('dependent.logout') }}"
             onclick="event.preventDefault();document.getElementById('dependent-logout-form').submit();">
            <i class="material-icons">logout</i>
            <p>{{ __('Sair') }}</p>
          </a>
          <form id="dependent-logout-form" action="{{ route('dependent.logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>

      {{-- ======================================
          SE FOR ADMINISTRADOR LOGADO
      ======================================= --}}
      @elseif($isAdmin)
        <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
          <a class="nav-link" href="{{ route('admin.home') }}">
            <i class="material-icons">dashboard</i>
              <p>{{ __('Painel de Controle') }}</p>
          </a>
        </li>

        <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'gerenciamento-de-usuarios') ? ' active' : '' }}">
          <a class="nav-link" data-toggle="collapse" href="#laravelExample" aria-expanded="false">
            <i class="material-icons">people</i>
            <p>{{ __('Usuários') }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="laravelExample">
            <ul class="nav">
              <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                  <i class="material-icons">badge</i>
                  <span class="sidebar-normal">{{ __('Perfil de Usuário') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'gerenciamento-de-usuarios' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('user.index') }}">
                  <i class="material-icons">group_add</i>
                  <span class="sidebar-normal">{{ __('Gerenciador de Usuários') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- PARCEIROS --}}
        <li class="nav-item{{ $activePage == 'partners' ? ' active' : '' }}">
          <a class="nav-link" href="{{ route('partner.index') }}">
            <i class="material-icons">check_circle</i>
            <p>{{ __('Parceiros') }}</p>
          </a>
        </li>

        {{-- BENEFICIÁRIOS E EMPRESAS --}}
        <li class="nav-item {{ in_array($activePage, ['beneficiaries', 'companies']) ? ' active' : '' }}">
          <a class="nav-link" data-toggle="collapse" href="#clientesMenu" aria-expanded="false">
            <i class="material-icons">face</i>
            <p>{{ __('Clientes') }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="clientesMenu">
            <ul class="nav">
              <li class="nav-item{{ $activePage == 'companies' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('company.index') }}">
                  <i class="material-icons">business</i>
                  <span class="sidebar-normal">{{ __('Empresas') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'beneficiaries' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('beneficiary.general.index') }}">
                  <i class="material-icons">person</i>
                  <p>{{ __('Beneficiários') }}</p>
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- SERVIÇOS --}}
        <li class="nav-item {{ ($activePage == 'convenios' || $activePage == 'convenios_categoria' || $activePage == 'convenios_tipo') ? ' active' : '' }}">
          <a class="nav-link" data-toggle="collapse" href="#convenioMenu" aria-expanded="false">
            <i class="material-icons">handshake</i>
            <p>{{ __('Serviços') }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="convenioMenu">
            <ul class="nav">
              <li class="nav-item{{ $activePage == 'convenios_categoria' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('convenio.categoria.index') }}">
                  <i class="material-icons">dashboard</i>
                  <span class="sidebar-normal">{{ __('Categorias') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'convenios_tipo' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('convenio.type.index') }}">
                  <i class="material-icons">dashboard</i>
                  <span class="sidebar-normal">{{ __('Tipos') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'convenios' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('convenio.index') }}">
                  <i class="material-icons">handshake</i>
                  <span class="sidebar-normal">{{ __('Serviços') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- FINANCEIRO --}}
        <li class="nav-item {{ in_array($activePage, ['financeiro', 'caixas', 'plano_contas', 'contas_a_pagar', 'contas_a_receber']) ? ' active' : '' }}">
          <a class="nav-link" data-toggle="collapse" href="#financeiroMenu" aria-expanded="false">
            <i class="material-icons">currency_exchange</i>
            <p>{{ __('Financeiro') }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="financeiroMenu">
            <ul class="nav">
              <li class="nav-item{{ $activePage == 'financeiro' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('financial.index') }}">
                  <i class="material-icons">dashboard</i>
                  <span class="sidebar-normal">{{ __('Lançamentos') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'caixa' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('caixa.index') }}">
                  <i class="material-icons">attach_money</i>
                  <span class="sidebar-normal">{{ __('Caixas') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'plano_contas' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('costcenter.index') }}">
                  <i class="material-icons">attach_money</i>
                  <span class="sidebar-normal">{{ __('Plano de Contas') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'contas_a_pagar' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('conta_pagar.index') }}">
                  <i class="material-icons">attach_money</i>
                  <span class="sidebar-normal">{{ __('Contas a Pagar') }}</span>
                </a>
              </li>
              <li class="nav-item{{ $activePage == 'contas_a_receber' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('conta_receber.index') }}">
                  <i class="material-icons">attach_money</i>
                  <span class="sidebar-normal">{{ __('Contas a Receber') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- LOGOUT ADMIN --}}
        <li class="nav-item" style="margin-bottom: 8rem">
          <a class="nav-link" href="{{ route('logout') }}"
             onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="material-icons">logout</i>
            <p>{{ __('Sair') }}</p>
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
      @endif

    </ul>
  </div>
</div>
