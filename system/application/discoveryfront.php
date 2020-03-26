<?php
@session_start();
require_once("../bootstrap.php");
require_once("../library/classes/AdminDAO.php");
$AdminDAO	=	new AdminDAO();	
include_once("../library/classes/login.class.php");
include_once("../library/classes/error.php");
require_once(FRAMEWORK_PATH."head.php");
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
header('Content-Type: text/html; charset=ISO-8859-1');

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$uid				=	$_GET['uid'];
$view				=	$_GET['view']; 
if($view == 1)
{
	$css	=	"disabled";
}
else
{
	$css	=	"";
}

/***************************************
		Query For Header Data
****************************************/	
//$AdminDAO->displayquery=1;
/*if(in_array($form_id,array(1,2)))
{
	$where_discoveryDetails	=	"(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND ";
}
else
{
	$where_discoveryDetails	=	"d.uid 			= :uid AND  ";
}*/
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
											a.email
											',
											/*(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND */
											"
											d.uid			=	'$uid' AND
											d.case_id 		= c.id AND  
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":uid"=>$uid)
										);

 
//$AdminDAO->displayquery=0;
$discovery_data		=	$discoveryDetails[0];
$case_title			=	$discovery_data['case_title'];
$discovery_id		=	$discovery_data['discovery_id'];
$discovery_uid		=	$discovery_data['uid'];
$case_number		=	$discovery_data['case_number'];
$jurisdiction		=	$discovery_data['jurisdiction'];
$judge_name			=	$discovery_data['judge_name'];
$county_name		=	$discovery_data['county_name'];
$court_address		=	$discovery_data['court_address'];
$department			=	$discovery_data['department'];
$case_id			=	$discovery_data['case_id'];
$form_id			=	$discovery_data['form_id'];
$set_number			=	$discovery_data['set_number'];
$attorney_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
$attorney_id		=	$discovery_data['attorney_id'];
$form_name			=	$discovery_data['form_name'];
$short_form_name	=	$discovery_data['short_form_name'];

$email				=	$discovery_data['email'];
$instructions		=	$discovery_data['discovery_instructions'];
$introduction		=	$discovery_data['introduction'];
$responding			=	$discovery_data['responding'];
$type				=	$discovery_data['type'];
$discovery_name		=	$discovery_data['discovery_name'];


$getResponses	=	$AdminDAO->getrows('responses',"*","fkdiscoveryid = '$discovery_id' AND isserved = 0 ORDER BY id DESC");
if(!empty($getResponses))
{
	$responseData					=	$getResponses[0];
	$response_id					=	$responseData['id'];
	$is_submitted					=	$responseData['is_submitted'];
	$discovery_verification_text	=	$responseData['discovery_verification_text'];
	$discovery_verification			=	$responseData['discovery_verification'];
	$verification_state				=	$responseData['verification_state'];
	$verification_signed_by			=	$responseData['verification_signed_by'];
	$verification_city				=	$responseData['verification_city'];
	$discovery_verification_by_name	=	$responseData['verification_by_name'];
	$verification_by_name			=	$responseData['verification_by_name'];
	
	
}
else
{
	$response_id	=	0;
}
if(! $verification_state)
{
	$verification_state = "California";
}
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
										'
										dq.id 				as 	discovery_question_id,
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
										dq.discovery_id 	= 	'$discovery_id' AND   
										(
											q.sub_part 		= 	'' OR 
											q.sub_part IS NULL OR 
											have_main_question	IN (0,2)
											
										)
										GROUP BY q.id
										$orderByMainQuestions
										"
									  );
//dump($mainQuestions);
											
/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions	=	$AdminDAO->getrows('question_admits',"*");


