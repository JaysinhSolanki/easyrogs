START TRANSACTION;

    DROP TABLE IF EXISTS `kb_kb_section`;
    DROP TABLE IF EXISTS `kb_section`;

    DROP TABLE IF EXISTS `kb`;
    CREATE TABLE IF NOT EXISTS `kb` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `area_id` int unsigned NOT NULL DEFAULT '0',
        `issue` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `solution` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `explanation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,

        `FROGS`  BOOL NOT NULL DEFAULT FALSE,
        `FROGSE` BOOL NOT NULL DEFAULT FALSE,
        `SROGS`  BOOL NOT NULL DEFAULT FALSE,
        `RFAS`   BOOL NOT NULL DEFAULT FALSE,
        `RPDS`   BOOL NOT NULL DEFAULT FALSE,

        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=751 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
