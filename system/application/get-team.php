<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $addressBookId = $_SESSION['addressbookid'];
  $format        = $_GET['format'] ? $_GET['format'] : 'json';
  
  $teamModel = new Team();
  $team = $teamModel->byAddressBookId($addressBookId);

  if ($team) {
    $members = $teamModel->getMembers($team['id']);
    
    if ($format === 'json') {
      die(json_encode([
        'id' => $team['id'],
        'members' => User::publishable($members)
      ]));
    }
    else {
      $smarty->assign(['team' => $team, 'members' => $members]);
      $smarty->display('get-team.tpl');
    }
  }
  else {
    http_response_code(404);
  }