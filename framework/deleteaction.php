<?php
session_start();
error_reporting(0);
include_once("../includes/classes/adminsecurity.php");
global $AdminDAO;
$actionid = $_REQUEST['actionid'];
if($actionid>0)
{
	$AdminDAO->deleterows('system_action'," pkactionid='$actionid' ");
	$AdminDAO->deleterows('system_groupaction'," fkactionid='$actionid' ");
}
?>