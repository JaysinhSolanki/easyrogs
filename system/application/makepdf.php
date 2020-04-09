<?php
@session_start();
include_once("../bootstrap.php");
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");

$fDebug = fopen('debug.log','a+');

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/
error_reporting(0);
$respond			=	0;
$uid				=	@$_GET['id'];
$view				=	$_GET['view'];
$downloadORwrite	=	@$_GET['downloadORwrite'];
$response_id		=	@$_GET['response_id'];
if($downloadORwrite == "")
{ 
	$downloadORwrite = 0;
} 
/**************************************
		SETTING DATA
***************************************/ 
$setting_details	=	$AdminDAO->getrows('system_setting','*','pksettingid = 1');
$setting_email		=	$setting_details[0]['email'];
/***************************************
		Query For Header Data
****************************************/	
//$AdminDAO->displayquery=1;
$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,forms f',
											'c.case_title 	as case_title,
											c.plaintiff,
											c.defendant,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.court_address as court_address,
											c.department 	as department, 
											c.case_attorney	as case_attorney,
											c.masterhead	as masterhead,
											d.case_id 		as case_id,
											d.id 			as discovery_id, 
											d.uid,
											d.send_date,
											d.propounding,
											d.responding,
											d.served,
											d.pos_text,
											d.is_served,
											d.type,
											d.proponding_attorney,
											d.discovery_name,
											d.propounding_uid,
											d.responding_uid,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											d.discovery_introduction as introduction,
											d.declaration_text,
											d.declaration_updated_by,
											d.declaration_updated_at,
											d.incidenttext, d.incidentoption,d.personnames1,d.personnames2,d.in_conjunction,d.interogatory_type,d.conjunction_setnumber,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											d.attorney_id	as attorney_id,
											(CASE WHEN (form_id = 1 OR form_id = 2) 
											 THEN
												  f.form_instructions 
											 ELSE
												  d.discovery_instrunctions 
											 END)
											 as instructions 
											',
											"d.uid 			= :uid AND  
											d.case_id 		= c.id AND  
											d.form_id		= f.id ",
											array(":uid"=>$uid)
										);

//$AdminDAO->displayquery=0;
//echo "<pre>";
//print_r($discoveryDetails);
//exit;
$discovery_data		=	$discoveryDetails[0];
$plaintiff			=	$discovery_data['plaintiff'];
$defendant			=	$discovery_data['defendant'];
$case_title			=	$discovery_data['case_title'];
$discovery_id		=	$discovery_data['discovery_id'];
$case_number		=	$discovery_data['case_number'];
$jurisdiction		=	$discovery_data['jurisdiction'];
$judge_name			=	$discovery_data['judge_name'];
$county_name		=	$discovery_data['county_name'];
$court_address		=	$discovery_data['court_address'];
$department			=	$discovery_data['department'];
$case_id			=	$discovery_data['case_id'];
$form_id			=	$discovery_data['form_id'];
$set_number			=	$discovery_data['set_number'];
$case_attorney		=	$discovery_data['case_attorney'];
$masterhead			=	$discovery_data['masterhead'];
	
$form_name				=	$discovery_data['form_name']." [SET ".$set_number."]";
$short_form_name		=	$discovery_data['short_form_name'];
	
$send_date				=	$discovery_data['send_date'];
$instructions			=	$discovery_data['instructions'];
$introduction			=	$discovery_data['introduction'];
$propounding_uid		=	$discovery_data['propounding_uid'];
$responding_uid			=	$discovery_data['responding_uid'];
$propounding			=	$discovery_data['propounding'];
$responding				=	$discovery_data['responding'];
$type					=	$discovery_data['type'];
$discovery_name			=	$discovery_data['discovery_name'];
$incidentoption			=	$discovery_data['incidentoption'];
$incidenttext			=	$discovery_data['incidenttext'];
$personnames2			=	$discovery_data['personnames2'];
$personnames1			=	$discovery_data['personnames1'];
fwrite($fDebug, "$personnames1 - $personnames2 \n");
$interogatory_type		=	$discovery_data['interogatory_type'];
$conjunction_setnumber	=	$discovery_data['conjunction_setnumber'];
$in_conjunction			=	$discovery_data['in_conjunction'];
$declaration_text		=	$discovery_data['declaration_text'];
$declaration_updated_by	=	$discovery_data['declaration_updated_by'];
$declaration_updated_at	=	$discovery_data['declaration_updated_at'];
$proponding_attorney	=	$discovery_data['proponding_attorney'];

