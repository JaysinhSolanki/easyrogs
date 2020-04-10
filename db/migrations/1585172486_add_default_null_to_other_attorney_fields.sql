START TRANSACTION;

  ALTER TABLE `clients` 
    CHANGE COLUMN `other_attorney_name` `other_attorney_name` VARCHAR(255) NULL COMMENT 'Only when client type = others' ,
    CHANGE COLUMN `other_attorney_email` `other_attorney_email` VARCHAR(255) NULL COMMENT 'Only when client type = others' ,
    CHANGE COLUMN `other_attorney_id` `other_attorney_id` VARCHAR(255) NULL COMMENT 'id from case_attorney table' ;

COMMIT;