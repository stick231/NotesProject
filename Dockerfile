FROM ubuntu:20.04

MAINTAINER Danil Kopylov <lobsterk@yandex.ru>

# Установка PHP 7.4
RUN apt-get update && \
    apt-get upgrade -y --no-install-recommends --no-install-suggests && \
    apt-get install software-properties-common -y --no-install-recommends --no-install-suggests && \
    apt-get update && \
    apt-get install php7.4-fpm php7.4-cli -y --no-install-recommends --no-install-suggests

# Установка Nginx и других необходимых пакетов
RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
    nginx \
    ca-certificates \
    gettext \
    mc \
    libmcrypt-dev  \
    libicu-dev \
    libcurl4-openssl-dev \
    mysql-client-8.0 \
    libldap2-dev \
    libfreetype6-dev \
    libfreetype6 \
    curl

# Установка расширений PHP
RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
    php-common \
    php-mongodb \
    php-curl \
    php-intl \
    php-soap \
    php-xml \
    php-bcmath \
    php-mysql \
    php-amqp \
    php-mbstring \
    php-ldap \
    php-zip \
    php-json \
    php-xml \
    php-xmlrpc \
    php-gmp \
    php-ldap \
    php-gd \
    php-redis \
    php-xdebug && \
    echo "extension=apcu.so" | tee -a /etc/php/7.4/mods-available/cache.ini

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Установка Node.js
RUN apt install -y gpg-agent && \
    curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt update && apt install -y nodejs yarn

# Установка часового пояса
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime

# Перенаправление логов
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
    && ln -sf /dev/stderr /var/log/php7.4-fpm.log

# Копирование конфигурации Nginx
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Копирование файлов проекта
COPY ./index.php /var/www/
COPY ./composer.json /var/www/
COPY ./composer.lock /var/www/

# Установка зависимостей
WORKDIR /var/www/
RUN composer install --no-dev --optimize-autoloader

# Запуск контейнера
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
