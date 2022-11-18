<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  

  $cases = $casesModel->getByUser($currentUser->id, );

  // TODO: solve this with a query?
  foreach($cases as &$case) {
    $userSide = $sidesModel->getByUserAndCase($currentUser->id, $case['id']);
    $case = Side::caseData($userSide);
  }
  unset($case);

  // LEGACY --------------------------------------------------------------------
  require_once __DIR__ . '/cases.php';