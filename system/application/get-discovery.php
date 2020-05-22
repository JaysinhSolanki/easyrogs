<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $discoveryId = $_GET['id'];

  if ( !$discoveryId ) {
    HttpResponse::malformed('Discovery ID is required.');
  }

  if ( !$discovery = $discoveriesModel->find($discoveryId)) {
    HttpResponse::notFound();
  }

  HttpResponse::successPayload($discovery);