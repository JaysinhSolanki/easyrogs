START TRANSACTION;

  ALTER TABLE `cases` 
    CHANGE COLUMN `case_title` `case_title` VARCHAR(255) NULL,
    CHANGE COLUMN `plaintiff` `plaintiff` VARCHAR(255) NULL,
    CHANGE COLUMN `defendant` `defendant` TEXT NULL,
    CHANGE COLUMN `case_number` `case_number` VARCHAR(255) NULL,
    CHANGE COLUMN `jurisdiction` `jurisdiction` VARCHAR(255) NULL,
    CHANGE COLUMN `county_name` `county_name` VARCHAR(255) NULL,
    CHANGE COLUMN `judge_name` `judge_name` VARCHAR(255) NULL,
    CHANGE COLUMN `court_address` `court_address` VARCHAR(255) NULL,
    CHANGE COLUMN `department` `department` VARCHAR(255) NULL,
    CHANGE COLUMN `date_filed` `date_filed` DATE NULL,
    CHANGE COLUMN `trial` `trial` DATE NULL,
    CHANGE COLUMN `discovery_cutoff` `discovery_cutoff` DATE NULL,
    CHANGE COLUMN `filed` `filed` DATE NULL,
    CHANGE COLUMN `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    CHANGE COLUMN `case_attorney` `case_attorney` VARCHAR(255) NULL ,
    CHANGE COLUMN `masterhead` `masterhead` TEXT NULL ;


COMMIT;