# Используем официальный образ PHP 8.1-fpm (выберите нужную вам версию PHP)
FROM php:8.2-fpm-alpine

# Устанавливаем системные зависимости, необходимые для расширений PHP
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    oniguruma-dev \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    git \
    unzip \
    linux-headers

# Устанавливаем общие расширения PHP
RUN docker-php-ext-install \
    pdo pdo_mysql \
    mysqli \
    opcache \
    intl \
    zip \
    mbstring \
    xml \
    sockets \
    bcmath \
    pcntl

# Устанавливаем Composer глобально
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./custom-php-settings.ini /usr/local/etc/php/conf.d/zz-custom-php-settings.ini

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# (Опционально) Копируем код приложения, если не монтируем его как volume
# COPY . .

# (Опционально) Устанавливаем зависимости Composer, если код скопирован выше
# RUN composer install --prefer-dist --no-scripts --no-dev --no-interaction

# Права доступа (если необходимо, зависит от пользователя, под которым работает fpm)
# RUN chown -R www-data:www-data /var/www/html

# Команда по умолчанию для php-fpm
CMD ["php-fpm"]
