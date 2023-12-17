# Run ProFTPd Admin Docker

Graphical User Interface for ProFTPd with MySQL and sqlite3 support


## Run

```
git clone https://github.com/jniltinho/ProFTPd-Admin.git html
rm -rf html/.git
mv html/Docker Docker
mv html Docker/
cd Docker
cp html/install/config-examples/debian/config-example.php html/configs/config.php
sed -i 's|yourdbpasswordhere|@proftpd2023|' html/configs/config.php
sed -i 's|/home/web|/data|' html/configs/config.php
sed -i 's|localhost|mariadb|' html/configs/config.php
mkdir dump dbdata
cp html/install/tables.sql dump/
docker-compose up -d
docker-compose exec mariadb bash -c "mysql -u root -h mariadb --password=root proftpd < /dump/tables.sql"
```


## Links

- https://hub.docker.com/_/mariadb
- https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-on-ubuntu-20-04
- https://github.com/docker/compose
