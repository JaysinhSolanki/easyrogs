<?php
@session_start();
include_once("../bootstrap.php");
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(0);
//dump($_POST);
//exit;
$updated_by				  		=	$_SESSION['addressbookid'];
$form_id						=	$_POST['form_id'];
$case_id						=	$_POST['case_id'];
$respond						=	$_POST['respond'];
$response_id					=	$_POST['response_id'];

//$discovery_uid				=	$_POST['uid'];
$have_main_question				=	$_POST['have_main_question'];
$answers						=	$_POST['answer'];
$subanswers						=	$_POST['subanswer'];
$uid							=	$_POST['uid'];
$introduction					=	$_POST['introduction'];
$discovery_verification			=	$_POST['discovery_verification'];
$discovery_verification_state	=	$_POST['discovery_verification_state'];
$discovery_verification_city	=	$_POST['discovery_verification_city'];
$discovery_sender_note			=	$_POST['discovery_sender_note'];
$discovery_verification_by_name	=	$_POST['discovery_verification_by_name'];
$response_name					=	$_POST['response_name'];
$verification_datetime			=	date("Y-m-d H:i:s");
$case							=	$AdminDAO->getrows("cases","*","id='$case_id'");
$form							=	$AdminDAO->getrows("forms","*","id='$form_id'");

$getdescovery_details	=	$AdminDAO->getrows("discoveries","*","uid='$uid'");
$attorney_id			=	$getdescovery_details[0]['attorney_id'];
$discovery_id			=	$getdescovery_details[0]['id'];
 
if($respond == "")
{
	$respond	=	0;
}
/*if($response_id == 0 || $response_id == "")
{
	$fields_responses	=	array("fkdiscoveryid","isserved","servedate","submitted_by");
	$values_responses	=	array($discovery_id,0,"0000-00-00",$updated_by);
	if($response_name != "")
	{
		$fields_responses[]		=	"responsename";
		$values_responses[]		=	$response_name; 
	}
	$response_id		=	$AdminDAO->insertrow("responses",$fields_responses,$values_responses);	
}*/
if($respond == 1)
{
	$objections		=	$_POST['objection'];
	//dump($objections);
	foreach($objections as $discovery_question_id => $objection)
	{
		//$AdminDAO->displayquery=1;
		/**
		* Check record already exists in response questions or not
		**/
		$getResponseQuestionData	=	$AdminDAO->getrows("response_questions","id",
																					"fkresponse_id				=	:fkresponse_id AND  	
																					fkdiscovery_question_id 	= 	:discovery_question_id",
																					array(	"discovery_question_id"	=>	$discovery_question_id,
																							"fkresponse_id"			=>	$response_id));
		//$AdminDAO->displayquery=0;
		//dump($getResponseQuestionData);
		if(!empty($getResponseQuestionData))
		{
			//echo 1;
			$fields_objection	=	array("objection");
			$values_objection	=	array($objection);
			$AdminDAO->updaterow('response_questions',$fields_objection,$values_objection,"fkdiscovery_question_id = '$discovery_question_id' AND fkresponse_id = '$response_id'");
		}
		else
		{
			//echo 2;
			$fields_objection	=	array("objection","fkdiscovery_question_id","fkresponse_id");
			$values_objection	=	array($objection,$discovery_question_id,$response_id);
			$AdminDAO->insertrow("response_questions",$fields_objection,$values_objection);
		}
	}
}


$subanswer		=	$_POST['subanswer'];
//dump($answers);
//dump($subanswer);

