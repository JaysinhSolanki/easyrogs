<?php
require_once("adminsecurity.php");
$arrayids = explode(',',$ids);
foreach($arrayids as $arr)
{
	$AdminDAO->deleterows('system_charttype'," pkcharttypeid		=	'$arr'");
}
$delmsg	=	msg(91,1);