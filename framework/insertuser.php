<?php
include_once("adminsecurity.php");
include_once("../includes/classes/class.phpmailer.php");
$fkgroupid	=	$_SESSION['groupid'];
$PHPMailer	=	new PHPMailer();
if($fname == '')
{
	msg(6,2);
}
if($lname == '')
{
	msg(8,2);
}
if($email)
{
	if($id==-1)
	{
		$uniqueemails	=	$AdminDAO->getrows('system_addressbook', 'COUNT(email) as totalemail',"email = '$email'");
		$uniqueemail	=	$uniqueemails[0]['totalemail'];
		if($uniqueemail > 0)
		{
			msg(9,2);
		}
	}
	else
	{
		$uniqueemails	=	$AdminDAO->getrows('system_addressbook', 'COUNT(email) as totalemail',"email = '$email' AND pkaddressbookid != '$id'");
		$uniqueemail	=	$uniqueemails[0]['totalemail'];
		if($uniqueemail>0)
		{
			msg(9,2);
		}
	}
}
else
{
	msg(10,2);
}
if($username)
{
	$uniqueunames	=	$AdminDAO->getrows('system_addressbook','pkaddressbookid','username', $username);
	$uniqueuname	=	$uniqueunames[0]['pkaddressbookid'];
	if($uniqueuname)
	{
		msg(21,2);
	}
}

if($pass == '')
{
	msg(22,2);
}
if($fkcountryid == 0)
{
	msg(13,2);
}
if($fkstateid == 0)
{
	msg(14,2);
}
$profilecompleted	=	1;

$fields				=	array('firstname','lastname','username','email','secondaryemail','password','phone','mobile','fax','fkcountryid','fkstateid','fkcityid','zip','street','address','isblocked');
$data				=	array($fname,$lname,$username,$email,$semail,$pass,$phone,$mobile,$fax,$fkcountryid,$fkstateid,$fkcityid,$zip,$street,$address,$isblocked);

if($id!="-1")
{
	$oldrecord			=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid =	'$id'");
	$AdminDAO->updaterow('system_addressbook',$fields,$data," pkaddressbookid = '$id'");
	
	$newrecord			=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid =	'$id'");
	$what				=	"Update Admin";
	$AdminDAO->logactivity($what,$oldrecord[0],$newrecord[0]);
}
msg(7);
?>