START TRANSACTION;

  ALTER TABLE `system_addressbook` CHANGE COLUMN `masthead` `masterhead` VARCHAR(255) NOT NULL ;

COMMIT;