<?php

class SQLite {

  private PDO $_db;

  public function __construct(string $database, string $username, string $password) {

    try {

      $this->_db = new PDO('sqlite:' . $database, $username, $password);
      $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->_db->setAttribute(PDO::ATTR_TIMEOUT, 600);

      $this->_db->query('
        CREATE TABLE IF NOT EXISTS "namespace"(
          "nameSpaceId" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK("nameSpaceId">=0),
          "timeIndexed" INTEGER NOT NULL CHECK("timeIndexed">=0),
          "hash" CHAR(34) NOT NULL,
          CONSTRAINT "hash_UNIQUE"
            UNIQUE("hash")
        )
      ');

      $this->_db->query('
        CREATE TABLE IF NOT EXISTS "block"(
          "blockId" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK("blockId">=0),
          "timeIndexed" INTEGER NOT NULL CHECK("timeIndexed">=0),
          "lostTransactions" INTEGER NOT NULL CHECK("lostTransactions">=0)
        )
      ');

      $this->_db->query('
        CREATE TABLE IF NOT EXISTS "data"(
          "dataId" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL CHECK("dataId">=0),
          "nameSpaceId" INTEGER NOT NULL CHECK("nameSpaceId">=0),
          "blockId" INTEGER NOT NULL CHECK("blockId">=0),
          "time" INTEGER NOT NULL CHECK("time">=0),
          "timeIndexed" INTEGER NOT NULL CHECK("timeIndexed">=0),
          "size" INTEGER NOT NULL,
          "ns" TEXT NOT NULL CHECK("ns" IN(\'0\', \'1\')),
          "deleted" TEXT NOT NULL CHECK("deleted" IN(\'0\', \'1\')),
          "txid" CHAR(64) NOT NULL,
          "key" TEXT NOT NULL,
          "value" TEXT NOT NULL,
          CONSTRAINT "txid_UNIQUE"
            UNIQUE("txid"),
          CONSTRAINT "fk_data_namespace"
            FOREIGN KEY("nameSpaceId")
            REFERENCES "namespace"("nameSpaceId"),
          CONSTRAINT "fk_data_block"
            FOREIGN KEY("blockId")
            REFERENCES "block"("blockId")
        )

      ');

      $this->_db->query('CREATE INDEX IF NOT EXISTS "data.fk_data_namespase_idx" ON "data" ("nameSpaceId")');
      $this->_db->query('CREATE INDEX IF NOT EXISTS "data.fk_data_block_idx" ON "data" ("blockId")');
      $this->_db->query('CREATE INDEX IF NOT EXISTS "data.deleted_INDEX" ON "data" ("deleted")');
      $this->_db->query('CREATE INDEX IF NOT EXISTS "data.ns_INDEX" ON "data" ("ns")');

    } catch(PDOException $e) {
      trigger_error($e->getMessage());
    }
  }

  public function getNamespaceValueByNS(string $ns) {

    try {

      $query = $this->_db->prepare('SELECT `data`.`value` AS `value`

                                           FROM `data`
                                           JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                           WHERE `namespace`.`hash` = ?
                                             AND `data`.`ns`        = "1"
                                             -- AND `data`.`deleted`   = "0" --

                                           ORDER BY `data`.`blockId` DESC

                                           LIMIT 1');

      $query->execute([$ns]);

      $result = $query->fetch();

      return $result ? $result['value'] : '';

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }

  public function getNamespaceHashByTX(string $txid) {

    try {

      $query = $this->_db->prepare('SELECT `namespace`.`hash`

                                           FROM `namespace`
                                           JOIN `data` ON (`data`.`nameSpaceId` = `namespace`.`nameSpaceId`)

                                           WHERE `data`.`txid` = ?');

      $query->execute([$txid]);

      return $query->rowCount() ? $query->fetch()['hash'] : '';

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }

  public function getData(string $namehash = '', string $txid = '', string $search = '', int $start = 0, int $limit = 10) {

    try {

      if ($txid) {

        $query = $this->_db->prepare('SELECT `block`.`blockId` AS `block`,
                                             `namespace`.`hash` AS `namehash`,
                                             `data`.`time` AS `time`,
                                             `data`.`key` AS `key`,
                                             `data`.`value` AS `value`,
                                             `data`.`txid` AS `txid`

                                             FROM `data`
                                             JOIN `block` ON (`block`.`blockId` = `data`.`blockId`)
                                             JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                             WHERE `data`.`txid`    = ?
                                               AND `data`.`ns`      = "0"
                                               -- AND `data`.`deleted` = "0" --

                                             ORDER BY `block`.`blockId` DESC

                                             LIMIT ' . (int) $start . ',' . (int) $limit);

        $query->execute([$txid]);

      } else if ($namehash) {

        $query = $this->_db->prepare('SELECT `block`.`blockId` AS `block`,
                                             `namespace`.`hash` AS `namehash`,
                                             `data`.`time` AS `time`,
                                             `data`.`key` AS `key`,
                                             `data`.`value` AS `value`,
                                             `data`.`txid` AS `txid`

                                             FROM `data`
                                             JOIN `block` ON (`block`.`blockId` = `data`.`blockId`)
                                             JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                             WHERE `namespace`.`hash` = ?
                                               AND `data`.`ns`        = "0"
                                               -- AND `data`.`deleted`   = "0" --

                                             ORDER BY `block`.`blockId` DESC

                                             LIMIT ' . (int) $start . ',' . (int) $limit);

        $query->execute([$namehash]);

      } else if ($search) {

        $query = $this->_db->prepare('SELECT `block`.`blockId` AS `block`,
                                             `namespace`.`hash` AS `namehash`,
                                             `data`.`time` AS `time`,
                                             `data`.`key` AS `key`,
                                             `data`.`value` AS `value`,
                                             `data`.`txid` AS `txid`

                                             FROM `data`
                                             JOIN `block` ON (`block`.`blockId` = `data`.`blockId`)
                                             JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                             WHERE (`data`.`key`       LIKE :search
                                                OR  `data`.`value`     LIKE :search
                                                OR  `block`.`blockId`  LIKE :search
                                                OR  `namespace`.`hash` LIKE :search
                                                OR  `data`.`txid`      LIKE :search)

                                               AND  `data`.`ns`      = "0"
                                               -- AND  `data`.`deleted` = "0" --

                                             ORDER BY `block`.`blockId` DESC

                                             LIMIT ' . (int) $start . ',' . (int) $limit);

        $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

        $query->execute();

      } else {

        $query = $this->_db->prepare('SELECT `block`.`blockId` AS `block`,
                                             `namespace`.`hash` AS `namehash`,
                                             `data`.`time` AS `time`,
                                             `data`.`key` AS `key`,
                                             `data`.`value` AS `value`,
                                             `data`.`txid` AS `txid`

                                             FROM `data`
                                             JOIN `block` ON (`block`.`blockId` = `data`.`blockId`)
                                             JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                             WHERE `data`.`ns`      = "0"
                                               -- AND `data`.`deleted` = "0" --

                                             ORDER BY `block`.`blockId` DESC

                                             LIMIT ' . (int) $start . ',' . (int) $limit);

        $query->execute();
      }

      $result = $query->fetchAll();

      return $result ? $result : [];

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }

  public function getTrends(int $offset = 0) {

    try {

      $query = $this->_db->prepare('SELECT `block`.`blockId` AS `block`,
                                            `namespace`.`hash` AS `namehash`,
                                            `data`.`time` AS `time`,
                                            `data`.`key` AS `key`,
                                            `data`.`value` AS `value`,
                                            `data`.`txid` AS `txid`

                                            FROM `data`
                                            JOIN `block` ON (`block`.`blockId` = `data`.`blockId`)
                                            JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                            WHERE `data`.`ns`      = "0"
                                            AND   `data`.`time`    >= ' . (int) $offset . '
                                              -- AND `data`.`deleted` = "0" --

                                            ORDER BY `block`.`blockId` DESC');

      $query->execute();

      $result = $query->fetchAll();

      return $result ? $result : [];

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }
}
