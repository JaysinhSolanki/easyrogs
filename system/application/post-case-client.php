<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $clientId    = $_POST['id'];
  $caseId      = $_POST['case_id'];
  $clientName	 = $_POST['client_name'];
  $clientRole  = $_POST['clientroles'];
  $clientType  = $_POST['clienttypes'];
  $clientEmail = $_POST['client_email'];

  // instantiate models
  $clients = new Client();
  $users   = new User();
  $sides   = new Side();
  $teams   = new Team();
  $cases   = new CaseModel();

  // do validation
  $valid = ($caseId && $clientName && $clientRole && $clientType )
           && 
           ($clientType == Client::CLIENT_TYPE_OTHER || $clientEmail);
  if ( !$valid ) {
    HttpResponse::malformed('Invalid input, please verify the required fields.');
  }

  if ($clientType === Client::CLIENT_TYPE_US ) {
    $currentSide = $sides->getByUserAndCase($currentUser->id, $caseId);
    if(!$currentSide) {
      $currentSide = $sides->create($clientRole, $caseId, null);
      $sides->addUser($currentSide['id'], $currentUser->id);
    }
    if ($currentSide['role'] && !Side::isRoleAggregable($clientRole, $currentSide['role'])) {
      HttpResponse::conflict('Attorney is already part of a conflicting side.');
    }
  }

  // client actions
  $clientFields = [
    'client_name'  => $clientName, 'client_email' => $clientEmail,
    'client_type'  => $clientType, 'client_role'  => $clientRole,
  ];
   
  if ($clientId) { // updating client
    $client = $clients->find($clientId);
    if (!$client) {
      HttpResponse::notFound('Client not found.');
    }
    // reset client status on case and update client
    $cases->removeClient($caseId, $clientId);
    $clients->updateClient($client['id'], $clientFields);
  }
  else {
    $client = $clients->create(
      array_merge($clientFields, ['case_id' => $caseId])
    );
  }
  
  // find or create suitable side
  $side = $currentSide
          ? $currentSide
          : $sides->create($clientRole, $caseId, null);
  
  // add client to side
  $sides->addClient($side['id'], $client, $clientRole);
  if (!$side['role']) {
    $sides->updateSide($side['id'], ['role' => $clientRole]);
  }

  HttpResponse::success('Added successfully!', ['case_id' => $caseId]);