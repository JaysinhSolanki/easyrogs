<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $cases = $casesModel->getByUser($currentUser->id);

  // LEGACY --------------------------------------------------------------------
  require_once __DIR__ . '/cases.php';