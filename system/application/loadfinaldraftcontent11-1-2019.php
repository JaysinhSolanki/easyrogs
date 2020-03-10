<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");

$discovery_uid		=	$_POST['discovery_id'];

	
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
												c.uid 	as case_uid, 
												d.case_id 		as case_id,
												d.is_opened,
												d.is_submitted,
												d.id 			as discovery_id, 
												d.uid,
												d.submit_date,
												d.send_date,
												d.discovery_verification_text,
												d.discovery_verification,
												d.propounding,
												d.responding,
												d.served,
												d.discovery_name,
												d.propounding_uid,
												d.responding_uid,
												d.is_send,
												d.attorney_id as attr_id,
												d.form_id 		as form_id,
												d.set_number 	as set_number,
												d.type,
												d.discovery_introduction as introduction,
												f.form_name	 	as form_name,
												f.short_form_name as short_form_name,
												a.firstname 	as atorny_fname,
												a.lastname 		as atorny_lname,
												a.address		as atorny_address,
												a.cityname,
												a.street,
												a.companyname	as atorny_firm,
												d.attorney_id	as attorney_id,
												a.email,
												a.phone,
												a.attorney_info,
												(CASE WHEN (form_id = 1 OR form_id = 2) 
												 THEN
													  f.form_instructions 
												 ELSE
													  d.discovery_instrunctions 
												 END)
												 as instructions 
												',
												"d.uid 			= :id AND 
												d.case_id 		= c.id AND  
												d.form_id		= f.id AND
												d.attorney_id 	= a.pkaddressbookid",
												array(":id"=>$discovery_uid)
											);
	
//$AdminDAO->displayquery=0;
//exit;
//dump($discoveryDetails);
$discovery_data		=	$discoveryDetails[0];
$uid				=	$discovery_data['uid'];
$case_uid			=	$discovery_data['case_uid'];
$discovery_name		=	$discovery_data['discovery_name'];
$judge_name			=	$discovery_data['judge_name'];
$case_id			=	$discovery_data['case_id'];
$case_title			=	$discovery_data['case_title'];
$case_number		=	$discovery_data['case_number'];
$county_name		=	$discovery_data['county_name'];
$is_send			=	$discovery_data['is_send'];
$set_number			=	$discovery_data['set_number'];
$form_name			=	$discovery_data['form_name'];
$propounding		=	$discovery_data['propounding'];
$responding			=	$discovery_data['responding'];
$discovery_id		=	$discovery_data['discovery_id'];
$attr_id			=	$discovery_data['attr_id'];
$department			=	$discovery_data['department'];
$type				=	$discovery_data['type'];
$form_id			=	$discovery_data['form_id'];
$instructions		=	$discovery_data['instructions'];

//Responding Party
$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name		=	$respondingdetails[0]['client_name'];
$responding_email		=	$respondingdetails[0]['client_email'];
$responding_type		=	$respondingdetails[0]['client_type'];
$responding_role		=	$respondingdetails[0]['client_role'];

//Propondoing Party
$propondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$propounding));
$proponding_name		=	$propondingdetails[0]['client_name'];
$proponding_email		=	$propondingdetails[0]['client_email'];
$proponding_type		=	$propondingdetails[0]['client_type'];
$proponding_role		=	$propondingdetails[0]['client_role'];

//Sender Details
/*$senderDetails		=	$AdminDAO->getrows("system_addressbook,system_state","*","pkaddressbookid = :id AND fkstateid = pkstateid",array(":id"=>$attr_id));

$senderDetail		=	$senderDetails[0];
$senderEmail		=	$senderDetail['email'];
$senderPhone		=	$senderDetail['phone'];
$senderName			=	$senderDetail['firstname']." ".$senderDetail['lastname'];	
$senderAddress		=	makeaddress($attr_id);//$senderDetail['address'].", Suite: ".$senderDetail['street'].", ".$senderDetail['zip'].", ".$senderDetail['cityname'].", ".$senderDetail['statename'].", United States of America";

$servicelists	=	$AdminDAO->getrows("attorney a, case_team c","*", "c.fkcaseid = :case_id  AND a.id = c.attorney_id AND is_deleted = 0 ORDER BY a.attorney_name ASC", array(":case_id"=>$case_id));
*/


