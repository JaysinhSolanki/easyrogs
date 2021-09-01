<?php
  require_once __DIR__ . '/../../_transient/hash-passwords.php';

  class HashPasswordsTest extends ERTestCase
  {
    const TEST_PASSWORD_HASHED = '$2y$10$fPhWfrp./VKcqocrJQ4lZOW87X4g3zt7oPkRKMXwAkiH02fUct9dy';
    const TEST_PASSWORD = 'test_password';

    public static function setUpBeforeClass(): void { global $usersModel;
      $usersModel->wipeTable(User::TABLE);

      $usersModel->create(['email' => 'plain@email.com',  'password' => self::TEST_PASSWORD]);
      $usersModel->create(['email' => 'hashed@email.com', 'password' => self::TEST_PASSWORD_HASHED]);
    }
    
    function testUpdatesPlainIgnoresHashesCanLogin() { global $usersModel;
      // run it couple of times, it should be idempotent
      for($repeat = 0; $repeat < 5; $repeat++) {
        HashPasswordsTransientJob::run();
        
        $user = $usersModel->getBy('system_addressbook', ['email' => 'plain@email.com'], 1);
        $this->assertTrue($user['password'] != $this->plainUser['password'], 'Plain text password changes' );
        $this->assertTrue(preg_match(BCRYPT_PASS_REGEXP, $user['password']) === 1, 'Password is correctly bcrypted');
        $this->assertTrue(password_verify(self::TEST_PASSWORD,  $user['password']), 'Hashed Can login');

        $user = $usersModel->getBy('system_addressbook', ['email' => 'hashed@email.com'], 1);
        $this->assertTrue($user['password'] != $this->hashedUser['password'], 'Hashed password doesnt change' );
        $this->assertTrue(password_verify(self::TEST_PASSWORD,  $user['password']), 'Hashed Can login');
      }
    }

    public static function tearDownAfterClass(): void { global $usersModel;
      $usersModel->wipeTable(User::TABLE);
    }

  }
