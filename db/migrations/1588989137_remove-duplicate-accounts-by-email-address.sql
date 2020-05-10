START TRANSACTION;
DELETE user1
  FROM `system_addressbook` AS user1
    INNER JOIN `system_addressbook` AS user2
  WHERE
    user1.pkaddressbookid < user2.pkaddressbookid
    AND user1.email = user2.email;
ALTER TABLE `system_addressbook`
  MODIFY COLUMN `email` varchar(45) COLLATE utf8_unicode_ci NOT NULL UNIQUE;
ALTER TABLE `system_addressbook`
  ADD UNIQUE INDEX `user_email_unique` (`email` ASC);
COMMIT;