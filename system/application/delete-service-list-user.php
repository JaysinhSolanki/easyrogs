<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $userId =	$_POST['user_id'];
	$caseId	= $_POST['case_id'];

  $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
  $sidesModel->removeFromServiceList($currentSide, $userId);

  HttpResponse::success('User removed successfully!');