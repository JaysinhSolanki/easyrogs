<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // params
  $caseId  = $_REQUEST['case_id'];
  $userId  = $_REQUEST['user_id'];

  $valid = $caseId && $userId;
  if( !$valid ) {
    HttpResponse::malformed('Case and User are required.');
  }

  $side        = $sidesModel->getByUserAndCase($userId, $caseId);
  $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
  if ( !$side || !$currentSide || $side['id'] != $currentSide['id'] ) {
    HttpResponse::unauthorized();
  }

  $sidesModel->activateUser($side['id'], $userId);
  
  // add user to service list if user is an attorney
  $attorney = $usersModel->findAttorney($userId);
  if ($attorney) {
    $sidesModel->updateServiceListForAttorney($side, $attorney);
  }

  // send notification
  CaseMailer::grantedRequest($attorney, $caseId, $currentUser->user);

  HttpResponse::success('Join request granted successfully!');