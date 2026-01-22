#!/bin/bash

set -e

echo "=== Configurando Laravel para HTTPS ==="

# Forçar URLs HTTPS (sobrescreve qualquer configuração do .env)
export APP_URL="${APP_URL:-https://demo-telemedicina.otmiz.tech}"
export ASSET_URL="${ASSET_URL:-${APP_URL}}"
export APP_FORCE_HTTPS=true

# Atualizar .env com URLs HTTPS e configurações de proxy
if [ -f /var/www/html/.env ]; then
    sed -i 's|^APP_URL=.*|APP_URL='"$APP_URL"'|g' /var/www/html/.env
    sed -i 's|^ASSET_URL=.*|ASSET_URL='"$ASSET_URL"'|g' /var/www/html/.env || echo "ASSET_URL=$ASSET_URL" >> /var/www/html/.env
    
    # Configurar Laravel para confiar em proxies (Traefik)
    grep -q "^TRUSTED_PROXIES=" /var/www/html/.env && sed -i 's|^TRUSTED_PROXIES=.*|TRUSTED_PROXIES=*|g' /var/www/html/.env || echo "TRUSTED_PROXIES=*" >> /var/www/html/.env
fi

echo "🚀 Iniciando aplicação Telemedicina..."

# Aguardar banco de dados
echo "⏳ Aguardando banco de dados..."
DB_HOST=${DB_HOST:-telemedicina-db}
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch(Exception \$e) { exit(1); }" &> /dev/null 2>&1; do
    echo "⏳ Banco de dados não está pronto - aguardando..."
    sleep 2
done
echo "✅ Banco de dados está pronto!"

# Verificar se APP_KEY está configurado
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:SEU_APP_KEY_AQUI" ]; then
    echo "⚠️  Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Criar storage link se não existir
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando storage link..."
    php artisan storage:link
fi

# Executar migrations (apenas se APP_ENV=production e existir flag)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📊 Executando migrations..."
    php artisan migrate --force
fi

# Limpar e recriar caches com as novas configurações
echo "=== Limpando caches ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== Recriando caches ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Corrigir permissões (se rodando como root)
if [ "$(id -u)" = "0" ]; then
    echo "🔐 Configurando permissões..."
    chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
fi

echo "✅ Inicialização completa!"
echo "🌐 Aplicação pronta para receber requisições"

# Executar comando passado como argumento
exec "$@"