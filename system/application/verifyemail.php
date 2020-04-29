<?php
  require_once __DIR__ . '/../bootstrap.php';

  $email = $_REQUEST['email'];

  $_SESSION['verification_code'] = $code = mt_rand(99999,99999999);

  UserMailer::verificationCode($email, $code);

  echo "success";
