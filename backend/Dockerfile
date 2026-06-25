FROM php:8.3-cli

# PostgreSQL PDO driver + composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first (better layer caching), then copy source.
COPY composer.json ./
RUN composer install --no-interaction --no-progress

COPY . .

EXPOSE 8080

# Built-in PHP server routing all requests through the front controller.
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/index.php"]
