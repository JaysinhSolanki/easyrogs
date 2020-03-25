START TRANSACTION;
  -- -----------------------------------------------------
  -- Table `sides`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `sides` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `case_id` BIGINT(20) UNSIGNED NOT NULL,
    `masthead` VARCHAR(255) NOT NULL,
    `role` VARCHAR(45) NULL,
    `primary_attorney_id` INT(11) NOT NULL,
    
    PRIMARY KEY (`id`),
    
    INDEX `fk_sides_cases1_idx` (`case_id` ASC),
    INDEX `fk_sides_attorney1_idx` (`primary_attorney_id` ASC),
    
    CONSTRAINT `fk_sides_cases1`
      FOREIGN KEY (`case_id`)
      REFERENCES `cases` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
    
    CONSTRAINT `fk_sides_attorney1`
      FOREIGN KEY (`primary_attorney_id`)
      REFERENCES `attorney` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `teams`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `teams` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `system_addressbook_id` INT NOT NULL,
    PRIMARY KEY (`id`),

    INDEX `fk_teams_users_idx` (`system_addressbook_id` ASC),
    
    CONSTRAINT `fk_teams_users`
      FOREIGN KEY (`system_addressbook_id`)
      REFERENCES `system_addressbook` (`pkaddressbookid`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `sides_clients`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `sides_clients` (
    `side_id` INT NOT NULL,
    `client_id` BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (`side_id`, `client_id`),
    INDEX `fk_sides_has_clients_clients1_idx` (`client_id` ASC),
    INDEX `fk_sides_has_clients_sides1_idx` (`side_id` ASC),
    CONSTRAINT `fk_sides_has_clients_sides1`
      FOREIGN KEY (`side_id`)
      REFERENCES `sides` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
    CONSTRAINT `fk_sides_has_clients_clients1`
      FOREIGN KEY (`client_id`)
      REFERENCES `clients` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `sides_users`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `sides_users` (
    `side_id` INT NOT NULL,
    `system_addressbook_id` INT NOT NULL,
    PRIMARY KEY (`side_id`, `system_addressbook_id`),
    INDEX `fk_sides_has_users_users1_idx` (`system_addressbook_id` ASC),
    INDEX `fk_sides_has_users_sides1_idx` (`side_id` ASC),
    CONSTRAINT `fk_sides_has_users_sides1`
      FOREIGN KEY (`side_id`)
      REFERENCES `sides` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
    CONSTRAINT `fk_sides_has_users_users1`
      FOREIGN KEY (`system_addressbook_id`)
      REFERENCES `system_addressbook` (`pkaddressbookid`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `users_teams`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `users_teams` (
    `system_addressbook_id` INT NOT NULL,
    `team_id` INT NOT NULL,
    PRIMARY KEY (`system_addressbook_id`, `team_id`),
    INDEX `fk_users_has_teams_teams1_idx` (`team_id` ASC),
    INDEX `fk_users_has_teams_users1_idx` (`system_addressbook_id` ASC),
    CONSTRAINT `fk_users_has_teams_users1`
      FOREIGN KEY (`system_addressbook_id`)
      REFERENCES `system_addressbook` (`pkaddressbookid`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
    CONSTRAINT `fk_users_has_teams_teams1`
      FOREIGN KEY (`team_id`)
      REFERENCES `teams` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
  ) ENGINE = InnoDB;
COMMIT;