<?php
require_once __DIR__ . '/../bootstrap.php';

include_once("../library/classes/functions.php");

$logger->info("MakePDF: starting");

$respond			= 0;
$uid				= @$_GET['id'];
$view				= $_GET['view'] ?: 0;
$downloadORwrite	= @$_GET['downloadORwrite'] ?: 0;
$response_id		= @$_GET['response_id'];

/**************************************
        SETTING DATA
***************************************/
$setting_details	= $AdminDAO->getrows('system_setting','*','pksettingid = 1');
$setting_email		= $setting_details[0]['email'];

/***************************************
        Query For Header Data
****************************************/
$discoveryDetails	= $AdminDAO->getrows('discoveries d,cases c,forms f',
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
                                            d.attorney_id as discovery_attorney_id,
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
                                            "d.uid 		= :uid AND
                                            d.case_id 	= c.id AND
                                            d.form_id	= f.id ",
                                            array(":uid"=>$uid)
                                        );
$discovery_data	= $discoveryDetails[0];

$propounding			= $discovery_data['propounding'];
$responding				= $discovery_data['responding'];
$proponding_attorney	= $discovery_data['proponding_attorney'];
$case_id				= $discovery_data['case_id'];

// Sides ---------------
$masterhead = '';

$users = new User();
$sides = new Side();

$attorneyId = $response_id ? null : $proponding_attorney;
$clientId 	= $response_id ? $responding : $propounding;
$user = $attorneyId ? $users->getByAttorneyId($attorneyId) : null;
$signingClient 	 = $clientsModel->find($clientId);
$signingSide     = $user ?
                      $sides->getByUserAndCase($user['pkaddressbookid'], $case_id)
                    : $sides->getByClientAndCase($clientId, $case_id);
$signingAttorney = $user ? $user : $sides->getPrimaryAttorney($signingSide['id']);

Side::legacyTranslateCaseData(
    $case_id,
    $discovery_data,
    $signingAttorney['pkaddressbookid'] // !! will use this attorney's side data
);

if ($signingSide){
    $masterhead = $sides->getMasterHead($signingSide);
}
$masterhead = $masterhead ? $masterhead : $users->getMasterHead($signingAttorney); // if the side doesnt have a masthead yet...
// --------------------

$plaintiff		= $discovery_data['plaintiff'];
$defendant		= $discovery_data['defendant'];
$case_title		= $discovery_data['case_title'];
$discovery_id	= $discovery_data['discovery_id'];
$case_number	= $discovery_data['case_number'];
$jurisdiction	= $discovery_data['jurisdiction'];
$judge_name		= $discovery_data['judge_name'];
$county_name	= $discovery_data['county_name'];
$court_address	= $discovery_data['court_address'];
$department		= $discovery_data['department'];

$form_id		= $discovery_data['form_id'];
$set_number		= $discovery_data['set_number'];
$case_attorney	= $discovery_data['case_attorney'];

$discovery_attorney_id = $discovery_data['discovery_attorney_id'];

$form_name				= $discovery_data['form_name']." [SET ".$set_number."]";
$short_form_name		= $discovery_data['short_form_name'];

$send_date				= $discovery_data['send_date'];
$instructions			= $discovery_data['instructions'];
$introduction			= $discovery_data['introduction'];
$propounding_uid		= $discovery_data['propounding_uid'];
$responding_uid			= $discovery_data['responding_uid'];
$propounding			= $discovery_data['propounding'];
$responding				= $discovery_data['responding'];
$type					= $discovery_data['type'];
$discovery_name			= $discovery_data['discovery_name'];
$incidentoption			= $discovery_data['incidentoption'];
$incidenttext			= $discovery_data['incidenttext'];
$personnames2			= $discovery_data['personnames2'];
$personnames1			= $discovery_data['personnames1'];
$interogatory_type		= $discovery_data['interogatory_type'];
$conjunction_setnumber	= $discovery_data['conjunction_setnumber'];
$in_conjunction			= $discovery_data['in_conjunction'];
$declaration_text		= $discovery_data['declaration_text'];
$declaration_updated_by	= $discovery_data['declaration_updated_by'];
$declaration_updated_at	= $discovery_data['declaration_updated_at'];
$proponding_attorney	= $discovery_data['proponding_attorney'];


