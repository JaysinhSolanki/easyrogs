<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

use function EasyRogs\_assert as _assert;


$action	=	$_POST['delete_or_leave'];
$caseId	=	$_POST['case_id'];
$selected_button_value = $_POST['value_selected_button'];
$deleteteam = $_POST['deleteteam'];
$deleteme = $_POST['deleteme'];
$current_logged_in_id = $_POST['current_logged_in_id'];
$another_attorney_id = $_POST['another_attorney_id'] == 'false' ? 'null' : $_POST['another_attorney_id'];
$lead_id =  $_POST['lead_id'];

$another_attorney_master_header = $_POST['another_attorney_master_header'];
$side_id = $_POST['side'];

$side_role = $_POST['side_role'];

function checkSides($userId1, $userId2 = null)
{
	global $sidesModel, $currentUser, $logger,
		$caseId;
	static $usersAndSides;

	if (!isset($usersAndSides)) $usersAndSides = $sidesModel->getSidesUsersByCase($caseId);
	if (!isset($userId2)) $userId2 = $currentUser->id;

	_assert([$userId1, $userId2]);
	$user1 = searchValue($usersAndSides, $userId1, 'user_id');
	$user2 = searchValue($usersAndSides, $userId2, 'user_id');
	_assert([$user1, $user2], "Something seems wrong with the DB.. corrupted?");

	return ($user1['side_id'] == $user2['side_id']) ? Side::SAME_SIDE : Side::OTHER_SIDE;
}

// $sides = new Side();
// $test =  $sides->getByUserAndCase($currentUser->id, $caseId);


// $test1 = $AdminDAO->getrows('attorney', "side_id = $side_id AND case_id = $caseId", array("side_id" =>$side_id));
// $group_row	=	$AdminDAO->getrows("system_groups","*"," pkgroupid = :groupid", array(":groupid"=>3));

// $getAllCases = $AdminDAO->getrows("attorney","*","side_id = $side_id AND case_id = $case_id",array("side_id"=>$side_id,"case_id"=>$case_id));	


// print_r($getAllCases);

// print_r('dd');

// exit;

// echo checkSides(306);

// echo "******";
// exit;

/**
 * 
 * TODO: refactor this some day, DB needs cascading constraints, just removing the case should be enough...
 * Also, this action thing is shitty, create separate endpoints for leaving case and removing case
 * 
 */




// entire case

if ($deleteteam == 'entire_case') {

	switch ($action) {

		case 1: // delete case


			// $discoveries = $discoveriesModel->getByUserAndCase($current_logged_in_id, $caseId);

			// // Side::legacyTranslateCaseData($case_id, $discoveries);

			// foreach( $discoveries as $discovery ) {
			// 	$id  = $discovery['id'];
			// 	$uid = $discovery['uid'];
			// 	$propoundingClient = $discovery['propounding'];
			// 	$propoundingAttorney = $discovery['propounding_attorney'] ?: -1;
			// 	$respondingClient = $discovery['responding'];
			// 	$creator_id  = $discovery['creator_id'];
			// 	//$is_submitted	= $discovery['is_submitted'];
			// 	$is_served		= $discovery['is_served'];
			// 	$discoveryType	= $discovery['type'];

			// 	$RequestPDF_FileName  = UPLOAD_URL ."documents/". $uid ."/". $discoveriesModel->getTitle($discovery) .".pdf";
			// 	$ResponsePDF_FileName = UPLOAD_URL ."documents/". $uid ."/". $responsesModel->getTitle(0,$discovery) .".pdf";
			// 	$totalChilds			= 0;
			// 	$totalChildsNotIncludes	= 0;

			// }

			// echo $id;

			// $attorney = $AdminDAO->getrows("attorney", "id", " case_id = :case_id AND fkaddressbookid = $current_logged_in_id", array("case_id" => $caseId));

			// $attorney_id = $attorney[0]['id'];

			// $dscvry	=	$AdminDAO->getrows('discoveries',  "id", " case_id = :case_id", array("case_id" => '226'));
			// // print_r($dscvry);
			// // die();

			// $fields		=	array('is_deleted');
			// $values		=	array('1');
			// $qry = $AdminDAO->updaterow('sides', $fields, $values, "case_id = '$caseId' AND role = '$side_role'");

			// $isdeleted = $AdminDAO->getrows('sides', "id", "case_id = :case_id AND is_deleted = '1'", array("case_id" => $caseId));

			// $fields		=	array('is_deleted');
			// $values		=	array('1');
			// $qry = $AdminDAO->updaterow('cases', $fields, $values, "id = '$caseId'");

			// $isdeleted = $AdminDAO->getrows('sides', "is_deleted", "case_id = :case_id", array("case_id" => $caseId));
			// $dt = [];
			// $sidecount = count($isdeleted);
			// foreach ($isdeleted as $isdltd) {
			// 	$dltd_sum = array_sum($isdltd);
			// 	array_push($dt, $dltd_sum);
			// }

			// if($sidecount == array_sum($dt)){
			// 	$fields		=	array('is_deleted');
			// 	$values		=	array('1');
			// 	$qry = $AdminDAO->updaterow('cases', $fields, $values, "id = '$caseId'");
			// 	break;
			// } else{
			// 	break;
			// }
			// die();

			// attorney
			$AdminDAO->deleterows('attorney', " case_id = :case_id AND side_id = $side_id", array("case_id" => $caseId));


			// attorneys_cases
			$AdminDAO->deleterows('attorneys_cases', " case_id = :case_id", array("case_id" => $caseId));


			// clients
			$AdminDAO->deleterows('clients', " case_id = :case_id AND client_role = '$side_role'", array("case_id" => $caseId));


			//questions
			$allDescoveries	=	$AdminDAO->getrows("discoveries", "GROUP_CONCAT(id) as ids", " case_id = :case_id", array("case_id" => $caseId));

			if (sizeof($allDescoveries) > 0) {
				$discoveryids	=	$allDescoveries[0]['ids'];
				$alldiscoveries			=	explode(",", $discoveryids);
				foreach ($alldiscoveries as $discovery_id) {
					$AdminDAO->deleterows('discovery_questions', " discovery_id = :discovery_id", array("discovery_id" => $discovery_id));
				}
			}

			$attorney = $AdminDAO->getrows("attorney", "id", " case_id = :case_id AND fkaddressbookid = '$current_logged_in_id'", array("case_id" => $caseId));

			$attorney_id = $attorney[0]['id'];


			// discoveries
			$AdminDAO->deleterows('discoveries', " case_id = :case_id AND attorney_id = '$attorney_id' AND served =''", array("case_id" => $caseId));


			$dscvry	=	$AdminDAO->getrows('discoveries',  "id", " case_id = :case_id AND attorney_id = '$attorney_id'", array("case_id" => $caseId));
			$dscvry_id = $dscvry[0]['id'];


			//documents
			$AdminDAO->deleterows('documents', " case_id = :case_id AND discovery_id = '$dscvry_id'", array("case_id" => $caseId));


			// sides 
			$fields		=	array('is_deleted');
			$values		=	array('1');
			$qry = $AdminDAO->updaterow('sides', $fields, $values, "case_id = '$caseId' AND role = '$side_role'");

			//cases
			$isdeleted = $AdminDAO->getrows('sides', "is_deleted", "case_id = :case_id", array("case_id" => $caseId));
			$dt = [];
			$sidecount = count($isdeleted);
			foreach ($isdeleted as $isdltd) {
				$dltd_sum = array_sum($isdltd);
				array_push($dt, $dltd_sum);
			}
			if($sidecount == array_sum($dt)){
				$fields		=	array('is_deleted');
				$values		=	array('1');
				$qry = $AdminDAO->updaterow('cases', $fields, $values, "id = '$caseId'");
				break;
			} else{
				break;
			}


		default: // leave case
			$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
			if ($currentSide) {
				$sidesModel->removeUser($currentSide['id'], $currentUser->id);
			}
			break;
	}
}

