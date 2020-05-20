START TRANSACTION;

ALTER TABLE `discoveries`
    ADD COLUMN `is_work_in_progress` BOOL NOT NULL DEFAULT TRUE;

UPDATE `discoveries`
    SET `is_work_in_progress` = FALSE;
    
COMMIT;