if($response_id > 0)
{
	$responseDetails		=	$AdminDAO->getrows("responses","*","id = :id",array(":id"=>$response_id));
	$responseDetail			=	$responseDetails[0];
	
	$is_served				=	$responseDetail['isserved'];
	$served					=	$responseDetail['servedate'];
	$submit_date			=	$responseDetail['submit_date'];
	$is_submitted			=	$responseDetail['is_submitted'];
	$pos_text				=	$responseDetail['postext'];
	$served_date			=	date("F d, Y",strtotime($served));
	$res_created_by				=	$responseDetail['created_by'];
	$is_verified			=	$responseDetail['discovery_verification'];
	$verification_text		=	$responseDetail['discovery_verification_text'];
	$verification_state		=	$responseDetail['verification_state'];
	$verification_city		=	$responseDetail['verification_city'];
	$verification_by_name	=	$responseDetail['verification_by_name'];
	$verification_datetime	=	$responseDetail['verification_datetime'];
	$verification_signed_by	=	$responseDetail['verification_signed_by'];
}
else
{
	$is_served				=	$responseDetail['isserved'];
	$served					=	$responseDetail['servedate'];
	$submit_date			=	$responseDetail['submit_date'];
	$is_submitted			=	$responseDetail['is_submitted'];
	$pos_text				=	$responseDetail['postext'];
	$served_date			=	date("F d, Y",strtotime($served));
	
	$is_verified			=	"";
	$verification_text		=	"";
	$verification_state		=	"";
	$verification_city		=	"";
	$verification_by_name	=	"";
	$verification_datetime	=	"";
	$verification_signed_by	=	"";
}

if($type == 1)//External
{
	$is_served				=	$discovery_data['is_served'];
	$pos_text				=	$discovery_data['pos_text'];
	$submit_date			=	$discovery_data['submit_date'];
	$served					=	$discovery_data['served'];
	$served_date			=	date("F d, Y",strtotime($served));
	
}




if($view == 1)
{
	$form_name = strtoupper($discovery_name);
}
else
{
	$form_name = strtoupper("RESPONSE TO ".$discovery_name);
	
}
$form_name 			= 	$form_name." [SET ".numberTowords( $set_number )."]";



$propondingdetails		=	getRPDetails($propounding);
$proponding_name		=	$propondingdetails['client_name'];
$proponding_email		=	$propondingdetails['client_email'];
$proponding_type		=	$propondingdetails['client_type'];
$proponding_role		=	$propondingdetails['client_role'];

$respondingdetails		=	getRPDetails($responding);
$responding_name		=	$respondingdetails['client_name'];
$responding_email		=	$respondingdetails['client_email'];
$responding_type		=	$respondingdetails['client_type'];
$responding_role		=	$respondingdetails['client_role'];

if($type == 1) //External
{
	
	
	if($response_id > 0)
	{
		$whereAt	=	"pkaddressbookid = '$res_created_by'";
	}
	else 
	{
		
		$discovery_created_by	=	$case_attorney;//$discovery_data['attorney_id'];
		$whereAt				=	"pkaddressbookid = '$discovery_created_by'";
	}
	/*{
		if(@$_GET['active_attr_email'] != "")
		{
			$active_attr_email	=	$_GET['active_attr_email']; // CURL request
		}
		else
		{
			$active_attr_email	=	$_SESSION['loggedin_email'];// Simple request
		}
		$whereAt	=	"email = '$active_attr_email'";
	}*/
	
	$getAttorneyDetails	=	$AdminDAO->getrows('system_addressbook',"*",$whereAt,array());
	$getAttorneyDetail	=	$getAttorneyDetails[0];
	
}
else if($type == 2) //Internal
{
	$where_attorneyDetails	= "";
	if($view == 1)
	{
		$c_client_id	=	$propounding;
		if($proponding_attorney > 0)
		{
			$where_attorneyDetails	=	" AND a.id = $proponding_attorney";
		}
	}
	else
	{
		$c_client_id	=	$responding;
	}
	$attorneyDetails	=	$AdminDAO->getrows('attorney a,client_attorney ca,system_addressbook sa',"*","sa.email = a.attorney_email AND ca.client_id = :client_id AND a.id = ca.attorney_id AND ca.case_id = :case_id $where_attorneyDetails",array('client_id'=>$c_client_id,'case_id'=>$case_id));
	$getAttorneyDetail	=	$attorneyDetails[0];
}

$atorny_name		=	$getAttorneyDetail['firstname']." ".$getAttorneyDetail['middlename']." ".$getAttorneyDetail['lastname'];
$atorny_email		=	$getAttorneyDetail['email'];
$atorny_address		=	$getAttorneyDetail['address'];
$atorny_city		=	$getAttorneyDetail['cityname'];
$atorny_zip			=	$getAttorneyDetail['zip'];
$atorny_street		=	$getAttorneyDetail['street'];
$atorny_phone		=	$getAttorneyDetail['phone'];
$fkstateid			=	$getAttorneyDetail['fkstateid'];
$atorny_firm		=	$getAttorneyDetail['companyname'];
$attorney_info		=	$getAttorneyDetail['attorney_info'];

$getState			=	$AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
$atorny_state		=	$getState[0]['statename'];
$atorny_state_short	=	$getState[0]['statecode'];

