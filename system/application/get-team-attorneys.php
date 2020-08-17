<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $caseId = $_GET['case_id'];

  if ( !$valid = $caseId ) { HttpResponse::malformed('Case ID is required.'); }

  $userSide        = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
  $attorneys       = $currentUser->getTeamAttorneys();
  $sideUsers       = $sidesModel->getUsers($userSide['id']);
  $primaryAttorney = $sidesModel->getPrimaryAttorney($userSide['id']);
  
  $attorneys = $attorneys ? $attorneys : [];

  // add side attorney if not part of the team
  if ($primaryAttorney) {
    if( !User::inCollection($primaryAttorney, $attorneys) ) {
      $attorneys[] = $primaryAttorney;
    }
  }

  // also add side attorneys
  if ($sideUsers) {
    foreach($sideUsers as $user) {
      if (User::isAttorney($user) && !User::inCollection($user, $attorneys)) {
        $attorneys[] = $user;
      }
    }
  }

  
  HttpResponse::successPayload(
    User::publishable($attorneys)
  );