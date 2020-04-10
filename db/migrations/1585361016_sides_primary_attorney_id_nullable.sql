START TRANSACTION;

  ALTER TABLE `sides` DROP FOREIGN KEY `fk_sides_attorney1`;
  ALTER TABLE `sides` 
    CHANGE COLUMN `primary_attorney_id` `primary_attorney_id` INT(11) NULL;
  
  ALTER TABLE `sides` 
    ADD CONSTRAINT `fk_sides_attorney1`
    FOREIGN KEY (`primary_attorney_id`)
    REFERENCES `attorney` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
    
COMMIT;