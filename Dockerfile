FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libonig-dev \
        libsqlite3-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" bcmath mbstring pcntl pdo_mysql pdo_sqlite zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Point Apache at Laravel's public directory and allow .htaccess rewrites.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan package:discover --ansi \
    && mkdir -p \
        storage/app/public/documents \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80

ENTRYPOINT ["entrypoint"]