/****************************************
	Load Documents Array if Form 3,4,5 case
****************************************/
$_SESSION['documents'][$uid]=array();
$where	=	"";
if($form_id == 5)
{
	$where	=	" AND fkresponse_id = '$response_id' ";
}
$olddocuments	=	$AdminDAO->getrows('documents',"*","discovery_id = '$discovery_id' $where");
if(sizeof($olddocuments) > 0)
{
	foreach($olddocuments as $data)
	{
		$doc_purpose	=	$data['document_notes'];
		$doc_name		=	$data['document_file_name'];
		$doc_path		=	"../uploads/documents/".$data['document_file_name'];
		if($doc_name != "")
		{
			$documents[$uid][]	=	array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);		
		}
	}
	$_SESSION['documents']	=	$documents;
}
$respondingdetails		=	getRPDetails($responding);
$responding_name		=	$respondingdetails['client_name'];
$responding_email		=	$respondingdetails['client_email'];
$responding_type		=	$respondingdetails['client_type'];
$responding_role		=	$respondingdetails['client_role'];

if($verification_signed_by == "")
{
	$verification_signed_by	=	$responding_name;
}
/****************************************************
Function for getting responding or proponding details
****************************************************/
function getRPDetails($rp_id) 
{
	global $AdminDAO;
	//$AdminDAO->displayquery=1;
	$clients			=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$rp_id));
	//$AdminDAO->displayquery=0;
	return $clients[0];
}
$discovery_name	=	"RESPONSE TO ";
 
if($discovery_data['discovery_name'] == '')
{
	$discovery_name	.= $discovery_data['form_name'];
}
else
{
	$discovery_name	.= $discovery_data['discovery_name'];
}
$discovery_name	.= " [Set ".numberTowords( $set_number )."]";

?>

<body class="blank">
<style>
.register-container
{
	max-width:100% !important;	
}
.instruction-collapse [data-toggle="collapse"]:after 
{
	content: "Hide";
	float: right;
	font-size: 14px;
	line-height: 20px;
	
}
.instruction-collapse [data-toggle="collapse"].collapsed:after 
{
	content: "Show";
	color: #fff;
}
body.modal-open 
{
    position: static !important;
}
</style>

