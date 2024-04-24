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

Variables

These variables can be passed to the image from docker-compose.yml as needed:


| Variable | 	Default | 	Description |
| ALLOW_OVERWRITE |  	on | 	allow clients to modify files |


ANONYMOUS_DISABLE 	off 	anonymous login
ANON_UPLOAD_ENABLE 	DenyAll 	anonymous upload
FTPUSER_PASSWORD_SECRET 	ftp-user-password-secret 	hashed pw of upload user
FTPUSER_NAME 	ftpuser 	upload username
FTPUSER_UID 	1001 	upload file ownership UID
LOCAL_UMASK 	022 	upload umask
MAX_CLIENTS 	10 	maximum simultaneous logins
MAX_INSTANCES 	30 	process limit
PASV_ADDRESS 		required--address of docker engine
PASV_MAX_PORT 	30100 	range of client ports (rebuild image if changed)
PASV_MIN_PORT 	30091 	
SFTP_ENABLE 	off 	use sftp instead of ftp
SFTP_PORT 	2222 	sftp port
TIMES_GMT 	off 	local time for directory listing
TZ 	UTC 	local timezone
WRITE_ENABLE 	AllowAll 	allow put/rm
Secrets
Secret 	Description
ftp-user-password-secret 	(optional) hashed pw of upload user


## Links

- https://hub.docker.com/_/mariadb
- https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-on-ubuntu-20-04
- https://github.com/docker/compose
