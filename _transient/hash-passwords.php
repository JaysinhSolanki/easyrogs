<?php 
  if (@$_ENV['APP_ENV'] != 'test') {
    require_once __DIR__ . '/../system/bootstrap.php';
  }  

  /**
   * THIS SCRIPT WILL:
   * Hash all unhashed (bcrypt) DB passwords
  */
  
  define('LOG_CONTEXT', 'HASH_PASSWORDS');
  define('PAGINATION_LIMIT', 1000);
  define('BCRYPT_PASS_REGEXP', "#^[$]2[abxy]?[$](?:0[4-9]|[12][0-9]|3[01])[$][./0-9a-zA-Z]{53}$#"); // https://en.wikipedia.org/wiki/Bcrypt

  // -----
  $logger = new EasyRogs\Logger(LOGS_DIR, true);

  class HashPasswordsTransientJob {

    static function run() { global $logger, $usersModel;
      $logger->info(LOG_CONTEXT . " Starting...");
      $usersCount = $usersModel->totalCount('system_addressbook');
      $logger->info(LOG_CONTEXT . " Will process $usersCount users");
      
      $offset = 0;
      while ($users = $usersModel->getSorted('system_addressbook', ['pkaddressbookid' => 'ASC'], $offset, PAGINATION_LIMIT)) {
        $logger->info(LOG_CONTEXT . " Will process " . count($users) .  " users");
    
        foreach($users as $user) {
          if (!preg_match(BCRYPT_PASS_REGEXP, $user['password'])) { // is NOT already encrypted?
            $logger->info(LOG_CONTEXT . ' UPDATE! ' . json_encode(['org_password' => $user['password'], 'password' => password_hash($user['password'], PASSWORD_DEFAULT), 'pkaddressbookid' => $user['pkaddressbookid']]) . "\n");
            $usersModel->update('system_addressbook', ['password' => password_hash($user['password'], PASSWORD_DEFAULT)], ['pkaddressbookid' => $user['pkaddressbookid']]);
          }
          else {
            $logger->debug(LOG_CONTEXT . ' Won\'t update ' . json_encode(['org_password' => $user['password'], 'password' => password_hash($user['password'], PASSWORD_DEFAULT), 'pkaddressbookid' => $user['pkaddressbookid']]) . "\n");
          }
        }
        $offset += PAGINATION_LIMIT;
        echo "-- Offset: $offset\n";
      }
    
      $logger->info(LOG_CONTEXT . " Done!");
    }
  }

  if (@$_ENV['APP_ENV'] != 'test') { HashPasswordsTransientJob::run(); }