// delete team

if ($deleteteam) {


	$fields_case_delete_team = array('is_deleted');
	$values_case_delete_team = array('0');
	// $AdminDAO->updaterow('sides_users', " side_id = " . $deleteteam, array("side_id" => $deleteteam));

	$AdminDAO->updaterowSide('sides_users', $fields_case_delete_team, $values_case_delete_team, "side_id= '$deleteteam'");
	$AdminDAO->updaterowSide('sides_clients', $fields_case_delete_team, $values_case_delete_team, "side_id= '$deleteteam'");
	echo "yoyo";

	// $AdminDAO->deleterows('sides_clients', " side_id = " . $deleteteam, array("side_id" => $deleteteam));
}

// UPDATE sides
// SET primary_attorney_id = 306 WHERE primary_attorney_id =94 AND case_id=217;




// delete me

if ($another_attorney_id != $current_logged_in_id && $another_attorney_id != 'null') {


	if ($deleteme && $lead_id == $current_logged_in_id) {


		$fields = array('primary_attorney_id');
		$values = array($another_attorney_id);

		$AdminDAO->updaterow('sides', $fields, $values, "primary_attorney_id = $current_logged_in_id AND case_id=$caseId");

		$fields_case = array('attorney_id', 'case_attorney',);
		$values_case = array($another_attorney_id, $another_attorney_id);

		$AdminDAO->updaterow('cases', $fields_case, $values_case, "id = $caseId");

		$fields_side = array('masterhead');
		$values_side = array($another_attorney_master_header);

		$AdminDAO->updaterow('sides', $fields_side, $values_side, "id = $side_id");

		$AdminDAO->deleterows('sides_users', " side_id = $side_id AND system_addressbook_id = $current_logged_in_id");
	} else {

		$AdminDAO->deleterows('sides_users', " side_id = " . $side_id, array("side_id" => $side_id));

		$AdminDAO->deleterows('sides_clients', " side_id = " . $side_id, array("side_id" => $side_id));
	}
} else {

	$blank_value = 320;
	$fields_delete_me = array('primary_attorney_id', 'masterhead');
	$values_delete_me = array($blank_value, '');

	$AdminDAO->updaterowSide('sides', $fields_delete_me, $values_delete_me, "primary_attorney_id = '$current_logged_in_id' AND case_id= '$caseId'");


	$AdminDAO->deleterows('sides_users', " side_id = '$side' AND system_addressbook_id = '$current_logged_in_id'");

	$fields_case_delete_me = array('attorney_id', 'case_attorney');
	$values_case_delete_me = array($blank_value, $blank_value);
	$AdminDAO->updaterow('cases', $fields_case_delete_me, $values_case_delete_me, "id = $caseId");
}
