<?php

class SignupControllerTest extends ERTestCase
{
  const INVITATION_EMAIL          = 'invitation@easyrogs.com';
  const EXISTING_EMAIL            = 'verfified@easyrogs.com';
  const EXISTING_UNVERIFIED_EMAIL = 'unverfified@easyrogs.com';
  const INVITATION_UID            = 'test';
  const EXPIRED_INVITATION_UID    = 'expired';
  
  const REQUIRED_FIELDS = [
    'firstname', 
    'lastname', 
    'email', 
    'password', 
    'terms', 
    'barnumber'
  ];
  
  const SAMPLE_PAYLOAD  = [
    'is_attorney'    => 1,
    'barnumber'      => '12345',
    'firstname'      => 'Test',
    'lastname'       => 'User',
    'email'          => 'some@email.com',
    'password'       => 'test',
    'terms'          => 1,
    'invitation_uid' => ''
  ];

  public static function setUpBeforeClass(): void { global $usersModel, $invitationsModel;
    $usersModel->wipeTable(User::TABLE);
    $usersModel->wipeTable('invitations');

    $usersModel->create(['email' => self::EXISTING_EMAIL, 'emailverified' => 1, 'fkgroupid' => User::ATTORNEY_GROUP_ID]);
    $usersModel->create(['email' => self::EXISTING_UNVERIFIED_EMAIL, 'emailverified' => 0, 'fkgroupid' => User::ATTORNEY_GROUP_ID]);
    $invitedUser = $usersModel->create(['email' => self::INVITATION_EMAIL, 'emailverified' => 0, 'fkgroupid' => User::ATTORNEY_GROUP_ID]);
    $invitationsModel->insertMultiple('invitations', [
      ['uid' => self::INVITATION_UID,         'attorney_id' => $invitedUser['pkaddressbookid'], 'status' => 1],
      ['uid' => self::EXPIRED_INVITATION_UID, 'attorney_id' => $invitedUser['pkaddressbookid'], 'status' => 2],
    ]);
  }
  protected function setUp(): void{
    @session_destroy();
    @session_start();
    
    $this->subject = new SignupController();
    $this->cleanupLastEmail();
    
    ob_start();
  }

  /**
   * @dataProvider showProvider
   */
  public function testShow($get, $expectedBodyRegexp) {
    $_GET = $get;
    $this->subject->show();

    $this->assertEquals(http_response_code(), false, 'Status code is OK'); // 200
    $this->assertTrue(preg_match($expectedBodyRegexp, ob_get_contents()) === 1, 'Response body is correct');
  }

  public function showProvider() {
    return [
      'No parameters'             => [[],                                      '/Create your account/'],
      'Unexistent Invitation UID' => [['uid' => 'invalid-uid'],                '/URL is invalid or expired./'],
      'Expired Invitation UID'    => [['uid' => self::EXPIRED_INVITATION_UID], '/URL is invalid or expired./'],
      'Valid Invitation UID'      => [['uid' => self::INVITATION_UID],         '/' . self::INVITATION_EMAIL . '/'],
    ];
  }

  /**
   * @dataProvider startValidationProvider
   */
  public function testStartValidation($post, $expectedBodyRegexp, $expectedResponseCode) {
    $_POST = $post;

    $this->subject->start();
    $this->assertTrue(preg_match($expectedBodyRegexp, ob_get_contents()) === 1, 'Response body is correct');
    $this->assertEquals(http_response_code(), $expectedResponseCode, 'Status code is correct');
  }

  function startValidationProvider() {
    $data = [];

    foreach(self::REQUIRED_FIELDS as $nullField) {
      $data["Missing $nullField"] = [
        array_merge(self::SAMPLE_PAYLOAD, [$nullField => null]), '/Please fill the required fields/', 400
      ];
    }

    return $data;
  }

  function testStartWithExistingUser() {
    $_POST = array_merge(self::SAMPLE_PAYLOAD, ['email' => self::EXISTING_EMAIL]);
    $this->subject->start();
    $this->assertTrue(preg_match('/Email address already in use/', ob_get_contents()) === 1, 'Address in use message in body');
    $this->assertEquals(http_response_code(), 409, 'Conflict response code');
  }

