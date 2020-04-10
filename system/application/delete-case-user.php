<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // params
  $caseId = $_POST['case_id'];
  $userId = $_POST['user_id'];

  $sides = new Side();
  
  $side = $sides->getByUserAndCase($currentUser->id, $caseId);
  $sides->removeUser($side['id'], $userId, true);

  HttpResponse::success('User removed successfully!');