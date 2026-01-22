#!/bin/bash

set -e

echo "=== Configurando Laravel para HTTPS ==="

# Forçar URLs HTTPS (sobrescreve qualquer configuração do .env)
export APP_URL="${APP_URL:-https://demo-telemedicina.otmiz.tech}"
export ASSET_URL="${ASSET_URL:-${APP_URL}}"
export APP_FORCE_HTTPS=true

# Função para atualizar .env
update_env_file() {
    if [ ! -f /var/www/html/.env ]; then
        return
    fi
    
    # Atualizar URLs HTTPS
    sed -i 's|^APP_URL=.*|APP_URL='"$APP_URL"'|g' /var/www/html/.env
    sed -i 's|^ASSET_URL=.*|ASSET_URL='"$ASSET_URL"'|g' /var/www/html/.env || echo "ASSET_URL=$ASSET_URL" >> /var/www/html/.env
    
    # Atualizar configurações de banco
    sed -i 's|^DB_HOST=.*|DB_HOST='"${DB_HOST:-mysql-db}"'|g' /var/www/html/.env
    sed -i 's|^DB_DATABASE=.*|DB_DATABASE='"${DB_DATABASE:-telemed_demo}"'|g' /var/www/html/.env
    sed -i 's|^DB_USERNAME=.*|DB_USERNAME='"${DB_USERNAME:-root}"'|g' /var/www/html/.env
    [ -n "$DB_PASSWORD" ] && sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='"$DB_PASSWORD"'|g' /var/www/html/.env
    
    # Configurar Laravel para confiar em proxies (Traefik)
    grep -q "^TRUSTED_PROXIES=" /var/www/html/.env && sed -i 's|^TRUSTED_PROXIES=.*|TRUSTED_PROXIES=*|g' /var/www/html/.env || echo "TRUSTED_PROXIES=*" >> /var/www/html/.env
}

# Atualizar .env se existir (antes de criar novo)
update_env_file

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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
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

