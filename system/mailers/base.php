<?php

  class BaseMailer {
    const FROM_EMAIL = 'service@easyrogs.com';
    const FROM_NAME  = 'EasyRogs Service';

    static function sendEmail($to, $subject, $body, 
      $fromName = self::FROM_NAME, 
      $fromEmail = self::FROM_EMAIL, 
      $attachments = [], $cc = [], $bcc = [] 
    ) {
      global $logger;

      // do not send real emails on dev
      if ( $_ENV['APP_ENV'] != 'prod') {
        $to = $cc = $bcc = ['easyrogs@mailinator.com', 'easyrogs@gmail.com'];
      }
      
      // validate params
      if (!($to && $subject && $body)) { 
        return $logger->error("Trying to send email with missing parts: To: $to, Subject: $subject, Body Length: ". strlen($body));
      }

      // normalize params
      foreach([$to, $cc, $bcc, $attachments] as &$part) {
        $part = is_array($part) ? $part : [$part];
      }

      // initialize php mailer
      $mail = new PHPMailer();
      $mail->isHTML(true);
      $mail->addReplyTo($fromEmail, $fromName);
      $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);

      // add parts
      $mail->Subject = $subject;
      $mail->Body    = $body;

      foreach($to  as $email) { $mail->addAddress($email); }
      foreach($cc  as $email) { $mail->AddCC($email); }
      foreach($bcc as $email) { $mail->AddBCC($email); }
      foreach($attachments as $attachment) { 
        $mail->addAttachment($attachment['path'], $attachment['filename']);
      }
      
      // send email
      try { 
        $mail->send(); 
      }
      catch( Exception $e ) { 
        $logger->error('Send mail failed: ' . $e->getMessage()); 
      }
    }
  }