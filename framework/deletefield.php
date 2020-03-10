<?php
@session_start();
//error_reporting(0);
include_once("adminsecurity.php");
global $AdminDAO;
$fieldid = $_REQUEST['fieldid'];
$AdminDAO->displayquery =1;
if($fieldid>0)
{
	$AdminDAO->deleterows('system_field'," pkfieldid= :fieldid ", array("fieldid"=>$fieldid));
	$AdminDAO->deleterows('system_groupfield'," fkfieldid=:fieldid ", array("fieldid"=>$fieldid));
}