/*if($_SESSION['groupid'] == 3)
{
	$active_attr_email	=	$_SESSION['loggedin_email'];
	$getAttorneyDetails	=	$AdminDAO->getrows('system_addressbook',"*","email = :email",array('email'=>$active_attr_email));
	$getAttorneyDetail	=	$getAttorneyDetails[0];	
	$atorny_name		=	$getAttorneyDetail['firstname']." ".$getAttorneyDetail['middlename']." ".$getAttorneyDetail['lastname'];
}
else
{
	$getAttorneyDetails	=	$AdminDAO->getrows('attorney a',"a.attorney_name","a.id = :id",array('id'=>$case_attorney));
	$getAttorneyDetail	=	$getAttorneyDetails[0];
	$atorny_name		=	$getAttorneyDetail['attorney_name'];
}*/
/**
* Check to see login attorney is responding party attorney or not
**/

if($view == 1) //1 = Discovery, 0 = Response
{
	$att_for_client_name		=	$proponding_name;
	$att_for_client_email		=	$proponding_email;
	$att_for_client_role		=	$proponding_role;
}
else 
{
	$att_for_client_name		=	$responding_name;
	$att_for_client_role		=	$responding_role;	
	$att_for_client_email		=	$responding_email;	
}	
/****************************************************
Function for getting responding or proponding details
****************************************************/
function getRPDetails($rp_id) 
{
	global $AdminDAO;
	$clients			=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$rp_id));
	return $clients[0];
}



/**************************************
		All Attoney's of this case
***************************************/
$allotherattornies	=	$AdminDAO->getrows('clients',"other_attorney_name,other_attorney_email","case_id = '$case_id' AND client_type = 'Others'");




/***************************************
	Query For Forms 1,2,3,4,5 Questions 
****************************************/
if(in_array($form_id,array(3,4,5)))
{
	$orderByMainQuestions	=	"  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number "; 
}
else
{
	$orderByMainQuestions	=	"  ORDER BY display_order, q.id ";
}
$mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
										'dq.id 				as 	discovery_question_id,
										q.id 				as 	question_id,
										q.question_type_id 	as 	question_type_id,
										q.question_title 	as 	question_title,
										q.question_number 	as 	question_number,
										q.sub_part 			as 	sub_part, 
										q.is_pre_defined 	as 	is_pre_defined,
										is_depended_parent,
										depends_on_question, 
										have_main_question,
										has_extra_text,
										extra_text,
										extra_text_field_label',
				
										"
										q.id 				= 	dq.question_id  AND
										dq.discovery_id = '$discovery_id' AND
										(
											q.sub_part 		= 	'' OR 
											q.sub_part IS NULL OR 
											have_main_question	IN (0,2)
											
										)
										GROUP BY q.id
										$orderByMainQuestions
										"
									  );
