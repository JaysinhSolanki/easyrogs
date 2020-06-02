START TRANSACTION;
  ALTER TABLE `discoveries` 
    ADD COLUMN `payment_intent_id` VARCHAR(45) NULL DEFAULT NULL;

  ALTER TABLE `system_addressbook`
    ADD COLUMN `payment_customer_id` VARCHAR(45) NULL;

  ALTER TABLE `sides`
    ADD COLUMN `payment_method_id` VARCHAR(45) NULL;
COMMIT;