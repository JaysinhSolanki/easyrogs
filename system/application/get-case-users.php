<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  // params
  $caseId = $_GET['case_id'];
  $format = isset( $_GET['format'] ) ? $_GET['format'] : 'json';

  $cases = new CaseModel();
  $sides = new Side();
  
  $users = $cases->getUsers($caseId);
  
  if ( $format == 'html') {
    $smarty->assign(['caseId' => $caseId, 'users' => $users]);
    $smarty->display('get-case-users.tpl');
  }
  else {
    HttpResponse::successPayload($users);
  }