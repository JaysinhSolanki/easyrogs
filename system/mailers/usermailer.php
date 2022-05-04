<?php

  class UserMailer extends BaseMailer  {
    const FORGOT_PASSWORD_SUBJECT   = 'Forgot Password';
    const VERIFICATION_CODE_SUBJECT = 'Verification Code';
    const SIGNUP_SUBJECT            = 'Welcome to AI4Discovery!';

    static function forgotPassword($user) {
      global $usersModel, $logger, $smarty;

      $user = is_array($user) ? $user : $usersModel->find($user);
      if (!$user) {
        return $logger->error('USER_MAILER_FORGOT_PASSWORD User not found');
      }

      // TODO: the action URL security for this is very week. We are just refactoring
      // legacy logic here but this needs to be fixed.
      $smarty->assign([
        'ASSETS_URL' => ASSETS_URL,
        'name'       => $usersModel->getFullName($user),
        'actionUrl'  => FRAMEWORK_URL . "resetpassword.php?verify=2&uid=$user[uid]",
        'actionText' => 'Recover Password'
      ]);
      $body    = $smarty->fetch('emails/forgot-password.tpl');
      $subject = self::FORGOT_PASSWORD_SUBJECT;
      $to      = $user['email'];

      parent::sendEmail($to, $subject, $body);
    }

    static function verificationCode($email, $code) {
      global $logger, $smarty;

      if (!($email && $code)) {
        return $logger->error("USER_MAILER_VERIFICATION_CODE Wrong arguments. Email: $email, code: $code");
      }

      $smarty->assign( [
        'ASSETS_URL' => ASSETS_URL,
        'code'       => $code
      ] );

      $body    = $smarty->fetch('emails/verification-code.tpl');
      $subject = self::VERIFICATION_CODE_SUBJECT;
      $to      = $email;

      parent::sendEmail($to, $subject, $body);
    }

    static function signup($email, $token) {
      global $logger, $smarty;

      if (!($email && $token)) {
        $logger->error("USER_MAILER_SIGNUP Wrong arguments. Email: $email, token: $token");
        throw new Exception('Email and Token are required.');
      }

      $smarty->assign([
        'ASSETS_URL' => ASSETS_URL,
        'signupUrl'  => DOMAIN . "finish-signup.php?t=$token"
      ]);

      $body    = $smarty->fetch('emails/signup.tpl');
      $subject = self::SIGNUP_SUBJECT;
      $to      = $email;

      parent::sendEmail($to, $subject, $body);
    }

  }
