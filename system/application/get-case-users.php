<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // params
  $caseId = $_GET['case_id'];
  $format = isset( $_GET['format'] ) ? $_GET['format'] : 'json';

  $cases = new CaseModel();
  $sides = new Side();
  
  if ( !$caseId ) { HttpResponse::malformed('Case ID is required'); }
  
  // $users = @$cases->getUsers($caseId);

  $users = @$cases->getUsersFlag($caseId);

  if ($users) {
    foreach($users as &$user) {
      $user['is_current_user'] = $user['pkaddressbookid'] == $currentUser->id;
    }
  }
  
  if ( $format == 'html') {
    $smarty->assign(['caseId' => $caseId, 'users' => $users]);
    $smarty->display('get-case-users.tpl');
  }
  else {
    HttpResponse::successPayload($users);
  }