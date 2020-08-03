<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
$discovery_id				=	$_POST['discovery_id'];
$instruction				=	$_POST['instruction'];
$response_id				=	$_POST['response_id'];
$final_responses			=	$_POST['final_response'];
$final_response_updated_on	=	date("Y-m-d H:i:s");

foreach( $final_responses as $discovery_question_id => $final_response ) {
	$getResponseQuestionData	= $AdminDAO->getrows("response_questions","id",
														"fkresponse_id				= :fkresponse_id AND  	
														fkdiscovery_question_id 	= :discovery_question_id",
														array(	"discovery_question_id"	=> $discovery_question_id,
																"fkresponse_id"			=> $response_id));
	if( !empty($getResponseQuestionData) ) {
		$fields	= array("final_response","final_response_updated_on");
		$values	= array($final_response,$final_response_updated_on);
		$AdminDAO->updaterow("response_questions",$fields,$values,"fkdiscovery_question_id = :fkdiscovery_question_id AND fkresponse_id = :fkresponse_id",array("fkdiscovery_question_id"=>$discovery_question_id,"fkresponse_id"=>$response_id));	
	}
	else {
		$fields	= array("fkdiscovery_question_id","fkresponse_id","answered_at","final_response","final_response_updated_on");
		$values	= array($discovery_question_id,$response_id,$final_response_updated_on,$final_response,$final_response_updated_on);
		$AdminDAO->insertrow("response_questions",$fields,$values);
	}
	
}
$fields	=	array("finaldraft_instruction","is_final_draft_created");
$values	=	array($instruction,1);
$AdminDAO->updaterow("discoveries",$fields,$values,"id = :id",array("id"=>$discovery_id));	

echo json_encode(array("messagetype"=>'success',"response_id"=>$response_id));