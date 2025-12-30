FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \n    libpng-dev \n    libjpeg-dev \n    libfreetype6-dev \n    libzip-dev \n    git \n    unzip \n    && docker-php-ext-configure gd --with-freetype --with-jpeg \n    && docker-php-ext-install gd zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
