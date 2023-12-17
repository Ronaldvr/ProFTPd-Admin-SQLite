# Run ProFTPd Admin Docker

Graphical User Interface for ProFTPd with MySQL and sqlite3 support


## Run

```
git clone https://github.com/jniltinho/ProFTPd-Admin.git html
rm -rf html/.git html/Docker
mkdir dump
cp html/install/tables.sql dump/
docker-compose up -d
docker-compose exec mariadb bash -c "mysql -u root -h mariadb --password=root proftpd < /dump/tables.sql"
```


## Links

- https://hub.docker.com/_/mariadb
- https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-on-ubuntu-20-04
- https://github.com/docker/compose
