<?php
require_once("adminsecurity.php");

$caseId = $_POST['id'];

$allow_reminders	=	0;

if($case_title == "") { msg(301,2); }
if($plaintiff == "") { msg(312,2); }
if($defendant == "") { msg(313,2); }
if($case_attorney == "") { msg(335,2); }
if($uid == "") { $uid	=	$AdminDAO->generateuid('cases'); } //Generate UID for cases

$case = $casesModel->find($caseId);

if(!$case) { HttpResponse::notFound(); }

$updateFields = [
	'jurisdiction', 'county_name', 'case_number', 'judge_name', 'department', 
	'case_title', 'plaintiff', 'defendant', 'court_address',  'discovery_cutoff', 
	'trial','masterhead', 'attorney_id', 'case_attorney'
];

$caseData = [];
foreach($updateFields as $field) {
	if ($_POST[$field] !== '') {
		$caseData[$field] = $_POST[$field];
	}	
}
$caseData = array_merge($caseData, [
	'attorney_id' 		 => $currentUser->id,
	'is_draft'				 => 0,
	// TODO: look into this v
	'trial'						 => $trial ? date( "Y-m-d", strtotime( dateformat($trial, 2) ) ) : null,
	'filed'						 => $filed ? date( "Y-m-d", strtotime( dateformat($filed, 2) ) ) : null,
	'discovery_cutoff' => $discovery_cutoff ? date( "Y-m-d", strtotime( dateformat($discovery_cutoff, 2) ) ) : null,
]);

$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
if(!$currentSide) { // side genesis
	$currentSide = $sidesModel->create(null, $caseId, null);
	$sidesModel->addUser($currentSide['id'], $currentUser->id);
}

if ($attorneyId = $_POST['case_attorney']) {
	$attorneySide = $sidesModel->getByUserAndCase($attorneyId, $caseId);
	if ( $attorneySide && $currentSide && $attorneySide['id'] != $currentSide['id'] ) {
		HttpResponse::successPayload([ // payload is backward comp
			"pkerrorid" => "409",
			"messagetype" => "4",
			"messagetext" => "Attorney is already in a conflicting side."
		]);
	}
}

// update current side
$sidesModel->updateCaseData($currentSide['id'], $caseData);
$casesModel->updateSide($currentSide, $caseData, true);

// save original case and update all sides with initial case data
if ( $case['is_draft']) {
	$casesModel->updateCase($caseId, $caseData, true);
	$sides = $sidesModel->byCaseId($caseId);
	foreach($sides as $side) {
		$sidesModel->updateCaseData($side['id'], $caseData);
	}
}

$currentUser->setCounty($caseData['county_name']);

msg(7);