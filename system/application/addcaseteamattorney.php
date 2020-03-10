<?php
require_once("adminsecurity.php");

@session_start();
$attorney_name		=	$_POST['attorney_name'];
$case_id			=	$_POST['case_id'];
$makememberofmyteam	=	$_POST['makememberofmyteam'];
$fkaddressbookid	=	$_SESSION['addressbookid'];
$attorney_email		=	$_POST['attorney_email'];

/**
* Edit Case
**/
$attorney_id		=	$_POST['attorney_id'];
if($attorney_id > 0)
{
	$alreadyExistWhere	=	" AND a.id != '$attorney_id' ";
}
else
{
	$alreadyExistWhere	=	"";
}

if($makememberofmyteam == 1)
{
	$attorney_type	=	1;
	$alreadyExists	=	$AdminDAO->getrows("attorney a","*", "a.attorney_email = :attorney_email AND a.fkaddressbookid = :fkaddressbookid AND a.attorney_type = 1 $alreadyExistWhere", array(":attorney_email"=>$attorney_email,":fkaddressbookid"=>$fkaddressbookid), "attorney_name", "ASC");
	
}
else
{
	$attorney_type	=	3;
	$alreadyExists	=	$AdminDAO->getrows("attorney a ,case_team ct","a.*,ct.id as case_team_id", "attorney_email = :attorney_email AND ct.attorney_id = a.id AND ct.fkcaseid = :case_id AND ct.is_deleted != 1 $alreadyExistWhere ", array(":case_id"=>$case_id,":attorney_email"=>$attorney_email), "attorney_name", "ASC");
}

$type			=	'error';
if($attorney_name == "")
{
	$msg	=	"Please enter name.";
}
else if($attorney_email == "")
{
	$msg	=	"Please enter email.";
}
else if(sizeof($alreadyExists) > 0)
{
	$msg	=	"Email already exists.";
}
else
{
	/**
	* Edit Case
	**/
	if($attorney_id > 0)
	{
		$fields			=	array("attorney_name","attorney_email","attorney_type");
		$values			=	array($attorney_name,$attorney_email,$attorney_type);
		$AdminDAO->updaterow("attorney",$fields,$values," id = :id", array("id"=>$attorney_id));
		$msg			=	"Updated successfully.";
		$type			=	'success';
	}
	/**
	* Add Case
	**/
	else
	{
		$uid			=	$AdminDAO->generateuid('attorney');
		$fields			=	array("attorney_name","attorney_email","case_id","uid","attorney_type","fkaddressbookid");
		$values			=	array($attorney_name,$attorney_email,$case_id,$uid,$attorney_type,$fkaddressbookid);
		$attorney_id	=	$AdminDAO->insertrow("attorney",$fields,$values);
		
		if($attorney_type	= 3)
		{
			$caseteamfields			=	array("fkcaseid","attorney_id");
			$caseteamvalues			=	array($case_id,$attorney_id);
			$AdminDAO->insertrow("case_team",$caseteamfields,$caseteamvalues);
				
		}
		$msg			=	"Added successfully.";
		$type			=	'success';
	}
	/**
	* If attorney is already the member of EasyRogs then attach him to the case
	**/
	$alreadyEasyRogsMember	=	$AdminDAO->getrows("system_addressbook","*", "email = :email ", array(":email"=>$attorney_email));
	if(!empty($alreadyEasyRogsMember))
	{
		$alreadyEasyRog	=	$alreadyEasyRogsMember[0];
		$already_email	=	$alreadyEasyRog['email'];
		$already_pkid	=	$alreadyEasyRog['pkaddressbookid'];
		$fields_ac		=	array("attorney_id","case_id");
		$values_ac		=	array($already_pkid,$case_id);
		$AdminDAO->insertrow("attorneys_cases",$fields_ac,$values_ac);	
	}
}
$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);