START TRANSACTION;

 CREATE TABLE IF NOT EXISTS `jobs_queue` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `method_name` VARCHAR(255) NULL,
    `data` TEXT NULL,
    `priority` TINYINT NOT NULL,
    `unique_id` VARCHAR(32) NULL,
    `created_at` DATETIME NOT NULL,
    `is_taken` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`))
  ENGINE = InnoDB;

COMMIT;