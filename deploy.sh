#!/bin/bash

# ============================================
# Script de Deploy - Appboxfarma Telemedicina
# ============================================

set -e  # Parar execução em caso de erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funções auxiliares
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Banner
echo "============================================"
echo "  🚀 Deploy - Appboxfarma Telemedicina"
echo "============================================"
echo ""

# Verificar se Docker e Docker Compose estão instalados
log_info "Verificando dependências..."
if ! command -v docker &> /dev/null; then
    log_error "Docker não está instalado!"
    exit 1
fi

# Detectar qual versão do Docker Compose está disponível
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
else
    log_error "Docker Compose não está instalado!"
    exit 1
fi

log_success "Docker e Docker Compose estão instalados ✓"
log_info "Usando: $DOCKER_COMPOSE"

# Verificar se arquivo .env existe
if [ ! -f .env ]; then
    log_warning "Arquivo .env não encontrado!"
    log_info "Copiando .env.example para .env..."
    cp .env.example .env
    log_warning "⚠️  IMPORTANTE: Configure o arquivo .env antes de continuar!"
    log_warning "Pressione ENTER após configurar o .env ou CTRL+C para cancelar"
    read
fi

# Perguntar modo de deploy
echo ""
log_info "Selecione o modo de deploy:"
echo "1) Deploy completo (primeira vez ou rebuild)"
echo "2) Deploy rápido (apenas restart)"
echo "3) Deploy com migrations"
read -p "Escolha [1-3]: " deploy_mode

# Deploy completo
if [ "$deploy_mode" = "1" ]; then
    log_info "Iniciando deploy completo..."
    
    # Parar containers existentes
    log_info "Parando containers existentes..."
    $DOCKER_COMPOSE down -v 2>/dev/null || true
    
    # Limpar volumes órfãos (opcional)
    read -p "Deseja limpar volumes antigos? (y/n): " clean_volumes
    if [ "$clean_volumes" = "y" ]; then
        log_warning "Removendo volumes antigos..."
        docker volume prune -f
    fi
    
    # Build das imagens
    log_info "Construindo imagens Docker..."
    $DOCKER_COMPOSE build --no-cache
    log_success "Build concluído ✓"
    
    # Subir serviços
    log_info "Iniciando containers..."
    $DOCKER_COMPOSE up -d
    
    # Aguardar banco de dados
    log_info "Aguardando banco de dados estar pronto..."
    sleep 15
    
    # Executar migrations
    log_info "Executando migrations..."
    $DOCKER_COMPOSE exec -T app php artisan migrate --force
    
    # Perguntar sobre seeders
    read -p "Deseja executar seeders? (y/n): " run_seeders
    if [ "$run_seeders" = "y" ]; then
        log_info "Executando seeders..."
        $DOCKER_COMPOSE exec -T app php artisan db:seed --force
    fi
    
    # Criar storage link
    log_info "Criando storage link..."
    $DOCKER_COMPOSE exec -T app php artisan storage:link
    
    # Otimizações
    log_info "Executando otimizações..."
    $DOCKER_COMPOSE exec -T app php artisan config:cache
    $DOCKER_COMPOSE exec -T app php artisan route:cache
    $DOCKER_COMPOSE exec -T app php artisan view:cache
    $DOCKER_COMPOSE exec -T app php artisan optimize
    
    log_success "Deploy completo finalizado! ✓"

# Deploy rápido
elif [ "$deploy_mode" = "2" ]; then
    log_info "Iniciando deploy rápido..."
    
    # Pull da última versão (se usar registry)
    # docker-compose pull
    
    # Restart dos containers
    log_info "Reiniciando containers..."
    $DOCKER_COMPOSE restart
    
    # Limpar caches
    log_info "Limpando caches..."
    $DOCKER_COMPOSE exec -T app php artisan cache:clear
    $DOCKER_COMPOSE exec -T app php artisan config:cache
    $DOCKER_COMPOSE exec -T app php artisan route:cache
    $DOCKER_COMPOSE exec -T app php artisan view:cache
    
    log_success "Deploy rápido finalizado! ✓"

# Deploy com migrations
elif [ "$deploy_mode" = "3" ]; then
    log_info "Iniciando deploy com migrations..."
    
    # Build e up
    log_info "Atualizando containers..."
    $DOCKER_COMPOSE up -d --build
    
    # Aguardar
    log_info "Aguardando containers..."
    sleep 10
    
    # Migrations
    log_info "Executando migrations..."
    $DOCKER_COMPOSE exec -T app php artisan migrate --force
    
    # Otimizações
    log_info "Executando otimizações..."
    $DOCKER_COMPOSE exec -T app php artisan config:cache
    $DOCKER_COMPOSE exec -T app php artisan route:cache
    $DOCKER_COMPOSE exec -T app php artisan view:cache
    $DOCKER_COMPOSE exec -T app php artisan optimize
    
    log_success "Deploy com migrations finalizado! ✓"
else
    log_error "Opção inválida!"
    exit 1
fi

echo ""
log_info "Verificando status dos containers..."
$DOCKER_COMPOSE ps

echo ""
log_info "Verificando logs (últimas 20 linhas)..."
$DOCKER_COMPOSE logs --tail=20 app

echo ""
log_success "============================================"
log_success "  ✅ Deploy finalizado com sucesso!"
log_success "============================================"
echo ""
log_info "Acesse a aplicação em: http://localhost:8000"
log_info "Ou configure seu domínio no .env (APP_URL)"
echo ""
log_info "Comandos úteis:"
echo "  - Ver logs: $DOCKER_COMPOSE logs -f app"
echo "  - Entrar no container: $DOCKER_COMPOSE exec app sh"
echo "  - Parar aplicação: $DOCKER_COMPOSE down"
echo "  - Ver status: $DOCKER_COMPOSE ps"
echo ""

# Verificar health check
log_info "Verificando health check..."
sleep 5
if curl -f http://localhost:8000/health &> /dev/null; then
    log_success "Health check OK! ✓"
else
    log_warning "Health check falhou. Verifique os logs."
    log_info "Execute: $DOCKER_COMPOSE logs app"
fi

echo ""
log_info "🎉 Deploy concluído!"