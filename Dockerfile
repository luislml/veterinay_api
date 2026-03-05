# -----------------------------
# Dockerfile Laravel API para Koyeb
# PHP 8.2 + Servidor built-in en puerto 8000
# -----------------------------

FROM php:8.2-cli

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Configuración para subir archivos grandes
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Dar permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto 8000 (para health check)
EXPOSE 8000

# Comando para arrancar Laravel usando servidor built-in
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