$generalQuestions	=	$AdminDAO->getrows('question_admits',"*");
/************************************************
	Discovery Conjuction with some RFA or not
************************************************/
//$AdminDAO->displayquery=1;
$isconwithdiscoveryid	=	0;
if(in_array($form_id,array(1,2)))
{
	$isConWithDiscovery		=	$AdminDAO->getrows('discoveries',"*",
														"propounding			= 	'$propounding' AND
														responding 				=	'$responding' AND
														case_id					=	'$case_id' AND
														interogatory_type		=	'$form_id' AND
														conjunction_setnumber 	= 	'$set_number'");
	if(sizeof($isConWithDiscovery) > 0)
	{
		$isconwithdiscoveryid 	=	$isConWithDiscovery[0]['id'];	
		$conjunction_setnumbers	=	$isConWithDiscovery[0]['conjunction_setnumber'];	
		if($is_served == 1)
		{
			$con_Details	=	array("con_discovery_name" => "REQUESTS FOR ADMISSION", "con_setnumber" => $conjunction_setnumbers);
		}
	}
}
if(in_array($form_id,array(4)))
{
	if($interogatory_type == 1)
	{
		$con_discovery	=	"FORM INTERROGATORIES - GENERAL";
	}
	else if($interogatory_type == 2)
	{
		$con_discovery	=	"FORM INTERROGATORIES - EMPLOYMENT LAW";
	}
	if($in_conjunction == 1)
	{
		$con_Details	=	array("con_discovery_name" => $con_discovery, "con_setnumber" => $conjunction_setnumber);
	}
	/*$con_Details		=	$AdminDAO->getrows('discoveries',"*",
														"propounding			= 	'$propounding' AND
														responding 				=	'$responding' AND
														case_id					=	'$case_id' AND
														set_number			 	= 	'$set_number' AND
														form_id IN (1,2)");*/
}

ob_start(); 
?>
<style>
	.tabela
	{
		width:100% !important;
	}
   .wikitable tbody tr th, table.jquery-tablesorter thead tr th.headerSort, .header-cell {
   background: #ccc;
   color: white;
   font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;
   font-weight: bold;
   font-size: 13pt;
   }
   .wikitable, table.jquery-tablesorter {
   box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
   }
   .tabela, .wikitable {
   border: 1px solid #A2A9B1;
   border-collapse: collapse;
   line-height:25px; 
   }
   .tabela tbody tr td, .wikitable tbody tr td {
   padding: 5px 10px 5px 10px;
   border: 1px solid #A2A9B1;
   border-collapse: collapse;
    line-height:25px; 
   }
   td
   {
		line-height:25px !important; 
   }
   .config-value {
   font-family: Arial, Helvetica, sans-serif;
   font-size:13pt; 
   background: white; 
   font-weight: bold;
   }
	.no-break {
	page-break-inside: avoid;
	}
	.break-page {
	page-break-before: always;
	}
</style>
<!-- =================================================== -->
<!-- 			HEADER PAGE 						 -->
<!-- =================================================== -->
<?php include_once('pdf-header.php');?>
<br />
<!-- =================================================== -->
<!-- 			QUESTIONS PAGE 						 -->
<!-- =================================================== -->
<p class="break-page1"></p>
<table class="wikitable1 tabela1">
    <tbody>
        <?php
        if(in_array($form_id,array(1,2)))
        {
            foreach($mainQuestions as $data)
            {
				$dependent_answer		=	"";
                $answer 				=	$data['answer'];
                $answer_time 			=	$data['answer_time'];
				$objection 				=	$data['objection'];
				$final_response			=	$data['final_response'];
				
                $question_id 			=	$data['question_id'];
                $question_type_id 		=	$data['question_type_id'];
                $question_title 		=	$data['question_title'];
                $question_number 		=	$data['question_number'];
                $sub_part 				=	$data['sub_part'];
                $is_pre_defined 		=	$data['is_pre_defined'];
                $discovery_question_id	=	$data['discovery_question_id'];
				$is_depended_parent		=	$data['is_depended_parent'];
				$depends_on_question	=	$data['depends_on_question'];
				$has_extra_text			=	$data['has_extra_text'];
				$extra_text				=	$data['extra_text'];
				$extra_text_field_label	=	$data['extra_text_field_label'];
				

				if($response_id > 0)
				{
					$getAnswers				=	$AdminDAO->getrows("response_questions","*",
																"fkresponse_id				=	:fkresponse_id AND  	
																fkdiscovery_question_id 	= 	:discovery_question_id",
																array(	"discovery_question_id"	=>	$discovery_question_id,
																"fkresponse_id"			=>	$response_id));
					$answer 				=	$getAnswers[0]['answer'];
					$answer_time 			=	$getAnswers[0]['answer_time'];
					$objection 				=	$getAnswers[0]['objection'];
					$final_response			=	$getAnswers[0]['final_response'];
				}
				else
				{
					$answer 				=	"";
					$answer_time 			=	"";
					$objection 				=	"";
					$final_response 		=	"";
				}
				
				/**
				* IF Depaends on some question then we need that question answer
				**/
				if($depends_on_question > 0 && $response_id > 0)
				{
					$dependent_answer	=	getAnswerOfDependentParentQuestion($discovery_id,$depends_on_question,$response_id);
				}
				if(($dependent_answer == "No" || $dependent_answer == "") && $view != 1 && $depends_on_question > 0)
				{
					continue;
				}
				
                if($question_type_id) // change by Hassan for sub elements
                {
                    $subQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
		
														'
														dq.id as discovery_question_id,
														q.id as question_id,
														q.question_type_id as question_type_id,
														q.form_id as form_id,
														q.question_title as question_title,
														q.question_number as question_number,
														q.sub_part as sub_part,
														q.is_pre_defined as is_pre_defined,
														dq.discovery_id',
														
											"q.question_number 	= 	:question_number AND 
											dq.discovery_id 	= 	:discovery_id    AND 
											q.id 				= 	 dq.question_id  AND
											q.sub_part 		   !=   ''  GROUP BY question_id ORDER BY question_number  ASC, sub_part ASC ",
											array(":question_number"=>$question_number,":discovery_id"=>$discovery_id));
                   
					if(sizeof($subQuestions) > 0 && $view == 1)
					{
						$subquestuions_string	=	"";
						foreach($subQuestions as $sub)
						{
							$sub_question_title 		=	$sub['question_title'];
							$sub_question_number 		=	$sub['question_number'];
							$sub_sub_part 				=	$sub['sub_part'];
							$subquestuions_string		.=	" (".$sub_sub_part.") ".$sub_question_title." ";
						}
						echo $subquestuions_string;
					}
					else
					{
						$subquestuions_string	=	"";
					}
				}
                ?>
                <tr>
                    <td colspan="2">
                        <h3>INTERROGATORY NO. <?php echo $question_number ?>:</h3>
                        <?php if($question_type_id == 3 && $view == 0) 
						{ 
						}
						else
						{
						?>
							<?php if($view == 0) { ?><b><u>Interrogatory</u></b> <?php }?>
                            <p><?php echo $question_title.$subquestuions_string; ?></p>
                            <?php
							if($has_extra_text == 1)
							{
								echo "<br><p><b>$extra_text_field_label: </b><br />$extra_text</p>";
							}
						}
						?>
						<?php //echo "Total Sub Questions:".sizeof($subQuestions)."<br>Question Type Id: ".$question_type_id."<br>Question Type Id: ".$question_type_id; ?>
                        <?php
						if($view == 1)
						{
							if($respond == 1)
							{
								?>
                        		<b><u>Objection</u></b>
                        		<p><?php echo $objection; ?></p>
                        		<?php
							}
						}
                        else
						{
							if($question_type_id == 1 || ($question_type_id == 3 && sizeof($subQuestions) == 0) )
							{
								?>
                                	<br>
									<b><u>Response</u></b>
                                    <?php
									if($final_response == "")
									{
									?>
										<p><?php echo finalResponseGenerate($objection,$answer); ?></p><br />
                                    <?php
									}
									else
									{
										echo "<p>".$final_response."</p><br />";
									}
									?>
								<?php
							}
							else if($question_type_id == 2 )
							{
								?>
                                	<br>
									<b><u>Response</u></b>
										<?php
										if(strtolower($answer) == 'yes'){$answer= "Yes";} 
										elseif(strtolower($answer) == 'no'){$answer =  "No";}
										else{$answer = "";}
										//echo finalResponseGenerate($objection,$answer);
										if($final_response == "")
										{
										?>
											<p><?php echo finalResponseGenerate($objection,$answer); ?></p><br />   
										<?php
										}
										else
										{
											echo "<p>".$final_response."</p><br />";
										}
										?>
								<?php   
							}
							if($question_type_id != 1)
							{
								if(in_array($question_number,array('17.1','217.1')) && $isconwithdiscoveryid != 0)
								{
									$con_mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q,response_questions rq',
                                                                    'rq.answer			as 	answer,
                                                                    rq.answer_detail	as 	answer_detail,
                                                                    rq.answered_at		as 	answer_time,
                                                                    dq.id 				as 	discovery_question_id,
                                                                    rq.objection		as  objection,
                                                                    q.id 				as 	question_id,
																	rq.fkresponse_id,
                                                                    q.question_type_id 	as 	question_type_id,
                                                                    q.question_title 	as 	question_title,
                                                                    q.question_number 	as 	question_number,
                                                                    q.sub_part 			as 	sub_part,
                                                                    q.is_pre_defined 	as 	is_pre_defined,
                                                                    q.question_title 	as 	question_title,
                                                                    is_depended_parent,
                                                                    depends_on_question, 
                                                                    have_main_question',
                                            
                                                                    "
                                                                    q.id 				= 	dq.question_id  	AND
																	rq.fkdiscovery_question_id	=	dq.id		AND
                                                                    rq.answer			=	'Deny' 				AND
                                                                    dq.discovery_id = '$isconwithdiscoveryid' 	
                                                                    ORDER BY q.question_number
                                                                    "
                                                                  );
									if(sizeof($con_mainQuestions) > 0)
									{
										$count=1;	
										foreach($con_mainQuestions as $con_question)
										{
											$con_discovery_question_id	=	$con_question['discovery_question_id'];
											$con_response_id			=	$con_question['fkresponse_id'];
										
											$query		=	"SELECT * FROM question_admits qa
																LEFT JOIN question_admit_results qar 
																ON  qar.discovery_question_id 	= 	'$con_discovery_question_id'  	AND
																qar.question_admit_id			=	qa.id 							AND 
																qar.fkresponse_id				=	'$con_response_id'";					  
											$con_SubQuestions	=	$AdminDAO->executeQuery($query);
											
												if($count == 1)
												{
													foreach($con_SubQuestions as $con_SubQuestion)
													{
													?>
														<p>(<?php echo $con_SubQuestion['question_no'].") ".$con_SubQuestion['question']; ?></p>
													
													<?php
													}
													echo "<br>";
												}
												/*foreach($con_SubQuestions as $con_SubQuestion)
												{
												?>
                                                    <p>(<?php echo $con_SubQuestion['question_no'].") ".$con_SubQuestion['sub_answer']; ?></p>
                                                
                                                <?php
												}*/
												?>
											<?php
											$count++;
										}
										?>
                                        <br>
                                        <b><u>Response</u></b>
                                        <p><?=nl2br($final_response)?></p>
                                        <?php
									}
									else
									{
										?>
                                        <br>
                                        <b><u>Response</u></b>
                                        <p></p>
                                        <?php
										
									}
								}
								else
								{
									if(strtolower($answer) == 'yes' || ($question_type_id == 3 && sizeof($subQuestions) > 0))
									{
										foreach($subQuestions as $data)
										{
											
											$question_id 			=	$data['question_id'];
											$question_type_id 		=	$data['question_type_id'];
											$form_id 				=	$data['form_id'];
											$question_title 		=	$data['question_title'];
											$question_number 		=	$data['question_number'];
											$sub_part 				=	$data['sub_part'];
											$is_pre_defined 		=	$data['is_pre_defined'];
											$discovery_question_id	=	$data['discovery_question_id'];
											
											if($response_id > 0)
											{
												$getAnswers				=	$AdminDAO->getrows("response_questions","*",
																				"fkresponse_id				=	:fkresponse_id AND  	
																				fkdiscovery_question_id 	= 	:discovery_question_id",
																				array(	"discovery_question_id"	=>	$discovery_question_id,
																						"fkresponse_id"			=>	$response_id));
												$answer 				=	$getAnswers[0]['answer'];
												$answer_time 			=	$getAnswers[0]['answer_time'];
												$objection 				=	$getAnswers[0]['objection'];
												$final_response			=	$getAnswers[0]['final_response'];
											}
											else
											{
												$answer 				=	"";
												$answer_time 			=	"";
												$objection 				=	"";
												$final_response			=	"";
											}
											?>
											<?php /*if($type == 2) { ?><b><u>Interrogatory</u></b><?php } */?>
											<p><?php echo "(".$sub_part.") ".$question_title ?></p>
											<!-- changes by Hassan -->
                                            <b><u>Response</u></b><br />
											<?php /*?><p><?php echo finalResponseGenerate($objection,$answer); ?></p><?php */?>
                                            <?php
	                                            $attached_response = finalResponseGenerate($objection,$answer);
											if($final_response == "" && $attached_response != null)
											{
											?>
												<p><?php echo $attached_response; ?></p><br />  
											<?php
											}
											else
											{
												echo "<p>".$final_response."</p><br />";
											}
											?>
                                            <?php
										}
									}
								}
							}
						}
						?>
                    </td>
                </tr>    
				<?php
            }
        }
        else if($form_id == 4)
		{
			foreach($mainQuestions as $data)
			{
				$question_id 			=	$data['question_id'];
				$question_type_id 		=	$data['question_type_id'];
				$question_title 		=	$data['question_title'];
				$question_number 		=	$data['question_number'];
				$sub_part 				=	$data['sub_part'];
				$is_pre_defined 		=	$data['is_pre_defined'];
				$discovery_question_id	=	$data['discovery_question_id'];
				if($response_id > 0)
				{
					$getAnswers				=	$AdminDAO->getrows("response_questions","*",
													"fkresponse_id				=	:fkresponse_id AND  	
													fkdiscovery_question_id 	= 	:discovery_question_id",
													array(	"discovery_question_id"	=>	$discovery_question_id,
															"fkresponse_id"			=>	$response_id));
					$answer 				=	$getAnswers[0]['answer'];
					$answer_time 			=	$getAnswers[0]['answer_time'];
					$answer_detail 			=	$getAnswers[0]['answer_detail'];
					$objection 				=	$getAnswers[0]['objection'];
					$final_response 		=	$getAnswers[0]['final_response'];
				}
				else
				{
					$answer 				=	"";
					$answer_time 			=	"";
					$answer_detail 			=	"";
					$objection 				=	"";
					$final_response			=	"";
				}
				
				?>
                 <tr>
                    <td colspan="2">
                    	<h3>REQUEST NO. <?php echo $question_number ?>:</h3>
                        <!--<b><u>Request</u></b>-->
                        <p><?php echo $question_title; ?></p>
                        <?php
						if($view != 1)
						{ 
						?>
                        	<br>
							<b><u>Response</u></b>
							<?php /*?><p><?php echo finalResponseGenerate($objection,$answer);?></p> <?php */?>
                            <?php
							if($final_response == "")
							{
							?>
								<p><?php echo finalResponseGenerate($objection,$answer); ?></p>   
							<?php
							}
							else
							{
								echo "<p>".$final_response."</p>";
							}
						}
						else if($respond == 1)
						{
						?>
                        <b><u>Objection</u></b>
                        <p><?php echo $objection; ?></p>
                        <?php
						}
						?>
                        <br /><br />
                </td>
                </tr>
                 <!--
                 Comment Sub Answers of RFA's on PDF part because
                 RFAS – The subparts and their answers don't go into the final Response to Requests for Admission. 
                 We let the Responding Party put her answers there because it's easier to put the subparts and answers right after the question. 
                 But, the final output is just Admits, Denys, and Objections. 
                 For the final output, the RFAS subparts and answers appear in response to either FROGS No. 17.1 or FROGSE No. 217.1, or both. 
                 -->
                 <?php /*?>
				 	
				 	<tr>
                 	<td colspan="2">
					<?php
					if(strtolower($answer) == 'deny')
					{
						?>
						<table class="wikitable tabela">
							<tbody>
							<?php
							foreach($generalQuestions as $generalQuestion)
							{
								$question_admit_id	=	$generalQuestion['id'];
								$subQuestionAnswers	=	$AdminDAO->getrows('question_admit_results',"*",":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id",array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id));
								$subQuestionAnswer	=	@$subQuestionAnswers[0]; 
								?>
								<tr>
									<td align="left">
                                    <b><?php echo $generalQuestion['question_no'] ?>) </b><?php echo $generalQuestion['question'] ?>
                                    </td>
                                </tr>
                                <tr>
									<td align="left"><?php echo "<b>ANS:</b>";  echo $subQuestionAnswer['sub_answer'] ?></td>
								</tr>
								<?php
							}
							?>
							</tbody>
						</table>
						<?php
					}
					?> 
					</td>
				</tr>
				 <?php */?> 
				<?php	
			} 
		}
        else if(in_array($form_id,array(3,5)))
        {
            foreach($mainQuestions as $data)
            {
                $question_id 		=	$data['question_id'];
                $question_type_id 	=	$data['question_type_id'];
                $question_title 	=	$data['question_title'];
                $question_number 	=	$data['question_number'];
                $sub_part 			=	$data['sub_part'];
                $is_pre_defined 	=	$data['is_pre_defined'];
                $discovery_question_id	=	$data['discovery_question_id'];
				if($response_id > 0)
				{
					$getAnswers				=	$AdminDAO->getrows("response_questions","*",
														"fkresponse_id				=	:fkresponse_id AND  	
														fkdiscovery_question_id 	= 	:discovery_question_id",
														array(	"discovery_question_id"	=>	$discovery_question_id,
																"fkresponse_id"			=>	$response_id));
					$answer 				=	$getAnswers[0]['answer'];
					$answer_time 			=	$getAnswers[0]['answer_time'];
					$answer_detail 			=	$getAnswers[0]['answer_detail'];
					$objection 				=	$getAnswers[0]['objection'];
					$final_response 		=	$getAnswers[0]['final_response'];
				}
				else
				{
					$answer 				=	"";
					$answer_time 			=	"";
					$answer_detail 			=	"";
					$objection 				=	"";
					$final_response 		=	"";
				}
                ?>
                <tr>
                    <td colspan="2">
                    	<?php
						if($form_id == 5)
						{
						?>
                         <h3>REQUEST NO. <?php echo $question_number ?>:</h3>
                    	<?php
						}
						else
						{
						?>
						 <h3>INTERROGATORY NO. <?php echo $question_number ?>:</h3>
						<?php
						}
						/*if($form_id == 5)
						{
						?>
                          <b><u>Request</u></b>
                    	<?php
						}
						else
						{
							?>
							<b><u>Interrogatory</u></b> 
                            <?php
						}*/
						?>
                        <p><?php echo $question_title; ?></p>
                        <?php
                        if($view != 1)
                        { 
                        ?>
                        	<br>
                            <b><u>Response</u></b>
                            <?php
                            if($form_id == 5)
                            { 	
								$reponse	=	'';
                                if($answer == "Select Your Response")
                                {
                                    echo "";
                                }
                                if(trim($answer) == "I have responsive documents") 
                                {
                                    $answer	= "Responsive documents are provided in Exhibit A.";
                                }
                                $str1	=	"A diligent search and a reasonable inquiry have been made in an effort to comply with this demand, however, responding party is unable to comply because they do not have any responsive documents in their possession, custody, or control.";
                                $str2	=	" However, respondent believes that ".$answer_detail." may have responsive documents.";
                                if(trim($answer) == "Responsive documents have never existed") 
                                {
                                    $answer	=	 $str1." Respondent does not believe that such documents have ever existed. ".$str2;
                                }
                                if(trim($answer) == "Responsive documents were destroyed") 
                                {
                                    $answer	=	 $str1." Respondent does not believe that such documents have ever existed. ".$str2; 
                                }
                                if(trim($answer) == "Responsive documents were lost, misplaced, stolen, or I lack access to them") 
                                {
                                    $answer	=	 $str1." Respondent believes that such documents were lost, misplace, stolen, or respondent lacks access to them. ".$str2;
                                }
                            }
                            /*else if($form_id == 3)
                            {
                                 echo "<p>".$answer."</p>";
                            }*/
							?>
                           <?php /*?> <p><?php echo finalResponseGenerate($objection,$answer); ?></p><?php */?>
                            <?php
							if($final_response == "")
							{
							?>
								<p><?php echo finalResponseGenerate($objection,$answer); ?></p>   
							<?php
							}
							else
							{
								echo "<p>".$final_response."</p>";
							}
                        }
						else if($respond == 1)
						{
							?>
							<b><u>Objection</u></b>
							<p><?php echo $objection; ?></p>
							<?php
						}
                        ?>
                        <br /><br />
                    </td>
                </tr>
					<?php
            }												
        }
        ?>
        <?php /*?><tr>
            <td  colspan="2">
            	<br />
	            Dated: <?php echo $served_date."  |  ".strtoupper($atorny_firm); ?>
            </td>
        </tr><?php */?>
        
        <tr>
        	<td width="60%"></td>
        	<td><br /><hr></td>
        </tr>
        <tr>
        	<td align="left" valign="top"><?php echo date('F j, Y'); ?></td>
            <td align="right">
                By: <?php echo ($atorny_name); ?><br>
                Attorney for <?php echo $att_for_client_role."<br>".$att_for_client_name ?> 
                <br />
            	Signed electronically,<br><img src="<?php echo ASSETS_URL; ?>images/court.png" style="width: 18px;padding-right: 3px;"> Cal. Rules of Court, rule 2.257
            </td>
        </tr>
    </tbody>
