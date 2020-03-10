<?php
@session_start();
require_once("adminsecurity.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$uid				=	@$_GET['id'];
$view				=	$_GET['view'];
$downloadORwrite	=	@$_GET['downloadORwrite'];
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
$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
											'c.case_title 	as case_title,
											c.plaintiff,
											c.defendant,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.court_address as court_address,
											c.department 	as department, 
											d.case_id 		as case_id,
											d.is_opened,
											d.is_submitted,
											d.id 			as discovery_id, 
											d.uid,
											d.submit_date,
											d.send_date,
											d.discovery_verification_text,
											d.discovery_verification,
											d.verification_state,
											d.verification_city,
											d.propounding,
											d.responding,
											d.served,
											d.discovery_name,
											d.propounding_uid,
											d.responding_uid,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											d.discovery_introduction as introduction,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.lastname 		as atorny_lname,
											a.middlename 	as atorny_mname,
											a.address		as atorny_address,
											a.email			as atorny_email,
											a.cityname,
											a.street,
											a.zip,
											a.phone,
											a.fkstateid,
											a.companyname	as atorny_firm,
											d.attorney_id	as attorney_id,
											a.email,
											a.attorney_info,
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
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":uid"=>$uid)
										);


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
$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_mname']." ".$discovery_data['atorny_lname'];
$atorny_email		=	$discovery_data['atorny_email'];
$atorny_address		=	$discovery_data['atorny_address'];
$atorny_city		=	$discovery_data['cityname'];
$atorny_zip			=	$discovery_data['zip'];
$atorny_street		=	$discovery_data['street'];
$atorny_phone		=	$discovery_data['phone'];
$fkstateid			=	$discovery_data['fkstateid'];
$atorny_firm		=	$discovery_data['atorny_firm'];
$form_name			=	$discovery_data['form_name']." [SET ".$set_number."]";
$short_form_name	=	$discovery_data['short_form_name'];
$submit_date		=	$discovery_data['submit_date'];
$send_date			=	$discovery_data['send_date'];
$instructions		=	$discovery_data['instructions'];
$introduction		=	$discovery_data['introduction'];
$propounding_uid	=	$discovery_data['propounding_uid'];
$responding_uid		=	$discovery_data['responding_uid'];
$propounding		=	$discovery_data['propounding'];
$responding			=	$discovery_data['responding'];
$served				=	$discovery_data['served'];
$served_date		=	date("F d, Y",strtotime($served));
$attorney_info		=	$discovery_data['attorney_info'];
$is_verified		=	$discovery_data['discovery_verification'];
$verification_text	=	$discovery_data['discovery_verification_text'];
$verification_state	=	$discovery_data['verification_state'];
$verification_city	=	$discovery_data['verification_city'];
$discovery_name		=	$discovery_data['discovery_name'];
if($view == 1)
{
	$form_name = strtoupper("REQUEST TO ".$discovery_name);
}
else
{
	$form_name = strtoupper("RESPONSE TO ".$discovery_name);
	
}
$form_name 			= 	$form_name." [SET ".$set_number."]";
$getState			=	$AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
$atorny_state		=	$getState[0]['statename'];
$atorny_state_short	=	$getState[0]['statecode'];

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

if($responding_type == 'Others')
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
	Query For Forms 1,2,5 Questions 
****************************************/

$mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
										'dq.answer			as 	answer,
										dq.answer_detail	as 	answer_detail,
										dq.answered_at		as 	answer_time,
										dq.id 				as 	discovery_question_id,
										dq.objection		as  objection,
										q.id 				as 	question_id,
										q.question_type_id 	as 	question_type_id,
										q.question_title 	as 	question_title,
										q.question_number 	as 	question_number,
										q.sub_part 			as 	sub_part,
										q.is_anserable 		as is_anserable,
										q.is_pre_defined 	as 	is_pre_defined,
										have_main_question',
				
										"
										q.id 				= 	dq.question_id  AND
										dq.discovery_id 	= 	'$discovery_id' AND   
										(
											q.sub_part 		= 	'' OR 
											q.sub_part IS NULL OR 
											have_main_question	IN (0,2)
											
										)
										GROUP BY q.id
										ORDER BY display_order
										"
									  );
