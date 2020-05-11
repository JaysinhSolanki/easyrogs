<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$uid            =   $_GET['id'];
$view           =   $_GET['view'];
$respond        =   $_GET['respond'];
$response_id    =   $_GET['response_id'];
$supp           =   @$_GET['supp'];
if ($supp == "") {
    $supp = 0;
}
if ($view == 1) {
    $css    =   "";
} else {
    $css    =   "";
}


/***************************************
		Query For Header Data
****************************************/
//$AdminDAO->displayquery=1;
$discoveryDetails   =   $AdminDAO->getrows(
    'discoveries d,cases c,system_addressbook a,forms f',
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
											d.due,
											d.served,
											d.type,
											/* d.discovery_instrunctions Fixed by JS 3/2/20 */
											/* d.discovery_instructions, */
											c.plaintiff,
											c.defendant,
											d.send_date,
											d.propounding,
											d.responding,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											d.discovery_introduction as introduction,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.lastname 		as atorny_lname,
											d.attorney_id	as attorney_id,
											d.discovery_name,
											d.conjunction_setnumber,
											d.interogatory_type,
											a.email,
											f.form_instructions
											 as instructions 
											',
    /*(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND */
                                            "d.uid 			= :uid AND  
											
											d.case_id 		= c.id AND  
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
    array(":uid"=>$uid)
);

$discovery_data                     =   $discoveryDetails[0];
Side::legacyTranslateCaseData(
  $discovery_data['case_id'],
  $discovery_data
);

$case_title                         =   $discovery_data['case_title'];//$discovery_data['plaintiff']." V ".$discovery_data['defendant'];
$discovery_id                   =   $discovery_data['discovery_id'];
$case_number                    =   $discovery_data['case_number'];
$jurisdiction                   =   $discovery_data['jurisdiction'];
$judge_name                         =   $discovery_data['judge_name'];
$county_name                    =   $discovery_data['county_name'];
$court_address                  =   $discovery_data['court_address'];
$department                         =   $discovery_data['department'];
$case_id                        =   $discovery_data['case_id'];
$form_id                        =   $discovery_data['form_id'];
$set_number                         =   $discovery_data['set_number'];
$atorny_name                    =   $discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
$attorney_id                    =   $discovery_data['attorney_id'];
$form_name                      =   $discovery_data['form_name']." [Set ".$set_number."]";
$short_form_name                =   $discovery_data['short_form_name'];
$send_date                      =   $discovery_data['send_date'];
$email                          =   $discovery_data['email'];
$instructions                   =   $discovery_data['discovery_instrunctions'];
$type                           =   $discovery_data['type'];
$introduction                   =   $discovery_data['introduction'];
$propounding                    =   $discovery_data['propounding'];
$responding                         =   $discovery_data['responding'];
$discovery_name                     =   $discovery_data['discovery_name'];
$served                             =   $discovery_data['served'];
$due                            =   $discovery_data['due'];
if ($served  != "") {
    $served                             =   dateformat($discovery_data['served']);
}
if ($due     != "") {
    $due                            =   dateformat($discovery_data['due']);
}

if ($view == 1) {
    $form_name = strtoupper($discovery_name);
} else {
    $form_name = strtoupper("RESPONSE TO ".$discovery_name);
}
$form_name          =   $form_name." [Set ".$set_number."]";
/***************************************
Query For Forms 1,2,3,4,5 Questions
****************************************/
if (in_array($form_id, array(3,4,5))) {
    $orderByMainQuestions   =   "  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number ";
} else {
    $orderByMainQuestions   =   "  ORDER BY display_order, q.id ";
}