</table>

<!-- =================================================== -->
<!-- 			VERIFICATION PAGE 						 -->
<!-- =================================================== -->
<?php
if($is_verified > 0)
{
?>
<p class="break-page"></p>     
<table class="tabela1" style="border:none !important">
  <tbody>
    <tr>
    	<td  colspan="2" align="center"><h3><u>VERIFICATION</u></h3></td>
    </tr>
    <tr>
    	<td  colspan="2" align="justify">
            <p>I am the <?php echo $verification_by_name ?> in this action, and I have read the foregoing <b><?php echo $form_name; ?></b> and know its contents. The matters stated therein are true based on my own knowledge, except as to those matters stated on information and belief, and as to those matters I believe them to be true.
            </p>
            <br />
            <p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct. Executed on <?php echo date("F j, Y",strtotime($verification_datetime)); ?> at <?php echo $verification_city.", ".$verification_state; ?>. <i>Electronically Signed at <?php echo date("n/j/Y",strtotime($verification_datetime))." ".str_replace(array('am','pm'),array('a.m','p.m'),date("g:i a",strtotime($verification_datetime))) ?>. Pacific Time.</i> 
            
            </p>
        </td>
    </tr>
    <?php /*?><tr>
        <td colspan="2" align="right">
        	<br /><br />
            <?php echo strtoupper($responding_name); ?>
            <br />
            Signed electronically,<br>Cal. Rules of Court, rule 2.257
        </td>
    </tr><?php */?>
  </tbody>
</table>
<table style="border:none !important" width="100%">
  <tbody>
    <tr>
        <td align="left"><?php echo date('F j, Y',strtotime($verification_datetime)); ?></td>
        <td align="right">By: <?php echo $verification_signed_by; ?><br /> Signed electronically<br />Cal. Rules of Court, rule 2.257</td>
    </tr>
  </tbody>
</table>
<?php
}
?>
<!-- =================================================== -->
<!-- 			DECLARATION OF ADDITIONAL DISCOVERY		 -->
<!-- =================================================== -->


     
<?php
if($declaration_text != "")
{
	?>
    <p class="break-page"></p>
    <?php
	echo $declaration_text;
}
?>
<!-- =================================================== -->
<!-- 			PROOF OF SERVICE (POS) 						 -->
<!-- =================================================== --> 
<?php
if($is_served == 1 && $pos_text != "")
{
	?>
    <p class="break-page"></p>
    <?php
	echo $pos_text;
}
?>



