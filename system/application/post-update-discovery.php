<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $discoveryId   = $_POST['id'];
  $discoveryData = $_POST['discovery'];

  if ( !$discoveryId ) {
    HttpResponse::malformed('Discovery ID is required.');
  }

  if ( !$discovery = $discoveriesModel->find($discoveryId)) {
    HttpResponse::notFound();
  }

  $discoveriesModel->updateById($discoveryId, $discoveryData);
  
  HttpResponse::success('Updated successfully!', $discovery);