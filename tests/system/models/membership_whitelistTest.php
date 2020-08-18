<?php
use PHPUnit\Framework\TestCase;

class MembershipWhitelistTest extends TestCase
{
  protected $subject;

  protected function setUp(): void
  {
    global $membershipWhitelist;

    $this->subject = $membershipWhitelist;
  }

  public static function setUpBeforeClass(): void
  {
    global $membershipWhitelist;
    
    $membershipWhitelist->insertMultiple('membership_whitelist', [
      ['address'  => 'jeff@ezrogs.com'],
      ['domain'   => 'easyrogs.com'],
      ['address'  => 'expires@ezrogs.com', 'expires_at' => '2020-01-01'],
      ['domain'   => 'expires.com',        'expires_at' => '2020-01-01'],
    ]);
  }

  /**
   * @dataProvider whitelistedProvider
   */
  public function testWhitelisted($email, $expected)
  {
    $this->assertEquals($this->subject->isWhitelisted(['email' => $email]), $expected);
  }

  public function whitelistedProvider() {
    return [
      'Whitelisted address'            => ['jeff@ezrogs.com',     true],
      'Whitelisted domain'             => ['any@easyrogs.com',    true],
      'Whitelisted by address expired' => ['expires@ezrogs.com',  false],
      'Whitelisted by domain expired'  => ['expired@expires.com', false],
      'Not Whitelisted'                => ['not@whitelisted.com', false],
    ];
  }

  public static function tearDownAfterClass(): void
  {
    global $membershipWhitelist;
    $membershipWhitelist->wipeTable('membership_whitelist');
  }
}