<div class="color-line"></div>
<div class="register-container" style="padding-top:10px !important">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-center m-b-md">
                <h3><?php echo $discovery_name ?></h3>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                		
                		<table class="table table-bordered table-hover table-striped">
                              <tbody>
                              	 <tr>
                                  <th>Case</th>
                                  <td><?php echo $case_title ?></td>
                                  <th>Number</th>
                                  <td><?php echo $case_number ?></td>
                                </tr>
                                 <tr>
                                 <th>County</th>
                                  <td><?php echo $county_name ?></td>
                                  <th>State</th>
                                  <td><?php echo $jurisdiction ?></td>
                                  
                                </tr>
                              </tbody>
                            </table>
                        <form name="discoverydetailsform" action="#" method="post" id="discoverydetailsform">
                        <input type="hidden" name="case_id" value="<?php echo $case_id ?>">
                        <input type="hidden" name="form_id" value="<?php echo $form_id ?>">
                        <input type="hidden" name="response_id" value="<?php echo $response_id ?>">
                        <input type="hidden" name="response_name" value="<?php echo $discovery_name ?>">
                        <input type="hidden" name="uid" value="<?php echo $uid ?>">
                        <input type="hidden" name="discovery_verification_by_name" id="discovery_verification_by_name" value="<?php echo @$discovery_verification_by_name ?>">
                        <input type="hidden" name="discovery_verification" id="discovery_verification" value="<?php echo @$discovery_verification ?>">
                        <input type="hidden" name="discovery_verification_state" id="discovery_verification_state" value='<?php echo @$verification_state ?>'>
                        <input type="hidden" name="discovery_verification_city" id="discovery_verification_city" value='<?php echo @$verification_city?>'>
                        <input type="hidden" name="discovery_verification_signed_by" id="discovery_verification_signed_by" value='<?php echo @$verification_signed_by?>'>
                        
                        <input type="hidden" name="discovery_sender_note" id="discovery_sender_note">
                         <input type="hidden" name="email_solicitation" id="email_solicitation">
                          <input type="hidden" name="email_body" id="email_body">
                        
                        
						<hr>
                       
                        <div class="row">
                        	<div id="loadinstructions"></div>
                        	<div class="col-md-12">
                            	<ul class="list-group">
                                    	<?php
										if(in_array($form_id,array(1,2)))
										{
											foreach($mainQuestions as $data)
											{
												$dependent_answer		=	"";	
												$question_id 			=	$data['question_id'];
												$question_type_id 		=	$data['question_type_id'];
												$have_main_question	 	=	$data['have_main_question'];
												$p_q_type_id 			=	$data['question_type_id'];
												$question_title 		=	$data['question_title'];
												$question_number 		=	$data['question_number'];
												$sub_part 				=	$data['sub_part'];
												$p_sub_part 			=	$data['sub_part'];
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
												}
												else
												{
													$answer 				=	"";
													$answer_time 			=	"";
												}
												/**
												* IF Depaends on some question then we need that question answer
												**/
												if($depends_on_question > 0 && $response_id > 0)
												{
													$dependent_answer	=	getAnswerOfDependentParentQuestion($discovery_id,$depends_on_question,$response_id);
												}
												?> 
												<li <?php if($depends_on_question != 0) {?>class="list-group-item row_<?php echo $depends_on_question; ?>" <?php if($dependent_answer == 'No' || $dependent_answer == ''){ ?>style='display:none;' <?php } }else {?> class="list-group-item"  <?php } ?>>
													<?php
													if($question_type_id != 1)
													{
														//$AdminDAO->displayquery=1;
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
																							have_main_question',
																							
																				"q.question_number 	= 	'$question_number' AND  
																				q.id 				= 	 dq.question_id  AND
																				dq.discovery_id 	= 	:discovery_id AND 
																				q.sub_part 			!=   '' GROUP BY question_id ORDER BY display_order ",
																				array(":discovery_id"=>$discovery_id));
														//dump($subQuestions);
														//$AdminDAO->displayquery=0;
														
													}
													?>
													<div class="form-group"> 
														<p> 
															<b>Q No. <?php echo $question_number;?><?php echo $have_main_question==0?"&nbsp;($sub_part)":""?>: </b>
															<?php 
															echo $question_title;
															if($has_extra_text == 1)
															{
																echo "<p><b>$extra_text_field_label: </b>$extra_text</p>";
															}
															if(($question_number == "17.1" || $question_number == "217.1"))
															{	
																
																foreach($subQuestions as $data)
																{
																	echo  ". (".$data['sub_part'].") ". $data['question_title'];
																}
															}
															 ?>
														</p>
														<?php
															if(($question_number == "17.1" || $question_number == "217.1"))
															{
																
															}
															else if($view != 1)
															{
																if($question_type_id == 1)
																{
																?>
																	<input type="hidden" name="have_main_question[<?php echo $discovery_question_id; ?>]" value="<?php echo $have_main_question?>"/>
																	<textarea id="answer<?php echo $discovery_question_id ?>" class="form-control" name="answer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo htmlentities($answer) ?></textarea>
																<?php
																}
																else if($question_type_id == 2)
																{
																	$question_no_makeid	=	str_replace('.','_',$question_number);
																	?>
																		<div class="form-check form-check-inline">
																			<label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Yes" onClick="checkFunction('<?php echo $question_no_makeid ?>','1')<?php if($is_depended_parent ==1 ){ ?>,showhidequestions('<?php echo $question_id;?>',1)<?php }?>" <?php if($answer == 'Yes'){echo "checked";} ?> <?php echo $css ?>>Yes</label>
																			<label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="No" onClick="checkFunction('<?php echo $question_no_makeid ?>','2')<?php if($is_depended_parent ==1 ){ ?>,showhidequestions('<?php echo $question_id;?>',2)<?php }?>" <?php if($answer == 'No'){echo "checked";} ?> <?php echo $css ?>>No</label>                                      
																		</div>
																	<?php
																}
																if($question_type_id != 1)
																{
																	?>
																	<ul class="list-group" id="subdiv<?php echo $question_no_makeid;?>" <?php if($question_type_id == 2 && $answer != "Yes"){ ?>style="display:none" <?php } ?>>
																		<?php
																		
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
																			$have_main_question		=	$data['have_main_question'];
																			if($response_id > 0)
																			{
																				$getAnswers				=	$AdminDAO->getrows("response_questions","*",
																												"fkresponse_id				=	:fkresponse_id AND  	
																												fkdiscovery_question_id 	= 	:discovery_question_id",
																												array(	"discovery_question_id"	=>	$discovery_question_id,
																														"fkresponse_id"			=>	$response_id));
																				$answer1 				=	$getAnswers[0]['answer'];
																				$answer_time 			=	$getAnswers[0]['answer_time'];
																			}
																			else
																			{
																				$answer1 				=	"";
																				$answer_time 			=	"";
																			}
																			
																		?>
																		 <li class="list-group-item">
																			<div class="form-group">
																				<p> 
																					<b><?php echo $sub_part ?>) </b>
																					<?php echo $question_title ?>
																				</p>
																				<input type="hidden" class="subanswer_<?php echo $question_no_makeid?>" name="have_main_question[<?php echo $discovery_question_id; ?>]" value="<?php echo $have_main_question?>" <?php if($answer == "No" || ($answer == "" && $p_q_type_id == 1) || ($answer == "" && $p_q_type_id == 2)){ ?> disabled <?php } ?>/>
																				<textarea  
																				id="answer<?php echo $discovery_question_id ?>" 
																				class="form-control subanswer_<?php echo $question_no_makeid?>" 
																				name="answer[<?php echo $discovery_question_id; ?>]" 
																				placeholder="Your Answer" required 
																				<?php echo $css ?> 
																				<?php /*?><?php if($answer == "No" || ($answer == "" && $p_q_type_id == 1) || ($answer == "" && $p_q_type_id == 2)){ ?> disabled <?php } ?>><?php echo $answer1 ?></textarea><?php */?>
                                                                                <?php if(($answer == "No" || $answer == "") && $p_q_type_id != 3){ ?> disabled <?php } ?>><?php echo htmlentities($answer1) ?></textarea>
																			</div>  
																		</li>   
																		<?php
																		}
																		?>
																  	</ul> 
																	<?php
																}	
															}
                                                        ?>
                                                    </div>
												</li>
												<?php	
											}
										}
										else if($form_id == 4)
										{
											foreach($mainQuestions as $data)
											{
												?>
												<li class="list-group-item">
													<?php
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
													}
													else
													{
														$answer 				=	"";
														$answer_time 			=	"";
														$answer_detail 			=	"";
													}
													?>
													<div class="form-group">
														<p> 
															<b>Q No. <?php echo $question_number ?>: </b>
															<?php echo $question_title; ?>
														</p>
                                                        <?php
														if($view != 1)
														{ 
														?>
                                                        <div class="form-check form-check-inline">
                                                            <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Admit" onClick="checkFunction('<?php echo $discovery_question_id ?>','2')" <?php if($answer == 'Admit'){echo "checked";} ?> <?php echo $css ?>>Admit</label>
                                                            <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Deny" onClick="checkFunction('<?php echo $discovery_question_id ?>','1')" <?php if($answer == 'Deny'){echo "checked";} ?> <?php echo $css ?>>Deny</label>                                      
                                                        </div>
                                                    	<ul class="list-group" id="subdiv<?php echo $discovery_question_id;?>" <?php if($answer == "Admit" || $answer == ""){ ?>style="display:none" <?php } ?>>
                                                        <?php
														foreach($generalQuestions as $generalQuestion)
														{
															$question_admit_id	=	$generalQuestion['id'];
															$subQuestionAnswers	=	$AdminDAO->getrows('question_admit_results',"*",":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id AND fkresponse_id = :fkresponse_id",array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id,"fkresponse_id" => $response_id));
															$subQuestionAnswer	=	$subQuestionAnswers[0]; 
															
															if($question_admit_id == 1)
															{
																$sub_answer_show	=	$question_number;
															}
															else
															{
																$sub_answer_show	=	htmlentities($subQuestionAnswer['sub_answer']);
															}
														?>
                                                        <li class="list-group-item">
                                                        <div class="form-group">
                                                            <p> 
                                                                <b><?php echo $generalQuestion['question_no'] ?>) </b>
                                                                <?php echo $generalQuestion['question'] ?>
                                                            </p>
                                                            <textarea <?php if($answer == "Admit" || $answer == ""){ ?> disabled <?php } ?> id="subanswer<?php echo $discovery_question_id.'_'.$question_admit_id ?>" class="form-control subanswer_<?php echo $discovery_question_id;?>" name="subanswer[<?php echo $discovery_question_id ?>][<?php echo $question_admit_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo $sub_answer_show; ?></textarea>
                                                        </div>
                                                        </li>
                                                        <?php
														}
														?>
                                                    </ul>
                                                    	<?php
														}
														?>
                                                    </div>
												</li>
												<?php	
											}
										}
										else if(in_array($form_id,array(3,5)))
										{
											foreach($mainQuestions as $data)
											{
												?>
												<li class="list-group-item">
													<?php
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
													}
													else
													{
														$answer 				=	"";
														$answer_time 			=	"";
														$answer_detail 			=	"";
													}
													?>
													<div class="form-group">
														<p> 
															<b>Q No. <?php echo $question_number ?>: </b>
															<?php echo $question_title; ?>
														</p>
                                                        <?php
														if($view != 1)
														{
															if($form_id == 5)
															{
															?>
                                                                <select class="form-control" id="answer<?php echo $discovery_question_id; ?>"  name="answer[<?php echo $discovery_question_id; ?>]" onChange="checkFunctionForm5('<?php echo $discovery_question_id; ?>',this.value)" <?php echo $css ?>>
                                                                <option <?php if($answer == "Select Your Response") echo "selected"; ?>>Select Your Response</option>
                                                                <option <?php if($answer == "I have responsive documents") echo "selected"; ?>>I have responsive documents</option>
                                                                <option <?php if($answer == "Responsive documents have never existed") echo "selected"; ?>>Responsive documents have never existed</option>
                                                                <option <?php if($answer == "Responsive documents were destroyed") echo "selected"; ?>>Responsive documents were destroyed</option>
                                                                <option <?php if($answer == "Responsive documents were lost, misplaced, stolen, or I lack access to them") echo "selected"; ?>>Responsive documents were lost, misplaced, stolen, or I lack access to them</option> 
                                                                </select>
															<?php
															}
															else if($form_id == 3)
															{
															?>
																<textarea id="answer<?php echo $discovery_question_id ?>" class="form-control " name="answer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo htmlentities($answer) ?></textarea>
															<?php
															}
															if($form_id == 5)
															{
															?>
																<ul class="list-group" id="note<?php echo $discovery_question_id;?>" <?php if($answer != "I have responsive documents"){ ?>style="display:none" <?php } ?>>
																	<li class="list-group-item">
																	<div class="form-group">
																		<p> 
																			<b>Note: </b>
																			Upload the documents in Exhibit A section at bottom.
																		</p>
																	</div>
																	</li>
																</ul> 
																<ul class="list-group" id="subdiv<?php echo $discovery_question_id;?>" <?php if($answer == 'Select Your Response' || $answer == "I have responsive documents"){ ?>style="display:none" <?php } ?>>
																	<li class="list-group-item">
																	<div class="form-group">
																		<p> 
																			<b>a) </b>
																			Enter the name and address of anyone you believes has the documents.
																		</p>
																		<textarea <?php if($answer == 'Select Your Response' || $answer == "I have responsive documents"){ ?> disabled <?php } ?> id="subanswer<?php echo $discovery_question_id ?>" class="form-control" name="subanswer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo htmlentities($answer_detail) ?></textarea>
																	</div>
																	</li>
																</ul> 
															<?php
															}
														}
															?>
                                                    </div>
												</li>
												<?php	
											}												
										}
										?>
                                </ul>
                            </div>
                            <?php
							if(in_array($form_id,array(3,4)) && $view != 1 && !empty($_SESSION['documents'][$uid]))
							{
							?>
                            <div class="col-md-12">
                            <hr> 
                            <ul class="list-group"> 
                                <li class="list-group-item">
                                    <div class="">
                                        <p> 
                                            <b>Documents related to this discovery.</b>
                                        </p>
                                    </div>
                                    <div id="uploaddiscoverydocs">
                                        
                                    </div>
                                </li>
                            </ul>
                            </div>
                            <?php
							}
							if(in_array($form_id,array(5)) && $view != 1)
							{
							?>
                            <div class="col-md-12">
                            	<hr> 
                                <ul class="list-group"> 
                                    <li class="list-group-item">
                                        <div class="">
                                        	<p> 
                                            	<h3>Exhibit A:</h3>
                                        		<b>Upload your documents here.</b>
                                        	</p>
                                        </div>
                                		<div id="extraupload"></div>
                                        <button type="button" class="btn btn-info" id="extrabutton">
                                            <i class="icon-ok bigger-110"></i>
                                            <span class="ladda-label">Upload</span><span class="ladda-spinner"></span>
                                        </button>
                                        <div id="uploadeddocs">
                                        	
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <?php
							}
							?>
                        </div>
                       	
						<?php
						//echo $view;
						//echo "Hello"; 
                      if($view != 1) 
						{
							?>
                            <div class="text-center">
                            <button type="button" class="btn btn-success" onClick="addform('discoveryfrontaction.php?q=0','discoverydetailsform',' ','discoveryfront-thanks.php');">
                            <i class="icon-ok bigger-110"></i>
                            <span class="ladda-label">Save</span><span class="ladda-spinner"></span>
                            </button>
                            <button type="button" class="btn  btn-info" onClick="submitForm();">
                            <i class="icon-ok bigger-110"></i>
                            <span class="ladda-label">Attorney <i class="fa fa-play" aria-hidden="true"></i></span><span class="ladda-spinner"></span>
                            </button>
                                <p>
                                    <b id="successmsg" style='color:Green; display:none;'>Thanks...! You have successfully created your account!</b>
                                    <b id="errormsg" style='color:Red; display:none;'>Oppppps...! Email already exist.</b>
                                </p>
                            </div>
                            <?php
						}
						?>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../jsinclude.php");
