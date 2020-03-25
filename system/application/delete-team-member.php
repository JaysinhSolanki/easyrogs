<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $addressBookId = $_SESSION['addressbookid'];
  $memberId      = isset($_POST['memberId']) ? $_POST['memberId'] 
                   : http_response_code(400);
  
  $teamModel = new Team();
  $team = $teamModel->byAddressBookId($addressBookId);

  if ($team) {
    $teamModel->deleteMember($team['id'], $memberId);
    http_response_code(204);
  }
  else {
    http_response_code(404);
  }