if(sizeof($answers) > 0)
{
	foreach($answers as $discovery_question_id => $answer)
	{
		/**
		* Check record already exists in response questions or not
		**/
		//$AdminDAO->displayquery=1;
		$getResponseQuestionData	=	$AdminDAO->getrows("response_questions","id",
																					"fkresponse_id				=	:fkresponse_id AND  	
																					fkdiscovery_question_id 	= 	:discovery_question_id",
																					array(	"discovery_question_id"	=>	$discovery_question_id,
																							"fkresponse_id"			=>	$response_id));
		//$AdminDAO->displayquery=0;
		//dump($getResponseQuestionData);
		
		$fields	=	array("answer");
		$values	=	array($answer);
		///////////////////////////////////////////
		//			IN FROM ID 5 CASE
		///////////////////////////////////////////
		if($form_id == 5)
		{
			$fields[]	=	'answer_detail';
			$values[]	=	$subanswer[$discovery_question_id];
		}
		if(!empty($getResponseQuestionData))
		{
			$AdminDAO->updaterow('response_questions',$fields,$values,"fkdiscovery_question_id = '$discovery_question_id' AND fkresponse_id = '$response_id'");
		}
		else
		{
			$fields[]			=	'fkdiscovery_question_id';
			$values[]			=	$discovery_question_id;
			$fields[]			=	'fkresponse_id';
			$values[]			=	$response_id;
			//echo "<br>Fields<br>";
			//dump($fields);
			//echo "<br>Values<br>";
			//dump($values);
			$AdminDAO->insertrow("response_questions",$fields,$values);
		}
	}
}

if($form_id == 4 && !empty($subanswer))
{
	$rfa_objection		=	$_POST['rfa_objection'];
	foreach($subanswer as $discovery_question_id => $subanswerArray)
	{
		foreach($subanswerArray as $question_admit_id => $sub_answer)
		{
			$fields1		=	array("discovery_question_id","question_admit_id","fkresponse_id");
			$values1		=	array($discovery_question_id,$question_admit_id,$response_id);
			$objection_data	=	$rfa_objection[$discovery_question_id][$question_admit_id];
		
			if($sub_answer != "")
			{
				$fields1[]	=	'sub_answer';
				$values1[]	=	$sub_answer;
			}
			if($respond == 1)
			{
				$fields1[]	=	'objection';
				$values1[]	=	$objection_data;
			}
			$checkalreadyexists	=	$AdminDAO->getrows('question_admit_results','*',"discovery_question_id = :discovery_question_id AND question_admit_id = :question_admit_id AND fkresponse_id = :fkresponse_id",array("discovery_question_id"=>$discovery_question_id,"question_admit_id"=>$question_admit_id,"fkresponse_id" => $response_id));	
		
			if(!empty($checkalreadyexists))
			{
				$AdminDAO->updaterow("question_admit_results",$fields1,$values1,"discovery_question_id = '$discovery_question_id' AND question_admit_id = '$question_admit_id' AND fkresponse_id = '$response_id'");	
			}
			else
			{
				$AdminDAO->insertrow("question_admit_results",$fields1,$values1);	
			}
		}
	}
}

