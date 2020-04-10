<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $attorneyId =	$_POST['id'];
	$caseId		  = $_POST['case_id'];

	$users = new User();
	$cases = new CaseModel();

  $user = $users->getByAttorneyId($attorneyId);
  if ( $user ) {
    $cases->removeUser($caseId, $user['pkaddressbookid'], true);
  }
  else {
    HttpResponse::notFound('Attorney not Found.');
  }

// LEGACY ----------------------------------------------------------------------
require_once __DIR__ . '/caseattorneydelete.php';