#!/bin/sh

set -e

echo "🚀 Iniciando aplicação Telemedicina..."

# Aguardar banco de dados
echo "⏳ Aguardando banco de dados..."
until php -r "try { new PDO('mysql:host=db;dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch(Exception \$e) { exit(1); }" &> /dev/null 2>&1; do
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

# Cache de configuração para performance
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Otimizando para produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
fi

# Corrigir permissões (se rodando como root)
if [ "$(id -u)" = "0" ]; then
    echo "🔐 Configurando permissões..."
    chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
else
    echo "⚠️  Rodando como usuário não-root, pulando configuração de permissões"
fi

echo "✅ Inicialização completa!"
echo "🌐 Aplicação pronta para receber requisições"

# Executar comando passado como argumento
exec "$@"