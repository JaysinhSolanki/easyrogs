<?php
include_once("../includes/classes/adminsecurity.php");
$arrayids = explode(',', $ids);
foreach($arrayids as $arr)
	{
	
	    $oldrecord				=	array();
		$categoryrecord			=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid   =    '$arr'");
		$oldrecord				=	array('categoryrecord'	=>	$categoryrecord[0]);
		$AdminDAO->deleterows('system_addressbook'," pkaddressbookid		=	'$arr'");
		$what	=	"Deleted USER.";
		$AdminDAO->logactivity($what,$oldrecord,'');
		
	}
	
	$delmsg	=	msg(91,1);
?>