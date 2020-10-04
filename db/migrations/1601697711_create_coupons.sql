START TRANSACTION;

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `uses` bigint(20) unsigned NOT NULL DEFAULT '0',
  `max_uses` bigint(20) DEFAULT NULL,
  `credits` bigint(20) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_idx` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

COMMIT;