<?php
require_once("adminsecurity.php");
$id					=	$_POST['id'];
$case_id			=	$_POST['case_id'];
$client_name		=	$_POST['client_name'];
$clientroles		=	$_POST['clientroles'];
$clienttypes		=	$_POST['clienttypes'];
$client_email		=	$_POST['client_email'];
$other_attorney_id	=	$_POST['other_attorney_id'];

//$alreadyExists	=	$AdminDAO->getrows("attorney","*", "case_id = :case_id AND attorney_email = :attorney_email ", array(":case_id"=>$case_id,":attorney_email"=>$attorney_email), "attorney_name", "ASC");
if($clienttypes == "Others")
{
	$client_email = "";
	
}
else
{
	$other_attorney_id	=	array();
}
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
else if($clienttypes != "Others" && $client_email == '')
{
	$msg	=	"Please enter party email.";
}
/*else if($clienttypes == "Others" && empty($other_attorney_id) )
{
	$msg	=	"Please select atleast one attorney with a client.";
}*/
else
{
	if($id>0)
	{
		
		$fields		=	array("client_name", "client_email", "client_type","client_role");
		$values		=	array($client_name,$client_email,$clienttypes,$clientroles);
		$AdminDAO->updaterow("clients",$fields,$values," id = :id", array("id"=>$id));
		$client_id	=	$id;
	}
	else
	{
		
		$fields		=	array("client_name", "client_email", "client_type","client_role", "case_id");
		$values		=	array($client_name,$client_email,$clienttypes,$clientroles,$case_id);
		$client_id	=	$AdminDAO->insertrow("clients",$fields,$values);
	}
	
	if($clienttypes == "Us")
	{
		/**
		* If Add case and client type is Us   then we add owner to that client in service list
		**/
		//$AdminDAO->displayquery=1;
		/*$checkDetails	=	$AdminDAO->getrows("attorney a, client_attorney ca","*",
											  "a.attorney_email	=	:attorney_email AND 
											  a.attorney_type 	= 	2 AND 
											  ca.attorney_id	=	a.id AND
											  ca.case_id 		= 	'$case_id'", 
											  array(":attorney_email" => $_SESSION['loggedin_email']));*/
				
		//$AdminDAO->displayquery=0;
		//if(empty($checkDetails)) 
		{
			$uid				=	@$AdminDAO->generateuid('attorney');
			$fields_attry		=	array("uid","attorney_name","attorney_email","case_id","attorney_type","fkaddressbookid");
			$values_attry		=	array($uid,$_SESSION['name'],$_SESSION['loggedin_email'],$case_id,2,$_SESSION['addressbookid']);
			$ownerattorney_id	=	$AdminDAO->insertrow("attorney",$fields_attry,$values_attry);
									
			$client_fields		=	array("case_id","attorney_id", "client_id");
			$client_values		=	array($case_id,$ownerattorney_id,$client_id);
			$AdminDAO->insertrow("client_attorney",$client_fields,$client_values);	
		}						
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
	/*if(!empty($other_attorney_id))
	{
		foreach($other_attorney_id as $attorney_id)
		{
			$client_fields	=	array("attorney_id", "client_id");
			$values			=	array($attorney_id,$client_id);
			$AdminDAO->insertrow("client_attorney",$client_fields,$values);
		}
	}*/
	
	$msg			=	"Added successfully.";
	$type			=	'success';
}
$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);


