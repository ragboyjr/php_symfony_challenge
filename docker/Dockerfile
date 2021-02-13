FROM php:7.4.15-fpm-buster as base

ENV APP_ROOT /var/www/html

COPY --from=mlocati/php-extension-installer:1.2.8 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_mysql amqp zip @composer-2.0.9 intl
RUN apt-get update && apt-get -y install apache2 jq unzip procps supervisor
RUN a2enmod proxy proxy_fcgi
RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/c096cface5fbd9f2d6b037391dfecae6fde1362e/wait-for-it.sh > /usr/local/bin/wait-for-it.sh && chmod +x /usr/local/bin/wait-for-it.sh

COPY apache2.conf /etc/apache2/sites-enabled/000-default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-entrypoint.sh /docker-entrypoint.sh
WORKDIR /var/www/html

ENTRYPOINT bash /docker-entrypoint.sh
