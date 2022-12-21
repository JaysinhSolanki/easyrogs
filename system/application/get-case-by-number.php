<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // extract params
  $caseNumber     = $_GET['number'];
  $requireClients = isset( $_GET['require_clients'] ) ? $_GET['require_clients'] : true;
  
  if(!$caseNumber) {
    HttpResponse::malformed('number is required.');
  }

  $case = $casesModel->getByNumber($caseNumber, $requireClients);
  
  $case['team_member'] = $casesModel->getActvCases($case['id'], $currentUser->id); // current user is part of the case team
  
  HttpResponse::successPayload($case);