if( $response_id ) {
    $responseDetails	= $AdminDAO->getrows("responses","*","id = :id",array(":id"=>$response_id));
    $responseDetail		= $responseDetails[0];

    $is_served				= $responseDetail['isserved'];
    $served					= $responseDetail['servedate'];
    $submit_date			= $responseDetail['submit_date'];
    $is_submitted			= $responseDetail['is_submitted'];
    $pos_text				= $responseDetail['postext'];
    $served_date			= date("F d, Y",strtotime($served));
    $res_created_by			= $responseDetail['created_by'];
    $is_verified			= $responseDetail['discovery_verification'];
    $verification_text		= $responseDetail['discovery_verification_text'];
    $verification_state		= $responseDetail['verification_state'];
    $verification_city		= $responseDetail['verification_city'];
    $verification_by_name	= $responseDetail['verification_by_name'];
    $verification_datetime	= $responseDetail['verification_datetime'];
    $verification_signed_by	= $responseDetail['verification_signed_by'];
}
else {
    $is_served			= $discovery_data['is_served'] || $discovery_data['served'];
    $served				= $discovery_data['served'];
    $submit_date		= $discovery_data['send_date'];
    $is_submitted		= $discovery_data['is_send'];
    $pos_text			= $discovery_data['pos_text'];
    $served_date		= date("F d, Y",strtotime($served));

    $is_verified			= "";
    $verification_text		= "";
    $verification_state		= "";
    $verification_city		= "";
    $verification_by_name	= "";
    $verification_datetime	= "";
    $verification_signed_by	= "";
}

if( $type == Discovery::TYPE_EXTERNAL) {
    $is_served		= $discovery_data['is_served'] || $discovery_data['served'];
    $pos_text		= $discovery_data['pos_text'];
    $submit_date	= $discovery_data['submit_date'];
    $served			= $discovery_data['served'];
    $served_date	= date("F d, Y",strtotime($served));
}

$form_name = strtoupper( ($view == Discovery::VIEW_RESPONDING ? "RESPONSE TO " : "") . $discovery_name ." [SET ".numberTowords( $set_number )."]" ); // TODO Disc+Set

$propondingdetails	= getRPDetails($propounding);
$proponding_name	= $propondingdetails['client_name'];
$proponding_email	= $propondingdetails['client_email'];
$proponding_type	= $propondingdetails['client_type'];
$proponding_role	= $propondingdetails['client_role'];

$respondingdetails	= getRPDetails($responding);
$responding_name	= $respondingdetails['client_name'];
$responding_email	= $respondingdetails['client_email'];
$responding_type	= $respondingdetails['client_type'];
$responding_role	= $respondingdetails['client_role'];

if( $type == Discovery::TYPE_EXTERNAL) {
    if( $response_id > 0 ) {
        $whereAt	= "pkaddressbookid = '$res_created_by'";
    } 
    else {
        $discovery_created_by = $case_attorney;//$discovery_data['attorney_id'];
        $whereAt			  = "pkaddressbookid = '$discovery_created_by'";
    }

    $getAttorneyDetails = $AdminDAO->getrows('system_addressbook',"*",$whereAt,array());
    $getAttorneyDetail  = $getAttorneyDetails[0];
}
else {
    assert( $type == Discovery::TYPE_INTERNAL, "INVALID \$type=$type" );

    $where_attorneyDetails	= "";
    if( $view == Discovery::VIEW_PROPOUNDING ) {
        $c_client_id = $propounding;
        if( $proponding_attorney ) {
            $where_attorneyDetails = " AND a.id = $proponding_attorney";
        }
    } 
    else {
        assert( $view == Discovery::VIEW_RESPONDING, "INVALID \$view=$view" );
        $c_client_id = $responding;
    }
    $attorneyDetails	= $AdminDAO->getrows('attorney a,client_attorney ca,system_addressbook sa',"*","sa.email = a.attorney_email AND ca.client_id = :client_id AND a.id = ca.attorney_id AND ca.case_id = :case_id $where_attorneyDetails",array('client_id'=>$c_client_id,'case_id'=>$case_id));
    $getAttorneyDetail	= $attorneyDetails[0];
}

