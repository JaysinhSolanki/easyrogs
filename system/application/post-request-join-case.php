<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // params
  $caseId   = $_POST['case_id'];
  $clientId = $_POST['client_id'];

  $valid = $caseId && $clientId;
  if( !$valid ) {
    HttpResponse::malformed('case_iD and client_id are required.');
  }

  $side = $sidesModel->getByClientAndCase($clientId, $caseId);
  if ( !$side ) {
    HttpResponse::notFound('Client side not found on case.');
  }
  $sideUsers = $sidesModel->getAllUsers($side['id']);
  if ($sideUsers) {
    $sidesModel->addUser($side['id'], $currentUser->user, false);
    // send emails
    Qutee\Task::create('CaseJoinRequestEmailTask', [
      'user_id'        => $currentUser->id,
      'case_id'        => $caseId
    ]);

    HttpResponse::success('Request sent successfully!');    
  }
  else {
    $sidesModel->addUser($side['id'], $currentUser->user); // add user directly
    // add user to service list if user is an attorney
    $attorney = $usersModel->findAttorney($userId);
    if ($currentUser->isAttorney()) {
      $sidesModel->updateServiceListForAttorney($side, $currentUser->user);
    }
    HttpResponse::success('You have been added to the case successfully!');
  }