?>

<script src="<?php echo VENDOR_URL;?>jquery-validation/jquery-1.9.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo VENDOR_URL;?>bootstrap/dist/js/bootstrap.min.js"></script>
<link href="<?php echo VENDOR_URL;?>uploadfile.css" rel="stylesheet">
<script src="<?php echo VENDOR_URL;?>jquery.uploadfile.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$.noConflict();
function loadinstructions(form_id,id)
{
	var type = '<?php echo $type ?>';
	$.get("discoveryloadforminstruction.php?form_id="+form_id+"&id="+id+"&viewonly=1&type="+type).done(function(resp){$("#loadinstructions").html(trim(resp));});
}
$(document).ready(function()
{
	loadinstructions('<?php echo $form_id ?>','<?php echo $discovery_id ?>');
	//$('#cityname').editable();
	//$('#statename').editable();
});
function submitForm()
{
	
	callModal();
	/*swal({
		title: "Verification?",
		text: "Do you want to verify the answers?",
		icon: "warning",
		dangerMode: true,
		buttons: 
		{
			catch: {text: "Yes"},
			defeat: {text: "No",className: "btn-danger"}
			
			
		},
	}).then((willDelete) => 
	{
		switch (willDelete) 
		{
			case "catch":
				$("#discovery_verification").val(1);
				callModal();
				$("#msgVerification").html("");
				break;

			case "defeat":
				addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');
				break;
			default:
		}
	});*/
}


