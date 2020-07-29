<?php
@session_start();
if( @sizeof($_POST) ) {
	extract( $_POST );
}
if( empty($_SESSION['includes_path']) ) {
	$message	=	"Sorry. but includes paths are not set up";
	header("Location: userlogin.php?msg=$msg");
	exit;
}
require_once(__DIR__.'/../system/bootstrap.php');
require_once(SYSTEMPATH."library/helper.php");
$path = $_SESSION['library_path'];
require_once($path."classes/functions.php");
require_once($path."classes/paging.class.php");
require_once($path."classes/usersecurity.php");

// if(!isset($AdminDAO))
// {
// 	$AdminDAO 	= 	new AdminDAO;
// }
if( !isset($Paging) ) {
	$Paging = new PagedResults();
}
if( !isset($userSecurity) ) {
	$userSecurity = new userSecurity;
}

$addressbookid = $_SESSION['addressbookid'];
$groupid	   = $_SESSION['groupid'];

/******************************************************/
if( empty($addressbookid) ) {
	header("Location: ".DOMAIN."../userlogin.php");
	exit;
}