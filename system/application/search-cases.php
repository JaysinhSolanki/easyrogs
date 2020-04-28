<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $term = $_GET['term'];
  
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
  
  HttpResponse::successPayload(["results" => $cases]);