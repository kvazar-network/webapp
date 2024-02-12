# KVAZAR webapp

[KevaCoin](https://github.com/kevacoin-project/) content Explorer written on [Symfony](https://github.com/symfony) and [Manticore](https://github.com/manticoresoftware)

This branch is modern replacement to [MySQL](https://github.com/kvazar-network/webapp/tree/mysql) and [SQLite](https://github.com/kvazar-network/webapp/tree/sqlite) implementations written in 2021.

Currently under development!

## Install

* `git clone https://github.com/kvazar-network/webapp.git`
* `cd webapp`
* `composer install`

## Index

* Install [crawler](https://github.com/kvazar-network/crawler) with `composer create-project kvazar/crawler`
* Add crontab task `crontab -e`:`* * * * * php kvazar/crawler/src/index.php`

## Launch

* `symfony server:start`