@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')

<div class="content">
    @if($sucesso ?? false)
        <div class="alert alert-success">
            {{ $sucesso }}
        </div>
    @endif
    @if($errors->any())
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
                        <h4 class="card-title">Serviços do Plano: {{ $plan->name }}</h4>
                        <p class="card-category">Lista completa</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{route('plan.index',['company'=>$plan->company->id])}}" class="btn btn-secondary"><i class="material-icons">arrow_back</i>
                                    VOLTAR
                                </a>
                                <h5 class="font-weight-bold">Serviços que esse plano usa:</h5>
                                <hr>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#convenience">
                                    <i class="material-icons">add</i> Adicionar Serviço
                                </button>
                                <br>
                                <br>
                                <h6>Quantidade: {{$planConveniences->count()}}</h6>
                                {{-- <h6>Valor total gasto com planos: R$ {{ number_format($planConveniencess->sum(fn($planConveniences) => $planConveniences->plan->value), 2, ',', '.') }}</h6> --}}
                                @forelse($planConveniences as $planConvenience)
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-lg" style="border: 1px solid #e0e0e0;">
                                        <div>
                                            <strong>{{$planConvenience->convenio->nome_convenio}} - @if($planConvenience->convenio->status == 'Inativo')<span style="color: red"> ATENÇÃO: Esse serviço está inativo.</span> @else <span style="color: rgb(40, 29, 246)"> Ativo </span>@endif</strong>
                                        </div>
                                        <div>
                                            <form action="{{route('plan.convenience.destroy',['plan_convenience'=>$planConvenience->id])}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <h6>Esse plano não esta usando nenhum serviço.</h6>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Adicionar Serviço --}}
<div class="modal fade" id="convenience" tabindex="-1" role="dialog" aria-labelledby="convenienceLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convenienceLabel">Adicionar Serviço</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('plan.convenience.store',['plan'=>$plan->id])}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="convenio-select">Selecione o Serviço</label>
                        <select class="form-control select2" id="convenio-select" name="convenio_id" required style="width: 100%;">
                            <option></option>
                            @forelse ($conveniences as $convenience)
                                <option value="{{ $convenience->id }}">{{ $convenience->nome_convenio }}</option>
                            @empty
                                <option value="">Sem Serviços Ativos</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Serviço</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
    {{-- Scripts para Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializa o Select2 para o campo de seleção de planos
            $('.select2').select2({
                placeholder: "Selecione um plano para a empresa",
                allowClear: true
            });
        });
    </script>
@endpush