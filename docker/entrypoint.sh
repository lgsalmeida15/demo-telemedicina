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
DB_HOST=${DB_HOST:-mysql-db}
DB_DATABASE=${DB_DATABASE:-telemed_demo}
DB_USERNAME=${DB_USERNAME:-telemedicina}
DB_PASSWORD=${DB_PASSWORD}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}

echo "📋 Configuração do banco:"
echo "   DB_HOST=$DB_HOST"
echo "   DB_DATABASE=$DB_DATABASE"
echo "   DB_USERNAME=$DB_USERNAME"
echo "   DB_PASSWORD=${DB_PASSWORD:+***definida***}"
echo "   DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD:+***definida***}"

MAX_ATTEMPTS=60
ATTEMPT=0

# Verificar resolução DNS
echo "🔍 Verificando resolução DNS do host '$DB_HOST'..."
if command -v nslookup > /dev/null 2>&1; then
    nslookup "$DB_HOST" 2>&1 | head -5 || echo "⚠️  Não foi possível resolver '$DB_HOST'"
fi

# Primeiro, aguardar MySQL aceitar conexões (sem especificar banco)
echo "⏳ Aguardando MySQL aceitar conexões em '$DB_HOST:3306'..."
until php -r "
try {
    \$host = getenv('DB_HOST');
    \$rootPass = getenv('DB_ROOT_PASSWORD');
    if (empty(\$rootPass)) {
        error_log('ERRO: DB_ROOT_PASSWORD não está definida!');
        exit(1);
    }
    \$dsn = 'mysql:host='.\$host.';port=3306';
    \$pdo = new PDO(\$dsn, 'root', \$rootPass, [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_CONNECT_TIMEOUT => 3
    ]);
    exit(0);
} catch(PDOException \$e) {
    exit(1);
} catch(Exception \$e) {
    exit(1);
}
" 2>&1 | grep -E "(ERRO|Erro)" || true; do
    ATTEMPT=$((ATTEMPT + 1))
    if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
        echo "❌ Timeout: MySQL não está respondendo após $MAX_ATTEMPTS tentativas"
        echo "🔍 Tentando diagnóstico final..."
        php -r "
        \$host = getenv('DB_HOST');
        \$rootPass = getenv('DB_ROOT_PASSWORD');
        echo 'Tentando conectar em: ' . \$host . ':3306' . PHP_EOL;
        echo 'Usando senha root: ' . (empty(\$rootPass) ? 'NÃO DEFINIDA!' : '***definida***') . PHP_EOL;
        try {
            \$pdo = new PDO('mysql:host='.\$host.';port=3306', 'root', \$rootPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            echo '✅ Conexão bem-sucedida!' . PHP_EOL;
        } catch(PDOException \$e) {
            echo '❌ Erro: ' . \$e->getMessage() . PHP_EOL;
        }
        " 2>&1 | grep -v "PHP" || true
        exit 1
    fi
    if [ $((ATTEMPT % 5)) -eq 0 ]; then
        echo "⏳ Tentativa $ATTEMPT/$MAX_ATTEMPTS: MySQL não está aceitando conexões - aguardando..."
    fi
    sleep 2
done
echo "✅ MySQL está aceitando conexões!"

# Aguardar banco específico estar disponível
echo "⏳ Aguardando banco '$DB_DATABASE' estar disponível..."
ATTEMPT=0
until php -r "
try {
    \$host = getenv('DB_HOST');
    \$db = getenv('DB_DATABASE');
    \$user = getenv('DB_USERNAME');
    \$pass = getenv('DB_PASSWORD');
    \$dsn = 'mysql:host='.\$host.';port=3306;dbname='.\$db;
    \$pdo = new PDO(\$dsn, \$user, \$pass, [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    exit(0);
} catch(PDOException \$e) {
    exit(1);
}
" 2>/dev/null; do
    ATTEMPT=$((ATTEMPT + 1))
    if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
        echo "❌ Timeout: Banco '$DB_DATABASE' não está disponível após $MAX_ATTEMPTS tentativas"
        echo "   Verifique se DB_HOST=$DB_HOST, DB_DATABASE=$DB_DATABASE, DB_USERNAME=$DB_USERNAME estão corretos"
        # Tentar mostrar erro real
        php -r "
        try {
            \$pdo = new PDO('mysql:host='.getenv('DB_HOST').';port=3306;dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch(PDOException \$e) {
            echo 'Erro: ' . \$e->getMessage() . PHP_EOL;
        }
        " 2>&1 | grep -v "PHP" || true
        exit 1
    fi
    if [ $((ATTEMPT % 5)) -eq 0 ]; then
        echo "⏳ Tentativa $ATTEMPT/$MAX_ATTEMPTS: Banco não está pronto - aguardando..."
    fi
    sleep 2
done
echo "✅ Banco de dados '$DB_DATABASE' está pronto!"

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

# Executar migrations (apenas se flag estiver ativa)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "📊 Executando migrations..."
    php artisan migrate --force
    
    # Executar seeders se flag estiver ativa
    if [ "$RUN_SEEDERS" = "true" ]; then
        echo "🌱 Executando seeders..."
        php artisan db:seed --force
    fi
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