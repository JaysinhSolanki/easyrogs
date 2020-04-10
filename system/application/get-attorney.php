<?php 
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  $id = $_GET['id'];
  if ( !$id ) {
    HttpResponse::malformed('ID is required');
  }

  $user = $usersModel->findAttorney($id);
  if ( !$user ) {
    HttpResponse::notFound('Attorney doesnt exists.');
  }
  else {
    $user['masterhead'] = $usersModel->getMasterHead($user);
    HttpResponse::successPayload(User::publishable($user));
  }