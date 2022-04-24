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

### nginx sef_mode example

```
location / {
  try_files $uri $uri/ =404 @sef;
}

location @sef {
  rewrite ^(/.*)$ /?$1 last;
}
```

### webapp example
https://kvazar.today
