<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
	$name  		 = $_POST['attorney_name'];
	$email 		 = $_POST['attorney_email'];
	$clientIds = $_POST['client_id'];
	$caseId		 = $_POST['case_id'];
	$userId		 = $_POST['user_id'];

	$valid = $clientIds && $email && $name && $caseId;
	if (!$valid) {
		HttpResponse::malformed('Please fill the required fields.');
	}
	
	$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);

	$emailUser = $usersModel->expressFindOrCreate($name, $email);
	if ( $userId && $user = $usersModel->find($userId) ) {
		$sidesModel->removeFromServiceList($currentSide, $user);
	}
	$sidesModel->updateServiceListForAttorney($currentSide, $emailUser, $clientIds, $name, $email);
	
	// Add to side
	$userSide = $sidesModel->getByUserAndCase($emailUser['pkaddressbookid'], $caseId);
	// only modify side if primary attorney is not set yet and user is not in another side
	if (!$userSide) {
		$side = $sidesModel->mergeClientSides($caseId, $clientIds, $userSide);
		
		if ( ! Side::hasPrimaryAttorney($side)) {
			$sidesModel->addUser($side['id'], $emailUser);
			
			if ( ! User::isActive($emailUser) ) {
				InvitationMailer::caseInvite($emailUser, $currentUser->user, $caseId);
			}
		}
	}

	HttpResponse::success('Added successfully.');