<?php
$html = ob_get_contents();

ob_clean();
// echo $html; exit;
// exit;
$footertext			=	'<table width="100%" style="margin-top:30px;">
						<tr>
							<td width="5%" style="line-height:3px"></td>
							<td style="line-height:18px" align="center">{PAGENO}<br><br>'.$form_name.'<br>All rights reserved © '.date("Y").' EasyRogs. U.S. Patent Pending</td>
							<td width="5%"  style="text-align: right; line-height:3px"></td>
						</tr>
						</table>';
$oddEvenConfiguration = 
 [
    'L' => [ 
      'content' => '',
    ],
    'C' => [
      'content' => $footertext,
    ],
    'R' => [
      'content' => '',
    ],
    'line' => 0, // That's the relevant parameter
  ];
$headerFooterConfiguration = [
  'odd' => $oddEvenConfiguration,
  'even' => $oddEvenConfiguration
];
//$mpdf->SetHeader($headerFooterConfiguration);
//$mpdf->SetFooter($headerFooterConfiguration);
//$fileName	=	"{$case_title}-{$atorny_name}.pdf";
$fileName	=	"{$form_name}.pdf";
if($downloadORwrite == 1)
{
	$folderPath	=	$_SESSION['system_path']."uploads/documents/{$uid}";
	if (!file_exists($folderPath)) 
	{
		mkdir($folderPath, 0777, true);
	}
	$filePath	=	$folderPath."/".$fileName;
}
else
{
	$filePath	=	"{$fileName}";
}

fclose($fDebug);
pdf($filePath,$headerFooterConfiguration,@$downloadORwrite);