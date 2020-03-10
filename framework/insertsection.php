<?php
include("adminsecurity.php");
if(sizeof($_POST)>0)
{
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	$sectionname			=		$_POST['sectionname'];
	$sectionnamehebrew		=		 "";//$_POST['sectionnamehebrew'];
	//$sectionicon			=		$_POST['sectionicon'];
    $status					=		$_POST['status'];
    $sectionid				=		$_POST['sectionid'];
	$sortorder				=		$_POST['sortorder'];
	if($sectionname=='')
	{
		msg(46,2);
	}
	$fields			=	array('sectionname','sectionnamehebrew','status','sortorder');
	$data			=	array($sectionname,$sectionnamehebrew,$status,$sortorder);
	if($sectionid==-1)
	{
		$AdminDAO->insertrow("system_section",$fields,$data);
	}
	else
	{
		$AdminDAO->updaterow("system_section",$fields,$data,"pksectionid='$sectionid'");
	}
}
else
{
	msg(47,2);
}
msg(7);
?>