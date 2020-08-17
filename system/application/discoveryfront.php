<?php
@session_start();
require_once("../bootstrap.php"); 
require_once(SYSTEMPATH."application/ctxhelp_header.php"); 
require_once(SYSTEMPATH."application/kb_modal.php"); 
include_once("../library/classes/login.class.php");
include_once("../library/classes/functions.php");

global $logger; 

$uid  = $_GET['uid'];
$view = $_GET['view'];
if( $view == 1) {
	$css = "disabled";
}
else {
	$css = "";
}

/***************************************
		Query For Header Data
****************************************/
$discoveryDetails	= $AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
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
											a.email,
											a.phone
											',
											/*(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND */
											"
											d.uid			= '$uid' AND
											d.case_id 		= c.id AND
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":uid"=>$uid)
										);


$discovery_data = $discoveryDetails[0];
Side::legacyTranslateCaseData(
	$discovery_data['case_id'],
	$discovery_data,
	$discovery_data['attorney_id'] // !! will use this attorney's side data
);

$case_title			= $discovery_data['case_title'];
$discovery_id		= $discovery_data['discovery_id'];
$discovery_uid		= $discovery_data['uid'];
$case_number		= $discovery_data['case_number'];
$jurisdiction		= $discovery_data['jurisdiction'];
$judge_name			= $discovery_data['judge_name'];
$county_name		= $discovery_data['county_name'];
$court_address		= $discovery_data['court_address'];
$department			= $discovery_data['department'];
$case_id			= $discovery_data['case_id'];
$form_id			= $discovery_data['form_id'];
$set_number			= $discovery_data['set_number'];
$attorney_name		= $discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
$attorney_id		= $discovery_data['attorney_id'];
$form_name			= $discovery_data['form_name'];
$short_form_name	= $discovery_data['short_form_name'];

$phone				= $discovery_data['phone'];
$email				= $discovery_data['email'];
$instructions		= $discovery_data['discovery_instructions'];
$introduction		= $discovery_data['introduction'];
$responding			= $discovery_data['responding'];
$type				= $discovery_data['type'];
$discovery_name		= Discovery::getTitle( "Response to ". $discovery_data['discovery_name'] ?? $discovery_data['form_name'], $set_number, Discovery::STYLE_AS_IS );

$logger->info([$discovery_data]);

$getResponses = $AdminDAO->getrows('responses',"*","fkdiscoveryid = '$discovery_id' ORDER BY id DESC");

include_once(SYSTEMPATH.'body.php');
include_once(SYSTEMPATH.'application/client_instructions_modal.php');
?>
<style>
.error p, .error br {
	padding: 0.2em;
	line-height: 2;
}
.error a {
	color: initial; background-color: #EEE;
}
.register-container {
	max-width:100% !important;
}
body.modal-open {
    position: static !important;
}
textarea#answer {
	white-space: pre-line;
}
</style>
<div class="main" style="">
    <aside class="sidebar left "><div class="fixed"></div></aside>
	<div class="container register-container col-md-10 col-lg-10 center-block" style="float: none;">
		<div class="hpanel">
			<div class="panel-body">
