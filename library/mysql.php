<?php

class MySQL {

  public function __construct() {

    try {

      $this->_db = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8', DB_USERNAME, DB_PASSWORD, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
      $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->_db->setAttribute(PDO::ATTR_TIMEOUT, 600);

    } catch(PDOException $e) {
      trigger_error($e->getMessage());
    }
  }

  public function getNamespaceName($namehash) {

    try {

      $query = $this->_db->prepare('SELECT `data`.`value` AS `value`

                                           FROM `data`
                                           JOIN `namespace` ON (`namespace`.`nameSpaceId` = `data`.`nameSpaceId`)

                                           WHERE `namespace`.`hash` = ?
                                             AND `data`.`ns`        = "1"
                                             -- AND `data`.`deleted`   = "0" --

                                           ORDER BY `data`.`blockId` DESC

                                           LIMIT 1');

      $query->execute([$namehash]);

      return $query->rowCount() ? $query->fetch()['value'] : [];

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }

  public function getData($namehash = false, $txid = false, $search = false, $start = 0, $limit = 10) {

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


      return $query->rowCount() ? $query->fetchAll() : [];

    } catch(PDOException $e) {

      trigger_error($e->getMessage());
      return false;
    }
  }
}
