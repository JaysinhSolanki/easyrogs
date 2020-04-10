START TRANSACTION;

  ALTER TABLE `system_addressbook` ADD COLUMN `masthead` VARCHAR(255) NOT NULL AFTER `updated_by`;

COMMIT;