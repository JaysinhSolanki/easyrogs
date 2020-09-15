<?php
  use PHPUnit\Framework\TestCase;

  class ERTestCase extends TestCase
  {
    protected $subject;

    function lastEmailBody() {
      return @file_get_contents(SYSTEMPATH . '_dev/last-email.htm');
    }

    function cleanupLastEmail() {
      @unlink(SYSTEMPATH . '_dev/last-email.htm');
    }
    
  }