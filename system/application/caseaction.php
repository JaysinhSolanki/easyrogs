<?php
require_once("adminsecurity.php");

global $usersModel;

$caseId = $_POST['id'];
$attorney_id = $_POST['case_attorney'];
$letterhead = $_FILES['letterhead'];
$old_attorney_id = $_POST['old_attorney_id'];

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
	'trial','masterhead', 'attorney_id', 'case_attorney', 'letterhead','header_height', 'footer_height'
];

$caseData = [];
foreach($updateFields as $field) {
	if ($_POST[$field] !== '') {
		$caseData[$field] = $_POST[$field];
	}	
}
$currentSide = $sidesModel->getByUserAndCase($attorney_id, $caseId);
// Upload Letter Head File on Server
if(isset($_FILES['letterhead']['name']) && !empty($_FILES['letterhead']['name'])){

	$letter_head_file 			=	$_FILES['letterhead'];
	$letter_head_file_name 		=	$_FILES['letterhead']['name'];
	$letter_head_filename_array = 	explode(".", $letter_head_file_name);
	$letter_head_filename 		= 	str_replace("-", " ", $letter_head_filename_array[0]);
	$letter_head_file_ext 		= 	end((explode(".", $letter_head_file_name)));
	$letter_head_filename 		= 	"Case-Letter-Head-".$caseId."-".date("YmdHis").".".$letter_head_file_ext;

	//$upload_letterhead_path 	=	__DIR__."/../uploads/case-letters";
	$upload_letterhead_path 	=	__DIR__."/../uploads/profile-letters";
	$upload_letter_path 		=	$upload_letterhead_path."/".$letter_head_filename;

	$header_height 				=	$_POST['header_height'];
	$footer_height 				=	$_POST['footer_height'];
	
	// Check Directory is Present or NOT
	if ( !is_dir( $upload_letterhead_path ) ) {
		mkdir( $upload_letterhead_path, 0755, true );
	}

	if(!empty($currentSide['letterhead'])){
		$case_letterhead 	=	$currentSide['letterhead'];
		
		if (strpos($case_letterhead, 'Case-Letter-Head') !== false) { 
			//die('dd');
			$upload_letterhead_path 	=	__DIR__."/../uploads/profile-letters";
			$old_case_letter_path 		=	$upload_letterhead_path."/".$case_letterhead;
			if (file_exists($old_case_letter_path)) {
				unlink($old_case_letter_path);
			} 
		}
	}

	// Upload PDF file on Server
	move_uploaded_file($letter_head_file["tmp_name"], $upload_letter_path);

} else {
	//$letter_head_filename = !empty($_POST['letterhead']) ? $_POST['letterhead'] : '';
	if(!empty($_POST['letterhead'])){
		$letter_head_filename 	=	$_POST['letterhead'];
		$header_height 			=	$_POST['header_height'];
		$footer_height 			=	$_POST['footer_height'];

	} else {
		$userData   			= 	$usersModel->find($_POST['case_attorney']);
		
		//$letter_head_filename 	=	$userData['letterhead'];
		//$header_height 			=	$userData['header_height'];
		//$footer_height 			=	$userData['footer_height'];

		$letter_head_filename 	=	null;
		$header_height 			=	null;
		$footer_height 			=	null;

		// Delete OLD Letter Head file when Atternoy Letterhead
		if (strpos($currentSide['letterhead'], 'Case-Letter-Head') !== false) { 
			$upload_letterhead_path 	=	__DIR__."/../uploads/profile-letters";
			$old_case_letter_path 		=	$upload_letterhead_path."/".$currentSide['letterhead'];
			if (file_exists($old_case_letter_path)) {
				unlink($old_case_letter_path);
			} 
		} 
	}
}

$caseData = array_merge($caseData, [
	'attorney_id' 		 => $currentUser->id,
	'is_draft'				 => 0,
	// TODO: look into this v
	'trial'						 => $trial ? date( "Y-m-d", strtotime( dateformat($trial, 2) ) ) : null,
	'filed'						 => $filed ? date( "Y-m-d", strtotime( dateformat($filed, 2) ) ) : null,
	'discovery_cutoff' => $discovery_cutoff ? date( "Y-m-d", strtotime( dateformat($discovery_cutoff, 2) ) ) : null,
	'letterhead' => $letter_head_filename,
	'header_height' => $header_height,
	'footer_height' => $footer_height,
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

if(!empty($caseData['letterhead'])){
	$casesModel->updateLetterhead($currentSide['case_id'], $caseData['letterhead'], true);
}
if(!empty($caseData['header_height'])){
	$casesModel->updateHeaderHeight($currentSide['case_id'], $caseData['header_height'], true);
}
if(!empty($caseData['footer_height'])){
	$casesModel->updateFooterHeight($currentSide['case_id'], $caseData['footer_height'], true);
}

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