# =============================================================================
# BASE STAGE - Optimized PHP-CLI for Laravel workers
# =============================================================================
FROM php:8.4-cli AS base

# Installer les dépendances système minimales
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    procps \
    libssl-dev \
    pkg-config \
    libpq-dev \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

# Configurer la timezone Europe/Paris
RUN ln -snf /usr/share/zoneinfo/Europe/Paris /etc/localtime \
    && echo "Europe/Paris" > /etc/timezone

# Installer les extensions PHP pour Laravel (sans serveur web)
RUN docker-php-ext-install pdo pdo_pgsql pcntl posix bcmath

# Installer Redis et MongoDB via PECL
RUN pecl install redis mongodb \
    && docker-php-ext-enable redis mongodb

# Configuration PHP timezone
RUN echo "date.timezone=Europe/Paris" >> /usr/local/etc/php/conf.d/timezone.ini

# Copier Composer depuis l'image officielle
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Configuration PHP pour CLI (optimisations)
RUN echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/cli-optimizations.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/cli-optimizations.ini

WORKDIR /app

# =============================================================================
# DEVELOPMENT STAGE - With Xdebug for CLI debugging
# =============================================================================
FROM base AS development

# Installer Xdebug pour le développement (CLI debugging)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuration Xdebug pour CLI debugging
RUN echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# =============================================================================
# PRODUCTION STAGE - Optimized CLI without debug tools
# =============================================================================
FROM base AS production

# Configuration PHP pour production CLI
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Optimisations production pour CLI
RUN echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini