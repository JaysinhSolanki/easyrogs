<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // params
  $caseId =	$_GET['caseId'];
  $userId =	$_GET['userId'];
  $format = $_GET['format'] ? $_GET['format'] : 'json';

  // validation
  if ( !$caseId ) {
    HttpResponse::malformed('Case ID is required');
  }

  // format sides, it's clients and users
  $sidesModel   =   new Side();
  //$sides        =   $sidesModel->byCaseId($caseId, $userId);
  $sides         =   $sidesModel->findAttorneyCaseLetterHead($caseId, $userId);
  
  if ( !$sides ) {
    //HttpResponse::notFound('Attorney Case Letter Head doesnt exists.');
    $sides['letterhead'] = null;
    $sides['header_height'] = 20;
    $sides['footer_height'] = 20;
    HttpResponse::successPayload($sides);
  } else {
    HttpResponse::successPayload($sides);
  }