function callModal()
{
	$('#myModal').modal('toggle');
	
}
//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');
function checkFunction(subdivid, option)
{
	//alert(subdivid);
	if(option == 1)
	{
		$("#subdiv"+subdivid).show();
		$(".subanswer_"+subdivid).prop('disabled',false);
		
	}
	else  if(option == 2)
	{
		$("#subdiv"+subdivid).hide();
		$(".subanswer_"+subdivid).prop('disabled',true);		
	}
}
function checkFunctionForm5(subdivid, option)
{
	//alert(option);
	if(option == 'I have responsive documents' || trim(option) == 'Select Your Response')
	{
		if(option == 'I have responsive documents')
		{
			$("#note"+subdivid).show();
		}
		else
		{
			$("#note"+subdivid).hide();
		}
		$("#subdiv"+subdivid).hide();
		$("#subanswer"+subdivid).prop('disabled',true);
	}
	else  
	{
		$("#subdiv"+subdivid).show();	
		$("#subanswer"+subdivid).prop('disabled',false);	
		$("#note"+subdivid).hide();
	}
}
$(document).ready(function()
{
	
	loaduploadeddocs();
	<?php
	if(in_array($form_id,array(3,4)))
	{
	?>
		loaduploaddiscoverydocs();
	<?php
	}
	?>
	var extraObj = $("#extraupload").uploadFile({
	url:"frontdocumentuploads.php",
	fileName:"myfile",
	extraHTML:function()
	{
			var html = "<div><input type='hidden' name='rp_uid' value='<?php echo $uid ?>' /> <br/>";
			html += "</div>";
			return html;    		
	},
	autoSubmit:false,
	afterUploadAll:function(obj)
	{
		$(".ajax-file-upload-container").html("");
		loaduploadeddocs();
	}
	});
	$("#extrabutton").click(function()
	{
		extraObj.startUpload();
	}); 
});
function loaduploadeddocs()
{
	var rp_uid	=	'<?php echo $uid; ?>';
	$("#uploadeddocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=1");
}
function loaduploaddiscoverydocs()
{
	var rp_uid	=	'<?php echo $uid; ?>';
	$("#uploaddiscoverydocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=0");
}

