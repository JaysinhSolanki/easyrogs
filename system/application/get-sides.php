<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // params
  $caseId =	$_GET['case_id'];
  $format = $_GET['format'] ? $_GET['format'] : 'json';
  
  // validation
  if ( !$caseId ) {
    HttpResponse::malformed('Case ID is required');
  }

  // format sides, it's clients and users
  $sidesModel   = new Side();
  $sides = $sidesModel->byCaseId($caseId);
  foreach($sides as &$side) {
    $side['editable'] = $sidesModel->userIsMember($side['id'], $currentUser->id);
    $side['users']    = $sidesModel->getUsers($side['id']);
    $side['clients']  = $sidesModel->getClients($side['id']);
    $side['attorney'] = User::publishable($sidesModel->getPrimaryAttorney($side['id']));

    if ( $side['users']) {
      foreach($side['users'] as &$user) {
        $user['group_name'] = User::GROUP_NAMES[$user['fkgroupid']];
        $user['is_primary'] = $user['pkaddressbookid'] == $side['primary_attorney_id'];
        $user['active']     = User::isActive($user);
      }
    }
    $side['serviceList'] = $sidesModel->getServiceList($side);
  }

  // render
  if ($format === 'json') {
    HttpResponse::success(['sides' => $sides]);
  }
  else {
    $smarty->assign('sides', $sides);
    $smarty->display('get-sides.tpl');
  }

