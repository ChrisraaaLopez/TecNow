#!/bin/sh

# Compilar assets de Vite
cd /var/www && npm run build

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano (mantiene el contenedor vivo)
nginx -g "daemon off;"