$mainQuestions  =   $AdminDAO->getrows(
    'discovery_questions dq,questions q',
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

/**
* GET Response Details
**/
if ($response_id > 0) {
    $getResponseDetails             =   $AdminDAO->getrows('responses', "*", "id = '$response_id'");
    $discovery_verification     =   $getResponseDetails[0]['discovery_verification'];
    $served                         =   $getResponseDetails[0]['servedate'];
    if ($served == "0000-00-00 00:00:00") {
        $served = "";
    } else {
        $served     =   dateformat($served);
    }
    /**
    * If going to create Supp/Amend of Response
    **/
    if ($supp == 1) {
        $getResponse        =   $AdminDAO->getrows("responses", "*", "fkdiscoveryid = :fkdiscoveryid AND fkresponseid != 0", array(":fkdiscoveryid"=>$discovery_id));
        $totalResponses         =   sizeof($getResponse)+1;
        $form_name          =   strtoupper(numToOrdinalWord($totalResponses))." RESPONSE TO ".$discovery_name." [Set ".$set_number."]";
    }
} else {
    $discovery_verification     =   "";
}

function numToOrdinalWord($num)
{
    $first_word = array('eth','First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Eleventh','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
    $second_word =array('','','Twenty','Thirty','Forty','Fifty');

    if ($num <= 20) {
        return $first_word[$num];
    }

    $first_num = substr($num, -1, 1);
    $second_num = substr($num, -2, 1);

    return $string = str_replace('y-eth', 'ieth', $second_word[$second_num].'-'.$first_word[$first_num]);
}
/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions   =   $AdminDAO->getrows('question_admits', "*");
/****************************************
	Load Documents Array if Form 3,4,5 case
****************************************/
if ($form_id == 5) {
    $where  =   " AND fkresponse_id = '$response_id' ";
}
$olddocuments   =   $AdminDAO->getrows('documents', "*", "discovery_id = '$discovery_id' $where");
$_SESSION['documents']  =   array();
if (sizeof($olddocuments) > 0) {
    foreach ($olddocuments as $data) {
        $doc_purpose    =   $data['document_notes'];
        $doc_name       =   $data['document_file_name'];
        $doc_path       =   SYSTEMPATH."uploads/documents/".$data['document_file_name'];
        if ($doc_name != "") {
            $documents[$uid][]  =   array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);
        }
    }
    $_SESSION['documents']  =   $documents;
}

/************************************************
	Discovery Conjunction with some RFA or not
************************************************/
//$isConWithDiscovery		=	$AdminDAO->getrows('discoveries',"id","conjunction_with = '$discovery_id'");
//$AdminDAO->displayquery=1;
$isconwithdiscoveryid   =   0;
if (in_array($form_id, array(1,2))) {
    $isConWithDiscovery         =   $AdminDAO->getrows(
        'discoveries',
        "*",
        "propounding			= 	'$propounding' AND
														responding 				=	'$responding' AND
														case_id					=	'$case_id' AND
														interogatory_type		=	'$form_id' AND
														conjunction_setnumber 	= 	'$set_number'"
    );
    if (sizeof($isConWithDiscovery) > 0) {
        $isconwithdiscoveryid   =   $isConWithDiscovery[0]['id'];
    }
}

//$AdminDAO->displayquery=1;
//dump($discoveryDetails);
//exit;

//Responding Party
$respondingdetails      =   $AdminDAO->getrows("clients", "*", "id = :id", array(":id"=>$responding));
$responding_name        =   $respondingdetails[0]['client_name'];
$responding_email       =   $respondingdetails[0]['client_email'];
$responding_type        =   $respondingdetails[0]['client_type'];
$responding_role        =   $respondingdetails[0]['client_role'];

//Propondoing Party
$propondingdetails      =   $AdminDAO->getrows("clients", "*", "id = :id", array(":id"=>$propounding));
$proponding_name        =   $propondingdetails[0]['client_name'];
$proponding_email       =   $propondingdetails[0]['client_email'];
$proponding_type        =   $propondingdetails[0]['client_type'];
$proponding_role        =   $propondingdetails[0]['client_role'];
?>
<style>
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
.w-900
{
    width:900px !important
}
.tooltip-inner {
    text-align: center !important;
}
</style>

<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading"></div>
            <div class="panel-body">
                <div class="panel panel-primary">
                <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <span style="font-size:18px; font-weight:600"><?php echo $form_name;?></span>
                    </div>
                </div>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-striped">
                  <tbody>
                     <tr>
                      <th>Case</th>
                      <td><?php echo str_replace(" V ", " v ", $case_title);?></td>
                      <th>Number</th>
                      <td><?php echo $case_number ?></td>
                    </tr>
                     <tr>
                        <th>County</th>
                      <td><?php echo $county_name ?></td>
                      <th>State</th>
                      <td><?php echo $jurisdiction ?></td>
                    </tr>
                    <?php /*?><tr>
                      <th>Court Address</th>
                      <td><?php echo $court_address ?></td>
                      <th>Attorney</th>
                      <td><?php echo $atorny_name ?></td>
                    </tr><?php */?>
                    <tr>
                      <td><b>Propounding Party</b></td>
                      <td><?php echo $proponding_name;  ?></td>
                      <td><b>Responding Party</b></td>
                       <td><?php echo $responding_name;  ?></td>
                    </tr>
                    <tr>
                      <td><b>Served Date</b></td>
                      <td><?php echo $served;  ?></td>
                      <td><b>Due Date</b></td>
                       <td><?php echo $due;  ?></td>
                    </tr>
                  </tbody>
                </table>
                    <form name="discoverydetailsform" action="#" method="post" id="discoverydetailsform">
                <input type="hidden" name="supp" value="<?php echo $supp ?>">
                <input type="hidden" name="supp_form_name" value="<?php echo $form_name ?>">
                <input type="hidden" name="form_id" value="<?php echo $form_id ?>">
                <input type="hidden" name="response_id" value="<?php echo $response_id ?>">
                <input type="hidden" name="case_id" value="<?php echo $case_id ?>">
                <input type="hidden" name="uid" value="<?php echo $uid ?>">
                <input type="hidden" name="discovery_id" value="<?php echo $discovery_id ?>">
                <input type="hidden" name="respond" value="<?php echo $respond ?>">
                <input type="hidden" name="discovery_verification" id="discovery_verification" value="<?php echo $discovery_verification ?>">
                <?php /*?><input type="hidden" name="discovery_verification_text" id="discovery_verification_text" value="<?php echo $discovery_verification_text ?>"><?php */?>
                <hr>
                <div class="row">
                <div id="loadinstructions"></div>
                <div class="col-md-12">
                    <ul class="list-group">
                    <?php
                    if (in_array($form_id, array(1,2))) {
                        foreach ($mainQuestions as $data) {
                            $dependent_answer       =   "";
                            $question_id            =   $data['question_id'];
                            $question_type_id       =   $data['question_type_id'];
                            $have_main_question     =   $data['have_main_question'];
                            $p_q_type_id            =   $data['question_type_id'];
                            $question_title         =   $data['question_title'];
                            $question_number        =   $data['question_number'];
                            $sub_part               =   $data['sub_part'];
                            $p_sub_part             =   $data['sub_part'];
                            $is_pre_defined         =   $data['is_pre_defined'];
                            $discovery_question_id  =   $data['discovery_question_id'];
                            $is_depended_parent         =   $data['is_depended_parent'];
                            $depends_on_question    =   $data['depends_on_question'];
                            $question_no_makeid         =   str_replace('.', '_', $question_number);
                            $has_extra_text             =   $data['has_extra_text'];
                            $extra_text                 =   $data['extra_text'];
                            $extra_text_field_label     =   $data['extra_text_field_label'];

                            if ($response_id > 0) {
                                $getAnswers                 =   $AdminDAO->getrows(
                                    "response_questions",
                                    "*",
                                    "fkresponse_id				=	:fkresponse_id AND  	
                                                                    fkdiscovery_question_id 	= 	:discovery_question_id",
                                    array(  "discovery_question_id"     =>  $discovery_question_id,
                                    "fkresponse_id"             =>  $response_id)
                                );
                                $answer                 =   $getAnswers[0]['answer'];
                                $answer_time            =   $getAnswers[0]['answer_time'];
                                $objection              =   $getAnswers[0]['objection'];
                            } else {
                                $answer                 =   "";
                                $answer_time            =   "";
                                $objection              =   "";
                            }
                            /**
                            * IF Depends upon some question then we need that question answer
                            **/
                            if ($depends_on_question > 0 && $response_id > 0) {
                                $dependent_answer   =   getAnswerOfDependentParentQuestion($discovery_id, $depends_on_question, $response_id);
                            }
                            ?>
                            <li <?php if ($depends_on_question != 0) {
?>class="list-group-item row_<?php echo $depends_on_question; ?>" <?php if (($dependent_answer == 'No' || $dependent_answer == '') && $view != 1) {
?>style='display:none;' <?php
}
} else {
?> class="list-group-item"  <?php
} ?>>
                                <?php
                                if ($question_type_id != 1) {
                                    

                                    //$AdminDAO->displayquery=1;
                                    $subQuestions   =   $AdminDAO->getrows(
                                        'discovery_questions dq,questions q',
                                        'dq.id as discovery_question_id,
                                                                        q.id as question_id,
                                                                        q.question_type_id as question_type_id,
                                                                        q.form_id as form_id,
                                                                        q.question_title as question_title,
                                                                        q.question_number as question_number,
                                                                        q.sub_part as sub_part,
                                                                        q.is_pre_defined as is_pre_defined,
                                                                        have_main_question',
                                        "q.question_number  =   :question_number AND
                                                            q.id 				= 	 dq.question_id  AND
                                                            dq.discovery_id     =   :discovery_id AND
                                                            q.sub_part 			!=   '' GROUP BY question_id",
                                        array(":question_number"=>$question_number,":discovery_id"=>$discovery_id)
                                    );

                                    if (sizeof($subQuestions) > 0) {
                                        $subquestuions_string   =   "";
                                        foreach ($subQuestions as $sub) {
                                            $sub_question_title         =   $sub['question_title'];
                                            $sub_question_number        =   $sub['question_number'];
                                            $sub_sub_part               =   $sub['sub_part'];
                                            $subquestuions_string       .=  " (".$sub_sub_part.") ".$sub_question_title." ";
                                        }
                                    } else {
                                        $subquestuions_string   =   "";
                                    }
                                }
                                ?>
                                <div class="form-group"> 
                                    <?php
                                    if ($view == 1) {
                                        ?>
                                        <p>
                                        <b>Q No. <?php echo $question_number ?>: </b>
                                        <?php echo $question_title.$subquestuions_string; ?>
                                        </p>
                                        <?php
                                    } else {
                                        //Get
                                        if (($question_number == "17.1" || $question_number == "217.1") /*&& $isconwithdiscoveryid != 0*/) {
                                            if ($isconwithdiscoveryid != 0) {
                                                $con_mainQuestions  =   $AdminDAO->getrows(
                                                    'discovery_questions dq,questions q,response_questions rq',
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
                                                //$AdminDAO->displayquery=0;
                                                //dump($con_mainQuestions);
                                                ?>
                                                <h5><u>FORM INTERROGATORY NO. <?php echo  $question_number; ?></u></h5>
                                                    <?php
                                                    if (sizeof($con_mainQuestions) > 0) {
                                                        ?>
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                <div class="form-group">
                                                                    <?php
                                                                    foreach ($con_mainQuestions as $con_question) {
                                                                        $con_discovery_question_id  =   $con_question['discovery_question_id'];
                                                                        $con_response_id            =   $con_question['fkresponse_id'];

                                                                        $query      =   "SELECT * FROM question_admits qa
                                                                                            LEFT JOIN question_admit_results qar 
                                                                                            ON  qar.discovery_question_id 	= 	'$con_discovery_question_id'  	AND
                                                                                            qar.question_admit_id			=	qa.id AND qar.fkresponse_id			=	'$con_response_id'";

                                                                        $con_SubQuestions   =   $AdminDAO->executeQuery($query);
                                                                        //dump( $con_SubQuestions);
                                                                        foreach ($con_SubQuestions as $con_SubQuestion) {
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
                                                    } else {
                                                    ?>
                                                        <p>
                                                            <b>Q No. <?php echo $question_number ?>: </b>
                                                            <?php echo $question_title.$subquestuions_string; ?>
                                                        </p>
                                                        <textarea style="background-color: antiquewhite;"  id="objection_<?php echo $discovery_question_id; ?>" class="form-control" name="objection[<?php echo $discovery_question_id ?>]" placeholder="Objection" required><?php echo html_entity_decode($objection) ?></textarea>
                                                    <?php
                                                    }
                                            }
                                            //$AdminDAO->displayquery=1;
                                        } else {
                                            //Hassan Editings
                                            ?>
                                            <p> 
                                                <b>Q No. <?php echo $question_number;?><?php echo $have_main_question==0?"&nbsp;($sub_part)":""?>: </b>
                                                <?php echo $question_title;
                                                if ($has_extra_text == 1) {
                                                    echo "<p><b>$extra_text_field_label: </b>$extra_text</p>";
                                                }
                                                if (in_array($question_type_id, array(1,2)) && $respond == 1) {
	                                                if( $objection == "" && $question_number == '12.2'){
		                                               $objection = "Objection, this interrogatory seeks information protected by the attorney work product privilege because it reflects counsel's evaluation of the case by revealing which witnesses counsel deemed important enough to interview. Nacht & Lewis Architects, Inc. v. Superior Court (1996) 47 Cal.App.4th 214, 217.";
	                                                }
                                                    ?>
                                                    <?php /*?>&nbsp;&nbsp;<a href="javascript:;" class="btn-info btn-sm" onclick="addObjectionFunction('<?php echo $discovery_question_id; ?>')">Add Objection</a><?php */?>
                                                    <textarea style="background-color: antiquewhite;"  id="objection_<?php echo $discovery_question_id; ?>" class="form-control" name="objection[<?php echo $discovery_question_id ?>]" placeholder="Objection" required><?php echo html_entity_decode($objection) ?></textarea>
                                                    <?php
                                                }
                                                ?>
                                                </p>
                                                <?php
                                                if ($question_type_id == 1) {
                                                ?>
                                                    <input type="hidden" name="have_main_question[<?php echo $discovery_question_id; ?>]" value="<?php echo $have_main_question?>"/>
                                                    <textarea id="answer<?php echo $discovery_question_id ?>" class="form-control" name="answer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo html_entity_decode($answer) ?></textarea>
                                                <?php
                                                } elseif ($question_type_id == 2) {
                                                    ?>
                                                        <div class="form-check form-check-inline">
                                                            <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Yes" onClick="checkFunction('<?php echo $question_no_makeid ?>','1')<?php if ($is_depended_parent ==1) {
?>,showhidequestions('<?php echo $question_id;?>',1)<?php
}?>" <?php if ($answer == 'Yes') {
    echo "checked";
} ?> <?php echo $css ?>>Yes</label>
                                                            <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="No" onClick="checkFunction('<?php echo $question_no_makeid ?>','2')<?php if ($is_depended_parent ==1) {
?>,showhidequestions('<?php echo $question_id;?>',2)<?php
}?>" <?php if ($answer == 'No') {
    echo "checked";
} ?> <?php echo $css ?>>No</label>                                      
                                                        </div>
                                                    <?php
                                                }
                                                if ($question_type_id != 1) {
                                                    ?>
                                                    
                                                    <ul class="list-group" id="subdiv<?php echo $question_no_makeid;?>" <?php if ($question_type_id == 2 && $answer != "Yes") {
?>style="display:none" <?php
} ?>>
                                                        <?php
                                                            //print_r($subQuestions);

                                                        foreach ($subQuestions as $data) {
                                                            $question_id            =   $data['question_id'];
                                                            $question_type_id       =   $data['question_type_id'];
                                                            $form_id                =   $data['form_id'];
                                                            $question_title         =   $data['question_title'];
                                                            $question_number        =   $data['question_number'];
                                                            $sub_part               =   $data['sub_part'];
                                                            $is_pre_defined         =   $data['is_pre_defined'];
                                                            $discovery_question_id  =   $data['discovery_question_id'];
                                                            $have_main_question         =   $data['have_main_question'];
                                                            if ($response_id > 0) {
                                                                $getAnswers                 =   $AdminDAO->getrows(
                                                                    "response_questions",
                                                                    "*",
                                                                    "fkresponse_id				=	:fkresponse_id AND  	
                                                                                                fkdiscovery_question_id 	= 	:discovery_question_id",
                                                                    array(  "discovery_question_id"     =>  $discovery_question_id,
                                                                    "fkresponse_id"             =>  $response_id)
                                                                );
                                                                $answer1                =   $getAnswers[0]['answer'];
                                                                $answer_time            =   $getAnswers[0]['answer_time'];
                                                                $objection              =   $getAnswers[0]['objection'];
                                                            } else {
                                                                $answer1                =   "";
                                                                $answer_time            =   "";
                                                                $objection              =   '';
                                                            }
                                                        ?>
                                                         <li class="list-group-item">
                                                            <div class="form-group">
                                                                <p> 
                                                                    <b><?php echo $sub_part.")" ?> </b>
                                                                    <?php echo $question_title; ?>
                                                                    <?php
                                                                    if ($respond == 1) {
                                                                    ?>
                                                                    <textarea style="background-color: antiquewhite;"  id="objection_<?php echo $discovery_question_id; ?>" class="form-control" name="objection[<?php echo $discovery_question_id ?>]" placeholder="Objection" ><?php echo html_entity_decode($objection) ?></textarea>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </p>
                                                                <input type="hidden" class="subanswer_<?php echo $question_no_makeid?>" name="have_main_question[<?php echo $discovery_question_id; ?>]" value="<?php echo $have_main_question?>" <?php if ($answer == "No" || ($answer == "" && $p_q_type_id == 1) || ($answer == "" && $p_q_type_id == 2)) {
?> disabled <?php
} ?>/>
                                                                <textarea  
                                                                id="answer<?php echo $discovery_question_id ?>" 
                                                                class="form-control subanswer_<?php echo $question_no_makeid?>" 
                                                                name="answer[<?php echo $discovery_question_id; ?>]" 
                                                                placeholder="Your Answer" required 
                                                                <?php echo $css ?> 
                                                                <?php if (($answer == "No" || $answer == "") && $p_q_type_id != 3) {
?> disabled <?php
} ?>><?php echo html_entity_decode($answer1) ?></textarea>
                                                            </div>  
                                                        </li>   
                                                        <?php
                                                        }
                                                        ?>
                                                  </ul> 
                                                    <?php
                                                }
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            
                            <?php
                        }
                    } elseif ($form_id == 4) {
                        foreach ($mainQuestions as $data) {
                            ?>
                            <li class="list-group-item">
                                <?php
                                $question_id            =   $data['question_id'];
                                $question_type_id       =   $data['question_type_id'];
                                $question_title         =   $data['question_title'];
                                $question_number        =   $data['question_number'];
                                $sub_part               =   $data['sub_part'];
                                $is_pre_defined         =   $data['is_pre_defined'];
                                $discovery_question_id  =   $data['discovery_question_id'];

                                if ($response_id > 0) {
                                    $getAnswers                 =   $AdminDAO->getrows(
                                        "response_questions",
                                        "*",
                                        "fkresponse_id				=	:fkresponse_id AND  	
                                                                    fkdiscovery_question_id 	= 	:discovery_question_id",
                                        array(  "discovery_question_id"     =>  $discovery_question_id,
                                        "fkresponse_id"             =>  $response_id)
                                    );
                                    $answer                 =   $getAnswers[0]['answer'];
                                    $answer_time            =   $getAnswers[0]['answer_time'];
                                    $answer_detail          =   $getAnswers[0]['answer_detail'];
                                    $objection              =   $getAnswers[0]['objection'];
                                } else {
                                    $answer                 =   "";
                                    $answer_time            =   "";
                                    $answer_detail          =   "";
                                    $objection              =   "";
                                }
                                ?>
                                <div class="form-group">
                                    <p> 
                                        <b>Q No. <?php echo $question_number ?>: </b>
                                        <?php echo $question_title; ?>
                                        
                                        <?php
                                        if ($respond == 1) {
                                        ?>
                                        <textarea style="background-color: antiquewhite;"  id="objection_<?php echo $discovery_question_id; ?>" class="form-control" name="objection[<?php echo $discovery_question_id ?>]" placeholder="Objection" required><?php echo html_entity_decode($objection) ?></textarea>
                                        <?php
                                        }
                                        ?>
                                    </p>
                                    <?php
                                    if ($view != 1) {
                                    ?>
                                    <div class="form-check form-check-inline">
                                        <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Admit" onClick="checkFunction('<?php echo $discovery_question_id ?>','2')" <?php if ($answer == 'Admit') {
                                            echo "checked";
} ?> <?php echo $css ?>>Admit</label>
                                        <label class="radio-inline"><input type="radio" name="answer[<?php echo $discovery_question_id ?>]" value="Deny" onClick="checkFunction('<?php echo $discovery_question_id ?>','1')" <?php if ($answer == 'Deny') {
                                            echo "checked";
} ?> <?php echo $css ?>>Deny</label>                                      
                                    </div>
                                    <br />
                                    <ul class="list-group" id="subdiv<?php echo $discovery_question_id;?>" <?php if ($answer == "Admit" || $answer == "") {
?>style="display:none" <?php
} ?>>
                                    <?php
                                    foreach ($generalQuestions as $generalQuestion) {
                                        $question_admit_id  =   $generalQuestion['id'];
                                        $subQuestionAnswers     =   $AdminDAO->getrows('question_admit_results', "*", ":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id AND fkresponse_id = :fkresponse_id", array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id,"fkresponse_id" => $response_id));
                                        $subQuestionAnswer  =   $subQuestionAnswers[0];

                                        if ($question_admit_id == 1) {
                                            $sub_answer_show    =   $question_number;
                                        } else {
                                            $sub_answer_show    =   trim(html_entity_decode($subQuestionAnswer['sub_answer']));
                                        }
                                        $sub_objection              =   trim(html_entity_decode($subQuestionAnswer['objection']));
                                    ?>
                                    <li class="list-group-item">
                                    <div class="form-group">
                                        <p> 
                                            <b><?php echo $generalQuestion['question_no'] ?>) </b>
                                            <?php echo $generalQuestion['question'] ?>
                                        </p>
                                        <textarea  style="background-color: antiquewhite; <?php if ($question_admit_id == 1) {
?> display:none <?php
} ?> " 
                                        <?php if ($answer == "Admit" || $answer == "") {
?> disabled <?php
} ?> 
                                        id="sub_objection<?php echo $discovery_question_id.'_'.$question_admit_id ?>" 
                                        class="form-control subanswer_<?php echo $discovery_question_id;?>" 
                                        name="rfa_objection[<?php echo $discovery_question_id ?>][<?php echo $question_admit_id; ?>]" 
                                        placeholder="Objection" required <?php echo $css ?>><?php echo ($sub_objection); ?></textarea>
                                        <br />
                                        <textarea 
                                        <?php if ($answer == "Admit" || $answer == "") {
?> disabled <?php
}
if ($question_admit_id == 1) {
?> readonly <?php
} ?> 
                                        id="subanswer<?php echo $discovery_question_id.'_'.$question_admit_id ?>" 
                                        class="form-control subanswer_<?php echo $discovery_question_id;?>" 
                                        name="subanswer[<?php echo $discovery_question_id ?>][<?php echo $question_admit_id; ?>]" 
                                        placeholder="Your Answer" required <?php echo $css ?>><?php echo ($sub_answer_show); ?></textarea>
                                        
                                        
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
                    } elseif (in_array($form_id, array(3,5))) {
                        foreach ($mainQuestions as $data) {
                            ?>
                            <li class="list-group-item">
                                <?php
                                $question_id        =   $data['question_id'];
                                $question_type_id   =   $data['question_type_id'];
                                $question_title     =   $data['question_title'];
                                $question_number    =   $data['question_number'];
                                $sub_part           =   $data['sub_part'];
                                $is_pre_defined     =   $data['is_pre_defined'];
                                $discovery_question_id  =   $data['discovery_question_id'];
                                if ($response_id > 0) {
                                    $getAnswers                 =   $AdminDAO->getrows(
                                        "response_questions",
                                        "*",
                                        "fkresponse_id				=	:fkresponse_id AND  	
                                                                        fkdiscovery_question_id 	= 	:discovery_question_id",
                                        array(  "discovery_question_id"     =>  $discovery_question_id,
                                        "fkresponse_id"             =>  $response_id)
                                    );
                                    $answer                 =   $getAnswers[0]['answer'];
                                    $answer_time            =   $getAnswers[0]['answer_time'];
                                    $answer_detail          =   $getAnswers[0]['answer_detail'];
                                    $objection              =   $getAnswers[0]['objection'];
                                } else {
                                    $answer                 =   "";
                                    $answer_time            =   "";
                                    $answer_detail          =   "";
                                    $objection              =   "";
                                }
                                ?>
                                <div class="form-group">
                                    <p> 
                                        <b>Q No. <?php echo $question_number ?>: </b>
                                        <?php echo $question_title; ?>
                                        <?php
                                        if ($respond == 1) {
                                        ?>
                                        <?php /*?>&nbsp;&nbsp;<a href="javascript:;" class="btn-info btn-sm" onclick="addObjectionFunction('<?php echo $discovery_question_id; ?>')">Add Objection</a><?php */?>
                                        <textarea style="background-color: antiquewhite;"  id="objection_<?php echo $discovery_question_id; ?>" class="form-control" name="objection[<?php echo $discovery_question_id ?>]" placeholder="Objection" required><?php echo html_entity_decode($objection) ?></textarea>
                                        <?php
                                        }
                                        ?>
                                    </p>
                                    <?php
                                    if ($view != 1) {
                                        if ($form_id == 5) {
                                        ?>
                                            <select class="form-control" id="answer<?php echo $discovery_question_id; ?>"  name="answer[<?php echo $discovery_question_id; ?>]" onChange="checkFunctionForm5('<?php echo $discovery_question_id; ?>',this.value)" <?php echo $css ?>>
                                            <option <?php if ($answer == "Select Your Response") {
                                                echo "selected";
} ?>>Select Your Response</option>
                                            <option <?php if ($answer == "I have responsive documents") {
                                                echo "selected";
} ?>>I have responsive documents</option>
                                            <option <?php if ($answer == "Responsive documents have never existed") {
                                                echo "selected";
} ?>>Responsive documents have never existed</option>
                                            <option <?php if ($answer == "Responsive documents were destroyed") {
                                                echo "selected";
} ?>>Responsive documents were destroyed</option>
                                            <option <?php if ($answer == "Responsive documents were lost, misplaced, stolen, or I lack access to them") {
                                                echo "selected";
} ?>>Responsive documents were lost, misplaced, stolen, or I lack access to them</option> 
                                            </select>
                                        <?php
                                        } elseif ($form_id == 3) {
                                        ?>
                                            <textarea id="answer<?php echo $discovery_question_id ?>" class="form-control " name="answer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo html_entity_decode($answer) ?></textarea>
                                        <?php
                                        }
                                        if ($form_id == 5) {
                                        ?>
                                            <ul class="list-group" id="note<?php echo $discovery_question_id;?>" <?php if ($answer != "I have responsive documents") {
?>style="display:none" <?php
} ?>>
                                                <li class="list-group-item">
                                                <div class="form-group">
                                                    <p> 
                                                        <b>Note: </b>
                                                        Upload the documents in Exhibit A section at bottom.
                                                    </p>
                                                </div>
                                                </li>
                                            </ul> 
                                            <ul class="list-group" id="subdiv<?php echo $discovery_question_id;?>" <?php if ($answer == 'Select Your Response' || $answer == "I have responsive documents") {
?>style="display:none" <?php
} ?>>
                                                <li class="list-group-item">
                                                <div class="form-group">
                                                    <p> 
                                                        <b>a) </b>
                                                        Enter the name and address of anyone you believes has the documents.
                                                       </p>
                                                    <textarea <?php if ($answer == 'Select Your Response' || $answer == "I have responsive documents") {
?> disabled <?php
} ?> id="subanswer<?php echo $discovery_question_id ?>" class="form-control" name="subanswer[<?php echo $discovery_question_id; ?>]" placeholder="Your Answer" required <?php echo $css ?>><?php echo html_entity_decode($answer_detail) ?></textarea>
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
                if (in_array($form_id, array(3,4)) && sizeof($olddocuments) > 0) {
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
                $formArray  =   array(5);//3,4,5
                if (in_array($form_id, $formArray) && $view != 1) {
                ?>
                <div class="col-md-12">
                    <hr> 
                    <ul class="list-group"> 
                        <li class="list-group-item">
                            <div class="form-group">
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
                <div class="text-center">
                <?php
                if ($view != 1) {
                    buttonsave('discoveryfrontaction.php', 'discoverydetailsform', 'wrapper', 'discoveries.php?pkscreenid=45&pid='.$case_id, 0);
                }
                buttoncancel(45, 'discoveries.php?pid='.$case_id);
                /*
                ?>
                <a  target="_blank" href="makepdf.php?id=<?php echo $_GET['id']?>&view=<?php echo $_GET['view'] ?>" class="btn btn-success" ><i class="fa fa-file-pdf-o"></i> PDF</a>
                <?php
                */
                if ($view != 1) {
                ?>
                <a href="javascript:;" class="btn btn-purple" onclick="serveFunction2('<?php echo $discovery_verification ?>','<?php echo $_GET['id']?>','<?php echo $response_id ?>')"  ><i class="fa fa-share"></i> Serve</a>
                <button type="button" class="btn btn-info buttonid " data-style="zoom-in" onclick="checkClientEmailFound('<?php echo $discovery_id ?>',2);" title="" >
                        <i class="icon-ok bigger-110"></i>
                            <span class="ladda-label">
                                Client <i class="fa fa-play" aria-hidden="true"></i>
                            </span>
                            <a href="#"><i style="font-size:16px" data-placement="top" data-toggle="tooltip" title="" class="fa fa-info-circle tooltipshow client-btn" aria-hidden="true" data-original-title=""></i></a>
                        <span class="ladda-spinner"></span>
                </button>
                <?php
                }
                ?>
            </div>
                </form>
                </div>
                </div>
            </div>
        </div> 
    </div>
</div>

<div class="modal fade" id="serve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:450px !important">
    <div class="modal-content" style="width: 586px">
        <div class="modal-body" id="loadmodalcontent">
        <div class="swal-icon swal-icon--warning">
        <span class="swal-icon--warning__body">
          <span class="swal-icon--warning__dot"></span> 
        </span>
        </div>
        <div class="swal-title" style="font-size:18px">Responses aren't verified.</div>
        <form id="deleteform" name="deleteform">
        <div class="swal-footer">
        <div class="swal-button-container">
        <button class="swal-button swal-button--confirm swal-button--success btn-success" type="button" onclick="callemailclientmodal()" ><i class="fa fa-user "></i> Ask Client to Verify</button>
        </div>
        <div class="swal-button-container">
        <button class="swal-button swal-button--confirm swal-button--info" type="button" onclick="callFinalDraftModal('<?php echo $_GET['id']?>','<?php echo $discovery_verification ?>','<?php echo $response_id ?>')" ><i class="fa fa-share"></i> Serve anyway</button>
        </div>
        <div class="swal-button-container">
        <button class="swal-button swal-button--danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
        </div>
        </div>
          
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="emailclientmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Email to client</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button> 
      </div>
      <div class="modal-body">
        <div class="form-group">
        <label for="caseteam_attr_name">Write an email text for client and click on send button. You'll be automatically notified when it's done.:</label>
        <textarea type="text" rows="10" name="message_to_client" class="form-control" id="message_to_client">
<?php
echo "Hi,

You have not verify the discovery SPECIAL INTERROGATORIES. Please click on the link below and verify your discovery. 

~LINK_HERE~.";
        ?>
        </textarea>
        </div>
      </div>
      <div class="modal-footer">
         <i id="msgEmailClientModal" style="color:red"></i> 
         <button type="button" onclick="serveaction(1,<?php echo $discovery_id; ?>)" class="btn btn-success"><i class="fa fa-share"></i> Send</button>
         <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button> 
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="finaldraft_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" id="m-width">
    <div class="modal-content">
      <div class="modal-header" style="padding:13px !important">
        <h5 class="modal-title text-center" id="finaldraft_modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -50px;font-size: 35px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_finaldraft_modal_content">
       <div class="text-center"> Loading...</div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="clientemailfound_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" id="m-width">
    <div class="modal-content">
      <div class="modal-header" style="padding:13px !important">
        <h5 class="modal-title text-center" id="clientemailfound_modal_title" style="font-size:24px">Please enter <?php echo $responding_name ?>'s email address below</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px;font-size: 30px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_clientemailfound_modal_content">
        
      </div>
    </div>
  </div>
</div>
<link href="../assets/vendors/uploadfile.css" rel="stylesheet">
<script src="../assets/vendors/jquery-validation/jquery-1.9.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../assets/vendors/jquery.uploadfile.min.js"></script>
<script>
$.noConflict();
function sendtoclientfunction(discovery_id,actiontype)
{
    $.LoadingOverlay("show");
    setTimeout(function()  
    {
        $.post( "discoveryfrontaction.php",  $("#discoverydetailsform").serialize()).done(function( data1) 
        {        
            $.post( "messagetoclientforverification.php", { discovery_id: discovery_id,actiontype:actiontype}).done(function( data ) 
            {
                $.LoadingOverlay("hide");
                response(data);
            });
        });
    }, 2000);   
}
function checkClientEmailFound(discovery_id,actiontype)
{
    $.post( "checkclientemailfound.php", { discovery_id: discovery_id,actiontype:actiontype}).done(function( data ) 
    {
        var obj = JSON.parse(data); 
        if(obj.found == 1)
        {
            sendtoclientfunction(obj.discovery_id,obj.actiontype);
        }
        else
        {
            callclientemailmodal(obj.discovery_id,obj.actiontype);
        }
    });
}
function callclientemailmodal(discovery_id,actiontype)
{
    $("#load_clientemailfound_modal_content").html("");
    $.post( "loadclientemailmodal.php", { discovery_id: discovery_id,actiontype:actiontype}).done(function( data ) 
    {
        $("#load_clientemailfound_modal_content").html(data);
        $('#clientemailfound_modal').modal('toggle');
    });
}
function saveclientemail()
{
    var client_email    =   $("#client_email").val();
    $("#msgAddEmailClientModal").html("");
    if(client_email == "")
    { 
        $("#msgAddEmailClientModal").html("Please enter client email.");
    }
    else  
    {
        $.post("saveclientemailaction.php",$("#addClientEmailModal").serialize()).done(function( data) 
        {   
            $('#clientemailfound_modal').modal('toggle');
            var obj = JSON.parse(data);
            sendtoclientfunction(obj.discovery_id,obj.actiontype);
        }); 
    }
}
function callemailclientmodal()
{
    $("#serve_modal").modal("toggle");
    //$("#emailclientmodal").modal("toggle");
    serveaction(1,<?php echo $discovery_id; ?>);
}
function addObjectionFunction(discovery_question_id)
{
    $("#loadobjectiondata").html("");
    $.post( "loadobjectionmodal.php", { discovery_question_id: discovery_question_id}).done(function( data ) 
    {
        $("#loadobjectiondata").html(data);
        $('#objectionModal').modal('toggle');
    });
}
function loadinstructions(form_id,id)
{
    var type = '<?php echo $type ?>';
    $.get("discoveryloadforminstruction.php?form_id="+form_id+"&id="+id+"&viewonly=1&type="+type).done(function(resp){$("#loadinstructions").html(trim(resp));});
}
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
    setTimeout(function()
    {
        loadToolTipForClientBtn('<?php echo $responding; ?>');
        
    }, 1000);
    
    $('.tooltipshow').tooltip({
    container: 'body',
    html: true
    });
    <?php
    if (in_array($form_id, array(3,4))) {
    ?>
        loaduploaddiscoverydocs();
    <?php
    }
    ?>
    loadinstructions('<?php echo $form_id ?>','<?php echo $discovery_id ?>');
    loaduploadeddocs();
    var extraObj = $("#extraupload").uploadFile({
    url:"frontdocumentuploads.php",
    fileName:"myfile",
    extraHTML:function()
    {
            var html = "<div><b style='display:none'>Document Purpose:</b><input type='hidden' class='form-control' name='doc_purpose' value='' /><input type='hidden' name='rp_uid' value='<?php echo $uid ?>' /> <br/>";
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
    var rp_uid  =   '<?php echo $uid; ?>';
    $("#uploadeddocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=1");
}
function loaduploaddiscoverydocs()
{
    var rp_uid  =   '<?php echo $uid; ?>';
    $("#uploaddiscoverydocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=0");
}
function deleteDoc(id,rp_uid)
{
    $.post( "deletefrontdocs.php", { id: id,rp_uid:rp_uid }).done(function( data ) 
    {
        loaduploadeddocs();
    });
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
function serveaction(actiontype,discover_id) //actiontype:1 => Email Client, actiontype:2 => Serve
{
    var error   =   0;
    var msg     =   "";
    //$("#msgEmailClientModal").html('');
    /*if(actiontype == 1)
    {
        var message_to_client   =   $("#message_to_client").val();
        if(message_to_client == '')
        {
            var msg =   "Please enter email text to client.";
            error = 1;
        }
    }
    else
    {
        var message_to_client   =   "";
    }*/
    
    if(error == 0)
    {
        $.post( "messagetoclientforverification.php", { discover_id: discover_id,actiontype:actiontype/*,message_to_client:message_to_client*/ }).done(function( data ) 
        {
            response(data);
            /*if(data == 'success')
            {
                $("#emailclientmodal").modal("toggle"); 
            }*/
        });
    }
    /*else
    {
        $("#msgEmailClientModal").html(msg);
    }*/
}
function serveFunction2(is_verified,discovery_id,response_id)
{
    if(is_verified == '')
    {
        //alert(1);
        setTimeout(function(){ $("#serve_modal").modal("toggle");  }, 1000);    
    }
    else
    {
        //alert("Go to Final Draft (In progress)");
        callFinalDraftModal('<?php echo $_GET['id']?>',is_verified,response_id);
        //serveaction(2,discover_id);
    }
}
function callFinalDraftModal(discovery_id,is_verified,response_id)
{
    $.post( "discoveryfrontaction.php",  $("#discoverydetailsform").serialize()).done(function( data ) 
    {
        var obj = JSON.parse(data); 
        response_id =   obj.response_id;
        $.post( "loadfinaldraftcontent.php", { discovery_id: discovery_id,response_id:response_id }).done(function( data ) 
        {
            $("#load_finaldraft_modal_content").html(data);
        });
        $('#m-width').addClass('w-900'); 
        if(is_verified == '')
        {
            $('#serve_modal').modal('toggle');
        }
        setTimeout(function(){ $('#finaldraft_modal').modal('toggle'); }, 1000);
    });
}
function FunctionFinalDraftAction()
{
    $.post( "finaldraftaction.php",  $("#finaldraft").serialize()).done(function( data ) 
    {
        var obj = JSON.parse(data); 
        var response_id =   obj.response_id;
        var messagetype =   obj.messagetype;
        if(messagetype == "success")
        {
            $('#finaldraft_modal').modal('toggle');
            PopupForPOS('<?php echo $discovery_id?>',response_id);
        }
    });
}
function PopupForPOS(discovery_id,response_id)
{
    $.post( "loadpospopupcontent.php",{id: discovery_id,respond:1,response_id:response_id}).done(function( data ) 
    {
        $("#load_general_modal_content").html(data);
    });
    $('#general_modal_title').html("PROOF OF ELECTRONIC SERVICE");
    $('#general-width').addClass('w-900');  
    //alert(123);
    setTimeout(function(){ $('#general_modal').modal('toggle');  }, 2000);
    
}

function servePOS()
{
    var pos_state   =   $("#pos_state").val();
    var pos_city    =   $("#pos_city").val();
    var error       =   0;
    var msg         =   "";
    
    if(pos_city == "")
    {
        error   =   1;
        msg     =   "Please enter city.";
    }
    if(pos_state == "")
    {
        error   =   1;
        msg     =   "Please enter state.";
    }
    if(error == 1)
    {
        $(".POS_msgdiv").html(msg);
    }
    else
    {
        $.LoadingOverlay("show");
        $("#citystate").replaceWith(pos_city+", "+pos_state);
        var poshtml     =   $("#poshtml").html();
        $("#pos_text").val(poshtml);
        $("#posstate").val(pos_state);
        $("#poscity").val(pos_city);
        
        //alert($("#pos_city").val());
        //writeDiscoveryPDF('<?php //echo $uid; ?>');
        setTimeout(function()
        {
            $.post( "propondingserveaction.php",$("#formPOS" ).serialize()).done(function( data ) 
            {
                $('#general_modal').modal('toggle');
                $.LoadingOverlay("hide");
                response(data);
            });         
        }, 2000);
    }
}
function writeDiscoveryPDF(uid)
{
    $.get( "makepdf.php", { id: uid, downloadORwrite: 1,view:0 }).done(function( data ) {});
}
</script>
