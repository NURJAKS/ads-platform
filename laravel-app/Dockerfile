FROM laravelsail/php82-composer:latest

# Install PostgreSQL + Redis extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/html

COPY . .

RUN composer install
RUN php artisan key:generate

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]

