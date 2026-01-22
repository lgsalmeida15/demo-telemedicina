#!/bin/bash

# ============================================
# Script de Build - Appboxfarma Telemedicina
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
echo "  🚀 Build - Appboxfarma Telemedicina"
echo "============================================"
echo ""

# Verificar se Docker está instalado
log_info "Verificando dependências..."
if ! command -v docker &> /dev/null; then
    log_error "Docker não está instalado!"
    exit 1
fi

log_success "Docker está instalado ✓"

# Verificar se arquivo .env existe
if [ ! -f .env ]; then
    log_warning "Arquivo .env não encontrado!"
    log_info "Copiando .env.example para .env..."
    if [ -f .env.example ]; then
        cp .env.example .env
        log_warning "⚠️  IMPORTANTE: Configure o arquivo .env antes de continuar!"
        log_warning "Pressione ENTER após configurar o .env ou CTRL+C para cancelar"
        read
    else
        log_error "Arquivo .env.example não encontrado!"
        exit 1
    fi
fi

# Perguntar sobre rebuild
echo ""
log_info "Opções de build:"
echo "1) Build completo (sem cache)"
echo "2) Build rápido (com cache)"
read -p "Escolha [1-2]: " build_mode

# Build da imagem
if [ "$build_mode" = "1" ]; then
    log_info "Construindo imagem Docker (sem cache)..."
    docker build --no-cache -t telemedicina:latest --build-arg APP_ENV=production -f Dockerfile .
elif [ "$build_mode" = "2" ]; then
    log_info "Construindo imagem Docker (com cache)..."
    docker build -t telemedicina:latest --build-arg APP_ENV=production -f Dockerfile .
else
    log_error "Opção inválida!"
    exit 1
fi

log_success "Build concluído ✓"

echo ""
log_info "Verificando imagem criada..."
docker images | grep telemedicina

echo ""
log_success "============================================"
log_success "  ✅ Build finalizado com sucesso!"
log_success "============================================"
echo ""
log_info "📋 Próximos passos:"
echo ""
log_info "1. Acesse o Portainer"
echo "2. Vá em Stacks > Add stack"
echo "3. Nome: telemedicina"
echo "4. Cole o conteúdo do arquivo docker-compose.yaml"
echo "5. Clique em 'Deploy the stack'"
echo ""
log_info "A imagem 'telemedicina:latest' está pronta para uso! 🎉"
