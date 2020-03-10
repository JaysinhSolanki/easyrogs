<?php
require_once("adminsecurity.php");
$arrayids = explode(',',$ids);
foreach($arrayids as $arr)
{
	$AdminDAO->deleterows('discoveries'," id		=	'$arr'");
	//$AdminDAO->deleterows('questions',"discovery_id	=	'$id'");
	$AdminDAO->deleterows('discovery_questions',"discovery_id	=	'$id'");
}
$delmsg	=	msg(91,1);