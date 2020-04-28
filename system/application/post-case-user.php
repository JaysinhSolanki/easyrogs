<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $caseId = $_POST['case_id'];
  $name	  = $_POST['name'];
  $email  = $_POST['email'];

  $valid = $name && $email;
  if ( !$valid ) {
    HttpResponse::malformed('Name & Email are required.');
  }

  $users = new User();
  $sides = new Side();

  $side = $sides->getByUserAndCase($currentUser->id, $caseId);
  if(!$side) {
    HttpResponse::notFound('Please set a lead counsel before adding team users.');
  }
  $user = User::publishable($users->expressFindOrCreate($name, $email));
  $sides->addUser($side['id'], $user);
  
  if (!User::isActive($user)) {
    InvitationMailer::caseInvite($user, $currentUser->user, $caseId);
  }  

  HttpResponse::successPayload($user);