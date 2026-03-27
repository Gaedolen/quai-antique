# syntax=docker/dockerfile:1
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libonig-dev libzip-dev curl libssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Installer l'extension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier le projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Permissions pour le dossier var
RUN chown -R www-data:www-data var

EXPOSE 9000

CMD ["php-fpm"]