<?php

require_once __DIR__ . "/../bootstrap.php";

include_once("../library/classes/functions.php"); 

$updated_by	= $_SESSION['addressbookid'];

$form_id 		                  = $_POST['form_id'];
$case_id 		                  = $_POST['case_id'];
$respond                          = $_POST['respond'];
$response_id                      = $_POST['response_id'];
$supp				              = $_POST['supp'];
$subanswer                        = $_POST['subanswer'];
$have_main_question	              = $_POST['have_main_question'];
$answers						  = isset($_POST['answer']) ? $_POST['answer'] : [];
$subanswers					      = $_POST['subanswer'];
$uid							  = $_POST['uid'];
$introduction				      = $_POST['introduction'];
$discovery_verification			  = $_POST['discovery_verification'];
$discovery_verification_state	  = $_POST['discovery_verification_state'];
$discovery_verification_city	  = $_POST['discovery_verification_city'];
$discovery_sender_note			  = $_POST['discovery_sender_note'];
$discovery_verification_by_name	  = $_POST['discovery_verification_by_name'];
$discovery_verification_signed_by = $_POST['discovery_verification_signed_by'];
$response_name					  = $_POST['response_name'];

$verification_datetime = date("Y-m-d H:i:s");

$case						= $AdminDAO->getrows("cases","*","id='$case_id'");
$form						= $AdminDAO->getrows("forms","*","id='$form_id'");

$getdescovery_details	= $AdminDAO->getrows("discoveries","*","uid='$uid'");
$attorney_id				= $getdescovery_details[0]['attorney_id'];
$discovery_id				= $getdescovery_details[0]['id'];
$discovery_name				= $getdescovery_details[0]['discovery_name'];
$set_number					= $getdescovery_details[0]['set_number'];
 
if($respond == "") { $respond	= 0; }

if($response_id == 0 || $response_id == "")
{
	$responsename		= "Response to ".$discovery_name." [Set ". $set_number."]";
	$fields_responses	= array("responsename","fkdiscoveryid","created_by");
	$values_responses	= array($responsename,$discovery_id,$_SESSION["addressbookid"]);
	$response_id		= $AdminDAO->insertrow("responses",$fields_responses,$values_responses);	
}
else 
{
	if($supp == 1)
	{
		$supp_form_name		= $_POST['supp_form_name'];
		$fields_responses	= array("responsename","fkdiscoveryid","fkresponseid","created_by");
		$values_responses	= array($supp_form_name,$discovery_id,$response_id,$_SESSION['addressbookid']);
		$response_id		= $AdminDAO->insertrow("responses",$fields_responses,$values_responses);
	}
}
if($respond == 1)
{
	$objections	= $_POST['objection'];
	foreach($objections as $discovery_question_id => $objection)
	{
		/**
		* Check record already exists in response questions or not
		**/
		$getResponseQuestionData	= $AdminDAO->getrows("response_questions","id",
																					"fkresponse_id				= :fkresponse_id AND  	
																					fkdiscovery_question_id 	= 	:discovery_question_id",
																					array(	"discovery_question_id"	=>	$discovery_question_id,
																							"fkresponse_id"			=>	$response_id));
		if( !empty($getResponseQuestionData) ) {
			$fields_objection	= array("objection");
			$values_objection	= array($objection);
			$AdminDAO->updaterow('response_questions',$fields_objection,$values_objection,"fkdiscovery_question_id = '$discovery_question_id' AND fkresponse_id = '$response_id'");
		}
		else {
			$fields_objection	= array("objection","fkdiscovery_question_id","fkresponse_id");
			$values_objection	= array($objection,$discovery_question_id,$response_id);
			$AdminDAO->insertrow("response_questions",$fields_objection,$values_objection);
		}
	}
}