$generalQuestions	=	$AdminDAO->getrows('question_admits',"*");
/************************************************
	Discovery Conjuction with some RFA or not
************************************************/
//$AdminDAO->displayquery=1;
$isConWithDiscovery	=	$AdminDAO->getrows('discoveries',"id","conjunction_with = '$discovery_id'");
if(sizeof($isConWithDiscovery) > 0)
{
	$isconwithdiscoveryid	=	$isConWithDiscovery[0]['id'];	
}
else
{
	$isconwithdiscoveryid	=	0;
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
<p class="break-page"></p>
<table class="wikitable1 tabela1">
    <tbody>
        <?php
        if(in_array($form_id,array(1,2)))
        {
            foreach($mainQuestions as $data)
            {
                $answer 			=	$data['answer'];
                $answer_time 		=	$data['answer_time'];
                $question_id 		=	$data['question_id'];
                $question_type_id 	=	$data['question_type_id'];
				$objection 			=	$data['objection'];
                $question_title 	=	$data['question_title'];
                $question_number 	=	$data['question_number'];
                $sub_part 			=	$data['sub_part'];
                $is_pre_defined 	=	$data['is_pre_defined'];
                $discovery_question_id	=	$data['discovery_question_id'];
                if($question_type_id != 1)
                {
                    $subQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
		
																							'dq.answer as answer,
																							dq.answered_at as answer_time,
																							dq.id as discovery_question_id,
																							dq.objection		as  objection,
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
																				q.sub_part 		   !=   ''  GROUP BY question_id ORDER BY display_order",
																				array(":question_number"=>$question_number,":discovery_id"=>$discovery_id));
                   
                }
                ?>
                <tr>
                    <td colspan="2">
                        <h3>INTERROGATORY NO. <?php echo $question_number ?>:</h3>
                        <?php
						if($question_type_id != 3 || $view == 1)
						{
						?>
                        <b><u>Interrogatory</u></b>
                        <p><?php echo $question_title; ?></p>
                        <b><u>Objection</u></b>
                        <p><?php echo $objection; ?></p>
                        <?php
						}
                        if($view != 1)
						{
							if($question_type_id == 1 || ($question_type_id == 3 && sizeof($subQuestions) == 0) )
							{
								?>
									<b><u>Response</u></b>
									<p><?php echo $answer; ?></p>   
								<?php
							}
							else if($question_type_id == 2 )
							{
								?>
									<b><u>Response</u></b>
									<p>
										<?php
										if(strtolower($answer) == 'yes'){echo "Yes";} 
										elseif(strtolower($answer) == 'no'){echo "No";}
										else{echo "No Answer";}
										?>
									</p>
								<?php   
							}
							if($question_type_id != 1)
							{
								if(in_array($question_number,array('17.1','217.1')) && $isconwithdiscoveryid != 0)
								{
									$con_mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
																							'dq.answer			as 	answer,
																							dq.answer_detail	as 	answer_detail,
																							dq.answered_at		as 	answer_time,
																							dq.id 				as 	discovery_question_id,
																							dq.objection		as  objection,
																							q.id 				as 	question_id,
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
																							dq.answer			=	'Deny' 				AND
																							dq.discovery_id = '$isconwithdiscoveryid' 	
																							ORDER BY q.question_number
																							"
																						  );
									if(sizeof($con_mainQuestions) > 0)
									{
										foreach($con_mainQuestions as $con_question)
										{
											$con_discovery_question_id	=	$con_question['discovery_question_id'];
											$con_SubQuestions	=	$AdminDAO->getrows('question_admits qa,question_admit_results qar',
																	'*',
																	"
																	qar.discovery_question_id 		= 	'$con_discovery_question_id'  	AND
																	qar.question_admit_id			=	qa.id "
																  );
											?>
                                                <h4><u>Q No. <?php echo $con_question['question_number'];  ?> of conjunction RFA:</u></h4>
                                                
                                                <?php
												foreach($con_SubQuestions as $con_SubQuestion)
												{
												?>
                                                
                                                	<b><u>Interrogatory</u></b>
                                                    <p>(<?php echo $con_SubQuestion['question_no'].") ".$con_SubQuestion['question']; ?></p>
                                                    <b><u>Response</u></b>
                                                    <p><?php echo $con_SubQuestion['sub_answer']; ?></p>
                                                
                                                <?php
												}
												?>
                                                <br />
											<?php
										}
									}
									else
									{
										?>
                                        <b><u>Response</u></b>
                                        <p>N/A</p>
                                        <?php
										
									}
								}
								else
								{
									if(strtolower($answer) == 'yes' || ($question_type_id == 3 && sizeof($subQuestions) > 0))
									{
										foreach($subQuestions as $data)
										{
											$answer 				=	$data['answer'];
											$answer_time 			=	$data['answer_time'];
											$question_id 			=	$data['question_id'];
											$question_type_id 		=	$data['question_type_id'];
											$form_id 				=	$data['form_id'];
											$question_title 		=	$data['question_title'];
											$question_number 		=	$data['question_number'];
											$sub_part 				=	$data['sub_part'];
											$objection 				=	$data['objection'];
											$is_pre_defined 		=	$data['is_pre_defined'];
											$discovery_question_id	=	$data['discovery_question_id'];
											?>
											<b><u>Interrogatory</u></b>
											<p><?php echo "(".$sub_part.") ".$question_title ?></p>
											<b><u>Response</u></b>
											<p><?php if($answer == ""){echo "N/A";}else{echo $answer;} ?></p>
                                            <b><u>Objection</u></b>
                        					<p><?php echo $objection; ?></p>
											<?php
										}
									}
								}
							}
						}
						?>
                        <br /><br />
                    </td>
                </tr>    
				<?php
            }
        }
        else if($form_id == 4)
		{
			foreach($mainQuestions as $data)
			{
				$answer 				=	$data['answer'];
				$answer_detail 			=	$data['answer_detail'];
				$answer_time 			=	$data['answer_time'];
				$question_id 			=	$data['question_id'];
				$question_type_id 		=	$data['question_type_id'];
				$question_title 		=	$data['question_title'];
				$objection 			=	$data['objection'];
				$question_number 		=	$data['question_number'];
				$sub_part 				=	$data['sub_part'];
				$is_pre_defined 		=	$data['is_pre_defined'];
				$discovery_question_id	=	$data['discovery_question_id'];
				?>
                 <tr>
                    <td colspan="2">
                    	<h3>REQUEST NO. <?php echo $question_number ?>:</h3>
                        <b><u>Request</u></b>
                        <p><?php echo $question_title; ?></p>
                        <?php
						if($view != 1)
						{ 
						?>
							<b><u>Response</u></b>
							<p><?php echo $answer;?></p> 
						<?php
						}
						?>
                        <b><u>Objection</u></b>
                        <p><?php echo $objection; ?></p>
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
                
                $answer 			=	$data['answer'];
                $answer_detail 		=	$data['answer_detail'];
                $answer_time 		=	$data['answer_time'];
                $question_id 		=	$data['question_id'];
                $question_type_id 	=	$data['question_type_id'];
                $question_title 	=	$data['question_title'];
				$objection 			=	$data['objection'];
                $question_number 	=	$data['question_number'];
                $sub_part 			=	$data['sub_part'];
                $is_pre_defined 	=	$data['is_pre_defined'];
                $discovery_question_id	=	$data['discovery_question_id'];
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
						?>
                        <?php
						if($form_id == 5)
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
						}
						?>
                        <p><?php echo $question_title; ?></p>
                        <b><u>Objection</u></b>
                        <p><?php echo $objection; ?></p>
                        <?php
                        if($view != 1)
                        { 
                        ?>
                            <b><u>Response</u></b>
                            <?php
                            if($form_id == 5)
                            { 	$reponse	=	'';
                                if($answer == "Select Your Response")
                                {
                                    echo "";
                                }
                                if($answer == "I have responsive documents") 
                                {
                                    echo "<p>Responsive documents are provided in Exhibit A.</p>";
                                }
                                $str1	=	"<p>A diligent search and a reasonable inquiry have been made in an effort to comply with this demand, however, responding party is unable to comply because they do not have any responsive documents in their possession, custody, or control.";
                                $str2	=	" However, respondent believes that ".$answer_detail." may have responsive documents.</p>";
                                if($answer == "Responsive documents have never existed") 
                                {
                                    echo $str1." Respondent does not believe that such documents have ever existed. ".$str2;
                                }
                                if($answer == "Responsive documents were destroyed") 
                                {
                                    echo $str1." Respondent does not believe that such documents have ever existed. ".$str2; 
                                }
                                if($answer == "Responsive documents were lost, misplaced, stolen, or I lack access to them") 
                                {
                                    echo $str1." Respondent believes that such documents were lost, misplace, stolen, or respondent lacks access to them. ".$str2;
                                }
                            }
                            else if($form_id == 3)
                            {
                                 echo "<p>".$answer."</p>";
                            }
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
        	<td width="70%"></td>
        	<td><br /><hr></td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?php echo strtoupper($atorny_name); ?><br>
                Attorney for <?php echo $att_for_client_role."<br>".$att_for_client_name ?> 
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
            <p>I am the <?php echo $responding_role ?> in this action, and I have read the foregoing <b><?php echo $form_name; ?></b> and know its contents. The matters stated therein are true based on my own knowledge, except as to those matters stated on information and belief, and as to those matters I believe them to be true.
            </p><p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct. Executed on <?php echo date("n/j/Y"); ?> at <?php echo $verification_city.", ".$verification_state; ?>. 
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right">
        	<br /><br />
            <?php echo strtoupper($responding_name); ?>
        </td>
    </tr>
  </tbody>
</table>
<?php
}
?>
<!-- =================================================== -->
<!-- 			PROOF OF SERVICE (POS) 						 -->
<!-- =================================================== -->
<?php
/*
<p class="break-page"></p>     
<table class="tabela1" style="border:none !important">
  <tbody>
    <tr>
    	<td colspan="2" align="center"><h3><u>PROOF OF SERVICE</u></h3></td>
    </tr>
    <tr>
    	<td  colspan="2" align="center"><?php echo  $case_title; ?><br />Case no: <?php echo $case_number ?></td>
    </tr>
    <tr>
    	<td  colspan="2" align="center"><?php echo  strtoupper("STATE OF CALIFORNIA, COUNTY OF ".$county_name); ?></td>
    </tr>
    <tr>
    	<td  colspan="2" align="justify">
        	<p>I am over the age of 18 years and not a party to the within action. My business address is <?php echo $atorny_address.", ".$atorny_city.", ".$atorny_state.", ".$atorny_zip; ?>.<br />
            On the date below, I served the foregoing <b><?php echo $form_name; ?></b> on all parties in this action via (<?php echo $setting_email; ?>).<br />
			<br />I declare under penalty of perjury under the laws of the State of California that the above is true and correct. Executed on <?php echo date("n/j/Y"); ?> at <?php echo $atorny_city; ?>, California.</p>
        </td>
    </tr>
    <!--<tr>
        <td width="70%"></td>
        <td><br /><br /><hr></td>
    </tr>-->
    <tr>
        <td colspan="2" align="right">
        	<br /><br />
            <?php echo strtoupper($atorny_name); ?>
        </td>
    </tr>	
    <tr>
    	<td  colspan="2" align="center"><h3><u>SERVICE LIST</u></h3></td>
    </tr>
    <tr>
    	<td  colspan="2">
        	<ol>
            <li><?php echo $atorny_name." (".$atorny_email.")"; ?></li>
			<?php
			foreach($allotherattornies as $data)
			{
				echo "<li>".$data['other_attorney_name']." (".$data['other_attorney_email'].")</li>";
			}
			?>
            </ol>
        </td>
    </tr>
  </tbody>
</table>
*/
?>
<?php
$html = ob_get_contents();

ob_clean();
//echo $html; exit;
//exit;
//$footertext	=	"|{PAGENO}<hr>$form_name|<br>© ".date('Y')." EasyRogs.com";
$footertext			=	'<table width="100%">
						<tr>
							<td width="5%" style="line-height:3px"></td>
							<td style="line-height:18px" align="center">{PAGENO}<hr>'.$form_name.'<br>© '.date('Y').' EasyRogs.com</td>
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
if($downloadORwrite == 1)
{
	$filePath	=	$_SESSION['system_path']."uploads/documents/{$case_title}-{$atorny_name}.pdf";
}
else
{
	$filePath	=	"{$case_title}-{$atorny_name}.pdf";
}
pdf($filePath,$headerFooterConfiguration,@$downloadORwrite);