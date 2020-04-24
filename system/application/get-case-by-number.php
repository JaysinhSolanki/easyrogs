<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // extract params
  $caseNumber = $_GET['number'];
  
  if(!$caseNumber) {
    HttpResponse::malformed('number is required.');
  }

  $case = $casesModel->getByNumber($caseNumber);
  $case['team_member'] = $casesModel->userInCase($case['id'], $currentUser->id); // current user is part of the case team
  
  HttpResponse::successPayload($case);