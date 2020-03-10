<?php
require_once("adminsecurity.php");
$arrayids = explode(',',$ids);
foreach($arrayids as $arr)
{
	$AdminDAO->deleterows('system_chart'," pkchartid		=	'$arr'");
}
$delmsg	=	msg(91,1);