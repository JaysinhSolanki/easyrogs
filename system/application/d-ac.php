<?php
@session_start();
require_once("../settings.php");
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
$randno			=	rand();
$emailsarray	=	array('jeff_schwartz@yahoo.com','EasyRogs@gmail.com');
//$emailsarray	=	array('gumptiondevelopers@outlook.com','gumptiondevelopers@yahoo.com');
echo "<h1>Emails updated successfully.<h1>";
echo "<br><br>";
foreach($emailsarray as $email)
{
	$fields			=	array("email");
	$values			=	array($randno.$email);
	$AdminDAO->updaterow("system_addressbook",$fields,$values,"email = '$email'");
	echo "<h1>".$email." => ".$randno.$email."</h1>";
}


