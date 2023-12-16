CREATE DATABASE IF NOT EXISTS proftpd;
CREATE USER 'proftpd'@'localhost' identified by '@proftpd2023';
GRANT ALL privileges on proftpd.* to 'proftpd'@'localhost';
SHOW DATABASES;
SELECT User,Host FROM mysql.user;
