<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $term = $_GET['term'];
  
  // TODO: OvO
  HttpResponse::send(
    501, 
    HttpResponse::TYPE_ERROR, 
    'Not Implemented with sides case data.', 
    []
  );

  $cases = [];
  if (trim($term)) { // only search if query provided
    // query
    $cases = $casesModel->search($term);

    // format response  
    $response = [];
    foreach($cases as &$case) {
      $case = [
        'id'   => $case['id'],
        'text' => "$case[case_title] ($case[county_name] $case[case_number])"
      ];
    }
  }

  HttpResponse::successPayload(["results" => $cases, 'term' => $term]);
