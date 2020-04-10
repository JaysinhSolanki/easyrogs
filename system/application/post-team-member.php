<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $addressBookId = $_SESSION['addressbookid'];

  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $memberId = $_POST['memberId'];
           
  // validation
  if ( !(($name && $email) || $memberId) ) {
    HttpResponse::malformed('Name & Email or Member are required.');
  }

  $teams = new Team();
  $users = new User();
  
  $team = $teams->byAddressBookId($addressBookId);
  if ($team) {
    $user = $memberId 
            ? $users->find($memberId) 
            : $users->expressFindOrCreate($name, $email);

    if ($teams->memberExists($team['id'], $user['pkaddressbookid'])) {
      HttpResponse::conflict('Member already exists.', HttpResponse::TYPE_WARNING);
    }
    else {
      $teams->addMember($team['id'], $user['pkaddressbookid']);
      HttpResponse::success('Member added successfully.');
    }
  }
  else {
    HttpResponse::notFound('Unable to get Team.');
  }