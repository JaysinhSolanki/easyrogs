<?php
@session_start();
if(@sizeof($_POST) > 0)
{
	extract($_POST);
}
if(empty($_SESSION['includes_path']))
{
	$message	=	"Sorry. but includes paths are not set up";
	header("Location: userlogin.php?msg=$msg");
	exit;
}
//**********************************************************
$path	=	$_SESSION['library_path'];//str_replace("//","/",$_SERVER['DOCUMENT_ROOT'].$str[0]); 
/*********************************************************/
require_once($_SESSION['system_path']."bootstrap.php");
require_once($_SESSION['system_path']."library/helper.php");
require_once($path."classes/functions.php");
require_once($path."classes/paging.class.php");
require_once($path."classes/usersecurity.php");
require_once($path."classes/AdminDAO.php");

if(!isset($AdminDAO))
{
	$AdminDAO 	= 	new AdminDAO;
}
if(!isset($Paging))
{
	$Paging	=	new PagedResults();
}
if(!isset($userSecurity))
{
	$userSecurity = new userSecurity;
}

$addressbookid		=	$_SESSION['addressbookid'];
$groupid			=	$_SESSION['groupid'];



/******************************************************/
if(empty($addressbookid))
{
	header("Location: ".$_SESSION['gumption_path']."/userlogin.php");
	exit;
}