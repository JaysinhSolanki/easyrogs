START TRANSACTION;
  ALTER TABLE `sides` DROP FOREIGN KEY `fk_sides_attorney1`;
  
  ALTER TABLE `sides` 
    ADD INDEX `fk_sides_attorney1_idx` (`primary_attorney_id` ASC),
    DROP INDEX `fk_sides_attorney1_idx`;
  
  ALTER TABLE `sides`
    ADD CONSTRAINT `fk_sides_attorney1`
        FOREIGN KEY (`primary_attorney_id`)
        REFERENCES `system_addressbook` (`pkaddressbookid`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
COMMIT;