// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // Inicializar funcionalidades
    initNavigation();
    initScrollEffects();
    initSearch();
    initCalendar();
    initConveniosFilter();
    initContactForm();
    initSmoothScroll();
});

// Navegação
function initNavigation() {
    const header = document.getElementById('header');
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Efeito de scroll no header
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Menu mobile
    hamburger.addEventListener('click', function() {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
        document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
    });

    // Fechar menu ao clicar em link
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Destacar link ativo baseado na seção visível
    const sections = document.querySelectorAll('section[id]');
    
    window.addEventListener('scroll', function() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (window.scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
}

// Efeitos de scroll
function initScrollEffects() {
    // Scroll suave para o indicador de scroll
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            document.querySelector('#sobre').scrollIntoView({
                behavior: 'smooth'
            });
        });
    }

    // Parallax effect no hero
    const heroImage = document.querySelector('.hero-image');
    if (heroImage) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            heroImage.style.transform = `translateY(${rate}px)`;
        });
    }
}

// Funcionalidade de busca
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchIcon = document.querySelector('.search-icon');

    if (searchInput && searchIcon) {
        searchIcon.addEventListener('click', function() {
            performSearch(searchInput.value);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(searchInput.value);
            }
        });

        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    }
}

function performSearch(query) {
    if (query.trim() === '') return;
    
    // Simular busca (em um site real, isso faria uma requisição ao servidor)
    console.log('Buscando por:', query);
    
    // Aqui você implementaria a lógica de busca real
    alert(`Funcionalidade de busca será implementada. Termo buscado: "${query}"`);
}

// Calendário de eventos
function initCalendar() {
    const calendarGrid = document.querySelector('.calendario-grid');
    const prevBtn = document.querySelector('.btn-nav.prev');
    const nextBtn = document.querySelector('.btn-nav.next');
    const calendarHeader = document.querySelector('.calendario-header h3');

    if (!calendarGrid) return;

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    const months = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    function generateCalendar(month, year) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        calendarHeader.textContent = `${months[month]} ${year}`;
        calendarGrid.innerHTML = '';

        // Dias da semana
        const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        weekDays.forEach(day => {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day-header';
            dayElement.textContent = day;
            calendarGrid.appendChild(dayElement);
        });

        // Espaços vazios antes do primeiro dia
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day empty';
            calendarGrid.appendChild(emptyDay);
        }

        // Dias do mês
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            // Destacar dia atual
            if (year === new Date().getFullYear() && 
                month === new Date().getMonth() && 
                day === new Date().getDate()) {
                dayElement.classList.add('today');
            }

            // Adicionar eventos (exemplo)
            if (day === 25 || day === 30) {
                dayElement.classList.add('has-event');
                dayElement.title = day === 25 ? 'Reunião da Diretoria' : 'Palestra sobre Sustentabilidade';
            }

            calendarGrid.appendChild(dayElement);
        }
    }

    // Event listeners para navegação
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentMonth, currentYear);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentMonth, currentYear);
        });
    }

    // Gerar calendário inicial
    generateCalendar(currentMonth, currentYear);

    // Adicionar estilos CSS para o calendário
    const calendarStyles = `
        .calendario-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: var(--cinza-medio);
            border-radius: var(--radius-md);
            overflow: hidden;
        }
        
        .calendar-day-header {
            background: var(--verde-institucional);
            color: var(--branco-puro);
            padding: var(--spacing-xs);
            text-align: center;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .calendar-day {
            background: var(--branco-puro);
            padding: var(--spacing-xs);
            text-align: center;
            cursor: pointer;
            transition: background var(--transition-normal);
            min-height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .calendar-day:hover {
            background: var(--cinza-claro);
        }
        
        .calendar-day.empty {
            cursor: default;
        }
        
        .calendar-day.today {
            background: var(--verde-amazonia);
            color: var(--branco-puro);
            font-weight: 600;
        }
        
        .calendar-day.has-event {
            background: var(--dourado-prestigio);
            color: var(--branco-puro);
            font-weight: 600;
        }
        
        .calendar-day.has-event:hover {
            background: #E65100;
        }
    `;

    // Adicionar estilos ao documento
    const styleSheet = document.createElement('style');
    styleSheet.textContent = calendarStyles;
    document.head.appendChild(styleSheet);
}

