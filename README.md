<<<<<<< HEAD
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

#### yggdrasil
[http://[203:9fd0:95df:54d7:29db:5ee1:fe2d:95c7]](http://[203:9fd0:95df:54d7:29db:5ee1:fe2d:95c7])
=======
>>>>>>> master
