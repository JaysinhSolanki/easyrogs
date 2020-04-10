START TRANSACTION;

  ALTER TABLE `case_team` 
    CHANGE COLUMN `is_deleted` `is_deleted` INT(11) NULL DEFAULT 0 COMMENT '1: Yes 0: No',
    CHANGE COLUMN `email_sent` `email_sent` INT(11) NULL DEFAULT 0 COMMENT '1: Yes 0: No' ;
    
COMMIT;