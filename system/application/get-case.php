<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $caseId = $_GET['id'];

  // backward comp, "drafts"
  if ( !$caseId ) {
    $saveUid	=	$AdminDAO->generateuid('cases');
    $caseId =	getDraft('cases','id',array('attorney_id' => $addressbookid,'uid'=>$saveUid,'allow_reminders'=>1),"attorney_id = '$addressbookid'");
    $caseowner = $caseteammember = true;
  }
  else {
    $userSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
    if (!$userSide) {
      HttpResponse::unauthorized('You are not authorized to see this case.');
    }
    $ownerSide = $sidesModel->getByUserAndCase($attorney_id, $caseId);
    $caseowner = $userSide['id'] === $ownerSide['id'];
    $caseteammember	=	true;    
  }

  $sides = new Side();

  $currentSide = $sides->getByUserAndCase($currentUser->id, $caseId);
  $currentPrimaryAttorney = $sides->getPrimaryAttorney($currentSide['id']);
  $currentPrimaryAttorneyId = $currentPrimaryAttorney['pkaddressbookid'];
  if ( !$currentPrimaryAttorneyId && $currentUser->isAttorney() ) {
    $currentPrimaryAttorneyId = $currentUser->id;
  }
  
  // LEGACY --------------------------------------------------------------------
  require_once __DIR__ . '/case.php';