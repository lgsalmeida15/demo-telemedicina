<div id="beneficiaryTutorial" class="tutorial-overlay">
    <div class="tutorial-container">

        <!-- SLIDE 1 -->
        <div class="tutorial-slide active">
            <h2>Bem-vindo à Área do Beneficiário</h2>
            <p>
                Aqui você poderá visualizar informações dos seus planos e benefícios,
                gerenciar dependentes e acessar a telemedicina da plataforma BOXFARMA.
            </p>
            <img src="{{ asset('material/img/tutorial/client-tutorial.png') }}" class="tutorial-img">

            <button class="btn-next">Próximo</button>
        </div>

        <!-- SLIDE 2 -->
        <div class="tutorial-slide">
            <h2>Início</h2>
            <p>
                No menu <strong>Início</strong>, você poderá visualizar rapidamente seus dados cadastrais,
                seus planos ativos e informações essenciais da sua conta.
            </p>
            <img src="{{ asset('material/img/tutorial/inicio.png') }}" class="tutorial-img">

            <div class="tutorial-buttons">
                <button class="btn-prev">Voltar</button>
                <button class="btn-next">Próximo</button>
            </div>
        </div>

        <!-- SLIDE 3 -->
        <div class="tutorial-slide">
            <h2>Telemedicina</h2>
            <p>
                Esta é a área exclusiva de <strong>Telemedicina</strong> para consultas rápidas, seguras e certificadas.
                O atendimento 
                inicia obrigatoriamente com um Clínico Geral, responsável pela avaliação e 
                triagem. Quando necessário, o paciente é encaminhado para um Especialista, 
                garantindo precisão diagnóstica e segurança no atendimento. OBS: para cancelar uma consulta, basta 
                acessar o link da consulta e cancelar por lá.
            </p>
            <img src="{{ asset('material/img/tutorial/telemedicina.png') }}" class="tutorial-img">

            <div class="tutorial-buttons">
                <button class="btn-prev">Voltar</button>
                <button class="btn-next">Próximo</button>
            </div>
        </div>
        

        <!-- SLIDE 4 -->
        <div class="tutorial-slide">
            <h2>Dependentes</h2>
            <p>
                Na aba <strong>Dependentes</strong> você pode cadastrar até 3 dependentes (sem limites de
                parentesco), editar informações, visualizar detalhes ou remover um dependente,
                caso necessário.
            </p>
            <img src="{{ asset('material/img/tutorial/dependente.png') }}" class="tutorial-img">

            <div class="tutorial-buttons">
                <button class="btn-prev">Voltar</button>
                <button class="btn-next">Próximo</button>
            </div>
        </div>
        

        <!-- SLIDE 5 -->
        <div class="tutorial-slide">
            <h2>Agendamentos</h2>
            <p>
                Na área de <strong>Agendamentos</strong> você encontra o histórico completo das suas consultas 
                já realizadas, consultas agendadas e canceladas.  
                <br><br>
                Essa seção ajuda você a acompanhar seu atendimento médico de forma 
                organizada, garantindo clareza sobre tudo que já ocorreu e sobre o que ainda está 
                programado. 
            </p>

            <img src="{{ asset('material/img/tutorial/agendamento.png') }}" class="tutorial-img">

            <div class="tutorial-buttons">
                <button class="btn-prev">Voltar</button>
                <button class="btn-next">Próximo</button>
            </div>
        </div>

        <!-- SLIDE 5 – FINAL -->
        <div class="tutorial-slide">
            <h2>Pronto! Boa experiência 🎉</h2>
            <p>
                Esperamos que você aproveite ao máximo sua área do beneficiário. Estamos aqui
                para garantir praticidade e eficiência no seu atendimento.
            </p>

            <label class="checkbox-label">
                <input type="checkbox" id="dontShowAgain"> Não mostrar novamente
            </label>

            <button class="btn-finish">Finalizar</button>
        </div>

    </div>
</div>

<style>
/* OVERLAY */
.tutorial-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    display: none; /* será ativado via JS */
    align-items: center;
    justify-content: center;
    z-index: 999999;
}

/* CARD */
.tutorial-container {
    width: 90%;
    max-width: 600px;
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    animation: fadeIn .4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(.96); }
    to   { opacity: 1; transform: scale(1); }
}

/* SLIDES */
.tutorial-slide { display: none; }
.tutorial-slide.active { display: block; }

.tutorial-slide h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #4081F6;
    margin-bottom: 1rem;
}

.tutorial-slide p {
    font-size: 1.05rem;
    margin-bottom: 1.5rem;
    line-height: 1.45rem;
}

.tutorial-img {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    object-fit: cover;
}

/* BUTTONS */
button.btn-next,
button.btn-prev,
button.btn-finish {
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-next {
    background: #4081F6;
    color: #fff;
}

.btn-prev {
    background: #cccccc;
}

.btn-finish {
    background: #28a745;
    color: #fff;
    width: 100%;
    margin-top: 10px;
}

.tutorial-buttons {
    display: flex;
    justify-content: space-between;
}

.checkbox-label {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}
</style>

<script>
// ===============================
// CONTROLADOR DO TUTORIAL
// ===============================
document.addEventListener("DOMContentLoaded", () => {

    const overlay        = document.getElementById("beneficiaryTutorial");
    const openBtn        = document.getElementById("openTutorialBtn");
    const slides         = document.querySelectorAll(".tutorial-slide");
    const dontShowAgain  = document.getElementById("dontShowAgain");
    const finishBtn      = document.querySelector(".btn-finish");

    if (!overlay || slides.length === 0) return;

    let current = 0;

    const updateSlides = () => {
        slides.forEach(s => s.classList.remove("active"));
        slides[current].classList.add("active");
    };

    // ---- REGISTRA BOTÕES NEXT / PREV (sempre) ----
    document.querySelectorAll(".btn-next").forEach(btn => {
        btn.addEventListener("click", () => {
            if (current < slides.length - 1) {
                current++;
                updateSlides();
            }
        });
    });

    document.querySelectorAll(".btn-prev").forEach(btn => {
        btn.addEventListener("click", () => {
            if (current > 0) {
                current--;
                updateSlides();
            }
        });
    });

    // ---- BOTÃO FINALIZAR ----
    if (finishBtn) {
        finishBtn.addEventListener("click", () => {
            if (dontShowAgain && dontShowAgain.checked) {
                localStorage.setItem("hideBeneficiaryTutorial", "true");
            }
            overlay.style.display = "none";
        });
    }

    // ---- MOSTRAR AUTOMÁTICO SE NÃO ESTIVER DESABILITADO ----
    if (localStorage.getItem("hideBeneficiaryTutorial") !== "true") {
        current = 0;
        updateSlides();
        overlay.style.display = "flex";
    }

    // ---- BOTÃO FLUTUANTE PARA REABRIR O TUTORIAL ----
    if (openBtn) {
        openBtn.addEventListener("click", () => {
            // "Reativa" o tutorial: limpa flag e desmarca checkbox
            localStorage.removeItem("hideBeneficiaryTutorial");
            if (dontShowAgain) dontShowAgain.checked = false;

            current = 0;
            updateSlides();
            overlay.style.display = "flex";
        });
    }

});
</script>


