<?php
session_start();
error_reporting(0);
include_once("../includes/classes/adminsecurity.php");
global $AdminDAO;
$pklabelid = $_REQUEST['labelid'];
if($pklabelid>0)
{
	$AdminDAO->deleterows('system_label'," pklabelid='$pklabelid' ");
	 
}
?>