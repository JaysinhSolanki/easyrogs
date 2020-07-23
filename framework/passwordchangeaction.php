<?php
require_once("adminsecurity.php");
$addressbookid			=	$_SESSION['addressbookid'];
$oldpassword			=	$_REQUEST['oldpassword'];
$newpassword			=	$_REQUEST['newpassword'];
$newpasswordconfirm		=	$_REQUEST['newpasswordconfirm'];
if($oldpassword)
{
		$passwords		=	$AdminDAO->getrows('system_addressbook', 'password',"pkaddressbookid = '$id'");
		$password		=	$passwords[0]['password'];

		if($password != $oldpassword)
		{
			msg(16,2);
		}
}
else
{
	msg(17,2);
}
if($newpassword == '')
{
	msg(18,2);
}
if($newpasswordconfirm)
{
	if($newpassword != $newpasswordconfirm)
	{
		msg(19,2);
	}
}
else
{	
	msg(20,2);
}


if($id > 0)
{
	$fields		=	array('password');
	$values		=	array(password_hash($newpassword,PASSWORD_DEFAULT));
	$AdminDAO->updaterow('system_addressbook',$fields,$values," pkaddressbookid = '$id'");
	msg(7);
}
?>