$atorny_name	= User::getFullName( $getAttorneyDetail );
$atorny_email	= $getAttorneyDetail['email'];
$atorny_address	= $getAttorneyDetail['address'];
$atorny_city	= $getAttorneyDetail['cityname'];
$atorny_zip		= $getAttorneyDetail['zip'];
$atorny_street	= $getAttorneyDetail['street'];
$atorny_phone	= $getAttorneyDetail['phone'];
$fkstateid		= $getAttorneyDetail['fkstateid'];
$atorny_firm	= $getAttorneyDetail['companyname'];
$attorney_info	= $getAttorneyDetail['attorney_info'];

$getState		= $AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
$atorny_state	= $getState[0]['statename'];
$atorny_state_short	= $getState[0]['statecode'];

/**
* Check to see login attorney is responding party attorney or not
**/

$att_for_client_name  = $signingClient['client_name'];
$att_for_client_email = $signingClient['client_email'];
$att_for_client_role  = $signingClient['client_role'];

/****************************************************
Function for getting responding or proponding details
****************************************************/
function getRPDetails( $id ) {
    global $AdminDAO;
    $clients = $AdminDAO->getrows("clients", "*", "id = :id", array(":id"=>$id) );
    return $clients[0];
}

/**************************************
        All Attoney's of this case
***************************************/
$allotherattornies = $AdminDAO->getrows('clients', "other_attorney_name,other_attorney_email", "case_id = '$case_id' AND client_type = 'Others'");

