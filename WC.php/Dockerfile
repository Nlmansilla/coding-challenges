FROM php:8.5-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Configuración de PHP para desarrollo
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-dev.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-dev.ini

# Mantener el contenedor activo
CMD ["php", "-a"]