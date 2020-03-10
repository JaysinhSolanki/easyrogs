<?php
@session_start();
require_once("adminsecurity.php");
$delete_or_leave	=	$_POST['delete_or_leave'];
$case_id			=	$_POST['case_id'];
$addressbookid		=	$_SESSION['addressbookid'];
if($delete_or_leave == 1)
{	
	//attorney
	$AdminDAO->deleterows('attorney'," case_id = :case_id", array("case_id"=>$case_id));
	
	//attorneys_cases
	$AdminDAO->deleterows('attorneys_cases'," case_id = :case_id", array("case_id"=>$case_id));
	
	//clients
	$AdminDAO->deleterows('clients'," case_id = :case_id", array("case_id"=>$case_id));
	
	//questions
	$allDescoveries	=	$AdminDAO->getrows("discoveries","GROUP_CONCAT(id) as ids"," case_id = :case_id", array("case_id"=>$case_id));
	
	if(sizeof($allDescoveries) > 0)
	{
		$discoveryids	=	$allDescoveries[0]['ids'];
		$alldiscoveries			=	explode(",",$discoveryids);
		foreach($alldiscoveries as $discovery_id)
		{
			$AdminDAO->deleterows('discovery_questions'," discovery_id = :discovery_id", array("discovery_id"=>$discovery_id));
			//$AdminDAO->deleterows('questions'," discovery_id = :discovery_id", array("discovery_id"=>$discovery_id));
		}
	}
	//discoveries
	$AdminDAO->deleterows('discoveries'," case_id = :case_id", array("case_id"=>$case_id));

	//documents
	$AdminDAO->deleterows('documents'," case_id = :case_id", array("case_id"=>$case_id));
	
	//cases
	$AdminDAO->deleterows('cases'," id = :case_id", array("case_id"=>$case_id));
	
	
}
else
{
	$AdminDAO->deleterows('attorneys_cases'," case_id = :id AND attorney_id = :addressbookid ", array("id"=>$case_id,"addressbookid"=>$addressbookid));
}