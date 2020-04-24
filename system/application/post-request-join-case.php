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

  if ( !$client = $clientsModel->find($clientId) ) {
    HttpResponse::notFound('Client not found.');
  }
  if ( !$side = $sidesModel->getByClientAndCase($clientId, $caseId) ) {
    HttpResponse::notFound('Client side not found on case.');
  }

  $case = $casesModel->find($side['case_id']);

  $sideUsers = $sidesModel->getAllUsers($side['id']);
  if ($sideUsers) {
    $sidesModel->addUser($side['id'], $currentUser->user, false);
    // send emails
    Qutee\Task::create('CaseJoinRequestEmailTask', [
      'user_id'        => $currentUser->id,
      'case_id'        => $caseId
    ]);

    HttpResponse::success(
      "Your request has been forwarded to $client[client_name]’s Team. You’ll be notified when it’s granted.",
      ['awaiting_request' => true]
    );
  }
  else {
    $sidesModel->addUser($side['id'], $currentUser->user); // add user directly
    // add user to service list if user is an attorney
    $attorney = $usersModel->findAttorney($userId);
    if ($currentUser->isAttorney()) {
      $sidesModel->updateServiceListForAttorney($side, $currentUser->user);
    }
    HttpResponse::success(
      "Your request to join $case[case_title] has been granted.",
      ['awaiting_request' => false]
    );
  }