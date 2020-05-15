<?php
@session_start();
require_once("adminsecurity.php"); 
$discovery_uid	=	$_POST['discovery_uid'];
$discoverydetails		=	$AdminDAO->getrows('discoveries',"*","uid = :uid",array(":uid"=>$discovery_uid));
if(sizeof($discoverydetails) > 0)
{
	$case_id		=	$discoverydetails[0]['case_id'];
	$form_id		=	$discoverydetails[0]['form_id'];
	$discovery_id	=	$discoverydetails[0]['id'];
	 
	$discoveryresponses		=	$AdminDAO->getrows('responses',"*","fkdiscoveryid = :fkdiscoveryid",array("fkdiscoveryid"=>$discovery_id));
	if(!empty($discoveryresponses))
	{
		foreach($discoveryresponses as $response_data)
		{
			$response_id	=	$response_data['id'];
			$AdminDAO->deleterows('responses',"id = :id",array("id"=>$response_id));
			$AdminDAO->deleterows('response_questions'," fkresponse_id = :fkresponse_id", array("fkresponse_id"=>$response_id));
		}
	}
	$fields				=	array('pos_state','pos_city','pos_text','pos_updated_at','pos_updated_by','is_served','served','due');
	$values				=	array("","", "",date('Y-m-d H:i:s'),$currentUser->id,0,"","");
	$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$discovery_id'");
	echo $case_id;
}