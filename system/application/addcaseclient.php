<?php
require_once("adminsecurity.php");

$id					=	$_POST['id'];
$case_id			=	$_POST['case_id'];
$client_name		=	$_POST['client_name'];
$clientroles		=	$_POST['clientroles'];
$clienttypes		=	$_POST['clienttypes'];
$client_email		=	$_POST['client_email'];
$other_attorney_id	=	$_POST['other_attorney_id'];

$type			=	'error';
if($client_name == "")
{
	$msg	=	"Please enter party name.";
}
else if($clientroles == "")
{
	$msg	=	"Please select party role.";
}
else if($clienttypes == "")
{
	$msg	=	"Please select representation.";
}
else
{
	// $client set on post-case-client.php
	$client_id = $client['id'];
	
	if($clienttypes == "Us")
	{
		$uid				=	@$AdminDAO->generateuid('attorney');
		$fields_attry		=	array("uid","attorney_name","attorney_email","case_id","attorney_type","fkaddressbookid");
		$values_attry		=	array($uid,$_SESSION['name'],$_SESSION['loggedin_email'],$case_id,2,$_SESSION['addressbookid']);
		$ownerattorney_id	=	$AdminDAO->insertrow("attorney",$fields_attry,$values_attry);
								
		$client_fields		=	array("case_id","attorney_id", "client_id");
		$client_values		=	array($case_id,$ownerattorney_id,$client_id);
		$AdminDAO->insertrow("client_attorney",$client_fields,$client_values);	
	}
	else if($id>0)
	{
		/**
		* If edit case and client type is not mine then we delete owner for service list if he is attached with that client
		**/
		$checkClientAttorney	=	$AdminDAO->getrows("client_attorney","*","client_id	=	:id", array(":id" => $id));
		
		if(!empty($checkClientAttorney))
		{
			foreach($checkClientAttorney as $client_attr_data)
			{
				$att_id			=	$client_attr_data['attorney_id'];
				$checkDetails	=	$AdminDAO->getrows("attorney","*","id	=	:id", array(":id" => $att_id));
				if(!empty($checkDetails))
				{
					if($checkDetails[0]['attorney_type'] == 2 && $checkDetails[0]['attorney_email'] == $_SESSION['loggedin_email'])
					{
						$AdminDAO->deleterows('attorney'," id = :id", array("id"=>$att_id	));
						$AdminDAO->deleterows('client_attorney'," client_id = :id AND case_id = :case_id", array("id"=>$id,"case_id"=>$case_id));
					}
				}
			}
		}
	}
	
	$msg			=	"Added successfully.";
	$type			=	'success';
}
$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);


