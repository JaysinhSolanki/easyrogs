<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $caseId =	$_GET['case_id'];
  $format = $_GET['format'] ? $_GET['format'] : 'json';
  
  // do validation
  if ( !$caseId ) {
    HttpResponse::malformed('Case ID is required');
  }

  // instantiate models
  $sides   = new Side();

  // get sides, it's clients and users
  $side = $sides->getByUserAndCase($currentUser->id, $caseId);
  $side['users']    = $sides->getUsers($side['id']);
  $side['clients']  = $sides->getClients($side['id']);
  $side['attorney'] = User::publishable($sides->getPrimaryAttorney($side['id']));
  $side['users'][] = $side['attorney'];
  foreach($side['users'] as &$user) {
    $user['group_name'] = User::GROUP_NAMES[$user['fkgroupid']];
    $user['is_primary'] = $user['pkaddressbookid'] == $side['primary_attorney_id'];
  }    

  // render
  if ($format === 'json') {
    HttpResponse::success(['side' => $side]);
  }
  else {
    $smarty->assign('side', $side);
    $smarty->display('get-side.tpl');
  }

