START TRANSACTION;


  ALTER TABLE `attorney` 
  ADD COLUMN `side_id` INT(11) AFTER `updated_by`,
  ADD INDEX `fk_side_idx` (`side_id` ASC);
  
  ALTER TABLE `attorney` 
  ADD CONSTRAINT `fk_side`
    FOREIGN KEY (`side_id`)
    REFERENCES `sides` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

COMMIT;