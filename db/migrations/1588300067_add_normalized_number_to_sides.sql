START TRANSACTION;

  ALTER TABLE sides
    ADD COLUMN `normalized_number` VARCHAR(255) NULL DEFAULT NULL;

COMMIT;