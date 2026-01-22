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
    
    # Remover stack existente (se houver)
    log_info "Removendo stack existente (se houver)..."
    docker stack rm telemedicina 2>/dev/null || true
    sleep 5
    
    # Limpar volumes órfãos (opcional)
    read -p "Deseja limpar volumes antigos? (y/n): " clean_volumes
    if [ "$clean_volumes" = "y" ]; then
        log_warning "Removendo volumes antigos..."
        docker volume prune -f
    fi
    
    # Build da imagem
    log_info "Construindo imagem Docker..."
    docker build -t telemedicina:latest --build-arg APP_ENV=production -f Dockerfile .
    log_success "Build concluído ✓"
    
    # Subir serviços como stack do Swarm
    log_info "Deployando stack no Swarm..."
    docker stack deploy -c docker-compose.yaml telemedicina
    log_success "Stack deployada ✓"
    
    # Aguardar serviços estarem prontos
    log_info "Aguardando serviços estarem prontos..."
    sleep 20
    
    # Obter nome do container do serviço
    APP_CONTAINER=$(docker ps --filter "name=telemedicina_telemedicina" --format "{{.Names}}" | head -n 1)
    
    if [ -z "$APP_CONTAINER" ]; then
        log_warning "Container da aplicação não encontrado. Aguardando mais tempo..."
        sleep 10
        APP_CONTAINER=$(docker ps --filter "name=telemedicina_telemedicina" --format "{{.Names}}" | head -n 1)
    fi
    
    if [ ! -z "$APP_CONTAINER" ]; then
        # Executar migrations
        log_info "Executando migrations..."
        docker exec $APP_CONTAINER php artisan migrate --force
        
        # Perguntar sobre seeders
        read -p "Deseja executar seeders? (y/n): " run_seeders
        if [ "$run_seeders" = "y" ]; then
            log_info "Executando seeders..."
            docker exec $APP_CONTAINER php artisan db:seed --force
        fi
        
        # Criar storage link
        log_info "Criando storage link..."
        docker exec $APP_CONTAINER php artisan storage:link || true
        
        # Otimizações
        log_info "Executando otimizações..."
        docker exec $APP_CONTAINER php artisan config:cache
        docker exec $APP_CONTAINER php artisan route:cache
        docker exec $APP_CONTAINER php artisan view:cache
        docker exec $APP_CONTAINER php artisan optimize
    else
        log_warning "Não foi possível encontrar o container. Execute manualmente:"
        log_info "docker exec <container_name> php artisan migrate --force"
    fi
    
    log_success "Deploy completo finalizado! ✓"

# Deploy rápido
elif [ "$deploy_mode" = "2" ]; then
    log_info "Iniciando deploy rápido..."
    
    # Atualizar stack
    log_info "Atualizando stack..."
    docker stack deploy -c docker-compose.yaml telemedicina
    
    # Obter container
    sleep 10
    APP_CONTAINER=$(docker ps --filter "name=telemedicina_telemedicina" --format "{{.Names}}" | head -n 1)
    
    if [ ! -z "$APP_CONTAINER" ]; then
        # Limpar caches
        log_info "Limpando caches..."
        docker exec $APP_CONTAINER php artisan cache:clear
        docker exec $APP_CONTAINER php artisan config:cache
        docker exec $APP_CONTAINER php artisan route:cache
        docker exec $APP_CONTAINER php artisan view:cache
    fi
    
    log_success "Deploy rápido finalizado! ✓"

# Deploy com migrations
elif [ "$deploy_mode" = "3" ]; then
    log_info "Iniciando deploy com migrations..."
    
    # Build da imagem
    log_info "Construindo imagem Docker..."
    docker build -t telemedicina:latest --build-arg APP_ENV=production -f Dockerfile .
    
    # Atualizar stack
    log_info "Atualizando stack..."
    docker stack deploy -c docker-compose.yaml telemedicina
    
    # Aguardar
    log_info "Aguardando serviços..."
    sleep 15
    
    # Obter container
    APP_CONTAINER=$(docker ps --filter "name=telemedicina_telemedicina" --format "{{.Names}}" | head -n 1)
    
    if [ ! -z "$APP_CONTAINER" ]; then
        # Migrations
        log_info "Executando migrations..."
        docker exec $APP_CONTAINER php artisan migrate --force
        
        # Otimizações
        log_info "Executando otimizações..."
        docker exec $APP_CONTAINER php artisan config:cache
        docker exec $APP_CONTAINER php artisan route:cache
        docker exec $APP_CONTAINER php artisan view:cache
        docker exec $APP_CONTAINER php artisan optimize
    fi
    
    log_success "Deploy com migrations finalizado! ✓"
else
    log_error "Opção inválida!"
    exit 1
fi

echo ""
log_info "Verificando status dos serviços..."
docker stack services telemedicina

echo ""
log_info "Verificando containers..."
docker ps --filter "name=telemedicina" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
log_info "Verificando logs (últimas 20 linhas)..."
APP_CONTAINER=$(docker ps --filter "name=telemedicina_telemedicina" --format "{{.Names}}" | head -n 1)
if [ ! -z "$APP_CONTAINER" ]; then
    docker logs --tail=20 $APP_CONTAINER
fi

echo ""
log_success "============================================"
log_success "  ✅ Deploy finalizado com sucesso!"
log_success "============================================"
echo ""
log_info "Acesse a aplicação em: https://demo-telemedicina.otmiz.tech"
echo ""
log_info "Comandos úteis:"
echo "  - Ver serviços: docker stack services telemedicina"
echo "  - Ver logs: docker service logs telemedicina_telemedicina -f"
echo "  - Entrar no container: docker exec -it <container_name> sh"
echo "  - Parar stack: docker stack rm telemedicina"
echo "  - Ver status: docker stack ps telemedicina"
echo ""

# Verificar health check
log_info "Verificando health check..."
sleep 10
if curl -L -k -f https://demo-telemedicina.otmiz.tech/health &> /dev/null; then
    log_success "Health check OK! ✓"
else
    log_warning "Health check falhou. Verifique os logs."
    log_info "Execute: docker service logs telemedicina_telemedicina"
fi

echo ""
log_info "🎉 Deploy concluído!"