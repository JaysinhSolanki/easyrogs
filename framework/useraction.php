<?php
require_once("adminsecurity.php");
if($firstname=="")
{
	msg(209,2);
}
if($lastname=="")
{
	msg(210,2);
}
if($email=="")
{
	msg(211,2);
}
if($password=="")
{
	msg(212,2);
}
if($phone=="")
{
	msg(213,2);
}
if($fkgroupid=="")
{
	msg(223,2);
}

$datetime			=	date("Y-m-d H:i:s");
$fields				=	array('firstname','lastname','email','password','phone','fkgroupid','emailverified');
$values				=	array($firstname,$lastname,$email,password_hash("rasmuslerdorf", PASSWORD_DEFAULT),$phone,$fkgroupid,1);
if($id != '-1')
{   
	$AdminDAO->updaterow('system_addressbook',$fields,$values,"pkaddressbookid   =    '$id'");	
}
else
{	
	$id	= $AdminDAO->insertrow("system_addressbook",$fields,$values);
}
msg(7);