# Criar banco usando root
echo "🔧 Verificando/Criando banco '$DB_DATABASE'..."
php <<'ENDPHP'
<?php
try {
    $host = getenv("DB_HOST");
    $rootPass = getenv("DB_ROOT_PASSWORD");
    $db = getenv("DB_DATABASE");
    
    if (empty($host) || empty($rootPass) || empty($db)) {
        throw new Exception("Variáveis de ambiente não definidas");
    }
    
    // Conectar como root
    $pdo = new PDO("mysql:host=".$host.";port=3306", "root", $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `".$db."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco criado/verificado: " . $db . "\n";
    
} catch(PDOException $e) {
    echo "❌ Erro ao criar banco: " . $e->getMessage() . "\n";
    exit(1);
} catch(Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
ENDPHP

# Verificar conexão com banco usando root
echo "⏳ Verificando conexão com banco '$DB_DATABASE' usando root..."
ATTEMPT=0
until php -r "
try {
    \$host = getenv('DB_HOST');
    \$rootPass = getenv('DB_ROOT_PASSWORD');
    \$db = getenv('DB_DATABASE');
    \$dsn = 'mysql:host='.\$host.';port=3306;dbname='.\$db;
    \$pdo = new PDO(\$dsn, 'root', \$rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    exit(0);
} catch(PDOException \$e) {
    exit(1);
}
" 2>/dev/null; do
    ATTEMPT=$((ATTEMPT + 1))
    if [ $ATTEMPT -ge 10 ]; then
        echo "❌ Timeout: Não foi possível conectar ao banco '$DB_DATABASE' após 10 tentativas"
        echo "🔍 Tentando diagnóstico..."
        php -r "
        try {
            \$host = getenv('DB_HOST');
            \$rootPass = getenv('DB_ROOT_PASSWORD');
            \$db = getenv('DB_DATABASE');
            \$dsn = 'mysql:host='.\$host.';port=3306;dbname='.\$db;
            \$pdo = new PDO(\$dsn, 'root', \$rootPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            echo '✅ Conexão bem-sucedida!' . PHP_EOL;
        } catch(PDOException \$e) {
            echo '❌ Erro: ' . \$e->getMessage() . PHP_EOL;
        }
        " 2>&1 | grep -v "PHP" || true
        exit 1
    fi
    if [ $((ATTEMPT % 3)) -eq 0 ]; then
        echo "⏳ Tentativa $ATTEMPT/10: Aguardando conexão com banco..."
    fi
    sleep 1
done
echo "✅ Banco de dados '$DB_DATABASE' está pronto e acessível!"

# Criar .env se não existir
if [ ! -f /var/www/html/.env ]; then
    echo "📝 Criando arquivo .env..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
        echo "✅ Arquivo .env criado a partir do .env.example"
    else
        # Criar .env básico com variáveis de ambiente
        cat > /var/www/html/.env <<EOF
APP_NAME=${APP_NAME:-Telemedicina}
APP_ENV=${APP_ENV:-production}
APP_KEY=
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://demo-telemedicina.otmiz.tech}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-mysql-db}
DB_PORT=3306
DB_DATABASE=${DB_DATABASE:-telemed_demo}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD}

CACHE_DRIVER=${CACHE_DRIVER:-redis}
SESSION_DRIVER=${SESSION_DRIVER:-redis}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
REDIS_HOST=${REDIS_HOST:-telemedicina-redis}
REDIS_PORT=6379
REDIS_PASSWORD=${REDIS_PASSWORD:-null}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-info}
EOF
        echo "✅ Arquivo .env criado com configurações básicas"
    fi
fi

# Atualizar .env com variáveis de ambiente do Docker
update_env_file

# Verificar e gerar APP_KEY se necessário (CRÍTICO - deve ser feito ANTES de qualquer operação Laravel)
if [ -f /var/www/html/.env ]; then
    APP_KEY_ENV=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2- | tr -d ' ')
    if [ -z "$APP_KEY_ENV" ] || [ "$APP_KEY_ENV" = "" ] || [ "$APP_KEY_ENV" = "null" ] || [ "$APP_KEY_ENV" = "base64:SEU_APP_KEY_AQUI" ]; then
        echo "⚠️  APP_KEY não encontrado ou inválido no .env - gerando..."
        
        # Limpar cache antes de gerar
        php artisan config:clear 2>/dev/null || true
        
        # Gerar APP_KEY
        if php artisan key:generate --force 2>&1; then
            echo "✅ APP_KEY gerado com sucesso"
        else
            echo "❌ Erro ao gerar APP_KEY com artisan, tentando método alternativo..."
            # Método alternativo: gerar diretamente
            NEW_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
            if grep -q "^APP_KEY=" /var/www/html/.env; then
                sed -i "s|^APP_KEY=.*|APP_KEY=$NEW_KEY|" /var/www/html/.env
            else
                echo "APP_KEY=$NEW_KEY" >> /var/www/html/.env
            fi
            echo "✅ APP_KEY gerado manualmente e adicionado ao .env"
        fi
        
        # Verificar se foi salvo corretamente
        APP_KEY_NEW=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2- | tr -d ' ')
        if [ -z "$APP_KEY_NEW" ] || [ "$APP_KEY_NEW" = "" ]; then
            echo "❌ ERRO CRÍTICO: APP_KEY não foi salvo no .env!"
            exit 1
        else
            echo "✅ APP_KEY confirmado no .env: ${APP_KEY_NEW:0:20}..."
        fi
        
        # Limpar cache novamente após gerar
        php artisan config:clear 2>/dev/null || true
    else
        echo "✅ APP_KEY já está configurado no .env"
    fi
else
    echo "❌ ERRO: Arquivo .env não existe! Deve ter sido criado anteriormente."
    exit 1
fi

# Criar storage link se não existir
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando storage link..."
    php artisan storage:link
fi

# Executar migrations (sempre, a menos que explicitamente desabilitado)
if [ "$RUN_MIGRATIONS" != "false" ]; then
    echo "📊 Executando migrations..."
    if php artisan migrate --force; then
        echo "✅ Migrations executadas com sucesso!"
    else
        echo "⚠️  Erro ao executar migrations (continuando mesmo assim)..."
    fi
    
    # Executar seeders se flag estiver ativa (padrão: true)
    if [ "$RUN_SEEDERS" != "false" ]; then
        echo "🌱 Executando seeders..."
        if php artisan db:seed --force; then
            echo "✅ Seeders executados com sucesso!"
        else
            echo "⚠️  Erro ao executar seeders (continuando mesmo assim)..."
        fi
    else
        echo "⏭️  Seeders desabilitados (RUN_SEEDERS=false)"
    fi
else
    echo "⏭️  Migrations desabilitadas (RUN_MIGRATIONS=false)"
fi

# Limpar e recriar caches com as novas configurações
echo "=== Limpando caches ==="
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "=== Recriando caches ==="
php artisan config:cache || echo "⚠️  Erro ao criar cache de configuração"
php artisan route:cache || echo "⚠️  Erro ao criar cache de rotas"
php artisan view:cache || echo "⚠️  Erro ao criar cache de views"
php artisan optimize || echo "⚠️  Erro ao otimizar aplicação"

# Corrigir permissões (se rodando como root)
if [ "$(id -u)" = "0" ]; then
    echo "🔐 Configurando permissões..."
    chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # Garantir que os logs sejam acessíveis
    touch /var/www/html/storage/logs/laravel.log
    chown www:www /var/www/html/storage/logs/laravel.log
    chmod 664 /var/www/html/storage/logs/laravel.log
fi

# Verificar se há erros de sintaxe PHP
echo "🔍 Verificando sintaxe PHP..."
if php -l /var/www/html/public/index.php > /dev/null 2>&1; then
    echo "✅ Sintaxe PHP OK"
else
    echo "⚠️  Erro de sintaxe PHP detectado!"
    php -l /var/www/html/public/index.php
fi

# Testar se o Laravel está respondendo
echo "🔍 Testando resposta do Laravel..."
if php -r "
try {
    require '/var/www/html/vendor/autoload.php';
    \$app = require_once '/var/www/html/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);
    echo '✅ Laravel carregado com sucesso' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Erro ao carregar Laravel: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
" 2>&1; then
    echo "✅ Laravel está funcionando corretamente"
else
    echo "⚠️  Erro ao carregar Laravel - verifique os logs"
fi

# Testar rota raiz (apenas verificar se não há erro fatal)
echo "🔍 Testando rota raiz..."
php -r "
try {
    require '/var/www/html/vendor/autoload.php';
    \$app = require_once '/var/www/html/bootstrap/app.php';
    \$request = Illuminate\Http\Request::create('/', 'GET');
    \$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);
    \$response = \$kernel->handle(\$request);
    \$status = \$response->getStatusCode();
    echo 'Status: ' . \$status . PHP_EOL;
    if (\$status === 200) {
        echo '✅ Rota raiz funcionando corretamente' . PHP_EOL;
    } else {
        echo '⚠️  Status ' . \$status . ' - verifique os logs do Laravel para detalhes' . PHP_EOL;
    }
} catch (Throwable \$e) {
    echo '❌ Erro fatal: ' . \$e->getMessage() . PHP_EOL;
    echo 'Arquivo: ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
    exit(1);
}
" 2>&1 | head -10

echo "✅ Inicialização completa!"
echo "🌐 Aplicação pronta para receber requisições"
echo ""
echo "📋 Informações úteis:"
echo "   - Logs do Laravel: /var/www/html/storage/logs/laravel.log"
echo "   - Logs do Nginx: /var/log/nginx/error.log"
echo "   - Logs do PHP: /var/log/php/error.log"
echo "   - Para ver logs em tempo real: docker exec <container> tail -f /var/www/html/storage/logs/laravel.log"
echo ""
echo "🔍 Verificando últimos erros do Laravel..."
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    echo "   Últimas 30 linhas do log do Laravel:"
    tail -n 30 /var/www/html/storage/logs/laravel.log 2>/dev/null | grep -A 20 -B 5 "ERROR\|Exception\|Error\|Fatal" || tail -n 30 /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "   (log vazio ou inacessível)"
else
    echo "   Arquivo de log ainda não foi criado"
fi
echo ""
echo "🔍 Verificando erros do PHP..."
if [ -f /var/log/php/error.log ]; then
    echo "   Últimas 5 linhas do log do PHP:"
    tail -n 5 /var/log/php/error.log 2>/dev/null || echo "   (log vazio ou inacessível)"
else
    echo "   Arquivo de log ainda não foi criado"
fi

# Executar comando passado como argumento
exec "$@"