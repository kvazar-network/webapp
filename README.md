# KVAZAR webapp

[KevaCoin](https://github.com/kevacoin-project/) content Explorer written on [Symfony](https://github.com/symfony) and [Manticore](https://github.com/manticoresoftware)

This branch is modern replacement to [MySQL](https://github.com/kvazar-network/webapp/tree/mysql) and [SQLite](https://github.com/kvazar-network/webapp/tree/sqlite) implementations written in 2021

## Instances

* `http://[201:23b4:991a:634d:8359:4521:5576:15b7]/kvazar/` - [Yggdrasil](https://github.com/yggdrasil-network/)
  * `http://kvazar.ygg` - [Alfis DNS](https://github.com/Revertron/Alfis) alias

## Install

* `apt install git composer manticore php-fpm php-curl php-mbstring php-pdo php-imagick`
* `git clone https://github.com/kvazar-network/webapp.git`
* `cd webapp`
* `composer update`

## Index

To update blockchain index, use [crawler](https://github.com/kvazar-network/crawler)

## Launch

* `symfony server:start`