/***************************************
    Query For Forms 1,2,3,4,5 Questions
****************************************/
if( in_array($form_id,array(3,4,5)) ) {
    $orderByMainQuestions = "  ORDER BY CAST(question_number as DECIMAL(10,2)), q.question_number ";
} 
else{
    $orderByMainQuestions = "  ORDER BY display_order, q.id ";
}
$mainQuestions = $AdminDAO->getrows(	'discovery_questions dq,questions q',

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
                                        
                                        "q.id = dq.question_id  AND
                                        dq.discovery_id = '$discovery_id' AND (
                                            q.sub_part = '' OR
                                            q.sub_part IS NULL OR
                                            have_main_question IN (0,2)
                                        )
                                        GROUP BY q.id
                                        $orderByMainQuestions
                                    " );
$generalQuestions = $AdminDAO->getrows('question_admits',"*");

/************************************************
    Discovery Conjuction with some RFA or not
************************************************/

$isconwithdiscoveryid = 0;
if( in_array($form_id,array(1,2)) ) {
    $isConWithDiscovery = $AdminDAO->getrows(   'discoveries',"*",
                                                "propounding		= 	'$propounding' AND
                                                responding 			= '$responding' AND
                                                case_id				= '$case_id' AND
                                                interogatory_type	= '$form_id' AND
                                                conjunction_setnumber 	= 	'$set_number'");
    if( sizeof($isConWithDiscovery) ) {
        $isconwithdiscoveryid 	= $isConWithDiscovery[0]['id'];
        $conjunction_setnumbers	= $isConWithDiscovery[0]['conjunction_setnumber'];
        if( $is_served ) {
            $con_Details	= array("con_discovery_name" => "REQUESTS FOR ADMISSION", "con_setnumber" => $conjunction_setnumbers);
        }
    }
}
if(in_array($form_id,array(4))) {
    if($interogatory_type == 1)
    {
        $con_discovery	= "FORM INTERROGATORIES - GENERAL";
    }
    else if($interogatory_type == 2)
    {
        $con_discovery	= "FORM INTERROGATORIES - EMPLOYMENT LAW";
    }
    if($in_conjunction == 1)
    {
        $con_Details	= array("con_discovery_name" => $con_discovery, "con_setnumber" => $conjunction_setnumber);
    }
}

ob_start();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr"><!-- makepdf.php -->
<head>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style>
    .tabela	{
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
   td {
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
</head>
<!-- =================================================== 	-->
<!-- 			HEADER PAGE 								-->
<!-- =================================================== 	-->

<?php include_once('pdf-header.php');?>
<br/>

<!-- ===================================================	-->
<!-- 			QUESTIONS PAGE 								-->
<!-- ===================================================	-->
<p class="break-page1"></p>
<div class="wikitable1 tabela1">
<?php
        if( in_array( $form_id, array(1,2) ) ) {
            foreach( $mainQuestions as $data ) {
                $dependent_answer	= "";
                $question_id 			= $data['question_id'];
                $question_type_id 		= $data['question_type_id'];
                $question_title 		= $data['question_title'];
                $question_number 		= $data['question_number'];
                $sub_part 				= $data['sub_part'];
                $is_pre_defined 		= $data['is_pre_defined'];
                $discovery_question_id	= $data['discovery_question_id'];
                $is_depended_parent		= $data['is_depended_parent'];
                $depends_on_question	= $data['depends_on_question'];
                $has_extra_text			= $data['has_extra_text'];
                $extra_text				= $data['extra_text'];
                $extra_text_field_label	= $data['extra_text_field_label'];

                if( $response_id ) {
                    $getAnswers = $AdminDAO->getrows(	"response_questions", "*",
                                                        "fkresponse_id = :fkresponse_id AND
                                                        fkdiscovery_question_id = :discovery_question_id",

                                                        array( 	"discovery_question_id" => $discovery_question_id,
                                                                "fkresponse_id"			=> $response_id )
                                                    );
                    $answer 		= trim($getAnswers[0]['answer']);
                    $answer_time 	= $getAnswers[0]['answer_time'];
                    $objection 		= trim($getAnswers[0]['objection']);
                    $final_response	= trim($getAnswers[0]['final_response']);
                } 
                else {
                    $answer 			= "";
                    $answer_time 		= "";
                    $objection 			= "";
                    $final_response 	= "";
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

                if( $question_type_id ) { // change by Hassan for sub elements
                    $subQuestions = $AdminDAO->getrows( 
                                        'discovery_questions dq,questions q',
                                        'dq.id as discovery_question_id,
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

                    if( sizeof($subQuestions) && $view == Discovery::VIEW_PROPOUNDING ) {
                        $subquestuions_string = "";
                        foreach($subQuestions as $sub) {
                            $sub_question_title 		= $sub['question_title'];
                            $sub_question_number 		= $sub['question_number'];
                            $sub_sub_part 				= $sub['sub_part'];
                            $subquestuions_string		.= " (".$sub_sub_part.") ".$sub_question_title." ";
                        }
                        echo $subquestuions_string;
                    }
                    else {
                        $subquestuions_string	= "";
                    }
                }
                echo "	<div class='q-row'>
                            <h3>INTERROGATORY NO. $question_number:</h3>";
                if( $view == Discovery::VIEW_RESPONDING && $question_type_id == 3 ) {
                }
                else {
                    if( $view == Discovery::VIEW_RESPONDING ) { 
                        echo "	<b><u>Interrogatory</u></b>"; 
                    }
                    echo "	<p class='q-subquestion'> $question_title $subquestuions_string </p>";
                    if( $has_extra_text ) {
                        echo "	<br/>
                                <p class='q-extra'>
                                    <b>$extra_text_field_label: </b><br/>
                                    $extra_text
                                </p>";
                    }
                }
                if( $view == Discovery::VIEW_PROPOUNDING ) {
                    if( $respond ) {
                        echo "	<b><u>Objection</u></b>
                                <p class='q-objection'> $objection </p>
                        ";
                    }
                } 
                else {
                    if( $question_type_id == 1 || ( $question_type_id == 3 && !sizeof($subQuestions) ) ) {
                        echo "	<br/>
                                <b><u>Response</u></b>
                            ";
                        if( $final_response ) {
                            echo "	<p class='q-response'> $final_response </p><br/>";
                        }
                        else {
                            echo "	<p class='q-response'>". finalResponseGenerate( $objection, $answer ) ."</p><br/>";
                        }
                    }
                    else if( $question_type_id == 2 ) {
                        echo "	<br/>
                                <b><u>Response</u></b>";
                                if( $final_response ) {
                                    echo "<p class='q-response'> $final_response </p><br/>";
                                }
                                else {
                                    enforceYesNo( $answer );
                                    echo "	<p class='q-response'>". finalResponseGenerate( $objection, $answer ) ."</p><br/>";
                                }
                    }
                    if( $question_type_id != 1) {
                        if( in_array( $question_number,array('17.1','217.1') ) && $isconwithdiscoveryid ) {
                            $con_mainQuestions	= $AdminDAO->getrows(
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

                                                            "q.id 				= 	dq.question_id  AND
                                                            rq.fkdiscovery_question_id	= dq.id	AND
                                                            rq.answer			= 'Deny' 			AND
                                                            dq.discovery_id = '$isconwithdiscoveryid'
                                                            ORDER BY q.question_number"
                                                            );
                            if( sizeof($con_mainQuestions) ) {
                                $count = 1;
                                foreach( $con_mainQuestions as $con_question ) {
                                    $con_discovery_question_id = $con_question['discovery_question_id'];
                                    $con_response_id		   = $con_question['fkresponse_id'];

                                    $query		= "SELECT * FROM question_admits qa
                                                        LEFT JOIN question_admit_results qar
                                                        ON  qar.discovery_question_id 	= 	'$con_discovery_question_id'  	AND
                                                        qar.question_admit_id			= qa.id 							AND
                                                        qar.fkresponse_id				= '$con_response_id'";
                                    $con_SubQuestions	= $AdminDAO->executeQuery($query);

                                        if($count == 1) {
                                            foreach( $con_SubQuestions as $con_SubQuestion ) {
                                                echo "<p class='q-subquestion'>". $con_SubQuestion['question_no'] .") ". $con_SubQuestion['question'] ."</p>";
                                            }
                                            echo "<br/>";
                                        }
                                    $count++;
                                }
                                echo "	<br/>
                                        <b><u>Response</u></b>
                                        <p class='q-response'>". nl2br($final_response) ."</p>";
                            }
                            else {
                                echo "	<br/>
                                        <b><u>Response</u></b>
                                        <p class='q-response' />";
                            }
                        }
                        else {
                            if( strtolower($answer) == 'yes' || ($question_type_id == 3 && sizeof($subQuestions) )) {
                                foreach( $subQuestions as $data ) {
                                    $question_id 			= $data['question_id'];
                                    $question_type_id 		= $data['question_type_id'];
                                    $form_id 				= $data['form_id'];
                                    $question_title 		= $data['question_title'];
                                    $question_number 		= $data['question_number'];
                                    $sub_part 				= $data['sub_part'];
                                    $is_pre_defined 		= $data['is_pre_defined'];
                                    $discovery_question_id	= $data['discovery_question_id'];

                                    if($response_id > 0) {
                                        $getAnswers = $AdminDAO->getrows(
                                                            "response_questions","*",
                                                            "fkresponse_id				= :fkresponse_id AND
                                                            fkdiscovery_question_id 	= 	:discovery_question_id",
                                                            array(	"discovery_question_id"	=>	$discovery_question_id,
                                                                    "fkresponse_id"			=>	$response_id));
                                        $answer 				= trim($getAnswers[0]['answer']);
                                        $answer_time 			= $getAnswers[0]['answer_time'];
                                        $objection 				= trim($getAnswers[0]['objection']);
                                        $final_response			= trim($getAnswers[0]['final_response']);
                                    }
                                    else {
                                        $answer 				= "";
                                        $answer_time 			= "";
                                        $objection 				= "";
                                        $final_response			= "";
                                    }
                                    echo "	<p class='q-question'>($sub_part) $question_title</p>
                                            <b><u>Response</u></b> <br/>";
                                    if( $final_response ) {
                                        echo "<p class='q-response'> $final_response </p><br/>";
                                    }
                                    else {
                                        $attached_response = finalResponseGenerate( $objection, $answer );
                                        if( $attached_response ) echo "	<p class='q-response'> $attached_response </p><br/>";
                                    }
                                }
                            }
                        }
                    }
                }
                echo "</div>";
            }
        }
        else if( $form_id == 4 ) {
            foreach($mainQuestions as $data) {
                $question_id 			= $data['question_id'];
                $question_type_id 		= $data['question_type_id'];
                $question_title 		= $data['question_title'];
                $question_number 		= $data['question_number'];
                $sub_part 				= $data['sub_part'];
                $is_pre_defined 		= $data['is_pre_defined'];
                $discovery_question_id	= $data['discovery_question_id'];

                if( $response_id ) {
                    $getAnswers = $AdminDAO->getrows(
                                        "response_questions","*",

                                        "fkresponse_id				= :fkresponse_id AND
                                        fkdiscovery_question_id 	= 	:discovery_question_id",

                                        array(	"discovery_question_id"	=>	$discovery_question_id,
                                                "fkresponse_id"			=>	$response_id));

                    $answer 				= trim($getAnswers[0]['answer']);
                    $answer_time 			= $getAnswers[0]['answer_time'];
                    $answer_detail 			= $getAnswers[0]['answer_detail'];
                    $objection 				= trim($getAnswers[0]['objection']);
                    $final_response 		= trim($getAnswers[0]['final_response']);
                }
                else {
                    $answer 				= "";
                    $answer_time 			= "";
                    $answer_detail 			= "";
                    $objection 				= "";
                    $final_response			= "";
                }

                echo "	<div class='q-row'>
                            <h3>REQUEST NO. $question_number:</h3>
                            <p class='q-question'> $question_title </p>";
                        if( $view == Discovery::VIEW_RESPONDING ) {
                            echo "	<br/>
                                    <b><u>Response</u></b>";
                            if( $final_response ) {
                                echo "	<p class='q-response'> $final_response </p>";
                            }
                            else {
                                echo "	<p class='q-response'>". finalResponseGenerate($objection,$answer) ."</p>";
                            }
                        }
                        else if( $respond == 1 ) {
                            echo "	<b><u>Objection</u></b>
                                    <p class='q-objection'> $objection </p>";
                        }
                echo "	<br/><br/>
                        </div>";
            }
        }
        else if(in_array($form_id,array(3,5))) {
            foreach( $mainQuestions as $data ) {
                $question_id 		    = $data['question_id'];
                $question_type_id 	    = $data['question_type_id'];
                $question_title 	    = $data['question_title'];
                $question_number 	    = $data['question_number'];
                $sub_part 			    = $data['sub_part'];
                $is_pre_defined 	    = $data['is_pre_defined'];
                $discovery_question_id	= $data['discovery_question_id'];
                if( $response_id ) {
                    $getAnswers = $AdminDAO->getrows(
                                        "response_questions","*",

                                        "fkresponse_id				= :fkresponse_id AND
                                        fkdiscovery_question_id 	= :discovery_question_id",

                                        array(	"discovery_question_id"	=>	$discovery_question_id,
                                                "fkresponse_id"			=>	$response_id) );
                    $answer 				= trim($getAnswers[0]['answer']);
                    $answer_time 			= $getAnswers[0]['answer_time'];
                    $answer_detail 			= $getAnswers[0]['answer_detail'];
                    $objection 				= trim($getAnswers[0]['objection']);
                    $final_response 		= trim($getAnswers[0]['final_response']);
                }
                else {
                    $answer 				= "";
                    $answer_time 			= "";
                    $answer_detail 			= "";
                    $objection 				= "";
                    $final_response 		= "";
                }
                echo "	<div class='q-row'>";
                if( $form_id == 5 ) {
                    echo "	<h3>REQUEST NO. $question_number:</h3>";
                }
                else {
                    echo "	<h3>INTERROGATORY NO. $question_number :</h3>";
                }

                echo "	<p class='q-question'>$question_title</p>";

                if( $view == Discovery::VIEW_RESPONDING ) {
                        echo "	<br/>
                                <b><u>Response</u></b>";
                        if( $form_id == 5 ) {
                            $reponse	= '';
                            if( $answer == "Select Your Response" ){
                                //echo "";
                            }
                            if( $answer == "I have responsive documents" ) {
                                $answer	= "Responsive documents are provided in Exhibit A.";
                            }
                            
                            $str1	= "A diligent search and a reasonable inquiry have been made in an effort to comply with this demand, however, responding party is unable to comply because they do not have any responsive documents in their possession, custody, or control.";
                            $str2	= " However, respondent believes that ".$answer_detail." may have responsive documents.";
                            if( $answer  == "Responsive documents have never existed") {
                                $answer	=  $str1." Respondent does not believe that such documents have ever existed. ".$str2;
                            }
                            if( $answer == "Responsive documents were destroyed" ) {
                                $answer	=  $str1." Respondent does not believe that such documents have ever existed. ".$str2;
                            }
                            if( $answer == "Responsive documents were lost, misplaced, stolen, or I lack access to them") {
                                $answer	=  $str1." Respondent believes that such documents were lost, misplace, stolen, or respondent lacks access to them. ".$str2;
                            }
                        }
                        if( $final_response ){
                            echo "<p class='q-response'> $final_response </p>";
                        }
                        else {
                            echo "<p class='q-response'>". finalResponseGenerate( $objection, $answer ) ."</p>";
                        }
                    }
                    else if( $respond == 1 ) {
                        echo "	<b><u>Objection</u></b>
                                <p class='q-objection'> $objection </p>";
                    }
                    echo "	<br/><br/>
                        </div>";
            }
        }
?>

<table>
    <tbody>
        <tr>
            <td width="60%"></td>
            <td>
                <br/>
                <hr/>
            </td>
        </tr>
        <tr>
            <td align="left" valign="top"><?php echo date('F j, Y'); ?></td>
            <td align="right">
                By: <?= User::getFullName($signingAttorney) ?><br/>
                    Attorney for <?= $att_for_client_role ?><br/>
                    <?= $att_for_client_name ?><br/>
                    Signed electronically,<br/>
                <img src="<?= ASSETS_URL ?>images/court.png"
                     style="width: 18px;padding-right: 3px;" /> Cal. Rules of Court, rule 2.257
            </td>
        </tr>
    </tbody>
</table>
</div>

<!-- =================================================== -->
<!-- 			VERIFICATION PAGE 						 -->
<!-- =================================================== -->
<?php
    if( $is_verified ) {
?>
<p class="break-page"></p>
<table class="tabela1" style="border:none !important">
  <tbody>
    <tr>
        <td  colspan="2" align="center"><h3><u>VERIFICATION</u></h3></td>
    </tr>
    <tr>
        <td  colspan="2" align="justify">
            <p class='declaration'>I am the <?php echo $verification_by_name ?> in this action, and I have read the foregoing <b><?php echo $form_name; ?></b> and know its contents. The matters stated therein are true based on my own knowledge, except as to those matters stated on information and belief, and as to those matters I believe them to be true.
            </p>
            <br/>
            <p class='declaration'>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct. Executed on <?php echo date("F j, Y",strtotime($verification_datetime)); ?> at <?php echo $verification_city.", ".$verification_state; ?>. <i>Electronically Signed at <?php echo date("n/j/Y",strtotime($verification_datetime))." ".str_replace(array('am','pm'),array('a.m','p.m'),date("g:i a",strtotime($verification_datetime))) ?>. Pacific Time.</i>
            </p>
        </td>
    </tr>
  </tbody>
</table>
<table style="border:none !important" width="100%">
  <tbody>
    <tr>
        <td align="left"><?php echo date('F j, Y',strtotime($verification_datetime)); ?></td>
        <td align="right">By: <?php echo $verification_signed_by; ?><br/>
            Signed electronically<br/>
            Cal. Rules of Court, rule 2.257
        </td>
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
    if( $declaration_text ) {
        echo "	<p class='break-page' />
                $declaration_text";
    }
?>
<!-- =================================================== -->
<!-- 			PROOF OF SERVICE (POS) 					 -->
<!-- =================================================== -->
<?php
if( $is_served && $pos_text) {
    echo "	<p class='break-page'/>
            $pos_text";
}
?>

<?php
$html = ob_get_contents();

ob_clean();

$footertext			= '<table width="100%" style="margin-top:30px;">
                        <tr>
                            <td width="5%" style="line-height:3px"></td>
                            <td style="line-height:18px" align="center">{PAGENO}<br/>
                                <br/>' . $form_name . '<br/>
                                All rights reserved © ' . date("Y") . ' EasyRogs. U.S. Patent Pending
                            </td>
                            <td width="5%"  style="text-align: right; line-height:3px"></td>
                        </tr>
                        </table>';
$oddEvenConfiguration = [
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

$fileName = "{$form_name}.pdf";
if ($downloadORwrite == 1) {
    $folderPath	= $_SESSION['system_path']."uploads/documents/{$uid}";
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
    $filePath = $folderPath."/".$fileName;
}
else {
    $filePath = "{$fileName}";
}

try {
    pdf( $filePath, $headerFooterConfiguration, @$downloadORwrite );
    if ($_ENV['APP_ENV'] != 'prod') {
        $logger->debug("PDF created and ". ( $downloadORwrite == 1 ? "saved to" : "sent to browser as"). " '$filePath', generated from html:\n\r\n\r$html\n\r\n\r\n\r" );

        // Save copy of the last PDF generated
        $savedir = __DIR__ . '/../_dev';
        if(!is_dir($savedir)) {
            mkdir( $savedir, 0755, true );
        }

        file_put_contents( $savedir. '/last-pdf.htm', $html );
        if ($downloadORwrite == 1) {
            copy( $filePath,   $savedir. '/last-pdf.pdf' );
        }
    }
}
catch( Exception $e ) {
    $logger->error(['MakePDF failed: ', $e->getMessage(), $e]);
}