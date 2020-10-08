<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$view    = @$_GET['view'];
$respond = @$_GET['respond'];
$is_supp = @$_GET['supp'] ?: 0;

$uid         = @$_GET['id'];
$response_id = @$_GET['response_id'];

if ($view == 1) { // TODO [!?]
    $css = "";
} else {
    $css = "";
}


/***************************************
		Query For Header Data
****************************************/
$discovery_data = $discoveriesModel->findDetails($uid);
Side::legacyTranslateCaseData(
  $discovery_data['case_id'],
  $discovery_data
);

$case_title      = $discovery_data['case_title'];
$discovery_id    = $discovery_data['discovery_id'];
$case_number     = $discovery_data['case_number'];
$jurisdiction    = $discovery_data['jurisdiction'];
$judge_name      = $discovery_data['judge_name'];
$county_name     = $discovery_data['county_name'];
$court_address   = $discovery_data['court_address'];
$department      = $discovery_data['department'];
$case_id         = $discovery_data['case_id'];
$form_id         = $discovery_data['form_id'];
$set_number      = $discovery_data['set_number'];
$atorny_name     = $discovery_data['attorney'];
$attorney_id     = $discovery_data['attorney_id'];
$short_form_name = $discovery_data['short_form_name'];
$send_date       = $discovery_data['send_date'];
$email           = $discovery_data['email'];
$instructions    = $discovery_data['discovery_instrunctions'];
$type            = $discovery_data['type'];
$introduction    = $discovery_data['introduction'];
$propounding     = $discovery_data['propounding'];
$responding      = $discovery_data['responding'];
$served          = $discovery_data['served'];
$due             = $discovery_data['due'];
if( $served ) {
    $served = dateformat($served);
}
if( $due != "" ) {
    $due = dateformat($discovery_data['due']);
}
if( ($view == Discovery::VIEW_RESPONDING) || $respond || $response_id ) { //!!
  $form_name = $responsesModel->getTitle($response_id, $discovery_data);
} else {
  $form_name = $discoveriesModel->getTitle($discovery_data);
}
$setSuffix = $discoveriesModel->getSet($discovery_data);

/***************************************
Query For Forms 1,2,3,4,5 Questions
****************************************/
if (in_array($form_id, array(Discovery::FORM_CA_SROGS,Discovery::FORM_CA_RFAS,Discovery::FORM_CA_RPDS))) {
    $orderByMainQuestions = "  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number ";
} else {
    $orderByMainQuestions = "  ORDER BY display_order, q.id ";
}

$mainQuestions  = $AdminDAO->getrows(
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
										q.id 				= dq.question_id  AND
										dq.discovery_id = '$discovery_id' AND
										(
											q.sub_part 		= '' OR
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
if( $response_id ) {
    $getResponseDetails     = $responsesModel->find($response_id);
    $discovery_verification = $getResponseDetails['discovery_verification'];
    $served                 = $getResponseDetails['servedate'];
    if ($served == "0000-00-00 00:00:00") {
        $served = "";
    } else {
        $served = dateformat($served);
    }
    /**
    * If going to create Supp/Amend of Response
    **/
    if( $is_supp ) {
        $form_name = $responsesModel->getTitle(0,$discovery_data,$is_supp);
    }
} else {
    $discovery_verification = "";
}

/***************************************
Query For Sub Questions Use in Form 4
****************************************/
$generalQuestions = $AdminDAO->getrows('question_admits', "*");
/****************************************
	Load Documents Array if Form 3,4,5 case
****************************************/
if ($form_id == Discovery::FORM_CA_RPDS) {
    $where = " AND fkresponse_id = '$response_id' ";
}
$olddocuments = $AdminDAO->getrows('documents', "*", "discovery_id = '$discovery_id' ".($where?:'') );
$_SESSION['documents'] = array();
if (sizeof($olddocuments) > 0) {
    foreach ($olddocuments as $data) {
        $doc_purpose = $data['document_notes'];
        $doc_name    = $data['document_file_name'];
        $doc_path    = SYSTEMPATH."uploads/documents/".$data['document_file_name'];
        if ($doc_name != "") {
            $documents[$uid][] = array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);
        }
    }
    $_SESSION['documents'] = $documents;
}

/************************************************
	Discovery Conjunction with some RFA or not
************************************************/
//$isConWithDiscovery = $AdminDAO->getrows('discoveries',"id","conjunction_with = '$discovery_id'");
//$AdminDAO->displayquery=1;
$isconwithdiscoveryid = 0;
if (in_array($form_id, array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE))) {
    $isConWithDiscovery = $AdminDAO->getrows(
                                        'discoveries',
                                        "*",
                                        "propounding	= '$propounding' AND
                                        responding 				= '$responding' AND
                                        case_id					= '$case_id' AND
                                        interogatory_type		= '$form_id' AND
                                        conjunction_setnumber 	= '$set_number'"
                                    );
    if (sizeof($isConWithDiscovery) > 0) {
        $isconwithdiscoveryid = $isConWithDiscovery[0]['id'];
    }
}