/***************************************
Query For Forms 1,2,3,4,5 Questions 
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
										q.is_pre_defined 	as 	is_pre_defined,
										is_depended_parent,
										depends_on_question, 
										have_main_question',
				
										"
										q.id 				= 	dq.question_id  AND
										dq.discovery_id = '$discovery_id' AND
										(
											q.sub_part 		= 	'' OR 
											q.sub_part IS NULL OR 
											have_main_question	IN (0,2)
											
										)
										GROUP BY q.id
										ORDER BY display_order, q.id 
										"
									  );

/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions	=	$AdminDAO->getrows('question_admits',"*");

/************************************************
	Discovery Conjuction with some RFA or not
************************************************/
//$isConWithDiscovery	=	$AdminDAO->getrows('discoveries',"id","conjunction_with = '$discovery_id'");
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
		$isconwithdiscoveryid	=	$isConWithDiscovery[0]['id'];	
	}
}
$respond = 1;

/*function finalResponseGenerate($objection,$answer)
{
	$transitiontext	=	"However, in the spirit of cooperation and without waiving any objection, respondent responds: ";
	
	if($objection == "" && $answer == "")
	{
		$finalResponse = "";
	}
	else
	{
		if($answer == "")
		{
			$finalResponse = $objection;
		}
		else if($objection == "")
		{
			$finalResponse = $answer;
		}
		else
		{
			$finalResponse = $objection.$transitiontext.$answer;
		}
	}
	echo htmlspecialchars($finalResponse);
}*/
//$instructions = "This responding party has not completed its investigation or discovery of the facts of this case and is not yet prepared for trial. The answers contained herein are based upon the information presently available, and specifically known, to this responding party and disclose only those contentions that presently occur to such party. It is anticipated that further discovery, independent investigation, legal research, and analysis will supply additional facts, modify known facts, and establish entirely new factual or legal contentions that may lead to substantial additions and modifications to the contentions set forth herein. The following responses are provided without prejudice to this responding party's right to produce evidence of any subsequently discovered facts as well as facts presently known that this responding may later recall. This responding party reserves the right to change any and all of the responses herein. Said responses are made in a good faith effort to supply as much factual information and legal contentions as are presently known to this responding party but should not be considered complete nor should they prejudice this responding party with respect to further discovery, research, analysis, and development of legal theories.";
?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-hover table-striped">
          <tbody>
             <tr>
              <th>Case Name</th>
              <td><?php echo $case_title; ?></td>
              <th>Case Number</th>
              <td><?php echo $case_number; ?></td>
            </tr>
             <tr>
                <th>County</th>
              <td><?php echo $county_name; ?></td>
              <th>Judge</th>
              <td><?php echo $judge_name; ?></td>
            </tr>
              <tr>
                <th>Department</th>
              <td><?php echo $department; ?></td>
              <th>Discovery</th>
              <td><?php echo $discovery_name; ?></td>
            </tr>                   
             <tr>
              <th>Propounding</th>
              <td><?php echo $proponding_name; ?></td>
              <th>Responding</th>
               <td><?php echo $responding_name; ?></td>
            </tr>
          </tbody>
        </table>
        <!-- ==================================================================== -->
    <h4><u>Instructions</u></h4>
    <?php
	if(in_array($form_id,array(3,4,5)))
	{
	?>
    <p> <?php echo html_entity_decode($instructions); ?></p>
	<?php
	}
	else
	{
		if($form_id == 1)
		{
			$checkedimg			=	'<img src="../uploads/icons/checkbox_checked_small.png" width="15px">';
			$uncheckedimg		=	'<img src="../uploads/icons/checkbox_empty_small.png" width="15px">';
			$incidenttext1		=	"&nbsp;&nbsp;(1) INCIDENT Includes the circumstances and events surrounding the alleged accident, injury, or other occurrence or breach of contract giving rise to this action or proceeding.";
			
			
			if($incidentoption == 1)
			{
				$incidenttext2		=	"&nbsp;&nbsp;(2) INCIDENT means (insert your definition here or on a separate, attached sheet labeled 'Sec. 4(a)(2)'):";
				$option1			=	$checkedimg.$incidenttext1;
				$option2			=	$uncheckedimg.$incidenttext2;
			}
			else if($incidentoption == 2)
			{
				$incidenttext2		=	"&nbsp;&nbsp;(2) $incidenttext";
				$option1			=	$uncheckedimg.$incidenttext1;
				$option2			=	$checkedimg.$incidenttext2;
			}
		?>
				<h4 class="text-center">Sec. 1. Instructions to All Parties</h4>
				<p>(a) Interrogatories are written questions prepared by a party to an action that are sent to any other party in the action to be answered under oath. The interrogatories below are form interrogatories approved for use in civil cases.</p>
                
                <p>(b) For time limitations, requirements for service on other parties, and other details, see Code of Civil Procedure section 2030 and the cases construing it.</p>
				<p>(c) These form interrogatories do not change existing law relating to interrogatories nor do they affect an answering party's right to assert any privilege or make any objection.</p>
				<h4 class="text-center">Sec. 2. Instructions to the Asking Party</h4><p>(a) These interrogatories are designed for optional use by parties in unlimited civil cases where the amount demanded exceeds $25,000. Separate interrogatories, Form Interrogatories --Economic Litigation (form FI-129), which have no subparts, are designed for use in limited civil cases where the amount demanded is $25,000 or less; however, those interrogatories may also be used in unlimited civil cases.</p>
                
                <p class="break-page"></p>
                
				<p>(b) Check the box next to each interrogatory that you want the answering party to answer. Use care in choosing those interrogatories that are applicable to the case.</p>
				<p>(c) You may insert your own definition of INCIDENT in Section 4, but only where the action arises from a course of conduct or a series of events occurring over a period of time.</p>
				<p>(d) The interrogatories in section 16.0, Defendant's Contentions -- Personal Injury, should not be used until the defendant has had a reasonable opportunity to conduct an investigation or discovery of plaintiff's injuries and damages.</p>
				<p>(e) Additional interrogatories may be attached.</p>
				<h4 class="text-center">Sec. 3. Instructions to the Answering Party</h4>
				<p>(a) An answer or other appropriate response must be given to each interrogatory checked by the asking party.</p>
				<p>(b) As a general rule, within 30 days after you are served with these interrogatories, you must serve your responses on the asking party and serve copies of your responses on all other parties to the action who have appeared. See Code of Civil Procedure section 2030 for details.</p>
				<p>(c) Each answer must be as complete and straightforward as the information reasonably available to you, including the information possessed by your attorneys or agents, permits. If an interrogatory cannot be answered completely, answer it to the extent possible.</p>
				<p>(d) If you do not have enough personal knowledge to fully answer an interrogatory, say so, but make a reasonable and good faith effort to get the information by asking other persons or organizations, unless the information is equally available to the asking party.</p>
				<p>(e) Whenever an interrogatory may be answered by referring to a document, the document may be attached as an exhibit to the response and referred to in the response. If the document has more than one page, refer to the page and section where the answer to the interrogatory can be found.</p>
				<p>(f) Whenever an address and telephone number for the same person are requested in more than one interrogatory, you are required to furnish them in answering only the first interrogatory asking for that information.</p>
				<p>(g) If you are asserting a privilege or making an objection to an interrogatory, you must specifically assert the privilege or state the objection in your written response.</p><p>(h) Your answers to these interrogatories must be verified, dated, and signed. You may wish to use the following form at the end of your answers.</p>
				<p>I declare under penalty of perjury under the laws of the State of California that the foregoing answers are true and correct.</p>
                <table class="tabela1" style="border:none !important;overflow: wrap">
                <tbody>
                <tr>
                    <tr>
                        <td  align="center">(DATE)</td>
                        <td  align="center">(SIGNATURE)</td>
                    </tr>
                </tr>
                </tbody>
                </table>	
				<p class="break-page"></p>
                <h4 class="text-center">Sec. 4. Definitions</h4>
                <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
                <p>(a) (Check one of the following):</p>
                <p>
                <?php 
                echo $option1;
                ?>
                </p>
                 <p>
                <?php 
                echo $option2;
                ?>
                </p>		
                
                <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your employees, your insurance companies, their agents, their employees, your attorneys, your accountants, your investigators, and anyone else acting on your behalf.</p>
                <p>(c) PERSON includes a natural person, firm, association, organization, partnership, business, trust, limited liability company, corporation, or public entity.</p>
                <p>(d) DOCUMENT means a writing, as defined in Evidence Code section 250, and includes the original or a copy of handwriting, typewriting, printing, photostats, photographs, electronically stored information, and every other means of recording upon any tangible thing and form of communicating or representation, including letters, words, pictures, sounds, or symbols, or combinations of them.</p>
                <p>(e) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil Procedure section 667.7(e)(3).</p>
                <p>(f) ADDRESS means the street address, including the city, state, and zip code.</p>
		<?php
		}
		else if($form_id == 2)
		{
		?>
		<div style="text-align:left">
				<h4 class="text-center">Sec. 1. Instructions to All Parties</h4>
				<p>(a) Interrogatories are written questions prepared by a party to an action that are sent to any other party in the action to be answered under oath. The interrogatories below are form interrogatories approved for use in employment cases.</p>
				<p>(b) For time limitations, requirements for service on other parties, and other details, see Code of Civil Procedure sections 2030.010-2030.410 and the cases construing those sections.</p>
				<p>(c) These form interrogatories do not change existing law relating to interrogatories nor do they affect an answering party's right to assert any privilege or make any objection.</p>
				<h4 class="text-center">Sec. 2. Instructions to the Asking Party</h4>
				<p>(a) These form interrogatories are designed for optional use by parties in employment cases. (Separate sets of interrogatories, Form Interrogatories-General (form DISC-001) and Form Interrogatories-Limited Civil Cases (Economic Litigation) (form DISC-004) may also be used where applicable in employment cases.)</p>
				<p>(b) Insert the names of the EMPLOYEE and EMPLOYER to whom these interrogatories apply in the definitions in sections 4(d) and (e) below.</p>
				<p>(c) Check the box next to each interrogatory that you want the answering party to answer. Use care in choosing those interrogatories that are applicable to the case.</p>
				<p>(d) The interrogatories in section 211.0, Loss of Income Interrogatories to Employer, should not be used until the employer has had a reasonable opportunity to conduct an investigation or discovery of the employee's injuries and damages.</p>
				<p>(e) Additional interrogatories may be attached.</p>
		
				<h4 class="text-center">Sec. 3. Instructions to the Answering Party</h4>
				<p>(a) You must answer or provide another appropriate response to each interrogatory that has been checked below.</p> 
				<p>(b) As a general rule, within 30 days after you are served with these interrogatories, you must serve your responses on the asking party and serve copies of your responses on all other parties to the action who have appeared. See Code of Civil Procedure sections 2030.260-2030.270 for details.</p>
				<p>(c) Each answer must be as complete and straightforward as the information reasonably available to you permits. If an interrogatory cannot be answered completely, answer it to the extent possible.</p>
				<p>(d) If you do not have enough personal knowledge to fully answer an interrogatory, say so but make a reasonable and good faith effort to get the information by asking other persons or organizations, unless the information is equally available to the asking party.</p>
				<p>(e) Whenever an interrogatory may be answered by referring to a document, the document may be attached as an exhibit to the response and referred to in the response. If the document has more than one page, refer to the page and section where the answer to the interrogatory can be found.</p>
				<p>(f) Whenever an address and telephone number for the same person are requested in more than one interrogatory, you are required to furnish them in answering only the first interrogatory asking for that information.</p>
				<p>(g) If you are asserting a privilege or making an objection to an interrogatory, you must specifically assert the privilege or state the objection in your written response.</p>
				<p>(h) Your answers to these interrogatories must be verified, dated, and signed. You may wish to use the following form at the end of your answers:</p>
				<p>I declare under penalty of perjury under the laws of the State of California that the foregoing answers are true and correct.</p>
				</div>
		<table class="tabela1" style="border:none !important;overflow: wrap">
        <tbody>
        <tr>
            <tr>
                <td  align="center">(DATE)</td>
                <td  align="center">(SIGNATURE)</td>
            </tr>
        </tr>
        </tbody>
        </table>
		<h4 class="text-center">Sec. 4. Definitions</h4>
        <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
        <p>(a) PERSON includes a natural person, firm, association, organization, partnership, business, trust, limited liability company, corporation, or public entity.</p>
        <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your employees, your insurance companies, their agents, their employees, your attorneys, your accountants, your investigators, and anyone else acting on your behalf.</p>
        <p>(c) EMPLOYMENT means a relationship in which an EMPLOYEE provides services requested by or on behalf of an EMPLOYER, other than an independent contractor relationship.</p>
        <?php 
        if($personnames1 != "")
        {
            ?>
            <p>(d) EMPLOYEE means a PERSON who provides services in an EMPLOYMENT relationship and who is a party to this lawsuit. For purposes of these interrogatories, EMPLOYEE refers to: <?php echo $personnames1; ?> </p>
            <?php
        }
        else
        {
            ?>
            <p>(d) EMPLOYEE means all such PERSONS</p>
            <?php
        }
        if($personnames2 != "")
        {
            ?>
            <p>(e) EMPLOYER means a PERSON who employs an EMPLOYEE to provide services in an EMPLOYMENT relationship and who is a party to this lawsuit. For purposes of these interrogatories, EMPLOYER refers to  <?php echo $personnames2; ?>:</p>        
            <?php
        }
        else
        {
            ?>
            <p>(d) EMPLOYEE means all such PERSONS</p>
            <?php
        }
        ?>
        <p>(f) ADVERSE EMPLOYMENT ACTION means any TERMINATION, suspension, demotion, reprimand, loss of pay, failure or refusal to hire, failure or refusal to promote, or other action or failure to act that adversely affects the EMPLOYEE'S rights or interests and which is alleged in the PLEADINGS.</p>
        <p>(g) TERMINATION means the actual or constructive termination of employment and includes a discharge, firing, layoff, resignation, or completion of the term of the employment agreement.</p>
        <p>(h) PUBLISH means to communicate orally or in writing to anyone other than the plaintiff. This includes communications by one of the defendant's employees to others. (Kelly v. General Telephone Co. (1982) 136 Cal.App.3d 278, 284.)</p>
        <p>(i) PLEADINGS means the original or most recent amended version of any complaint, answer, cross-complaint, or answer to cross-complaint.</p>
        <p>(j) BENEFIT means any benefit from an EMPLOYER, including an "employee welfare benefit plan" or employee pension benefit plan" within the meaning of Title 29 United States Code section 1002(1) or (2) or ERISA.</p>
        <p>(k) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil Procedure section 667.7(e)(3).</p>
        <p>(l) DOCUMENT means a writing, as defined in Evidence Code section 250, and includes the original or a copy of handwriting, typewriting, printing, photostats, photographs, electronically stored information, and every other means of recording upon any tangible thing and form of communicating or representation, including letters, words, pictures, sounds, or symbols, or combinations of them.</p>
        <p>(m) ADDRESS means the street address, including the city, state, and zip code.</p>
		<?php	
		}
	}
	?>
	<!-- ======================================= -->
        <form id="finaldraft" name="finaldraft">
        <input type="hidden" name="discovery_id" value="<?php echo $discovery_id?>" />
        	 <?php /*?><div class="form-group">
              <label for="comment">Instruction:</label>
              <textarea class="form-control" rows="10" id="instruction" name="instruction"><?php echo $instructions; ?></textarea>
            </div> <?php */?>
            <div class="">
                <ul class="list-group">
                        <?php
                        if(in_array($form_id,array(1,2)))
                        {
                            foreach($mainQuestions as $data)
                            {
                                $answer 				=	$data['answer'];
                                $answer_time 			=	$data['answer_time'];
                                $question_id 			=	$data['question_id'];
                                $question_type_id 		=	$data['question_type_id'];
                                $have_main_question 	=	$data['have_main_question'];
                                $objection 				=	$data['objection'];
                                $p_q_type_id 			=	$data['question_type_id'];
                                $question_title 		=	$data['question_title'];
                                $question_number 		=	$data['question_number'];
                                $sub_part 				=	$data['sub_part'];
                                $p_sub_part 			=	$data['sub_part'];
                                $is_pre_defined 		=	$data['is_pre_defined'];
                                $discovery_question_id	=	$data['discovery_question_id'];
                                $is_depended_parent		=	$data['is_depended_parent'];
                                $depends_on_question	=	$data['depends_on_question'];
                                $question_no_makeid		=	str_replace('.','_',$question_number);
                                ?>
                                <li  class="list-group-item">
                                    <?php
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
                                                                            have_main_question',
                                                                            
                                                                "q.question_number 	= 	:question_number AND  
                                                                q.id 				= 	 dq.question_id  AND
                                                                dq.discovery_id 	= 	:discovery_id AND 
                                                                q.sub_part 			!=   '' GROUP BY question_id",
                                                                array(":question_number"=>$question_number,":discovery_id"=>$discovery_id));
                                    
                                    }
                                    ?>
                                    <div class="form-group"> 
                                        <?php
										if(($question_number == "17.1" || $question_number == "217.1") && $isconwithdiscoveryid != 0)
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
											//dump($con_mainQuestions);
											?>
                                            <h4><u>FORM INTERROGATORY NO. <?php echo  $question_number; ?></u></h4>
											<?php
                                            if(sizeof($con_mainQuestions) > 0)
                                            {
                                                ?>
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <div class="form-group">
                                                            <?php
                                                            foreach($con_mainQuestions as $con_question)
                                                            {
                                                                $con_discovery_question_id	=	$con_question['discovery_question_id'];
                                                            
                                                                $query		=	"SELECT * FROM question_admits qa
                                                                                    LEFT JOIN question_admit_results qar 
                                                                                    ON  qar.discovery_question_id 	= 	'$con_discovery_question_id'  	AND
                                                                                    qar.question_admit_id			=	qa.id";
                                                            
                                                                $con_SubQuestions	=	$AdminDAO->executeQuery($query);
                                                            
                                                                foreach($con_SubQuestions as $con_SubQuestion)
                                                                {
                                                                ?>
                                                                    <p><?php echo $con_SubQuestion['question_no'].". ".$con_SubQuestion['sub_answer']; ?></p>
                                                                <?php
                                                                }
                                                                echo "<br>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <?php	
                                            }
											else
											{
											?>
											<ul class="list-group">
                                                <li class="list-group-item">
                                                    <div class="form-group"> 
                                                        <p> 
                                                            No Deny answer in RFA conjunction with this discovery.
                                                        </p>
                                                    </div>
                                                </li>
											</ul>
											<?php
											}
										}
										else
										{
											?>
                                            <p> 
                                                <b>Q No. <?php echo $question_number;?><?php echo $have_main_question==0?"&nbsp;($sub_part)":""?>: </b>
                                                <?php echo $question_title; ?>
                                            </p>
                                            <?php
											if(in_array($question_type_id,array(1,2)))
											{
											?>
											<textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?php echo  finalResponseGenerate($objection,$answer); ?></textarea>
											<?php
											}
											if($question_type_id != 1)
											{
												?>
												<ul class="list-group" id="subdiv<?php echo $question_no_makeid;?>" <?php if($question_type_id == 2 && $answer != "Yes"){ ?>style="display:none" <?php } ?>>
													<?php
													foreach($subQuestions as $data)
													{
														$answer1 				=	$data['answer'];
														$answer_time 			=	$data['answer_time'];
														$question_id 			=	$data['question_id'];
														$objection 				=	$data['objection'];
														$question_type_id 		=	$data['question_type_id'];
														$form_id 				=	$data['form_id'];
														$question_title 		=	$data['question_title'];
														$question_number 		=	$data['question_number'];
														$sub_part 				=	$data['sub_part'];
														$is_pre_defined 		=	$data['is_pre_defined'];
														$discovery_question_id	=	$data['discovery_question_id'];
														$have_main_question		=	$data['have_main_question'];
													?>
													 <li class="list-group-item">
														<div class="form-group">
															<p> 
																<b><?php echo $sub_part.")" ?> </b>
																<?php echo $question_title; ?>
															</p>
															<textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?php echo  finalResponseGenerate($objection,$answer1); ?> </textarea>
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
									$answer 				=	$data['answer'];
									$answer_detail 			=	$data['answer_detail'];
									$answer_time 			=	$data['answer_time'];
									$question_id 			=	$data['question_id'];
									$question_type_id 		=	$data['question_type_id'];
									$question_title 		=	$data['question_title'];
									$question_number 		=	$data['question_number'];
									$sub_part 				=	$data['sub_part'];
									$is_pre_defined 		=	$data['is_pre_defined'];
									$discovery_question_id	=	$data['discovery_question_id'];
									$objection 				=	$data['objection'];
									?>
									<div class="form-group">
										<p> 
											<b>Q No. <?php echo $question_number ?>: </b>
											<?php echo $question_title; ?>
										</p>
                                        <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?php echo  finalResponseGenerate($objection,$answer); ?></textarea>
                                        <!-- 
                                        We comment it because in RFA we do not show sub parts if deny because we show sub parts of this in SROGS or FROGSE
                                        -->
										<?php /*?><ul class="list-group" id="subdiv<?php echo $discovery_question_id;?>" <?php if($answer == "Admit" || $answer == ""){ ?>style="display:none" <?php } ?>>
                                            <?php
                                            foreach($generalQuestions as $generalQuestion)
                                            {
                                                $question_admit_id	=	$generalQuestion['id'];
                                                $subQuestionAnswers	=	$AdminDAO->getrows('question_admit_results',"*",":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id",array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id));
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
                                                    <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?php echo  finalResponseGenerate($objection,$sub_answer_show); ?></textarea>
                                                </div>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul><?php */?>
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
                                    $answer 			=	$data['answer'];
                                    $answer_detail 		=	$data['answer_detail'];
                                    $answer_time 		=	$data['answer_time'];
                                    $question_id 		=	$data['question_id'];
                                    $question_type_id 	=	$data['question_type_id'];
                                    $question_title 	=	$data['question_title'];
                                    $question_number 	=	$data['question_number'];
                                    $sub_part 			=	$data['sub_part'];
                                    $is_pre_defined 	=	$data['is_pre_defined'];
                                    $discovery_question_id	=	$data['discovery_question_id'];
                                    $objection 				=	$data['objection'];
                                    
                                    ?>
                                    <div class="form-group">
                                        <p> 
                                            <b>Q No. <?php echo $question_number ?>: </b>
                                            <?php echo $question_title; ?>
                                            
                                        </p>
                                        <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?php echo  finalResponseGenerate($objection,$answer); ?></textarea>
                                    </div>
                                </li>
                                <?php	
                            }												
                        }
                        ?>
                </ul>
            </div>
            <div class="" style="text-align:right">
                <i id="finaldraft_msgdiv" style="color:red"></i>
                <button type="button" class="btn btn-primary" onclick="FunctionFinalDraftAction();"><i class="fa-arrow-circle-right fa"></i> Continue </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function()
{
	$("#finaldraft_modal_title").html("Final Draft");
	//CKEDITOR.replace('pos_text', { height: 500});
	//loadinstructions();
});
function loadinstructions()
{
	var type = '<?php echo $type ?>';
	var form_id = '<?php echo $form_id ?>';
	var id = '<?php echo $discovery_id ?>';
	$.get("discoveryloadforminstruction.php?form_id="+form_id+"&id="+id+"&viewonly=1&type="+type).done(function(resp){$("#instruction").val(trim(resp));});
}
</script>
