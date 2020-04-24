<?php
  require_once(__DIR__ . '/../bootstrap.php');
  
  // params
  $token         = $_GET['token'];
  $tokenParts    = explode('-', $token);
  $caseUID       = $tokenParts[0];
  $userUID       = $tokenParts[1];
  $actionUserUID = $tokenParts[2];

  $valid = $caseUID && $userUID;
  if( !$valid ) {
    HttpResponse::unauthorized('Invalid Token.');
  }

  $case         = $casesModel->findByUID($caseUID);
  $user         = $usersModel->findByUID($userUID);
  $actionUser   = $usersModel->findByUID($actionUserUID);
  $userId       = $user['pkaddressbookid'];
  $actionUserId = $actionUser['pkaddressbookid'];
  $caseId       = $case['id'];

  $side = $sidesModel->getByUserAndCase($userId, $caseId);
  if (!$side) { HttpResponse::notFound(); }
  
  if ($actionUserId) {
    $actionUserSide = $sidesModel->getByUserAndCase($actionUserId, $caseId);
    if ($actionUserSide['id'] != $side['id']) { HttpResponse::unauthorized(); }
  }
  
  $sidesModel->removeUser($side['id'], $userId);
  
  // send notification
  Qutee\Task::create('CaseJoinRequestDeniedEmailTask', [
    'user_id'        => $userId,
    'case_id'        => $caseId,
    'action_user_id' => $actionUserId
  ]);

  header('Location: /system/application/index.php?notify=denied-join-request');