  function testStartSuccess() {
    $_POST = array_merge(self::SAMPLE_PAYLOAD);
    $this->subject->start();
    $this->assertTrue(preg_match('/Success!/', ob_get_contents()) === 1, 'Body has success message');
    $this->assertTrue(preg_match('/finish-signup.php/', $this->lastEmailBody()) === 1, 'Email sent with finish signup link');
    $this->assertEquals(http_response_code(), 200, 'Status code is success');
  }

  function testFinishWithInvalidToken() {
    $_GET['t'] = 'invalid-token';
    $this->subject->finish();
    $this->assertEquals(http_response_code(), 302, 'User is redirected');
  }

  function testFinishWithExistingUser() { global $usersModel;
    $_GET['t'] = $this->subject->jwtEncodeToken(
      array_merge(self::SAMPLE_PAYLOAD, ['email' => self::EXISTING_UNVERIFIED_EMAIL])
    );
    
    $existingUser = $usersModel->getByEmail(self::EXISTING_UNVERIFIED_EMAIL);
    $this->assertEquals('', $existingUser['firstname'], 'User data is empty');
    $this->assertEquals(0, $existingUser['emailverified'], 'User email is NOT verified');
    $this->assertEquals(null, $_SESSION['addressbookid'], 'No logged in user');

    $this->subject->finish();
    
    $existingUser = $usersModel->getByEmail(self::EXISTING_UNVERIFIED_EMAIL);

    $this->assertEquals(self::SAMPLE_PAYLOAD['firstname'], $existingUser['firstname'], 'User data is updated');
    $this->assertEquals(1, $existingUser['emailverified'], 'User email is verified');
    $this->assertEquals($_SESSION['addressbookid'], $existingUser['pkaddressbookid'], 'Logs the user in');
    $this->assertEquals(http_response_code(), 302, 'Redirects');
  }

  function testFinishWithExistingInvitation() { global $usersModel, $invitationsModel;
    $_GET['t'] = $this->subject->jwtEncodeToken(
      array_merge(self::SAMPLE_PAYLOAD, [
        'email'          => self::INVITATION_EMAIL,
        'invitation_uid' => self::INVITATION_UID
      ])
    );
    
    $invitation  = $invitationsModel->getBy('invitations', ['uid' => self::INVITATION_UID], 1);
    $invitedUser = $usersModel->getByEmail(self::INVITATION_EMAIL);
    
    $this->assertEquals(Invitation::STATUS_NEW, $invitation['status'], 'Invitation is not used');
    $this->assertEquals('', $invitedUser['firstname'], 'User data is empty');
    $this->assertEquals(null, $_SESSION['addressbookid'], 'No logged in user');

    $this->subject->finish();
    
    $invitedUser = $usersModel->getByEmail(self::INVITATION_EMAIL);
    $invitation = $invitationsModel->getBy('invitations', ['uid' => self::INVITATION_UID], 1);

    $this->assertEquals(Invitation::STATUS_USED, $invitation['status'], 'Redeems the invitation');
    $this->assertEquals(self::SAMPLE_PAYLOAD['firstname'], $invitedUser['firstname'], 'User data is updated');
    $this->assertEquals($_SESSION['addressbookid'], $invitedUser['pkaddressbookid'], 'Logs the user in');
    $this->assertEquals(http_response_code(), 302, 'Redirects');
  }

  function testWithNewUser() { global $usersModel;
    $initialCount = $usersModel->totalCount(User::TABLE);
    
    $user = $usersModel->getByEmail(self::SAMPLE_PAYLOAD['email']);
    $this->assertEmpty($user, 'User email doesnt exists initially');

    $_GET['t'] = $this->subject->jwtEncodeToken(self::SAMPLE_PAYLOAD);
    $this->subject->finish();

    $currentCount = $usersModel->totalCount(User::TABLE);
    $user = $usersModel->getByEmail(self::SAMPLE_PAYLOAD['email']);
    
    $this->assertGreaterThan($initialCount, $currentCount, 'User was created');
    $this->assertNotEmpty($user, 'User exists by email');
    $this->assertEquals($_SESSION['addressbookid'], $user['pkaddressbookid'], 'Logs the user in');
    $this->assertEquals(http_response_code(), 302, 'Redirects');
  }

  protected function tearDown(): void { ob_end_clean(); }
  public static function tearDownAfterClass(): void { global $usersModel, $invitationsModel;
    $usersModel->wipeTable(User::TABLE);
    $invitationsModel->wipeTable('invitations');
  }
}