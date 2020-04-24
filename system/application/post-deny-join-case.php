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
  $sidesModel->removeUser($side['id'], $userId);
  
  // send notification
  Qutee\Task::create('CaseJoinRequestDeniedEmailTask', [
    'user_id'        => $userId,
    'case_id'        => $caseId,
    'action_user_id' => $currentUser->id
  ]);

  HttpResponse::success('Join request denied.');