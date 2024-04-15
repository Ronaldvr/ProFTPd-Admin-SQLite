FROM php:8.2.7-apache-bullseye

ENV DEBIAN_FRONTEND noninteractive
ENV SHELL /bin/bash
#ENV TZ America/Sao_Paulo


#required dependecies
RUN apt-get update && apt-get -y install --no-install-recommends tzdata locales \
    libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev zlib1g-dev net-tools \
    libxml2-dev libzip-dev libonig-dev graphviz openssl
#proftp and modules
RUN apt-get update && apt-get -y install \
	proftpd-basic proftpd-mod-crypto  proftpd-mod-vroot \
#sqlite support
RUN sudo apt-get install -y sqlite3 libsqlite3-dev  proftpd-mod-sqlite
RUN mkdir /sqlite_db

COPY ./install/tables-sqlite3.sql /sqlite_db/sqlite_install_tables.sql

RUN /usr/bin/sqlite3 /sqlite_db/ftpadmin.db

RUN sqlite3 /sqlite_db/ftpadmin.db < install/tables-sqlite3.sql

RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archive/*.deb
	

RUN ln -s "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite headers \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo_mysql mysqli zip \
    && docker-php-source delete \
    && docker-php-ext-enable mysqli \
    && chown -R www-data:www-data /var/www/html \
    && mkdir -p /etc/supervisord

COPY ./sql.conf /etc/proftpd/sql.conf
COPY ./modules.conf /etc/proftpd/modules.conf

# https://github.com/ochinchina/supervisord
COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord

COPY ./supervisord/supervisord.conf /etc/
COPY ./supervisord/proftpd.conf /etc/supervisord/
RUN sed -i 's|set -e|set -e\n/usr/local/bin/supervisord --daemon -c /etc/supervisord.conf \&|' /usr/local/bin/docker-php-entrypoint

EXPOSE 20 21
