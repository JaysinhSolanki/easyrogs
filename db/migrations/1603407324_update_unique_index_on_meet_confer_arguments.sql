START TRANSACTION;
  -- Migration queries -- 
  
  ALTER TABLE `meet_confer_arguments` DROP FOREIGN KEY `fk_meet_confer_question`;
  ALTER TABLE `meet_confer_arguments` DROP INDEX `question_id`;


  ALTER TABLE `meet_confer_arguments` ADD CONSTRAINT `fk_meet_confer_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  ALTER TABLE `meet_confer_arguments` ADD UNIQUE INDEX `meet_confer_question_id` (`meet_confer_id` ASC, `question_id` ASC);

COMMIT;