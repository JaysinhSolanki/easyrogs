<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

	$action	=	$_POST['delete_or_leave'];
	$caseId	=	$_POST['case_id'];
	$selected_button_value = $_POST['value_selected_button'];
	$deleteteam = $_POST['deleteteam'];
	$deleteme = $_POST['deleteme'];
	$current_logged_in_id = $_POST['current_logged_in_id'];
	$another_attorney_id = $_POST['another_attorney_id'] == 'false'? $current_logged_in_id : $_POST['another_attorney_id']; 


	$another_attorney_master_header = $_POST['another_attorney_master_header']; 
	$side_id = $_POST['side']; 

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

		$AdminDAO->deleterows('sides_clients'," side_id = ".$deleteteam, array("side_id"=>$deleteteam));
	
	}
	

		// UPDATE sides
		// SET primary_attorney_id = 306 WHERE primary_attorney_id =94 AND case_id=217;
		
		if($another_attorney_id != $current_logged_in_id){
		if($deleteme){
		
		$fields = array('primary_attorney_id');
		$values = array($another_attorney_id);
		echo $AdminDAO->updaterow('sides',$fields,$values,"primary_attorney_id = $current_logged_in_id AND case_id=$caseId");

		$fields_case = array('attorney_id','case_attorney',);
		$values_case = array($another_attorney_id,$another_attorney_id);

		echo $AdminDAO->updaterow('cases',$fields_case,$values_case,"id = $caseId");

		$fields_side = array('masterhead');
		$values_side = array($another_attorney_master_header);

		echo $AdminDAO->updaterow('sides',$fields_side,$values_side,"id = $side_id");
		}
	}
	else{

		$blank_value = 11;
		$fields = array('primary_attorney_id');		
		$values = array($blank_value);
		echo $AdminDAO->updaterow('sides',$fields,$values,"primary_attorney_id = $current_logged_in_id AND case_id=$caseId");
	}