function deleteDoc(id,rp_uid)
{
	$.post( "deletefrontdocs.php", { id: id,rp_uid:rp_uid }).done(function( data ) 
	{
		loaduploadeddocs();
	});
}
function SaveVerificationText(flag)
{
	var checkerror	=	0;
	if(flag == 1)
	{
		$("#discovery_verification").val(1);
		$("#msgVerification").html("");
		$("#discovery_verification_by_name").val($("#verification_by_name").val());
		$("#discovery_verification_state").val($("#verification_state").val());
		$("#discovery_verification_city").val($("#verification_city").val());
		$("#discovery_verification_signed_by").val($("#verification_signed_by").val());
		
		$("#email_solicitation").val($("#email_solicitation_popup").val());
		$("#email_body").val($("#email_body_popup").val());
		
		if($("#verification_city").val() == "")
		{
			$("#msgVerification").html("Please enter your city.");
			var checkerror	=	1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');	
		}
		else if($("#verification_by_name").val() == "")
		{
			$("#msgVerification").html("Please enter your type.");
			var checkerror	=	1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');	
		}
		else if($("#verification_signed_by").val() == "")
		{
			$("#msgVerification").html("Please enter your name.");
			var checkerror	=	1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');	
		}
		else
		{
			callModal();
			addform('discoveryfrontaction.php?q=1&redirect=1','discoverydetailsform','','discoveryfront-thanks.php');
		}
	}
	else
	{
		callModal();
		addform('discoveryfrontaction.php?q=1&redirect=1','discoverydetailsform','','discoveryfront-thanks.php');
	}
}
function showhidequestions(parentid,yesorno)
{
	if(yesorno == 1)
	{
		$(".row_"+parentid).show();
	}
	else 
	{
		$(".row_"+parentid).hide();
	}
	
}
</script>

