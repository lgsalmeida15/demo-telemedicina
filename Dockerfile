# ============================================
# STAGE 1: Build Assets (Node.js)
# ============================================
FROM node:18-alpine AS assets-builder

WORKDIR /app

# Copiar apenas arquivos de dependências primeiro (cache layer)
COPY package*.json ./

# Instalar dependências
RUN npm ci --production=false

# Copiar arquivos necessários para build
COPY resources/ resources/
COPY webpack.mix.js ./
COPY public/ public/

# Build de assets para produção
RUN npm run production || true

# Garantir que mix-manifest.json existe (criar vazio se não existir)
RUN if [ ! -f /app/public/mix-manifest.json ]; then \
        echo '{}' > /app/public/mix-manifest.json; \
    fi

# ============================================
# STAGE 2: PHP Dependencies
# ============================================
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copiar arquivos do composer
COPY composer.json composer.lock ./

# Instalar dependências (sem dev)
# Ignorar requisitos de plataforma que serão instalados no stage final
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-intl

# Copiar resto do código
COPY . .

# Gerar autoloader otimizado
RUN composer dump-autoload --optimize --no-dev

# ============================================
# STAGE 3: Production Image
# ============================================
FROM php:8.3-fpm-alpine

# Metadados
LABEL maintainer="Appboxfarma"
LABEL description="Demo Telemedicina - Laravel Application"

# Instalar dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        intl \
        mbstring \
        opcache \
        bcmath \
    && rm -rf /var/cache/apk/*

# Instalar extensão Redis (opcional mas recomendado)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Configurações PHP otimizadas
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configuração PHP-FPM
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Criar usuário não-root
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar código da aplicação
COPY --from=composer-builder --chown=www:www /app /var/www/html

# Copiar assets compilados
COPY --from=assets-builder --chown=www:www /app/public/css /var/www/html/public/css
COPY --from=assets-builder --chown=www:www /app/public/js /var/www/html/public/js
COPY --from=assets-builder --chown=www:www /app/public/mix-manifest.json /var/www/html/public/mix-manifest.json

# Configuração do Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configuração do Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Criar diretórios necessários e configurar permissões
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/log/supervisor \
    /var/log/php \
    /var/log/nginx \
    /var/run \
    && chown -R www:www /var/www/html \
    && chown -R www:www /var/log/php \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 /var/www/html \
    && chmod -R 755 /var/log/php

# Script de entrada
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expor porta
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Usuário padrão
# USER www  # Supervisor precisa rodar como root

# Comando de inicialização
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]