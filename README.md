# kvazar-network webapp
Web-oriented content exploring platform for Kevacoin Blockchain

### requirements
```
php-8^
php-curl
php-mbstring
php-sqlite3
php-pdo
php-bcmath
php-gd
```
#### database

https://github.com/kvazar-network/database

##### MySQL

https://github.com/kvazar-network/webapp/tree/master

##### SQLite

https://github.com/kvazar-network/webapp/tree/sqlite

#### crontab

```
0 0 * * * /path-to/php /path-to/crontab/sitemap.php > /dev/null 2>&1
```

### nginx sef_mode example

```
location / {
  try_files $uri $uri/ =404 @sef;
}

location @sef {
  rewrite ^(/.*)$ /?$1 last;
}
```

### examples
#### web
https://kvazar.today

#### yggdrasil
[http://[203:7693:ae20:18a6:7689:cb63:c53d:43c6]](http://[203:7693:ae20:18a6:7689:cb63:c53d:43c6])
