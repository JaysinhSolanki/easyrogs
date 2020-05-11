START TRANSACTION;
  ALTER TABLE `sides`
    ADD COLUMN `case_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    ADD COLUMN `case_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    ADD COLUMN `plaintiff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    ADD COLUMN `defendant` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    ADD COLUMN `trial` date DEFAULT NULL,
    ADD COLUMN `discovery_cutoff` date DEFAULT NULL,
    ADD COLUMN `county_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
COMMIT;