# ProFTPd Admin - Docker
Adapted the proftpd Admin to a Dockerized Graphical User Interface for ProFTPd with MySQL and sqlite3 support
Also removed ezSQL (which was an very obsolete version and an actually unnecessary interface) and changed the 
database backend to PDO, during the rework necesary for this< also some obsolet pre php 8 statements were found
and changed.

## About ProFTPd Admin


This GUI for ProFTPd was written to support a basic user management feature
when using the SQL module. Originally written by Lex Brugmann in 2004, 
updated by [Christian Beer](https://github.com/ChristianBeer/ProFTPd-Admin)
in 2012 to support the latest PHP version.  
2017 updated by [Michael Keck](https://github.com/mkkeck) with build-in login for
the admin user, secure the directories _`configs/`_ and _`includes`_ and moved 
_`tables*.sql`_ to _`install/tables*.sql`_.
Added _`install/config-examples`_ for [OS specific configurations](install/config-examples).

It's possible to use either of SHA1 and pbkdf2 with either of MySQL/MariaDB 
and sqlite3. pbkdf2 is supported since ProFTPd 1.3.5.

You can look at some [screenshots](screenshots/README.md) to see if this is 
the tool you need.



## Installation

NOTE: Work in Progress!

* Install docker
  
copy 

* docker_compose.yml
* .env
* config\cofig.php
* config\proftpd.conf
* config\modules.conf
* config\sql.conf

for your preferred database backend to a local folder and adapt to your needs.

run docker compose up

(and re-adjust the configs according to the errormessages you get :-)

until you have a running ftp server

enjoy!

**Note:**  
Please use, if available, a secured connection to your webserver via `https`.
You can do this by your webserver configurations or simple set in the
_`config.php`_:
```php
/**
 * Force SSL usage
 *
 * Important: You should change this to true on live systems or configure
 *            your webserver to use SSL!
 */
$cfg['force_ssl'] = true; // default was false
```
Please notice that you need a SSL-certificate to use secured connection.






## Thanks

- Lex Brugman for initiating this project 
- Justin Vincent for the ezSQL library 
- Ricardo Padilha for implementing sqlite3, pbkdf2 and bootstrap support
- Christian Beer for his update to support the latest PHP version
- Robert Tulke for the Debian Jessie example



## Copyright / License

- © 2004 The Netherlands, Lex Brugman; lex_brugman@users.sourceforge.net
- © 2012 Christian Beer; djangofett@gmx.net
- © 2015 Ricardo Padilha; ricardo@droboports.com
- © 2017 Robert Tulke; https://github.com/rtulke/
- © 2017 Michael Keck; https://github.com/mkkeck

---------------------------------------------------------------------------

Published under the GPLv2 License (see [LICENSE](LICENSE) for details)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, download from 
[http://www.gnu.org/licenses/gpl-2.0.txt](http://www.gnu.org/licenses/gpl-2.0.txt)
