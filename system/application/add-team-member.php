<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $addressBookId = $_SESSION['addressbookid'];

  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $memberId = $_POST['memberId'];
           
  // validation
  if ( !(($name && $email) || $memberId) ) {
    http_response_code(400);
    die(json_encode(['error' => 'Name & Email or Member are required.']));
  }

  $teamModel = new Team();
  $users = new SystemAddressBook();
  
  $team = $teamModel->byAddressBookId($addressBookId);
  if ($team) {
    if ( $memberId ){
      $user = $users->find($memberId);
    }
    else {
      $user = $users->getByEmail($email);
    }    
    if ( !$user ) {
      $nameParts = explode(' ', $name);
      $user = $users->create([
        'firstname'  => $nameParts[0], 
        'lastname'   => implode( ' ', array_slice($nameParts,  1) ), 
        'email'      => $email,
        'updated_by' => $addressBookId,
      ]);
      // TODO: trigger invite email job
    }

    $exists = $teamModel->memberExists($team['id'], $user['pkaddressbookid']);
    if ($exists) {
      die(json_encode(['msg' => 'Member already exists.']));
    }
    else {
      $teamModel->addMember($team['id'], $user['pkaddressbookid']);
      die(json_encode(['msg' => 'Member added successfully.']));
    }
  }
  else {
    http_response_code(404);
  }