<?php

class BaseMailer
{

  // ['easyrogs@mailinator.com', 'easyrogs@gmail.com', 'optimizenetz@gmail.com', 'jeff@jeffschwartzlaw.com'];
  // prod = 'easyrogs@gmail.com';
  // const TESTING_DOMAIN         = 'ezrogs.com';
  // const TESTING_DEV_RECIPIENTS = ['optimizenetz@gmail.com', 'testnetz321@gmail.com'];
  // const TESTING_PROD_RECIPIENT = 'testnetz321@gmail.com';

  const TESTING_DOMAIN         = 'ezrogs.com';
  const TESTING_DEV_RECIPIENTS = ['easyrogs@gmail.com'];
  const TESTING_PROD_RECIPIENT = 'easyrogs@gmail.com';
  // const TESTING_DOMAIN         = 'ezrogs.com';
  // const TESTING_DEV_RECIPIENTS = ['easyrogs@mailinator.com', 'easyrogs@gmail.com', 'optimizenetz@gmail.com', 'jeff@jeffschwartzlaw.com'];
  // const TESTING_PROD_RECIPIENT = 'easyrogs@gmail.com';
  
  const FROM_EMAIL = 'service@easyrogs.com';
  const FROM_NAME  = 'AI4Discovery Service';

  static function sendEmail(
    $to,
    $subject,
    $body,
    $fromName = self::FROM_NAME,
    $fromEmail = self::FROM_EMAIL,
    $attachments = [],
    $cc = [],
    $bcc = []
  ) {
    global $logger;

    // validate params
    if (!($to && $subject && $body)) {
      return $logger->error("Trying to send email with missing parts: To: $to, Subject: $subject, Body Length: " . strlen($body));
    }

    // normalize params
    $to  = is_array($to)  ? $to  : [$to];
    $cc  = is_array($cc)  ? $cc  : [$cc];
    $bcc = is_array($bcc) ? $bcc : [$bcc];
    $logger->info("recipients(before): to(" . json_encode($to) . ") cc(" . json_encode($cc) . ") bcc(" . json_encode($bcc) . ")");

    // force all email to Test Recipient untill we are on easyrogs.com
    $to = $cc = $bcc = self::TESTING_DEV_RECIPIENTS;

    // do not send real emails on dev
    if ($_ENV['APP_ENV'] != 'prod') {
      $to = $cc = $bcc = self::TESTING_DEV_RECIPIENTS; // Send all emails to our own account on testing environments
    } else { // handle testing emails on prod
      foreach ($to as &$email_ref) {
        $emailParts = explode('@', $email_ref);
        $domain = trim(strtolower($emailParts[1]));
        if ($domain === self::TESTING_DOMAIN) {
          $email_ref = self::TESTING_PROD_RECIPIENT;
        }
      }
      $bcc = array_merge($bcc, self::TESTING_DEV_RECIPIENTS); // Make sure we keep a copy of all emails sent in production
    }
    //$logger->info( "recipients(after): to(".json_encode($to).") cc(".json_encode($cc).") bcc(".json_encode($bcc).")" );

    // initialize php mailer
    $mail = new PHPMailer();
    $mail->isHTML(true);
    $mail->addReplyTo($fromEmail, $fromName);
    $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);

    // add parts
    $mail->Subject = $subject;
    $mail->Body    = $body;

    foreach ($to  as $email_addr) {
      $mail->addAddress($email_addr);
    }
    foreach ($cc  as $email_addr) {
      $mail->AddCC($email_addr);
    }
    foreach ($bcc as $email_addr) {
      $mail->AddBCC($email_addr);
    }
    foreach ($attachments as $attachment) {
      $mail->addAttachment($attachment['path'], $attachment['filename']);
    }

    // send email
    try {
      $sendIt = $_ENV['APP_ENV'] === 'prod' || $_ENV['DEV_SEND_EMAILS'];
      if ($sendIt) {
        $mail->send();
      }
      $logger->info("Mail " . ($sendIt ? "sent" : "skipped") . " to: " . json_encode($to) . ", Subject: $subject, Body: --\n\r\n\r" . $body . "\n\r--\n\r\n\r");

      if ($_ENV['APP_ENV'] != 'prod') {
        // Save copy of the last email
        $savedir = _DIR_ . '/../_dev';
        if (!is_dir($savedir)) {
          mkdir($savedir, 0755, true);
        }

        file_put_contents($savedir . '/last-email.htm',             $body);
        file_put_contents($savedir . '/last-email-attachments.txt', json_encode($attachments, JSON_PRETTY_PRINT + JSON_UNESCAPED_LINE_TERMINATORS + JSON_UNESCAPED_SLASHES));

        if (is_dir($savedir)) {
          array_map('unlink', glob("$savedir/attach/*"));
        } else {
          mkdir($savedir . '/attach', 0755, true);
        }
        foreach ($attachments as $attachment) {
          if (is_file($attachment['path'])) {
            try {
              @copy($attachment['path'], $savedir . '/attach/' . $attachment['filename']);
            } catch (Throwable $e) {
            }
          } else {
            $logger->warn(["Email attachment not found", $attachment['path']]);
          }
        }
      }

      return $mail;
    } catch (Throwable $e) {
      $logger->error(['Send mail failed', $e]);
    }
  }
}