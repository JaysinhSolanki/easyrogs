<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // params
  $caseId = $_GET['case_id'];
  $format = isset( $_GET['format'] ) ? $_GET['format'] : 'json';

  $cases = new CaseModel();
  $sides = new Side();
  
  $clients = $cases->getAllClients($caseId);
  
  $currentSide = $sides->getByUserAndCase($currentUser->id, $caseId);
  
	foreach($clients as &$client) {
    $clientSide = $sides->getByClientAndCase($client['id'], $caseId);
    if ( $client['client_type'] != Client::CLIENT_TYPE_PRO_PER) {
      $client['client_type'] = $currentSide && $currentSide['id'] == $clientSide['id'] 
                               ? Client::CLIENT_TYPE_US 
                               : Client::CLIENT_TYPE_OTHER;
    }    
    
		if ($client['client_type'] === Client::CLIENT_TYPE_OTHER) {
			unset($client['client_email']);
		}
	}

  if ( $format == 'html') {
    $smarty->assign('caseId', $caseId);
    $smarty->assign('clients', $clients);
    $smarty->display('get-case-clients.tpl');
  }
  else {
    HttpResponse::successPayload($clients);
  }