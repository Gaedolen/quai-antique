# syntax=docker/dockerfile:1

# ---- Base PHP-FPM ----
FROM php:8.2-fpm

# ---- Installer dépendances système ----
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libonig-dev libzip-dev curl zlib1g-dev libicu-dev g++ libssl-dev pkg-config \
    && docker-php-ext-install pdo pdo_mysql zip intl mbstring

# ---- Installer l'extension MongoDB pour PHP ----
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# ---- Installer Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Définir le répertoire de travail ----
WORKDIR /var/www/html

# ---- Copier le projet ----
COPY . .

# ---- Installer les dépendances PHP (prod) ----
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ---- Clear cache Symfony pour la prod ----
RUN APP_ENV=prod php bin/console cache:clear --no-warmup
RUN APP_ENV=prod php bin/console cache:warmup

# ---- Permissions pour Symfony ----
RUN chown -R www-data:www-data var \
    && chmod -R 775 var

# ---- Exposer le port PHP-FPM ----
EXPOSE 9000

# ---- Commande de démarrage ----
CMD ["php-fpm"]