// Filtro de serviços
function initConveniosFilter() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const convenioCards = document.querySelectorAll('.convenio-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover classe active de todos os botões
            filterBtns.forEach(b => b.classList.remove('active'));
            // Adicionar classe active ao botão clicado
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            convenioCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Formulário de contato
function initContactForm() {
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulário
            if (validateForm(this)) {
                submitForm(this);
            }
        });

        // Validação em tempo real
        const inputs = contactForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    }
}

function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Remover classes de erro anteriores
    field.classList.remove('error');
    const existingError = field.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Validações específicas
    if (field.hasAttribute('required') && value === '') {
        isValid = false;
        errorMessage = 'Este campo é obrigatório.';
    } else if (field.type === 'email' && value !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Por favor, insira um e-mail válido.';
        }
    } else if (field.type === 'tel' && value !== '') {
        const phoneRegex = /^[\d\s\(\)\-\+]+$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Por favor, insira um telefone válido.';
        }
    }

    // Mostrar erro se houver
    if (!isValid) {
        field.classList.add('error');
        const errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        errorElement.textContent = errorMessage;
        field.parentElement.appendChild(errorElement);
    }

    return isValid;
}

function submitForm(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Mostrar loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    submitBtn.disabled = true;

    // Simular envio (em um site real, isso faria uma requisição ao servidor)
    setTimeout(() => {
        // Resetar botão
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Mostrar mensagem de sucesso
        showNotification('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');

        // Limpar formulário
        form.reset();
    }, 2000);
}

// Scroll suave
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetSection.offsetTop - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Sistema de notificações
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Adicionar estilos se não existirem
    if (!document.querySelector('#notification-styles')) {
        const notificationStyles = document.createElement('style');
        notificationStyles.id = 'notification-styles';
        notificationStyles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--branco-puro);
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-lg);
                padding: var(--spacing-md);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: var(--spacing-sm);
                z-index: 10000;
                max-width: 400px;
                animation: slideInRight 0.3s ease;
                border-left: 4px solid var(--verde-institucional);
            }
            
            .notification-success {
                border-left-color: #4CAF50;
            }
            
            .notification-error {
                border-left-color: #F44336;
            }
            
            .notification-content {
                display: flex;
                align-items: center;
                gap: var(--spacing-sm);
            }
            
            .notification-content i {
                font-size: 1.2rem;
            }
            
            .notification-success .notification-content i {
                color: #4CAF50;
            }
            
            .notification-error .notification-content i {
                color: #F44336;
            }
            
            .notification-close {
                background: none;
                border: none;
                cursor: pointer;
                color: var(--cinza-elegante);
                padding: var(--spacing-xs);
                border-radius: var(--radius-sm);
                transition: background var(--transition-normal);
            }
            
            .notification-close:hover {
                background: var(--cinza-claro);
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(notificationStyles);
    }

    document.body.appendChild(notification);

    // Fechar notificação
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', function() {
        closeNotification(notification);
    });

    // Auto fechar após 5 segundos
    setTimeout(() => {
        if (document.body.contains(notification)) {
            closeNotification(notification);
        }
    }, 5000);
}

function closeNotification(notification) {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => {
        if (document.body.contains(notification)) {
            document.body.removeChild(notification);
        }
    }, 300);
}

// Utilitários
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Lazy loading para imagens
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Adicionar estilos para campos com erro
const errorStyles = document.createElement('style');
errorStyles.textContent = `
    .form-group input.error,
    .form-group select.error,
    .form-group textarea.error {
        border-color: #F44336;
        background-color: rgba(244, 67, 54, 0.05);
    }
    
    .error-message {
        color: #F44336;
        font-size: 0.8rem;
        margin-top: var(--spacing-xs);
        display: block;
    }
`;
document.head.appendChild(errorStyles);



