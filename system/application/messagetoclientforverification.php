<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

	$actionType     = $_REQUEST['actiontype'];
	$discoveryId    = $_REQUEST['discovery_id'];
	$notesForClient = $_REQUEST['notes_for_client'];

	$discovery = $discoveriesModel->find($discoveryId);
	
	switch($actionType) {
		case 1: DiscoveryMailer::clientVerification($discovery); break;
		case 2: DiscoveryMailer::clientResponse($discovery, $currentUser->user, $notesForClient); break;
	}
	$AdminDAO->updaterow('responses', array('is_submitted'), array('0'), "fkdiscoveryid = :id", array("id"=>$discoveryId) );	

	// struct is legacy.
	HttpResponse::successPayload([
		"messagetype"	 	=> 	2,
		"pkerrorid" 		=> 	7,
		"loadpageurl" 	=> 	"discoveries.php?pid=$discovery[case_id]&pkscreenid=45",
		"loaddivname" 	=> 	"screenfrmdiv",
		"messagetext"		=>	"Email has been sent successfully."
	]);