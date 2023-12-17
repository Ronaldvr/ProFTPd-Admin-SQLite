#!/bin/bash
## Install ProFTPD Debian 12
## http://www.proftpd.org/
## https://www.digitalocean.com/community/tutorials/how-to-set-up-proftpd-with-a-mysql-backend-on-ubuntu-12-10
## https://github.com/mkkeck/ProFTPd-Admin-Secure-Version
## https://www.linuxbabe.com/mail-server/postfixadmin-ubuntu
## https://tecadmin.net/how-to-install-php-on-debian-12/
## https://tecadmin.net/switch-between-multiple-php-version-on-debian/
## https://stackoverflow.com/questions/6479107/how-to-enable-mysql-query-log


apt-get update
apt-get install -yq vim git unzip net-tools
apt-get install -yq proftpd-basic proftpd-mod-mysql proftpd-mod-crypto
apt-get install -yq php7.4 php-mysql mcrypt php-mcrypt php-mbstring apache2 mariadb-server mariadb-client

## Config Mysql
systemctl start mariadb
systemctl enable mariadb
mysql_secure_installation

mkdir -p /etc/proftpd/backup
cp -aR /etc/proftpd/* /etc/proftpd/backup/

cd /var/www/html
git clone https://github.com/jniltinho/ProFTPd-Admin.git proftpdadmin
cp proftpdadmin/install/config-examples/debian/config-example.php proftpdadmin/configs/config.php
sed -i 's|yourdbpasswordhere|@proftpd2023|' proftpdadmin/configs/config.php


echo "CREATE DATABASE IF NOT EXISTS proftpd;
CREATE USER 'proftpd'@'localhost' identified by '@proftpd2023';
GRANT ALL privileges on proftpd.* to 'proftpd'@'localhost';
SHOW DATABASES;
SELECT User,Host FROM mysql.user;" > create_db.sql

mysql -p < create_db.sql
mysql -p proftpd < proftpdadmin/install/tables.sql
rm -f create_db.sql


cp proftpdadmin/install/config-examples/debian/sql.conf /etc/proftpd/
sed -i 's|yourdbpasswordhere|@proftpd2023|' /etc/proftpd/sql.conf
#sed -i 's|MultilineRFC2228|#MultilineRFC2228|g' /etc/proftpd/proftpd.conf

cp proftpdadmin/install/config-examples/debian/modules.conf /etc/proftpd/
#sed -i 's|#LoadModule mod_ident.c|LoadModule mod_ident.c|' /etc/proftpd/modules.conf
#sed -i 's|#LoadModule mod_sql.c|LoadModule mod_sql.c|' /etc/proftpd/modules.conf
#sed -i 's|#LoadModule mod_sql_mysql.c|LoadModule mod_sql_mysql.c|' /etc/proftpd/modules.conf
#sed -i 's|#LoadModule mod_sql_passwd.c|LoadModule mod_sql_passwd.c|' /etc/proftpd/modules.conf


## Check Config
proftpd --configtest -c /etc/proftpd/proftpd.conf
systemctl restart proftpd
