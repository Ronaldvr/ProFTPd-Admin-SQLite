# Run ProFTPd Admin Docker

Graphical User Interface for ProFTPd with MySQL and sqlite3 support


## Run

```
git clone https://github.com/Ronaldvr/ProFTPd-Admin-SQLite.git html
rm -rf html/.git
mv html/Docker Docker
mkdir Docker/html
mv html Docker/html/proftpdadmin
cd Docker
cp html/proftpdadmin/install/config-examples/debian/config-example.php html/proftpdadmin/configs/config.php
sed -i 's|yourdbpasswordhere|@proftpd2023|' html/proftpdadmin/configs/config.php
sed -i 's|/home/web|/srv/ftp|' html/proftpdadmin/configs/config.php
sed -i 's|localhost|mariadb|' html/proftpdadmin/configs/config.php
mkdir dump dbdata
cp html/proftpdadmin/install/tables-sqlite3.sql dump/
rm -rf html/proftpdadmin/install
docker-compose up -d
docker-compose exec sqlite3 auth.sqlit3e < /dump/tables-sqlite3.sql

## http://localhost:8080/proftpdadmin
## LOGIN: admin PASS: @Admin2023
```


## Links

- https://hub.docker.com/_/mariadb
- https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-on-ubuntu-20-04
- https://github.com/docker/compose
