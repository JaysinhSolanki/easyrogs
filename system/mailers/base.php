<?php

  class BaseMailer {
    
    const TESTING_DOMAIN         = 'ezrogs.com';
    const TESTING_DEV_RECIPIENTS = ['easyrogs@mailinator.com', 'easyrogs@gmail.com'];
    const TESTING_PROD_RECIPIENT = 'easyrogs@gmail.com';
    
    const FROM_EMAIL = 'service@easyrogs.com';
    const FROM_NAME  = 'EasyRogs Service';

    static function sendEmail(
      $to, $subject, $body, 
      $fromName = self::FROM_NAME, $fromEmail = self::FROM_EMAIL, 
      $attachments = [], 
      $cc = [], $bcc = [] 
    ) {
      global $logger;

      // validate params
      if (!($to && $subject && $body)) { 
        return $logger->error("Trying to send email with missing parts: To: $to, Subject: $subject, Body Length: ". strlen($body));
      }

      // normalize params
      $to  = is_array($to)  ? $to  : [$to];
      $cc  = is_array($cc)  ? $cc  : [$cc];
      $bcc = is_array($bcc) ? $bcc : [$bcc];

      // do not send real emails on dev
      if ($_ENV['APP_ENV'] != 'prod') {
        $to = $cc = $bcc = self::TESTING_DEV_RECIPIENTS; // Send all emails to our own account on testing environments
      }
      else { // handle testing emails on prod
        foreach($to as &$email) {
          $emailParts = explode('@', $email);
          $domain = trim(strtolower($emailParts[1]));
          if ($domain === self::TESTING_DOMAIN) {
            $email = self::TESTING_PROD_RECIPIENT;
          }
        }
        $bcc = array_merge($bcc, self::TESTING_DEV_RECIPIENTS); // Make sure we keep a copy of all emails sent in production
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
        $logger->info("Mail sent to: " .json_encode($to). ", Subject: $subject, Body: --\n\r\n\r" .$body. "\n\r--\n\r\n\r" );
        if ($_ENV['APP_ENV'] != 'prod') {
          // Save copy of the last email
          $savedir = __DIR__ . '/../_dev';
          if(!is_dir($savedir)) { mkdir( $savedir, 0755, true ); }

          file_put_contents( $savedir. '/last-email.htm',             $body );
          file_put_contents( $savedir. '/last-email-attachments.txt', json_encode($attachments) );

          if (is_dir($savedir)) { 
            array_map( 'unlink', glob("$savedir/attach/*") );
          } else {
            mkdir( $savedir. '/attach', 0755, true ); 
          }
          foreach($attachments as $attachment) { 
            if (is_file($attachment['path'])) {
              try {
                @copy( $attachment['path'], $savedir .'/attach/'. $attachment['filename'] );
              } catch( Exception $e ) { }
            } else {
              $logger->warn("Email attachment not found: " . $attachment['path']);
            }
          }
        }
      }
      catch( Exception $e ) { 
        $logger->error('Send mail failed: ' . $e->getMessage()); 
      }
    }
  }
