<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Checkout - {{ $company->name }}</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: #f5f7fb;
            font-family: "Inter", Arial, sans-serif;
        }

        .checkout-wrapper {
            display: flex;
            gap: 25px;
        }

        @media(max-width: 992px) {
            .checkout-wrapper {
                flex-direction: column;
            }

            .resume-box {
                margin-top: 40px;
            }
        }

        .checkout-card {
            background: #fff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        /* STEP LINE STYLE */
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stepper .step {
            flex: 1;
            text-align: center;
            padding-bottom: 10px;
            font-weight: 600;
            font-size: 15px;
            color: #9aa0a6;
            position: relative;
        }

        .step.active {
            color: #005bff;
        }

        .step.active::after {
            content: '';
            width: 40%;
            height: 3px;
            background: #005bff;
            border-radius: 10px;
            position: absolute;
            bottom: 0;
            left: 30%;
            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                width: 0;
            }

            to {
                opacity: 1;
                width: 40%;
            }
        }


        /* PLANOS COM ANIMAÇÃO */
        .plan-card {
            background: #ffffff;
            border: 2px solid #e3e8ef;
            border-radius: 16px;
            padding: 18px;
            transition: .25s;
            cursor: pointer;
        }

        .plan-card:hover {
            border-color: #5fa3ff;
            transform: translateY(-3px);
        }

        .plan-card.selected {
            border-color: #005bff;
            background: #eaf1ff;
            transform: translateY(-5px);
        }

        /* RESUMO DO PEDIDO */
        .resume-box {
            background: #fff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            min-width: 280px;
        }

        .price-total {
            font-size: 26px;
            font-weight: 700;
            color: #111;
        }

        /* INPUTS MODERNOS */
        .form-control-lg {
            border-radius: 10px;
            height: 50px;
        }

        .btn-primary {
            border-radius: 10px;
            background: linear-gradient(90deg, #006cff, #4898ff);
            border: none;
        }

        .btn-success {
            border-radius: 10px;
            background: linear-gradient(90deg, #00b45a, #00d06e);
            border: none;
        }
    </style>


    <style>
        .checkout-header {
            padding-top: 20px;
            padding-bottom: 10px;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        /* Alturas padronizadas */
        .logo-elo {
            height: 55px;
            width: auto;
            object-fit: contain;
            filter: contrast(1.1);
        }

        .logo-boxfarma {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        /* Linha vertical separadora */
        .divider {
            width: 2px;
            height: 40px;
            background: #e1e4e8;
            border-radius: 2px;
        }

        /* MOBILE */
        @media(max-width: 576px) {
            .logo-box {
                flex-direction: column;
                gap: 18px;
            }

            .divider {
                display: none;
            }

            .logo-elo {
                height: 50px;
            }

            .logo-boxfarma {
                height: 52px;
            }
        }
    </style>

</head>

<body>
    <div class="checkout-header text-center my-4">
        <div class="logo-box d-flex justify-content-center align-items-center gap-4">
            <img src="{{ asset('material/img/elo-logo-original.png') }}" alt="Logo Elo" class="logo-elo">

            <div class="divider"></div>

            <img src="{{ asset('material/img/logo.png') }}" alt="Logo BoxFarma" class="logo-boxfarma">
        </div>
    </div>

    <div class="container mt-4 mb-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-card">

                    <!-- STEP INDICATOR -->
                    <div class="stepper">
                        <div class="step step-1 active">Plano</div>
                        <div class="step step-2">Beneficiário</div>
                        <div class="step step-3">Pagamento</div>
                    </div>

                    <!-- STEP 1 -->
                    <div id="step1" class="step-box active">
                        <h4 class="font-weight-bold mb-3">Escolha seu plano</h4>

                        <div class="row">
                            @foreach ($plans as $plan)
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="plan-card" data-id="{{ $plan->uuid }}">
                                        <h5 class="font-weight-bold mb-1">{{ $plan->name }}</h5>
                                        {{-- <p class="text-muted small">{{ $plan->description }}</p> --}}
                                        <h4 class="text-primary font-weight-bold mb-0">
                                            R$ {{ number_format($plan->value, 2, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" id="selected_plan">

                        <div class="text-right mt-4">
                            <button class="btn btn-primary btn-lg px-5" id="goToStep2" disabled>Continuar</button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step2" class="step-box" style="display:none;">
                        <h4 class="font-weight-bold mb-3">Informações do Beneficiário</h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nome Completo</label>
                                <input id="name" name="name" class="form-control form-control-lg"
                                    value="{{ old('name') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>CPF</label>
                                <input id="cpf" name="cpf" class="form-control form-control-lg"
                                    value="{{ old('cpf') }}" placeholder="___.___.___-__">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>E-mail</label>
                                <input id="email" name="email" class="form-control form-control-lg"
                                    value="{{ old('email') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Telefone</label>
                                <input id="phone" name="phone" class="form-control form-control-lg"
                                    value="{{ old('phone') }}" placeholder="( __ ) ______-____">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Data de Nascimento</label>
                                <input id="birth_date" type="date" name="birth_date"
                                    class="form-control form-control-lg" value="{{ old('birth_date') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Gênero</label>
                                <select id="gender" class="form-control form-control-lg">
                                    <option value="">Selecione</option>
                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino
                                    </option>
                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Senha de acesso</label>
                                <input id="password" name="password" class="form-control form-control-lg"
                                    value="{{ old('password') }}"
                                    placeholder="para acesso a área do cliente">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nome da Mãe</label>
                                <input id="mother_name" name="mother_name" class="form-control form-control-lg"
                                    value="{{ old('mother_name') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-light btn-lg" onclick="backToStep(1)">Voltar</button>
                            <button class="btn btn-primary btn-lg" onclick="goToStep(3)">Continuar</button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div id="step3" class="step-box" style="display:none;">
                        <h4 class="font-weight-bold mb-3">Pagamento</h4>

                        <div class="mb-3">
                            <label>Forma de Pagamento</label>
                            <select id="payment_type" name="payment_type" class="form-control form-control-lg">
                                <option value="CREDIT_CARD"
                                    {{ old('payment_type') == 'CREDIT_CARD' ? 'selected' : '' }}>Crédito</option>
                                {{-- <option value="DEBIT_CARD"
                                    {{ old('payment_type') == 'DEBIT_CARD' ? 'selected' : '' }}>Débito</option> --}}
                            </select>
                        </div>

                        <div id="card_fields" class="mt-4">
                            <h5 class="font-weight-bold mb-3">Dados do Cartão</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nome no Cartão</label>
                                    <input id="card_holder" name="card_holder" value="{{ old('card_holder') }}"
                                        class="form-control form-control-lg">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Número do Cartão</label>
                                    <input id="card_number" name="card_number" value="{{ old('card_number') }}"
                                        class="form-control form-control-lg">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Mês</label>
                                    <input id="card_month" name="card_month" value="{{ old('card_month') }}"
                                        class="form-control form-control-lg">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Ano</label>
                                    <input id="card_year" name="card_year" value="{{ old('card_year') }}"
                                        class="form-control form-control-lg">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>CVV (Código de Segurança)</label>
                                    <input id="ccv" name="ccv" value="{{ old('ccv') }}"
                                        class="form-control form-control-lg">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>CEP</label>
                                    <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                        class="form-control form-control-lg">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Número do endereço</label>
                                    <input id="address_number" name="address_number"
                                        value="{{ old('address_number') }}" class="form-control form-control-lg">
                                </div>
                                <div class="col-12 mt-3">
                                    <a href="#" data-toggle="modal" data-target="#termsModal" class="small text-primary">
                                        Consultar Termos de Uso
                                    </a>

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="accept_terms">
                                        <label class="form-check-label" for="accept_terms">
                                            Li e aceito os Termos de Uso
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-light btn-lg" onclick="backToStep(2)">Voltar</button>

                            <form method="POST" action="{{ route('checkout.process') }}" id="finalForm">
                                @csrf

                                <input type="hidden" name="company_uuid" value="{{ $company->uuid }}">
                                <input type="hidden" name="plan_uuid" id="form_plan_uuid">
                                <input type="hidden" name="name" id="form_name">
                                <input type="hidden" name="cpf" id="form_cpf">
                                <input type="hidden" name="email" id="form_email">
                                <input type="hidden" name="phone" id="form_phone">
                                <input type="hidden" name="birth_date" id="form_birth_date">
                                <input type="hidden" name="gender" id="form_gender">
                                <input type="hidden" name="password" id="form_password">
                                <input type="hidden" name="mother_name" id="form_mother_name">
                                <input type="hidden" name="payment_type" id="form_payment_type">

                                <input type="hidden" name="card_holder" id="form_card_holder">
                                <input type="hidden" name="card_number" id="form_card_number">
                                <input type="hidden" name="card_month" id="form_card_month">
                                <input type="hidden" name="card_year" id="form_card_year">
                                <input type="hidden" name="ccv" id="form_ccv">
                                <input type="hidden" name="postal_code" id="form_postal_code">
                                <input type="hidden" name="address_number" id="form_address_number">

                                <button id="btn-finalizar" type="submit" class="btn btn-success btn-lg px-5" disabled>
                                    Finalizar
                                </button>

                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RESUMO DO PEDIDO -->
            <div class="col-lg-4">
                <div class="resume-box">
                    <h5 class="font-weight-bold">Resumo do pedido</h5>

                    <div class="mt-3">
                        <strong class="resume-plan-name text-primary"></strong>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="subtotal">R$ 0,00</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="price-total">Total</span>
                        <span class="price-total total">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL TERMOS DE USO -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:14px;">
        <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="termsTitle">Termo de Uso – BoxFarma Telemedicina</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto;">
    
            <h5 class="fw-bold text-dark mb-3">1. Partes</h5>
            <p class="text-secondary small text-justify">
                <strong>1.1.</strong> Este Termo de Uso (“Termo”) regula a utilização da plataforma de telemedicina oferecida pela BoxFarma, doravante denominada “Plataforma”, por parte do Usuário (paciente), doravante “Você” ou “Usuário”. A utilização da Plataforma caracteriza sua aceitação plena e irrestrita de todos os termos e condições aqui descritos.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">2. Objeto</h5>
            <p class="text-secondary small text-justify">
                <strong>2.1.</strong> A Plataforma permite a realização de serviços de telemedicina: consultas médicas a distância (vídeo, áudio ou chat), emissão de prescrições e encaminhamentos, orientações médicas, acompanhamento de saúde e demais serviços médicos conforme disponibilidade da BoxFarma.
            </p>
            <p class="text-secondary small text-justify">
                <strong>2.2.</strong> A telemedicina não substitui integralmente o atendimento presencial. Em situações de urgência, emergência ou que requeiram exame físico, o Usuário deverá procurar atendimento presencial.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">3. Elegibilidade e Conta de Usuário</h5>
            <p class="text-secondary small text-justify">
                <strong>3.1.</strong> A utilização da Plataforma é individual, vinculada ao CPF do Usuário. Não é permitida a cessão ou compartilhamento de acesso com terceiros.
            </p>
            <p class="text-secondary small text-justify">
                <strong>3.2.</strong> Ao se cadastrar, o Usuário declara ser maior de 18 anos ou, em caso de menor de idade, estar representado por responsável legal.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">4. Privacidade, Dados e Segurança</h5>
            <p class="text-secondary small text-justify">
                <strong>4.1.</strong> A BoxFarma coletará, armazenará e tratará os dados fornecidos pelo Usuário (identificação, dados de saúde, histórico, etc.), em conformidade com a legislação aplicável, inclusive a Lei Geral de Proteção de Dados (LGPD).
            </p>
            <p class="text-secondary small text-justify">
                <strong>4.2.</strong> Os dados de consultas (áudio, vídeo ou chat), prontuário e prescrições poderão ser armazenados, preservando sigilo médico e privacidade.
            </p>
            <p class="text-secondary small text-justify">
                <strong>4.3.</strong> A BoxFarma não compartilhará seus dados com terceiros sem consentimento, salvo por obrigação legal ou regulatória.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">5. Consentimento e Responsabilidades</h5>
            <p class="text-secondary small text-justify">
                <strong>5.1.</strong> Ao utilizar a Plataforma, o Usuário declara estar ciente e concordar com as limitações do atendimento remoto e com a necessidade de atendimento presencial quando for o caso.
            </p>
            <p class="text-secondary small text-justify">
                <strong>5.2.</strong> O Usuário reconhece que a teleconsulta depende de conexão de internet, uso de dispositivos compatíveis e ambiente adequado (microfone, câmera, iluminação). Problemas técnicos podem interferir na qualidade do atendimento.
            </p>
            <p class="text-secondary small text-justify">
                <strong>5.3.</strong> O Usuário declara que todas as informações pessoais e de saúde fornecidas são verdadeiras, completas e atualizadas.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">6. Limitações de Responsabilidade da Plataforma</h5>
            <p class="text-secondary small text-justify">
                <strong>6.1.</strong> A BoxFarma não será responsável por eventual falha técnica, interrupção de internet ou indisponibilidade da Plataforma que impeça o atendimento.
            </p>
            <p class="text-secondary small text-justify">
                <strong>6.2.</strong> A Plataforma não se responsabiliza por danos advindos de uso indevido, compartilhamento de acesso ou informações incorretas fornecidas pelo Usuário.
            </p>
            <p class="text-secondary small text-justify">
                <strong>6.3.</strong> A telemedicina não garante diagnóstico definitivo em todos os casos — a responsabilidade médica segue as normas aplicáveis, conforme a Conselho Federal de Medicina (CFM) e a legislação vigente.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">7. Pagamento e Cancelamento</h5>
            <p class="text-secondary small text-justify">
                <strong>7.1.</strong> Pelo uso da Plataforma, o Usuário pagará a mensalidade informada no momento da contratação.
            </p>
            <p class="text-secondary small text-justify">
                <strong>7.2.</strong> O Usuário pode cancelar o serviço a qualquer momento através do portal, e o acesso será interrompido imediatamente após o cancelamento.
            </p>
            <p class="text-secondary small text-justify">
                <strong>7.3.</strong> Em conformidade com o direito do consumidor, o Usuário pode solicitar reembolso total dentro de até 7 (sete) dias após a contratação, se desejar.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">8. Vigência e Alterações</h5>
            <p class="text-secondary small text-justify">
                <strong>8.1.</strong> Este Termo entra em vigor a partir do momento em que o Usuário aceita os termos e inicia o uso da Plataforma.
            </p>
            <p class="text-secondary small text-justify">
                <strong>8.2.</strong> A BoxFarma reserva-se o direito de modificar este Termo a qualquer momento, sendo publicadas as alterações, e o uso continuado da Plataforma implicará aceitação das novas versões.
            </p>

            <h5 class="fw-bold text-dark mt-4 mb-3">9. Disposições Gerais</h5>
            <p class="text-secondary small text-justify">
                <strong>9.1.</strong> Este Termo é regido pelas leis brasileiras, em especial pela legislação aplicável à telemedicina (como a Lei 14.510/2022 e a Resolução CFM 2.314/2022) e normas de proteção de dados.
            </p>
            <p class="text-secondary small text-justify">
                <strong>9.2.</strong> Caso qualquer cláusula deste Termo seja considerada inválida ou inexequível, as demais continuarão em pleno vigor.
            </p>
            <p class="text-secondary small text-justify">
                <strong>9.3.</strong> Qualquer controvérsia relativa à interpretação ou aplicação deste Termo será dirimida pelos tribunais competentes com base na lei brasileira.
            </p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
    </div>

    


    <script src="https://unpkg.com/imask"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS + Popper (necessário para o modal funcionar) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Seleção automática do plano após erro
        @if (session('selected_plan'))
            const selectedPlan = "{{ session('selected_plan') }}";

            $('.plan-card').each(function() {
                if ($(this).data('id') === selectedPlan) {
                    $(this).addClass('selected');
                    $('#selected_plan').val(selectedPlan);
                    updateResume();
                    $('#goToStep2').prop('disabled', false);
                }
            });
        @endif

        /* PLANOS */
        $('.plan-card').click(function() {
            $('.plan-card').removeClass('selected');
            $(this).addClass('selected');
            $('#selected_plan').val($(this).data('id'));
            $('#goToStep2').prop('disabled', false);
        });

        $('#goToStep2').click(() => goToStep(2));

        function goToStep(step) {
            $('.step').removeClass('active');
            $('.step-' + step).addClass('active');

            $('.step-box').hide();
            $('#step' + step).fadeIn(200);
        }

        function backToStep(step) {
            goToStep(step);
        }



        /* === ATUALIZAÇÃO DO RESUMO DO PEDIDO === */
        function updateResume() {
            let planId = $('#selected_plan').val();
            if (!planId) return;

            let card = $('.plan-card.selected');

            let planName = card.find('h5').text().trim();
            let planValueText = card.find('h4').text().trim().replace('R$', '').trim();
            let planValue = parseFloat(planValueText.replace('.', '').replace(',', '.'));

            $('.resume-box .subtotal').text("R$ " + planValue.toFixed(2).replace('.', ','));
            $('.resume-box .total').text("R$ " + planValue.toFixed(2).replace('.', ','));
            $('.resume-box .resume-plan-name').text(planName);
        }

        $('.plan-card').click(function() {
            $('.plan-card').removeClass('selected');
            $(this).addClass('selected');

            $('#selected_plan').val($(this).data('id'));
            $('#goToStep2').prop('disabled', false);

            updateResume();
        });



        // DESATIVAR BOTÃO ENQUANTO NÃO ACEITAR OS TERMOS
        $('#accept_terms').on('change', function () {
            if (this.checked) {
                $('#btn-finalizar').prop('disabled', false);
            } else {
                $('#btn-finalizar').prop('disabled', true);
            }
        });




        /* === MÁSCARAS IMask === */

        // CPF
        IMask(document.getElementById('cpf'), {
            mask: '000.000.000-00',
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });

        // Telefone (aceita 9 dígitos)
        const phoneInput = document.getElementById('phone');
        const phoneMask = IMask(phoneInput, {
            mask: [{
                    mask: '(00) 0000-0000'
                },
                {
                    mask: '(00) 00000-0000'
                }
            ],
            lazy: false, // placeholder sempre exibido
            placeholderChar: '_' // melhor visual
        });



        // Número do cartão
        IMask(document.getElementById('card_number'), {
            mask: '0000 0000 0000 0000',
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });

        // Mês
        IMask(document.getElementById('card_month'), {
            mask: IMask.MaskedRange,
            from: 1,
            to: 12,
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });

        // Ano (ex.: 2024 → 24)
        IMask(document.getElementById('card_year'), {
            mask: '0000',
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });

        // CVV
        IMask(document.getElementById('ccv'), {
            mask: '000',
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });

        // CEP
        IMask(document.getElementById('postal_code'), {
            mask: '00000-000',
            lazy: false // ← Mostra o padrão placeholder automaticamente
        });


        /* === SUBMIT === */
        $('#finalForm').submit(function() {
            $('#form_plan_uuid').val($('#selected_plan').val());
            $('#form_name').val($('#name').val());
            $('#form_cpf').val($('#cpf').val());
            $('#form_email').val($('#email').val());
            $('#form_phone').val($('#phone').val());
            $('#form_birth_date').val($('#birth_date').val());
            $('#form_gender').val($('#gender').val());
            $('#form_password').val($('#password').val());
            $('#form_mother_name').val($('#mother_name').val());
            $('#form_payment_type').val($('#payment_type').val());
            $('#form_card_holder').val($('#card_holder').val());
            $('#form_card_number').val($('#card_number').val());
            $('#form_card_month').val($('#card_month').val());
            $('#form_card_year').val($('#card_year').val());
            $('#form_ccv').val($('#ccv').val());
            $('#form_postal_code').val($('#postal_code').val());
            $('#form_address_number').val($('#address_number').val());
        });
    </script>


</body>

</html>
