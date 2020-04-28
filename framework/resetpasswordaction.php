<?php
//ob_start();
require_once("../system/bootstrap.php"); 
include_once($_SESSION['library_path']."classes/AdminDAO.php");	 
include_once($_SESSION['library_path']."classes/functions.php");	 
include_once($_SESSION['library_path']."classes/login.class.php"); 
include_once($_SESSION['library_path']."helper.php");

$AdminDAO		=	new AdminDAO();
$password		=	$_POST['password'];
$cpassword		=	$_POST['cpassword'];
$uid			=	$_POST['uid'];
if($uid != "")
{
	$customers				=	$AdminDAO->getrows('system_addressbook',"email","uid = '$uid'");
	$customeremail			=	$customers[0]['email'];	
	if($customeremail != "")
	{
		if($password == '')
		{
			msg(18,2);
		}
		else if($cpassword != "")
		{
			if($password != $cpassword)
			{
				//echo $error	=	$Error->display(19,2);
				msg(19,2);
			}
			else // update
			{
				// Successfull email after reset password
				
				$fields				=	array('password');
				$values				=	array($password);
				$AdminDAO->updaterow('system_addressbook',$fields,$values," uid = '$uid'");
				$to					=	array($customeremail);
				$emailmarker		=	"";
				$redirectme			=	DOMAIN.'userlogin.php';
				msg(131,2);
			}
		}
		else
		{	
			//echo $error	=	$Error->display(20,2);
			msg(20,2);
		}
	}
	else
	{
		//echo $error	=	$Error->display(132,2);
		msg(132,2);
	}
}
?>