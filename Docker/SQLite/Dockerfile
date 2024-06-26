FROM php:8-apache-bullseye

ENV DEBIAN_FRONTEND noninteractive
ENV SHELL /bin/bash


ENV ALLOW_OVERWRITE=on \
    ANONYMOUS_DISABLE=off \
    ANON_UPLOAD_ENABLE=DenyAll \
    FTPUSER_PASSWORD_SECRET=ftp-user-password-secret \
    FTPUSER_NAME=ftpuser \
    FTPUSER_UID=1001 \
    LOCAL_UMASK=022 \
    MAX_CLIENTS=10 \
    MAX_INSTANCES=30 \
    PASV_ADDRESS= \
    PASV_MAX_PORT=30100 \
    PASV_MIN_PORT=30091 \
    SERVER_NAME=ProFTPD \
    TIMES_GMT=off \
    TZ=UTC \
    WRITE_ENABLE=AllowAll

#required dependencies
RUN apt-get update && apt-get -y install --no-install-recommends tzdata locales \
    libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev zlib1g-dev net-tools \
    libxml2-dev libzip-dev libonig-dev graphviz openssl
#proftp and modules
RUN apt-get update && apt-get -y install \
	proftpd-basic proftpd-mod-crypto  proftpd-mod-vroot 
	
#sqlite support
RUN sudo apt-get install -y sqlite3 libsqlite3-dev  proftpd-mod-sqlite
RUN mkdir /sqlite_db
#create database and tables
COPY ./install/tables-sqlite3.sql /sqlite_db/sqlite_install_tables.sql
RUN /usr/bin/sqlite3 /sqlite_db/ftpadmin.db
RUN sqlite3 /sqlite_db/ftpadmin.db < install/tables-sqlite3.sql


#cleanup
RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archive/*.deb
	

RUN ln -s "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" 
RUN  a2enmod rewrite headers 
RUN  docker-php-ext-configure gd 
RUN  docker-php-ext-install gd zip 
RUN  docker-php-source delete 
RUN  chown -R www-data:www-data /var/www/html 

COPY ./sql.conf /etc/proftpd/sql.conf
COPY ./modules.conf /etc/proftpd/modules.conf

# Do we actually *need this* ??
# https://github.com/ochinchina/supervisord
#COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord
#RUN  mkdir -p /etc/supervisord
#COPY ./supervisord/supervisord.conf /etc/
#COPY ./supervisord/proftpd.conf /etc/supervisord/
#RUN sed -i 's|set -e|set -e\n/usr/local/bin/supervisord --daemon -c /etc/supervisord.conf \&|' /usr/local/bin/docker-php-entrypoint

# https://docs.docker.com/config/containers/multi-service_container/
# Tells me it is the least preferred option due to need for additional installation and probable (always) additional issues and vulnerabilities.
# So swtih to the second option: a .sh file to start the stuff
COPY ./Docker/entrypoint.sh /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 20 21 $PASV_MIN_PORT-$PASV_MAX_PORT