<?php
//Email body and solutation setup
//Email Salutation 
$emaildata				=	$AdminDAO->getrows("email_log","email_salutation","sender_type = 2 AND receiver_type = 1 ORDER BY id DESC LIMIT 1",array());
$email_solicitation		=	$emaildata[0]['email_salutation'];
if($email_solicitation == "")
{
	$email_solicitation	=	"Hi,";	
}


ob_start();
?>
<h4><?php echo $discovery_name." [Set ".numberTowords( $set_number )."]"; ?> is submitted successfully from the client.</h4>
<ul style="padding-left:5px">
	<li><b>Case Title:</b> <?php echo $case_title ?></li>
    <li><b>Case#:</b> <?php echo $case_number ?></li>
    <li><b>State:</b> <?php echo $jurisdiction ?></li>
    <li><b>County/District:</b> <?php echo $county_name ?></li>
    <li><b>Court Address:</b> <?php echo $court_address ?></li>
    <li><b>Attorney:</b> <?php echo $attorney_name ?></li>
</ul>
	<?php
    $email_body = ob_get_contents();
    ob_clean();
?>
<!-- Verification Modal Start -->
<div class="modal fade" id="myModal" role="dialog"  data-backdrop="static" data-keyboard="false">
<div class="modal-dialog" style="width:880px">
  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header" style="padding:15px">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
     <h3 class="modal-title text-center">VERIFICATION</h3>
    </div>
    <div class="modal-body">
    	<?php
		$statesArray	=	$AdminDAO->getrows("system_state","*","fkcountryid = '254'");
		if($verification_by_name == "")
		{
			$verification_by_name	=	$responding_role;
		}
		?>
        <div id="verification_text" style="line-height:23px !important; text-align:justify; font-size:13px">
            <p>I am the <input type='text' name='verification_by_name' id='verification_by_name' value='<?php echo $verification_by_name; ?>' placeholder='Role: Plaintiff, Defendant, etc.'> in this action, and I have read the foregoing <b><?php echo $discovery_name ?></b> and know its contents.
             The matters stated therein are true based on my own knowledge, except as to those matters stated on information and belief, and as to those matters I believe them to be true.
            </p><p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct.<br>Executed on <?php echo date("F j, Y"); ?> at <input placeholder="City" type='text' name='verification_city' id='verification_city' value='<?php echo $verification_city; ?>'>,
            <input type='text' name='verification_state' id='verification_state' value='<?php echo $verification_state; ?>' placeholder="State">.
            <?php /*?><select id="verification_state" name="verification_state" style="height:32px">
            <?php
			foreach($statesArray as $data)
			{
			?>
            <option <?php if($data['statename'] == $verification_state){echo "selected";} ?>><?php echo $data['statename']; ?></option>
            <?php
			}
			?>
            </select><?php */?>
            </p>
            <p>
                <img src="<?php echo ASSETS_URL; ?>images/court.png" style="width: 18px;padding-right: 3px;">
                <?php
                    // added by JS 3/7/20
                    if($form_id == 3){
                        echo "Code Civ.Proc., &sect; 2030.250  ";
                        echo instruction(16);
                    }
                    else if($form_id == 4){
                        echo "Code Civ.Proc., &sect; 2033.240 ";
                        echo instruction(19);
                    }
                ?>
            </p>
        </div>
        <br>
        <br>
        <p class="text-right">
        By: <input placeholder="Signed By" type='text' name='verification_signed_by' id='verification_signed_by' value='<?php echo $verification_signed_by ?>'>
		
        <br>Signed electronically,
        <br> <img src="<?php echo ASSETS_URL; ?>images/court.png" style="width: 18px;padding-right: 3px;">Cal. Rules of Court, rule 2.257</p>
        <br>
        <br>
        <div class="form-group" style="display:none">
        <p> 
            <h3>Email Template</h3>
        </p>
        <div class="form-group">
            <label for="email_solicitation" class="col-form-label">Email Salutation:</label>
            <input type="text" name="email_solicitation_popup" id="email_solicitation_popup" placeholder="Add Salutation"  class="form-control m-b" value="<?php echo $email_solicitation; ?>">
        </div>
        <div class="form-group">
            <label for="email_body" class="col-form-label">Email Body:</label>
            <textarea  rows="10" name="email_body_popup" id="email_body_popup" placeholder="Add email body"  class="form-control m-b"><?php echo ($email_body); ?></textarea>
        </div>
        </div>
        
    </div>
    <div class="modal-footer">
      <div class="row">
      	<div class="col-md-7 text-left" style="margin-top:7px">
            <span style="color:red" id="msgVerification"></span>
        </div>
        <div class="col-md-5 text-right">
        	<button type="button" class="btn btn-primary" onClick="SaveVerificationText(1)">Click to Verify</button>
            <button type="button" class="btn btn-danger" onClick="SaveVerificationText(2)">Skip</button>
            
        </div>
      </div>
    </div>
  </div>
  
</div>
</div>
<!-- Verification Modal End -->
<script>
$(document).ready(function()
{
	 CKEDITOR.replace( 'email_body_popup' );
});
</script>
</body>
</html>