START TRANSACTION;

    DROP TABLE IF EXISTS `kb_kb_section`;
    DROP TABLE IF EXISTS `kb`;
    DROP TABLE IF EXISTS `kb_section`;

    CREATE TABLE IF NOT EXISTS `kb_section` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` text NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE IF NOT EXISTS `kb` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `area_id` int unsigned NOT NULL DEFAULT '0',
        `issue` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `solution` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `explanation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `notes` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=751 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `kb_kb_section` (
        `kb_id` int UNSIGNED NOT NULL,
        `section_id` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`kb_id`,`section_id`),
        KEY `fk_id_idx` (`kb_id`),
        KEY `fk_section_id_idx` (`section_id`),
        CONSTRAINT `fk_kb_id` FOREIGN KEY (`kb_id`) REFERENCES `kb` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_section_id` FOREIGN KEY (`section_id`) REFERENCES `kb_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB;
    
COMMIT;
