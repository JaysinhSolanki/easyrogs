<?php
require_once("adminsecurity.php");
$attorney_type		=	$_POST['attorney_type'];
$attorney_name		=	$_POST['attorney_name'];
$client_id			=	$_POST['client_id'];
$case_id			=	$_POST['case_id'];
$fkaddressbookid	=	$_SESSION['addressbookid'];
$attorney_email		=	$_POST['attorney_email'];
$editattorney_id	=	$_POST['editattorney_id'];
$alreadyExists		=	array();
if($attorney_type == 1)
{
	$alreadyExists	=	$AdminDAO->getrows("attorney","*", "attorney_email = :attorney_email AND fkaddressbookid = :fkaddressbookid AND attorney_type = :attorney_type ", array(":attorney_type"=>$attorney_type,":attorney_email"=>$attorney_email,":fkaddressbookid"=>$fkaddressbookid));
	$case_id		=	0;
}
else if($attorney_type == 2 && sizeof($client_id) > 0)
{
	$clientIdsList	=	implode(",",$client_id);
	if($editattorney_id>0)
	{
		$alreadyExists	=	$AdminDAO->getrows("attorney a,client_attorney ca ","*", "a.id != :id AND a.case_id = :case_id AND a.attorney_email = :attorney_email  AND a.attorney_type = :attorney_type AND ca.attorney_id = a.id ", array(":id"=>$editattorney_id,":case_id"=>$case_id,":attorney_email"=>$attorney_email,":attorney_type"=>2));
	}
	else
	{
		$alreadyExists	=	$AdminDAO->getrows("attorney a,client_attorney ca ","*", "a.case_id = :case_id AND a.attorney_email = :attorney_email  AND a.attorney_type = :attorney_type AND ca.attorney_id = a.id ", array(":case_id"=>$case_id,":attorney_email"=>$attorney_email,":attorney_type"=>2));
	}
	$case_id		=	$_POST['case_id'];  
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
else if(sizeof($client_id) < 1 && $attorney_type == 2)
{
	$msg	=	"Please select client.";
}
else if(sizeof($alreadyExists) > 0)
{
	$attorney_id	=		$alreadyExists[0]['id'];
	$msg			=		"Email already attached.";
}
else
{
	$fields			=	array("attorney_name","attorney_email","case_id","attorney_type","fkaddressbookid");
	$values			=	array($attorney_name,$attorney_email,$case_id,$attorney_type,$fkaddressbookid);
	if($editattorney_id>0 && $attorney_type == 2)
	{
		/**
		* GET old email of attorney and deatach it from case
		**/
		$attrDetails	=	$AdminDAO->getrows("attorney a,system_addressbook sa","*", "a.id = :id AND a.attorney_email = sa.email", array(":id"=>$editattorney_id));
		//dump($attrDetails);
		if(!empty($attrDetails))
		{
			$attrDetail			=	$attrDetails[0];
			$oldAttachedUser	=	$attrDetail['pkaddressbookid'];
			$AdminDAO->deleterows('attorneys_cases'," attorney_id = :attorney_id AND case_id = :case_id", array("attorney_id"=>$oldAttachedUser,"case_id"=>$case_id));
		}
		$AdminDAO->updaterow("attorney",$fields,$values," id = :id", array("id"=>$editattorney_id));
		if($attorney_type == 2)
		{
			$attorney_id	= $editattorney_id;
			$AdminDAO->deleterows('client_attorney'," attorney_id = :attorney_id", array("attorney_id"=>$attorney_id));
			foreach($client_id as $c_id)
			{
				/*$isExists	=	$AdminDAO->getrows("client_attorney","*","attorney_id = :id", array("id"=>$editattorney_id));
				if(!empty($isExists))
				{
					$client_fields	=	array("client_id");
					$client_values	=	array($c_id);
					$AdminDAO->updaterow("client_attorney",$client_fields,$client_values,"attorney_id = :id", array("id"=>$editattorney_id));
				}
				else*/
				{
					//echo "<br>LINE--->".__LINE__;
					$client_fields	=	array("case_id","attorney_id", "client_id");
					$client_values	=	array($_POST['case_id'],$attorney_id,$c_id);
					$AdminDAO->insertrow("client_attorney",$client_fields,$client_values);
				}
			}
		}
	}
	else
	{
		$uid			=	@$AdminDAO->generateuid('attorney');
		$fields[]		=	'uid';
		$values[]		=	$uid;
		$attorney_id	=	$AdminDAO->insertrow("attorney",$fields,$values);
		if($attorney_type == 2)
		{
			foreach($client_id as $c_id)
			{
				$client_fields	=	array("case_id","attorney_id", "client_id");
				$client_values	=	array($_POST['case_id'],$attorney_id,$c_id);
				$AdminDAO->insertrow("client_attorney",$client_fields,$client_values);
			}
		}
	}
	if($attorney_type == 2)
	{
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
	$msg			=	"Added successfully.";
	$type			=	'success';
}
$jsonArray	=	array("type"=>$type,"msg"=>$msg,"attorney_type"=>$attorney_type,"case_id"=>$case_id);
echo json_encode($jsonArray);