//Responding Party
$respondingdetails      = $AdminDAO->getrows("clients", "*", "id = :id", array(":id"=>$responding));
$responding_name        = $respondingdetails[0]['client_name'];
$responding_email       = $respondingdetails[0]['client_email'];
$responding_type        = $respondingdetails[0]['client_type'];
$responding_role        = $respondingdetails[0]['client_role'];

//Propondoing Party
$propondingdetails      = $AdminDAO->getrows("clients", "*", "id = :id", array(":id"=>$propounding));
$proponding_name        = $propondingdetails[0]['client_name'];
$proponding_email       = $propondingdetails[0]['client_email'];
$proponding_type        = $propondingdetails[0]['client_type'];
$proponding_role        = $propondingdetails[0]['client_role'];
?>
<style>
body.modal-open
{
    position: static !important;
}
.w-900
{
    width:900px !important
}
.tooltip-inner {
    text-align: center;
}
</style>


<div id="screenfrmdiv" class="main">

    <aside class="sidebar left "><div class="fixed"></div></aside>
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading"></div>
            <div class="panel-body">
                <div class="panel panel-primary">
                <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <input  id="form_title" type="text" class="form-control"
                                onfocus="formTitle_Edit()" onblur="formTitle_Save()"
                                style="font-size:18px; font-weight:600" value="<?= $form_name ?>" />
                        <button type="button" class="btn-mini" style="float: right; margin: -2.2em 0.5em 0 0.8em;" onclick="formTitle_Revert()">
                            <i class="fa fa-undo " />
                        </button>
                    </div>
                </div>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-striped">
                  <tbody>
                     <tr>
                      <th>Case</th>
                      <td><?= str_replace(" V ", " v ", $case_title) ?></td>
                      <th>Number</th>
                      <td><?= $case_number ?></td>
                    </tr>
                     <tr>
                        <th>County</th>
                      <td><?= $county_name ?></td>
                      <th>State</th>
                      <td><?= $jurisdiction ?></td>
                    </tr>
                    <tr>
                      <td><b>Propounding Party</b></td>
                      <td><?= $proponding_name ?></td>
                      <td><b>Responding Party</b></td>
                       <td><?= $responding_name ?></td>
                    </tr>
                    <tr>
                      <td><b>Served Date</b></td>
                      <td><?= $served ?></td>
                      <td><b>Due Date</b></td>
                       <td><?= $due ?></td>
                    </tr>
                  </tbody>
                </table>
                    <form name="discoverydetailsform" action="#" method="post" id="discoverydetailsform">
                <input type="hidden" name="supp" value="<?= $is_supp ?>">
                <input type="hidden" name="supp_form_name" value="<?= $form_name ?>">
                <input type="hidden" name="form_id" value="<?= $form_id ?>">
                <input type="hidden" name="response_id" value="<?= $response_id ?>">
                <input type="hidden" name="case_id" value="<?= $case_id ?>">
                <input type="hidden" name="uid" value="<?= $uid ?>">
                <input type="hidden" name="discovery_id" value="<?= $discovery_id ?>">
                <input type="hidden" name="respond" value="<?= $respond ?>">
                <input type="hidden" name="discovery_verification" id="discovery_verification" value="<?= $discovery_verification ?>">
                <hr>
                <div class="row">
                <div id="loadinstructions"></div>
                <div class="col-md-12">
                    <ul class="list-group">
