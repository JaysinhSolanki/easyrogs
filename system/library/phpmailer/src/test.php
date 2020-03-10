<?php
require 'PHPMailer.php';
require 'Exception.php';

//require 'SMTP.php';
$mail = new PHPMailer(true);
/* Open the try/catch block. */
try {
	$mail->isHTML(TRUE);
   /* Set the mail sender. */
   $mail->setFrom('umar@gumption.pk', 'Darth Vader');

   /* Add a recipient. */
   $mail->addAddress('GumptionTechnologies@gmail.com', 'Emperor');

   /* Set the subject. */
   $mail->Subject = 'Force';

   /* Set the mail message body. */
   $mail->Body = '<b>There is a great disturbance in the Force.</b>';

   /* Finally send the mail. */
   $mail->send();
}
catch (Exception $e)
{
   /* PHPMailer exception. */
   echo $e->errorMessage();
}
catch (\Exception $e)
{
   /* PHP exception (note the backslash to select the global namespace Exception class). */
   echo $e->getMessage();
}