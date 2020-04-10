<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $cases = new CaseModel();
  $sideCases = $cases->getByUser($currentUser->id);

  // LEGACY --------------------------------------------------------------------
  require_once __DIR__ . '/cases.php';