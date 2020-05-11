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
  
  if ($sidesModel->getByUserAndCase($currentUser->id, $caseId) ) {
    HttpResponse::conflict('You are already part of this case.');
  }

  $case = $casesModel->find($side['case_id']);

  $sideUsers = $sidesModel->getAllUsers($side['id']);
  if ($sideUsers) {
    $sidesModel->addUser($side['id'], $currentUser->user, false);

    // send emails
    CaseMailer::joinRequest($currentUser->user, $case);

    HttpResponse::success(
      "Your request has been forwarded to $client[client_name]'s Team. You'll be notified when it's granted.",
      ['awaiting_request' => true]
    );
  }
  else {
    $sidesModel->addUser($side['id'], $currentUser->user); // add user directly

    HttpResponse::success(
      "Your request to join $side[case_title] has been granted.",
      ['awaiting_request' => false]
    );
  }