# kvazar-network webapp
Web-oriented content exploring platform for Kevacoin Blockchain

### requirements
```
php-7.4
php-curl
php-mbstring
php-mysql
php-bcmath
php-gd
```
#### database

https://github.com/kvazar-network/database

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

### web
https://kvazar.today

### yggdrasil
[http://[200:4f97:7cc6:fdd5:1508:5dcc:d8a3:88c5]](http://[200:4f97:7cc6:fdd5:1508:5dcc:d8a3:88c5])