if(in_array($form_id,array(5)))
{
	$olddocuments	=	$_SESSION['documents'][$uid];
	//$AdminDAO->deleterows('documents',"attorney_id	=	'$attorney_id' AND form_id = '$form_id' AND case_id = '$case_id'");
	$AdminDAO->deleterows('documents',"discovery_id = '$discovery_id' AND fkresponse_id = '$response_id'");
	
	if(sizeof($olddocuments) > 0)
	{
		foreach($olddocuments as $data)
		{
			$doc_purpose	=	$data['doc_purpose'];
			$doc_name		=	$data['doc_name'];
			$doc_path		=	$data['doc_path'];
			if($doc_name != "")
			{
				$doc_fields		=	array("form_id",'attorney_id','case_id','document_notes','document_file_name','discovery_id','fkresponse_id');
				$doc_values		=	array($form_id,$attorney_id,$case_id,$doc_purpose,$doc_name,$discovery_id,$response_id);
				$AdminDAO->insertrow("documents",$doc_fields,$doc_values);
			}
		}
	}
}
if($_GET['q']==1)
{  
	
	$fields				=	array("submit_date",'is_submitted','verification_by_name','discovery_verification','verification_state','verification_city','verification_datetime');
	$values				=	array(date("Y-m-d H:i:s"),'1',$discovery_verification_by_name,$discovery_verification,$discovery_verification_state,$discovery_verification_city,$verification_datetime);
	
	$AdminDAO->updaterow('responses',$fields,$values,"id='$response_id'");
	
		
	$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
											'c.case_title 	as case_title,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.court_address as court_address,
											c.department 	as department, 
											d.case_id 		as case_id,
											d.id 			as discovery_id,
											d.uid,
											d.type,
											d.discovery_instrunctions,
											d.propounding,
											d.responding,
											d.discovery_name,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											d.discovery_introduction as introduction,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.lastname 		as atorny_lname,
											d.attorney_id	as attorney_id,
											a.email,
											(CASE WHEN (form_id = 1 OR form_id = 2) 
											 THEN
												  f.form_instructions 
											 ELSE
												  d.discovery_instrunctions 
											 END)
											 as instructions 
											',
											/*(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND */
											"
											d.uid			=	'$uid' AND
											d.case_id 		= c.id AND  
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":uid"=>$uid)
										);

	//dump($discoveryDetails);
	//exit;
	$discovery_data		=	$discoveryDetails[0];
	$case_title			=	$discovery_data['case_title'];
	$discovery_id		=	$discovery_data['discovery_id'];
	$discovery_uid		=	$discovery_data['uid'];
	$discovery_name		=	$discovery_data['discovery_name'];
	$case_number		=	$discovery_data['case_number'];
	$jurisdiction		=	$discovery_data['jurisdiction'];
	$judge_name			=	$discovery_data['judge_name'];
	$county_name		=	$discovery_data['county_name'];
	$court_address		=	$discovery_data['court_address'];
	$department			=	$discovery_data['department'];
	$case_id			=	$discovery_data['case_id'];
	$form_id			=	$discovery_data['form_id'];
	$set_number			=	$discovery_data['set_number'];
	$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
	$form_name			=	$discovery_data['form_name'];
	$short_form_name	=	$discovery_data['short_form_name'];
	$atorny_email		=	$discovery_data['email'];
	$responding			=	$discovery_data['responding'];

	$to_emails[]	=	$atorny_email;
	
	
	$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
	$responding_name		=	$respondingdetails[0]['client_name'];
	$responding_email		=	$respondingdetails[0]['client_email'];
	$responding_type		=	$respondingdetails[0]['client_type'];
	$responding_role		=	$respondingdetails[0]['client_role'];
	
	//Get Case Team Emails
	$caseteams				=	$AdminDAO->getrows("case_team,attorney","attorney.attorney_email as caseteamemail","case_team.fkcaseid = :case_id AND case_team.attorney_id = attorney.id AND case_team.is_deleted = 0",array(":case_id"=>$case_id));
	foreach($caseteams as $caseteam)
	{
		$to_emails[]	=	$caseteam['caseteamemail'];
	}
	
	//dump($to_emails);
	/*$email_salutation	=	$_POST['email_solicitation'];
	$email_body			=	$email_salutation."<br>".$_POST['email_body'];*/
	/*If you want to add custom input email from user then uncomment above 2 lines and comment bottom line. And on front page remove display:none from fields*/
	
	 $email_body		=	 "<h4>{$responding_name} has returned {$discovery_name} [SET {$set_number}] </h4><p>All rights reserved &copy; ".date('Y')." EasyRogs. U.S. Patent Pending<p>"; 
	//$to_emails		=	array("gumptiondevelopers@gmail.com");
	$responding_name	=	$responding_name;
	$responding_email	=	$responding_email;
	
	send_email($to_emails,$case_title,$email_body,$responding_email,$responding_name,1,array(),array("easyrogs@gmail.com")); 
	
	
	/*Email log details*/
	$discovery_id		=	$discovery_id;
	$loggedin_id		=	$responding;
	$email_subject		=	$case_title;
	$send_from			=	$responding_email;
	$to_values			=	$to_emails;
	$email_salutation	=	$email_salutation;
	$email_body			=	$email_body;
	$bcc_values			=	array("easyrogs@gmail.com");
	$cc_values			=	array();
	$sender_type		=	2;
	$receiver_type		=	1;
	$sending_script		=	2;
	emaillog($discovery_id,$loggedin_id,$email_subject,$send_from,$to_values,$email_salutation,$email_body,$bcc_values,$cc_values,$sender_type,$receiver_type,$sending_script);
}
//exit;
msg(7);