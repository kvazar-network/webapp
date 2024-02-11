# KVAZAR (webapp)

[KevaCoin](https://github.com/kevacoin-project/) content Explorer written on [Symfony](https://github.com/symfony) and uses [Manticore](https://github.com/manticoresoftware) for full-text search.

This project is new generation of [MySQL](https://github.com/kvazar-network/webapp/tree/mysql) and [SQLite](https://github.com/kvazar-network/webapp/tree/sqlite) implementations.

Master branch currently under development!

## Install

* `git clone https://github.com/kvazar-network/webapp.git`
* `cd webapp`
* `composer install`

## Setup

* Web application requires RPC connection to KevaCoin node
* Manticore search server must be installed also (application uses `php-pdo` driver to interact search index)
* Configure crontab task to update search index: `* * * * * /crontab/index`

## Launch

* `symfony server:start`