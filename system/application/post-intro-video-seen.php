<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $valid = ($currentUser);
  if( !$valid ) {
    HttpResponse::malformed("Something wrong, can't find current user.");
  }

  if (!$currentUser->user['intro_seen']) {
    $usersModel->update('system_addressbook', 
      ['intro_seen' => 1], 
      ['pkaddressbookid' => $currentUser->id]
    );
  }

  HttpResponse::success(
    "OK",
    ['awaiting_request' => false]
  );
?>