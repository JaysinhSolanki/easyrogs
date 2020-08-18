START TRANSACTION;

  CREATE TABLE `membership_whitelist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `address` varchar(255) DEFAULT NULL,
    `domain` varchar(255) DEFAULT NULL,
    `expires_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_address_domain` (`address`,`domain`) USING BTREE
  ) ENGINE=InnoDB;

COMMIT;