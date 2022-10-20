<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$action	=	$_POST['delete_or_leave'];
$caseId	=	$_POST['case_id'];
$selected_button_value = $_POST['value_selected_button'];
$deleteteam = $_POST['deleteteam'];
$deleteme = $_POST['deleteme'];
$current_logged_in_id = $_POST['current_logged_in_id'];
$another_attorney_id = $_POST['another_attorney_id'] == 'false' ? 'null' : $_POST['another_attorney_id'];


$another_attorney_master_header = $_POST['another_attorney_master_header'];
$side_id = $_POST['side'];

/**
 * 
 * TODO: refactor this some day, DB needs cascading constraints, just removing the case should be enough...
 * Also, this action thing is shitty, create separate endpoints for leaving case and removing case
 * 
 */

if ($deleteteam == 'entire_case') {

	switch ($action) {

		case 1: // delete case
			//attorney
			$AdminDAO->deleterows('attorney', " case_id = :case_id", array("case_id" => $caseId));

			//attorneys_cases
			$AdminDAO->deleterows('attorneys_cases', " case_id = :case_id", array("case_id" => $caseId));

			//clients
			$AdminDAO->deleterows('clients', " case_id = :case_id", array("case_id" => $caseId));

			//questions
			$allDescoveries	=	$AdminDAO->getrows("discoveries", "GROUP_CONCAT(id) as ids", " case_id = :case_id", array("case_id" => $caseId));

			if (sizeof($allDescoveries) > 0) {
				$discoveryids	=	$allDescoveries[0]['ids'];
				$alldiscoveries			=	explode(",", $discoveryids);
				foreach ($alldiscoveries as $discovery_id) {
					$AdminDAO->deleterows('discovery_questions', " discovery_id = :discovery_id", array("discovery_id" => $discovery_id));
				}
			}

			//discoveries
			$AdminDAO->deleterows('discoveries', " case_id = :case_id", array("case_id" => $caseId));

			//documents
			$AdminDAO->deleterows('documents', " case_id = :case_id", array("case_id" => $caseId));

			//cases
			$AdminDAO->deleterows('cases', " id = :case_id", array("case_id" => $caseId));
			break;

		default: // leave case
			$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
			if ($currentSide) {
				$sidesModel->removeUser($currentSide['id'], $currentUser->id);
			}
			break;
	}
}

if ($deleteteam) {

	$AdminDAO->deleterows('sides_users', " side_id = " . $deleteteam, array("side_id" => $deleteteam));

	$AdminDAO->deleterows('sides_clients', " side_id = " . $deleteteam, array("side_id" => $deleteteam));
}

// UPDATE sides
// SET primary_attorney_id = 306 WHERE primary_attorney_id =94 AND case_id=217;

if ($another_attorney_id != $current_logged_in_id && $another_attorney_id != 'null') {

	if ($deleteme) {

		$fields = array('primary_attorney_id');
		$values = array($another_attorney_id);
		$AdminDAO->updaterow('sides', $fields, $values, "primary_attorney_id = $current_logged_in_id AND case_id=$caseId");

		$fields_case = array('attorney_id', 'case_attorney',);
		$values_case = array($another_attorney_id, $another_attorney_id);

		$AdminDAO->updaterow('cases', $fields_case, $values_case, "id = $caseId");

		$fields_side = array('masterhead');
		$values_side = array($another_attorney_master_header);

		$AdminDAO->updaterow('sides', $fields_side, $values_side, "id = $side_id");
	}
} else {

	$blank_value = 320;
	$fields_delete_me = array('primary_attorney_id','masterhead');
	$values_delete_me = array($blank_value,'');
	$AdminDAO->updaterowSide('sides', $fields_delete_me, $values_delete_me, "primary_attorney_id = $current_logged_in_id AND case_id= $caseId");
	
	$AdminDAO->deleterows('sides_users', " side_id = $side AND system_addressbook_id = $current_logged_in_id");

	$fields_case_delete_me = array('attorney_id','case_attorney');
	$values_case_delete_me = array($blank_value,$blank_value);
	$AdminDAO->updaterow('cases', $fields_case_delete_me, $values_case_delete_me, "id = $caseId");
	                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
}