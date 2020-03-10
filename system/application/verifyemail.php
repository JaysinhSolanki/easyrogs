<?php
@session_start();
include_once("../library/helper.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$code							=	mt_rand(99999,99999999);
$_SESSION['verification_code'] 	= 	$code;
$email							=	$_POST['email'];
$html							=	"Verification code is $code.";
$senderName						=	"EasyRogs Service";
$senderEmail					=	"service@easyrogs.com";
ob_start();
?>
<p>Verification code is <?php echo $code ?>.</p>
<br>
All rights reserved &copy; <?php echo date('Y') ?> EasyRogs. U.S. Patent Pending
<?php
$html = ob_get_contents(); 
ob_clean();
send_email(array($email),"EasyRogs Verification Code",$html,$senderEmail,$senderName,1,array(),array(),array());
echo "success";
