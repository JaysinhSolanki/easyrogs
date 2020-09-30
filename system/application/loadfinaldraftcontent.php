<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$discovery_uid	= $_POST['discovery_id'];
$response_id	= $_POST['response_id'];

$discoveryDetails = $AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
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
                                                d.id 			as discovery_id,
                                                d.uid,
                                                d.send_date,
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

$discovery_data = $discoveryDetails[0];
Side::legacyTranslateCaseData(
    $discovery_data['case_id'],
    $discovery_data,
    $discovery_data['attorney_id'] // !! will use this attorney's side data
);

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

/***************************************
Query For Forms 1,2,3,4,5 Questions
****************************************/
if( in_array($form_id,array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS, Discovery::FORM_CA_RPDS)) ) {
    $orderByMainQuestions = "  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number ";
}
else {
    $orderByMainQuestions = "  ORDER BY display_order, q.id ";
}
$mainQuestions = $AdminDAO->getrows('discovery_questions dq,questions q',
                                        'dq.id 				as 	discovery_question_id,
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
                                        $orderByMainQuestions
                                        "
                                      );

/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions = $AdminDAO->getrows('question_admits',"*");

/************************************************
    Discovery Conjuction with some RFA or not
************************************************/
$isconwithdiscoveryid = 0;
if( in_array($form_id,array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE)) ) {
    $isConWithDiscovery = $AdminDAO->getrows('discoveries',"*",
                                                        "propounding			= '$propounding' AND
                                                        responding 				= '$responding' AND
                                                        case_id					= '$case_id' AND
                                                        interogatory_type		= '$form_id' AND
                                                        conjunction_setnumber 	= '$set_number'");
    if( sizeof($isConWithDiscovery) ) {
        $isconwithdiscoveryid	=	$isConWithDiscovery[0]['id'];
    }
}
$respond = 1;

$instructions = "This responding party has not completed its investigation or discovery of the facts of this case and is not yet prepared for trial. The answers contained herein are based upon the information presently available, and specifically known, to this responding party and disclose only those contentions that presently occur to such party. It is anticipated that further discovery, independent investigation, legal research, and analysis will supply additional facts, modify known facts, and establish entirely new factual or legal contentions that may lead to substantial additions and modifications to the contentions set forth herein. The following responses are provided without prejudice to this responding party's right to produce evidence of any subsequently discovered facts as well as facts presently known that this responding may later recall. This responding party reserves the right to change any and all of the responses herein. Said responses are made in a good faith effort to supply as much factual information and legal contentions as are presently known to this responding party but should not be considered complete nor should they prejudice this responding party with respect to further discovery, research, analysis, and development of legal theories.";
?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped">
          <tbody>
             <tr>
                  <th>Case Name</th>
                  <td><?= $case_title ?></td>
                  <th>Discovery</th>
                  <td><?= $discovery_name ?></td>
            </tr>
            <tr>
                <th>Propounding</th>
                  <td><?= $proponding_name ?></td>
                  <th>Responding</th>
                   <td><?= $responding_name ?></td>
            </tr>
          </tbody>
        </table>
        <form id="finaldraft" name="finaldraft">
        <input type="hidden" name="discovery_id" value="<?= $discovery_id ?>" />
         <input type="hidden" name="response_id" value="<?= $response_id  ?>" />
             <div class="form-group">
              <h4 for="comment" class="text-center">PRELIMINARY STATEMENT</h4>
              <textarea class="form-control" rows="10" id="instruction" name="instruction"><?=
                  $instructions
              ?></textarea>
            </div>
            <div class="">
                <ul class="list-group">
<?php
                        if( in_array($form_id,array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE)) ) {
                            foreach($mainQuestions as $data) {
                                $question_id 			=	$data['question_id'];
                                $question_type_id 		=	$data['question_type_id'];
                                $have_main_question 	=	$data['have_main_question'];
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
                                if( $response_id ) {
                                    $getAnswers				=	$AdminDAO->getrows("response_questions","*",
                                                                        "fkresponse_id				=	:fkresponse_id AND
                                                                        fkdiscovery_question_id 	= 	:discovery_question_id",
                                                                        array(	"discovery_question_id"	=>	$discovery_question_id,
                                                                                "fkresponse_id"			=>	$response_id));
                                    $answer 				=	$getAnswers[0]['answer'];
                                    $answer_time 			=	$getAnswers[0]['answer_time'];
                                    $objection 				=	$getAnswers[0]['objection'];
                                }
                                else {
                                    $answer 				=	"";
                                    $answer_time 			=	"";
                                    $objection 				=	"";
                                }

                                /**
                                * IF Depends-on-some-question then we need that question answer
                                **/
                                if( $depends_on_question && $response_id ) {
                                    $dependent_answer = trim( getAnswerOfDependentParentQuestion( $discovery_id, $depends_on_question, $response_id ) );
                                }
                                if( $depends_on_question && ( $dependent_answer == 'No' || !$dependent_answer ) && $view == Discovery::VIEW_RESPONDING ) {
                                    continue;
                                }

                                ?>
                                <li  class="list-group-item">
<?php
                                    if($question_type_id != 1)
                                    {
                                        $subQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',

                                                                            'dq.id as discovery_question_id,
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
                                            $con_mainQuestions = $AdminDAO->getrows('discovery_questions dq,questions q,response_questions rq',
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
?>
                                            <h4><u>FORM INTERROGATORY NO. <?= $question_number ?></u></h4>
<?php
                                            if( sizeof($con_mainQuestions) )
                                            {
?>
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <div class="form-group">
<?php
                                                            $finaldraftResponse	=	"";
                                                            foreach($con_mainQuestions as $con_question)
                                                            {
                                                                $con_discovery_question_id	=	$con_question['discovery_question_id'];
                                                                $con_response_id			=	$con_question['fkresponse_id'];

                                                                   $query		=	"SELECT * FROM question_admits qa
                                                                                    LEFT JOIN question_admit_results qar
                                                                                    ON  qar.discovery_question_id 	= 	'$con_discovery_question_id'  	AND
                                                                                    qar.question_admit_id			=	qa.id AND qar.fkresponse_id			=	'$con_response_id'";

                                                                $con_SubQuestions	=	$AdminDAO->executeQuery($query);

                                                                foreach($con_SubQuestions as $con_SubQuestion)
                                                                {
                                                                    $finaldraftResponse	.=	$con_SubQuestion['question_no'].". ".$con_SubQuestion['sub_answer']."\n";
                                                                }
                                                                $finaldraftResponse	.=	"\n";
                                                            }
?>
                                                            <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?=
                                                                $finaldraftResponse
                                                             ?></textarea>
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
                                            if(in_array($question_type_id,array(Discovery::FORM_CA_FROGS,Discovery::FORM_CA_FROGSE)))
                                            {
                                            ?>
                                            <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?=
                                                finalResponseGenerate( $objection, $answer )
                                            ?></textarea>
                                            <?php
                                            }
                                            if($question_type_id != 1)
                                            {
                                                ?>
                                                <ul class="list-group" id="subdiv<?php echo $question_no_makeid;?>" <?php if($question_type_id == 2 && $answer != "Yes"){ ?>style="display:none" <?php } ?>>
<?php
                                                    foreach( $subQuestions as $data ) {
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
                                                            $objection 				=	$getAnswers[0]['objection'];
                                                        }
                                                        else {
                                                            $answer1 				=	"";
                                                            $answer_time 			=	"";
                                                            $objection 				=	'';
                                                        }
?>
                                                     <li class="list-group-item">
                                                        <div class="form-group">
                                                            <p>
                                                                <b><?= $sub_part.")" ?> </b>
                                                                <?= $question_title ?>
                                                            </p>
                                                            <textarea rows="5" id="final_response<?php echo $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?=
                                                                finalResponseGenerate( $objection, $answer1 )
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
                                    $question_id 		 = $data['question_id'];
                                    $question_type_id 	 = $data['question_type_id'];
                                    $question_title 	 = $data['question_title'];
                                    $question_number 	 = $data['question_number'];
                                    $sub_part 			 = $data['sub_part'];
                                    $is_pre_defined 	 = $data['is_pre_defined'];
                                    $discovery_question_id = $data['discovery_question_id'];
                                    if( $response_id ) {
                                        $getAnswers		= $AdminDAO->getrows("response_questions","*",
                                                               "fkresponse_id				=	:fkresponse_id AND
                                                               fkdiscovery_question_id 	= 	:discovery_question_id",
                                                               array(	"discovery_question_id"	=> $discovery_question_id,
                                                                       "fkresponse_id"			=> $response_id));
                                        $answer 		= $getAnswers[0]['answer'];
                                        $answer_time 	= $getAnswers[0]['answer_time'];
                                        $answer_detail 	= trim($getAnswers[0]['answer_detail']);
                                        $objection 		= $getAnswers[0]['objection'];
                                    }
                                    else {
                                        $answer 				= "";
                                        $answer_time 			= "";
                                        $answer_detail 			= "";
                                        $objection 				= "";
                                    }
?>
                                    <div class="form-group">
                                        <p>
                                            <b>Q No. <?= $question_number ?>: </b>
                                            <?= $question_title ?>
                                        </p>
                                        <textarea rows="5" id="final_response<?= $discovery_question_id ?>" class="form-control" name="final_response[<?php echo $discovery_question_id; ?>]" placeholder="Final Response" required><?=
                                            finalResponseGenerate( $objection, $answer )
                                        ?></textarea>
                                        <!--
                                        [!] We commented out this code because in RFA we do not show sub parts if deny because we show sub parts of this in SROGS or FROGSE
                                        -->
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        else if( in_array($form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RPDS)) ) {
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
                                    $discovery_question_id = $data['discovery_question_id'];
                                    if( $response_id ) {
                                        $getAnswers = $AdminDAO->getrows("response_questions","*",
                                                                            "fkresponse_id				=	:fkresponse_id AND
                                                                            fkdiscovery_question_id 	= 	:discovery_question_id",
                                                                            array(	"discovery_question_id"	=>	$discovery_question_id,
                                                                                    "fkresponse_id"			=>	$response_id));
                                        $answer 		= trim($getAnswers[0]['answer']);
                                        $answer_time 	= $getAnswers[0]['answer_time'];
                                        $answer_detail 	= trim($getAnswers[0]['answer_detail']);
                                        $objection 		= $getAnswers[0]['objection'];
                                    }
                                    else {
                                        $answer 		= "";
                                        $answer_time 	= "";
                                        $answer_detail 	= "";
                                        $objection 		= "";
                                    }
                                    if( $form_id == Discovery::FORM_CA_RPDS ) {
                                        $response = '';
                                        if( $answer == Discovery::RPDS_ANSWER_NONE ) {
                                            //
                                        }
                                        if( $answer == Discovery::RPDS_ANSWER_HAVE_DOCS )  {
                                            $response = "Responsive documents have been provided.";
                                        }
                                        $str1 = "A diligent search and a reasonable inquiry have been made in an effort to comply with this demand, however, responding party is unable to comply because they do not have any responsive documents in their possession, custody, or control.";
                                        $str2 = $answer_detail ? (" However, respondent believes that ".$answer_detail." may have responsive documents.") : "";
                                        if( $answer == Discovery::RPDS_ANSWER_DOCS_NEVER_EXISTED ) {
                                            $response = $str1." Respondent does not believe that such documents have ever existed. ". $str2;
                                        }
                                        if( $answer == Discovery::RPDS_ANSWER_DOCS_DESTROYED ) {
                                            $response = $str1." Respondent believes that such documents have been destroyed. ".$str2;
                                        }
                                        if( $answer == Discovery::RPDS_ANSWER_DOCS_NO_ACCESS ) {
                                            $response = $str1." Respondent believes that such documents were lost, misplaced, stolen, or respondent lacks access to them. ".$str2;
                                        }
                                    }
                                    ?>
                                    <div class="form-group">
                                        <p>
                                            <b>Q No. <?= $question_number ?>: </b>
                                            <?= $question_title ?>
                                        </p>
                                        <textarea rows="5" id="final_response<?= $discovery_question_id ?>"
                                                    class="form-control"  required
                                                    name="final_response[<?= $discovery_question_id ?>]"
                                                    placeholder="Final Response"><?=
                                            finalResponseGenerate( $objection, $response )
                                        ?></textarea>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                </ul>
            </div>
            <div class="" style="text-align:right">
                <i id="POS_msgdiv" class="POS_msgdiv" style="color:red"></i>
                <button type="button" class="btn btn-purple" onclick="FunctionFinalDraftAction();"><i class="fa-arrow-circle-right fa"></i> Continue </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
jQuery( $ = {
    $("#finaldraft_modal_title").html("Final Draft");
    //CKEDITOR.replace('pos_text', { height: 500});
    //loadinstructions();
} );
function loadinstructions() {
    var type = '<?= $type ?>',
        form_id = '<?= $form_id ?>',
        id = '<?= $discovery_id ?>'
    $.get(`discoveryloadforminstruction.php?form_id=${form_id}&id=${id}&case_id=<?= $case_id ?>&viewonly=1&type=${type}`)
        .done( resp => {
            $("#instruction").val( trim(resp) );

            // const { discoveryType, discoveryFormNames, discoveryForm, } = globalThis,
			// 		suffix = (discoveryForm ? '@' + discoveryFormNames[discoveryForm-1] : '');
			// ctxUpdate({ id: `4x_${discoveryType}${suffix}`, pkscreenid: '47', url: 'sloadfinaldraftcontent.php', } );

            CKEDITOR.replace( 'instruction' );
        });
}
</script>