<?php
                    if (in_array($form_id, array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE))) {
                        foreach ($mainQuestions as $data) {
                            $dependent_answer       = "";
                            $question_id            = $data['question_id'];
                            $question_type_id       = $data['question_type_id'];
                            $have_main_question     = $data['have_main_question'];
                            $p_q_type_id            = $data['question_type_id'];
                            $question_title         = $data['question_title'];
                            $question_number        = $data['question_number'];
                            $sub_part               = $data['sub_part'];
                            $p_sub_part             = $data['sub_part'];
                            $is_pre_defined         = $data['is_pre_defined'];
                            $discovery_question_id  = $data['discovery_question_id'];
                            $is_depended_parent     = $data['is_depended_parent'];
                            $depends_on_question    = $data['depends_on_question'];
                            $question_no_makeid     = str_replace('.', '_', $question_number);
                            $has_extra_text         = $data['has_extra_text'];
                            $extra_text             = $data['extra_text'];
                            $extra_text_field_label = $data['extra_text_field_label'];

                            if ($response_id > 0) {
                                $getAnswers = $AdminDAO->getrows(
                                                            "response_questions",
                                                            "*",
                                                            "fkresponse_id				= :fkresponse_id AND
                                                            fkdiscovery_question_id 	= :discovery_question_id",
                                                            array(  "discovery_question_id"     =>  $discovery_question_id,
                                                            "fkresponse_id"             =>  $response_id)
                                                        );
                                $answer       = $getAnswers[0]['answer'];
                                $answer_time  = $getAnswers[0]['answer_time'];
                                $objection    = $getAnswers[0]['objection'];
                            } else {
                                $answer       = "";
                                $answer_time  = "";
                                $objection    = "";
                            }
                            /**
                            * IF Depends upon some question then we need that question answer
                            **/
                            if ($depends_on_question > 0 && $response_id > 0) {
                                $dependent_answer = getAnswerOfDependentParentQuestion($discovery_id, $depends_on_question, $response_id);
                            }
?>
							<li class='list-group-item <?= $depends_on_question ? "row_$depends_on_question" : "" ?>'
								style='<?= $depends_on_question && ($view != 1) && ( $dependent_answer == 'No' || $dependent_answer == '' ) ? " display:none;" : "" ?>'>
<?php
                                if ($question_type_id != 1) {
                                    $subQuestions   = $AdminDAO->getrows(
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

                                                                    "q.question_number  = :question_number AND
                                                                    q.id 				= dq.question_id  AND
                                                                    dq.discovery_id     = :discovery_id AND
                                                                    q.sub_part 			!= '' GROUP BY question_id",

                                                                    array(":question_number"=>$question_number,":discovery_id"=>$discovery_id)
                                                                );

                                    if( sizeof($subQuestions) ) {
                                        $subquestuions_string   = "";
                                        foreach ($subQuestions as $sub) {
                                            $sub_question_title         = $sub['question_title'];
                                            $sub_question_number        = $sub['question_number'];
                                            $sub_sub_part               = $sub['sub_part'];
                                            $subquestuions_string       .= " ($sub_sub_part) $sub_question_title ";
                                        }
                                    } else {
                                        $subquestuions_string = "";
                                    }
                                }
?>
                                <div class="form-group">
<?php
                                    if ($view == 1) {
?>
                                        <p>
                                            <b>Q No. <?= $question_number ?>: </b>
                                            <?= $question_title . $subquestuions_string ?>
                                        </p>
<?php
                                    } else { //Get
                                        if( ($question_number == "17.1" || $question_number == "217.1") /*&& $isconwithdiscoveryid != 0*/) {
                                            if( $isconwithdiscoveryid ) {
                                                $con_mainQuestions  = $AdminDAO->getrows(
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
                                                                        "q.id 				= dq.question_id  	AND
                                                                        rq.fkdiscovery_question_id	= dq.id		AND
                                                                        rq.answer			= 'Deny' 				AND
                                                                        dq.discovery_id = '$isconwithdiscoveryid'
                                                                        ORDER BY q.question_number
                                                                        "
                                                );
?>
                                                <h5><u>FORM INTERROGATORY NO. <?=  $question_number ?></u></h5>
<?php
                                                    if( sizeof($con_mainQuestions) ) {
?>
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                <div class="form-group">
<?php
                                                                    foreach ($con_mainQuestions as $con_question) {
                                                                        $con_discovery_question_id  = $con_question['discovery_question_id'];
                                                                        $con_response_id            = $con_question['fkresponse_id'];

                                                                        $query      = "SELECT * FROM question_admits qa
                                                                                            LEFT JOIN question_admit_results qar
                                                                                            ON  qar.discovery_question_id 	= '$con_discovery_question_id'  	AND
                                                                                            qar.question_admit_id			= qa.id AND qar.fkresponse_id			= '$con_response_id'";

                                                                        $con_SubQuestions   = $AdminDAO->executeQuery($query);

                                                                        foreach ($con_SubQuestions as $con_SubQuestion) {
?>
                                                                            <p><?= $con_SubQuestion['question_no'].". ".$con_SubQuestion['sub_answer'] ?></p>
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
                                                            <b>Q No. <?= $question_number ?>: </b>
                                                            <?= $question_title . $subquestuions_string ?>
                                                        </p>
                                                        <textarea required
                                                                style="background-color: antiquewhite;"
                                                                id="objection_<?= $discovery_question_id ?>"
                                                                class="form-control"
                                                                name="objection[<?= $discovery_question_id ?>]"
                                                                placeholder="Objection"><?=
                                                            html_entity_decode($objection)
                                                        ?></textarea>
<?php
                                                    }
                                            }
                                        } else { //Hassan Editings
?>
                                            <p>
                                                <b>Q No. <?= $question_number ?><?= $have_main_question==0 ? "&nbsp;($sub_part)" : "" ?>: </b>
<?php
                                                echo $question_title;
                                                if ($has_extra_text == 1) {
                                                    echo "<p><b>$extra_text_field_label: </b>$extra_text</p>";
                                                }
                                                if (in_array($question_type_id, array(1,2)) && $respond == 1) {
	                                                if( $objection == "" && $question_number == '12.2') {
		                                               $objection = "Objection, this interrogatory seeks information protected by the attorney work product privilege because it reflects counsel's evaluation of the case by revealing which witnesses counsel deemed important enough to interview. Nacht & Lewis Architects, Inc. v. Superior Court (1996) 47 Cal.App.4th 214, 217.";
	                                                }
?>
                                                    <textarea required
                                                            style="background-color: antiquewhite;"
                                                            id="objection_<?= $discovery_question_id; ?>"
                                                            class="form-control"
                                                            name="objection[<?= $discovery_question_id ?>]"
                                                            placeholder="Objection"><?=
                                                        html_entity_decode($objection)
                                                    ?></textarea>
<?php
                                                }
?>
                                                </p>
<?php
                                                if ($question_type_id == 1) {
?>
                                                    <input type="hidden"
                                                            name="have_main_question[<?= $discovery_question_id ?>]"
                                                            value="<?= $have_main_question ?>"/>
                                                    <textarea required
                                                            id="answer<?= $discovery_question_id ?>"
                                                            class="form-control" <?= $css ?>
                                                            name="answer[<?= $discovery_question_id ?>]"
                                                            placeholder="Your Answer"><?=
                                                        html_entity_decode($answer)
                                                    ?></textarea>
<?php
                                                } elseif( $question_type_id == 2 ) {
?>
                                                        <div class="form-check form-check-inline">
                                                            <label class="radio-inline">
                                                                <input type="radio" <?= ($answer == 'Yes') ? "checked" : "" ?>
                                                                       value="Yes" <?= $css ?>
                                                                       onClick="checkFunction('<?= $question_no_makeid ?>','1')<?=
                                                                            ($is_depended_parent ==1) ? ",showhidequestions('$question_id',1)" : "" ?>"
                                                                       name="answer[<?= $discovery_question_id ?>]">
                                                                Yes
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" <?= ($answer == 'No') ? "checked" : "" ?>
                                                                       value="No" <?= $css ?>
                                                                       onClick="checkFunction('<?= $question_no_makeid ?>','2')<?=
                                                                            ($is_depended_parent ==1) ? ",showhidequestions('$question_id',2)" : "" ?>"
                                                                       name="answer[<?= $discovery_question_id ?>]">
                                                                No
                                                            </label>
                                                        </div>
<?php
                                                }
                                                if( $question_type_id != 1 ) {
?>

                                                    <ul class="list-group"
                                                        <?= ($question_type_id == 2 && $answer != "Yes") ? ' style="display:none;" ' : '' ?>
                                                        id="subdiv<?= $question_no_makeid ?>">
<?php
                                                        foreach( $subQuestions as $data ) {
                                                            $question_id            = $data['question_id'];
                                                            $question_type_id       = $data['question_type_id'];
                                                            $form_id                = $data['form_id'];
                                                            $question_title         = $data['question_title'];
                                                            $question_number        = $data['question_number'];
                                                            $sub_part               = $data['sub_part'];
                                                            $is_pre_defined         = $data['is_pre_defined'];
                                                            $discovery_question_id  = $data['discovery_question_id'];
                                                            $have_main_question     = $data['have_main_question'];
                                                            if( $response_id ) {
                                                                $getAnswers = $AdminDAO->getrows( "response_questions", "*",
                                                                    								"fkresponse_id			= :fkresponse_id AND
																									fkdiscovery_question_id = :discovery_question_id",
                                                                    							   array( "discovery_question_id" =>  $discovery_question_id,
                                                                    									   "fkresponse_id"        =>  $response_id ) );
                                                                $answer1                = $getAnswers[0]['answer'];
                                                                $answer_time            = $getAnswers[0]['answer_time'];
                                                                $objection              = $getAnswers[0]['objection'];
                                                            } else {
                                                                $answer1                = "";
                                                                $answer_time            = "";
                                                                $objection              = '';
                                                            }
?>
                                                         <li class="list-group-item">
                                                            <div class="form-group">
                                                                <p>
                                                                    <b><?= $sub_part ?>)</b>&nbsp;<?= $question_title ?>
<?php
                                                                    if( $respond == 1 ) {
?>
                                                                    <textarea   style="background-color: antiquewhite;"
                                                                                id="objection_<?= $discovery_question_id ?>"
                                                                                class="form-control"
                                                                                name="objection[<?= $discovery_question_id ?>]"
                                                                                placeholder="Objection" ><?=
                                                                        html_entity_decode($objection)
                                                                    ?></textarea>
<?php
                                                                    }
?>
                                                                </p>
                                                                <input type="hidden"
                                                                        class="subanswer_<?= $question_no_makeid?>"
                                                                        name="have_main_question[<?= $discovery_question_id ?>]"
                                                                        value="<?= $have_main_question ?>"
                                                                        <?= ($answer == "No" || ($answer == "" && $p_q_type_id == 1) || ($answer == "" && $p_q_type_id == 2)) ? " disabled " : "" ?> />
                                                                <textarea required
                                                                            id="answer<?= $discovery_question_id ?>"
                                                                            class="form-control subanswer_<?= $question_no_makeid ?>" <?= $css ?>
                                                                            name="answer[<?= $discovery_question_id ?>]"
                                                                            <?= (($answer == "No" || $answer == "") && $p_q_type_id != 3) ? " disabled " : "" ?>
                                                                            placeholder="Your Answer" ><?=
                                                                    html_entity_decode($answer1)
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
                                    }
?>
                                </div>
                            </li>

<?php
                        }
                    } elseif ($form_id == Discovery::FORM_CA_RFAS) {
                        foreach ($mainQuestions as $data) {
?>
                            <li class="list-group-item">
<?php
                                $question_id            = $data['question_id'];
                                $question_type_id       = $data['question_type_id'];
                                $question_title         = $data['question_title'];
                                $question_number        = $data['question_number'];
                                $sub_part               = $data['sub_part'];
                                $is_pre_defined         = $data['is_pre_defined'];
                                $discovery_question_id  = $data['discovery_question_id'];

                                if ($response_id > 0) {
                                    $getAnswers                 = $AdminDAO->getrows(
                                        "response_questions",
                                        "*",
                                        "fkresponse_id				= :fkresponse_id AND
                                                                    fkdiscovery_question_id 	= :discovery_question_id",
                                        array(  "discovery_question_id"     =>  $discovery_question_id,
                                        "fkresponse_id"             =>  $response_id)
                                    );
                                    $answer                 = $getAnswers[0]['answer'];
                                    $answer_time            = $getAnswers[0]['answer_time'];
                                    $answer_detail          = $getAnswers[0]['answer_detail'];
                                    $objection              = $getAnswers[0]['objection'];
                                } else {
                                    $answer                 = "";
                                    $answer_time            = "";
                                    $answer_detail          = "";
                                    $objection              = "";
                                }
?>
                                <div class="form-group">
                                    <p>
                                        <b>Q No. <?= $question_number ?>: </b><?= $question_title ?>
<?php
                                        if ($respond == 1) {
?>
                                        <textarea required
                                                style="background-color: antiquewhite;"
                                                id="objection_<?= $discovery_question_id ?>"
                                                class="form-control"
                                                name="objection[<?= $discovery_question_id ?>]"
                                                placeholder="Objection"><?=
                                            html_entity_decode($objection)
                                        ?></textarea>
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
                                        $question_admit_id = $generalQuestion['id'];
                                        $subQuestionAnswers = $AdminDAO->getrows('question_admit_results', "*", ":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id AND fkresponse_id = :fkresponse_id", array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id,"fkresponse_id" => $response_id));
                                        $subQuestionAnswer = $subQuestionAnswers[0];

                                        if ($question_admit_id == 1) {
                                            $sub_answer_show    = $question_number;
                                        } else {
                                            $sub_answer_show    = trim(html_entity_decode($subQuestionAnswer['sub_answer']));
                                        }
                                        $sub_objection              = trim(html_entity_decode($subQuestionAnswer['objection']));
?>
                                    <li class="list-group-item">
                                    <div class="form-group">
                                        <p><b><?= $generalQuestion['question_no'] ?>) </b><?= $generalQuestion['question'] ?></p>
                                        <textarea required
                                            style="background-color: antiquewhite; <?= ($question_admit_id == 1) ? 'display:none;' : '' ?>"
                                            <?= ($answer == "Admit" || $answer == "") ? " disabled " : "" ?>
                                            id="sub_objection<?= $discovery_question_id.'_'.$question_admit_id ?>"
                                            class="form-control subanswer_<?= $discovery_question_id ?>" <?= $css ?>
                                            name="rfa_objection[<?= $discovery_question_id ?>][<?= $question_admit_id ?>]"
                                            placeholder="Objection"><?=
                                                $sub_objection
                                        ?></textarea>
                                        <br />
                                        <textarea required
                                            <?= ($answer == "Admit" || $answer == "") ? "disabled" : "" ?>
                                            <?= ($question_admit_id == 1) ? "readonly" : "" ?>
                                            id="subanswer<?= $discovery_question_id.'_'.$question_admit_id ?>"
                                            class="form-control subanswer_<?= $discovery_question_id;?>" <?= $css ?>
                                            name="subanswer[<?= $discovery_question_id ?>][<?= $question_admit_id; ?>]"
                                            placeholder="Your Answer" ><?=
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
                    } elseif (in_array($form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RPDS))) {
                        foreach ($mainQuestions as $data) {
?>
                            <li class="list-group-item">
<?php
                                $question_id        = $data['question_id'];
                                $question_type_id   = $data['question_type_id'];
                                $question_title     = $data['question_title'];
                                $question_number    = $data['question_number'];
                                $sub_part           = $data['sub_part'];
                                $is_pre_defined     = $data['is_pre_defined'];
                                $discovery_question_id  = $data['discovery_question_id'];
                                if ($response_id > 0) {
                                    $getAnswers                 = $AdminDAO->getrows(
                                        "response_questions",
                                        "*",
                                        "fkresponse_id				= :fkresponse_id AND
                                                                        fkdiscovery_question_id 	= :discovery_question_id",
                                        array(  "discovery_question_id"     =>  $discovery_question_id,
                                        "fkresponse_id"             =>  $response_id)
                                    );
                                    $answer                 = $getAnswers[0]['answer'];
                                    $answer_time            = $getAnswers[0]['answer_time'];
                                    $answer_detail          = $getAnswers[0]['answer_detail'];
                                    $objection              = $getAnswers[0]['objection'];
                                } else {
                                    $answer                 = "";
                                    $answer_time            = "";
                                    $answer_detail          = "";
                                    $objection              = "";
                                }
?>
                                <div class="form-group">
                                    <p>
                                        <b>Q No. <?= $question_number ?>: </b><?= $question_title ?>
<?php
                                        if ($respond == 1) {
?>
                                        <textarea required
                                                  style="background-color: antiquewhite;"
                                                  id="objection_<?= $discovery_question_id ?>"
                                                  class="form-control"
                                                  name="objection[<?= $discovery_question_id ?>]"
                                                  placeholder="Objection" ><?=
                                            html_entity_decode($objection)
                                        ?></textarea>
<?php
                                        }
?>
                                    </p>
<?php
                                    if( !$view ) {
                                        if($form_id == Discovery::FORM_CA_RPDS ) {
?>
                                        <select class="form-control" <?= $css ?>
                                                id="answer<?= $discovery_question_id ?>"
                                                name="answer[<?= $discovery_question_id ?>]"
                                                onChange="checkFunctionForm5('<?= $discovery_question_id ?>',this.value)">
                                            <option <?= ($answer == Discovery::RPDS_ANSWER_NONE              ) ? "selected" : "" ?>>Select Your Response</option>
                                            <option <?= ($answer == Discovery::RPDS_ANSWER_HAVE_DOCS         ) ? "selected" : "" ?>>I have responsive documents</option>
                                            <option <?= ($answer == Discovery::RPDS_ANSWER_DOCS_NEVER_EXISTED) ? "selected" : "" ?>>Responsive documents have never existed</option>
                                            <option <?= ($answer == Discovery::RPDS_ANSWER_DOCS_DESTROYED    ) ? "selected" : "" ?>>Responsive documents were destroyed</option>
                                            <option <?= ($answer == Discovery::RPDS_ANSWER_DOCS_NO_ACCESS    ) ? "selected" : "" ?>>Responsive documents were lost, misplaced, stolen, or I lack access to them</option>
                                        </select>
                                        <script>window.requestAnimationFrame( _ => checkFunctionForm5('<?= $discovery_question_id ?>',$('#answer<?= $discovery_question_id ?>').val()) )</script>
<?php
                                        } elseif( $form_id == Discovery::FORM_CA_SROGS ) {
?>
                                            <textarea required
                                                        id="answer<?= $discovery_question_id ?>"
                                                        class="form-control " <?= $css ?>
                                                        name="answer[<?= $discovery_question_id ?>]"
                                                        placeholder="Your Answer"  ><?=
                                                html_entity_decode($answer)
                                            ?></textarea>
<?php
                                        }
                                        if( $form_id == Discovery::FORM_CA_RPDS ) {
?>
                                            <ul class="list-group" id="subdiv<?= $discovery_question_id ?>" <?= $answer != Discovery::RPDS_ANSWER_DOCS_NO_ACCESS ? " style='display:none' " : "" ?>>
                                                <li class="list-group-item">
                                                <div class="form-group">
                                                    <p>
                                                        <?= Discovery::RPDS_DETAIL_QUESTION ?>
                                                    </p>
                                                    <textarea required
                                                                id="subanswer<?= $discovery_question_id ?>"
                                                                class="form-control" <?= $css ?>
                                                                name="subanswer[<?= $discovery_question_id ?>]"
                                                                <?= $answer != Discovery::RPDS_ANSWER_DOCS_NO_ACCESS ? " disabled " : "" ?>
                                                                placeholder="Your Answer"><?=
                                                        html_entity_decode($answer_detail)
                                                    ?></textarea>
                                                </div>
                                                </li>
                                            </ul>
                                            <ul class="list-group" id="subdiv-have-docs<?= $discovery_question_id ?>" <?= ($answer != Discovery::RPDS_ANSWER_HAVE_DOCS) ? " style='display:none' " : "" ?>>
                                                <li class="list-group-item">
                                                <div class="form-group">
                                                    <p>
                                                        <?= Discovery::RPDS_HAVE_DOCS_DETAIL_QUESTION ?>
                                                    </p>
                                                    <textarea required
                                                                id="subanswer-have-docs<?= $discovery_question_id ?>"
                                                                class="form-control" <?= $css ?>
                                                                name="subanswer[<?= $discovery_question_id ?>]"
                                                                <?= ($answer != Discovery::RPDS_ANSWER_HAVE_DOCS) ? " disabled " : "" ?>
                                                                placeholder="Your Answer"><?=
                                                        html_entity_decode($answer_detail)
                                                    ?></textarea>
                                                    <p>
                                                        <b>Note: </b>
                                                        Upload your documents below.
                                                    </p>
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
                if( in_array($form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS)) && sizeof($olddocuments) ) {
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
                $formArray = array(Discovery::FORM_CA_RPDS);// Discovery::FORM_CA_SROGS,Discovery::FORM_CA_RFAS,Discovery::FORM_CA_RPDS
                if( in_array($form_id, $formArray) && $view != 1 ) {
?>
                <div class="col-md-12">
                    <hr>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="form-group">
                                <p><b>Upload your documents here.</b></p>
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
                if ($view != 1) {
?>
                <a href="javascript:;" class="btn btn-purple" onclick="serveFunction2('<?= $discovery_verification ?>','<?= $_GET['id'] ?>','<?= $response_id ?>')"  ><i class="fa fa-share"></i> Serve</a>
                <button type="button" class="btn btn-info buttonid" data-style="zoom-in" onclick="checkClientEmailFound('<?= $discovery_id ?>',2);" title="" >
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
    <aside class="sidebar right"><div class="fixed"></div></aside>
</div>

<div class="modal fade" id="serve_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:450px !important">
    <div class="modal-content" style="width: 586px">
        <div class="modal-body" id="loadmodalcontent">
            <div class="swal-icon swal-icon--warning">
                <span class="swal-icon--warning__body"><span class="swal-icon--warning__dot"></span></span>
            </div>
            <div class="swal-title" style="font-size:18px">Responses aren't verified.</div>
            <form id="deleteform" name="deleteform">
                <div class="swal-footer">
                    <div class="swal-button-container">
                        <button class="swal-button swal-button--confirm swal-button--success btn-success" type="button" onclick="callemailclientmodal()" ><i class="fa fa-user "></i> Ask Client to Verify </button>
                    </div>
                    <div class="swal-button-container">
                        <button class="swal-button swal-button--confirm swal-button--info" type="button" onclick="callFinalDraftModal('<?= $_GET['id'] ?>','<?= $discovery_verification ?>','<?= $response_id ?>')" ><i class="fa fa-share"></i> Serve Anyway </button>
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
<div class="modal fade" id="emailclientmodal" tabindex="-1" role="dialog" aria-hidden="true">
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
        <textarea type="text" rows="10" name="message_to_client" class="form-control" id="message_to_client"><?=
"Hi,

You have not verify the discovery SPECIAL INTERROGATORIES. Please click on the link below and verify your discovery.

~LINK_HERE~."

        ?></textarea>
        </div>
      </div>
      <div class="modal-footer">
         <i id="msgEmailClientModal" style="color:red"></i>
         <button type="button" onclick="serveaction(1,<?= $discovery_id ?>,'#message_to_client')" class="btn btn-success"><i class="fa fa-share"></i> Send</button>
         <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
      </div>
    </div>
  </div>
</div>

<div id="finaldraft_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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

<div id="value-edit_modal" class="modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:13px !important">
        <h5 class="modal-title text-center">Edit response name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -50px;font-size: 35px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text" name="edited_form_name" class="form-control m-b" value="">
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" onclick="formTitle_Save()">Change</button>
        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
function formTitle_Revert() { //debugger;
    const orgValue = '<?= $form_name ?>',
          $input = $(`#form_title`)
    $(`[name='supp_form_name']`).val( orgValue )
    $input.val( orgValue ); debugger;
    $input[0].focus({preventScroll: false})
}
function formTitle_Edit() { //debugger;
    const value = $(`[name='supp_form_name']`).val(),
          regex = /<?= preg_quote($setSuffix,'/') ?>$/,
          $input = $(`#form_title`)
    $input.val( value.replace( regex,'') )
    $input[0].scrollIntoView({behavior: "auto", block: "center", inline: "nearest"})
}
function formTitle_Save() { //debugger;
    const $input = $(`#form_title`)

    let value = $input.val()
    console.assert( $input.length, `Something wrong here`, {$input,value})
    if( value && value.trim() ) {
        if( !value.endsWith('<?= $setSuffix ?>')) {
            value = value + '<?= $setSuffix ?>'
        }
        $(`[name='supp_form_name']`).val( value )
    } else { // revert value
        toastr.warning(`Sorry, this field cannot be empty`)
        value = $(`[name='supp_form_name']`).val()
    }
    $(`#form_title`).val( value )
}
</script>

<?php
include_once(SYSTEMPATH.'application/client-email-found_modal.php');
include_once(SYSTEMPATH.'application/client_instructions_modal.php');
//include_once(SYSTEMPATH.'jsinclude.php');
?>
<script>

const RPDS_ANSWER_NONE               = '<?= Discovery::RPDS_ANSWER_NONE ?>'
const RPDS_ANSWER_HAVE_DOCS          = '<?= Discovery::RPDS_ANSWER_HAVE_DOCS ?>'
const RPDS_ANSWER_DOCS_NEVER_EXISTED = '<?= Discovery::RPDS_ANSWER_DOCS_NEVER_EXISTED ?>'
const RPDS_ANSWER_DOCS_DESTROYED     = '<?= Discovery::RPDS_ANSWER_DOCS_DESTROYED ?>'
const RPDS_ANSWER_DOCS_NO_ACCESS     = '<?= Discovery::RPDS_ANSWER_DOCS_NO_ACCESS ?>'

function sendtoclientfunction( discovery_id, actiontype, notesElement='' ) {
    const notes_for_client = notesElement && $(notesElement).val().trimEnd() || "";
    $.LoadingOverlay("show");
    setTimeout( _ => {
        $.post( "discoveryfrontaction.php", $("#discoverydetailsform").serialize() )
            .done( _ => {
                $.post( "messagetoclientforverification.php", { discovery_id, actiontype, notes_for_client } )
                    .done( data => {
                        $("#discovery-client-instructions_modal").modal('hide');
                        $.LoadingOverlay("hide");
                        response(data);
                    } );
            } );
    }, 2000);
}

function checkClientEmailFound( discovery_id, actiontype ) {
    $.post( "checkclientemailfound.php", { discovery_id, actiontype } )
        .done( data => {
            const obj = JSON.parse(data);
            if( obj.found ) {
                if( actiontype == 2 ) {
                    $('#client-instructions-send').on('click', _ => sendtoclientfunction(discovery_id,2,'#notes_for_client') );
                    $('#discovery-client-instructions_modal').modal('show');
                } else {
                    sendtoclientfunction( $discovery_id, actiontype );
                }
            }
            else {
                callclientemailmodal(obj.discovery_id,obj.actiontype);
            }
        });
}
function saveclientemail() {
    var client_email = $("#client_email").val();
    $("#msgAddEmailClientModal").html("");
    if( client_email ){
        $.post("saveclientemailaction.php", $("#addClientEmailModal").serialize() )
            .done( data => {
                var obj = JSON.parse(data);
                $('#client-email-found_modal').modal('show');
                sendtoclientfunction(obj.discovery_id,obj.actiontype);
        });
    }
    else {
        $("#msgAddEmailClientModal").html("Please enter client email.");
    }
}
function callemailclientmodal()
{
    $("#serve_modal").modal("toggle");
    serveaction(1,<?= $discovery_id ?>);
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
function loadinstructions( form_id, id ) {
    var type = '<?= $type ?>';
    $.get(`discoveryloadforminstruction.php?form_id=${form_id}&id=${id}&case_id=<?= $case_id ?>&viewonly=1&type=${type}`)
        .done( resp => {
            $("#loadinstructions").html( trim(resp) );

            const   type = <?= $respond ?: 0 ?> ? 2 : 1,
                    { discoveryFormNames, discoveryForm, } = globalThis,
                    suffix = (discoveryForm ? '@' + discoveryFormNames[discoveryForm-1] : '');
            ctxUpdate({ id: `49_${type}${suffix}`, pkscreenid: '49', url: 'discoverydetails.php', } );

            enableCKEditor('textarea#instruction');
        });
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

function checkFunctionForm5(subdivid, option) {
	$(`#subdiv${subdivid}, #subdiv-have-docs${subdivid}`).hide();
	$(`#subanswer${subdivid}, #subanswer-have-docs${subdivid}`).prop('disabled', true);

	option = String(option).trim();

	switch (option) {
		case RPDS_ANSWER_HAVE_DOCS:
			$("#subdiv-have-docs"+subdivid).show();
			$("#subanswer-have-docs"+subdivid).prop('disabled',false);
		break;
		case RPDS_ANSWER_DOCS_NO_ACCESS:
			$("#subdiv"+subdivid).show();
			$("#subanswer"+subdivid).prop('disabled',false);
		break;
	}
}

jQuery( $ => {
    setTimeout( _ => loadToolTipForClientBtn('<?= $responding ?>'), 500 );
} );

function loaduploadeddocs() {
    var rp_uid  = '<?= $uid ?>';
    $("#uploadeddocs").load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype=1");
}
function loaduploaddiscoverydocs() {
    var rp_uid  = '<?= $uid ?>';
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
function serveaction( actiontype, discovery_id, notesElement='' ) { //actiontype:1 => Email Client, actiontype:2 => Serve
    const notes_for_client = notesElement && $(notesElement).val().trimEnd() || "";
    $.post( "messagetoclientforverification.php", { discovery_id, actiontype, notes_for_client } )
        .done( data => {
            $("#discovery-client-instructions_modal").modal('hide');
            response(data);
        } );
}
function serveFunction2(is_verified,discovery_id,response_id) {
    if(!is_verified)
    {
        setTimeout(function(){ $("#serve_modal").modal("toggle");  }, 1000);
    }
    else
    {
        //alert("Go to Final Draft (In progress)");
        callFinalDraftModal('<?= $_GET['id'] ?>',is_verified,response_id);
        //serveaction(2,discovery_id);
    }
}
function callFinalDraftModal(discovery_id,is_verified,response_id)
{
    $.post( "discoveryfrontaction.php",  $("#discoverydetailsform").serialize()).done(function( data )
    {
        var obj = JSON.parse(data);
        response_id = obj.response_id;
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
        var response_id = obj.response_id;
        var messagetype = obj.messagetype;
        if(messagetype == "success")
        {
            $('#finaldraft_modal').modal('toggle');
            PopupForPOS('<?= $discovery_id ?>',response_id);
        }
    });
}
function PopupForPOS(discovery_id,response_id) {
    $("#load_general_modal_content").html('');
    $.post( "loadpospopupcontent.php", {id: discovery_id,respond:1,response_id})
        .done( data => {
            $("#load_general_modal_content").html(data);
        } );
    $('#general_modal_title').html("PROOF OF ELECTRONIC SERVICE");
    $('#general-width').addClass('w-900');
    setTimeout( _ => $('#general_modal').modal('show'), 2000);

}
function writeDiscoveryPDF(uid) {
    $.get( "makepdf.php", { id: uid, downloadORwrite: 1, view:0 })
        .done( data => {} );
}

jQuery( $ => {
<?php
    if( in_array($form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS)) ) {
?>
        loaduploaddiscoverydocs();
<?php
    }
?>
    loadinstructions('<?= $form_id ?>','<?= $discovery_id ?>');
    loaduploadeddocs();
    const extraObj = $("#extraupload")
        .uploadFile( {
            url: "frontdocumentuploads.php",
            fileName: "myfile",
            extraHTML: _ => {
                const html = `<div>
                                <b style='display:none'>Document Purpose:</b>
                                <input type='hidden' class='form-control' name='doc_purpose' value='' />
                                <input type='hidden' name='rp_uid' value='<?= $uid ?>' /><br/>
                            </div>`;
                return html;
            },
            autoSubmit: false,
            afterUploadAll: obj => {
                $(".ajax-file-upload-container").html("");
                loaduploadeddocs();
            }
        } );
    $("#extrabutton").click( _ =>  {
        extraObj.startUpload();
    } );

	autogrowTextareas()

} );
</script>
