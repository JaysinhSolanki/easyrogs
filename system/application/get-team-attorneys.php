<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $caseId = $_GET['case_id'];

  if ( !$valid = $caseId ) { HttpResponse::malformed('Case ID is required.'); }

  $attorneys    = $currentUser->getTeamAttorneys();
  $userSide     = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
  $sideAttorney = $sidesModel->getPrimaryAttorney($userSide['id']);
  
  // add side attorney if not part of the team
  if ( $sideAttorney ) {
    if( !User::inCollection($sideAttorney, $attorneys) ) {
      $attorneys[] = $sideAttorney;
    }
  }
  
  HttpResponse::successPayload(
    User::publishable($attorneys)
  );