<?php
if(!empty($getResponses))
{
	$responseData					= $getResponses[0];
	$response_id					= $responseData['id'];
	$is_submitted					= $responseData['is_submitted'];
	$submit_date					= $responseData['submit_date'];
	$discovery_verification_text	= $responseData['discovery_verification_text'];
	$discovery_verification			= $responseData['discovery_verification'];
	$verification_state				= $responseData['verification_state'];
	$verification_signed_by			= $responseData['verification_signed_by'];
	$verification_city				= $responseData['verification_city'];
	$discovery_verification_by_name	= $responseData['verification_by_name'];
	$verification_by_name			= $responseData['verification_by_name'];
	if( $is_submitted || $is_served ) {
		$action      = $is_served ? "served" : "submitted";
		$submit_date = strtotime($submit_date);
?>
	<table class="table table-bordered table-hover table-striped error">
		<tr class="m-b-md">
			<h3 class="text-center "><?= ucwords("Already $action") ?></h3>
		</tr>
		<tr>
			<td>
		<p>We're sorry, but the <?= $discovery_name ?> in the case of <?= "<i>$case_title</i>" ?> were already <?= $action ?> on <?= date('F j, Y', $submit_date ) ." at ". date('h:i A', $submit_date ) ?>.</p> 
		<p>If you believe this to be an error, please contact <?= $attorney_name ?> at <?= "<a href='mailto:$email'>$email</a>" ?> or <?= "<a href='tel:$phone'>$phone</a>" ?>.</p>
		<br/>
		<p>Thank you.</p>
			</td>
		</tr>
	</table>
	<!-- Smartsupp Live Chat script -->
	<script type="text/javascript">
		var _smartsupp = _smartsupp || {};
		_smartsupp.key = 'ae242385584ca4d3fd78d74a04dbd806ef3957e0';
		window.smartsupp||(function(d) {
			var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
			s=d.getElementsByTagName('script')[0];c=d.createElement('script');
			c.type='text/javascript';c.charset='utf-8';c.async=true;
			c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
		})(document);
	</script>

	</body>
	</html>

<?php
		exit;
	}
}
else {
	$response_id	= 0;
}
if(! $verification_state) {
	$verification_state = "California";
}
/***************************************
Query For Forms 1,2,3,4,5 Questions
****************************************/
if(in_array($form_id,array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS, Discovery::FORM_CA_RPDS))) {
	$orderByMainQuestions	= "  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number ";
}
else {
	$orderByMainQuestions	= "  ORDER BY display_order, q.id ";
}
$mainQuestions	= $AdminDAO->getrows('discovery_questions dq,questions q',
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

/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions	= $AdminDAO->getrows('question_admits',"*");


/****************************************
	Load Documents Array if Form 3,4,5 case
****************************************/
$_SESSION['documents'][$uid]=array();
$where	= "";
if( $form_id == Discovery::FORM_CA_RPDS ) {
	$where	= " AND fkresponse_id = '$response_id' ";
}
$olddocuments = $AdminDAO->getrows('documents',"*","discovery_id = '$discovery_id' $where");
if( sizeof($olddocuments) ) {
	foreach($olddocuments as $data) {
		$doc_purpose	= $data['document_notes'];
		$doc_name		= $data['document_file_name'];
		$doc_path		= SYSTEMPATH."uploads/documents/".$data['document_file_name'];
		if( $doc_name ) {
			$documents[$uid][]	= array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);
		}
	}
	$_SESSION['documents']	= $documents;
}
$respondingdetails		= getRPDetails($responding);
$responding_name		= $respondingdetails['client_name'];
$responding_email		= $respondingdetails['client_email'];
$responding_type		= $respondingdetails['client_type'];
$responding_role		= $respondingdetails['client_role'];

if( !$verification_signed_by ) {
	$verification_signed_by	= $responding_name;
}
/****************************************************
Function for getting responding or proponding details
****************************************************/
function getRPDetails($rp_id) {
	global $AdminDAO;
	$clients = $AdminDAO->getrows("clients","*","id = :id",array(":id"=>$rp_id));
	return $clients[0];
}
?>

            <div class="text-center m-b-md">
                <h3><?= $discovery_name ?></h3>
            </div>
			<table class="table table-bordered table-hover table-striped">
				<tr>
					<th>Case</th>
					<td><?= $case_title ?></td>
					<th>Number</th>
					<td><?= $case_number ?></td>
				</tr>
				<tr>
					<th>County</th>
					<td><?= $county_name ?></td>
					<th>State</th>
					<td><?= $jurisdiction ?></td>
				</tr>
			</table>

			<form name="discoverydetailsform" action="#" method="post" id="discoverydetailsform">
			<input type="hidden" name="case_id" value="<?= $case_id ?>">
			<input type="hidden" name="form_id" value="<?= $form_id ?>">
			<input type="hidden" name="response_id" value="<?= $response_id ?>">
			<input type="hidden" name="response_name" value="<?= $discovery_name ?>">
			<input type="hidden" name="uid" value="<?= $uid ?>">
			<input type="hidden" name="discovery_verification_by_name" id="discovery_verification_by_name" value="<?= @$discovery_verification_by_name ?>">
			<input type="hidden" name="discovery_verification" id="discovery_verification" value="<?= @$discovery_verification ?>">
			<input type="hidden" name="discovery_verification_state" id="discovery_verification_state" value='<?= @$verification_state ?>'>
			<input type="hidden" name="discovery_verification_city" id="discovery_verification_city" value='<?= @$verification_city?>'>
			<input type="hidden" name="discovery_verification_signed_by" id="discovery_verification_signed_by" value='<?= @$verification_signed_by?>'>

			<input type="hidden" name="discovery_sender_note" id="discovery_sender_note">
			<input type="hidden" name="email_solicitation" id="email_solicitation">
			<input type="hidden" name="email_body" id="email_body">
			<hr>
			<div class="row">
				<div id="loadinstructions"></div>
				<div class="col-md-12">
					<ul class="list-group">
