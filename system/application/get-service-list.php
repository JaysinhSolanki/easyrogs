<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // params
  $caseId = $_GET['case_id'];
  $format = isset( $_GET['format'] ) ? $_GET['format'] : 'json';

  if ( !$caseId ) { HttpResponse::malformed('Case ID is required'); }
    
  $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
  $clients     = $casesModel->getAllClients($caseId);
  
  $serviceList = [];
  if ($currentSide) {
    $serviceList = User::publishable(
      $sidesModel->getServiceList($currentSide['id']),
      ['clients', 'attorney_id', 'attorney_name', 'attorney_email']
    );
  }

  if ( $format == 'html') {
    $smarty->assign([
      'caseId'      => $caseId, 
      'serviceList' => $serviceList,
      'clients'     => $clients
    ]);
    $smarty->display('get-service-list.tpl');
  }
  else {
    HttpResponse::successPayload($serviceList);
  }