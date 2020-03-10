<?php
session_start();
error_reporting(0);
include_once("../includes/classes/adminsecurity.php");
global $AdminDAO;

$operation	=	$_REQUEST['oper'];
$ids		=	trim($_REQUEST['id'],",");
if($operation == 'del')
{
	//$AdminDAO->displayquery = 1;
	$AdminDAO->updaterow("system_screen",array("isdeleted"),array(1),"pkscreenid IN ($ids)");
//	exit;
}
?>