<?php
										if( in_array( $form_id, array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE) ) ) {
											foreach($mainQuestions as $data) {
												$dependent_answer		= "";
												$question_id 			= $data['question_id'];
												$question_type_id 		= $data['question_type_id'];
												$have_main_question	 	= $data['have_main_question'];
												$p_q_type_id 			= $data['question_type_id'];
												$question_title 		= $data['question_title'];
												$question_number 		= $data['question_number'];
												$sub_part 				= $data['sub_part'];
												$p_sub_part 			= $data['sub_part'];
												$is_pre_defined 		= $data['is_pre_defined'];
												$discovery_question_id	= $data['discovery_question_id'];
												$is_depended_parent		= $data['is_depended_parent'];
												$depends_on_question	= $data['depends_on_question'];
												$has_extra_text			= $data['has_extra_text'];
												$extra_text				= $data['extra_text'];
												$extra_text_field_label	= $data['extra_text_field_label'];
												if( $response_id ) {
													$getAnswers = $AdminDAO->getrows("response_questions","*",
																						"fkresponse_id				= :fkresponse_id AND
																						fkdiscovery_question_id 	= 	:discovery_question_id",
																						array(	"discovery_question_id"	=>	$discovery_question_id,
																								"fkresponse_id"			=>	$response_id));
													$answer 				= $getAnswers[0]['answer'];
													$answer_time 			= $getAnswers[0]['answer_time'];
												}
												else {
													$answer 				= "";
													$answer_time 			= "";
												}
												/**
												* IF depends on some question then we need that question answer
												**/
												if( $depends_on_question && $response_id ) {
													$dependent_answer = getAnswerOfDependentParentQuestion( $discovery_id, $depends_on_question, $response_id );
												}
?>
												<li class='list-group-item <?= 
														!$depends_on_question 
															? "'" 
															: "row_<?= $depends_on_question ?>' " .( ( $dependent_answer == 'No' || $dependent_answer == '' ) ? "style='display:none;'" : '' ) 
													?>>
<?php
												if( $question_type_id != 1 ) {
														$subQuestions	= $AdminDAO->getrows( 'discovery_questions dq, questions q',

																					'dq.id as discovery_question_id,
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
																					q.sub_part 			!=   '' 
																					GROUP BY question_id 
																					ORDER BY display_order ",

																					array( ":discovery_id" => $discovery_id ) 
																			);
												}
?>
												<div class="form-group">
													<p>
														<b>Q No. <?= $question_number ?> <?= !$have_main_question ? "&nbsp;($sub_part)" : "" ?>: </b>
<?php
															echo $question_title;
															if( $has_extra_text ) {
																echo "<p><b>$extra_text_field_label: </b>$extra_text</p>";
															}
															if( ($question_number == "17.1" || $question_number == "217.1") ) {
																foreach( $subQuestions as $data ) {
																	echo  ". (".$data['sub_part'].") ". $data['question_title'];
																}
															}
?>
													</p>
<?php
															if( ($question_number == "17.1" || $question_number == "217.1") ) {
															}
															else if( $view != 1 ) {
																if( $question_type_id == 1 ) {
?>
																	<input type="hidden" name="have_main_question[<?= $discovery_question_id; ?>]" value="<?= $have_main_question?>"/>
																	<textarea id="answer<?= $discovery_question_id ?>" class="form-control" name="answer[<?= $discovery_question_id; ?>]" placeholder="Your Answer" required <?= $css ?>><?= 
																		htmlentities($answer) 
																	?></textarea>
<?php
																}
																else if( $question_type_id == 2 ) {
																	$question_no_makeid	= str_replace('.','_',$question_number);
?>
																		<div class="form-check form-check-inline">
																			<label class="radio-inline"><input type="radio" name="answer[<?= $discovery_question_id ?>]" value="Yes" onClick="checkFunction('<?= $question_no_makeid ?>','1')<?php if($is_depended_parent ==1 ){ ?>,showhidequestions('<?= $question_id ?> ',1)<?php }?>" <?php if($answer == 'Yes'){echo "checked";} ?> <?= $css ?>>Yes</label>
																			<label class="radio-inline"><input type="radio" name="answer[<?= $discovery_question_id ?>]" value="No" onClick="checkFunction('<?= $question_no_makeid ?>','2')<?php if($is_depended_parent ==1 ){ ?>,showhidequestions('<?= $question_id ?> ',2)<?php }?>" <?php if($answer == 'No'){echo "checked";} ?> <?= $css ?>>No</label>
																		</div>
<?php
																}
																if( $question_type_id != 1 ) {
?>
																	<ul class="list-group" id="subdiv<?= $question_no_makeid ?>" <?= ( $question_type_id == 2 && $answer != "Yes" ) ? 'style="display:none"' : '' ?>
<?php
																		foreach( $subQuestions as $data ) {
																			$question_id 			= $data['question_id'];
																			$question_type_id 		= $data['question_type_id'];
																			$form_id 				= $data['form_id'];
																			$question_title 		= $data['question_title'];
																			$question_number 		= $data['question_number'];
																			$sub_part 				= $data['sub_part'];
																			$is_pre_defined 		= $data['is_pre_defined'];
																			$discovery_question_id	= $data['discovery_question_id'];
																			$have_main_question		= $data['have_main_question'];
																			if( $response_id ) {
																				$getAnswers			= $AdminDAO->getrows( "response_questions", "*",
																												"fkresponse_id			= :fkresponse_id AND
																												fkdiscovery_question_id = :discovery_question_id",
																												array(	"discovery_question_id"	=> $discovery_question_id,
																														"fkresponse_id"			=> $response_id ) 
																											);
																				$answer1 			= $getAnswers[0]['answer'];
																				$answer_time		= $getAnswers[0]['answer_time'];
																			}
																			else {
																				$answer1 				= "";
																				$answer_time 			= "";
																			}
?>
																		<li class="list-group-item">
																			<div class="form-group">
																				<p>
																					<b><?= $sub_part ?>) </b>
																					<?= $question_title ?>
																				</p>
																				<input type="hidden" class="subanswer_<?= $question_no_makeid?>" name="have_main_question[<?= $discovery_question_id; ?>]" value="<?= $have_main_question?>" <?php if($answer == "No" || ($answer == "" && $p_q_type_id == 1) || ($answer == "" && $p_q_type_id == 2)){ ?> disabled <?php } ?>/>
																				<textarea id="answer<?= $discovery_question_id ?>"
																						class="form-control subanswer_<?= $question_no_makeid?>"
																						name="answer[<?= $discovery_question_id; ?>]"
																						placeholder="Your Answer" required
																						<?= $css ?>
																						<?= (($answer == "No" || $answer == "") && $p_q_type_id != 3) ? "disabled" : "" ?>><?= 
																					htmlentities($answer1) 
																				?></textarea>
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
										else if( $form_id == Discovery::FORM_CA_RFAS ) {
											foreach( $mainQuestions as $data ) {
?>
												<li class="list-group-item">
<?php
													$question_id 			= $data['question_id'];
													$question_type_id 		= $data['question_type_id'];
													$question_title 		= $data['question_title'];
													$question_number 		= $data['question_number'];
													$sub_part 				= $data['sub_part'];
													$is_pre_defined 		= $data['is_pre_defined'];
													$discovery_question_id	= $data['discovery_question_id'];
													if($response_id > 0) {
														$getAnswers				= $AdminDAO->getrows("response_questions","*",
																						"fkresponse_id				= :fkresponse_id AND
																						fkdiscovery_question_id 	= 	:discovery_question_id",
																						array(	"discovery_question_id"	=>	$discovery_question_id,
																								"fkresponse_id"			=>	$response_id));
														$answer 				= $getAnswers[0]['answer'];
														$answer_time 			= $getAnswers[0]['answer_time'];
														$answer_detail 			= $getAnswers[0]['answer_detail'];
													}
													else
													{
														$answer 				= "";
														$answer_time 			= "";
														$answer_detail 			= "";
													}
													?>
													<div class="form-group">
														<p>
															<b>Q No. <?= $question_number ?>: </b>
															<?= $question_title; ?>
														</p>
<?php
													if( $view != 1 ) {
?>
                                                        <div class="form-check form-check-inline">
                                                            <label class="radio-inline"><input type="radio" name="answer[<?= $discovery_question_id ?>]" value="Admit" onClick="checkFunction('<?= $discovery_question_id ?>','2')" <?php if($answer == 'Admit'){echo "checked";} ?> <?= $css ?>>Admit</label>
                                                            <label class="radio-inline"><input type="radio" name="answer[<?= $discovery_question_id ?>]" value="Deny" onClick="checkFunction('<?= $discovery_question_id ?>','1')" <?php if($answer == 'Deny'){echo "checked";} ?> <?= $css ?>>Deny</label>
                                                        </div>
                                                    	<ul class="list-group" id="subdiv<?= $discovery_question_id ?>" <?php if($answer == "Admit" || $answer == ""){ ?>style="display:none" <?php } ?>>
<?php
														foreach($generalQuestions as $generalQuestion) {
															$question_admit_id	= $generalQuestion['id'];
															$subQuestionAnswers	= $AdminDAO->getrows('question_admit_results',"*",":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id AND fkresponse_id = :fkresponse_id",array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id,"fkresponse_id" => $response_id));
															$subQuestionAnswer	= $subQuestionAnswers[0];

															if($question_admit_id == 1) {
																$sub_answer_show	= $question_number;
															}
															else {
																$sub_answer_show	= htmlentities($subQuestionAnswer['sub_answer']);
															}
?>
                                                        <li class="list-group-item">
                                                        <div class="form-group">
                                                            <p>
                                                                <b><?= $generalQuestion['question_no'] ?>) </b>
                                                                <?= $generalQuestion['question'] ?>
                                                            </p>
                                                            <textarea <?php if($answer == "Admit" || $answer == ""){ ?> disabled <?php } ?> id="subanswer<?= $discovery_question_id.'_'.$question_admit_id ?>" class="form-control subanswer_<?= $discovery_question_id ?> " name="subanswer[<?= $discovery_question_id ?>][<?= $question_admit_id; ?>]" placeholder="Your Answer" required <?= $css ?>><?= 
																$sub_answer_show
															?></textarea>
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
										else if( in_array( $form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RPDS)) ) {
											foreach( $mainQuestions as $data ) {
?>
												<li class="list-group-item">
<?php
													$question_id 		= $data['question_id'];
													$question_type_id 	= $data['question_type_id'];
													$question_title 	= $data['question_title'];
													$question_number 	= $data['question_number'];
													$sub_part 			= $data['sub_part'];
													$is_pre_defined 	= $data['is_pre_defined'];
													$discovery_question_id	= $data['discovery_question_id'];
													if( $response_id > 0 ) {
														$getAnswers				= $AdminDAO->getrows("response_questions","*",
																							"fkresponse_id				= :fkresponse_id AND
																							fkdiscovery_question_id 	= 	:discovery_question_id",
																							array(	"discovery_question_id"	=>	$discovery_question_id,
																									"fkresponse_id"			=>	$response_id));
														$answer 				= $getAnswers[0]['answer'];
														$answer_time 			= $getAnswers[0]['answer_time'];
														$answer_detail 			= $getAnswers[0]['answer_detail'];
													}
													else {
														$answer 				= "";
														$answer_time 			= "";
														$answer_detail 			= "";
													}
?>
													<div class="form-group">
														<p>
															<b>Q No. <?= $question_number ?>: </b>
															<?= $question_title; ?>
														</p>
<?php
														if( !$view ) {
															if( $form_id == Discovery::FORM_CA_RPDS ) {
?>
                                                                <select class="form-control" id="answer<?= $discovery_question_id; ?>"  name="answer[<?= $discovery_question_id; ?>]" onChange="checkFunctionForm5('<?= $discovery_question_id ?>',this.value)" <?= $css ?>>
                                                                <option <?php if($answer == "Select Your Response") echo "selected"; ?>>Select Your Response</option>
                                                                <option <?php if($answer == "I have responsive documents") echo "selected"; ?>>I have responsive documents</option>
                                                                <option <?php if($answer == "Responsive documents have never existed") echo "selected"; ?>>Responsive documents have never existed</option>
                                                                <option <?php if($answer == "Responsive documents were destroyed") echo "selected"; ?>>Responsive documents were destroyed</option>
                                                                <option <?php if($answer == "Responsive documents were lost, misplaced, stolen, or I lack access to them") echo "selected"; ?>>Responsive documents were lost, misplaced, stolen, or I lack access to them</option>
                                                                </select>
<?php
															}
															else if( $form_id == Discovery::FORM_CA_SROGS ) {
?>
																<textarea id="answer<?= $discovery_question_id ?>" class="form-control " name="answer[<?= $discovery_question_id; ?>]" placeholder="Your Answer" required <?= $css ?>><?= 
																	htmlentities($answer) 
																?></textarea>
<?php
															}
															if( $form_id == Discovery::FORM_CA_RPDS ) {
?>
																<ul class="list-group" id="note<?= $discovery_question_id ?>" <?php if($answer != "I have responsive documents"){ ?>style="display:none" <?php } ?>>
																	<li class="list-group-item">
																	<div class="form-group">
																		<p>
																			<b>Note: </b>
																			Upload your documents below.
																		</p>
																	</div>
																	</li>
																</ul>
																<ul class="list-group" id="subdiv<?= $discovery_question_id ?>" <?php if($answer == 'Select Your Response' || $answer == "I have responsive documents"){ ?>style="display:none" <?php } ?>>
																	<li class="list-group-item">
																	<div class="form-group">
																		<p>
																			<b>a) </b>
																			Enter the name and address of anyone you believes has the documents.
																		</p>
																		<textarea <?= ($answer == 'Select Your Response' || $answer == "I have responsive documents") ? "disabled" : '' ?> 
																				id="subanswer<?= $discovery_question_id ?>" 
																				class="form-control" 
																				name="subanswer[<?= $discovery_question_id; ?>]" 
																				placeholder="Your Answer" 
																				required <?= $css ?>><?= 
																			htmlentities($answer_detail) 
																		?></textarea>
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
							if( in_array( $form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS) ) 
								&& !$view
								&& !empty($_SESSION['documents'][$uid]) ) {
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
							if( in_array( $form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RPDS) ) && !$view ) {
?>
                            <div class="col-md-12">
                            	<hr>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="">
                                        	<p><b>Upload your documents here.</b></p>
                                        </div>
                                		<div id="extraupload"></div>
                                        <button type="button" class="btn btn-info" id="extrabutton">
                                            <i class="icon-ok bigger-110"></i>
                                            <span class="ladda-label">Upload</span><span class="ladda-spinner"></span>
                                        </button>
                                        <div id="uploadeddocs"></div> <!-- documents are uploaded here -->
                                    </li>
                                </ul>
                            </div>
<?php
							}
?>
                        </div>

<?php
                      if( $view != 1 ) {
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
    <aside class="sidebar right"><div class="fixed"></div></aside>
</div>

<link href="<?= VENDOR_URL ?>uploadfile.css" rel="stylesheet">
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>

<script>

function loadinstructions( form_id, id ) {
	var type = '<?= $type ?>';
	$.get(`discoveryloadforminstruction.php?form_id=${form_id}&id=${id}&case_id=<?= $case_id ?>&viewonly=1&type=${type}`)
		.done( resp => {
			$("#loadinstructions").html( trim(resp) );

			const { discoveryType, discoveryFormNames, discoveryForm, } = globalThis,
					suffix = (discoveryForm ? '@' + discoveryFormNames[discoveryForm-1] : '');
			ctxUpdate({ id: `47_${discoveryType}${suffix}`, pkscreenid: '47', url: 'discoveryfront.php', } );

			CKEDITOR.replace( 'instruction' );
		});
}
function submitForm() {
	callModal();
}

function callModal() {
	$('#myModal').modal('toggle');
}
function checkFunction( subdivid, option ) {
	if(option == 1)	{
		$("#subdiv"+subdivid).show();
		$(".subanswer_"+subdivid).prop('disabled',false);
	}
	else if(option == 2) {
		$("#subdiv"+subdivid).hide();
		$(".subanswer_"+subdivid).prop('disabled',true);
	}
}
function checkFunctionForm5(subdivid, option) {
	option = trim(option);
	if( option == 'I have responsive documents' || option == 'Select Your Response') {
		if(option == 'I have responsive documents') {
			$("#note"+subdivid).show();
		}
		else {
			$("#note"+subdivid).hide();
		}
		$("#subdiv"+subdivid).hide();
		$("#subanswer"+subdivid).prop('disabled',true);
	}
	else {
		$("#subdiv"+subdivid).show();
		$("#subanswer"+subdivid).prop('disabled',false);
		$("#note"+subdivid).hide();
	}
}
$(document).ready( _ => {

	loadinstructions('<?= $form_id ?>','<?= $discovery_id ?>');
	loaduploadeddocs();
<?php
	if( in_array($form_id,array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS)) ) {
?>
		loaduploaddiscoverydocs();
<?php
	}
?>
	var extraObj = $("#extraupload")
						.uploadFile( {
								url:"frontdocumentuploads.php",
								fileName: "myfile",
								extraHTML: _ => {
										var html = "<div><input type='hidden' name='rp_uid' value='<?= $uid ?>' /> <br/>";
										html += "</div>";
										return html;
								},
								autoSubmit: false,
								afterUploadAll: obj => {
									$(".ajax-file-upload-container").html("");
									loaduploadeddocs();
								}
						} );
	$("#extrabutton").click( _ => {
		extraObj.startUpload();
	});
});
function loaduploadeddocs() {
	var rp_uid	= '<?= $uid; ?>';
	$("#uploadeddocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=1");
}
function loaduploaddiscoverydocs() {
	var rp_uid	= '<?= $uid; ?>';
	$("#uploaddiscoverydocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=0");
}

function deleteDoc(id,rp_uid) {
	$.post( "deletefrontdocs.php", { id: id,rp_uid:rp_uid })
		.done( data => {
			loaduploadeddocs();
		} );
}
function SaveVerificationText(flag) {
	var checkerror	= 0;
	if( flag == 1 ) {
		$("#discovery_verification").val(1);
		$("#msgVerification").html("");
		$("#discovery_verification_by_name").val($("#verification_by_name").val());
		$("#discovery_verification_state").val($("#verification_state").val());
		$("#discovery_verification_city").val($("#verification_city").val());
		$("#discovery_verification_signed_by").val($("#verification_signed_by").val());

		$("#email_solicitation").val($("#email_solicitation_popup").val());
		$("#email_body").val($("#email_body_popup").val());

		if( !$("#verification_city").val().trim() ) {
			$("#msgVerification").html("Please enter your city.");
			var checkerror	= 1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');
		}
		else if( !$("#verification_by_name").val().trim() ) {
			$("#msgVerification").html("Please enter your type.");
			var checkerror	= 1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');
		}
		else if( !$("#verification_signed_by").val().trim() ) {
			$("#msgVerification").html("Please enter your name.");
			var checkerror	= 1;
			//callModal();
			//addform('discoveryfrontaction.php?q=1','discoverydetailsform',' ','discoveryfront-thanks.php');
		}
		else {
			callModal();
			addform('discoveryfrontaction.php?q=1&redirect=1','discoverydetailsform','','discoveryfront-thanks.php');
		}
	}
	else {
		callModal();
		addform('discoveryfrontaction.php?q=1&redirect=1','discoverydetailsform','','discoveryfront-thanks.php');
	}
}
function showhidequestions( parentid,yesorno ) {
	if( yesorno == 1 ) {
		$(".row_"+parentid).show();
	}
	else {
		$(".row_"+parentid).hide();
	}
}
</script>

<?php
//Email body and solutation setup
//Email Salutation
$emaildata				= $AdminDAO->getrows("email_log","email_salutation","sender_type = 2 AND receiver_type = 1 ORDER BY id DESC LIMIT 1",array());
$email_solicitation		= $emaildata[0]['email_salutation'];
if( !$email_solicitation ) {
	$email_solicitation	= "Hi,";
}

ob_start();
?>
	<h4><?= $discovery_name ?> is submitted successfully from the client.</h4>
	<ul style="padding-left:5px">
		<li><b>Case Title:</b> <?= $case_title ?></li>
		<li><b>Case#:</b> <?= $case_number ?></li>
		<li><b>State:</b> <?= $jurisdiction ?></li>
		<li><b>County/District:</b> <?= $county_name ?></li>
		<li><b>Court Address:</b> <?= $court_address ?></li>
		<li><b>Attorney:</b> <?= $attorney_name ?></li>
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
		$statesArray	= $AdminDAO->getrows("system_state","*","fkcountryid = '254'");
		if( !$verification_by_name ) {
			$verification_by_name = $responding_role;
		}
?>
        <div id="verification_text" style="line-height:23px !important; text-align:justify; font-size:13px">
            <p>I am the <input type='text' name='verification_by_name' id='verification_by_name' value='<?= $verification_by_name ?>' placeholder='Role: Plaintiff, Defendant, etc.' required pattern="[A-Za-z]+" /> in this action, and I have read the foregoing <b><?= Discovery::getTitle($discovery_name) ?></b> and know its contents. The matters stated therein are true based on my own knowledge, except as to those matters stated on information and belief, and as to those matters I believe them to be true.
            </p>
			<p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct.<br>
			Executed on <?= date("F j, Y") ?> at <input placeholder="City" type='text' name='verification_city' id='verification_city' value='<?= $verification_city; ?>' required pattern="[A-Za-z]+" />,
			<input type='text' name='verification_state' id='verification_state' value='<?= $verification_state; ?>' placeholder="State" required pattern="[A-Za-z]+" />.
            </p>
            <p>
						<?php if( in_array( $form_id, [Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS]) ): ?>
              <img src="<?= ASSETS_URL; ?>images/court.png" style="width: 18px;padding-right: 3px;">
						<?php endif; ?>
<?php
                    // added by JS 3/7/20
                    if(in_array($form_id, [Discovery::FORM_CA_SROGS, Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE])) {
                        echo "Code Civ.Proc., &sect; 2030.250  ";
                        echo instruction(16);
                    }
                    else if($form_id == Discovery::FORM_CA_RFAS){
                        echo "Code Civ.Proc., &sect; 2033.240 ";
                        echo instruction(19);
										}
										else if($form_id == Discovery::FORM_CA_RPDS ){
											echo "Code Civ.Proc., &sect; 2031.250, subd. (a)";
											echo instruction(20);
										}
?>
            </p>
        </div>
        <br>
        <br>
        <p class="text-right">
        	By: <input placeholder="Signed By" type='text' name='verification_signed_by' id='verification_signed_by' value='<?= $verification_signed_by ?>' required pattern="[A-Za-z]+" /><br>

			Signed electronically,<br>

			<img src="<?= ASSETS_URL ?>images/court.png" style="width: 18px;padding-right: 3px;">Cal. Rules of Court, rule 2.257
		</p><br>
		<br>

		<div class="form-group" style="display:none">
			<p> <h3>Email Template</h3></p>
			<div class="form-group">
				<label for="email_solicitation" class="col-form-label">Email Salutation:</label>
				<input type="text" name="email_solicitation_popup" id="email_solicitation_popup" placeholder="Add Salutation"  class="form-control m-b" value="<?= $email_solicitation; ?>">
			</div>
			<div class="form-group">
				<label for="email_body" class="col-form-label">Email Body:</label>
				<textarea  rows="10" name="email_body_popup" id="email_body_popup" placeholder="Add email body"  class="form-control m-b"><?=
					$email_body
				?></textarea>
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

<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
	var _smartsupp = _smartsupp || {};
	_smartsupp.key = 'ae242385584ca4d3fd78d74a04dbd806ef3957e0';
	window.smartsupp||(function(d) {
		var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
		s=d.getElementsByTagName('script')[0];c=d.createElement('script');
		c.type='text/javascript';c.charset='utf-8';c.async=true;
		c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
	})(document);
</script>

</body>
</html>

<script>
globalThis['discoveryType'] = "<?= $type ?>";

jQuery( $ => { 
	CKEDITOR.replace( 'email_body_popup' );
} );
</script>
