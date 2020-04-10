<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $clientId = $_POST['id'];
  $caseId 	=	$_POST['case_id'];

  // do validation
  $valid = $clientId && $caseId;
  if ( !$valid ) {
    HttpResponse::malformed('Client and Case ID are required.');
  }

  // instantiate models
  $cases = new CaseModel();
  $cases->removeClient($caseId, $clientId, true);

  // LEGACY --------------------------------------------------------------------
  require_once __DIR__ . '/clientdelete.php';