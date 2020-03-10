<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
$discovery_id				=	$_POST['discovery_id'];
$instruction				=	$_POST['instruction'];
$final_responses			=	$_POST['final_response'];
$final_response_updated_on	=	date("Y-m-d H:i:s");
foreach($final_responses as $discovery_question_id => $final_response)
{
	$fields	=	array("final_response","final_response_updated_on");
	$values	=	array($final_response,$final_response_updated_on);
	$AdminDAO->updaterow("discovery_questions",$fields,$values,"id = :id",array("id"=>$discovery_question_id));	

}

$fields	=	array("finaldraft_instruction","is_final_draft_created");
$values	=	array($instruction,1);
$AdminDAO->updaterow("discoveries",$fields,$values,"id = :id",array("id"=>$discovery_id));	

echo "success";