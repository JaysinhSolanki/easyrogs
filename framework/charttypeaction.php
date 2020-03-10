<?php
require_once("adminsecurity.php");
$charttypename	=	trim($charttypename);
if($id !=	'-1')
{
	$charts	=	$AdminDAO->getrows('system_charttype',"*","charttypename	=	'$charttypename' AND pkcharttypeid	!=	'$id'");
	if(sizeof($charts)>0)
	{
		msg(231,2);	
	}
}
else
{
	$charts	=	$AdminDAO->getrows('system_charttype',"*","charttypename	=	'$charttypename'");
	if(sizeof($charts)>0)
	{
		msg(231,2);	
	}
}
if(trim($charttypename) == '')
{
	msg(230,2);	
}
$user=$_SESSION['addressbookid'];
$datetime=date("Y-m-d H:i:s");
$fields				=	array('charttypename');
$values				=	array($charttypename);
if($id != '-1')
{
    $AdminDAO->updaterow("system_charttype",$fields,$values," pkcharttypeid ='$id'");
}
else
{	$id	= $AdminDAO->insertrow("system_charttype",$fields,$values);
}
msg(7);
?>