START TRANSACTION;
  
  -- DELETE ORPHAN ROWS
  DELETE FROM client_attorney WHERE attorney_id NOT IN (SELECT id from attorney);

  ALTER TABLE `client_attorney` 
    ADD INDEX `fk_attorney_idx` (`attorney_id` ASC);

  ALTER TABLE `client_attorney` 
    ADD CONSTRAINT `fk_attorney`
      FOREIGN KEY (`attorney_id`)
        REFERENCES `attorney` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE;

COMMIT;