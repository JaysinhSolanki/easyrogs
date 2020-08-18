<?php 
  require_once __DIR__ . '/../system/bootstrap.php';

  define('CREDITS', 3);

  /**
   * THIS SCRIPT WILL:
   * Add "CREDITS" credits to attorneys on system_addressbook where the credits column is NULL
   */

  $logContext = 'ADD_CREDITS';
  $logger = new EasyRogs\Logger(LOGS_DIR, true);

  $logger->info("$logContext Starting...");
  $logger->info("$logContext Adding " . CREDITS . " initial credits to attorney users...");
  
  $rowsAffected = $usersModel->writeQuery("
    UPDATE IGNORE system_addressbook
    SET credits = " . CREDITS . "
    WHERE fkgroupid = " . User::ATTORNEY_GROUP_ID . "
          AND credits IS NULL"
  );

  $logger->info("$logContext Done!");