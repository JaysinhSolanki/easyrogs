<?php
require_once("adminsecurity.php");
$chartname	=	trim($chartname);
if($id !=	'-1')
{
	$charts	=	$AdminDAO->getrows('system_chart',"*","chartname	=	'$chartname' AND pkchartid	!=	'$id'");
	if(sizeof($charts)>0)
	{
		msg(227,2);	
	}
}
else
{
	$charts	=	$AdminDAO->getrows('system_chart',"*","chartname	=	'$chartname'");
	if(sizeof($charts)>0)
	{
		msg(227,2);	
	}
}
if(trim($chartname) == '')
{
	msg(226,2);	
}
if($fkcharttypeid<1)
{
	msg(217,2);	
}
if(trim($description) == '')
{
	msg(222,2);	
}
if(trim($charttitle) == '')
{
	msg(228,2);	
}
if(trim($chartsubtitle) == '')
{
	msg(232,2);	
}
if(trim($xaxis_staticname) == '' && trim($xaxis_queryname) == '')
{
		msg(233,2);	
}
if(trim($yaxis_staticname) == '' && trim($yaxis_queryname) == '')
{
		msg(234,2);	
}
if(trim($yaxis_staticdata) == '' && trim($yaxis_querydata) == '')
{
		msg(235,2);	
}
$user=$_SESSION['addressbookid'];
$datetime=date("Y-m-d H:i:s");
$fields				=	array('chartname','fkcharttypeid','description','charttitle','chartsubtitle','xaxis_staticname','xaxis_queryname','yaxis_staticname','yaxis_queryname','yaxis_staticdata','yaxis_querydata');
$values				=	array($chartname,$fkcharttypeid,$description,$charttitle ,$chartsubtitle,$xaxis_staticname,$xaxis_queryname,$yaxis_staticname,$yaxis_queryname,$yaxis_staticdata,$yaxis_querydata);
if($id != '-1')
{
    $AdminDAO->updaterow("system_chart",$fields,$values," pkchartid ='$id'");
}
else
{	$id	= $AdminDAO->insertrow("system_chart",$fields,$values);
}
msg(7);
?>