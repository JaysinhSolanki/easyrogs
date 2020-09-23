START TRANSACTION;

  ALTER TABLE `meet_confer_arguments` DROP FOREIGN KEY `fk_meet_confer_question`;

  ALTER TABLE `meet_confer_arguments` 
    ADD CONSTRAINT `fk_meet_confer_question`
      FOREIGN KEY (`question_id`)
      REFERENCES `questions` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE;
      
COMMIT;