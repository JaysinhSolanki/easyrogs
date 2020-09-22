START TRANSACTION;

  CREATE TABLE `meet_confers` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
    `response_id` INT(11) NOT NULL,
    `served` TINYINT NOT NULL DEFAULT 0,
    `served_at` DATETIME NULL,
    `masterhead` TEXT NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `attorney_masterhead` TEXT NOT NULL,
    `intro` LONGTEXT NOT NULL,
    `conclusion` LONGTEXT NOT NULL,
    `signature` TEXT NOT NULL,
    `payment_intent_id` VARCHAR(45) NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB;

  ALTER TABLE `meet_confers` ADD UNIQUE INDEX `response_id` (`response_id` ASC);
  
  -- !!! `responses` is MyISAM  so we can't add FK constraint !!!
  -- ALTER TABLE `meet_confers` ADD CONSTRAINT `fk_meet_confer_response` FOREIGN KEY (`response_id`) REFERENCES `responses` (`id`) ON DELETE CASCADE;

  CREATE TABLE `meet_confer_arguments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `meet_confer_id` INT(11) UNSIGNED NOT NULL,
    `question_id` BIGINT(20) UNSIGNED NOT NULL,
    `body` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB;

  ALTER TABLE `meet_confer_arguments` ADD UNIQUE INDEX `question_id` (`question_id` ASC);
  ALTER TABLE `meet_confer_arguments` ADD CONSTRAINT `fk_meet_confer_argument_mc` FOREIGN KEY (`meet_confer_id`) REFERENCES `meet_confers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  ALTER TABLE `meet_confer_arguments` ADD CONSTRAINT `fk_meet_confer_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;