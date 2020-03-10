<?php
require_once("adminsecurity.php");
@session_start();
$randno			=	rand();
$user_id		=	$_SESSION['addressbookid'];
$orignal_email	=	$_SESSION['loggedin_email'];
$new_email		=	$randno.$orignal_email;

/*$fields		=	array("email","orignal_email","isclosed");
$values			=	array($new_email,$orignal_email,1);
$AdminDAO->updaterow("system_addressbook",$fields,$values,"pkaddressbookid = '$user_id'");

$fields1		=	array("attorney_email");
$values1		=	array($new_email);
$AdminDAO->updaterow("attorney",$fields1,$values1,"attorney_email = '$orignal_email'");*/
$AdminDAO->deleterows('attorney'," attorney_email = :attorney_email", array("attorney_email"=>$orignal_email)); 
$AdminDAO->deleterows('system_addressbook',"pkaddressbookid = :pkaddressbookid", array("pkaddressbookid"=>$user_id));


setcookie('rememberme','', time()+(86400*30), "/");
setcookie("rememberme",'', time()-3600);
$admin_url		=	$_SESSION['admin_url'];
@session_destroy();
header("Location:".$admin_url."userlogin.php?loggedout=1");
exit; 