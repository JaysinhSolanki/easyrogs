<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
	$name  		  = $_POST['attorney_name'];
	$email 		  = $_POST['attorney_email'];
	$clientIds  = $_POST['client_id'];
	$caseId		  = $_POST['case_id'];
	$attorneyId	=	$_POST['editattorney_id'];

	$users = new User();
	$sides = new Side();
	$cases = new CaseModel();

	$valid = $clientIds && $email && $name && $caseId;
	if (!$valid) {
		HttpResponse::malformed('Please fill the required fields.');
	}
	
	if ($attorneyId) { 
		$user = $users->getByAttorneyId($attorneyId, true);
		if ($user) {
			$cases->removeUser($caseId, $user['id'], true);
		}
		else {
			HttpResponse::notFound('Attorney not Found.');
		}
	}
	else {
		$user = $users->expressFindOrCreate($name, $email);
	}
	
	$userSide = $sides->getByUserAndCase($user['pkaddressbookid'], $caseId);
	$side = $sides->mergeClientSides($caseId, $clientIds, $userSide);
	if ( !$side ) {
		HttpResponse::unprocessable('Attorney is already in a conflicting side of the case.');
	}
	elseif (!$userSide) {
		$sides->addUser($side['id'], $user);
		if (!User::isActive($user)) {
			InvitationMailer::caseInvite($user, $currentUser->user, $caseId);
		}		
	}

	// LEGACY ----------------------------------------------------------------------
	require_once __DIR__ . '/addattorney.php';