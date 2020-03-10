<?php
require_once("adminsecurity.php");
$addressbookid		=	$_SESSION['addressbookid'];
/*$userfirstname		=	$_REQUEST['firstname'];
$userlastname		=	$_REQUEST['lastname'];
$useremail			=	$_REQUEST['email'];
$userphone			=	$_REQUEST['phone'];
$fkgroupid			=	$_REQUEST['fkgroupid'];*/
//$designation		=	$_REQUEST['designation'];
$uniqueemails		=	$AdminDAO->getrows('system_addressbook', 'COUNT(email) as totalemail',"email = '$useremail'");
$uniqueemail		=	$uniqueemails[0]['totalemail'];	
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
if($phone=="")
{
	msg(213,2);
}
if($fkgroupid=="")
{
	msg(223,2);
}
/*if($designation=="")
{
	msg(164,2);
}*/
if($uniqueemail>0)
{
	msg(9,2);
}
if($id > 0)
{
	$fields				=	array('firstname','lastname','email','phone');
	$values				=	array($firstname,$lastname,$email,$phone);
	$AdminDAO->updaterow('system_addressbook',$fields,$values," pkaddressbookid = '$addressbookid'");
}
msg(30);
?>