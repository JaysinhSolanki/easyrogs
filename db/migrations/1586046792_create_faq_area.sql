START TRANSACTION;

  CREATE TABLE IF NOT EXISTS `faq_area` (
    `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
    `area_title` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  ALTER TABLE `faqs` ADD COLUMN `area_id` int(9) unsigned NOT NULL DEFAULT '0';
  ALTER TABLE `faqs` ADD INDEX `area_id` (`area_id` ASC);
  ALTER TABLE `faqs` ADD CONSTRAINT `fk_faqs_faq_area1` FOREIGN KEY (`area_id`) REFERENCES `faq_area` (`id`);
COMMIT;