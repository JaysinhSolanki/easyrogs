<?php
require_once("adminsecurity.php");
$id				=	$_POST['id'];
$case_team_id	=	$_POST['case_team_id'];
$attorney_type	=	$_POST['attorney_type'];
$case_id		=	$_POST['case_id'];
$deletefromattr_team	=	$_POST['deletefromattr_team'];
if($attorney_type == 3)
{
	if($deletefromattr_team == 1)
	{
		$isMyTeamMember	=	$AdminDAO->getrows("attorney","*", "id = :id AND  attorney_type = 1", array(":id"=>$id), "attorney_name", "ASC");
		if(sizeof($isMyTeamMember) > 0)
		{
			$AdminDAO->deleterows('attorney'," id = :id", array("id"=>$id));
		}	
	}	
	//$AdminDAO->deleterows('case_team'," id = :id", array("id"=>$case_team_id));
	$fields	=	array('is_deleted');
	$values	=	array(1);
	$AdminDAO->updaterow("case_team",$fields,$values,"id ='{$case_team_id}'");
	echo $case_id;
}
else
{
	$AdminDAO->deleterows('attorney'," id = :id", array("id"=>$id));	
	if($attorney_type == 2)
	{
		$AdminDAO->deleterows("client_attorney","attorney_id = :id", array("id"=>$id));
	}
}
