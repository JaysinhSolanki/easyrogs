<?php
require_once("adminsecurity.php");

$owner =	$_REQUEST['owner'];

$allow_reminders	=	0;

if($owner == 1)
{
	if($case_title == "") { msg(301,2); }
	if($plaintiff == "") { msg(312,2); }
	if($defendant == "") { msg(313,2); }
	if($case_attorney == "") { msg(335,2); }
	if($uid == "") { $uid	=	$AdminDAO->generateuid('cases'); } //Generate UID for cases
	
	/**
	* Setting Cookies for Attorney County
	**/
	setcookie("ER_ATTORNEY_COUNTY",$county_name,time()+31556926 ,'/');
	
	/**
	* Add current active user as attorney with this case so we show them cases 
	**/
	$casedetails	=	$AdminDAO->getrows("cases","is_draft","id = :id",array("id"=>$id));
	if($casedetails[0]['is_draft'] == 1)
	{
		$AdminDAO->insertrow("attorneys_cases",array("case_id", "attorney_id"),array($id, $_SESSION['addressbookid']));
	}
	
	if($trial != "")
	{
		$trial	=	dateformat($trial,2);
		$trial	=	date("Y-m-d",strtotime($trial));
	}
	if($filed != "")
	{
		$filed	=	dateformat($filed,2);
		$filed	=	date("Y-m-d",strtotime($filed));
	}
	if($discovery_cutoff != "")
	{
		$discovery_cutoff	=	dateformat($discovery_cutoff,2);
		$discovery_cutoff	=	date("Y-m-d",strtotime($discovery_cutoff));
	}
	$fields	=	array('jurisdiction','county_name','case_number','judge_name','department','case_title','plaintiff','defendant','court_address','uid','is_draft','allow_reminders','discovery_cutoff','trial','date_filed', 'masterhead');
	$values	=	array($jurisdiction,$county_name,$case_number,$judge_name,$department,$case_title,$plaintiff,$defendant,'--',$uid,0,$allow_reminders,$discovery_cutoff,$trial,$filed, $masterhead);
	if($caseowner == 1)
	{
		$fields[]	=	"attorney_id";
		$values[]	=	$_SESSION['addressbookid'];
	}

	if ($case_attorney) {
		$attorneySide = $sidesModel->getByUserAndCase($case_attorney, $id);
		$currentSide =  $sidesModel->getByUserAndCase($currentUser->id, $id);
		if ( $attorneySide && $currentSide && $attorneySide['id'] != $currentSide['id'] ) {
			HttpResponse::successPayload([
				"pkerrorid" => "409",
				"messagetype" => "4",
				"messagetext" => "Attorney is already in a conflicting side."
			]);
		}
		$fields[]	=	"case_attorney";
		$values[]	=	$case_attorney;
	}

	// Update case and team if attorney changed
	$fieldsMap = [];
	foreach( $fields as $idx => $field) { // convert to fields hash
		if ($values[$idx] !== '') {
			$fieldsMap[$field] = $values[$idx];
		}		
	}

	$casesModel->updateCase($id, $fieldsMap, true);
}

msg(7);