// Hero Slider Functionality
function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');
    
    let currentSlide = 0;
    let slideInterval;
    const slideDelay = 6000; // 6 segundos

    function showSlide(index) {
        // Remove active class from all slides and indicators
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Add active class to current slide and indicator
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        
        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }

    function startAutoPlay() {
        slideInterval = setInterval(nextSlide, slideDelay);
    }

    function stopAutoPlay() {
        clearInterval(slideInterval);
    }

    // Event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000); // Restart after 2 seconds
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000); // Restart after 2 seconds
        });
    }

    // Indicator clicks
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000); // Restart after 2 seconds
        });
    });

    // Pause on hover
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        heroSlider.addEventListener('mouseenter', stopAutoPlay);
        heroSlider.addEventListener('mouseleave', startAutoPlay);
    }

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000);
        }
    }

    if (heroSlider) {
        heroSlider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        heroSlider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000);
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            stopAutoPlay();
            setTimeout(startAutoPlay, 2000);
        }
    });

    // Start autoplay
    startAutoPlay();

    // Pause when page is not visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoPlay();
        } else {
            startAutoPlay();
        }
    });
}

// Enhanced scroll effects for slider
function initSliderScrollEffects() {
    const heroSlides = document.querySelectorAll('.hero-slide');
    
    if (heroSlides.length > 0) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.3;
            
            heroSlides.forEach(slide => {
                const heroImage = slide.querySelector('.hero-image');
                if (heroImage) {
                    heroImage.style.transform = `translateY(${rate}px)`;
                }
            });
        });
    }
}

// Document preview interactions
function initDocumentPreview() {
    const documentFrame = document.querySelector('.document-frame');
    
    if (documentFrame) {
        documentFrame.addEventListener('click', function() {
            // Add click effect
            this.style.transform = 'perspective(1000px) rotateY(0deg) rotateX(0deg) scale(1.02)';
            
            setTimeout(() => {
                this.style.transform = 'perspective(1000px) rotateY(-5deg) rotateX(2deg)';
            }, 200);
        });
    }
}

// Progress bar for auto-play
function initSliderProgress() {
    const heroSlider = document.querySelector('.hero-slider');
    
    if (heroSlider) {
        const progressBar = document.createElement('div');
        progressBar.className = 'slider-progress';
        heroSlider.appendChild(progressBar);
        
        let progressInterval;
        const progressDuration = 6000; // Same as slide delay
        
        function startProgress() {
            progressBar.style.width = '0%';
            progressBar.style.transition = `width ${progressDuration}ms linear`;
            
            setTimeout(() => {
                progressBar.style.width = '100%';
            }, 50);
        }
        
        function resetProgress() {
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';
        }
        
        // Start progress on page load
        startProgress();
        
        // Reset and restart progress on slide change
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach(indicator => {
            indicator.addEventListener('click', () => {
                resetProgress();
                setTimeout(startProgress, 100);
            });
        });
        
        // Reset progress every 6 seconds (when slide changes)
        setInterval(() => {
            resetProgress();
            setTimeout(startProgress, 100);
        }, progressDuration);
    }
}

// Smooth transitions between slides
function initSlideTransitions() {
    const slides = document.querySelectorAll('.hero-slide');
    
    slides.forEach((slide, index) => {
        slide.style.zIndex = slides.length - index;
    });
}

// Initialize all slider functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add slider initialization to existing DOMContentLoaded
    initHeroSlider();
    initSliderScrollEffects();
    initDocumentPreview();
    initSliderProgress();
    initSlideTransitions();
});

// Update existing scroll effects function to work with slider
function updateScrollEffects() {
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            const activeSlide = document.querySelector('.hero-slide.active');
            if (activeSlide) {
                const heroImage = activeSlide.querySelector('.hero-image');
                if (heroImage) {
                    heroImage.style.transform = `translateY(${rate}px)`;
                }
            }
        });
    }
}

