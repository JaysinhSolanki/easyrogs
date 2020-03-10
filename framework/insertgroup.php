<?php
error_reporting(0);
include_once("adminsecurity.php");
$groupname		=	filter($_POST['groupname']);
$postedscreens	=	$_POST['screens'];
$groupid		=	$_GET['groupid'];
if($groupname	==	'')
{
	msg(297,2);
	//print"Group name must be provided.";
	//exit;	
}
$table		=	"system_groups";
$field		=	array('groupname','fkaddressbookid');
$value		=	array($groupname,$_SESSION['addressbookid']);
if($groupid=='-1')
{
	$groupid	=	$AdminDAO->insertrow($table,$field,$value);
}
else
{
	$AdminDAO->updaterow($table,$field,$value,"pkgroupid = '$groupid'");
}
foreach($_POST as $key=>$value)
{
	list($x,$screenid,$fieldid)	=	explode('_',$key);
	if($x=='fields')
	{
		$postedfields[]	=	$fieldid;
	}
	elseif($x=='actions')
	{
		$postedactions[]	=	$fieldid;
	}
}

$groupfieldz		=	$_SESSION['groupfieldz'];
$groupactionz		=	$_SESSION['groupactionz'];
$groupscreenz		=	$_SESSION['groupscreenz'];

if($postedscreens == '')
{
	$postedscreens =	array();
}
if($postedfields == '')
{
	$postedfields =	array();
}
if($postedactions == '')
{
	$postedactions =	array();
}

//starting to delete old data
if($groupid!='-1')
{
	//old data to be deleted
	$oldfields	=	@array_diff($groupfieldz,$postedfields);
	$oldactions	=	@array_diff($groupactionz,$postedactions);
	$oldscreens	=	@array_diff($groupscreenz,$postedscreens);
	if(@sizeof($oldfields) > 0)
	{
		foreach($oldfields as $dfields)
		{
			$AdminDAO->deleterows('system_groupfield'," fkgroupid='$groupid' AND fkfieldid='$dfields' ",'1');
		}
	}
	if(@sizeof($oldactions) > 0)
	{
		foreach($oldactions as $dactions)
		{
			$AdminDAO->deleterows('system_groupaction'," fkgroupid='$groupid' AND fkactionid='$dactions' ",'1');
		}
		
	}
	if(@sizeof($oldscreens) > 0)
	{
		foreach($oldscreens as $dscreens)
		{
			$AdminDAO->deleterows('system_groupscreen'," fkgroupid='$groupid' AND fkscreenid='$dscreens' ",'1');
		}
	}
}
//end deletion

//new data to be inserted
$newfields	=	@array_diff($postedfields,$groupfieldz);
$newactions	=	@array_diff($postedactions,$groupactionz);
$newscreens	=	@array_diff($postedscreens,$groupscreenz);

$gfields	=	array('fkgroupid','fkfieldid');
$gactions	=	array('fkgroupid','fkactionid');
$gscreens	=	array('fkgroupid','fkscreenid');

//starting to insert new data
if(@sizeof($newfields) > 0)
{
	foreach($newfields as $fieldz)
	{
		$data	=	array($groupid,$fieldz);
		$AdminDAO->insertrow("system_groupfield",$gfields,$data);
	}
}
if(@sizeof($newactions) > 0)
{
	foreach($newactions as $actionz)
	{
		$data	=	array($groupid,$actionz);
		$AdminDAO->insertrow("system_groupaction",$gactions,$data);
	}
}
if(@sizeof($newscreens) > 0)
{
	foreach($newscreens as $screenz)
	{
		$data	=	array($groupid,$screenz);
		$AdminDAO->insertrow("system_groupscreen",$gscreens,$data);
	}
}
msg(7);
//end insertion
//print"Group data has been saved.";
?>