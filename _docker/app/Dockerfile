FROM php:8.2-fpm

# Установка необходимых пакетов для PHP
RUN echo 'deb http://mirror.yandex.ru/debian/ bookworm main contrib non-free non-free-firmware' > /etc/apt/sources.list && \
    echo 'deb http://security.debian.org/debian-security bookworm-security main contrib non-free non-free-firmware' >> /etc/apt/sources.list && \
    echo 'deb http://mirror.yandex.ru/debian/ bookworm-updates main contrib non-free non-free-firmware' >> /etc/apt/sources.list && \
    apt-get clean && \
    apt-get update -o Acquire::Retries=5 && \
    apt-get install -y --no-install-recommends \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip \
      unzip \
      git \
      curl && \
    docker-php-ext-install pdo_mysql bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Копирование файла php.ini
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Установка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Установка Node.js и npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Установка рабочей директории
WORKDIR /var/www
