FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    pcntl \
    zip \
    exif

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar todo el proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --optimize-autoloader

# Instalar dependencias de Node y compilar assets con Vite
RUN npm install && npm run build

# Dar permisos correctos a Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copiar script de inicio
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]
