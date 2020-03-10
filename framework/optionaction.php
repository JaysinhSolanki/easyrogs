<?php
include_once("../includes/classes/adminsecurity.php");
$optionnew		=	$_GET['optionnew'];
$attributeid	=	$_GET['attributeid'];
$optionarray		=	array('optionname','fkattributeid');
$optionvalues		=	array($optionnew,$attributeid);
$lastoptionid		=	$AdminDAO->insertrow("shop_tblattributeoption",$optionarray,$optionvalues);
//$optionarray		=	array();
//$optionarray['optionid']	=	$lastoptionid;
//$optionarray['optionname']	=	$optionnew;
echo $optionjason	=	json_encode(array("optionname" => $optionnew, "attributeid" => $lastoptionid));
?>