if( sizeof($answers) ) {
	foreach( $answers as $discovery_question_id => $answer ) {
		/**
		* Check record already exists in response questions or not
		**/
		$getResponseQuestionData	= $AdminDAO->getrows("response_questions","id",
																					"fkresponse_id				= :fkresponse_id AND  	
																					fkdiscovery_question_id 	= 	:discovery_question_id",
																					array(	"discovery_question_id"	=>	$discovery_question_id,
																							"fkresponse_id"			=>	$response_id));
		
		$fields	= array("answer");
		$values	= array($answer);

		if( $form_id == Discovery::FORM_CA_RPDS ) {
			$fields[]	= 'answer_detail';
			$values[]	= $subanswer[$discovery_question_id];
		}
		if( !empty($getResponseQuestionData) ) {
			$AdminDAO->updaterow('response_questions',$fields,$values,"fkdiscovery_question_id = '$discovery_question_id' AND fkresponse_id = '$response_id'");
		}
		else {
			$fields[]	= 'fkdiscovery_question_id';
			$values[]	= $discovery_question_id;
			$fields[]	= 'fkresponse_id';
			$values[]	= $response_id;
			$AdminDAO->insertrow("response_questions",$fields,$values);
		}
	}
}

if( $form_id == Discovery::FORM_CA_RFAS && !empty($subanswer) ) {
	$rfa_objection		= $_POST['rfa_objection'];
	foreach( $subanswer as $discovery_question_id => $subanswerArray ) {
		foreach( $subanswerArray as $question_admit_id => $sub_answer ) {
			$fields1		= array("discovery_question_id","question_admit_id","fkresponse_id");
			$values1		= array($discovery_question_id,$question_admit_id,$response_id);
			$objection_data	= $rfa_objection[$discovery_question_id][$question_admit_id];
		
			if( $sub_answer ) {
				$fields1[]	= 'sub_answer';
				$values1[]	= $sub_answer;
			}
			if($respond == 1) {
				$fields1[]	= 'objection';
				$values1[]	= $objection_data;
			}
			$checkalreadyexists	= $AdminDAO->getrows('question_admit_results','*',"discovery_question_id = :discovery_question_id AND question_admit_id = :question_admit_id AND fkresponse_id = :fkresponse_id",array("discovery_question_id"=>$discovery_question_id,"question_admit_id"=>$question_admit_id,"fkresponse_id" => $response_id));	
		
			if( !empty($checkalreadyexists) ) {
				$AdminDAO->updaterow("question_admit_results",$fields1,$values1,"discovery_question_id = '$discovery_question_id' AND question_admit_id = '$question_admit_id' AND fkresponse_id = '$response_id'");	
			}
			else {
				$AdminDAO->insertrow("question_admit_results",$fields1,$values1);	
			}
		}
	}
}

if( in_array($form_id, array(Discovery::FORM_CA_RPDS)) ) {
	$olddocuments	= $_SESSION['documents'][$uid];
	$AdminDAO->deleterows('documents',"discovery_id = '$discovery_id' AND fkresponse_id = '$response_id'");
	
	if( sizeof($olddocuments) ) {
		foreach( $olddocuments as $data ) {
			$doc_purpose	= $data['doc_purpose'];
			$doc_name		= $data['doc_name'];
			$doc_path		= $data['doc_path'];
			if( $doc_name ) {
				$doc_fields		= array("form_id",'attorney_id','case_id','document_notes','document_file_name','discovery_id','fkresponse_id');
				$doc_values		= array($form_id,$attorney_id,$case_id,$doc_purpose,$doc_name,$discovery_id,$response_id);
				$AdminDAO->insertrow("documents",$doc_fields,$doc_values);
			}
		}
	}
}
if( $_GET['q'] ) {
	$fields	= array("submit_date",'is_submitted','verification_signed_by','verification_by_name','discovery_verification','verification_state','verification_city','verification_datetime');
	$values = array(date("Y-m-d H:i:s"),'1',$discovery_verification_signed_by,$discovery_verification_by_name,$discovery_verification,$discovery_verification_state,$discovery_verification_city,$verification_datetime);
	
	$AdminDAO->updaterow('responses',$fields,$values,"id='$response_id'");
	$response = $AdminDAO->getrows('responses','*',"id='$response_id'");
	
	$discovery = $discoveriesModel->findByUID($uid);
	DiscoveryMailer::clientResponded($discovery,$response);
}

echo json_encode([
	"pkerrorid"   => "7",
	"messagetype" => "2",
	"messagetext" => "Data has been saved successfully.",
	"response_id" => $response_id
]);
