<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

	$action	=	$_POST['delete_or_leave'];
	$caseId	=	$_POST['case_id'];
	$selected_button_value = $_POST['value_selected_button'];
	$deleteteam = $_POST['deleteteam'];

	/**
	 * 
	 * TODO: refactor this some day, DB needs cascading constraints, just removing the case should be enough...
	 * Also, this action thing is shitty, create separate endpoints for leaving case and removing case
	 * 
	 */

if($deleteteam == 'entire_case'){

	switch( $action ) {

		case 1: // delete case
			//attorney
			$AdminDAO->deleterows('attorney'," case_id = :case_id", array("case_id"=>$caseId));
			
			//attorneys_cases
			$AdminDAO->deleterows('attorneys_cases'," case_id = :case_id", array("case_id"=>$caseId));
			
			//clients
			$AdminDAO->deleterows('clients'," case_id = :case_id", array("case_id"=>$caseId));
			
			//questions
			$allDescoveries	=	$AdminDAO->getrows("discoveries","GROUP_CONCAT(id) as ids"," case_id = :case_id", array("case_id"=>$caseId));
			
			if(sizeof($allDescoveries) > 0) {
				$discoveryids	=	$allDescoveries[0]['ids'];
				$alldiscoveries			=	explode(",",$discoveryids);
				foreach($alldiscoveries as $discovery_id) {
					$AdminDAO->deleterows('discovery_questions'," discovery_id = :discovery_id", array("discovery_id"=>$discovery_id));
				}
			}

			//discoveries
			$AdminDAO->deleterows('discoveries'," case_id = :case_id", array("case_id"=>$caseId));

			//documents
			$AdminDAO->deleterows('documents'," case_id = :case_id", array("case_id"=>$caseId));
			
			//cases
			$AdminDAO->deleterows('cases'," id = :case_id", array("case_id"=>$caseId));
		break;	
		
		default: // leave case
			$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
			if ($currentSide) {
				$sidesModel->removeUser($currentSide['id'], $currentUser->id);
			}
		break;
	}
}

	if($deleteteam){

		$AdminDAO->deleterows('sides_users'," side_id = ".$deleteteam, array("side_id"=>$deleteteam));

		// $AdminDAO->deleterows('attorney'," case_id = :case_id", array("case_id"=>$caseId));
	
	}