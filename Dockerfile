FROM php:8.4-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libpq-dev \
    netcat-openbsd

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

COPY --chown=www-data:www-data . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf

COPY wait-for-db.sh /usr/local/bin/wait-for-db.sh

RUN chmod +x /usr/local/bin/wait-for-db.sh

EXPOSE 80

CMD ["sh", "-c", "/usr/local/bin/wait-for-db.sh && apache2-foreground"]
